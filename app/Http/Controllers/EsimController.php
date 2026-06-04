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
            Log::warning('eSIM API unavailable, using static country list', ['error' => $e->getMessage()]);

            // Fallback: static list so the UI always renders
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
            ['country_name'=>'Afghanistan','iso2_code'=>'AF','iso3_code'=>'AFG'],
            ['country_name'=>'Aland Islands','iso2_code'=>'AX','iso3_code'=>'ALA'],
            ['country_name'=>'Albania','iso2_code'=>'AL','iso3_code'=>'ALB'],
            ['country_name'=>'Algeria','iso2_code'=>'DZ','iso3_code'=>'DZA'],
            ['country_name'=>'American Samoa','iso2_code'=>'AS','iso3_code'=>'ASM'],
            ['country_name'=>'Andorra','iso2_code'=>'AD','iso3_code'=>'AND'],
            ['country_name'=>'Anguilla','iso2_code'=>'AI','iso3_code'=>'AIA'],
            ['country_name'=>'Antigua and Barbuda','iso2_code'=>'AG','iso3_code'=>'ATG'],
            ['country_name'=>'Argentina','iso2_code'=>'AR','iso3_code'=>'ARG'],
            ['country_name'=>'Armenia','iso2_code'=>'AM','iso3_code'=>'ARM'],
            ['country_name'=>'Aruba','iso2_code'=>'AW','iso3_code'=>'ABW'],
            ['country_name'=>'Australia','iso2_code'=>'AU','iso3_code'=>'AUS'],
            ['country_name'=>'Austria','iso2_code'=>'AT','iso3_code'=>'AUT'],
            ['country_name'=>'Azerbaijan','iso2_code'=>'AZ','iso3_code'=>'AZE'],
            ['country_name'=>'Bahamas','iso2_code'=>'BS','iso3_code'=>'BHS'],
            ['country_name'=>'Bahrain','iso2_code'=>'BH','iso3_code'=>'BHR'],
            ['country_name'=>'Bangladesh','iso2_code'=>'BD','iso3_code'=>'BGD'],
            ['country_name'=>'Barbados','iso2_code'=>'BB','iso3_code'=>'BRB'],
            ['country_name'=>'Belarus','iso2_code'=>'BY','iso3_code'=>'BLR'],
            ['country_name'=>'Belgium','iso2_code'=>'BE','iso3_code'=>'BEL'],
            ['country_name'=>'Benin','iso2_code'=>'BJ','iso3_code'=>'BEN'],
            ['country_name'=>'Bermuda','iso2_code'=>'BM','iso3_code'=>'BMU'],
            ['country_name'=>'Bolivia','iso2_code'=>'BO','iso3_code'=>'BOL'],
            ['country_name'=>'Bosnia and Herzegovina','iso2_code'=>'BA','iso3_code'=>'BIH'],
            ['country_name'=>'Botswana','iso2_code'=>'BW','iso3_code'=>'BWA'],
            ['country_name'=>'Brazil','iso2_code'=>'BR','iso3_code'=>'BRA'],
            ['country_name'=>'Brunei Darussalam','iso2_code'=>'BN','iso3_code'=>'BRN'],
            ['country_name'=>'Bulgaria','iso2_code'=>'BG','iso3_code'=>'BGR'],
            ['country_name'=>'Burkina Faso','iso2_code'=>'BF','iso3_code'=>'BFA'],
            ['country_name'=>'Cambodia','iso2_code'=>'KH','iso3_code'=>'KHM'],
            ['country_name'=>'Cameroon','iso2_code'=>'CM','iso3_code'=>'CMR'],
            ['country_name'=>'Canada','iso2_code'=>'CA','iso3_code'=>'CAN'],
            ['country_name'=>'Cape Verde','iso2_code'=>'CV','iso3_code'=>'CPV'],
            ['country_name'=>'Cayman Islands','iso2_code'=>'KY','iso3_code'=>'CYM'],
            ['country_name'=>'Central African Republic','iso2_code'=>'CF','iso3_code'=>'CAF'],
            ['country_name'=>'Chad','iso2_code'=>'TD','iso3_code'=>'TCD'],
            ['country_name'=>'Chile','iso2_code'=>'CL','iso3_code'=>'CHL'],
            ['country_name'=>'China','iso2_code'=>'CN','iso3_code'=>'CHN'],
            ['country_name'=>'Colombia','iso2_code'=>'CO','iso3_code'=>'COL'],
            ['country_name'=>'Congo','iso2_code'=>'CG','iso3_code'=>'COG'],
            ['country_name'=>'Costa Rica','iso2_code'=>'CR','iso3_code'=>'CRI'],
            ['country_name'=>'Croatia','iso2_code'=>'HR','iso3_code'=>'HRV'],
            ['country_name'=>'Cuba','iso2_code'=>'CU','iso3_code'=>'CUB'],
            ['country_name'=>'Curaçao','iso2_code'=>'CW','iso3_code'=>'CUW'],
            ['country_name'=>'Cyprus','iso2_code'=>'CY','iso3_code'=>'CYP'],
            ['country_name'=>'Czech Republic','iso2_code'=>'CZ','iso3_code'=>'CZE'],
            ["country_name"=>"Côte d'Ivoire",'iso2_code'=>'CI','iso3_code'=>'CIV'],
            ['country_name'=>'Denmark','iso2_code'=>'DK','iso3_code'=>'DNK'],
            ['country_name'=>'Dominica','iso2_code'=>'DM','iso3_code'=>'DMA'],
            ['country_name'=>'Dominican Republic','iso2_code'=>'DO','iso3_code'=>'DOM'],
            ['country_name'=>'Ecuador','iso2_code'=>'EC','iso3_code'=>'ECU'],
            ['country_name'=>'Egypt','iso2_code'=>'EG','iso3_code'=>'EGY'],
            ['country_name'=>'El Salvador','iso2_code'=>'SV','iso3_code'=>'SLV'],
            ['country_name'=>'Estonia','iso2_code'=>'EE','iso3_code'=>'EST'],
            ['country_name'=>'Ethiopia','iso2_code'=>'ET','iso3_code'=>'ETH'],
            ['country_name'=>'Faroe Islands','iso2_code'=>'FO','iso3_code'=>'FRO'],
            ['country_name'=>'Fiji','iso2_code'=>'FJ','iso3_code'=>'FJI'],
            ['country_name'=>'Finland','iso2_code'=>'FI','iso3_code'=>'FIN'],
            ['country_name'=>'France','iso2_code'=>'FR','iso3_code'=>'FRA'],
            ['country_name'=>'French Guiana','iso2_code'=>'GF','iso3_code'=>'GUF'],
            ['country_name'=>'Gabon','iso2_code'=>'GA','iso3_code'=>'GAB'],
            ['country_name'=>'Gambia','iso2_code'=>'GM','iso3_code'=>'GMB'],
            ['country_name'=>'Georgia','iso2_code'=>'GE','iso3_code'=>'GEO'],
            ['country_name'=>'Germany','iso2_code'=>'DE','iso3_code'=>'DEU'],
            ['country_name'=>'Ghana','iso2_code'=>'GH','iso3_code'=>'GHA'],
            ['country_name'=>'Gibraltar','iso2_code'=>'GI','iso3_code'=>'GIB'],
            ['country_name'=>'Greece','iso2_code'=>'GR','iso3_code'=>'GRC'],
            ['country_name'=>'Greenland','iso2_code'=>'GL','iso3_code'=>'GRL'],
            ['country_name'=>'Grenada','iso2_code'=>'GD','iso3_code'=>'GRD'],
            ['country_name'=>'Guadeloupe','iso2_code'=>'GP','iso3_code'=>'GLP'],
            ['country_name'=>'Guam','iso2_code'=>'GU','iso3_code'=>'GUM'],
            ['country_name'=>'Guatemala','iso2_code'=>'GT','iso3_code'=>'GTM'],
            ['country_name'=>'Guernsey','iso2_code'=>'GG','iso3_code'=>'GGY'],
            ['country_name'=>'Guinea','iso2_code'=>'GN','iso3_code'=>'GIN'],
            ['country_name'=>'Guinea-Bissau','iso2_code'=>'GW','iso3_code'=>'GNB'],
            ['country_name'=>'Guyana','iso2_code'=>'GY','iso3_code'=>'GUY'],
            ['country_name'=>'Haiti','iso2_code'=>'HT','iso3_code'=>'HTI'],
            ['country_name'=>'Holy See (Vatican City State)','iso2_code'=>'VA','iso3_code'=>'VAT'],
            ['country_name'=>'Honduras','iso2_code'=>'HN','iso3_code'=>'HND'],
            ['country_name'=>'Hong Kong','iso2_code'=>'HK','iso3_code'=>'HKG'],
            ['country_name'=>'Hungary','iso2_code'=>'HU','iso3_code'=>'HUN'],
            ['country_name'=>'Iceland','iso2_code'=>'IS','iso3_code'=>'ISL'],
            ['country_name'=>'India','iso2_code'=>'IN','iso3_code'=>'IND'],
            ['country_name'=>'Indonesia','iso2_code'=>'ID','iso3_code'=>'IDN'],
            ['country_name'=>'Iraq','iso2_code'=>'IQ','iso3_code'=>'IRQ'],
            ['country_name'=>'Ireland','iso2_code'=>'IE','iso3_code'=>'IRL'],
            ['country_name'=>'Isle of Man','iso2_code'=>'IM','iso3_code'=>'IMN'],
            ['country_name'=>'Israel','iso2_code'=>'IL','iso3_code'=>'ISR'],
            ['country_name'=>'Italy','iso2_code'=>'IT','iso3_code'=>'ITA'],
            ['country_name'=>'Jamaica','iso2_code'=>'JM','iso3_code'=>'JAM'],
            ['country_name'=>'Japan','iso2_code'=>'JP','iso3_code'=>'JPN'],
            ['country_name'=>'Jersey','iso2_code'=>'JE','iso3_code'=>'JEY'],
            ['country_name'=>'Jordan','iso2_code'=>'JO','iso3_code'=>'JOR'],
            ['country_name'=>'Kazakhstan','iso2_code'=>'KZ','iso3_code'=>'KAZ'],
            ['country_name'=>'Kenya','iso2_code'=>'KE','iso3_code'=>'KEN'],
            ['country_name'=>'Kosovo','iso2_code'=>'XK','iso3_code'=>'XKX'],
            ['country_name'=>'Kuwait','iso2_code'=>'KW','iso3_code'=>'KWT'],
            ['country_name'=>'Kyrgyzstan','iso2_code'=>'KG','iso3_code'=>'KGZ'],
            ['country_name'=>'Laos','iso2_code'=>'LA','iso3_code'=>'LAO'],
            ['country_name'=>'Latvia','iso2_code'=>'LV','iso3_code'=>'LVA'],
            ['country_name'=>'Lebanon','iso2_code'=>'LB','iso3_code'=>'LBN'],
            ['country_name'=>'Liechtenstein','iso2_code'=>'LI','iso3_code'=>'LIE'],
            ['country_name'=>'Lithuania','iso2_code'=>'LT','iso3_code'=>'LTU'],
            ['country_name'=>'Luxembourg','iso2_code'=>'LU','iso3_code'=>'LUX'],
            ['country_name'=>'Macau','iso2_code'=>'MO','iso3_code'=>'MAC'],
            ['country_name'=>'Madagascar','iso2_code'=>'MG','iso3_code'=>'MDG'],
            ['country_name'=>'Malawi','iso2_code'=>'MW','iso3_code'=>'MWI'],
            ['country_name'=>'Malaysia','iso2_code'=>'MY','iso3_code'=>'MYS'],
            ['country_name'=>'Maldives','iso2_code'=>'MV','iso3_code'=>'MDV'],
            ['country_name'=>'Mali','iso2_code'=>'ML','iso3_code'=>'MLI'],
            ['country_name'=>'Malta','iso2_code'=>'MT','iso3_code'=>'MLT'],
            ['country_name'=>'Martinique','iso2_code'=>'MQ','iso3_code'=>'MTQ'],
            ['country_name'=>'Mauritius','iso2_code'=>'MU','iso3_code'=>'MUS'],
            ['country_name'=>'Mexico','iso2_code'=>'MX','iso3_code'=>'MEX'],
            ['country_name'=>'Moldova','iso2_code'=>'MD','iso3_code'=>'MDA'],
            ['country_name'=>'Mongolia','iso2_code'=>'MN','iso3_code'=>'MNG'],
            ['country_name'=>'Montenegro','iso2_code'=>'ME','iso3_code'=>'MNE'],
            ['country_name'=>'Morocco','iso2_code'=>'MA','iso3_code'=>'MAR'],
            ['country_name'=>'Mozambique','iso2_code'=>'MZ','iso3_code'=>'MOZ'],
            ['country_name'=>'Myanmar','iso2_code'=>'MM','iso3_code'=>'MMR'],
            ['country_name'=>'Nepal','iso2_code'=>'NP','iso3_code'=>'NPL'],
            ['country_name'=>'Netherlands','iso2_code'=>'NL','iso3_code'=>'NLD'],
            ['country_name'=>'New Zealand','iso2_code'=>'NZ','iso3_code'=>'NZL'],
            ['country_name'=>'Nicaragua','iso2_code'=>'NI','iso3_code'=>'NIC'],
            ['country_name'=>'Niger','iso2_code'=>'NE','iso3_code'=>'NER'],
            ['country_name'=>'Nigeria','iso2_code'=>'NG','iso3_code'=>'NGA'],
            ['country_name'=>'North Macedonia','iso2_code'=>'MK','iso3_code'=>'MKD'],
            ['country_name'=>'Norway','iso2_code'=>'NO','iso3_code'=>'NOR'],
            ['country_name'=>'Oman','iso2_code'=>'OM','iso3_code'=>'OMN'],
            ['country_name'=>'Pakistan','iso2_code'=>'PK','iso3_code'=>'PAK'],
            ['country_name'=>'Palestine','iso2_code'=>'PS','iso3_code'=>'PSE'],
            ['country_name'=>'Panama','iso2_code'=>'PA','iso3_code'=>'PAN'],
            ['country_name'=>'Papua New Guinea','iso2_code'=>'PG','iso3_code'=>'PNG'],
            ['country_name'=>'Paraguay','iso2_code'=>'PY','iso3_code'=>'PRY'],
            ['country_name'=>'Peru','iso2_code'=>'PE','iso3_code'=>'PER'],
            ['country_name'=>'Philippines','iso2_code'=>'PH','iso3_code'=>'PHL'],
            ['country_name'=>'Poland','iso2_code'=>'PL','iso3_code'=>'POL'],
            ['country_name'=>'Portugal','iso2_code'=>'PT','iso3_code'=>'PRT'],
            ['country_name'=>'Puerto Rico','iso2_code'=>'PR','iso3_code'=>'PRI'],
            ['country_name'=>'Qatar','iso2_code'=>'QA','iso3_code'=>'QAT'],
            ['country_name'=>'Reunion','iso2_code'=>'RE','iso3_code'=>'REU'],
            ['country_name'=>'Romania','iso2_code'=>'RO','iso3_code'=>'ROU'],
            ['country_name'=>'Russia','iso2_code'=>'RU','iso3_code'=>'RUS'],
            ['country_name'=>'Rwanda','iso2_code'=>'RW','iso3_code'=>'RWA'],
            ['country_name'=>'Saint Kitts and Nevis','iso2_code'=>'KN','iso3_code'=>'KNA'],
            ['country_name'=>'Saint Lucia','iso2_code'=>'LC','iso3_code'=>'LCA'],
            ['country_name'=>'Saint Vincent and the Grenadines','iso2_code'=>'VC','iso3_code'=>'VCT'],
            ['country_name'=>'Saudi Arabia','iso2_code'=>'SA','iso3_code'=>'SAU'],
            ['country_name'=>'Senegal','iso2_code'=>'SN','iso3_code'=>'SEN'],
            ['country_name'=>'Serbia','iso2_code'=>'RS','iso3_code'=>'SRB'],
            ['country_name'=>'Seychelles','iso2_code'=>'SC','iso3_code'=>'SYC'],
            ['country_name'=>'Sierra Leone','iso2_code'=>'SL','iso3_code'=>'SLE'],
            ['country_name'=>'Singapore','iso2_code'=>'SG','iso3_code'=>'SGP'],
            ['country_name'=>'Slovakia','iso2_code'=>'SK','iso3_code'=>'SVK'],
            ['country_name'=>'Slovenia','iso2_code'=>'SI','iso3_code'=>'SVN'],
            ['country_name'=>'South Africa','iso2_code'=>'ZA','iso3_code'=>'ZAF'],
            ['country_name'=>'South Korea','iso2_code'=>'KR','iso3_code'=>'KOR'],
            ['country_name'=>'Spain','iso2_code'=>'ES','iso3_code'=>'ESP'],
            ['country_name'=>'Sri Lanka','iso2_code'=>'LK','iso3_code'=>'LKA'],
            ['country_name'=>'Suriname','iso2_code'=>'SR','iso3_code'=>'SUR'],
            ['country_name'=>'Sweden','iso2_code'=>'SE','iso3_code'=>'SWE'],
            ['country_name'=>'Switzerland','iso2_code'=>'CH','iso3_code'=>'CHE'],
            ['country_name'=>'Taiwan','iso2_code'=>'TW','iso3_code'=>'TWN'],
            ['country_name'=>'Tajikistan','iso2_code'=>'TJ','iso3_code'=>'TJK'],
            ['country_name'=>'Tanzania','iso2_code'=>'TZ','iso3_code'=>'TZA'],
            ['country_name'=>'Thailand','iso2_code'=>'TH','iso3_code'=>'THA'],
            ['country_name'=>'Togo','iso2_code'=>'TG','iso3_code'=>'TGO'],
            ['country_name'=>'Trinidad and Tobago','iso2_code'=>'TT','iso3_code'=>'TTO'],
            ['country_name'=>'Tunisia','iso2_code'=>'TN','iso3_code'=>'TUN'],
            ['country_name'=>'Turkey','iso2_code'=>'TR','iso3_code'=>'TUR'],
            ['country_name'=>'Turkmenistan','iso2_code'=>'TM','iso3_code'=>'TKM'],
            ['country_name'=>'Turks and Caicos Islands','iso2_code'=>'TC','iso3_code'=>'TCA'],
            ['country_name'=>'Uganda','iso2_code'=>'UG','iso3_code'=>'UGA'],
            ['country_name'=>'Ukraine','iso2_code'=>'UA','iso3_code'=>'UKR'],
            ['country_name'=>'United Arab Emirates','iso2_code'=>'AE','iso3_code'=>'ARE'],
            ['country_name'=>'United Kingdom','iso2_code'=>'GB','iso3_code'=>'GBR'],
            ['country_name'=>'United States','iso2_code'=>'US','iso3_code'=>'USA'],
            ['country_name'=>'Uruguay','iso2_code'=>'UY','iso3_code'=>'URY'],
            ['country_name'=>'Uzbekistan','iso2_code'=>'UZ','iso3_code'=>'UZB'],
            ['country_name'=>'Venezuela','iso2_code'=>'VE','iso3_code'=>'VEN'],
            ['country_name'=>'Vietnam','iso2_code'=>'VN','iso3_code'=>'VNM'],
            ['country_name'=>'Virgin Islands (British)','iso2_code'=>'VG','iso3_code'=>'VGB'],
            ['country_name'=>'Virgin Islands (U.S.)','iso2_code'=>'VI','iso3_code'=>'VIR'],
            ['country_name'=>'Zambia','iso2_code'=>'ZM','iso3_code'=>'ZMB'],
            ['country_name'=>'Zimbabwe','iso2_code'=>'ZW','iso3_code'=>'ZWE'],
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
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Please fill in all required fields correctly.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Check if this is a demo bundle (for testing)
            $isDemo = str_starts_with($request->bundle_code, 'esim_');

            if ($isDemo) {
                // Use demo bundle data
                $demoBundles = [
                    'esim_1GB_7D' => ['name' => '1 GB Data Plan', 'data' => '1 GB', 'validity' => 7, 'cost' => 12.00, 'price' => 16.50],
                    'esim_3GB_15D' => ['name' => '3 GB Data Plan', 'data' => '3 GB', 'validity' => 15, 'cost' => 25.00, 'price' => 33.00],
                    'esim_5GB_30D' => ['name' => '5 GB Data Plan', 'data' => '5 GB', 'validity' => 30, 'cost' => 40.00, 'price' => 51.00],
                    'esim_10GB_30D' => ['name' => '10 GB Data Plan', 'data' => '10 GB', 'validity' => 30, 'cost' => 65.00, 'price' => 81.00],
                    'esim_UNL_7D' => ['name' => 'Unlimited Data', 'data' => 'Unlimited', 'validity' => 7, 'cost' => 45.00, 'price' => 55.00],
                    'esim_UNL_30D' => ['name' => 'Unlimited Data', 'data' => 'Unlimited', 'validity' => 30, 'cost' => 120.00, 'price' => 150.00],
                ];

                $demoBundle = $demoBundles[$request->bundle_code] ?? $demoBundles['esim_5GB_30D'];
                $costPrice = $demoBundle['cost'];
                $sellingPrice = $demoBundle['price'];
                $dataAmount = $demoBundle['data'];
                $validityDays = $demoBundle['validity'];
                $bundleName = $demoBundle['name'];
            } else {
                // Re-fetch bundle from API for server-side price verification
                $montyService = new MontyEsimService($this->tenantMarkup());
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
