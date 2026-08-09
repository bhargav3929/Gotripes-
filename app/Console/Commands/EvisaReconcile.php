<?php

namespace App\Console\Commands;

use App\Mail\BookingNotificationMail;
use App\Mail\EvisaCustomerMail;
use App\Models\FluxirVisaApplication;
use App\Services\FluxirService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Close the loop on e-visa applications whose customer never made it back to
 * the success URL.
 *
 * The pay-now flow only calls finalize-checkout when the browser returns from
 * Stripe. A customer who pays and then closes the tab is charged while the
 * application sits at ReadyForPayment forever, with nobody notified — the
 * failure is completely silent. This command is the safety net: it asks Fluxir
 * what actually happened and finishes the job.
 *
 * It also relays terminal decisions (approved / rejected) to the traveller,
 * which nothing else does.
 *
 * Safe to run repeatedly: every side effect is guarded by a persisted flag
 * (status, notified_at, customer_notified_at).
 */
class EvisaReconcile extends Command
{
    protected $signature = 'evisa:reconcile
                            {--minutes=15 : Only consider applications older than this}
                            {--dry-run : Report what would change without touching anything}';

    protected $description = 'Finalize paid e-visa applications abandoned at the payment step and relay status changes';

    /** Fluxir states that mean the application reached a final decision. */
    private const APPROVED_STATES = ['ApplicationConfirmed', 'InitialApprove'];
    private const REJECTED_STATES = ['ApplicationRejected', 'ReviewFailed', 'Aborted'];

    public function handle(FluxirService $fluxir): int
    {
        if (!$fluxir->isConfigured()) {
            $this->error('Fluxir is not configured — set FLUXIR_CLIENT_SECRET and friends.');
            return self::FAILURE;
        }

        $dry = (bool) $this->option('dry-run');
        $cutoff = now()->subMinutes((int) $this->option('minutes'));

        // Unscoped: this runs on the CLI where there is no tenant context, so
        // CompanyScope would otherwise match zero rows.
        $pending = FluxirVisaApplication::withoutCompanyScope()
            ->whereIn('status', ['awaiting_payment', 'created', 'paid', 'submit_failed'])
            ->where('created_at', '<=', $cutoff)
            ->whereNotNull('fluxir_service_application_id')
            ->orderBy('id')
            ->get();

        // In-flight applications that already went through, so we can relay the
        // provider's decision to the traveller.
        $submitted = FluxirVisaApplication::withoutCompanyScope()
            ->where('status', 'submitted')
            ->whereNotIn('state', array_merge(self::APPROVED_STATES, self::REJECTED_STATES))
            ->whereNotNull('fluxir_service_application_id')
            ->orderBy('id')
            ->get();

        if ($pending->isEmpty() && $submitted->isEmpty()) {
            $this->info('Nothing to reconcile.');
            return self::SUCCESS;
        }

        $rows = [];
        $recovered = 0;

        foreach ($pending as $record) {
            if (!$record->fluxir_trip_id) {
                $rows[] = [$record->order_id, $record->status, '—', 'no trip on record'];
                continue;
            }

            $live = $fluxir->getServiceApplication($record->fluxir_trip_id, $record->fluxir_service_application_id);
            if (!($live['success'] ?? false)) {
                $rows[] = [$record->order_id, $record->status, '—', 'provider unreachable'];
                continue;
            }

            $state = $live['data']['state'] ?? null;

            // Fluxir considers it paid (settled checkout, or already moved past
            // the payment gate) while our record never got finalized: recover it.
            if (!$fluxir->applicationIsPaid($live['data'])) {
                $rows[] = [$record->order_id, $record->status, $state ?: '—', 'still unpaid'];
                continue;
            }

            if ($dry) {
                $rows[] = [$record->order_id, $record->status, $state ?: '—', 'WOULD RECOVER'];
                $recovered++;
                continue;
            }

            // Pay-now applications still need the finalize call; credit-flow ones
            // (already past ReadyForPayment) do not.
            if ($record->fluxir_trip_id && $state === 'ReadyForPayment') {
                $final = $fluxir->finalizeCheckout($record->fluxir_trip_id);
                $state = $final['data']['serviceApplications'][0]['state'] ?? $state;
            }

            $record->update([
                'is_paid' => true,
                'status'  => 'submitted',
                'state'   => $state ?: $record->state,
            ]);

            $this->notifyBusiness($record, 'Recovered e-Visa application — PAID',
                'This application was paid but never completed because the customer did not return '
                . 'from the payment page. It has now been finalized automatically.');
            $this->notifyCustomer($record, 'submitted');

            $rows[] = [$record->order_id, 'submitted', $state ?: '—', 'RECOVERED'];
            $recovered++;

            Log::warning('e-Visa application recovered by reconcile', [
                'order_id' => $record->order_id,
                'state'    => $state,
            ]);
        }

        // Relay approvals / rejections to the traveller.
        foreach ($submitted as $record) {
            if (!$record->fluxir_trip_id) {
                continue;
            }

            $live = $fluxir->getServiceApplication($record->fluxir_trip_id, $record->fluxir_service_application_id);
            if (!($live['success'] ?? false)) {
                continue;
            }
            $state = $live['data']['state'] ?? null;
            if (!$state || $state === $record->state) {
                continue;
            }

            $stage = in_array($state, self::APPROVED_STATES, true) ? 'approved'
                : (in_array($state, self::REJECTED_STATES, true) ? 'rejected' : null);

            if ($dry) {
                $rows[] = [$record->order_id, 'submitted', $state, $stage ? 'WOULD EMAIL ' . $stage : 'state changed'];
                continue;
            }

            $record->update(['state' => $state]);

            if ($stage) {
                // A decision is a new event, so send regardless of the
                // submission-time guard.
                $this->notifyCustomer($record, $stage, force: true);
                if ($stage === 'rejected') {
                    $this->notifyBusiness($record, 'e-Visa REJECTED by provider',
                        'The provider rejected this application. Contact the customer about next steps '
                        . 'and any refund due.', force: true);
                }
            }

            $rows[] = [$record->order_id, 'submitted', $state, $stage ? 'emailed ' . $stage : 'state updated'];
        }

        if ($rows) {
            $this->table(['Order', 'Status', 'Provider state', 'Action'], $rows);
        }

        if ($recovered > 0) {
            $this->warn("{$recovered} application(s) were paid but never finalized — now recovered.");
        } else {
            $this->info('No abandoned payments found.');
        }

        return self::SUCCESS;
    }

    private function notifyBusiness(FluxirVisaApplication $record, string $heading, string $intro, bool $force = false): void
    {
        if (!$force && $record->notified_at) {
            return;
        }
        try {
            $recipients = booking_recipients(service_notification_emails('visa', $record->company));
            if (empty($recipients)) {
                return;
            }
            Mail::to($recipients)->send(new BookingNotificationMail(
                heading: $heading,
                intro: $intro,
                rows: array_filter([
                    'Applicant'   => trim(($record->first_name ?? '') . ' ' . ($record->last_name ?? '')),
                    'Email'       => $record->email,
                    'Phone'       => $record->phone,
                    'Nationality' => $record->nationality,
                    'Destination' => $record->destination_code,
                    'Arrival'     => optional($record->arrival_date)->format('d M Y'),
                    'Amount'      => trim(($record->currency ?? 'USD') . ' ' . $record->amount),
                    'State'       => $record->state,
                ]),
                reference: $record->order_id,
                replyToAddress: $record->email,
            ));
            $record->forceFill(['notified_at' => now()])->save();
        } catch (\Throwable $e) {
            Log::error('e-Visa reconcile business notification failed', [
                'order_id' => $record->order_id,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    private function notifyCustomer(FluxirVisaApplication $record, string $stage, bool $force = false): void
    {
        if (!$record->email || (!$force && $record->customer_notified_at)) {
            return;
        }
        try {
            Mail::to($record->email)->send(new EvisaCustomerMail($record, $stage));
            if ($stage === 'submitted') {
                $record->forceFill(['customer_notified_at' => now()])->save();
            }
        } catch (\Throwable $e) {
            Log::error('e-Visa reconcile customer email failed', [
                'order_id' => $record->order_id,
                'stage'    => $stage,
                'error'    => $e->getMessage(),
            ]);
        }
    }
}
