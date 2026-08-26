<?php

namespace App\Console\Commands;

use App\Events\PaymentConfirmed;
use App\Models\EsimOrder;
use App\Models\NomodTransaction;
use App\Services\EsimProvisioningService;
use App\Services\NomodService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Close the loop on eSIM orders whose customer never made it back to the
 * Nomod success/failure/cancelled redirect.
 *
 * `esim:reconcile` compares GoTrips against MontyeSIM's own order list, so it
 * can only ever see an order that was actually provisioned. If a customer
 * pays at Nomod and closes the tab before the redirect completes,
 * `assignBundle()` is never called at all — MontyeSIM has no record of the
 * order under any reference, so it stays invisible to that command even with
 * `--all`. This command closes that specific gap by asking NOMOD (not
 * MontyeSIM) what actually happened, using the same NomodTransaction rows
 * and NomodService::getCheckout() the live payment callback already relies
 * on.
 *
 * Unlike the live callback — only ever invoked after Nomod's own redirect
 * signals the checkout concluded, so any non-'paid' outcome there is
 * genuinely terminal — this command polls speculatively. It only acts on
 * Nomod's known terminal states (paid / cancelled / expired / failed) and
 * leaves anything else (still 'created', or an unrecognized value) for a
 * later run rather than risk marking an in-flight payment as failed.
 *
 * Idempotent: once a transaction moves off 'created' it no longer matches
 * this command's query, so re-running it cannot double-apply. Provisioning
 * itself is additionally guarded by EsimProvisioningService's own per-unit
 * already-issued check, so calling it here is as safe as the live callback
 * or the manager portal's "Retry provisioning" button calling it.
 */
class EsimPaymentReconcile extends Command
{
    protected $signature = 'esim:reconcile-payments
                            {--minutes=15 : Only consider checkouts older than this}
                            {--dry-run : Report what would change without touching anything}';

    protected $description = 'Recover eSIM orders paid at Nomod but never confirmed because the customer did not return';

    private const TERMINAL_FAILED = ['cancelled', 'expired', 'failed'];

    public function handle(NomodService $nomod): int
    {
        $dry = (bool) $this->option('dry-run');
        $cutoff = now()->subMinutes((int) $this->option('minutes'));

        // Unscoped: this runs on the CLI where there is no tenant context, so
        // CompanyScope would otherwise match zero rows.
        $stuck = NomodTransaction::withoutCompanyScope()
            ->where('booking_type', 'esim')
            ->where('status', 'created')
            ->where('created_at', '<=', $cutoff)
            ->orderBy('id')
            ->get();

        if ($stuck->isEmpty()) {
            $this->info('Nothing to reconcile.');
            return self::SUCCESS;
        }

        $rows = [];
        $recovered = 0;

        foreach ($stuck as $transaction) {
            $orderId = (int) str_replace('ORDESIM', '', $transaction->order_id);
            $order = EsimOrder::withoutCompanyScope()->find($orderId);

            if (!$order) {
                $rows[] = [$transaction->order_id, $transaction->status, '—', 'order not found'];
                continue;
            }

            $result = $nomod->getCheckout($transaction->checkout_id);
            if (!($result['success'] ?? false)) {
                $rows[] = [$transaction->order_id, $order->payment_status, '—', 'provider unreachable'];
                continue;
            }

            $liveStatus = $result['data']['status'] ?? 'unknown';
            $terminalPaid = $liveStatus === 'paid';
            $terminalFailed = in_array($liveStatus, self::TERMINAL_FAILED, true);

            if (!$terminalPaid && !$terminalFailed) {
                $rows[] = [$transaction->order_id, $order->payment_status, $liveStatus, 'still in progress'];
                continue;
            }

            if ($dry) {
                $newStatus = $terminalPaid ? 'paid' : ($liveStatus === 'cancelled' ? 'cancelled' : 'failed');
                $rows[] = [$transaction->order_id, $order->payment_status, $liveStatus, 'WOULD SET ' . $newStatus];
                $recovered++;
                continue;
            }

            $transaction->update(['status' => $liveStatus, 'response_data' => $result['data']]);

            if ($terminalPaid) {
                $order->update(['payment_status' => 'paid']);

                $this->dispatchPaymentConfirmed($order, $transaction);

                // Provisioning is the actual point of a recovered eSIM payment —
                // the customer needs their eSIM, not just a corrected flag.
                // Reuses the same guarded service the live callback and the
                // manager "Retry provisioning" button call, so double-charge /
                // double-issue protection applies unchanged.
                $provisionResult = (new EsimProvisioningService())->provision($order);

                Log::warning('eSIM order recovered by payment reconcile', [
                    'order_reference' => $transaction->order_id,
                    'esim_order_id'   => $order->id,
                    'provisioned'     => $provisionResult['success'] ?? false,
                    'provision_error' => $provisionResult['error'] ?? null,
                ]);

                $rows[] = [
                    $transaction->order_id,
                    'paid',
                    $liveStatus,
                    ($provisionResult['success'] ?? false)
                        ? 'RECOVERED + PROVISIONED'
                        : 'RECOVERED, provision failed: ' . ($provisionResult['error'] ?? '?'),
                ];
            } else {
                // Nomod's own 'cancelled' outcome (the customer backed out of
                // checkout) is kept distinct from a genuine decline/expiry, same
                // as the live callback in NomodController — both used to collapse
                // to 'failed', making the two indistinguishable in the manager
                // portal.
                $newStatus = $liveStatus === 'cancelled' ? 'cancelled' : 'failed';
                $order->update(['payment_status' => $newStatus]);

                Log::warning('eSIM order payment recovered as ' . $newStatus . ' by reconcile', [
                    'order_reference' => $transaction->order_id,
                    'esim_order_id'   => $order->id,
                ]);

                $rows[] = [$transaction->order_id, $newStatus, $liveStatus, 'RECOVERED (' . $newStatus . ')'];
            }

            $recovered++;
        }

        if ($rows) {
            $this->table(['Order', 'Status', 'Provider status', 'Action'], $rows);
        }

        if ($recovered > 0) {
            $this->warn("{$recovered} order(s) were stuck at a stale payment state — now recovered.");
        } else {
            $this->info('No abandoned payments found.');
        }

        return self::SUCCESS;
    }

    /**
     * Mirrors NomodController::dispatchPaymentConfirmed() for the esim source
     * type only, so a payment recovered here still generates tenant
     * commission exactly as it would have via the live callback.
     */
    private function dispatchPaymentConfirmed(EsimOrder $order, NomodTransaction $transaction): void
    {
        try {
            $companyId = $order->company_id;
            if (!$companyId) {
                Log::warning('Esim payment reconcile: order has no company_id, skipping PaymentConfirmed', [
                    'esim_order_id' => $order->id,
                ]);
                return;
            }

            event(new PaymentConfirmed(
                payable:     $order,
                companyId:   (int) $companyId,
                grossAmount: (float) $transaction->amount,
                currency:    (string) ($transaction->currency ?? 'AED'),
                sourceType:  config('commission.eligible_services')['esim'] ?? 'esim_order',
                reference:   $transaction->order_id,
            ));
        } catch (\Throwable $e) {
            Log::error('Esim payment reconcile: failed to dispatch PaymentConfirmed', [
                'order_id' => $transaction->order_id,
                'error'    => $e->getMessage(),
            ]);
        }
    }
}
