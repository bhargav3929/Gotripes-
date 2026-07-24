<?php

namespace App\Console\Commands;

use App\Models\EsimOrder;
use App\Services\MontyEsimService;
use Illuminate\Console\Command;

/**
 * Compare GoTrips eSIM orders against what MontyeSIM actually issued.
 *
 * Read-only: it never assigns a bundle or spends the wallet. Written after
 * orders 37-39 were taken as paid but never provisioned ("Insufficient
 * Balance"), which nobody noticed because the two systems were only ever
 * compared by hand.
 */
class EsimReconcile extends Command
{
    protected $signature = 'esim:reconcile {--all : Include unpaid and pending orders}';

    protected $description = 'Compare local eSIM orders with the MontyeSIM reseller portal';

    public function handle(MontyEsimService $monty): int
    {
        $this->info('Fetching orders from MontyeSIM…');

        $remote = $monty->listOrders();

        if (!$remote['success']) {
            $this->error('Could not reach MontyeSIM: ' . ($remote['error'] ?? 'unknown'));
            return self::FAILURE;
        }

        $this->line("MontyeSIM reports <options=bold>{$remote['total']}</> order(s) on this reseller account.");

        // Index the provider's orders by both id and our reference so either side
        // of the match works, whichever field their payload happens to carry.
        $remoteById = [];
        foreach ($remote['orders'] as $o) {
            foreach (['order_id', 'id', 'order_reference'] as $key) {
                if (!empty($o[$key])) {
                    $remoteById[(string) $o[$key]] = $o;
                }
            }
        }

        $local = EsimOrder::withoutCompanyScope()
            ->when(!$this->option('all'), fn ($q) => $q->where('payment_status', 'paid'))
            ->orderBy('id')
            ->get();

        if ($local->isEmpty()) {
            $this->warn('No local orders to compare.');
            return self::SUCCESS;
        }

        $rows = [];
        $mismatches = 0;

        foreach ($local as $order) {
            $matched = $order->monty_order_id && isset($remoteById[(string) $order->monty_order_id]);

            if ($order->payment_status === 'paid' && !$order->monty_order_id) {
                $verdict = 'PAID, NOT ISSUED';
                $mismatches++;
            } elseif ($order->monty_order_id && !$matched) {
                $verdict = 'NOT IN PORTAL';
                $mismatches++;
            } elseif ($matched) {
                $verdict = 'in sync';
            } else {
                $verdict = 'not paid yet';
            }

            $rows[] = [
                $order->order_reference ?: ('#' . $order->id),
                $order->customer_email,
                $order->payment_status,
                $order->reservation_status,
                $order->monty_order_id ?: '—',
                $verdict,
            ];
        }

        $this->table(
            ['Reference', 'Customer', 'Payment', 'Provisioning', 'Monty order', 'Verdict'],
            $rows
        );

        if ($mismatches > 0) {
            $this->error("{$mismatches} order(s) are out of sync — customers may have paid without receiving an eSIM.");
            $this->line('Open each in the manager portal and use "Retry provisioning".');
            return self::FAILURE;
        }

        $this->info('All compared orders are in sync with MontyeSIM.');
        return self::SUCCESS;
    }
}
