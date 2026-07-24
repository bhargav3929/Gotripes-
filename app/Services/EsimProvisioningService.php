<?php

namespace App\Services;

use App\Mail\BookingNotificationMail;
use App\Models\EsimOrder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Provisions a paid eSIM order with MontyeSIM.
 *
 * Shared by the Nomod payment callback and the manager retry action so both
 * paths apply the same guards — most importantly the one that stops an order
 * being assigned (and the wallet charged) twice.
 */
class EsimProvisioningService
{
    /**
     * Assign the bundle for a paid order and record the outcome.
     *
     * Reuses the order's existing order_reference, so a retry continues the
     * same order rather than creating a second one.
     *
     * @return array{success: bool, error?: string, skipped?: bool}
     */
    public function provision(EsimOrder $order): array
    {
        if ($order->payment_status !== 'paid') {
            return ['success' => false, 'error' => 'Order is not paid.', 'skipped' => true];
        }

        // Already provisioned. Assigning again would charge the wallet a second
        // time and issue the customer a duplicate eSIM, so refuse outright —
        // a missing QR email is a resend problem, not a provisioning one.
        if ($order->monty_order_id) {
            return ['success' => false, 'error' => 'Order is already provisioned.', 'skipped' => true];
        }

        try {
            $assignment = (new MontyEsimService())->assignBundle(
                $order->bundle_code,
                $order->customer_email,
                $order->customer_name,
                $order->order_reference
            );
        } catch (\Exception $e) {
            $order->update([
                'reservation_status' => 'error',
                'monty_response' => $this->failureRecord($e->getMessage()),
            ]);
            Log::error('MontyeSIM API error while provisioning', [
                'esim_order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            $this->notifyFailure($order, $e->getMessage());

            return ['success' => false, 'error' => $e->getMessage()];
        }

        if ($assignment['success'] ?? false) {
            $order->update([
                'monty_order_id' => $assignment['order_id'] ?? null,
                'monty_iccid' => $assignment['iccid'] ?? null,
                'reservation_status' => 'completed',
                'monty_response' => $assignment['data'] ?? $assignment,
            ]);

            Log::info('eSIM order provisioned successfully', [
                'esim_order_id' => $order->id,
                'order_reference' => $order->order_reference,
                'monty_order_id' => $assignment['order_id'] ?? null,
            ]);

            return ['success' => true];
        }

        $error = $assignment['error'] ?? 'Unknown';

        // Keep the reason ON the order, not just in the log. The provider
        // returns an empty body on some failures, and storing that verbatim
        // (as this did) left the manager portal showing a failed order with no
        // explanation — which is exactly what happened to orders 37–39 when the
        // reseller wallet ran dry.
        $order->update([
            'reservation_status' => 'assign_failed',
            'monty_response' => $this->failureRecord($error, $assignment),
        ]);

        // The customer has paid but has no eSIM — this needs a human.
        Log::error('MontyeSIM assign failed after payment', [
            'esim_order_id' => $order->id,
            'order_reference' => $order->order_reference,
            'error' => $error,
        ]);

        $this->notifyFailure($order, $error);

        return ['success' => false, 'error' => $error];
    }

    /**
     * A readable record of why provisioning failed, stored on the order so the
     * manager portal can show it without anyone opening a log file.
     *
     * @param  array<string, mixed>  $assignment  Raw result from the assign call.
     * @return array<string, mixed>
     */
    private function failureRecord(string $error, array $assignment = []): array
    {
        return array_filter([
            'failed'            => true,
            'error'             => $error,
            'failed_at'         => now()->toIso8601String(),
            'provider_status'   => $assignment['status'] ?? null,
            'provider_response' => $assignment['data'] ?? null,
        ], fn ($v) => $v !== null && $v !== []);
    }

    /**
     * Alert the business team that a paid order was left unprovisioned.
     * Never lets a mail failure mask the underlying provisioning failure.
     */
    private function notifyFailure(EsimOrder $order, string $error): void
    {
        try {
            $recipients = booking_recipients(
                service_notification_emails('esim', $order->company)
            );

            if (empty($recipients)) {
                return;
            }

            Mail::to($recipients)->send(new BookingNotificationMail(
                heading: 'eSIM provisioning FAILED — action required',
                intro: 'A customer has paid but their eSIM was not issued and no QR code was sent. '
                     . 'Open the order in the manager portal and use "Retry provisioning".',
                rows: [
                    'Customer' => $order->customer_name,
                    'Email'    => $order->customer_email,
                    'Phone'    => $order->customer_phone,
                    'Bundle'   => $order->bundle_name,
                    'Country'  => $order->country_name,
                    'Paid'     => trim(($order->currency ?? '') . ' ' . $order->selling_price),
                    'Error'    => $error,
                ],
                reference: $order->order_reference,
                replyToAddress: $order->customer_email,
            ));
        } catch (\Throwable $e) {
            Log::error('eSIM provisioning-failure notification could not be sent', [
                'esim_order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
