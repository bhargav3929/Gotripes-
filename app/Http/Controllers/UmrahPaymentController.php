<?php

namespace App\Http\Controllers;

use App\Models\NomodTransaction;
use App\Services\NomodService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UmrahPaymentController extends Controller
{
    public function initiate(Request $request)
    {
        $request->validate([
            'package_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
        ]);

        $packageName = $request->input('package_name');
        $amount = round((float) $request->input('amount'), 2);
        $orderId = 'ORDUM' . time();

        try {
            $nomodService = new NomodService();
            $checkout = $nomodService->createCheckout([
                'amount' => $amount,
                'currency' => 'AED',
                'order_id' => $orderId,
                'description' => $packageName,
            ]);

            if (!($checkout['success'] ?? false)) {
                return response()->json([
                    'success' => false,
                    'error' => $checkout['error'] ?? 'Payment gateway error.',
                ], 500);
            }

            NomodTransaction::create([
                'checkout_id' => $checkout['checkout_id'],
                'order_id' => $orderId,
                'status' => 'created',
                'amount' => $amount,
                'currency' => 'AED',
                'booking_type' => 'umrah',
                'checkout_url' => $checkout['checkout_url'],
                'metadata' => ['package_name' => $packageName],
                'response_data' => $checkout['data'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'checkout_url' => $checkout['checkout_url'],
                'orderId' => $orderId,
            ]);

        } catch (\Exception $e) {
            Log::error('Umrah payment initiation failed', [
                'error' => $e->getMessage(),
                'package' => $packageName,
                'amount' => $amount,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Payment initiation failed. Please try again.',
            ], 500);
        }
    }
}
