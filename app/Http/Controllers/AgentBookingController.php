<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgentBooking;
use App\Models\NomodTransaction;
use App\Services\NomodService;
use Illuminate\Support\Facades\Validator;

class AgentBookingController extends Controller
{
    public function submit(Request $request)
    {
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
            // Validate Nomod configuration
            $apiKey = config('nomod.api_key');

            if (empty($apiKey)) {
                \Log::error('Nomod API key missing');

                if ($isAjax) {
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

            // Create Nomod checkout
            $nomodService = new NomodService();
            $checkout = $nomodService->createCheckout([
                'amount' => round((float) $booking->amount, 2),
                'currency' => 'AED',
                'order_id' => $orderId,
                'description' => 'Agent Payment - ' . $request->service,
                'customer' => [
                    'name' => $booking->client_name,
                    'email' => $booking->client_email,
                    'phone' => $booking->client_phone,
                ],
            ]);

            if (!$checkout['success']) {
                \Log::error('Nomod checkout creation failed', [
                    'order_id' => $orderId,
                    'error' => $checkout['error'] ?? 'Unknown',
                ]);

                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'message' => $checkout['error'] ?? 'Payment initiation failed. Please try again.'
                    ], 500);
                }
                return redirect()->back()->with('error', 'Payment initiation failed.');
            }

            NomodTransaction::create([
                'checkout_id' => $checkout['checkout_id'],
                'order_id' => $orderId,
                'status' => 'created',
                'amount' => round((float) $booking->amount, 2),
                'currency' => 'AED',
                'booking_type' => 'agent_booking',
                'checkout_url' => $checkout['checkout_url'],
                'customer' => ['name' => $booking->client_name, 'email' => $booking->client_email, 'phone' => $booking->client_phone],
                'response_data' => $checkout['data'] ?? null,
            ]);

            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'checkout_url' => $checkout['checkout_url'],
                ]);
            }

            return redirect()->back()->with('error', 'Please enable JavaScript to proceed with payment.');

        } catch (\Exception $e) {
            \Log::error('Agent Payment Submission Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
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
