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
        $amount = number_format((float) $params['amount'], 2, '.', '');

        $payload = [
            'amount' => $amount,
            'currency' => $params['currency'] ?? 'AED',
            'reference_id' => $params['order_id'],
            'success_url' => config('nomod.success_url') . '?reference_id=' . $params['order_id'],
            'failure_url' => config('nomod.failure_url') . '?reference_id=' . $params['order_id'],
            'cancelled_url' => config('nomod.cancelled_url') . '?reference_id=' . $params['order_id'],
            'items' => $params['items'] ?? [
                [
                    'item_id' => $params['order_id'],
                    'name' => $params['description'] ?? 'Payment',
                    'quantity' => 1,
                    'unit_amount' => $amount,
                ],
            ],
        ];

        if (!empty($params['customer'])) {
            $c = $params['customer'];
            $nameParts = explode(' ', trim($c['name'] ?? ''), 2);
            $payload['customer'] = [
                'first_name' => $nameParts[0] ?: 'Customer',
                'last_name' => $nameParts[1] ?? $nameParts[0] ?: 'Customer',
                'email' => $c['email'] ?? '',
            ];
            $phone = $this->normalizePhone($c['phone'] ?? $c['phone_number'] ?? null);
            if (!empty($phone)) {
                $payload['customer']['phone_number'] = $phone;
            }
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

    /**
     * Normalize a phone number to E.164 (e.g. "+971501234567") as required by Nomod.
     * Strips spaces, dashes, brackets and any other formatting and prefixes "+".
     * The form is responsible for sending a valid number; this just cleans formatting.
     */
    private function normalizePhone($phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $phone);

        if ($digits === '') {
            return null;
        }

        return '+' . $digits;
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
