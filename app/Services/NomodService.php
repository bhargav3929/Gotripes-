<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NomodService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('nomod.api_key');
        $this->baseUrl = rtrim(config('nomod.base_url'), '/');
    }

    public function createCheckout(array $params): array
    {
        $payload = [
            'amount' => (int) round($params['amount'] * 100), // Nomod expects amount in minor units (fils)
            'currency' => $params['currency'] ?? 'AED',
            'reference_id' => $params['order_id'],
            'success_url' => config('nomod.success_url') . '?reference_id=' . $params['order_id'],
            'failure_url' => config('nomod.failure_url') . '?reference_id=' . $params['order_id'],
            'cancel_url' => config('nomod.cancelled_url') . '?reference_id=' . $params['order_id'],
        ];

        if (!empty($params['description'])) {
            $payload['description'] = $params['description'];
        }

        if (!empty($params['customer'])) {
            $payload['customer'] = $params['customer'];
        }

        if (!empty($params['items'])) {
            $payload['items'] = $params['items'];
        }

        if (!empty($params['metadata'])) {
            $payload['metadata'] = $params['metadata'];
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-API-KEY' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/v1/checkout", $payload);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'checkout_id' => $data['id'] ?? null,
                    'checkout_url' => $data['url'] ?? null,
                    'status' => $data['status'] ?? null,
                    'data' => $data,
                ];
            }

            Log::error('Nomod createCheckout failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'order_id' => $params['order_id'],
            ]);

            return [
                'success' => false,
                'error' => 'Payment gateway error: ' . ($response->json('message') ?? $response->body()),
            ];

        } catch (\Exception $e) {
            Log::error('Nomod createCheckout exception', [
                'message' => $e->getMessage(),
                'order_id' => $params['order_id'],
            ]);

            return [
                'success' => false,
                'error' => 'Payment gateway unavailable. Please try again.',
            ];
        }
    }

    public function getCheckout(string $checkoutId): array
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-API-KEY' => $this->apiKey,
                ])
                ->get("{$this->baseUrl}/v1/checkout/{$checkoutId}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error('Nomod getCheckout failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'checkout_id' => $checkoutId,
            ]);

            return [
                'success' => false,
                'error' => 'Failed to verify payment: ' . ($response->json('message') ?? $response->body()),
            ];

        } catch (\Exception $e) {
            Log::error('Nomod getCheckout exception', [
                'message' => $e->getMessage(),
                'checkout_id' => $checkoutId,
            ]);

            return [
                'success' => false,
                'error' => 'Payment verification unavailable.',
            ];
        }
    }
}
