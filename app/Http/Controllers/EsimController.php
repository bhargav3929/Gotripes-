<?php

namespace App\Http\Controllers;

use App\Models\EsimOrder;
use App\Models\NomodTransaction;
use App\Services\MontyEsimService;
use App\Services\NomodService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EsimController extends Controller
{
    public function index()
    {
        return view('esim');
    }

    /**
     * Return available countries as JSON (proxied + cached from MontyeSIM).
     */
    public function getCountries(): JsonResponse
    {
        try {
            $service = new MontyEsimService();
            $countries = $service->getCountries();

            // Also fetch minimum prices per country for "from $X" display
            // Countries are cached, so this is lightweight
            return response()->json([
                'success' => true,
                'countries' => $countries,
            ]);
        } catch (\Exception $e) {
            Log::error('eSIM getCountries failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Unable to load countries. Please try again.',
            ], 500);
        }
    }

    /**
     * Return bundles for a specific country.
     */
    public function getBundles(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'country_code' => 'required|string|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => 'Invalid country code.'], 422);
        }

        try {
            $service = new MontyEsimService();
            $bundles = $service->getBundles($request->country_code);

            return response()->json([
                'success' => true,
                'bundles' => $bundles,
            ]);
        } catch (\Exception $e) {
            Log::error('eSIM getBundles failed', [
                'country_code' => $request->country_code,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Unable to load plans. Please try again.',
            ], 500);
        }
    }

    /**
     * Process eSIM purchase: validate, create order, initiate Nomod checkout.
     */
    public function purchase(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:200',
            'phone' => 'nullable|string|max:20',
            'bundle_code' => 'required|string|max:200',
            'country_code' => 'required|string|max:5',
            'country_name' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Please fill in all required fields correctly.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Re-fetch bundle from API for server-side price verification
            $montyService = new MontyEsimService();
            $bundle = $montyService->findBundle($request->bundle_code, $request->country_code);

            if (!$bundle) {
                return response()->json([
                    'success' => false,
                    'error' => 'Selected plan is no longer available. Please choose another.',
                ], 404);
            }

            $costPrice = $bundle['cost_price'];
            $sellingPrice = $bundle['selling_price'];
            $dataAmount = ($bundle['unlimited'] ?? false)
                ? 'Unlimited'
                : ($bundle['gprs_limit'] ?? 0) . ' ' . ($bundle['data_unit'] ?? 'GB');
            $validityDays = (int) ($bundle['validity'] ?? 0);
            $bundleName = $bundle['bundle_marketing_name'] ?? $bundle['bundle_name'] ?? 'eSIM Plan';

            // Create eSIM order
            $esimOrder = EsimOrder::create([
                'order_reference' => '', // Will set after we have the ID
                'customer_name' => $request->name,
                'customer_email' => $request->email,
                'customer_phone' => $request->phone,
                'country_code' => $request->country_code,
                'country_name' => $request->country_name,
                'bundle_code' => $request->bundle_code,
                'bundle_name' => $bundleName,
                'data_amount' => $dataAmount,
                'validity_days' => $validityDays,
                'monty_cost_price' => $costPrice,
                'selling_price' => $sellingPrice,
                'currency' => 'AED',
                'reservation_status' => 'pending',
                'payment_status' => 'unpaid',
            ]);

            // Set the order reference with the ID
            $orderReference = 'ORDESIM' . $esimOrder->id;
            $esimOrder->update(['order_reference' => $orderReference]);

            // Create Nomod checkout
            $nomodService = new NomodService();
            $checkoutResult = $nomodService->createCheckout([
                'amount' => $sellingPrice,
                'currency' => 'AED',
                'order_id' => $orderReference,
                'description' => "eSIM: {$bundleName} - {$request->country_name}",
                'customer' => [
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone ?? '',
                ],
                'items' => [
                    [
                        'item_id' => $request->bundle_code,
                        'name' => "{$bundleName} ({$dataAmount}, {$validityDays} days)",
                        'quantity' => 1,
                        'unit_amount' => number_format($sellingPrice, 2, '.', ''),
                    ],
                ],
                'metadata' => [
                    'type' => 'esim',
                    'esim_order_id' => $esimOrder->id,
                    'country' => $request->country_name,
                ],
            ]);

            if (!$checkoutResult['success']) {
                Log::error('eSIM Nomod checkout failed', [
                    'esim_order_id' => $esimOrder->id,
                    'error' => $checkoutResult['error'] ?? 'Unknown',
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'Payment initialization failed. Please try again.',
                ], 500);
            }

            // Store Nomod transaction
            NomodTransaction::create([
                'checkout_id' => $checkoutResult['checkout_id'],
                'order_id' => $orderReference,
                'status' => 'created',
                'amount' => $sellingPrice,
                'currency' => 'AED',
                'booking_type' => 'esim',
                'checkout_url' => $checkoutResult['checkout_url'],
                'items' => [
                    [
                        'item_id' => $request->bundle_code,
                        'name' => $bundleName,
                        'quantity' => 1,
                        'unit_amount' => $sellingPrice,
                    ],
                ],
                'customer' => [
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                ],
                'metadata' => [
                    'type' => 'esim',
                    'esim_order_id' => $esimOrder->id,
                    'country_code' => $request->country_code,
                    'country_name' => $request->country_name,
                ],
            ]);

            return response()->json([
                'success' => true,
                'checkout_url' => $checkoutResult['checkout_url'],
                'order_reference' => $orderReference,
            ]);

        } catch (\Exception $e) {
            Log::error('eSIM purchase exception', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'error' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }
}
