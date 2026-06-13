<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Fluxir e-Visa (Global Travel Compliance) API client.
 *
 * Implements the documented trip flow (https://developer.fluxir.com):
 *   token -> createPerson -> createTrip -> createServiceApplication ->
 *   uploadIdentityDocument -> updateServiceApplication(state) ->
 *   getCheckout -> finalizeCheckout.
 *
 * Conventions mirror {@see MontyEsimService}: a cached OAuth bearer token, a
 * single request() wrapper that retries once on 401/403, and a uniform
 * ['success' => bool, 'data'|'error' => ...] return shape.
 *
 * Auth = OAuth2 client_credentials (scope TravelAideAPI). EVERY call also
 * carries the X-Tenant header.
 */
class FluxirService
{
    protected string $authUrl;
    protected string $apiUrl;
    protected ?string $tenantId;
    protected ?string $clientId;
    protected ?string $clientSecret;
    protected string $scope;
    protected string $grantType;
    protected int $timeout;
    protected bool $debug;

    public function __construct()
    {
        $this->authUrl      = rtrim((string) config('fluxir.auth_url'), '/');
        $this->apiUrl       = rtrim((string) config('fluxir.api_url'), '/');
        $this->tenantId     = config('fluxir.tenant_id');
        $this->clientId     = config('fluxir.client_id');
        $this->clientSecret = config('fluxir.client_secret');
        $this->scope        = (string) config('fluxir.scope', 'TravelAideAPI');
        $this->grantType    = (string) config('fluxir.grant_type', 'client_credentials');
        $this->timeout      = (int) config('fluxir.timeout', 45);
        $this->debug        = (bool) config('fluxir.debug', false);
    }

    /** Whether the integration has the credentials it needs to run. */
    public function isConfigured(): bool
    {
        return $this->tenantId && $this->clientId && $this->clientSecret;
    }

    /* ===================================================================
     | Authentication
     |===================================================================*/

    public function getToken(): string
    {
        return Cache::get('fluxir_access_token') ?: $this->authenticate();
    }

    /**
     * Exchange client credentials for a bearer token (form-encoded), per docs:
     * POST {auth_url}/connect/token  with X-Tenant header.
     */
    protected function authenticate(): string
    {
        try {
            $response = Http::timeout($this->timeout)
                ->asForm()
                ->withHeaders(['X-Tenant' => (string) $this->tenantId])
                ->post("{$this->authUrl}/connect/token", [
                    'grant_type'    => $this->grantType,
                    'scope'         => $this->scope,
                    'client_id'     => (string) $this->clientId,
                    'client_secret' => (string) $this->clientSecret,
                ]);

            if ($response->successful()) {
                $data  = $response->json();
                $token = $data['access_token'] ?? null;
                if ($token) {
                    Cache::put('fluxir_access_token', $token, (int) config('fluxir.token_ttl', 43200));
                    return $token;
                }
            }

            Log::error('Fluxir authentication failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \RuntimeException('Fluxir authentication failed: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Fluxir authentication exception', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    /* ===================================================================
     | Core request wrapper
     |===================================================================*/

    /**
     * Authenticated JSON request to the Fluxir API. Retries once on 401/403.
     *
     * @param string $method   GET|POST|PATCH|PUT|DELETE
     * @param string $endpoint Path under api_url, e.g. 'api/app/persons'
     */
    protected function request(string $method, string $endpoint, array $payload = [], array $query = []): array
    {
        return $this->send($method, $endpoint, function (PendingRequest $req) use ($method, $endpoint, $payload, $query) {
            $url = "{$this->apiUrl}/" . ltrim($endpoint, '/');
            return match (strtoupper($method)) {
                'GET'    => $req->get($url, $query),
                'POST'   => $req->post($url, $payload),
                'PATCH'  => $req->patch($url, $payload),
                'PUT'    => $req->put($url, $payload),
                'DELETE' => $req->delete($url, $payload),
                default  => $req->get($url, $query),
            };
        }, fn (PendingRequest $req) => $req->acceptJson()->asJson());
    }

    /**
     * Shared send/retry/normalise pipeline used by both JSON and multipart calls.
     *
     * @param callable $dispatch  fn(PendingRequest): Response — performs the call
     * @param callable $decorate  fn(PendingRequest): PendingRequest — content headers
     */
    protected function send(string $method, string $endpoint, callable $dispatch, callable $decorate): array
    {
        $token = $this->getToken();

        $build = function () use (&$token, $decorate) {
            $req = Http::timeout($this->timeout)
                ->withToken($token)
                ->withHeaders([
                    'X-Tenant'        => (string) $this->tenantId,
                    'Accept-Language' => 'en',
                ]);
            return $decorate($req);
        };

        try {
            if ($this->debug) {
                Log::debug('Fluxir request', compact('method', 'endpoint'));
            }

            $response = $dispatch($build());

            if (in_array($response->status(), [401, 403])) {
                Cache::forget('fluxir_access_token');
                $token    = $this->authenticate();
                $response = $dispatch($build());
            }

            if ($this->debug) {
                Log::debug('Fluxir response', ['status' => $response->status(), 'body' => $response->body()]);
            }

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json() ?? []];
            }

            if ($response->status() === 204) {
                return ['success' => true, 'data' => []];
            }

            Log::error('Fluxir API error', [
                'method'   => $method,
                'endpoint' => $endpoint,
                'status'   => $response->status(),
                'body'     => $response->body(),
            ]);

            return [
                'success' => false,
                'status'  => $response->status(),
                'error'   => 'Fluxir error: ' . ($response->json('error.message') ?? $response->json('message') ?? $response->body()),
                'data'    => $response->json() ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('Fluxir API exception', [
                'method'   => $method,
                'endpoint' => $endpoint,
                'message'  => $e->getMessage(),
            ]);
            return ['success' => false, 'error' => 'Visa service is temporarily unavailable. Please try again.'];
        }
    }

    /* ===================================================================
     | Trip flow — step by step
     |===================================================================*/

    /**
     * Step 1 — Create a person (traveller).
     * POST /api/app/persons  { firstName, lastName, email, hasVisas, items }
     */
    public function createPerson(array $person): array
    {
        return $this->request('POST', 'api/app/persons', [
            'firstName' => $person['firstName'] ?? null,
            'lastName'  => $person['lastName'] ?? null,
            'email'     => $person['email'] ?? null,
            'hasVisas'  => $person['hasVisas'] ?? false,
            'items'     => $person['items'] ?? (object) [],
        ]);
    }

    /**
     * Step 2 — Create a trip.
     * POST /api/app/trip  { originationCode, destinatonCode, from, to, ... }
     * NOTE: Fluxir's field is spelled "destinatonCode" (sic) per their schema.
     */
    public function createTrip(array $trip): array
    {
        return $this->request('POST', 'api/app/trip', [
            'originationCode'    => $trip['originationCode'] ?? config('fluxir.default_origin'),
            'destinatonCode'     => $trip['destinationCode'] ?? config('fluxir.default_destination'),
            'from'               => $trip['from'] ?? null,
            'to'                 => $trip['to'] ?? null,
            'description'        => $trip['description'] ?? null,
            'visaApplicationIds' => $trip['visaApplicationIds'] ?? null,
            'notes'              => $trip['notes'] ?? null,
        ]);
    }

    /**
     * Registered provider names for a service type.
     * GET /api/app/travel-services/provider-names?serviceType=Visa (live-verified).
     * Returns { items: ["Fluxir", ...] }.
     */
    public function getProviderNames(string $serviceType = 'Visa'): array
    {
        return $this->request('GET', 'api/app/travel-services/provider-names', [], ['serviceType' => $serviceType]);
    }

    /**
     * Discover service intents for a trip. Each intent carries serviceIntentKey,
     * provider, documentSchemeVersionId and fee.
     * POST /api/app/travel-services/service-intents
     *
     * VERIFIED live: the tripContext.items MUST use the system.* keys below to
     * scope by destination — otherwise the endpoint tries to return the entire
     * catalog and fails with 414 RequestUriTooLong.
     */
    public function getServiceIntents(array $trip, array $serviceTypes = ['Visa']): array
    {
        return $this->request('POST', 'api/app/travel-services/service-intents', [
            'serviceTypesFilter'  => $serviceTypes,
            'inputRestrictionMode' => 'ByPassOnMissing',
            'continuationToken'   => '',
            'tripContext' => [
                'items' => array_filter([
                    'system.tripFrom'        => $trip['from'] ?? null,
                    'system.tripTo'          => $trip['to'] ?? null,
                    'system.tripOrigination' => $trip['originationCode'] ?? null,
                    'system.tripDestination' => $trip['destinationCode'] ?? null,
                ]),
                'travelerContexts' => [],
            ],
        ]);
    }

    /**
     * Resolve the full service intent for a visa to a destination.
     * Returns the intent item: ['serviceIntentKey'=>'Visa-Fluxir-ARE-2',
     * 'provider'=>'Fluxir', 'documentSchemeVersionId'=>1218, 'fee'=>96, ...].
     *
     * If a fixed key is configured, it is matched within the discovered list so
     * we still get its provider + documentSchemeVersionId.
     */
    public function resolveServiceIntent(array $trip): ?array
    {
        $intents = $this->getServiceIntents($trip, ['Visa']);
        if (!($intents['success'] ?? false)) {
            return null;
        }

        $items = array_values(array_filter(
            $intents['data']['items'] ?? [],
            fn ($i) => ($i['serviceType'] ?? null) === 'Visa'
        ));

        $fixed = config('fluxir.service_intent_key');
        $dest  = $trip['destinationCode'] ?? null;

        // Prefer a configured key, then destination match, then first.
        foreach ($items as $i) {
            if ($fixed && ($i['serviceIntentKey'] ?? null) === $fixed) {
                return $i;
            }
        }
        foreach ($items as $i) {
            if (!$dest || ($i['destinationCountryCode'] ?? null) === $dest) {
                return $i;
            }
        }

        return $items[0] ?? null;
    }

    /**
     * Step 3 — Create a travel-service (visa) application.
     * POST /api/app/travel-services
     *
     * VERIFIED live: providerName AND serviceIntentKey are REQUIRED (the public
     * docs incorrectly mark them optional).
     */
    public function createServiceApplication(array $app): array
    {
        return $this->request('POST', 'api/app/travel-services', [
            'serviceType'       => $app['serviceType'] ?? config('fluxir.service_type', 'Visa'),
            'providerName'      => $app['providerName'] ?? config('fluxir.provider_name'),
            'personId'          => $app['personId'],
            'serviceIntentKey'  => $app['serviceIntentKey'] ?? null,
            'tripId'            => $app['tripId'],
            'documentVersionId' => $app['documentVersionId'] ?? null,
            'serviceTypeId'     => $app['serviceTypeId'] ?? null,
            'dueDate'           => $app['dueDate'] ?? null,
        ]);
    }

    /**
     * Step 4 — Upload a file to a specific document item of a service application.
     * POST /api/app/attachments/document-item?serviceApplicationId=..&itemNameId=..
     * (multipart/form-data, field "file"). VERIFIED live.
     *
     * @param string $itemNameId Scheme item, e.g. 'passportFile' or 'traveler.personalPhoto'
     */
    public function uploadDocumentItem(int|string $serviceApplicationId, string $itemNameId, string $contents, string $filename): array
    {
        $url = "{$this->apiUrl}/api/app/attachments/document-item?" . http_build_query([
            'serviceApplicationId' => $serviceApplicationId,
            'itemNameId'           => $itemNameId,
        ]);

        return $this->send(
            'POST',
            'api/app/attachments/document-item',
            fn (PendingRequest $req) => $req->post($url),
            fn (PendingRequest $req) => $req->acceptJson()->attach('file', $contents, $filename)
        );
    }

    /**
     * Standalone identity-document upload (not tied to a scheme item).
     * POST /api/app/attachments/identity-document (multipart, field "file").
     */
    public function uploadIdentityDocument(string $contents, string $filename): array
    {
        return $this->send(
            'POST',
            'api/app/attachments/identity-document',
            fn (PendingRequest $req) => $req->post("{$this->apiUrl}/api/app/attachments/identity-document"),
            fn (PendingRequest $req) => $req->acceptJson()->attach('file', $contents, $filename)
        );
    }

    /**
     * Step 5 — Update the service application (fill visa form items, advance state).
     * PATCH /api/app/travel-services/{id}
     *
     * @param string $state One of Draft|ReadyForPayment|ReadyForReview|InReview|...
     */
    public function updateServiceApplication(int|string $serviceApplicationId, array $items, string $state = 'Draft'): array
    {
        return $this->request('PATCH', "api/app/travel-services/{$serviceApplicationId}", [
            'items' => $items,
            'state' => $state,
        ]);
    }

    /**
     * Step 6 (CREDIT / invoicing model) — submit the application straight for
     * review, bypassing the Stripe checkout entirely.
     *
     * Per Fluxir (Kirill Gandyl, 11 Jun 2026): once the tenant is on the
     * invoicing model, "no additional API calls are required — you simply call
     * PATCH /api/app/travel-services/{id} with the status set to ReadyForReview
     * and the rest is handled automatically." Fluxir then bills us monthly.
     *
     * Files must already be attached (see uploadDocumentItem); items may be empty.
     */
    public function submitForReview(int|string $serviceApplicationId, array $items = []): array
    {
        return $this->updateServiceApplication($serviceApplicationId, $items, 'ReadyForReview');
    }

    /**
     * Step 6 (PAY-NOW model) — Get the hosted checkout (Stripe) session for a trip.
     * GET /api/app/trip/{tripId}/checkout?serviceApplicationIds=..&successUrl=..&cancelUrl=..
     *
     * VERIFIED live: responds { "result": "cs_test_..." } — a Stripe Checkout
     * Session id. Exposed here as data.session_id for the caller.
     */
    public function getCheckout(int|string $tripId, array $serviceApplicationIds, ?string $successUrl = null, ?string $cancelUrl = null): array
    {
        $res = $this->request('GET', "api/app/trip/{$tripId}/checkout", [], [
            'serviceApplicationIds' => implode(',', $serviceApplicationIds),
            'successUrl'            => $successUrl ?? config('fluxir.success_url'),
            'cancelUrl'             => $cancelUrl ?? config('fluxir.cancel_url'),
        ]);

        if ($res['success'] ?? false) {
            $res['session_id'] = $res['data']['result'] ?? $res['data']['id'] ?? null;
        }

        return $res;
    }

    /**
     * Step 7 — Finalize the checkout after payment returns.
     * POST /api/app/trip/{tripId}/finalize-checkout  (empty body)
     */
    public function finalizeCheckout(int|string $tripId): array
    {
        return $this->request('POST', "api/app/trip/{$tripId}/finalize-checkout", []);
    }

    /* ===================================================================
     | Reference data & lookups
     |===================================================================*/

    /** Countries available for trips. GET /api/app/trip-country/countries (live-verified). */
    public function getTripCountries(array $query = []): array
    {
        return $this->request('GET', 'api/app/trip-country/countries', [], $query);
    }

    /**
     * Flat [['code'=>alpha3,'name'=>title], ...] list of all countries, A→Z,
     * for nationality/origin pickers. Cached — reference data changes rarely.
     */
    public function getCountryOptions(int $ttl = 86400): array
    {
        return Cache::remember('fluxir.country_options.v1', $ttl, function () {
            $res = $this->getTripCountries();
            $out = [];
            foreach (($res['data'] ?? []) as $c) {
                if (!empty($c['codeAlpha3'])) {
                    $out[] = ['code' => $c['codeAlpha3'], 'name' => $c['title'] ?? $c['codeAlpha3']];
                }
            }
            usort($out, fn ($a, $b) => strcmp($a['name'], $b['name']));
            return $out;
        });
    }

    /**
     * Normalize a raw documents-scheme payload into a flat, view-friendly tree:
     * sections -> fields. Maps Fluxir item types to form input types and pulls
     * English labels + enum option lists. Drives the dynamic e-visa form.
     *
     * @param array $schemeData  The `data` array from getDocumentsScheme().
     * @return array<int,array{title:string,fields:array}>
     */
    public function normalizeScheme(array $schemeData): array
    {
        $label = function ($node): string {
            foreach ((($node['title']['values'] ?? [])) as $v) {
                if (($v['language'] ?? '') === 'English') {
                    return trim($v['value'] ?? '');
                }
            }
            return trim((($node['title']['values'][0]['value'] ?? '')) ?: ($node['nameId'] ?? ''));
        };

        // Fluxir itemType -> [html input kind, isFile]
        $map = [
            'String'         => ['text', false],
            'StringMultiLine'=> ['textarea', false],
            'Number'         => ['number', false],
            'Date'           => ['date', false],
            'PhoneNumber'    => ['tel', false],
            'Address'        => ['text', false],
            'Enum'           => ['select', false],
            'EnumSet'        => ['multiselect', false],
            'File'           => ['file', true],
            'FilePassport'   => ['file', true],
        ];

        $sections = [];
        foreach ($schemeData as $section) {
            if (!is_array($section)) {
                continue;
            }
            $fields = [];
            foreach (($section['items'] ?? []) as $item) {
                $type = $item['itemType'] ?? 'String';
                [$kind, $isFile] = $map[$type] ?? ['text', false];

                $options = [];
                if (in_array($kind, ['select', 'multiselect'], true)) {
                    foreach (($item['enumValue']['enumValues'] ?? []) as $opt) {
                        $options[] = [
                            'value' => $opt['nameId'] ?? '',
                            'label' => $label($opt) ?: ($opt['nameId'] ?? ''),
                        ];
                    }
                }

                $fields[] = [
                    'name_id'   => $item['nameId'] ?? '',
                    'label'     => $label($item) ?: ($item['nameId'] ?? ''),
                    'kind'      => $kind,
                    'is_file'   => $isFile,
                    'required'  => (bool) ($item['validationIsRequired'] ?? !($item['isOptional'] ?? false)),
                    'maxlength' => $item['validationMaxLength'] ?? null,
                    'options'   => $options,
                ];
            }
            if ($fields) {
                $sections[] = ['title' => $label($section) ?: 'Details', 'fields' => $fields];
            }
        }

        return $sections;
    }

    /**
     * Resolve the service intent for a SPECIFIC visa type within a destination.
     * serviceIntentKey format is `Visa-{provider}-{dest}-{visaTypeId}`, so we
     * match the chosen visa type by that trailing id. Falls back to the generic
     * destination resolver when no id is given or no exact match is found.
     */
    public function resolveServiceIntentForType(array $trip, ?int $visaTypeId): ?array
    {
        if (!$visaTypeId) {
            return $this->resolveServiceIntent($trip);
        }

        $intents = $this->getServiceIntents($trip, ['Visa']);
        if (!($intents['success'] ?? false)) {
            return null;
        }
        $items = array_values(array_filter(
            $intents['data']['items'] ?? [],
            fn ($i) => ($i['serviceType'] ?? null) === 'Visa'
        ));
        foreach ($items as $i) {
            $key = $i['serviceIntentKey'] ?? '';
            if (preg_match('/-' . $visaTypeId . '$/', $key)) {
                return $i;
            }
        }
        return $this->resolveServiceIntent($trip);
    }

    /**
     * Visa types. GET /api/app/visas/types (live-verified).
     * Optional query: Filter, Origination, Destination, WithVersions, SkipCount, MaxResultCount.
     */
    public function getVisaTypes(array $query = []): array
    {
        return $this->request('GET', 'api/app/visas/types', [], $query);
    }

    /**
     * Online (web-bookable) eVisa catalog grouped by destination country.
     *
     * Filters the full visa-type list to applicationMethod=Online (the only ones
     * we can sell through the on-site flow) and groups by destination, attaching
     * each type's net fee, durations and active document-scheme version. Country
     * names + alpha-2 (for flags) come from the trip-country reference list.
     *
     * Cached — Fluxir's catalog changes rarely. Returns [] on API failure so the
     * caller can degrade gracefully.
     *
     * @return array<string,array> keyed by destination alpha-3 code, each:
     *   ['code','alpha2','name','types'=>[['id','name','category','entry',
     *    'validity','validity_unit','stay','stay_unit','net_fee','processing',
     *    'version_id'], ...]]
     */
    public function getOnlineVisaCatalog(int $ttl = 86400): array
    {
        return Cache::remember('fluxir.online_catalog.v1', $ttl, function () {
            $res = $this->getVisaTypes(['WithVersions' => 'true', 'MaxResultCount' => 1000]);
            if (!($res['success'] ?? false)) {
                return [];
            }

            // alpha-3 => ['title','alpha2'] for labels + flags.
            $names = [];
            $ref = $this->getTripCountries();
            foreach (($ref['data'] ?? []) as $c) {
                if (!empty($c['codeAlpha3'])) {
                    $names[$c['codeAlpha3']] = [
                        'title'  => $c['title'] ?? $c['codeAlpha3'],
                        'alpha2' => $c['codeAlpha2'] ?? '',
                    ];
                }
            }

            $countries = [];
            foreach (($res['data']['items'] ?? []) as $it) {
                $t = $it['type'] ?? [];
                if (($t['applicationMethod'] ?? '') !== 'Online') {
                    continue;
                }
                $dest = $t['destinationCountry'] ?? null;
                $fee  = $it['billing']['fee'] ?? null;
                // Only sellable types: a real numeric net fee > 0.
                if (!$dest || !is_numeric($fee) || (float) $fee <= 0) {
                    continue;
                }

                $versionId = null;
                foreach (($t['versions'] ?? []) as $v) {
                    if (($v['state'] ?? '') === 'Published') {
                        $versionId = $v['id'] ?? null;
                        break;
                    }
                }

                $countries[$dest] ??= [
                    'code'   => $dest,
                    'alpha2' => $names[$dest]['alpha2'] ?? '',
                    'name'   => $names[$dest]['title'] ?? $dest,
                    'types'  => [],
                ];
                $countries[$dest]['types'][] = [
                    'id'           => $t['id'] ?? null,
                    'name'         => trim($t['name'] ?? 'eVisa'),
                    'category'     => $t['categoryName'] ?? null,
                    'entry'        => $t['entryLimitation'] ?? null,
                    'validity'     => $t['validityPeriod'] ?? null,
                    'validity_unit'=> $t['validityPeriodUnit'] ?? null,
                    'stay'         => $t['stayDuration'] ?? null,
                    'stay_unit'    => $t['stayDurationUnit'] ?? null,
                    'net_fee'      => (float) $fee,
                    'processing'   => $it['billing']['expectedProcessingTime'] ?? null,
                    'version_id'   => $versionId,
                ];
            }

            // Cheapest type first within each country; countries A→Z by name.
            foreach ($countries as &$c) {
                usort($c['types'], fn ($a, $b) => $a['net_fee'] <=> $b['net_fee']);
            }
            unset($c);
            uasort($countries, fn ($a, $b) => strcmp($a['name'], $b['name']));

            return $countries;
        });
    }

    /**
     * Active document-scheme version for a visa type.
     * GET /api/app/visa-type/{id}/active-version (live-verified path family).
     */
    public function getActiveVisaTypeVersion(int|string $visaTypeId): array
    {
        return $this->request('GET', "api/app/visa-type/{$visaTypeId}/active-version");
    }

    /**
     * Document scheme (required documents) for a visa scheme version.
     * GET /api/app/documents-scheme?visaSchemeVersionId=..&normalized=.. (per docs).
     */
    public function getDocumentsScheme(int|string $visaSchemeVersionId, bool $normalized = true): array
    {
        return $this->request('GET', 'api/app/documents-scheme', [], [
            'visaSchemeVersionId' => $visaSchemeVersionId,
            'normalized'          => $normalized ? 'true' : 'false',
        ]);
    }

    /** Run passport OCR to pre-fill traveller fields. POST /api/app/passport-recognition */
    public function passportRecognition(string $contents, string $filename): array
    {
        return $this->send(
            'POST',
            'api/app/passport-recognition',
            fn (PendingRequest $req) => $req->post("{$this->apiUrl}/api/app/passport-recognition"),
            fn (PendingRequest $req) => $req->acceptJson()->attach('file', $contents, $filename)
        );
    }

    /** Retrieve a trip (status, applications, payment). GET /api/app/trip/{tripId} */
    public function getTrip(int|string $tripId): array
    {
        return $this->request('GET', "api/app/trip/{$tripId}");
    }

    /** Retrieve a service application's current state. GET /api/app/travel-services/{id} */
    public function getServiceApplication(int|string $serviceApplicationId): array
    {
        return $this->request('GET', "api/app/travel-services/{$serviceApplicationId}");
    }
}
