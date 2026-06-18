<?php

namespace App\Http\Controllers;

use App\Events\PaymentConfirmed;
use App\Models\NomodTransaction;
use App\Models\UAEVApplication;
use App\Models\ActivityBooking;
use App\Models\AgentBooking;
use App\Models\EsimOrder;
use App\Models\UAEActivity;
use App\Mail\PaymentStatusMail;
use App\Mail\SupplierBookingMail;
use App\Mail\CustomerPaymentConfirmationMail;
use App\Mail\AdminPaymentNotificationMail;
use App\Mail\BookingNotificationMail;
use App\Services\NomodService;
use App\Services\MontyEsimService;
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

        return view('payment.status')->with('response', $response);
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
        if (stripos($orderId, 'ORDUM') !== false) {
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

                    // Reserve + Complete with MontyeSIM
                    try {
                        $montyService = new MontyEsimService();
                        $reservation = $montyService->reserveBundle(
                            $esimOrder->bundle_code,
                            $esimOrder->customer_email,
                            $esimOrder->customer_name,
                            $esimOrder->order_reference
                        );

                        if ($reservation['success'] ?? false) {
                            $esimOrder->update([
                                'monty_order_id' => $reservation['order_id'] ?? null,
                                'monty_iccid' => $reservation['iccid'] ?? null,
                                'reservation_status' => 'reserved',
                                'monty_response' => $reservation['data'] ?? $reservation,
                            ]);

                            // Complete — triggers QR code email from MontyeSIM
                            $completion = $montyService->completeBundle($esimOrder->order_reference);

                            if ($completion['success'] ?? false) {
                                $esimOrder->update([
                                    'reservation_status' => 'completed',
                                    'monty_response' => $completion['data'] ?? $completion,
                                ]);
                                Log::info('eSIM order completed successfully', [
                                    'esim_order_id' => $esimOrder->id,
                                    'order_reference' => $esimOrder->order_reference,
                                ]);
                            } else {
                                $esimOrder->update([
                                    'reservation_status' => 'complete_failed',
                                    'monty_response' => $completion['data'] ?? $completion,
                                ]);
                                Log::error('MontyeSIM complete failed after reserve', [
                                    'esim_order_id' => $esimOrder->id,
                                    'error' => $completion['error'] ?? 'Unknown',
                                ]);
                            }
                        } else {
                            $esimOrder->update([
                                'reservation_status' => 'reserve_failed',
                                'monty_response' => $reservation['data'] ?? $reservation,
                            ]);
                            Log::error('MontyeSIM reservation failed after payment', [
                                'esim_order_id' => $esimOrder->id,
                                'error' => $reservation['error'] ?? 'Unknown',
                            ]);
                        }
                    } catch (\Exception $e) {
                        $esimOrder->update(['reservation_status' => 'error']);
                        Log::error('MontyeSIM API error after payment', [
                            'error' => $e->getMessage(),
                            'esim_order_id' => $esimOrder->id,
                        ]);
                    }
                } else {
                    $esimOrder->update(['payment_status' => 'failed']);
                }
            }
        }
    }

    private function getCustomerData($orderId, $bookingType)
    {
        if ($bookingType == 1 || stripos($orderId, 'UAEV') !== false) {
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
        if (stripos($orderId, 'ORDUM') !== false) {
            return 'umrah';
        } elseif (stripos($orderId, 'ORDESIM') !== false) {
            return 4;
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
}
