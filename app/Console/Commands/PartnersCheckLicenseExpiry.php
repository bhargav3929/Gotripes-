<?php

namespace App\Console\Commands;

use App\Mail\B2bPartnerLicenseExpiringMail;
use App\Models\B2bPartner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * UAE B2B partners must never be allowed to keep trading once their trade
 * license has lapsed — dealing with an unlicensed partner is a fraud/legal
 * exposure for the business, per the client's own stated rationale. Nobody
 * is manually watching expiry dates, so this command is the safety net:
 * warn ~30 days out (business + partner), then hard-disable login the
 * moment the license actually expires.
 *
 * Safe to run repeatedly: every side effect is guarded by a persisted flag
 * (expiry_warning_sent_at for the warning, is_active for the disable —
 * a disabled partner simply drops out of the disable-stage query on the
 * next run, no separate flag needed for that half).
 */
class PartnersCheckLicenseExpiry extends Command
{
    protected $signature = 'partners:check-license-expiry
                            {--dry-run : Report what would change without touching anything}';

    protected $description = 'Warn about and auto-disable B2B partners whose UAE trade license is expiring or has expired';

    private const WARNING_WINDOW_DAYS = 30;

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $today = now()->startOfDay();

        // Unscoped: this runs on the CLI where there is no tenant context, so
        // CompanyScope would otherwise match zero rows.
        $expiringSoon = B2bPartner::withoutCompanyScope()
            ->where('country', B2bPartner::UAE_COUNTRY_NAME)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->whereNotNull('trade_license_expiry_date')
            ->whereBetween('trade_license_expiry_date', [$today, $today->copy()->addDays(self::WARNING_WINDOW_DAYS)])
            ->whereNull('expiry_warning_sent_at')
            ->orderBy('trade_license_expiry_date')
            ->get();

        $expired = B2bPartner::withoutCompanyScope()
            ->where('country', B2bPartner::UAE_COUNTRY_NAME)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->whereNotNull('trade_license_expiry_date')
            ->where('trade_license_expiry_date', '<', $today)
            ->orderBy('trade_license_expiry_date')
            ->get();

        if ($expiringSoon->isEmpty() && $expired->isEmpty()) {
            $this->info('No B2B partner licenses need attention.');
            return self::SUCCESS;
        }

        $rows = [];

        foreach ($expiringSoon as $partner) {
            if ($dry) {
                $rows[] = [$partner->company_name, $partner->trade_license_expiry_date->format('d M Y'), 'WOULD WARN'];
                continue;
            }

            $this->notifyExpiryWarning($partner);
            $partner->forceFill(['expiry_warning_sent_at' => now()])->save();
            $rows[] = [$partner->company_name, $partner->trade_license_expiry_date->format('d M Y'), 'warned'];
        }

        foreach ($expired as $partner) {
            if ($dry) {
                $rows[] = [$partner->company_name, $partner->trade_license_expiry_date->format('d M Y'), 'WOULD DISABLE'];
                continue;
            }

            $this->notifyDisabled($partner);
            $partner->disableForExpiry();
            $rows[] = [$partner->company_name, $partner->trade_license_expiry_date->format('d M Y'), 'DISABLED'];
        }

        $this->table(['Agency', 'License Expiry', 'Action'], $rows);

        return self::SUCCESS;
    }

    private function notifyExpiryWarning(B2bPartner $partner): void
    {
        try {
            $recipients = booking_recipients(service_notification_emails('b2b_partners', $partner->company));
            if (!empty($recipients)) {
                Mail::to($recipients)->send(new \App\Mail\BookingNotificationMail(
                    heading: 'B2B partner trade license expiring soon',
                    intro: 'This UAE B2B partner\'s trade license expires within the next ' . self::WARNING_WINDOW_DAYS . ' days. They have been notified to renew.',
                    rows: [
                        'Agency' => $partner->company_name,
                        'Contact' => $partner->contact_name,
                        'Email' => $partner->email,
                        'Trade License #' => $partner->trade_license_number,
                        'Expiry Date' => $partner->trade_license_expiry_date->format('d M Y'),
                    ],
                    reference: (string) $partner->id,
                    replyToAddress: $partner->email,
                ));
            }
        } catch (\Throwable $e) {
            Log::error('B2B partner expiry-warning business email failed', [
                'partner_id' => $partner->id,
                'error' => $e->getMessage(),
            ]);
        }

        try {
            Mail::to($partner->email)->send(new B2bPartnerLicenseExpiringMail($partner));
        } catch (\Throwable $e) {
            Log::error('B2B partner expiry-warning partner email failed', [
                'partner_id' => $partner->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function notifyDisabled(B2bPartner $partner): void
    {
        try {
            $recipients = booking_recipients(service_notification_emails('b2b_partners', $partner->company));
            if (!empty($recipients)) {
                Mail::to($recipients)->send(new \App\Mail\BookingNotificationMail(
                    heading: 'B2B partner auto-disabled — trade license expired',
                    intro: 'This UAE B2B partner\'s trade license has expired. Their account has been automatically disabled and they can no longer log in.',
                    rows: [
                        'Agency' => $partner->company_name,
                        'Contact' => $partner->contact_name,
                        'Email' => $partner->email,
                        'Trade License #' => $partner->trade_license_number,
                        'Expired On' => $partner->trade_license_expiry_date->format('d M Y'),
                    ],
                    reference: (string) $partner->id,
                    replyToAddress: $partner->email,
                ));
            }
        } catch (\Throwable $e) {
            Log::error('B2B partner auto-disable business email failed', [
                'partner_id' => $partner->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
