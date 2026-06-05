<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MontyEsimService
{
    protected string $baseUrl;
    protected string $username;
    protected string $password;
    protected float $markupPercent;

    public function __construct(?float $markupPercent = null)
    {
        $this->baseUrl = rtrim(config('montyesim.base_url'), '/');
        $this->username = config('montyesim.username') ?? '';
        $this->password = config('montyesim.password') ?? '';
        $this->markupPercent = $markupPercent ?? (float) config('montyesim.markup_percent', 20);
    }

    /**
     * Authenticate with MontyeSIM and cache the access token.
     */
    public function getToken(): string
    {
        $cached = Cache::get('montyesim_access_token');
        if ($cached) {
            return $cached;
        }

        return $this->authenticate();
    }

    /**
     * Login to MontyeSIM API, cache both access and refresh tokens.
     */
    protected function authenticate(): string
    {
        try {
            $response = Http::timeout(30)
                ->post("{$this->baseUrl}/Agent/login", [
                    'username' => $this->username,
                    'password' => $this->password,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $accessToken = $data['access_token'] ?? null;
                $refreshToken = $data['refresh_token'] ?? null;

                if ($accessToken) {
                    // Cache access token for 14 minutes (TTL is 15 min)
                    Cache::put('montyesim_access_token', $accessToken, 840);
                }
                if ($refreshToken) {
                    // Cache refresh token for 55 minutes (TTL is 60 min)
                    Cache::put('montyesim_refresh_token', $refreshToken, 3300);
                }

                return $accessToken ?? '';
            }

            Log::error('MontyeSIM authentication failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \RuntimeException('MontyeSIM authentication failed: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('MontyeSIM authentication exception', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Refresh the access token using cached refresh token.
     */
    protected function refreshAuth(): ?string
    {
        $refreshToken = Cache::get('montyesim_refresh_token');
        if (!$refreshToken) {
            return $this->authenticate();
        }

        try {
            $response = Http::timeout(30)
                ->post("{$this->baseUrl}/Token/Refresh", [
                    'refresh_token' => $refreshToken,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $accessToken = $data['access_token'] ?? null;
                $newRefreshToken = $data['refresh_token'] ?? null;

                if ($accessToken) {
                    Cache::put('montyesim_access_token', $accessToken, 840);
                }
                if ($newRefreshToken) {
                    Cache::put('montyesim_refresh_token', $newRefreshToken, 3300);
                }

                return $accessToken;
            }

            // Refresh failed, try full auth
            return $this->authenticate();

        } catch (\Exception $e) {
            Log::warning('MontyeSIM token refresh failed, falling back to auth', ['error' => $e->getMessage()]);
            return $this->authenticate();
        }
    }

    /**
     * Make an authenticated request to MontyeSIM API.
     * Retries once on 401 (expired token).
     */
    protected function request(string $method, string $endpoint, array $data = [], array $query = []): array
    {
        $token = $this->getToken();
        $url = "{$this->baseUrl}/{$endpoint}";

        $attempt = function () use ($method, $url, $data, $query, &$token) {
            $request = Http::timeout(30)->withHeaders([
                'Access-Token' => $token,
                'Content-Type' => 'application/json',
            ]);

            if (!empty($query)) {
                $url_with_query = $url . '?' . http_build_query($query);
            } else {
                $url_with_query = $url;
            }

            return match (strtoupper($method)) {
                'GET' => $request->get($url_with_query),
                'POST' => $request->post($url_with_query, $data),
                default => $request->get($url_with_query),
            };
        };

        try {
            $response = $attempt();

            // Retry once on 401/403 with fresh token
            if (in_array($response->status(), [401, 403])) {
                Cache::forget('montyesim_access_token');
                $token = $this->refreshAuth();
                $response = $attempt();
            }

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            // 204 No Content means empty result (valid but empty)
            if ($response->status() === 204) {
                return [
                    'success' => true,
                    'data' => [],
                ];
            }

            Log::error('MontyeSIM API error', [
                'method' => $method,
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => 'MontyeSIM API error: ' . ($response->json('message') ?? $response->body()),
            ];

        } catch (\Exception $e) {
            Log::error('MontyeSIM API exception', [
                'method' => $method,
                'endpoint' => $endpoint,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'MontyeSIM service unavailable. Please try again.',
            ];
        }
    }

    /**
     * Get all available countries. Cached for 24 hours.
     */
    public function getCountries(): array
    {
        return Cache::remember('montyesim_countries', 86400, function () {
            $result = $this->request('GET', 'AvailableCountries', [], ['lang' => 'en']);

            if ($result['success'] && isset($result['data']['countries'])) {
                return $result['data']['countries'];
            }

            return [];
        });
    }

    /**
     * Get all available regions. Cached for 24 hours.
     */
    public function getRegions(): array
    {
        return Cache::remember('montyesim_regions', 86400, function () {
            $result = $this->request('GET', 'AvailableRegions', [], ['lang' => 'en']);

            if ($result['success'] && isset($result['data']['regions'])) {
                return $result['data']['regions'];
            }

            return [];
        });
    }

    /**
     * Get bundles for a specific country. Cached for 1 hour per country.
     * Applies markup to prices.
     */
    public function getBundles(string $countryCode, string $currencyCode = 'USD'): array
    {
        $cacheKey = "montyesim_raw_bundles_{$countryCode}_{$currencyCode}";

        $rawBundles = Cache::remember($cacheKey, 3600, function () use ($countryCode, $currencyCode) {
            $result = $this->request('GET', 'Bundles', [], [
                'country_code' => $countryCode,
                'currency_code' => $currencyCode,
            ]);

            return ($result['success'] && isset($result['data']['bundles']))
                ? $result['data']['bundles']
                : [];
        });

        $markup = 1 + ($this->markupPercent / 100);

        // MontyeSIM returns bundle prices in the requested currency (USD by default),
        // but customers are always charged in AED. Convert at the fixed AED peg
        // unless the source data is already AED. Without this, a USD price was being
        // charged as the same number in AED (selling well below cost).
        $toAed = strtoupper($currencyCode) === 'AED'
            ? 1.0
            : (float) config('montyesim.usd_to_aed', 3.6725);

        return array_map(function ($bundle) use ($markup, $toAed) {
            $costPrice = (float) ($bundle['bundle_price_final'] ?? $bundle['subscriber_price'] ?? 0) * $toAed;
            $bundle['cost_price'] = round($costPrice, 2);
            $bundle['selling_price'] = round($costPrice * $markup, 2);
            return $bundle;
        }, $rawBundles);
    }

    /**
     * Find a specific bundle by code from the bundles list.
     */
    public function findBundle(string $bundleCode, string $countryCode): ?array
    {
        $bundles = $this->getBundles($countryCode);

        foreach ($bundles as $bundle) {
            if (($bundle['bundle_code'] ?? '') === $bundleCode) {
                return $bundle;
            }
        }

        return null;
    }

    /**
     * Reserve a bundle (step 1 of 2-step purchase flow).
     * Locks the eSIM and deducts from wallet.
     */
    public function reserveBundle(string $bundleCode, string $email, string $name, string $orderReference): array
    {
        $result = $this->request('POST', 'Bundles/Reserve', [
            'bundle_code' => $bundleCode,
            'email' => $email,
            'name' => $name,
            'order_reference' => $orderReference,
        ]);

        if ($result['success']) {
            $data = $result['data'];
            return [
                'success' => true,
                'order_id' => $data['order_id'] ?? null,
                'iccid' => $data['iccid'] ?? null,
                'remaining_balance' => $data['remaining_wallet_balance'] ?? null,
                'data' => $data,
            ];
        }

        return [
            'success' => false,
            'error' => $result['error'] ?? 'Failed to reserve bundle',
            'data' => $result['data'] ?? [],
        ];
    }

    /**
     * Complete a reserved bundle (step 2 of 2-step flow).
     * Triggers QR code email from MontyeSIM to customer.
     */
    public function completeBundle(string $orderReference): array
    {
        $result = $this->request('POST', 'Bundles/Complete', [
            'order_reference' => $orderReference,
        ]);

        if ($result['success']) {
            return [
                'success' => true,
                'data' => $result['data'],
            ];
        }

        return [
            'success' => false,
            'error' => $result['error'] ?? 'Failed to complete bundle',
            'data' => $result['data'] ?? [],
        ];
    }

    /**
     * Cancel a reserved bundle. Refunds wallet balance.
     */
    public function cancelBundle(string $orderReference): array
    {
        $result = $this->request('POST', 'Bundles/Cancel', [
            'order_reference' => $orderReference,
        ]);

        if ($result['success']) {
            return [
                'success' => true,
                'data' => $result['data'],
            ];
        }

        return [
            'success' => false,
            'error' => $result['error'] ?? 'Failed to cancel bundle',
            'data' => $result['data'] ?? [],
        ];
    }

    /**
     * Get consumption/usage data for an order.
     */
    public function getConsumption(string $orderId): array
    {
        return $this->request('GET', 'Orders/Consumption', [], [
            'order_id' => $orderId,
        ]);
    }

    /**
     * Resend the QR code email to customer.
     */
    public function resendEmail(string $orderId): array
    {
        return $this->request('POST', 'Orders/ResendEmail', [
            'order_id' => $orderId,
        ]);
    }
}
