<?php

namespace App\Http\Controllers;

use App\Events\PaymentConfirmed;
use App\Models\NomodTransaction;
use App\Models\UAEVApplication;
use App\Models\ActivityBooking;
use App\Models\AgentBooking;
use App\Models\EsimOrder;
use App\Models\FifaTicketRequest;
use App\Models\UAEActivity;
use App\Mail\PaymentStatusMail;
use App\Mail\SupplierBookingMail;
use App\Mail\CustomerPaymentConfirmationMail;
use App\Mail\AdminPaymentNotificationMail;
use App\Mail\BookingNotificationMail;
use App\Services\NomodService;
use App\Services\EsimProvisioningService;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NomodController extends Controller
{
    public function success(Request $request)
    {
        return $this->handleCallback($request, 'success');
    }

    public function failure(Request $request)
    {
        return $this->handleCallback($request, 'failure');
    }

    public function cancelled(Request $request)
    {
        return $this->handleCallback($request, 'cancelled');
    }

    private function handleCallback(Request $request, string $callbackType)
    {
        $referenceId = $request->query('reference_id');

        if (!$referenceId) {
            return view('payment.status')->with('response', [
                'order_status' => 'Failed',
                'failure_message' => 'Missing payment reference.',
            ]);
        }

        // Look up the transaction by order_id (which we used as reference_id)
        $transaction = NomodTransaction::where('order_id', $referenceId)->first();

        if (!$transaction) {
            return view('payment.status')->with('response', [
                'order_id' => $referenceId,
                'order_status' => 'Failed',
                'failure_message' => 'Transaction not found.',
            ]);
        }

        // Verify payment status with Nomod API
        $nomodService = new NomodService();
        $verifyResult = $nomodService->getCheckout($transaction->checkout_id);

        $nomodStatus = 'unknown';
        $responseData = [];

        if ($verifyResult['success']) {
            $responseData = $verifyResult['data'];
            $nomodStatus = $responseData['status'] ?? 'unknown';

            $transaction->update([
                'status' => $nomodStatus,
                'response_data' => $responseData,
            ]);
        } else {
            // Fallback to callback type if API verification fails
            $statusMap = [
                'success' => 'paid',
                'failure' => 'expired',
                'cancelled' => 'cancelled',
            ];
            $nomodStatus = $statusMap[$callbackType] ?? 'unknown';

            $transaction->update([
                'status' => $nomodStatus,
            ]);
        }

        // Map Nomod status to our display status
        $orderStatus = match ($nomodStatus) {
            'paid' => 'Success',
            'cancelled' => 'Cancelled',
            'expired', 'failed' => 'Failed',
            default => 'Failed',
        };

        $bookingType = $this->determineBookingType($referenceId);

        // Send payment status email
        $this->sendPaymentStatusEmail($referenceId, $orderStatus, $responseData);

        // Update booking status
        $this->updateBookingStatus($referenceId, $orderStatus, $bookingType);

        // Dispatch PaymentConfirmed → triggers RecordCommission listener.
        // This is the SINGLE place commission is created — guarantees it only
        // happens after a real payment success (no premature recording).
        if ($orderStatus === 'Success') {
            $this->dispatchPaymentConfirmed($referenceId, $transaction);
        }

        // Build response array compatible with payment/status.blade.php
        $response = [
            'order_id' => $referenceId,
            'order_status' => $orderStatus,
            'amount' => $transaction->amount,
            'currency' => $transaction->currency ?? 'AED',
            'payment_mode' => 'Nomod Checkout',
            'tracking_id' => $transaction->checkout_id,
            'bank_ref_no' => $transaction->checkout_id,
            'failure_message' => $orderStatus !== 'Success' ? ($responseData['failure_reason'] ?? 'Payment was not completed.') : null,
            'status_message' => $orderStatus !== 'Success' ? ($responseData['status_message'] ?? 'Payment was not completed.') : null,
        ];

        // Sharjah visas carry a refundable security deposit. Surface the refund
        // breakdown on the acknowledgment page so the customer can see what comes
        // back to them, rather than only the lump sum they were charged.
        $response = array_merge($response, $this->sharjahRefundBreakdown($referenceId));

        return view('payment.status')->with('response', $response);
    }

    /**
     * Build the Sharjah security-deposit refund breakdown for the acknowledgment
     * page, from the values persisted on the application at submit time.
     *
     * Figures are per applicant: group submissions create one row per traveller
     * and nothing links them back to a single order, so the first application in
     * the reference is the reliable source. Returns an empty array for any order
     * that is not a visa, or a visa carrying no deposit (i.e. not Sharjah, or an
     * unconfigured deposit) — the view then renders nothing extra.
     */
    private function sharjahRefundBreakdown(string $referenceId): array
    {
        if (stripos($referenceId, 'UAEV') === false) {
            return [];
        }

        // Handles both ORDUAEV{id} and the group form ORDUAEV-GRP-{id}-{time}.
        if (!preg_match('/(\d+)/', str_ireplace(['ORDUAEV', 'GRP'], '', $referenceId), $m)) {
            return [];
        }

        $application = UAEVApplication::find((int) $m[1]);
        if (!$application) {
            return [];
        }

        $deposit = (float) ($application->UAEV_deposit_amount ?? 0);
        if ($deposit <= 0) {
            return [];
        }

        $refundable = (float) ($application->UAEV_refund_amount ?? 0);

        return [
            'deposit_amount'    => $deposit,
            'deposit_admin_fee' => round(max(0, $deposit - $refundable), 2),
            'deposit_refundable' => $refundable,
        ];
    }

    /**
     * Send 3 automated emails on payment completion:
     * A) Supplier — activity + customer info, NO payment details
     * B) Admin/Owner — full purchase order + payment confirmation + invoice
     * C) Customer — booking confirmation + receipt + supplier info + support
     */
    private function sendPaymentStatusEmail($orderId, $paymentStatus, $responseData = [])
    {
        try {
            $bookingData = $this->getBookingData($orderId);

            if (!$bookingData) {
                Log::warning('No booking data found for email', ['order_id' => $orderId]);
                return false;
            }

            $mailData = array_merge($bookingData['data'], $responseData);
            $mailData['order_id'] = $orderId;
            $mailData['payment_status'] = $paymentStatus;
            $mailData['sent_at'] = now()->toDateTimeString();

            $customerEmail = $this->extractCustomerEmail($bookingData['data'], $bookingData['type']);
            // Route admin notification to the tenant's inbox; fall back to platform default.
            $adminEmail = current_company()?->email ?: config('mail.from.address');

            // Enrich with supplier info for activity bookings
            $activity = null;
            if ($bookingData['type'] === 'activity' && isset($bookingData['data']['activityId'])) {
                $activity = UAEActivity::where('activityID', $bookingData['data']['activityId'])->first();
                if ($activity) {
                    $mailData['activityName'] = $activity->activityName ?? 'Activity';
                    $mailData['activityLocation'] = $activity->activityLocation ?? '';
                    $mailData['supplierName'] = $activity->supplierName ?? '';
                    $mailData['supplierEmail'] = $activity->supplierEmail ?? '';
                }
            }

            // ─── EMAIL C: Customer Confirmation ───────────────────────
            if ($customerEmail) {
                try {
                    Mail::to($customerEmail)->send(
                        new CustomerPaymentConfirmationMail($mailData, $paymentStatus, $bookingData['type'])
                    );
                    Log::info('Customer payment email sent', ['to' => $customerEmail, 'order_id' => $orderId]);
                } catch (\Exception $e) {
                    Log::error('Customer payment email failed', ['error' => $e->getMessage(), 'order_id' => $orderId]);
                }
            }

            // ─── EMAIL B: Admin/Owner Notification ────────────────────
            if ($adminEmail) {
                try {
                    Mail::to($adminEmail)->send(
                        new AdminPaymentNotificationMail($mailData, $paymentStatus, $bookingData['type'])
                    );
                    Log::info('Admin payment email sent', ['to' => $adminEmail, 'order_id' => $orderId]);
                } catch (\Exception $e) {
                    Log::error('Admin payment email failed', ['error' => $e->getMessage(), 'order_id' => $orderId]);
                }
            }

            // ─── EMAIL A: Supplier Notification (NO payment details) ──
            if ($activity && !empty($activity->supplierEmail)) {
                try {
                    $supplierMailData = $bookingData['data'];
                    $supplierMailData['activityName'] = $activity->activityName ?? 'Activity';
                    $supplierMailData['activityLocation'] = $activity->activityLocation ?? '';
                    $supplierMailData['status'] = $paymentStatus;
                    $supplierMailData['currency'] = $supplierMailData['currency'] ?? 'AED';

                    Mail::to($activity->supplierEmail)->send(
                        new SupplierBookingMail($supplierMailData, $activity->supplierName ?? 'Supplier')
                    );
                    Log::info('Supplier payment email sent', ['to' => $activity->supplierEmail, 'order_id' => $orderId]);
                } catch (\Exception $e) {
                    Log::warning('Supplier payment email failed', ['error' => $e->getMessage(), 'order_id' => $orderId]);
                }
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Payment email system failed', ['error' => $e->getMessage(), 'order_id' => $orderId]);
            return false;
        }
    }

    /**
     * Resolve the underlying booking/order from the reference id and
     * dispatch PaymentConfirmed. The RecordCommission listener picks it up.
     *
     * Looks at the order_id prefix to identify the service:
     *   ORDAB*    → activity booking
     *   ORDESIM*  → eSIM order
     *   ORDUAEV*  → visa application
     *   ORDAG*    → agent booking (flights/hotels)
     *   ORDUM*    → umrah (no commission — direct billed by platform)
     */
    private function dispatchPaymentConfirmed(string $referenceId, NomodTransaction $transaction): void
    {
        try {
            [$payable, $sourceType] = $this->resolvePayable($referenceId);

            if (!$payable || !$sourceType) {
                return; // umrah, or unknown type — nothing to record
            }

            $companyId = $payable->company_id ?? null;
            if (!$companyId) {
                Log::warning('PaymentConfirmed: payable has no company_id', [
                    'reference_id' => $referenceId,
                    'payable_type' => get_class($payable),
                    'payable_id'   => $payable->getKey(),
                ]);
                return;
            }

            event(new PaymentConfirmed(
                payable:     $payable,
                companyId:   (int) $companyId,
                grossAmount: (float) $transaction->amount,
                currency:    (string) ($transaction->currency ?? 'AED'),
                sourceType:  $sourceType,
                reference:   $referenceId,
            ));
        } catch (\Throwable $e) {
            // Never break the payment-confirmation response over commission bookkeeping.
            Log::error('Failed to dispatch PaymentConfirmed', [
                'reference_id' => $referenceId,
                'error'        => $e->getMessage(),
            ]);
        }
    }

    /**
     * @return array{0: ?\Illuminate\Database\Eloquent\Model, 1: ?string}
     */
    private function resolvePayable(string $referenceId): array
    {
        $services = config('commission.eligible_services', []);

        if (str_contains($referenceId, 'ORDAB')) {
            $id = (int) str_replace('ORDAB', '', $referenceId);
            return [ActivityBooking::find($id), $services['activity'] ?? null];
        }
        if (str_contains($referenceId, 'ORDESIM')) {
            $id = (int) str_replace('ORDESIM', '', $referenceId);
            return [EsimOrder::find($id), $services['esim'] ?? null];
        }
        if (str_contains($referenceId, 'ORDUAEV')) {
            $id = (int) str_replace('ORDUAEV', '', $referenceId);
            return [UAEVApplication::find($id), $services['visa'] ?? null];
        }
        if (str_contains($referenceId, 'ORDAG')) {
            $id = (int) str_replace('ORDAG', '', $referenceId);
            return [AgentBooking::find($id), $services['agent_booking'] ?? null];
        }

        return [null, null];
    }

    private function extractCustomerEmail($bookingData, $bookingType)
    {
        $email = null;

        switch ($bookingType) {
            case 'visa':
                $email = $bookingData['UAEV_email'] ?? null;
                break;

            case 'evisa':
                $email = $bookingData['email'] ?? null;
                break;

            case 'agent_booking':
                $email = $bookingData['client_email'] ?? null;
                break;

            case 'activity':
                $email = $bookingData['email'] ?? null;
                break;

            case 'esim':
                $email = $bookingData['customer_email'] ?? null;
                break;

            default:
                $possibleFields = ['email', 'UAEV_email', 'customer_email', 'billing_email'];
                foreach ($possibleFields as $field) {
                    if (!empty($bookingData[$field])) {
                        $email = $bookingData[$field];
                        break;
                    }
                }
        }

        return $email;
    }

    private function getBookingData($orderId)
    {
        if (stripos($orderId, 'ORDFIFA') !== false) {
            $booking = FifaTicketRequest::find(str_replace('ORDFIFA', '', $orderId));
            if ($booking) {
                $data = $booking->toArray();
                $data['booking_title'] = trim(($booking->match_label ?: 'FIFA World Cup 2026') . ' — ' . $booking->category);
                return ['type' => 'fifa', 'data' => $data];
            }
        } elseif (stripos($orderId, 'ORDVISA') !== false) {
            $application = \App\Models\FluxirVisaApplication::where('order_id', $orderId)->first();
            if ($application) {
                return ['type' => 'evisa', 'data' => $application->toArray()];
            }
        } elseif (stripos($orderId, 'ORDUMB') !== false) {
            $booking = \App\Models\UmrahBooking::where('order_id', $orderId)->first();
            if ($booking) {
                return [
                    'type' => 'umrah_bus',
                    'data' => [
                        'order_id' => $orderId,
                        'amount' => $booking->total_price,
                        'currency' => 'AED',
                        'package_name' => $booking->package->title ?? 'Umrah Bus Package',
                        'customer_name' => $booking->customer_name,
                        'customer_email' => $booking->customer_email,
                        'customer_phone' => $booking->customer_phone,
                    ],
                ];
            }
        } elseif (stripos($orderId, 'ORDSV') !== false) {
            $application = \App\Models\SaudiVisaApplication::where('order_id', $orderId)->first();
            if ($application) {
                return [
                    'type' => 'saudi_visa',
                    'data' => [
                        'order_id' => $orderId,
                        'amount' => $application->price,
                        'currency' => 'AED',
                        'visa_type' => $application->visaType->name ?? 'Saudi Visa',
                        'customer_name' => $application->full_name,
                        'customer_email' => $application->email,
                        'customer_phone' => $application->phone,
                    ],
                ];
            }
        } elseif (stripos($orderId, 'ORDUM') !== false) {
            $transaction = NomodTransaction::where('order_id', $orderId)->first();
            if ($transaction) {
                return [
                    'type' => 'umrah',
                    'data' => [
                        'order_id' => $orderId,
                        'amount' => $transaction->amount,
                        'currency' => $transaction->currency ?? 'AED',
                        'package_name' => $transaction->metadata['package_name'] ?? 'Umrah Package',
                        'status' => $transaction->status,
                    ],
                ];
            }
        } elseif (stripos($orderId, 'ORDESIM') !== false) {
            $esimOrderId = str_replace('ORDESIM', '', $orderId);
            $esimOrder = EsimOrder::find($esimOrderId);

            if ($esimOrder) {
                return [
                    'type' => 'esim',
                    'data' => $esimOrder->toArray(),
                ];
            }
        } elseif (stripos($orderId, 'UAEV') !== false) {
            $applicationId = str_replace('ORDUAEV', '', $orderId);
            $application = UAEVApplication::find($applicationId);

            if ($application) {
                return [
                    'type' => 'visa',
                    'data' => $application->toArray()
                ];
            }
        } elseif (stripos($orderId, 'ORDAG') !== false) {
            $bookingId = str_replace('ORDAG', '', $orderId);
            $booking = AgentBooking::find($bookingId);

            if ($booking) {
                return [
                    'type' => 'agent_booking',
                    'data' => $booking->toArray()
                ];
            }
        } elseif (stripos($orderId, 'AB') !== false) {
            $bookingId = str_replace('ORDAB', '', $orderId);
            $booking = ActivityBooking::find($bookingId);

            if ($booking) {
                $bookingData = $booking->toArray();

                if ($booking->activityId) {
                    $activity = UAEActivity::where('activityID', $booking->activityId)->first();
                    $bookingData['activity_name'] = $activity ? $activity->activityName : 'Activity Not Found';
                } else {
                    $bookingData['activity_name'] = 'No Activity ID';
                }

                $bookingDate = null;

                if ($booking->date) {
                    try {
                        $bookingDate = $booking->date->format('d M Y');
                    } catch (\Exception $e) {
                    }
                }

                if (!$bookingDate && isset($booking->getAttributes()['date']) && $booking->getAttributes()['date']) {
                    try {
                        $rawDate = $booking->getAttributes()['date'];
                        $bookingDate = \Carbon\Carbon::createFromFormat('Y-m-d', $rawDate)->format('d M Y');
                    } catch (\Exception $e) {
                    }
                }

                if (!$bookingDate && isset($bookingData['date']) && $bookingData['date']) {
                    try {
                        $bookingDate = \Carbon\Carbon::parse($bookingData['date'])->format('d M Y');
                    } catch (\Exception $e) {
                    }
                }

                $bookingData['booking_date'] = $bookingDate ?: 'Date Not Available';

                return [
                    'type' => 'activity',
                    'data' => $bookingData
                ];
            }
        }

        return null;
    }

    private function getEmailRecipients($bookingType)
    {
        $recipients = [config('mail.from.address')];

        switch ($bookingType) {
            case 'visa':
            case 'agent_booking':
                break;
            case 'activity':
                break;
        }

        $recipients = array_filter($recipients);

        return $recipients;
    }

    private function updateBookingStatus($orderId, $paymentStatus, $bookingType)
    {
        // Process referral tracking for successful payments
        if ($paymentStatus === 'Success') {
            $this->processReferralTracking($orderId, $bookingType);
        }

        if ($bookingType === 'umrah' && stripos($orderId, 'ORDUM') !== false) {
            // Umrah payments are tracked in nomod_transactions only; status already updated in handleCallback
            return;
        } elseif ($bookingType === 'umrah_bus' && stripos($orderId, 'ORDUMB') !== false) {
            $booking = \App\Models\UmrahBooking::where('order_id', $orderId)->first();
            if ($booking) {
                $status = ($paymentStatus === 'Success') ? 'paid' : 'failed';
                $booking->update(['payment_status' => $status]);
                
                if ($paymentStatus === 'Success') {
                    $totalPassengers = $booking->adults + $booking->children + $booking->infants;
                    $departure = \App\Models\UmrahDeparture::where('umrah_package_id', $booking->umrah_package_id)
                        ->where('departure_date', $booking->departure_date->toDateString())
                        ->first();
                    if ($departure) {
                        $departure->increment('seats_booked', $totalPassengers);
                    }
                    
                    $this->notifyUmrahBooking($booking);
                }
            }
        } elseif ($bookingType === 'saudi_visa' && stripos($orderId, 'ORDSV') !== false) {
            $application = \App\Models\SaudiVisaApplication::where('order_id', $orderId)->first();
            if ($application) {
                $status = ($paymentStatus === 'Success') ? 'paid' : 'failed';
                $application->update(['payment_status' => $status]);
                
                if ($paymentStatus === 'Success') {
                    $this->notifySaudiVisaApplication($application);
                }
            }
        } elseif ($bookingType === 'evisa' && stripos($orderId, 'ORDVISA') !== false) {
            $this->finalizeEvisaBooking($orderId, $paymentStatus);
        } elseif ($bookingType === 'fifa' && stripos($orderId, 'ORDFIFA') !== false) {
            $this->finalizeFifaBooking($orderId, $paymentStatus);
        } elseif ($bookingType === 1 && stripos($orderId, 'UAEV') !== false) {
            $applicationId = str_replace('ORDUAEV', '', $orderId);
            $application = UAEVApplication::find($applicationId);

            if ($application) {
                $status = ($paymentStatus === 'Success') ? 'Paid' : 'Failed';
                $application->update(['payment_status' => $status]);
            }
        } elseif ($bookingType === 2 && stripos($orderId, 'AB') !== false) {
            $bookingId = str_replace('ORDAB', '', $orderId);
            $booking = ActivityBooking::find($bookingId);

            if ($booking) {
                $status = ($paymentStatus === 'Success') ? 'paid' : 'payment_failed';
                $booking->update(['status' => $status]);
            }
        } elseif (($bookingType === 3 || $bookingType === 'agent_booking') && stripos($orderId, 'ORDAG') !== false) {
            $bookingId = str_replace('ORDAG', '', $orderId);
            $booking = AgentBooking::find($bookingId);

            if ($booking) {
                $status = ($paymentStatus === 'Success') ? 'Paid' : 'Failed';
                $booking->update(['payment_status' => $status]);
            }
        } elseif ($bookingType === 4 && stripos($orderId, 'ORDESIM') !== false) {
            $esimOrderId = str_replace('ORDESIM', '', $orderId);
            $esimOrder = EsimOrder::find($esimOrderId);

            if ($esimOrder) {
                if ($paymentStatus === 'Success') {
                    $esimOrder->update(['payment_status' => 'paid']);

                    // Notify the business team that an eSIM order was paid. Resolve
                    // recipients from the ORDER's tenant (this runs in a payment
                    // callback where current_company() may be the apex host).
                    try {
                        $recipients = booking_recipients(
                            service_notification_emails('esim', $esimOrder->company)
                        );
                        if (!empty($recipients)) {
                            Mail::to($recipients)->send(new BookingNotificationMail(
                                heading: 'New eSIM order',
                                intro: 'An eSIM order has been paid and is being provisioned.',
                                rows: [
                                    'Customer'  => $esimOrder->customer_name,
                                    'Email'     => $esimOrder->customer_email,
                                    'Phone'     => $esimOrder->customer_phone,
                                    'Bundle'    => $esimOrder->bundle_name,
                                    'Country'   => $esimOrder->country_name,
                                    'Validity'  => $esimOrder->validity_days ? $esimOrder->validity_days . ' days' : null,
                                    'Price'     => trim(($esimOrder->currency ?? '') . ' ' . $esimOrder->selling_price),
                                ],
                                reference: $esimOrder->order_reference,
                                replyToAddress: $esimOrder->customer_email,
                            ));
                        }
                    } catch (\Throwable $e) {
                        Log::error('eSIM booking notification failed', [
                            'esim_order_id' => $esimOrder->id,
                            'error'         => $e->getMessage(),
                        ]);
                    }

                    // Provision with MontyeSIM. A successful assign both charges
                    // the reseller wallet and sends the customer their QR code,
                    // so this is the point of no return for the order. On
                    // failure the service alerts the team and the order can be
                    // retried from the manager portal.
                    (new EsimProvisioningService())->provision($esimOrder);
                } else {
                    $esimOrder->update(['payment_status' => 'failed']);
                }
            }
        }
    }

    /**
     * After Nomod confirms payment for a Fluxir e-visa application:
     * mark paid, submit to Fluxir on credit, notify the business team.
     * notified_at guards against duplicate callbacks.
     */
    private function finalizeEvisaBooking(string $orderId, string $paymentStatus): void
    {
        $record = \App\Models\FluxirVisaApplication::where('order_id', $orderId)->first();
        if (!$record) {
            return;
        }

        if ($paymentStatus !== 'Success') {
            $record->update(['status' => 'failed']);
            return;
        }

        $record->update(['is_paid' => true, 'status' => 'paid']);

        $fluxir = new \App\Services\FluxirService();
        $review = $fluxir->submitForReview(
            $record->fluxir_service_application_id,
            $record->items ?? []
        );

        $submitted = $review['success'] ?? false;

        if ($submitted) {
            $record->update([
                'state'         => $review['data']['state'] ?? 'ReadyForReview',
                'status'        => 'submitted',
                'last_response' => $review['data'] ?? null,
            ]);
        } else {
            // The customer has paid but Fluxir has not accepted the application,
            // so nobody is processing their visa. Record it as such rather than
            // leaving the order looking merely 'paid'.
            $record->update([
                'status'        => 'submit_failed',
                'last_response' => $review['data'] ?? null,
            ]);
            Log::error('Fluxir submitForReview failed after Nomod payment', [
                'order_id' => $orderId,
                'error'    => $review['error'] ?? null,
            ]);
        }

        if ($record->notified_at) {
            return;
        }

        try {
            $recipients = booking_recipients(
                service_notification_emails('visa', $record->company)
            );
            if (!empty($recipients)) {
                Mail::to($recipients)->send(new BookingNotificationMail(
                    heading: $submitted
                        ? 'New e-Visa application — PAID'
                        : 'e-Visa SUBMISSION FAILED — action required',
                    intro: $submitted
                        ? 'A customer paid for an e-Visa. The application has been submitted to Fluxir for processing.'
                        : 'A customer paid for an e-Visa but the application was REJECTED by Fluxir and is not being '
                          . 'processed. Submit it manually or refund the customer.',
                    rows: array_filter([
                        'Applicant'   => trim(($record->first_name ?? '') . ' ' . ($record->last_name ?? '')),
                        'Email'       => $record->email,
                        'Phone'       => $record->phone,
                        'Nationality' => $record->nationality,
                        'Destination' => $record->destination_code,
                        'Arrival'     => optional($record->arrival_date)->format('d M Y'),
                        'Amount'      => 'USD ' . $record->amount,
                        'Error'       => $submitted ? null : ($review['error'] ?? 'Unknown'),
                    ]),
                    reference: $orderId,
                    replyToAddress: $record->email,
                ));
            }
            $record->forceFill(['notified_at' => now()])->save();
        } catch (\Throwable $e) {
            Log::error('e-Visa booking notification failed', ['order_id' => $orderId, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Mark a FIFA ticket booking paid/failed and notify the configured FIFA
     * recipients (the agents). The customer confirmation is sent separately by
     * sendPaymentStatusEmail. notified_at dedupes a duplicate payment callback.
     */
    private function finalizeFifaBooking(string $orderId, string $paymentStatus): void
    {
        $booking = FifaTicketRequest::find(str_replace('ORDFIFA', '', $orderId));
        if (!$booking) {
            return;
        }

        if ($paymentStatus !== 'Success') {
            $booking->update(['payment_status' => 'failed']);
            return;
        }

        $booking->update([
            'payment_status' => 'paid',
            'status'         => 'paid',
            'paid_at'        => now(),
        ]);

        if ($booking->notified_at) {
            return; // already notified on an earlier callback
        }

        try {
            $recipients = booking_recipients(service_notification_emails('fifa', $booking->company));
            if (!empty($recipients)) {
                Mail::to($recipients)->send(new BookingNotificationMail(
                    heading: 'FIFA ticket booking — PAID',
                    intro: 'A customer paid for FIFA World Cup 2026 tickets online.',
                    rows: [
                        'Match'    => $booking->match_label,
                        'Category' => $booking->category,
                        'Quantity' => $booking->quantity,
                        'Amount'   => trim(($booking->currency ?? '') . ' ' . $booking->amount),
                        'Customer' => $booking->name,
                        'Email'    => $booking->email,
                        'Phone'    => $booking->phone,
                    ],
                    reference: $orderId,
                    replyToAddress: $booking->email,
                ));
            }
            $booking->forceFill(['notified_at' => now()])->save();
        } catch (\Throwable $e) {
            Log::error('FIFA booking notification failed', ['order_id' => $orderId, 'error' => $e->getMessage()]);
        }
    }

    private function getCustomerData($orderId, $bookingType)
    {
        if ($bookingType === 'fifa' || stripos($orderId, 'ORDFIFA') !== false) {
            $booking = FifaTicketRequest::find(str_replace('ORDFIFA', '', $orderId));
            if ($booking) {
                return ['name' => $booking->name, 'email' => $booking->email, 'phone' => $booking->phone];
            }
        } elseif ($bookingType == 1 || stripos($orderId, 'UAEV') !== false) {
            $applicationId = str_replace('ORDUAEV', '', $orderId);
            $application = UAEVApplication::find($applicationId);

            if ($application) {
                return [
                    'name' => $application->UAEV_first_name . ' ' . $application->UAEV_last_name,
                    'email' => $application->UAEV_email,
                    'phone' => $application->UAEV_phone
                ];
            }
        } elseif ($bookingType == 2 || stripos($orderId, 'AB') !== false) {
            $bookingId = str_replace('ORDAB', '', $orderId);
            $booking = ActivityBooking::find($bookingId);

            if ($booking) {
                return [
                    'name' => $booking->name,
                    'email' => $booking->email,
                    'phone' => $booking->phone
                ];
            }
        } elseif ($bookingType == 3 || stripos($orderId, 'ORDAG') !== false) {
            $bookingId = str_replace('ORDAG', '', $orderId);
            $booking = AgentBooking::find($bookingId);

            if ($booking) {
                return [
                    'name' => $booking->client_name,
                    'email' => $booking->client_email,
                    'phone' => $booking->client_phone
                ];
            }
        } elseif ($bookingType == 4 || stripos($orderId, 'ORDESIM') !== false) {
            $esimOrderId = str_replace('ORDESIM', '', $orderId);
            $esimOrder = EsimOrder::find($esimOrderId);

            if ($esimOrder) {
                return [
                    'name' => $esimOrder->customer_name,
                    'email' => $esimOrder->customer_email,
                    'phone' => $esimOrder->customer_phone,
                ];
            }
        }

        return null;
    }

    private function determineBookingType($orderId)
    {
        if (stripos($orderId, 'ORDFIFA') !== false) {
            return 'fifa';
        } elseif (stripos($orderId, 'ORDUMB') !== false) {
            return 'umrah_bus';
        } elseif (stripos($orderId, 'ORDUM') !== false) {
            return 'umrah';
        } elseif (stripos($orderId, 'ORDSV') !== false) {
            return 'saudi_visa';
        } elseif (stripos($orderId, 'ORDESIM') !== false) {
            return 4;
        } elseif (stripos($orderId, 'ORDVISA') !== false) {
            return 'evisa';
        } elseif (stripos($orderId, 'UAEV') !== false) {
            return 1;
        } elseif (stripos($orderId, 'ORDAG') !== false) {
            return 3;
        } elseif (stripos($orderId, 'AB') !== false) {
            return 2;
        }
        return 1;
    }

    /**
     * Process referral tracking for successful payments
     */
    private function processReferralTracking($orderId, $bookingType)
    {
        try {
            $transaction = NomodTransaction::where('order_id', $orderId)->first();

            if (!$transaction) {
                return;
            }

            // Get order details based on booking type
            $orderDetails = $this->getOrderDetailsForReferral($orderId, $bookingType);

            if (!$orderDetails) {
                return;
            }

            // Determine order type string
            $orderTypeMap = [
                1 => 'visa',
                2 => 'activity',
                3 => 'agent_booking',
                4 => 'esim',
                'umrah' => 'umrah',
            ];
            $orderType = $orderTypeMap[$bookingType] ?? 'other';

            // Process referral
            $referralService = new ReferralService();
            $referralService->processOrderReferral(
                $orderId,
                $orderType,
                (float) $transaction->amount,
                $orderDetails['email'],
                $orderDetails['name'],
                [
                    'transaction_id' => $transaction->checkout_id,
                    'booking_type' => $bookingType,
                    'currency' => $transaction->currency ?? 'AED',
                ],
                request()
            );

            Log::info('Referral tracking processed for order', [
                'order_id' => $orderId,
                'order_type' => $orderType,
                'amount' => $transaction->amount,
            ]);

        } catch (\Exception $e) {
            Log::error('Referral tracking failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get order details for referral processing
     */
    private function getOrderDetailsForReferral($orderId, $bookingType)
    {
        $customerData = $this->getCustomerData($orderId, $bookingType);

        if ($customerData) {
            return [
                'name' => $customerData['name'] ?? null,
                'email' => $customerData['email'] ?? null,
            ];
        }

        return null;
    }

    private function notifyUmrahBooking(\App\Models\UmrahBooking $booking): void
    {
        try {
            $recipients = booking_recipients(service_notification_emails('hajj_umrah', $booking->company));
            if (!empty($recipients)) {
                Mail::to($recipients)->send(new BookingNotificationMail(
                    heading: 'New Umrah Bus Booking — PAID',
                    intro: 'A customer has booked and paid for an Umrah Bus Package.',
                    rows: [
                        'Package'        => $booking->package->title ?? 'Umrah Bus Package',
                        'Departure Date' => $booking->departure_date->format('d M Y'),
                        'Adults'         => $booking->adults,
                        'Children'       => $booking->children,
                        'Infants'        => $booking->infants,
                        'Total Price'    => 'AED ' . number_format($booking->total_price, 2),
                        'Payment Method' => $booking->payment_gateway,
                        'Customer Name'  => $booking->customer_name,
                        'Email'          => $booking->customer_email,
                        'Phone'          => $booking->customer_phone,
                    ],
                    reference: $booking->order_id,
                    replyToAddress: $booking->customer_email,
                ));
            }
        } catch (\Throwable $e) {
            Log::error('Umrah booking notification failed', ['order_id' => $booking->order_id, 'error' => $e->getMessage()]);
        }
    }

    private function notifySaudiVisaApplication(\App\Models\SaudiVisaApplication $application): void
    {
        try {
            $recipients = booking_recipients(service_notification_emails('visas', $application->company));
            if (!empty($recipients)) {
                Mail::to($recipients)->send(new BookingNotificationMail(
                    heading: 'New Saudi Visa Application — PAID',
                    intro: 'A customer has submitted and paid for a Saudi Visa.',
                    rows: [
                        'Visa Type'   => $application->visaType->name ?? 'Saudi Visa',
                        'Nationality' => $application->nationality,
                        'Applicant'   => $application->full_name,
                        'Email'       => $application->email,
                        'Phone'       => $application->phone,
                        'Price'       => 'AED ' . number_format($application->price, 2),
                    ],
                    reference: $application->order_id,
                    replyToAddress: $application->email,
                ));
            }
        } catch (\Throwable $e) {
            Log::error('Saudi Visa booking notification failed', ['order_id' => $application->order_id, 'error' => $e->getMessage()]);
        }
    }
}
