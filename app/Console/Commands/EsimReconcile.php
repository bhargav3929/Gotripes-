<?php

namespace App\Console\Commands;

use App\Models\EsimOrder;
use App\Services\EsimProvisioningService;
use App\Services\MontyEsimService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Reconcile GoTrips eSIM orders against what MontyeSIM actually issued.
 *
 * Written after orders 37-39 were taken as paid but never provisioned
 * ("Insufficient Balance"), which nobody noticed because the two systems were
 * only ever compared by hand. It used to only report; reporting does not help
 * at 2am, so it now repairs what it safely can:
 *
 *   - paid but never assigned      -> provision (guarded against double-charge)
 *   - assigned but no QR emailed   -> send the QR
 *   - issued upstream, unknown here -> adopt the provider's order onto ours
 *
 * The last case is real: ORDESIM40 was Successful and Active at MontyeSIM
 * while our row still said "unpaid, pending" — the wallet had been charged for
 * an eSIM our own records did not know existed.
 *
 * Every repair is idempotent. --dry-run reports without touching anything.
 */
class EsimReconcile extends Command
{
    protected $signature = 'esim:reconcile
                            {--all : Include unpaid and pending orders}
                            {--dry-run : Report differences without repairing them}';

    protected $description = 'Reconcile eSIM orders with MontyeSIM and repair what is safely repairable';

    public function handle(MontyEsimService $monty): int
    {
        $dry = (bool) $this->option('dry-run');

        $this->info('Fetching orders from MontyeSIM…');
        $remote = $monty->listOrders();

        if (!$remote['success']) {
            $this->error('Could not reach MontyeSIM: ' . ($remote['error'] ?? 'unknown'));
            return self::FAILURE;
        }

        $this->line("MontyeSIM reports <options=bold>{$remote['total']}</> order(s) on this reseller account.");

        // Index the provider's orders by both id and our reference so either
        // side of the match works, whichever field their payload carries.
        $remoteById = [];
        $remoteByReference = [];
        foreach ($remote['orders'] as $o) {
            foreach (['order_id', 'id'] as $key) {
                if (!empty($o[$key])) {
                    $remoteById[(string) $o[$key]] = $o;
                }
            }
            if (!empty($o['order_reference'])) {
                $remoteByReference[(string) $o['order_reference']] = $o;
            }
        }

        $local = EsimOrder::withoutCompanyScope()
            ->when(!$this->option('all'), fn ($q) => $q->where(function ($w) {
                // Anything that could need repair: paid orders, and any order
                // the provider might have issued behind our back.
                $w->where('payment_status', 'paid')->orWhereNotNull('monty_order_id');
            }))
            ->orderBy('id')
            ->get();

        // Orders the provider issued against a reference we have no local row
        // for at all — money spent with nothing to show for it locally.
        $localReferences = EsimOrder::withoutCompanyScope()->pluck('order_reference')->all();
        $orphans = array_diff(array_keys($remoteByReference), $localReferences);

        $rows = [];
        $repaired = 0;
        $problems = 0;
        $provisioner = new EsimProvisioningService();

        foreach ($local as $order) {
            $matched = $order->monty_order_id && isset($remoteById[(string) $order->monty_order_id]);
            $upstream = $remoteByReference[(string) $order->order_reference] ?? null;

            // 1. The provider issued this order but our row never recorded it.
            if (!$order->monty_order_id && $upstream) {
                $problems++;
                if ($dry) {
                    $rows[] = [$order->order_reference, $order->payment_status, 'ISSUED UPSTREAM', 'would adopt provider order'];
                    continue;
                }
                $order->update([
                    'monty_order_id'     => $upstream['order_id'] ?? null,
                    'monty_iccid'        => $upstream['iccid'] ?? null,
                    'reservation_status' => 'completed',
                    'payment_status'     => $order->payment_status === 'paid' ? 'paid' : $order->payment_status,
                    'monty_response'     => $upstream,
                ]);
                Log::warning('eSIM order adopted from provider during reconcile', [
                    'esim_order_id'  => $order->id,
                    'monty_order_id' => $upstream['order_id'] ?? null,
                ]);
                $repaired++;
                $rows[] = [$order->order_reference, $order->payment_status, 'ISSUED UPSTREAM', 'ADOPTED'];
                $order->refresh();
            }

            // 2. Paid, but not (fully) provisioned. Judged per unit — the same
            // completeness check EsimProvisioningService::provision() itself
            // uses — rather than the parent's mirrored monty_order_id, which is
            // set as soon as the FIRST unit succeeds. Guarding on that column
            // alone made a half-provisioned group order (e.g. 17-of-20) invisible
            // to this reconciler: it was neither "never assigned" nor complete.
            if ($order->payment_status === 'paid') {
                $quantity = $order->unitCount();
                $issuedUnits = $order->units()->whereNotNull('monty_order_id')->count();
                // Orders placed before quantities existed have no unit rows at
                // all — fall back to the parent column for them, same as the
                // provisioning service does.
                $legacyDone = $issuedUnits === 0 && $order->monty_order_id;
                $fullyProvisioned = $legacyDone || ($quantity > 0 && $issuedUnits >= $quantity);

                if (!$fullyProvisioned) {
                    $problems++;
                    $label = $issuedUnits > 0 ? "NOT ISSUED ({$issuedUnits}/{$quantity})" : 'NOT ISSUED';
                    if ($dry) {
                        $rows[] = [$order->order_reference, 'paid', $label, 'would provision'];
                        continue;
                    }
                    $result = $provisioner->provision($order);
                    if ($result['success'] ?? false) {
                        $repaired++;
                        $rows[] = [$order->order_reference, 'paid', $label, 'PROVISIONED'];
                        $order->refresh();
                    } else {
                        $rows[] = [$order->order_reference, 'paid', $label, 'failed: ' . ($result['error'] ?? '?')];
                        continue;
                    }
                }
            }

            // 3. Issued, but the customer was never sent their QR by us.
            if ($order->monty_order_id && !$order->qr_sent_at && $order->customer_email) {
                if ($dry) {
                    $rows[] = [$order->order_reference, $order->payment_status, 'no QR sent', 'would email QR'];
                    continue;
                }
                $sent = $provisioner->sendQrEmail($order);
                $rows[] = [
                    $order->order_reference,
                    $order->payment_status,
                    'no QR sent',
                    ($sent['success'] ?? false) ? 'QR EMAILED' : 'QR failed: ' . ($sent['error'] ?? '?'),
                ];
                if ($sent['success'] ?? false) {
                    $repaired++;
                }
                continue;
            }

            // 4. We think it is issued, the provider disagrees.
            if ($order->monty_order_id && !$matched && !$upstream) {
                $problems++;
                $rows[] = [$order->order_reference, $order->payment_status, 'NOT IN PORTAL', 'needs a human'];
            }
        }

        foreach ($orphans as $ref) {
            $problems++;
            $o = $remoteByReference[$ref];
            $rows[] = [$ref, '—', 'NO LOCAL ORDER', 'provider issued ' . ($o['iccid'] ?? '?') . ' — needs a human'];
        }

        if ($rows) {
            $this->table(['Reference', 'Payment', 'Problem', 'Action'], $rows);
        }

        // The balance is what decides whether tomorrow's orders can be filled
        // at all, so surface it every run.
        $balance = $monty->getWalletBalance();
        if ($balance !== null) {
            $this->newLine();
            $this->line(sprintf('Reseller wallet balance: <options=bold>%.2f</>', $balance));
            if ($balance < 50) {
                $this->error('WALLET IS LOW — new eSIM sales will start failing once it is exhausted. Top up now.');
            }
        }

        if ($problems === 0) {
            $this->info('All compared orders are in sync with MontyeSIM.');
            return self::SUCCESS;
        }

        $this->newLine();
        $this->warn("{$problems} problem(s) found, {$repaired} repaired automatically.");

        if ($repaired < $problems) {
            $this->line('Anything left needs a human — open it in the manager portal.');
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
