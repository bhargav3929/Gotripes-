<?php

namespace App\Http\Controllers;

use App\Models\EsimOrder;
use App\Models\NomodTransaction;
use App\Services\MontyEsimService;
use App\Services\NomodService;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EsimController extends Controller
{
    /**
     * Most eSIMs one order may cover.
     *
     * Sized for a full coach with room to spare. The real constraint is the
     * prepaid reseller wallet, which is checked against the whole order below —
     * this cap only stops a mistyped quantity becoming a very large charge.
     */
    public const MAX_QUANTITY = 50;

    /**
     * How recent an existing unpaid order for the same customer/bundle/
     * quantity must be to be treated as a duplicate submission (double-click
     * across tabs, refresh-and-resubmit, or a network-level retry) rather
     * than a genuinely new, separate purchase.
     */
    private const DUPLICATE_WINDOW_SECONDS = 120;

    private function tenantMarkup(): ?float
    {
        $company = current_company();
        return $company && $company->markup_percentage > 0
            ? (float) $company->markup_percentage
            : null;
    }

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
            $service = new MontyEsimService($this->tenantMarkup());
            $countries = $service->getCountries();

            return response()->json([
                'success' => true,
                'countries' => $countries,
            ]);
        } catch (\Exception $e) {
            // Deliberately no static fallback: listing countries we cannot
            // actually sell for turns a provider outage into "every plan is
            // sold out", which is far harder to diagnose and still wastes the
            // customer's time.
            Log::error('eSIM countries unavailable', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'countries' => [],
                'error' => 'eSIM plans are temporarily unavailable. Please try again shortly.',
            ], 503);
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
            $service = new MontyEsimService($this->tenantMarkup());
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
            'phone' => 'required|string|max:20',
            'bundle_code' => 'required|string|max:200',
            'country_code' => 'required|string|max:5',
            'country_name' => 'required|string|max:100',
            // Tour operators buy one plan for a whole coach. Capped so a typo
            // cannot drain the reseller wallet in a single click.
            'quantity' => 'nullable|integer|min:1|max:' . self::MAX_QUANTITY,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Please fill in all required fields correctly.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $quantity = max(1, (int) $request->input('quantity', 1));

        // Duplicate-submission guard. Nothing before this point creates any
        // state, so a request that turns out to be a duplicate costs nothing.
        //
        // The lock serializes two requests that arrive close enough together
        // that neither would otherwise see the other's row yet (two tabs
        // submitted within the same instant, or a client-side retry firing
        // before the first request has committed) — without it, both could
        // pass the "any recent duplicate?" check below at the same time and
        // each create their own order. The lock only ever guards this one
        // purchase intent (keyed on email+bundle+country+quantity), so it
        // cannot block or delay any other customer's purchase.
        $lockKey = 'esim_purchase:' . sha1(strtolower((string) $request->email) . '|' . $request->bundle_code . '|' . $request->country_code . '|' . $quantity);
        $lock = Cache::lock($lockKey, 20);

        try {
            $lock->block(5);
        } catch (LockTimeoutException $e) {
            return response()->json([
                'success' => false,
                'error' => 'This purchase is already being processed. Please wait a moment before trying again.',
            ], 429);
        }

        try {
            // An existing unpaid order for the exact same customer/bundle/
            // country/quantity, created moments ago, is almost certainly this
            // same purchase intent arriving twice — not a second, deliberate
            // purchase. Reuse its checkout rather than creating another payable
            // order and another Nomod checkout session. An order outside the
            // window, or already paid/failed/cancelled, is never touched here —
            // only a fresh, still-open attempt qualifies.
            $existingOrder = EsimOrder::where('customer_email', $request->email)
                ->where('bundle_code', $request->bundle_code)
                ->where('country_code', $request->country_code)
                ->where('quantity', $quantity)
                ->where('payment_status', 'unpaid')
                ->where('created_at', '>=', now()->subSeconds(self::DUPLICATE_WINDOW_SECONDS))
                ->orderByDesc('id')
                ->first();

            if ($existingOrder) {
                $existingCheckout = NomodTransaction::where('order_id', $existingOrder->order_reference)
                    ->where('status', 'created')
                    ->orderByDesc('id')
                    ->first();

                if ($existingCheckout && $existingCheckout->checkout_url) {
                    Log::info('eSIM purchase: reusing existing unpaid order/checkout for a duplicate submission', [
                        'esim_order_id'   => $existingOrder->id,
                        'order_reference' => $existingOrder->order_reference,
                    ]);

                    return response()->json([
                        'success' => true,
                        'checkout_url' => $existingCheckout->checkout_url,
                        'order_reference' => $existingOrder->order_reference,
                    ]);
                }
                // An unpaid order exists but has no usable checkout (e.g. Nomod
                // was down on the prior attempt) — reuse the ORDER below rather
                // than creating a second one, and just create a fresh checkout
                // for it.
            }

            // Price and plan details are always re-fetched from the provider.
            // A placeholder/demo branch used to live here: any bundle_code
            // starting with "esim_" was priced from a hardcoded table and sold
            // happily, even though no such bundle exists upstream — so the
            // customer paid and provisioning then failed every time.
            $montyService = new MontyEsimService($this->tenantMarkup());
            $bundle = $montyService->findBundle($request->bundle_code, $request->country_code);

            if (!$bundle) {
                return response()->json([
                    'success' => false,
                    'error' => 'Selected plan is no longer available. Please choose another.',
                ], 404);
            }

            $costPrice = $bundle['cost_price'];
            // Per-eSIM price, and the order total the customer is charged.
            $unitSellingPrice = (float) $bundle['selling_price'];
            $sellingPrice = round($unitSellingPrice * $quantity, 2);
            $dataAmount = ($bundle['unlimited'] ?? false)
                ? 'Unlimited'
                : ($bundle['gprs_limit'] ?? 0) . ' ' . ($bundle['data_unit'] ?? 'GB');
            $validityDays = (int) ($bundle['validity'] ?? 0);
            $bundleName = $bundle['bundle_marketing_name'] ?? $bundle['bundle_name'] ?? 'eSIM Plan';

            // Refuse the sale if the reseller wallet cannot cover the cost.
            // Taking the customer's money for a bundle we then cannot buy is
            // the worst outcome available, and the wallet running dry is how
            // orders 37-39 were lost.
            // The whole order is checked, not one unit: a 30-eSIM group booking
            // costs thirty times as much, and half-provisioning it would leave
            // paying customers without an eSIM.
            $orderNetCost = (float) ($bundle['bundle_price_final'] ?? 0) * $quantity;

            if (!$montyService->canAfford($orderNetCost)) {
                Log::critical('eSIM sale blocked: reseller wallet cannot cover the order', [
                    'bundle_code' => $request->bundle_code,
                    'quantity'    => $quantity,
                    'net_cost'    => $orderNetCost,
                ]);

                return response()->json([
                    'success' => false,
                    'error' => $quantity > 1
                        ? 'We cannot issue this many eSIMs right now. Please try a smaller quantity or contact support.'
                        : 'This plan is temporarily unavailable. Please try again later or contact support.',
                ], 503);
            }

            if ($existingOrder) {
                // Reuse the order found above rather than creating a second one
                // for the same purchase intent — only its checkout was missing.
                $esimOrder = $existingOrder;
                $orderReference = $esimOrder->order_reference;
            } else {
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
                    'quantity' => $quantity,
                    'data_amount' => $dataAmount,
                    'validity_days' => $validityDays,
                    'monty_cost_price' => $costPrice,
                    'selling_price' => $sellingPrice,
                    'unit_selling_price' => $unitSellingPrice,
                    'currency' => 'AED',
                    'reservation_status' => 'pending',
                    'payment_status' => 'unpaid',
                ]);

                // Set the order reference with the ID
                $orderReference = 'ORDESIM' . $esimOrder->id;
                $esimOrder->update(['order_reference' => $orderReference]);
            }

            // Create Nomod checkout
            $nomodService = new NomodService();
            $checkoutResult = $nomodService->createCheckout([
                'amount' => $sellingPrice,
                'currency' => 'AED',
                'order_id' => $orderReference,
                'description' => $quantity > 1
                    ? "eSIM x{$quantity}: {$bundleName} - {$request->country_name}"
                    : "eSIM: {$bundleName} - {$request->country_name}",
                'customer' => array_filter([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone ?: null,
                ]),
                'items' => [
                    [
                        'item_id' => substr($request->bundle_code, 0, 50),
                        'name' => "{$bundleName} ({$dataAmount}, {$validityDays} days)",
                        'quantity' => $quantity,
                        'unit_amount' => number_format($unitSellingPrice, 2, '.', ''),
                    ],
                ],
                'metadata' => [
                    'type' => 'esim',
                    'esim_order_id' => (string) $esimOrder->id,
                    'country' => $request->country_name,
                    'quantity' => (string) $quantity,
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
        } finally {
            $lock->release();
        }
    }
}
