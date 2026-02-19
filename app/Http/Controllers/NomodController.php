<?php
// Nomod Hosted Checkout payment callback controller

namespace App\Http\Controllers;

use App\Models\NomodTransaction;
use App\Models\UAEVApplication;
use App\Models\ActivityBooking;
use App\Models\AgentBooking;
use App\Models\UAEActivity;
use App\Mail\PaymentStatusMail;
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

    private function sendPaymentStatusEmail($orderId, $paymentStatus, $responseData = [])
    {
        try {
            $bookingData = $this->getBookingData($orderId);

            if (!$bookingData) {
                return false;
            }

            $mailData = array_merge($bookingData['data'], $responseData);
            $mailData['order_id'] = $orderId;
            $mailData['payment_status'] = $paymentStatus;
            $mailData['sent_at'] = now()->toDateTimeString();

            $customerEmail = $this->extractCustomerEmail($bookingData['data'], $bookingData['type']);

            if (!$customerEmail) {
                return false;
            }

            $adminEmails = $this->getEmailRecipients($bookingData['type']);
            $recipients = array_merge([$customerEmail], $adminEmails);
            $recipients = array_unique(array_filter($recipients));

            $mailable = new PaymentStatusMail($mailData, $paymentStatus, $bookingData['type']);

            Mail::to($recipients)->send($mailable);

            return true;

        } catch (\Exception $e) {
            Log::error('Nomod payment email failed', ['error' => $e->getMessage(), 'order_id' => $orderId]);
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
