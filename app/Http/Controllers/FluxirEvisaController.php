<?php

namespace App\Http\Controllers;

use App\Models\EvisaSetting;
use App\Models\FluxirVisaApplication;
use App\Services\FluxirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Multi-country e-Visa storefront on top of Fluxir.
 *
 *   GET  /e-visa              form() — country/visa-type picker + dynamic form
 *   GET  /e-visa/types        types() — AJAX: a country's visa options + prices
 *   POST /e-visa/scheme       scheme() — AJAX: resolved fee + dynamic document scheme
 *   POST /e-visa/apply        apply() — run the Fluxir flow with dynamic items/files
 *
 * Customer price = Fluxir net fee + EvisaSetting markup. The submit flow is
 * scheme-driven: every File item in the chosen visa's document scheme is
 * uploaded, every other item is PATCHed — so it adapts to any country.
 */
class FluxirEvisaController extends Controller
{
    private static array $codeMap = [
        'AE' => 'ARE', 'IN' => 'IND', 'GB' => 'GBR', 'US' => 'USA',
        'AF' => 'AFG', 'AL' => 'ALB', 'DZ' => 'DZA', 'AD' => 'AND',
        'AO' => 'AGO', 'AR' => 'ARG', 'AM' => 'ARM', 'AU' => 'AUS',
        'AT' => 'AUT', 'AZ' => 'AZE', 'BH' => 'BHR', 'BD' => 'BGD',
        'BY' => 'BLR', 'BE' => 'BEL', 'BZ' => 'BLZ', 'BJ' => 'BEN',
        'BT' => 'BTN', 'BO' => 'BOL', 'BA' => 'BIH', 'BW' => 'BWA',
        'BR' => 'BRA', 'BN' => 'BRN', 'BG' => 'BGR', 'BF' => 'BFA',
        'BI' => 'BDI', 'KH' => 'KHM', 'CM' => 'CMR', 'CA' => 'CAN',
        'CV' => 'CPV', 'CF' => 'CAF', 'TD' => 'TCD', 'CL' => 'CHL',
        'CN' => 'CHN', 'CO' => 'COL', 'KM' => 'COM', 'CG' => 'COG',
        'CD' => 'COD', 'CR' => 'CRI', 'CI' => 'CIV', 'HR' => 'HRV',
        'CU' => 'CUB', 'CY' => 'CYP', 'CZ' => 'CZE', 'DK' => 'DNK',
        'DJ' => 'DJI', 'DO' => 'DOM', 'EC' => 'ECU', 'EG' => 'EGY',
        'SV' => 'SLV', 'GQ' => 'GNQ', 'ER' => 'ERI', 'EE' => 'EST',
        'SZ' => 'SWZ', 'ET' => 'ETH', 'FJ' => 'FJI', 'FI' => 'FIN',
        'FR' => 'FRA', 'GA' => 'GAB', 'GM' => 'GMB', 'GE' => 'GEO',
        'DE' => 'DEU', 'GH' => 'GHA', 'GR' => 'GRC', 'GT' => 'GTM',
        'GN' => 'GIN', 'GY' => 'GUY', 'HT' => 'HTI', 'HN' => 'HND',
        'HK' => 'HKG', 'HU' => 'HUN', 'IS' => 'ISL', 'ID' => 'IDN',
        'IR' => 'IRN', 'IQ' => 'IRQ', 'IE' => 'IRL', 'IL' => 'ISR',
        'IT' => 'ITA', 'JM' => 'JAM', 'JP' => 'JPN', 'JO' => 'JOR',
        'KZ' => 'KAZ', 'KE' => 'KEN', 'KW' => 'KWT', 'KG' => 'KGZ',
        'LA' => 'LAO', 'LV' => 'LVA', 'LB' => 'LBN', 'LS' => 'LSO',
        'LR' => 'LBR', 'LY' => 'LBY', 'LI' => 'LIE', 'LT' => 'LTU',
        'LU' => 'LUX', 'MO' => 'MAC', 'MG' => 'MDG', 'MW' => 'MWI',
        'MY' => 'MYS', 'MV' => 'MDV', 'ML' => 'MLI', 'MT' => 'MLT',
        'MR' => 'MRT', 'MU' => 'MUS', 'MX' => 'MEX', 'MD' => 'MDA',
        'MC' => 'MCO', 'MN' => 'MNG', 'ME' => 'MNE', 'MA' => 'MAR',
        'MZ' => 'MOZ', 'MM' => 'MMR', 'NA' => 'NAM', 'NP' => 'NPL',
        'NL' => 'NLD', 'NZ' => 'NZL', 'NI' => 'NIC', 'NE' => 'NER',
        'NG' => 'NGA', 'KP' => 'PRK', 'MK' => 'MKD', 'NO' => 'NOR',
        'OM' => 'OMN', 'PK' => 'PAK', 'PS' => 'PSE', 'PA' => 'PAN',
        'PG' => 'PNG', 'PY' => 'PRY', 'PE' => 'PER', 'PH' => 'PHL',
        'PL' => 'POL', 'PT' => 'PRT', 'PR' => 'PRI', 'QA' => 'QAT',
        'RO' => 'ROU', 'RU' => 'RUS', 'RW' => 'RWA', 'SM' => 'SMR',
        'SA' => 'SAU', 'SN' => 'SEN', 'RS' => 'SRB', 'SC' => 'SYC',
        'SL' => 'SLE', 'SG' => 'SGP', 'SK' => 'SVK', 'SI' => 'SVN',
        'SO' => 'SOM', 'ZA' => 'ZAF', 'KR' => 'KOR', 'SS' => 'SSD',
        'ES' => 'ESP', 'LK' => 'LKA', 'SD' => 'SDN', 'SR' => 'SUR',
        'SE' => 'SWE', 'CH' => 'CHE', 'SY' => 'SYR', 'TW' => 'TWN',
        'TJ' => 'TJK', 'TZ' => 'TZA', 'TH' => 'THA', 'TG' => 'TGO',
        'TT' => 'TTO', 'TN' => 'TUN', 'TR' => 'TUR', 'TM' => 'TKM',
        'UG' => 'UGA', 'UA' => 'UKR', 'UY' => 'URY', 'UZ' => 'UZB',
        'VE' => 'VEN', 'VN' => 'VNM', 'YE' => 'YEM', 'ZM' => 'ZMB',
        'ZW' => 'ZWE'
    ];

    public function __construct(protected FluxirService $fluxir)
    {
    }

    /** The picker page. */
    public function form()
    {
        $catalog = $this->fluxir->isConfigured() ? $this->fluxir->getOnlineVisaCatalog() : [];
        $markup  = EvisaSetting::markupPercent();

        $iso3to2 = [];
        foreach (self::$codeMap as $iso2 => $iso3) {
            $iso3to2[$iso3] = strtolower($iso2);
        }

        // Shape a lightweight country list for the picker (code, name, flag, #types).
        $countries = [];
        foreach ($catalog as $code => $c) {
            $countries[] = [
                'code'   => $code,
                'name'   => $c['name'],
                'iso2'   => strtolower($c['alpha2'] ?? ''),
                'flag'   => $this->flagEmoji($c['alpha2'] ?? ''),
                'types'  => count($c['types']),
            ];
        }

        // All countries for the nationality picker.
        $nationalities = $this->fluxir->isConfigured() ? $this->fluxir->getCountryOptions() : [];
        foreach ($nationalities as &$n) {
            $n['iso2'] = $iso3to2[$n['code']] ?? '';
        }
        unset($n);

        return view('visa.evisa', compact('countries', 'nationalities', 'markup'));
    }

    /**
     * AJAX: visa-type options for a destination + the traveller's nationality.
     *
     * Prices come from the live service-intents (authoritative, same source the
     * application uses) so the listed price always matches the resolved price,
     * and only visa types actually AVAILABLE for that nationality are returned.
     * Friendly name/validity/stay are merged in from the cached catalog.
     */
    public function types(Request $request)
    {
        $code = strtoupper((string) $request->query('country'));
        $nat  = strtoupper((string) $request->query('nationality'));
        $codeMap = self::$codeMap;

        if (strlen($code) === 2) {
            $code = $codeMap[$code] ?? $code;
        }
        if (strlen($nat) === 2) {
            $nat = $codeMap[$nat] ?? $nat;
        }

        if (strlen($code) !== 3) {
            return response()->json(['success' => false, 'types' => []]);
        }
        if (strlen($nat) !== 3) {
            return response()->json(['success' => false, 'needs_nationality' => true, 'types' => []]);
        }

        $catalog = $this->fluxir->isConfigured() ? $this->fluxir->getOnlineVisaCatalog() : [];
        $country = $catalog[$code] ?? null;
        $metaById = [];
        foreach (($country['types'] ?? []) as $t) {
            $metaById[$t['id']] = $t;
        }

        $items = [];
        if ($this->fluxir->isConfigured()) {
            $trip = [
                'originationCode' => $nat,
                'destinationCode' => $code,
                'from'            => $request->query('arrival_date')   ?: now()->addDays(30)->format('Y-m-d'),
                'to'              => $request->query('departure_date') ?: now()->addDays(40)->format('Y-m-d'),
            ];
            $intents = $this->fluxir->getServiceIntents($trip, ['Visa']);
            $items = ($intents['success'] ?? false) ? ($intents['data']['items'] ?? []) : [];
        }

        if (empty($items) && (config('app.env') === 'local' || config('app.env') === 'testing')) {
            $types = [
                [
                    'id'         => 1,
                    'name'       => 'United Arab Emirates Tourist eVisa',
                    'category'   => 'Tourist',
                    'validity'   => '60 Days',
                    'stay'       => '30 Days',
                    'entry'      => 'Single Entry',
                    'processing' => '3-5 Days',
                    'price'      => 111.00,
                ],
                [
                    'id'         => 2,
                    'name'       => 'United Arab Emirates Multiple Entry eVisa',
                    'category'   => 'Multiple Entry Tourist',
                    'validity'   => '90 Days',
                    'stay'       => '60 Days',
                    'entry'      => 'Multiple Entry',
                    'processing' => '1-3 Days',
                    'price'      => 288.00,
                ],
                [
                    'id'         => 3,
                    'name'       => 'United Arab Emirates Express Tourist eVisa',
                    'category'   => 'Express Tourist',
                    'validity'   => '30 Days',
                    'stay'       => '30 Days',
                    'entry'      => 'Single Entry',
                    'processing' => '24 Hours',
                    'price'      => 207.00,
                ],
            ];
            return response()->json([
                'success'  => true,
                'country'  => ['code' => $code, 'name' => 'United Arab Emirates'],
                'currency' => 'USD',
                'types'    => $types,
            ]);
        }

        $types = [];
        foreach ($items as $i) {
            if (($i['serviceType'] ?? null) !== 'Visa') {
                continue;
            }
            $key = $i['serviceIntentKey'] ?? '';
            if (!preg_match('/-(\d+)$/', $key, $m)) {
                continue;
            }
            $typeId = (int) $m[1];
            $fee    = $i['fee'] ?? null;
            if (!is_numeric($fee) || (float) $fee <= 0) {
                continue;
            }
            $meta = $metaById[$typeId] ?? [];
            $types[] = [
                'id'         => $typeId,
                'name'       => $meta['name'] ?? ($country['name'] ?? $code) . ' eVisa',
                'category'   => $meta['category'] ?? null,
                'validity'   => $this->duration($meta['validity'] ?? null, $meta['validity_unit'] ?? null),
                'stay'       => $this->duration($meta['stay'] ?? null, $meta['stay_unit'] ?? null),
                'entry'      => $meta['entry'] ?? null,
                'processing' => $meta['processing'] ?? null,
                'price'      => EvisaSetting::customerPrice((float) $fee),
            ];
        }
        // Cheapest first.
        usort($types, fn ($a, $b) => $a['price'] <=> $b['price']);

        return response()->json([
            'success'  => true,
            'country'  => ['code' => $code, 'name' => $country['name'] ?? $code],
            'currency' => 'USD',
            'types'    => $types,
        ]);
    }

    /** AJAX: authoritative fee + dynamic document scheme for a chosen visa type. */
    public function scheme(Request $request)
    {
        $data = $request->validate([
            'destination_code' => 'required|string|min:2|max:3',
            'visa_type_id'     => 'required|integer',
            'nationality'      => 'nullable|string|min:2|max:3',
            'arrival_date'     => 'nullable|date',
            'departure_date'   => 'nullable|date',
        ]);

        $codeMap = [
            'AE' => 'ARE', 'IN' => 'IND', 'GB' => 'GBR', 'US' => 'USA',
            'AF' => 'AFG', 'AL' => 'ALB', 'DZ' => 'DZA', 'AD' => 'AND',
            'AO' => 'AGO', 'AR' => 'ARG', 'AM' => 'ARM', 'AU' => 'AUS',
            'AT' => 'AUT', 'AZ' => 'AZE', 'BH' => 'BHR', 'BD' => 'BGD',
            'BY' => 'BLR', 'BE' => 'BEL', 'BZ' => 'BLZ', 'BJ' => 'BEN',
            'BT' => 'BTN', 'BO' => 'BOL', 'BA' => 'BIH', 'BW' => 'BWA',
            'BR' => 'BRA', 'BN' => 'BRN', 'BG' => 'BGR', 'BF' => 'BFA',
            'BI' => 'BDI', 'KH' => 'KHM', 'CM' => 'CMR', 'CA' => 'CAN',
            'CV' => 'CPV', 'CF' => 'CAF', 'TD' => 'TCD', 'CL' => 'CHL',
            'CN' => 'CHN', 'CO' => 'COL', 'KM' => 'COM', 'CG' => 'COG',
            'CD' => 'COD', 'CR' => 'CRI', 'CI' => 'CIV', 'HR' => 'HRV',
            'CU' => 'CUB', 'CY' => 'CYP', 'CZ' => 'CZE', 'DK' => 'DNK',
            'DJ' => 'DJI', 'DO' => 'DOM', 'EC' => 'ECU', 'EG' => 'EGY',
            'SV' => 'SLV', 'GQ' => 'GNQ', 'ER' => 'ERI', 'EE' => 'EST',
            'SZ' => 'SWZ', 'ET' => 'ETH', 'FJ' => 'FJI', 'FI' => 'FIN',
            'FR' => 'FRA', 'GA' => 'GAB', 'GM' => 'GMB', 'GE' => 'GEO',
            'DE' => 'DEU', 'GH' => 'GHA', 'GR' => 'GRC', 'GT' => 'GTM',
            'GN' => 'GIN', 'GY' => 'GUY', 'HT' => 'HTI', 'HN' => 'HND',
            'HK' => 'HKG', 'HU' => 'HUN', 'IS' => 'ISL', 'ID' => 'IDN',
            'IR' => 'IRN', 'IQ' => 'IRQ', 'IE' => 'IRL', 'IL' => 'ISR',
            'IT' => 'ITA', 'JM' => 'JAM', 'JP' => 'JPN', 'JO' => 'JOR',
            'KZ' => 'KAZ', 'KE' => 'KEN', 'KW' => 'KWT', 'KG' => 'KGZ',
            'LA' => 'LAO', 'LV' => 'LVA', 'LB' => 'LBN', 'LS' => 'LSO',
            'LR' => 'LBR', 'LY' => 'LBY', 'LI' => 'LIE', 'LT' => 'LTU',
            'LU' => 'LUX', 'MO' => 'MAC', 'MG' => 'MDG', 'MW' => 'MWI',
            'MY' => 'MYS', 'MV' => 'MDV', 'ML' => 'MLI', 'MT' => 'MLT',
            'MR' => 'MRT', 'MU' => 'MUS', 'MX' => 'MEX', 'MD' => 'MDA',
            'MC' => 'MCO', 'MN' => 'MNG', 'ME' => 'MNE', 'MA' => 'MAR',
            'MZ' => 'MOZ', 'MM' => 'MMR', 'NA' => 'NAM', 'NP' => 'NPL',
            'NL' => 'NLD', 'NZ' => 'NZL', 'NI' => 'NIC', 'NE' => 'NER',
            'NG' => 'NGA', 'KP' => 'PRK', 'MK' => 'MKD', 'NO' => 'NOR',
            'OM' => 'OMN', 'PK' => 'PAK', 'PS' => 'PSE', 'PA' => 'PAN',
            'PG' => 'PNG', 'PY' => 'PRY', 'PE' => 'PER', 'PH' => 'PHL',
            'PL' => 'POL', 'PT' => 'PRT', 'PR' => 'PRI', 'QA' => 'QAT',
            'RO' => 'ROU', 'RU' => 'RUS', 'RW' => 'RWA', 'SM' => 'SMR',
            'SA' => 'SAU', 'SN' => 'SEN', 'RS' => 'SRB', 'SC' => 'SYC',
            'SL' => 'SLE', 'SG' => 'SGP', 'SK' => 'SVK', 'SI' => 'SVN',
            'SO' => 'SOM', 'ZA' => 'ZAF', 'KR' => 'KOR', 'SS' => 'SSD',
            'ES' => 'ESP', 'LK' => 'LKA', 'SD' => 'SDN', 'SR' => 'SUR',
            'SE' => 'SWE', 'CH' => 'CHE', 'SY' => 'SYR', 'TW' => 'TWN',
            'TJ' => 'TJK', 'TZ' => 'TZA', 'TH' => 'THA', 'TG' => 'TGO',
            'TT' => 'TTO', 'TN' => 'TUN', 'TR' => 'TUR', 'TM' => 'TKM',
            'UG' => 'UGA', 'UA' => 'UKR', 'UY' => 'URY', 'UZ' => 'UZB',
            'VE' => 'VEN', 'VN' => 'VNM', 'YE' => 'YEM', 'ZM' => 'ZMB',
            'ZW' => 'ZWE'
        ];

        $destCode = strtoupper($data['destination_code']);
        if (strlen($destCode) === 2) {
            $destCode = $codeMap[$destCode] ?? $destCode;
        }

        $natCode = strtoupper($data['nationality'] ?? '');
        if (strlen($natCode) === 2) {
            $natCode = $codeMap[$natCode] ?? $natCode;
        }

        $trip = [
            'originationCode' => $natCode ?: null,
            'destinationCode' => $destCode,
            'from'            => $data['arrival_date']   ?? now()->addDays(30)->format('Y-m-d'),
            'to'              => $data['departure_date'] ?? now()->addDays(40)->format('Y-m-d'),
        ];

        $intent = $this->fluxir->isConfigured() ? $this->fluxir->resolveServiceIntentForType($trip, (int) $data['visa_type_id']) : null;
        if (!$intent) {
            if (config('app.env') === 'local' || config('app.env') === 'testing') {
                return response()->json([
                    'success'        => true,
                    'price'          => $data['visa_type_id'] == 1 ? 111.00 : ($data['visa_type_id'] == 2 ? 288.00 : 207.00),
                    'currency'       => 'USD',
                    'version_id'     => 1218,
                    'intent_key'     => 'mock-intent',
                    'sections'       => [
                        [
                            'title' => 'Required Documents & Details',
                            'fields' => [
                                [
                                    'name_id' => 'passport_copy',
                                    'label' => 'Passport Copy (Info Page)',
                                    'kind' => 'file',
                                    'is_file' => true,
                                    'required' => true,
                                ],
                                [
                                    'name_id' => 'photograph',
                                    'label' => 'Passport Photo',
                                    'kind' => 'file',
                                    'is_file' => true,
                                    'required' => true,
                                ],
                            ],
                        ],
                    ],
                ]);
            }
            return response()->json(['success' => false, 'message' => 'This visa is not available for the selected nationality.'], 422);
        }

        $netFee    = (float) ($intent['fee'] ?? 0);
        $versionId = $intent['documentSchemeVersionId'] ?? null;

        $sections = [];
        if ($versionId) {
            $schemeRes = $this->fluxir->getDocumentsScheme($versionId);
            if ($schemeRes['success'] ?? false) {
                $sections = $this->fluxir->normalizeScheme($schemeRes['data'] ?? []);
            }
        }

        return response()->json([
            'success'        => true,
            'price'          => EvisaSetting::customerPrice($netFee),
            'currency'       => 'USD',
            'version_id'     => $versionId,
            'intent_key'     => $intent['serviceIntentKey'] ?? null,
            'sections'       => $sections,
        ]);
    }

    public function apply(Request $request)
    {
        if (!$this->fluxir->isConfigured()) {
            if (config('app.env') === 'local' || config('app.env') === 'testing') {
                $orderId = 'ORDVISA-MOCK' . strtoupper(uniqid());
                return response()->json([
                    'success'   => true,
                    'order_id'  => $orderId,
                    'on_credit' => true,
                    'redirect'  => route('visa.fluxir.success', ['order_id' => 'mock-order']),
                ]);
            }
            return response()->json(['success' => false, 'message' => 'Visa service is not configured yet. Please contact support.'], 503);
        }

        $data = $request->validate([
            'first_name'       => 'required|string|max:100',
            'last_name'        => 'required|string|max:100',
            'email'            => 'required|email',
            'phone'            => 'nullable|string|max:30',
            'nationality'      => 'required|string|min:2|max:3',
            'destination_code' => 'required|string|min:2|max:3',
            'visa_type_id'     => 'required|integer',
            'arrival_date'     => 'required|date',
            'departure_date'   => 'required|date|after_or_equal:arrival_date',
            'items'            => 'array',        // nameId => scalar value
            'files'            => 'array',        // nameId => UploadedFile
            'files.*'          => 'file|mimes:pdf,jpg,jpeg,png|max:8192',
        ]);

        $codeMap = [
            'AE' => 'ARE', 'IN' => 'IND', 'GB' => 'GBR', 'US' => 'USA',
            'AF' => 'AFG', 'AL' => 'ALB', 'DZ' => 'DZA', 'AD' => 'AND',
            'AO' => 'AGO', 'AR' => 'ARG', 'AM' => 'ARM', 'AU' => 'AUS',
            'AT' => 'AUT', 'AZ' => 'AZE', 'BH' => 'BHR', 'BD' => 'BGD',
            'BY' => 'BLR', 'BE' => 'BEL', 'BZ' => 'BLZ', 'BJ' => 'BEN',
            'BT' => 'BTN', 'BO' => 'BOL', 'BA' => 'BIH', 'BW' => 'BWA',
            'BR' => 'BRA', 'BN' => 'BRN', 'BG' => 'BGR', 'BF' => 'BFA',
            'BI' => 'BDI', 'KH' => 'KHM', 'CM' => 'CMR', 'CA' => 'CAN',
            'CV' => 'CPV', 'CF' => 'CAF', 'TD' => 'TCD', 'CL' => 'CHL',
            'CN' => 'CHN', 'CO' => 'COL', 'KM' => 'COM', 'CG' => 'COG',
            'CD' => 'COD', 'CR' => 'CRI', 'CI' => 'CIV', 'HR' => 'HRV',
            'CU' => 'CUB', 'CY' => 'CYP', 'CZ' => 'CZE', 'DK' => 'DNK',
            'DJ' => 'DJI', 'DO' => 'DOM', 'EC' => 'ECU', 'EG' => 'EGY',
            'SV' => 'SLV', 'GQ' => 'GNQ', 'ER' => 'ERI', 'EE' => 'EST',
            'SZ' => 'SWZ', 'ET' => 'ETH', 'FJ' => 'FJI', 'FI' => 'FIN',
            'FR' => 'FRA', 'GA' => 'GAB', 'GM' => 'GMB', 'GE' => 'GEO',
            'DE' => 'DEU', 'GH' => 'GHA', 'GR' => 'GRC', 'GT' => 'GTM',
            'GN' => 'GIN', 'GY' => 'GUY', 'HT' => 'HTI', 'HN' => 'HND',
            'HK' => 'HKG', 'HU' => 'HUN', 'IS' => 'ISL', 'ID' => 'IDN',
            'IR' => 'IRN', 'IQ' => 'IRQ', 'IE' => 'IRL', 'IL' => 'ISR',
            'IT' => 'ITA', 'JM' => 'JAM', 'JP' => 'JPN', 'JO' => 'JOR',
            'KZ' => 'KAZ', 'KE' => 'KEN', 'KW' => 'KWT', 'KG' => 'KGZ',
            'LA' => 'LAO', 'LV' => 'LVA', 'LB' => 'LBN', 'LS' => 'LSO',
            'LR' => 'LBR', 'LY' => 'LBY', 'LI' => 'LIE', 'LT' => 'LTU',
            'LU' => 'LUX', 'MO' => 'MAC', 'MG' => 'MDG', 'MW' => 'MWI',
            'MY' => 'MYS', 'MV' => 'MDV', 'ML' => 'MLI', 'MT' => 'MLT',
            'MR' => 'MRT', 'MU' => 'MUS', 'MX' => 'MEX', 'MD' => 'MDA',
            'MC' => 'MCO', 'MN' => 'MNG', 'ME' => 'MNE', 'MA' => 'MAR',
            'MZ' => 'MOZ', 'MM' => 'MMR', 'NA' => 'NAM', 'NP' => 'NPL',
            'NL' => 'NLD', 'NZ' => 'NZL', 'NI' => 'NIC', 'NE' => 'NER',
            'NG' => 'NGA', 'KP' => 'PRK', 'MK' => 'MKD', 'NO' => 'NOR',
            'OM' => 'OMN', 'PK' => 'PAK', 'PS' => 'PSE', 'PA' => 'PAN',
            'PG' => 'PNG', 'PY' => 'PRY', 'PE' => 'PER', 'PH' => 'PHL',
            'PL' => 'POL', 'PT' => 'PRT', 'PR' => 'PRI', 'QA' => 'QAT',
            'RO' => 'ROU', 'RU' => 'RUS', 'RW' => 'RWA', 'SM' => 'SMR',
            'SA' => 'SAU', 'SN' => 'SEN', 'RS' => 'SRB', 'SC' => 'SYC',
            'SL' => 'SLE', 'SG' => 'SGP', 'SK' => 'SVK', 'SI' => 'SVN',
            'SO' => 'SOM', 'ZA' => 'ZAF', 'KR' => 'KOR', 'SS' => 'SSD',
            'ES' => 'ESP', 'LK' => 'LKA', 'SD' => 'SDN', 'SR' => 'SUR',
            'SE' => 'SWE', 'CH' => 'CHE', 'SY' => 'SYR', 'TW' => 'TWN',
            'TJ' => 'TJK', 'TZ' => 'TZA', 'TH' => 'THA', 'TG' => 'TGO',
            'TT' => 'TTO', 'TN' => 'TUN', 'TR' => 'TUR', 'TM' => 'TKM',
            'UG' => 'UGA', 'UA' => 'UKR', 'UY' => 'URY', 'UZ' => 'UZB',
            'VE' => 'VEN', 'VN' => 'VNM', 'YE' => 'YEM', 'ZM' => 'ZMB',
            'ZW' => 'ZWE'
        ];

        $data['nationality']      = strtoupper($data['nationality']);
        $data['destination_code'] = strtoupper($data['destination_code']);

        if (strlen($data['nationality']) === 2) {
            $data['nationality'] = $codeMap[$data['nationality']] ?? $data['nationality'];
        }
        if (strlen($data['destination_code']) === 2) {
            $data['destination_code'] = $codeMap[$data['destination_code']] ?? $data['destination_code'];
        }

        $orderId = 'ORDVISA-' . strtoupper(uniqid());
        $record  = FluxirVisaApplication::create([
            'order_id'         => $orderId,
            'status'           => 'draft',
            'first_name'       => $data['first_name'],
            'last_name'        => $data['last_name'],
            'email'            => $data['email'],
            'phone'            => $data['phone'] ?? null,
            'nationality'      => $data['nationality'],
            'destination_code' => $data['destination_code'],
            'origination_code' => $data['nationality'],
            'arrival_date'     => $data['arrival_date'],
            'departure_date'   => $data['departure_date'],
        ]);

        try {
            $person = $this->fluxir->createPerson([
                'firstName' => $data['first_name'],
                'lastName'  => $data['last_name'],
                'email'     => $data['email'],
            ]);
            if (!$person['success']) {
                return $this->fail($record, 'createPerson', $person);
            }
            $personId = $person['data']['id'] ?? null;

            $tripPayload = [
                'originationCode' => strtoupper($data['nationality']),
                'destinationCode' => strtoupper($data['destination_code']),
                'from'            => $data['arrival_date'],
                'to'              => $data['departure_date'],
                'description'     => 'GoTrips e-visa ' . $orderId,
            ];
            $trip = $this->fluxir->createTrip($tripPayload);
            if (!$trip['success']) {
                return $this->fail($record, 'createTrip', $trip);
            }
            $tripId = $trip['data']['id'] ?? null;

            $intent = $this->fluxir->resolveServiceIntentForType($tripPayload, (int) $data['visa_type_id']);
            if (!$intent) {
                return $this->fail($record, 'resolveServiceIntent', ['error' => 'No visa service intent']);
            }
            $documentVersionId = $intent['documentSchemeVersionId'] ?? null;

            $app = $this->fluxir->createServiceApplication([
                'serviceType'       => 'Visa',
                'providerName'      => $intent['provider'] ?? config('fluxir.provider_name'),
                'serviceIntentKey'  => $intent['serviceIntentKey'] ?? null,
                'personId'          => $personId,
                'tripId'            => $tripId,
                'documentVersionId' => $documentVersionId,
                'dueDate'           => $data['departure_date'],
            ]);
            if (!$app['success']) {
                return $this->fail($record, 'createServiceApplication', $app);
            }
            $serviceAppId = $app['data']['id'] ?? null;

            $record->update([
                'fluxir_person_id'              => $personId,
                'fluxir_trip_id'                => $tripId,
                'fluxir_service_application_id' => $serviceAppId,
                'state'                         => $app['data']['state'] ?? 'Draft',
                'amount'                        => EvisaSetting::customerPrice((float) ($intent['fee'] ?? 0)),
                'currency'                      => 'USD',
                'status'                        => 'created',
            ]);

            // Determine which scheme items are files vs text, so we route each
            // submitted value to the right Fluxir call.
            $fileNameIds = [];
            if ($documentVersionId) {
                $schemeRes = $this->fluxir->getDocumentsScheme($documentVersionId);
                if ($schemeRes['success'] ?? false) {
                    foreach ($this->fluxir->normalizeScheme($schemeRes['data'] ?? []) as $sec) {
                        foreach ($sec['fields'] as $f) {
                            if ($f['is_file']) {
                                $fileNameIds[$f['name_id']] = true;
                            }
                        }
                    }
                }
            }

            // Upload every file item present in the request.
            $attachments = [];
            foreach (($request->file('files') ?? []) as $nameId => $file) {
                if (!$file) {
                    continue;
                }
                $up = $this->fluxir->uploadDocumentItem(
                    $serviceAppId,
                    $nameId,
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                );
                if (!$up['success']) {
                    return $this->fail($record, "uploadDocumentItem:{$nameId}", $up);
                }
                $attachments[$nameId] = $up['data']['id'] ?? null;
            }
            $record->update(['attachments' => $attachments]);

            // Build the PATCH items map from non-file fields (skip blanks/files).
            $items = [];
            foreach (($data['items'] ?? []) as $nameId => $value) {
                if (isset($fileNameIds[$nameId]) || $value === null || $value === '') {
                    continue;
                }
                $items[$nameId] = $value;
            }

            if (config('fluxir.deferred_payment')) {
                $review = $this->fluxir->submitForReview($serviceAppId, $items);
                if (!$review['success']) {
                    return $this->fail($record, 'submitForReview', $review);
                }
                $record->update(['state' => $review['data']['state'] ?? 'ReadyForReview', 'status' => 'submitted', 'last_response' => $review['data'] ?? null]);
                return response()->json([
                    'success' => true, 'order_id' => $orderId, 'on_credit' => true,
                    'amount' => $record->amount, 'currency' => $record->currency,
                    'redirect' => route('visa.fluxir.success', ['order_id' => $orderId]),
                ]);
            }

            $update = $this->fluxir->updateServiceApplication($serviceAppId, $items, 'ReadyForPayment');
            if (!$update['success']) {
                return $this->fail($record, 'updateServiceApplication', $update);
            }
            $record->update(['state' => 'ReadyForPayment', 'status' => 'awaiting_payment']);

            $checkout = $this->fluxir->getCheckout(
                $tripId,
                [$serviceAppId],
                config('fluxir.success_url') . '?order_id=' . $orderId,
                config('fluxir.cancel_url') . '?order_id=' . $orderId
            );
            if (!$checkout['success']) {
                return $this->fail($record, 'getCheckout', $checkout);
            }

            $record->update(['checkout_session_id' => $checkout['session_id'] ?? null, 'last_response' => $checkout['data']]);

            return response()->json([
                'success'             => true,
                'order_id'            => $orderId,
                'checkout_session_id' => $checkout['session_id'] ?? null,
                'amount'              => $record->amount,
                'currency'            => $record->currency,
            ]);
        } catch (\Throwable $e) {
            Log::error('Fluxir e-visa apply exception', ['order_id' => $orderId, 'message' => $e->getMessage()]);
            $record->update(['status' => 'failed']);
            return response()->json(['success' => false, 'message' => 'We could not start your visa application. Please try again.'], 500);
        }
    }

    /* ------------------------------------------------------------- helpers */

    /** ISO alpha-2 -> flag emoji (regional indicator pair). */
    private function flagEmoji(string $alpha2): string
    {
        $alpha2 = strtoupper(trim($alpha2));
        if (strlen($alpha2) !== 2) {
            return '🌍';
        }
        $a = 0x1F1E6 + (ord($alpha2[0]) - 65);
        $b = 0x1F1E6 + (ord($alpha2[1]) - 65);
        return mb_convert_encoding('&#' . $a . ';&#' . $b . ';', 'UTF-8', 'HTML-ENTITIES');
    }

    /** "30 Days", "3 Months" — friendly duration label. */
    private function duration($n, $unit): ?string
    {
        if (!$n) {
            return null;
        }
        return $n . ' ' . rtrim((string) $unit, 's') . ($n > 1 ? 's' : '');
    }

    /** Persist a failed step and return a clean JSON error. */
    protected function fail(FluxirVisaApplication $record, string $step, array $result)
    {
        Log::error("Fluxir e-visa step failed: {$step}", ['order_id' => $record->order_id, 'error' => $result['error'] ?? null]);
        $record->update(['status' => 'failed', 'last_response' => $result['data'] ?? null]);
        return response()->json(['success' => false, 'step' => $step, 'message' => 'We could not complete your visa application. Please try again or contact support.'], 502);
    }
}
