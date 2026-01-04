<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgentBooking;
use Illuminate\Support\Facades\Validator;

require_once app_path('Helpers/Crypto.php');

class AgentBookingController extends Controller
{
    public function submit(Request $request)
    {
        error_reporting(0);
        ini_set('display_errors', 0);
        ob_start();

        // Check if this is an AJAX/JSON request
        $isAjax = $request->ajax() || $request->wantsJson() || $request->expectsJson();

        $validator = Validator::make($request->all(), [
            'client_name' => 'required|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_phone' => 'required|string|max:50',
            'service' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'agent_name' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            ob_end_clean();
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Validate CCAvenue configuration
            $merchantId = config('services.ccavenue.merchant_id');
            $workingKey = config('services.ccavenue.working_key');
            $accessCode = config('services.ccavenue.access_code');

            if (empty($merchantId) || empty($workingKey) || empty($accessCode)) {
                \Log::error('CCAvenue configuration missing', [
                    'merchant_id_set' => !empty($merchantId),
                    'working_key_set' => !empty($workingKey),
                    'access_code_set' => !empty($accessCode)
                ]);

                if ($isAjax) {
                    ob_end_clean();
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment gateway configuration error. Please contact support.'
                    ], 500);
                }
                return redirect()->back()->with('error', 'Payment gateway configuration error.');
            }

            // Save to Database
            $booking = AgentBooking::create([
                'agent_name' => $request->input('agent_name', 'Agent'),
                'client_name' => $request->client_name,
                'client_email' => $request->input('client_email', 'no-email-provided@gotrips.com'),
                'client_phone' => $request->client_phone,
                'service_type' => $request->service,
                'amount' => $request->amount,
                'currency' => 'AED',
                'details' => $request->input('remarks', ''),
                'payment_status' => 'Pending',
            ]);

            // Generate Order ID
            $orderId = 'ORDAG' . $booking->id;
            $booking->order_id = $orderId;
            $booking->save();

            // Prepare CC Avenue Payload
            // CRITICAL: Use EXACT same pattern as working CCAvenueController
            // The redirect_url MUST match what's registered in CCAvenue merchant account
            $cancelUrl = config('services.ccavenue.cancel_url');
            $ccavenueUrl = config('services.ccavenue.url');

            // Use the configured redirect URL that is whitelisted with CCAvenue
            $redirectUrl = config('services.ccavenue.redirect_url');

            $paymentData = [
                'merchant_id' => $merchantId,
                'order_id' => $orderId,
                'currency' => 'AED',
                'amount' => number_format((float) $booking->amount, 2, '.', ''),
                'redirect_url' => $redirectUrl,
                'cancel_url' => $cancelUrl,
                'language' => 'EN',
                'billing_name' => $booking->client_name,
                'billing_email' => $booking->client_email,
                'billing_tel' => $booking->client_phone,
            ];

            // Log payment data for debugging (without sensitive info)
            \Log::info('Agent Payment - CCAvenue Request Data', [
                'order_id' => $orderId,
                'merchant_id' => $merchantId,
                'amount' => $paymentData['amount'],
                'currency' => $paymentData['currency'],
                'redirect_url' => $redirectUrl,
                'cancel_url' => $cancelUrl,
                'has_working_key' => !empty($workingKey),
                'has_access_code' => !empty($accessCode),
            ]);

            $paramString = http_build_query($paymentData);

            // Log the parameter string length (for debugging)
            \Log::info('Agent Payment - Parameter String', [
                'order_id' => $orderId,
                'param_string_length' => strlen($paramString),
                'param_string_preview' => substr($paramString, 0, 100) . '...'
            ]);

            $encryptedData = ccavenue_encrypt($paramString, $workingKey);

            // Log encryption result
            \Log::info('Agent Payment - Encryption Result', [
                'order_id' => $orderId,
                'encrypted_length' => strlen($encryptedData),
                'encryption_success' => !empty($encryptedData)
            ]);

            if (empty($encryptedData)) {
                \Log::error('CCAvenue encryption failed', [
                    'order_id' => $orderId,
                    'param_string_length' => strlen($paramString)
                ]);

                if ($isAjax) {
                    ob_end_clean();
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment encryption failed. Please try again.'
                    ], 500);
                }
                return redirect()->back()->with('error', 'Payment encryption failed.');
            }

            $unexpectedOutput = ob_get_clean();
            if (!empty($unexpectedOutput)) {
                \Log::warning('Unexpected Output during Agent Booking: ' . $unexpectedOutput);
            }

            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'encrypted_data' => $encryptedData,
                    'access_code' => $accessCode,
                    'ccavenue_url' => $ccavenueUrl
                ], 200, [], JSON_UNESCAPED_SLASHES);
            }

            // If not AJAX (fallback)
            return redirect()->back()->with('error', 'Please enable JavaScript to proceed with payment.');

        } catch (\Exception $e) {
            $unexpectedOutput = ob_get_clean();
            if (!empty($unexpectedOutput)) {
                \Log::error('Unexpected Output during Agent Booking: ' . $unexpectedOutput);
            }

            \Log::error('Agent Payment Submission Error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while processing your payment. Please try again or contact support.'
                ], 500);
            }
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
