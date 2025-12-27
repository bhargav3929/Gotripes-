<?php

namespace App\Http\Controllers;

use App\Models\CCAvenueTransaction;
use App\Models\UAEVApplication;
use App\Models\ActivityBooking;
use App\Models\AgentBooking;
use App\Models\UAEActivity;
use App\Mail\PaymentStatusMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
// use Illuminate\Support\Facades\Log;

require_once app_path('Helpers/Crypto.php');

class CCAvenueController extends Controller
{
    public function initiatePayment(Request $request)
    {
        $orderId = $request->input('order_id');
        $amount = $request->input('amount');
        $bookingType = $request->input('booking_type', 1);

        if (!$orderId || !$amount) {
            return response()->json(['error' => 'Missing order_id or amount'], 400);
        }

        $customerData = $this->getCustomerData($orderId, $bookingType);

        if (!$customerData) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        $merchantId = config('services.ccavenue.merchant_id');
        $workingKey = config('services.ccavenue.working_key');
        $accessCode = config('services.ccavenue.access_code');
        $cancelUrl = config('services.ccavenue.cancel_url');

        $paymentData = [
            'merchant_id' => $merchantId,
            'order_id' => $orderId,
            'currency' => 'AED',
            'amount' => number_format($amount, 2, '.', ''),
            'redirect_url' => config('services.ccavenue.redirect_url'),
            'cancel_url' => $cancelUrl,
            'language' => 'EN',
            'billing_name' => $customerData['name'],
            'billing_email' => $customerData['email'],
            'billing_tel' => $customerData['phone'],
        ];

        $paramString = http_build_query($paymentData);

        try {
            $encryptedData = ccavenue_encrypt($paramString, $workingKey);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Encryption failed'], 500);
        }

        return response()->json([
            'encryptedData' => $encryptedData,
            'accessCode' => $accessCode,
            'merchant_id' => $merchantId,
        ]);
    }

    public function handleResponse(Request $request)
    {
        $workingKey = config('services.ccavenue.working_key');
        $encResp = $request->input('encResp');

        if (!$encResp) {
            return view('payment.status')->with('response', ['error' => 'Invalid response']);
        }

        try {
            $decryptedResponse = ccavenue_decrypt($encResp, $workingKey);
        } catch (\Exception $e) {
            return view('payment.status')->with('response', ['error' => 'Decryption failed']);
        }

        parse_str($decryptedResponse, $responseArr);

        $orderId = $responseArr['order_id'] ?? null;
        if (!$orderId) {
            return view('payment.status')->with('response', ['error' => 'Invalid order ID']);
        }

        $bookingType = $this->determineBookingType($orderId);

        $transaction = CCAvenueTransaction::updateOrCreate(
            ['order_id' => $orderId],
            [
                'tracking_id' => $responseArr['tracking_id'] ?? null,
                'bank_ref_no' => $responseArr['bank_ref_no'] ?? null,
                'order_status' => $responseArr['order_status'] ?? null,
                'failure_message' => $responseArr['failure_message'] ?? null,
                'amount' => $responseArr['amount'] ?? null,
                'payment_mode' => $responseArr['payment_mode'] ?? null,
                'booking_type' => $bookingType,
                'response_data' => json_encode($responseArr),
            ]
        );

        $this->sendPaymentStatusEmail($orderId, $responseArr['order_status'] ?? 'Failed', $responseArr);

        $this->updateBookingStatus($orderId, $responseArr['order_status'] ?? 'Failed', $bookingType);

        return view('payment.status')->with('response', $responseArr);
    }

    public function cancel(Request $request)
    {
        $orderId = null;
        $responseArr = [];

        $encResp = $request->input('encResp');
        if ($encResp) {
            try {
                $workingKey = config('services.ccavenue.working_key');
                $decryptedResponse = ccavenue_decrypt($encResp, $workingKey);

                parse_str($decryptedResponse, $responseArr);
                $orderId = $responseArr['order_id'] ?? null;

            } catch (\Exception $e) {
                // Continue to fallback
            }
        }

        if (!$orderId) {
            $orderId = $request->query('order_id') ??
                $request->input('order_id') ??
                $request->input('orderNo') ??
                null;
        }

        if ($orderId) {
            $bookingType = $this->determineBookingType($orderId);

            $cancellationData = array_merge($responseArr, [
                'order_id' => $orderId,
                'order_status' => 'Cancelled',
                'failure_message' => 'Payment cancelled by user',
                'cancelled_at' => now()->toDateTimeString()
            ]);

            $transaction = CCAvenueTransaction::updateOrCreate(
                ['order_id' => $orderId],
                [
                    'order_status' => 'Cancelled',
                    'failure_message' => 'Payment cancelled by user',
                    'booking_type' => $bookingType,
                    'response_data' => json_encode($cancellationData),
                ]
            );

            $this->sendPaymentStatusEmail($orderId, 'Cancelled', $cancellationData);
        }

        return view('payment.cancel');
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

    // UPDATED: Added debugging for booking date
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
                // DEBUG: Log what we found in the database
                // Log::info('DEBUG: Booking found', [
                //     'booking_id' => $bookingId,
                //     'date_from_model' => $booking->date,
                //     'date_raw' => $booking->getAttributes()['date'] ?? 'No date attribute',
                //     'all_attributes' => $booking->getAttributes()
                // ]);

                $bookingData = $booking->toArray();

                // Fetch activity name using the activityId from booking
                if ($booking->activityId) {
                    $activity = UAEActivity::where('activityID', $booking->activityId)->first();
                    if ($activity) {
                        $bookingData['activity_name'] = $activity->activityName;
                    } else {
                        $bookingData['activity_name'] = 'Activity Not Found';
                    }
                } else {
                    $bookingData['activity_name'] = 'No Activity ID';
                }

                // ENHANCED: Handle booking date with multiple fallbacks and debugging
                $bookingDate = null;

                // Try approach 1: Using the model attribute (with casting)
                if ($booking->date) {
                    try {
                        $bookingDate = $booking->date->format('d M Y');
                        // Log::info('DEBUG: Date from model casting worked', ['formatted_date' => $bookingDate]);
                    } catch (\Exception $e) {
                        // Log::error('DEBUG: Model casting failed', ['error' => $e->getMessage()]);
                    }
                }

                // Try approach 2: Direct raw attribute access
                if (!$bookingDate && isset($booking->getAttributes()['date']) && $booking->getAttributes()['date']) {
                    try {
                        $rawDate = $booking->getAttributes()['date'];
                        $bookingDate = \Carbon\Carbon::createFromFormat('Y-m-d', $rawDate)->format('d M Y');
                        // Log::info('DEBUG: Date from raw attribute worked', ['raw_date' => $rawDate, 'formatted_date' => $bookingDate]);
                    } catch (\Exception $e) {
                        // Log::error('DEBUG: Raw attribute parsing failed', ['raw_date' => $rawDate ?? 'null', 'error' => $e->getMessage()]);
                    }
                }

                // Try approach 3: From toArray() result
                if (!$bookingDate && isset($bookingData['date']) && $bookingData['date']) {
                    try {
                        $arrayDate = $bookingData['date'];
                        $bookingDate = \Carbon\Carbon::parse($arrayDate)->format('d M Y');
                        // Log::info('DEBUG: Date from array worked', ['array_date' => $arrayDate, 'formatted_date' => $bookingDate]);
                    } catch (\Exception $e) {
                        // Log::error('DEBUG: Array date parsing failed', ['array_date' => $arrayDate ?? 'null', 'error' => $e->getMessage()]);
                    }
                }

                // Final assignment
                $bookingData['booking_date'] = $bookingDate ?: 'Date Not Available';

                // Log::info('DEBUG: Final booking date assigned', [
                //     'booking_date' => $bookingData['booking_date'],
                //     'activity_name' => $bookingData['activity_name']
                // ]);

                return [
                    'type' => 'activity',
                    'data' => $bookingData
                ];
            } else {
                // Log::error('DEBUG: Booking not found', ['booking_id' => $bookingId]);
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
                    'name' => $booking->client_name, // Fixed: accessing property, not array
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
