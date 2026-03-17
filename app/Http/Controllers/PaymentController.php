<?php
// DEPRECATED: This controller used CCAvenue and has been replaced by NomodController.
// Kept for historical reference.

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    // Show the payment form to the user
    public function showForm()
    {
        Log::info('Displaying CCAvenue payment form');
        return view('payment_form');
    }

    // Initiate payment and redirect to CCAvenue UAE gateway
    public function initiatePayment(Request $request)
    {
        Log::info('Initiate payment request received', $request->all());

        require_once(app_path('Helpers/Crypto.php'));

        $order_id = uniqid();
        $amount = $request->input('amount');

        // Prepare the data as per UAE requirements
        $data = [
            'merchant_id'   => config('services.ccavenue.merchant_id'),
            'order_id'      => $order_id,
            'currency'      => 'AED',
            'amount'        => $amount,
            'redirect_url'  => config('services.ccavenue.redirect_url'),
            'cancel_url'    => config('services.ccavenue.cancel_url'),
            'language'      => 'EN',
            'billing_name'  => "Test Customer", // Adjust as needed
        ];

        Log::info('Built payment data array', $data);

        // Format merchant data string
        $merchant_data = '';
        foreach ($data as $key => $value) {
            $merchant_data .= $key . '=' . $value . '&';
        }
        $merchant_data = rtrim($merchant_data, '&');

        Log::info('Merchant data string', ['merchant_data' => $merchant_data]);

        // Encrypt merchant data
        $working_key = config('services.ccavenue.working_key');
        $access_code = config('services.ccavenue.access_code');
        $encRequest = ccavenue_encrypt($merchant_data, $working_key);

        Log::info('Encrypted request data for CCAvenue', [
            'encRequest' => $encRequest,
            'merchant_data' => $merchant_data,
        ]);

        $ccavenue_url = config('services.ccavenue.url');
        Log::info('Will POST to CCAvenue UAE at:', ['ccavenue_url' => $ccavenue_url]);

        return view('ccavenue_redirect', [
            'encRequest'    => $encRequest,
            'accessCode'    => $access_code,
            'ccavenue_url'  => $ccavenue_url,
            'order_id'      => $order_id,
            'amount'        => $amount,
        ]);
    }

    // Handle response from CCAvenue UAE
    public function paymentResponse(Request $request)
    {
        Log::info('Received POST from CCAvenue UAE', $request->all());

        require_once(app_path('Helpers/Crypto.php'));
        $working_key = config('services.ccavenue.working_key');
        $encResponse = $request->input('encResp');

        Log::info('Raw encrypted response', ['encResp' => $encResponse]);

        $decryptedResponse = ccavenue_decrypt($encResponse, $working_key);
        Log::info('Decrypted payment response', [
            'decryptedResponse' => $decryptedResponse,
        ]);

        parse_str($decryptedResponse, $responseData);

        Log::info('Parsed payment response array', $responseData);

        if (isset($responseData['order_status'])) {
            if ($responseData['order_status'] === "Success") {
                Log::info('Payment Success', $responseData);
                return view('payment_success', compact('responseData'));
            }
            if ($responseData['order_status'] === "Failure") {
                Log::warning('Payment Failure', $responseData);
                return view('payment_failure', compact('responseData'));
            }
            if ($responseData['order_status'] === "Aborted") {
                Log::warning('Payment Aborted', $responseData);
                return view('payment_cancel', compact('responseData'));
            }
            Log::error('Unknown order_status in CCAvenue response', $responseData);
            return 'Invalid response received from CCAvenue.';
        } else {
            Log::error('Order status missing in CCAvenue response!', $responseData);
            return 'No order status found in CCAvenue response.';
        }
    }

    // Optionally handle a cancelled payment route (GET)
    public function paymentCancel(Request $request)
    {
        Log::info('CCAvenue payment cancelled by user or system');
        return view('payment_cancel');
    }
}
