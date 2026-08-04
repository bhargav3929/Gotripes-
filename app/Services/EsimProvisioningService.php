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

        $quantity = $order->unitCount();
        $monty    = new MontyEsimService();

        $issued = [];
        $failed = [];

        for ($n = 1; $n <= $quantity; $n++) {
            // Each eSIM is a separate assignment at the provider, so each needs
            // its own reference. A quantity-1 order keeps the plain reference it
            // has always used, which matters for reconciling existing orders.
            $reference = $quantity > 1
                ? $order->order_reference . '-' . $n
                : $order->order_reference;

            $unit = $order->units()->firstOrCreate(
                ['unit_number' => $n],
                ['reservation_status' => 'pending'],
            );

            // A retry should not re-buy a unit that already came through.
            if ($unit->monty_order_id) {
                $issued[] = $unit;
                continue;
            }

            try {
                $assignment = $monty->assignBundle(
                    $order->bundle_code,
                    $order->customer_email,
                    $order->customer_name,
                    $reference
                );
            } catch (\Exception $e) {
                $unit->update([
                    'reservation_status' => 'error',
                    'monty_response'     => $this->failureRecord($e->getMessage()),
                ]);
                Log::error('MontyeSIM API error while provisioning', [
                    'esim_order_id' => $order->id,
                    'unit_number'   => $n,
                    'error'         => $e->getMessage(),
                ]);
                $failed[$n] = $e->getMessage();
                continue;
            }

            if ($assignment['success'] ?? false) {
                $unit->update([
                    'monty_order_id'     => $assignment['order_id'] ?? null,
                    'monty_iccid'        => $assignment['iccid'] ?? null,
                    'reservation_status' => 'completed',
                    'monty_response'     => $assignment['data'] ?? $assignment,
                ]);
                $issued[] = $unit;
                continue;
            }

            $error = $assignment['error'] ?? 'Unknown';

            // Keep the reason ON the record, not just in the log. The provider
            // returns an empty body on some failures, and storing that verbatim
            // (as this did) left the manager portal showing a failed order with
            // no explanation — which is what happened to orders 37–39 when the
            // reseller wallet ran dry.
            $unit->update([
                'reservation_status' => 'assign_failed',
                'monty_response'     => $this->failureRecord($error, $assignment),
            ]);
            $failed[$n] = $error;
        }

        // Mirror the first issued unit onto the parent. Every existing guard,
        // manager view and report reads these columns, and for a quantity-1
        // order the result is byte-for-byte what it was before.
        $first = $issued[0] ?? null;

        if ($first) {
            $order->update([
                'monty_order_id'     => $first->monty_order_id,
                'monty_iccid'        => $first->monty_iccid,
                'reservation_status' => $failed ? 'assign_failed' : 'completed',
                'monty_response'     => $failed
                    ? $this->failureRecord(
                        'Issued ' . count($issued) . ' of ' . $quantity . ' eSIMs. Failed units: '
                        . implode('; ', array_map(fn($k, $v) => "#$k: $v", array_keys($failed), $failed))
                      )
                    : $first->monty_response,
            ]);
        } else {
            $error = $failed ? reset($failed) : 'Unknown';
            $order->update([
                'reservation_status' => 'assign_failed',
                'monty_response'     => $this->failureRecord($error),
            ]);
        }

        if (empty($failed)) {
            Log::info('eSIM order provisioned successfully', [
                'esim_order_id'   => $order->id,
                'order_reference' => $order->order_reference,
                'quantity'        => $quantity,
                'monty_order_id'  => $first?->monty_order_id,
            ]);

            $this->sendQrEmail($order);

            return ['success' => true];
        }

        // The customer has paid but is short of eSIMs — this needs a human.
        Log::error('MontyeSIM assign failed after payment', [
            'esim_order_id'   => $order->id,
            'order_reference' => $order->order_reference,
            'issued'          => count($issued),
            'quantity'        => $quantity,
            'failures'        => $failed,
        ]);

        // Anything that did come through is still worth sending — a group of 20
        // that got 18 eSIMs should receive those 18 now, not after the retry.
        if ($issued) {
            $this->sendQrEmail($order);
        }

        $summary = count($issued) . ' of ' . $quantity . ' eSIMs issued. ' . implode('; ', $failed);
        $this->notifyFailure($order, $summary);

        return ['success' => false, 'error' => $summary];
    }

    /**
     * Send the customer their eSIM, from GoTrips.
     *
     * MontyeSIM emails its own QR on assign, but we have no way to confirm it
     * arrived and it carries their branding. The installation credentials live
     * on the order detail endpoint (not on the assign response), so we fetch
     * them and send our own copy. A failure here is logged and surfaced but
     * never fails the provisioning: the eSIM exists either way, and the QR can
     * be resent from the manager portal.
     */
    public function sendQrEmail(EsimOrder $order): array
    {
        if (!$order->monty_order_id) {
            return ['success' => false, 'error' => 'Order has not been provisioned yet.'];
        }
        if (!$order->customer_email) {
            return ['success' => false, 'error' => 'Order has no customer email.'];
        }

        try {
            $monty = new MontyEsimService();

            // Activation details are only on the order detail endpoint, not on
            // the assign response, so each issued unit is read back here. They
            // are cached on the unit so a resend does not re-hit the provider
            // once per eSIM.
            $units = $order->units()->whereNotNull('monty_order_id')->get();

            // Orders placed before quantities existed have no unit rows; treat
            // the parent as the single unit so resend keeps working for them.
            if ($units->isEmpty()) {
                $units = collect([
                    $order->units()->firstOrCreate(
                        ['unit_number' => 1],
                        [
                            'monty_order_id'     => $order->monty_order_id,
                            'monty_iccid'        => $order->monty_iccid,
                            'reservation_status' => 'completed',
                        ],
                    ),
                ]);
            }

            foreach ($units as $unit) {
                if ($unit->activation_code || ($unit->smdp_address && $unit->matching_id)) {
                    continue;
                }

                $detail = $monty->getOrder($unit->monty_order_id);

                if (!($detail['success'] ?? false)) {
                    Log::error('Could not read eSIM activation details for the QR email', [
                        'esim_order_id' => $order->id,
                        'unit_number'   => $unit->unit_number,
                        'error'         => $detail['error'] ?? null,
                    ]);
                    continue;
                }

                $d = $detail['data'];
                $unit->update([
                    'activation_code' => $d['activation_code'] ?? null,
                    'smdp_address'    => $d['smdp_address'] ?? null,
                    'matching_id'     => $d['matching_id'] ?? null,
                ]);
            }

            $units = $units->fresh();

            // Nothing readable came back for any unit — sending an email with no
            // QR and no manual details would only generate a support call.
            if ($units->every(fn($u) => $u->lpaString() === null)) {
                return ['success' => false, 'error' => 'Could not read activation details'];
            }

            // One email, every QR — a tour operator gets all of them together.
            Mail::to($order->customer_email)->send(new \App\Mail\EsimQrMail(
                order: $order,
                units: $units,
            ));

            $order->forceFill(['qr_sent_at' => now()])->save();

            Log::info('eSIM QR emailed to customer', [
                'esim_order_id' => $order->id,
                'to'            => $order->customer_email,
                'qr_count'      => $units->count(),
            ]);

            return ['success' => true];
        } catch (\Throwable $e) {
            Log::error('eSIM QR email failed', [
                'esim_order_id' => $order->id,
                'error'         => $e->getMessage(),
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
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
