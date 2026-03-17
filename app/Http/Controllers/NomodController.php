<?php

namespace App\Http\Controllers;

use App\Models\NomodTransaction;
use App\Models\UAEVApplication;
use App\Models\ActivityBooking;
use App\Models\AgentBooking;
use App\Models\UAEActivity;
use App\Mail\PaymentStatusMail;
use App\Mail\SupplierBookingMail;
use App\Mail\CustomerPaymentConfirmationMail;
use App\Mail\AdminPaymentNotificationMail;
use App\Services\NomodService;
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
            $adminEmail = config('mail.from.address');

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
        if (stripos($orderId, 'UAEV') !== false) {
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
        if ($bookingType === 1 && stripos($orderId, 'UAEV') !== false) {
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
        }

        return null;
    }

    private function determineBookingType($orderId)
    {
        if (stripos($orderId, 'UAEV') !== false) {
            return 1;
        } elseif (stripos($orderId, 'AB') !== false) {
            return 2;
        } elseif (stripos($orderId, 'ORDAG') !== false) {
            return 3;
        }
        return 1;
    }
}
