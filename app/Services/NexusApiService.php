<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Farenexus nexusAPI integration (Flight GDS + NDC).
 *
 * Wraps the nexusAPI gateway which aggregates Travelport/Galileo (1G), Amadeus,
 * Sabre and direct NDC airline content. Exposes the standard booking lifecycle:
 *
 *     search() -> price() -> book() (PNR) -> ticket() -> [cancel()/void()/refund()]
 *
 * Mirrors the conventions of {@see MontyEsimService}: cached bearer token,
 * a single request() wrapper that retries once on 401/403, and a uniform
 * ['success' => bool, ...] return shape so callers never touch raw HTTP.
 *
 * NOTE: endpoint paths and payload shapes live in config/nexusapi.php and are
 * placeholders until the official Farenexus partner docs are wired in. Methods
 * are written so that swapping in the real request/response mapping is local.
 */
class NexusApiService
{
    protected string $baseUrl;
    protected array $auth;
    protected array $gds;
    protected array $endpoints;
    protected float $markupPercent;
    protected int $timeout;
    protected bool $debug;

    public function __construct(?float $markupPercent = null)
    {
        $this->baseUrl       = rtrim((string) config('nexusapi.base_url'), '/');
        $this->auth          = config('nexusapi.auth', []);
        $this->gds           = config('nexusapi.gds', []);
        $this->endpoints     = config('nexusapi.endpoints', []);
        $this->markupPercent = $markupPercent ?? (float) config('nexusapi.markup_percent', 0);
        $this->timeout       = (int) config('nexusapi.timeout', 45);
        $this->debug         = (bool) config('nexusapi.debug', false);
    }

    /* ===================================================================
     | Authentication
     |===================================================================*/

    /**
     * Return a valid bearer token, authenticating if the cache is empty.
     */
    public function getToken(): string
    {
        return Cache::get('nexusapi_access_token') ?: $this->authenticate();
    }

    /**
     * Authenticate against nexusAPI and cache the bearer token.
     *
     * Sends both key/secret and username/password — confirm which pair the
     * real auth endpoint expects and trim accordingly.
     */
    protected function authenticate(): string
    {
        try {
            $response = Http::timeout($this->timeout)
                ->asJson()
                ->post($this->url($this->endpoints['token'] ?? 'v1/auth/token'), array_filter([
                    'api_key'    => $this->auth['api_key'] ?? null,
                    'api_secret' => $this->auth['api_secret'] ?? null,
                    'username'   => $this->auth['username'] ?? null,
                    'password'   => $this->auth['password'] ?? null,
                ]));

            if ($response->successful()) {
                $data  = $response->json();
                $token = $data['access_token'] ?? $data['token'] ?? null;

                if ($token) {
                    $ttl = (int) ($this->auth['token_ttl'] ?? 840);
                    Cache::put('nexusapi_access_token', $token, $ttl);
                    return $token;
                }
            }

            Log::error('nexusAPI authentication failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            throw new \RuntimeException('nexusAPI authentication failed: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('nexusAPI authentication exception', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    /* ===================================================================
     | Core request wrapper
     |===================================================================*/

    /**
     * Make an authenticated nexusAPI request. Retries once on 401/403 with a
     * fresh token. Always returns ['success' => bool, 'data'|'error' => ...].
     *
     * The GDS context (PCC, branch, devices) is injected into every call so
     * the gateway knows which point-of-sale to issue against.
     */
    protected function request(string $method, string $endpoint, array $payload = [], array $query = []): array
    {
        $token = $this->getToken();
        $url   = $this->url($endpoint);

        // Merge GDS point-of-sale context into the body for write/search calls.
        if (strtoupper($method) !== 'GET') {
            $payload = array_merge(['gds_context' => $this->gdsContext()], $payload);
        }

        $attempt = function () use ($method, $url, $payload, $query, &$token) {
            $request = Http::timeout($this->timeout)
                ->withToken($token)
                ->acceptJson()
                ->asJson();

            return match (strtoupper($method)) {
                'GET'    => $request->get($url, $query),
                'POST'   => $request->post($url, $payload),
                'PUT'    => $request->put($url, $payload),
                'DELETE' => $request->delete($url, $payload),
                default  => $request->get($url, $query),
            };
        };

        try {
            if ($this->debug) {
                Log::debug('nexusAPI request', compact('method', 'endpoint', 'payload', 'query'));
            }

            $response = $attempt();

            if (in_array($response->status(), [401, 403])) {
                Cache::forget('nexusapi_access_token');
                $token    = $this->authenticate();
                $response = $attempt();
            }

            if ($this->debug) {
                Log::debug('nexusAPI response', ['status' => $response->status(), 'body' => $response->body()]);
            }

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json() ?? []];
            }

            if ($response->status() === 204) {
                return ['success' => true, 'data' => []];
            }

            Log::error('nexusAPI error', [
                'method'   => $method,
                'endpoint' => $endpoint,
                'status'   => $response->status(),
                'body'     => $response->body(),
            ]);

            return [
                'success' => false,
                'error'   => 'nexusAPI error: ' . ($response->json('message') ?? $response->json('error') ?? $response->body()),
                'status'  => $response->status(),
                'data'    => $response->json() ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('nexusAPI exception', [
                'method'   => $method,
                'endpoint' => $endpoint,
                'message'  => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => 'nexusAPI service unavailable. Please try again.'];
        }
    }

    /* ===================================================================
     | Air (flights) — booking lifecycle
     |===================================================================*/

    /**
     * Low-fare / availability search.
     *
     * @param array $params Normalised search params, e.g.:
     *   [
     *     'trip_type'   => 'oneway|return|multicity',
     *     'segments'    => [['origin'=>'DXB','destination'=>'LHR','date'=>'2026-07-01'], ...],
     *     'adults'      => 1, 'children' => 0, 'infants' => 0,
     *     'cabin'       => 'economy|premium|business|first',
     *     'currency'    => 'AED',
     *     'direct_only' => false,
     *     'airlines'    => [],   // optional preferred/NDC airline filter
     *   ]
     */
    public function search(array $params): array
    {
        $result = $this->request('POST', $this->endpoints['air_search'], $params);

        if ($result['success']) {
            $offers = $result['data']['offers'] ?? $result['data']['results'] ?? $result['data'];
            $result['data'] = $this->applyMarkup(is_array($offers) ? $offers : []);
        }

        return $result;
    }

    /**
     * Re-price / revalidate a selected offer before booking (fare may change).
     *
     * @param string $offerId  Offer/solution key returned by search().
     */
    public function price(string $offerId, array $extra = []): array
    {
        return $this->request('POST', $this->endpoints['air_price'], array_merge([
            'offer_id' => $offerId,
        ], $extra));
    }

    /**
     * Fetch fare rules / penalties for an offer.
     */
    public function fareRules(string $offerId): array
    {
        return $this->request('POST', $this->endpoints['air_rules'], ['offer_id' => $offerId]);
    }

    /**
     * Seat map for a priced offer.
     */
    public function seatMap(string $offerId): array
    {
        return $this->request('POST', $this->endpoints['air_seatmap'], ['offer_id' => $offerId]);
    }

    /**
     * Available ancillaries (baggage, meals, seats) for an offer.
     */
    public function ancillaries(string $offerId): array
    {
        return $this->request('POST', $this->endpoints['air_ancillary'], ['offer_id' => $offerId]);
    }

    /**
     * Create the booking / PNR (book without ticketing).
     *
     * @param array $payload e.g.:
     *   [
     *     'offer_id'   => '...',                 // priced offer key
     *     'passengers' => [[                     // one per traveller
     *        'type'=>'ADT','title'=>'MR','first_name'=>'...','last_name'=>'...',
     *        'dob'=>'1990-01-01','gender'=>'M','nationality'=>'AE',
     *        'passport'=>['number'=>'...','expiry'=>'...','issuing_country'=>'AE'],
     *     ]],
     *     'contact'    => ['email'=>'...','phone'=>'...'],
     *     'ancillaries'=> [],                    // optional selected extras
     *   ]
     */
    public function book(array $payload): array
    {
        $result = $this->request('POST', $this->endpoints['air_book'], $payload);

        if ($result['success']) {
            $data = $result['data'];
            return [
                'success'       => true,
                'pnr'           => $data['pnr'] ?? $data['record_locator'] ?? null,
                'booking_ref'   => $data['booking_reference'] ?? $data['booking_id'] ?? null,
                'status'        => $data['status'] ?? 'booked',
                'ticket_time_limit' => $data['ticket_time_limit'] ?? $data['ttl'] ?? null,
                'data'          => $data,
            ];
        }

        return $result;
    }

    /**
     * Issue the ticket(s) for an existing PNR. Call after payment is captured.
     */
    public function ticket(string $bookingRef, array $extra = []): array
    {
        $result = $this->request('POST', $this->endpoints['air_ticket'], array_merge([
            'booking_reference' => $bookingRef,
        ], $extra));

        if ($result['success']) {
            $data = $result['data'];
            return [
                'success'       => true,
                'status'        => $data['status'] ?? 'ticketed',
                'ticket_numbers'=> $data['ticket_numbers'] ?? $data['tickets'] ?? [],
                'data'          => $data,
            ];
        }

        return $result;
    }

    /**
     * Retrieve / refresh a booking by reference or PNR.
     */
    public function retrieve(string $bookingRef): array
    {
        return $this->request('GET', $this->endpoints['air_retrieve'], [], [
            'booking_reference' => $bookingRef,
        ]);
    }

    /**
     * Cancel an (un-ticketed) booking.
     */
    public function cancel(string $bookingRef, array $extra = []): array
    {
        return $this->request('POST', $this->endpoints['air_cancel'], array_merge([
            'booking_reference' => $bookingRef,
        ], $extra));
    }

    /**
     * Void a ticket (within the same-day void window).
     */
    public function void(string $bookingRef, array $extra = []): array
    {
        return $this->request('POST', $this->endpoints['air_void'], array_merge([
            'booking_reference' => $bookingRef,
        ], $extra));
    }

    /**
     * Request a refund (policy-validated by nexusAPI / airline).
     */
    public function refund(string $bookingRef, array $extra = []): array
    {
        return $this->request('POST', $this->endpoints['air_refund'], array_merge([
            'booking_reference' => $bookingRef,
        ], $extra));
    }

    /* ===================================================================
     | Hotel / Car (next phase per Farenexus roadmap) — stubs
     |===================================================================*/

    public function searchHotels(array $params): array
    {
        return $this->request('POST', $this->endpoints['hotel_search'], $params);
    }

    public function bookHotel(array $payload): array
    {
        return $this->request('POST', $this->endpoints['hotel_book'], $payload);
    }

    public function searchCars(array $params): array
    {
        return $this->request('POST', $this->endpoints['car_search'], $params);
    }

    public function bookCar(array $payload): array
    {
        return $this->request('POST', $this->endpoints['car_book'], $payload);
    }

    /* ===================================================================
     | Helpers
     |===================================================================*/

    /**
     * Build the GDS point-of-sale context sent with each write/search call.
     * Optionally override the issuing branch (e.g. 'uae','canada','usa').
     */
    public function gdsContext(?string $branch = null): array
    {
        $pcc = $this->gds['pcc'] ?? null;

        if ($branch) {
            $pcc = config("nexusapi.branches.$branch") ?: $pcc;
        }

        return array_filter([
            'provider'      => $this->gds['provider'] ?? '1G',
            'pcc'           => $pcc,
            'target_branch' => $this->gds['target_branch'] ?? null,
            'gpm_client_id' => $this->gds['gpm_client_id'] ?? null,
            'accreditation' => $this->gds['accreditation'] ?? null,
            'devices'       => array_filter($this->gds['devices'] ?? []),
        ]);
    }

    /**
     * Apply configured markup to a list of offers, preserving net fare.
     */
    protected function applyMarkup(array $offers): array
    {
        if ($this->markupPercent <= 0) {
            return $offers;
        }

        $factor = 1 + ($this->markupPercent / 100);

        return array_map(function ($offer) use ($factor) {
            if (is_array($offer) && isset($offer['total_price'])) {
                $net = (float) $offer['total_price'];
                $offer['net_price']     = round($net, 2);
                $offer['total_price']   = round($net * $factor, 2);
                $offer['markup_percent'] = $this->markupPercent;
            }
            return $offer;
        }, $offers);
    }

    /**
     * Resolve a config endpoint path to an absolute URL.
     */
    protected function url(string $path): string
    {
        return $this->baseUrl . '/' . ltrim($path, '/');
    }
}
