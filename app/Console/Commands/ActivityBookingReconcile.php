<?php

namespace App\Console\Commands;

use App\Events\PaymentConfirmed;
use App\Models\ActivityBooking;
use App\Models\NomodTransaction;
use App\Services\NomodService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Close the loop on activity bookings whose customer never made it back to
 * the Nomod success/failure/cancelled redirect.
 *
 * The pay-yourself flow only advances `status` past its DB default
 * ('pending') when the browser returns from Nomod's hosted checkout (see
 * NomodController::handleCallback()). A customer who pays and then closes the
 * tab is charged while the booking sits at 'pending' forever — indistinguishable
 * in the Manager Portal from a booking nobody ever tried to pay for. This
 * command is the safety net: it asks Nomod what actually happened and
 * finishes the job, the same way EsimReconcile/EvisaReconcile already do for
 * their order types.
 *
 * Unlike the live callback — which is only ever invoked after Nomod's own
 * redirect signals the checkout has concluded, so any non-'paid' outcome
 * there is genuinely terminal — this command polls speculatively. It only
 * acts on Nomod's known terminal states (paid / cancelled / expired / failed)
 * and leaves anything else (still 'created', or an unrecognized status) for a
 * later run rather than risk marking an in-flight payment as failed.
 *
 * Idempotent: once a transaction moves off 'created' it no longer matches
 * this command's query, so re-running it cannot double-apply.
 */
class ActivityBookingReconcile extends Command
{
    protected $signature = 'activity:reconcile
                            {--minutes=15 : Only consider checkouts older than this}
                            {--dry-run : Report what would change without touching anything}';

    protected $description = 'Finalize activity bookings abandoned at the Nomod payment step';

    private const TERMINAL_FAILED = ['cancelled', 'expired', 'failed'];

    public function handle(NomodService $nomod): int
    {
        $dry = (bool) $this->option('dry-run');
        $cutoff = now()->subMinutes((int) $this->option('minutes'));

        // Unscoped: this runs on the CLI where there is no tenant context, so
        // CompanyScope would otherwise match zero rows.
        $stuck = NomodTransaction::withoutCompanyScope()
            ->where('booking_type', 'activity')
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
            $bookingId = (int) str_replace('ORDAB', '', $transaction->order_id);
            $booking = ActivityBooking::withoutCompanyScope()->find($bookingId);

            if (!$booking) {
                $rows[] = [$transaction->order_id, $transaction->status, '—', 'booking not found'];
                continue;
            }

            $result = $nomod->getCheckout($transaction->checkout_id);
            if (!($result['success'] ?? false)) {
                $rows[] = [$transaction->order_id, $booking->status, '—', 'provider unreachable'];
                continue;
            }

            $liveStatus = $result['data']['status'] ?? 'unknown';
            $terminalPaid = $liveStatus === 'paid';
            $terminalFailed = in_array($liveStatus, self::TERMINAL_FAILED, true);

            if (!$terminalPaid && !$terminalFailed) {
                $rows[] = [$transaction->order_id, $booking->status, $liveStatus, 'still in progress'];
                continue;
            }

            $newBookingStatus = $terminalPaid ? 'paid' : 'payment_failed';

            if ($dry) {
                $rows[] = [$transaction->order_id, $booking->status, $liveStatus, 'WOULD SET ' . $newBookingStatus];
                $recovered++;
                continue;
            }

            $transaction->update(['status' => $liveStatus, 'response_data' => $result['data']]);
            $booking->update(['status' => $newBookingStatus]);

            if ($newBookingStatus === 'paid') {
                $this->dispatchPaymentConfirmed($booking, $transaction);
            }

            Log::warning('Activity booking recovered by reconcile', [
                'order_id'   => $transaction->order_id,
                'booking_id' => $booking->id,
                'new_status' => $newBookingStatus,
            ]);

            $rows[] = [$transaction->order_id, $newBookingStatus, $liveStatus, 'RECOVERED'];
            $recovered++;
        }

        if ($rows) {
            $this->table(['Order', 'Status', 'Provider status', 'Action'], $rows);
        }

        if ($recovered > 0) {
            $this->warn("{$recovered} booking(s) were stuck at a stale payment state — now recovered.");
        } else {
            $this->info('No abandoned payments found.');
        }

        return self::SUCCESS;
    }

    /**
     * Mirrors NomodController::dispatchPaymentConfirmed() for the activity
     * source type only, so a payment recovered here still generates tenant
     * commission exactly as it would have via the live callback.
     */
    private function dispatchPaymentConfirmed(ActivityBooking $booking, NomodTransaction $transaction): void
    {
        try {
            $companyId = $booking->company_id;
            if (!$companyId) {
                Log::warning('Activity reconcile: booking has no company_id, skipping PaymentConfirmed', [
                    'booking_id' => $booking->id,
                ]);
                return;
            }

            event(new PaymentConfirmed(
                payable:     $booking,
                companyId:   (int) $companyId,
                grossAmount: (float) $transaction->amount,
                currency:    (string) ($transaction->currency ?? 'AED'),
                sourceType:  config('commission.eligible_services')['activity'] ?? 'activity_booking',
                reference:   $transaction->order_id,
            ));
        } catch (\Throwable $e) {
            Log::error('Activity reconcile: failed to dispatch PaymentConfirmed', [
                'order_id' => $transaction->order_id,
                'error'    => $e->getMessage(),
            ]);
        }
    }
}
