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
    public function __construct(protected FluxirService $fluxir)
    {
    }

    /** The picker page. */
    public function form()
    {
        $catalog = $this->fluxir->isConfigured() ? $this->fluxir->getOnlineVisaCatalog() : [];
        $markup  = EvisaSetting::markupPercent();

        // Shape a lightweight country list for the picker (code, name, flag, #types).
        $countries = [];
        foreach ($catalog as $code => $c) {
            $countries[] = [
                'code'  => $code,
                'name'  => $c['name'],
                'flag'  => $this->flagEmoji($c['alpha2'] ?? ''),
                'types' => count($c['types']),
            ];
        }

        // All countries for the nationality picker.
        $nationalities = $this->fluxir->isConfigured() ? $this->fluxir->getCountryOptions() : [];

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
        if (strlen($code) !== 3) {
            return response()->json(['success' => false, 'types' => []]);
        }
        if (strlen($nat) !== 3) {
            return response()->json(['success' => false, 'needs_nationality' => true, 'types' => []]);
        }

        $catalog = $this->fluxir->getOnlineVisaCatalog();
        $country = $catalog[$code] ?? null;
        $metaById = [];
        foreach (($country['types'] ?? []) as $t) {
            $metaById[$t['id']] = $t;
        }

        $trip = [
            'originationCode' => $nat,
            'destinationCode' => $code,
            'from'            => $request->query('arrival_date')   ?: now()->addDays(30)->format('Y-m-d'),
            'to'              => $request->query('departure_date') ?: now()->addDays(40)->format('Y-m-d'),
        ];
        $intents = $this->fluxir->getServiceIntents($trip, ['Visa']);
        $items = ($intents['success'] ?? false) ? ($intents['data']['items'] ?? []) : [];

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
                'id'       => $typeId,
                'name'     => $meta['name'] ?? ($country['name'] ?? $code) . ' eVisa',
                'validity' => $this->duration($meta['validity'] ?? null, $meta['validity_unit'] ?? null),
                'stay'     => $this->duration($meta['stay'] ?? null, $meta['stay_unit'] ?? null),
                'entry'    => $meta['entry'] ?? null,
                'price'    => EvisaSetting::customerPrice((float) $fee),
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
            'destination_code' => 'required|string|size:3',
            'visa_type_id'     => 'required|integer',
            'nationality'      => 'nullable|string|size:3',
            'arrival_date'     => 'nullable|date',
            'departure_date'   => 'nullable|date',
        ]);

        $trip = [
            'originationCode' => strtoupper($data['nationality'] ?? '') ?: null,
            'destinationCode' => strtoupper($data['destination_code']),
            'from'            => $data['arrival_date']   ?? now()->addDays(30)->format('Y-m-d'),
            'to'              => $data['departure_date'] ?? now()->addDays(40)->format('Y-m-d'),
        ];

        $intent = $this->fluxir->resolveServiceIntentForType($trip, (int) $data['visa_type_id']);
        if (!$intent) {
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

    /** Run the Fluxir application with a scheme-driven (dynamic) document set. */
    public function apply(Request $request)
    {
        if (!$this->fluxir->isConfigured()) {
            return response()->json(['success' => false, 'message' => 'Visa service is not configured yet. Please contact support.'], 503);
        }

        $data = $request->validate([
            'first_name'       => 'required|string|max:100',
            'last_name'        => 'required|string|max:100',
            'email'            => 'required|email',
            'phone'            => 'nullable|string|max:30',
            'nationality'      => 'required|string|size:3',
            'destination_code' => 'required|string|size:3',
            'visa_type_id'     => 'required|integer',
            'arrival_date'     => 'required|date',
            'departure_date'   => 'required|date|after_or_equal:arrival_date',
            'items'            => 'array',        // nameId => scalar value
            'files'            => 'array',        // nameId => UploadedFile
            'files.*'          => 'file|mimes:pdf,jpg,jpeg,png|max:8192',
        ]);

        $orderId = 'ORDVISA-' . strtoupper(uniqid());
        $record  = FluxirVisaApplication::create([
            'order_id'         => $orderId,
            'status'           => 'draft',
            'first_name'       => $data['first_name'],
            'last_name'        => $data['last_name'],
            'email'            => $data['email'],
            'phone'            => $data['phone'] ?? null,
            'nationality'      => strtoupper($data['nationality']),
            'destination_code' => strtoupper($data['destination_code']),
            'origination_code' => strtoupper($data['nationality']),
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
