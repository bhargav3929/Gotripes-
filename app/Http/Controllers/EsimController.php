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

            return response()->json([
                'success' => true,
                'countries' => $countries,
            ]);
        } catch (\Exception $e) {
            Log::warning('eSIM API unavailable, using static country list', ['error' => $e->getMessage()]);

            $countries = self::getStaticCountries();
            return response()->json([
                'success' => true,
                'countries' => $countries,
            ]);
        }
    }

    /**
     * Static fallback country list when MontyeSIM API is unavailable.
     */
    private static function getStaticCountries(): array
    {
        return [
            ['country_name' => 'Afghanistan', 'iso2_code' => 'AF', 'iso3_code' => 'AFG'],
            ['country_name' => 'Albania', 'iso2_code' => 'AL', 'iso3_code' => 'ALB'],
            ['country_name' => 'Algeria', 'iso2_code' => 'DZ', 'iso3_code' => 'DZA'],
            ['country_name' => 'Argentina', 'iso2_code' => 'AR', 'iso3_code' => 'ARG'],
            ['country_name' => 'Australia', 'iso2_code' => 'AU', 'iso3_code' => 'AUS'],
            ['country_name' => 'Austria', 'iso2_code' => 'AT', 'iso3_code' => 'AUT'],
            ['country_name' => 'Bangladesh', 'iso2_code' => 'BD', 'iso3_code' => 'BGD'],
            ['country_name' => 'Belgium', 'iso2_code' => 'BE', 'iso3_code' => 'BEL'],
            ['country_name' => 'Brazil', 'iso2_code' => 'BR', 'iso3_code' => 'BRA'],
            ['country_name' => 'Canada', 'iso2_code' => 'CA', 'iso3_code' => 'CAN'],
            ['country_name' => 'Chile', 'iso2_code' => 'CL', 'iso3_code' => 'CHL'],
            ['country_name' => 'China', 'iso2_code' => 'CN', 'iso3_code' => 'CHN'],
            ['country_name' => 'Colombia', 'iso2_code' => 'CO', 'iso3_code' => 'COL'],
            ['country_name' => 'Czech Republic', 'iso2_code' => 'CZ', 'iso3_code' => 'CZE'],
            ['country_name' => 'Denmark', 'iso2_code' => 'DK', 'iso3_code' => 'DNK'],
            ['country_name' => 'Egypt', 'iso2_code' => 'EG', 'iso3_code' => 'EGY'],
            ['country_name' => 'Finland', 'iso2_code' => 'FI', 'iso3_code' => 'FIN'],
            ['country_name' => 'France', 'iso2_code' => 'FR', 'iso3_code' => 'FRA'],
            ['country_name' => 'Germany', 'iso2_code' => 'DE', 'iso3_code' => 'DEU'],
            ['country_name' => 'Greece', 'iso2_code' => 'GR', 'iso3_code' => 'GRC'],
            ['country_name' => 'Hong Kong', 'iso2_code' => 'HK', 'iso3_code' => 'HKG'],
            ['country_name' => 'Hungary', 'iso2_code' => 'HU', 'iso3_code' => 'HUN'],
            ['country_name' => 'India', 'iso2_code' => 'IN', 'iso3_code' => 'IND'],
            ['country_name' => 'Indonesia', 'iso2_code' => 'ID', 'iso3_code' => 'IDN'],
            ['country_name' => 'Ireland', 'iso2_code' => 'IE', 'iso3_code' => 'IRL'],
            ['country_name' => 'Israel', 'iso2_code' => 'IL', 'iso3_code' => 'ISR'],
            ['country_name' => 'Italy', 'iso2_code' => 'IT', 'iso3_code' => 'ITA'],
            ['country_name' => 'Japan', 'iso2_code' => 'JP', 'iso3_code' => 'JPN'],
            ['country_name' => 'Malaysia', 'iso2_code' => 'MY', 'iso3_code' => 'MYS'],
            ['country_name' => 'Mexico', 'iso2_code' => 'MX', 'iso3_code' => 'MEX'],
            ['country_name' => 'Netherlands', 'iso2_code' => 'NL', 'iso3_code' => 'NLD'],
            ['country_name' => 'New Zealand', 'iso2_code' => 'NZ', 'iso3_code' => 'NZL'],
            ['country_name' => 'Norway', 'iso2_code' => 'NO', 'iso3_code' => 'NOR'],
            ['country_name' => 'Pakistan', 'iso2_code' => 'PK', 'iso3_code' => 'PAK'],
            ['country_name' => 'Philippines', 'iso2_code' => 'PH', 'iso3_code' => 'PHL'],
            ['country_name' => 'Poland', 'iso2_code' => 'PL', 'iso3_code' => 'POL'],
            ['country_name' => 'Portugal', 'iso2_code' => 'PT', 'iso3_code' => 'PRT'],
            ['country_name' => 'Qatar', 'iso2_code' => 'QA', 'iso3_code' => 'QAT'],
            ['country_name' => 'Russia', 'iso2_code' => 'RU', 'iso3_code' => 'RUS'],
            ['country_name' => 'Saudi Arabia', 'iso2_code' => 'SA', 'iso3_code' => 'SAU'],
            ['country_name' => 'Singapore', 'iso2_code' => 'SG', 'iso3_code' => 'SGP'],
            ['country_name' => 'South Africa', 'iso2_code' => 'ZA', 'iso3_code' => 'ZAF'],
            ['country_name' => 'South Korea', 'iso2_code' => 'KR', 'iso3_code' => 'KOR'],
            ['country_name' => 'Spain', 'iso2_code' => 'ES', 'iso3_code' => 'ESP'],
            ['country_name' => 'Sweden', 'iso2_code' => 'SE', 'iso3_code' => 'SWE'],
            ['country_name' => 'Switzerland', 'iso2_code' => 'CH', 'iso3_code' => 'CHE'],
            ['country_name' => 'Taiwan', 'iso2_code' => 'TW', 'iso3_code' => 'TWN'],
            ['country_name' => 'Thailand', 'iso2_code' => 'TH', 'iso3_code' => 'THA'],
            ['country_name' => 'Turkey', 'iso2_code' => 'TR', 'iso3_code' => 'TUR'],
            ['country_name' => 'Ukraine', 'iso2_code' => 'UA', 'iso3_code' => 'UKR'],
            ['country_name' => 'United Arab Emirates', 'iso2_code' => 'AE', 'iso3_code' => 'ARE'],
            ['country_name' => 'United Kingdom', 'iso2_code' => 'GB', 'iso3_code' => 'GBR'],
            ['country_name' => 'United States', 'iso2_code' => 'US', 'iso3_code' => 'USA'],
            ['country_name' => 'Vietnam', 'iso2_code' => 'VN', 'iso3_code' => 'VNM'],
        ];
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

            if (empty($bundles)) {
                $bundles = $this->getDefaultBundles();
            }

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
                'success' => true,
                'bundles' => $this->getDefaultBundles(),
            ]);
        }
    }

    /**
     * Default bundles fallback.
     */
    private function getDefaultBundles(): array
    {
        return [
            [
                'bundle_code' => 'esim_1GB_7D',
                'bundle_name' => '1 GB Global - 7 Days',
                'bundle_marketing_name' => '1 GB Starter Plan',
                'gprs_limit' => 1,
                'data_unit' => 'GB',
                'validity' => 7,
                'selling_price' => 5.00,
                'cost_price' => 3.50,
                'unlimited' => false,
                'supports_calls_sms' => false,
                'support_topup' => true,
                'description' => 'Perfect for short trips and light browsing.',
                'isMockData' => true,
            ],
            [
                'bundle_code' => 'esim_3GB_15D',
                'bundle_name' => '3 GB Global - 15 Days',
                'bundle_marketing_name' => '3 GB Explorer Plan',
                'gprs_limit' => 3,
                'data_unit' => 'GB',
                'validity' => 15,
                'selling_price' => 10.00,
                'cost_price' => 7.00,
                'unlimited' => false,
                'supports_calls_sms' => false,
                'support_topup' => true,
                'description' => 'Ideal for social media and maps during your travels.',
                'isMockData' => true,
            ],
            [
                'bundle_code' => 'esim_5GB_30D',
                'bundle_name' => '5 GB Global - 30 Days',
                'bundle_marketing_name' => '5 GB Voyager Plan',
                'gprs_limit' => 5,
                'data_unit' => 'GB',
                'validity' => 30,
                'selling_price' => 15.00,
                'cost_price' => 11.00,
                'unlimited' => false,
                'supports_calls_sms' => true,
                'support_topup' => true,
                'description' => 'Great value for extended stays.',
                'isMockData' => true,
            ],
            [
                'bundle_code' => 'esim_UNL_30D',
                'bundle_name' => 'Unlimited Global - 30 Days',
                'bundle_marketing_name' => 'Unlimited Monthly Elite',
                'gprs_limit' => 0,
                'data_unit' => 'GB',
                'validity' => 30,
                'selling_price' => 25.00,
                'cost_price' => 18.00,
                'unlimited' => true,
                'supports_calls_sms' => true,
                'support_topup' => true,
                'description' => 'The ultimate connectivity experience.',
                'isMockData' => true,
            ],
        ];
    }

    /**
     * Process eSIM purchase.
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
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Please fill in all required fields correctly.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $isDemo = str_starts_with($request->bundle_code, 'esim_');

            if ($isDemo) {
                $demoBundles = [
                    'esim_1GB_7D' => ['name' => '1 GB Starter Plan', 'data' => '1 GB', 'validity' => 7, 'cost' => 3.50, 'price' => 5.00],
                    'esim_3GB_15D' => ['name' => '3 GB Explorer Plan', 'data' => '3 GB', 'validity' => 15, 'cost' => 7.00, 'price' => 10.00],
                    'esim_5GB_30D' => ['name' => '5 GB Voyager Plan', 'data' => '5 GB', 'validity' => 30, 'cost' => 11.00, 'price' => 15.00],
                    'esim_UNL_30D' => ['name' => 'Unlimited Monthly Elite', 'data' => 'Unlimited', 'validity' => 30, 'cost' => 18.00, 'price' => 25.00],
                ];

                $demoBundle = $demoBundles[$request->bundle_code] ?? $demoBundles['esim_5GB_30D'];
                $costPrice = $demoBundle['cost'];
                $sellingPrice = $demoBundle['price'];
                $dataAmount = $demoBundle['data'];
                $validityDays = $demoBundle['validity'];
                $bundleName = $demoBundle['name'];
            } else {
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
            }

            $esimOrder = EsimOrder::create([
                'order_reference' => '',
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

            $orderReference = 'ORDESIM' . $esimOrder->id;
            $esimOrder->update(['order_reference' => $orderReference]);

            $nomodService = new NomodService();
            $checkoutResult = $nomodService->createCheckout([
                'amount' => $sellingPrice,
                'currency' => 'AED',
                'order_id' => $orderReference,
                'description' => "eSIM: {$bundleName} - {$request->country_name}",
                'customer' => array_filter([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone ?: null,
                ]),
                'items' => [
                    [
                        'item_id' => substr($request->bundle_code, 0, 50),
                        'name' => "{$bundleName} ({$dataAmount}, {$validityDays} days)",
                        'quantity' => 1,
                        'unit_amount' => number_format($sellingPrice, 2, '.', ''),
                    ],
                ],
                'metadata' => [
                    'type' => 'esim',
                    'esim_order_id' => (string) $esimOrder->id,
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
