<?php

namespace App\Console\Commands;

use App\Mail\AgentLicenseExpiringMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Same rationale and structure as partners:check-license-expiry, applied to
 * `company_agent` Users: warn ~30 days before trade_license_expiry_date,
 * then hard-disable login the moment it actually expires. Nobody is
 * manually watching agent expiry dates, so this is the safety net.
 *
 * Safe to run repeatedly: every side effect is guarded by a persisted flag
 * (expiry_warning_sent_at for the warning, is_active for the disable — a
 * disabled agent simply drops out of the disable-stage query next run).
 */
class AgentsCheckLicenseExpiry extends Command
{
    protected $signature = 'agents:check-license-expiry
                            {--dry-run : Report what would change without touching anything}';

    protected $description = 'Warn about and auto-disable agents whose trade license is expiring or has expired';

    private const WARNING_WINDOW_DAYS = 30;

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $today = now()->startOfDay();

        // The users table has no tenant scope, so this naturally runs across
        // every company without needing to bypass anything.
        $expiringSoon = User::where('role', 'company_agent')
            ->where('is_active', true)
            ->whereNotNull('trade_license_expiry_date')
            ->whereBetween('trade_license_expiry_date', [$today, $today->copy()->addDays(self::WARNING_WINDOW_DAYS)])
            ->whereNull('expiry_warning_sent_at')
            ->orderBy('trade_license_expiry_date')
            ->get();

        $expired = User::where('role', 'company_agent')
            ->where('is_active', true)
            ->whereNotNull('trade_license_expiry_date')
            ->where('trade_license_expiry_date', '<', $today)
            ->orderBy('trade_license_expiry_date')
            ->get();

        if ($expiringSoon->isEmpty() && $expired->isEmpty()) {
            $this->info('No agent licenses need attention.');
            return self::SUCCESS;
        }

        $rows = [];

        foreach ($expiringSoon as $agent) {
            if ($dry) {
                $rows[] = [$agent->name, $agent->trade_license_expiry_date->format('d M Y'), 'WOULD WARN'];
                continue;
            }

            $this->notifyExpiryWarning($agent);
            $agent->forceFill(['expiry_warning_sent_at' => now()])->save();
            $rows[] = [$agent->name, $agent->trade_license_expiry_date->format('d M Y'), 'warned'];
        }

        foreach ($expired as $agent) {
            if ($dry) {
                $rows[] = [$agent->name, $agent->trade_license_expiry_date->format('d M Y'), 'WOULD DISABLE'];
                continue;
            }

            $this->notifyDisabled($agent);
            $agent->disableForExpiry();
            $rows[] = [$agent->name, $agent->trade_license_expiry_date->format('d M Y'), 'DISABLED'];
        }

        $this->table(['Agent', 'License Expiry', 'Action'], $rows);

        return self::SUCCESS;
    }

    private function notifyExpiryWarning(User $agent): void
    {
        try {
            $recipients = booking_recipients(service_notification_emails('agents', $agent->company));
            if (!empty($recipients)) {
                Mail::to($recipients)->send(new \App\Mail\BookingNotificationMail(
                    heading: 'Agent trade license expiring soon',
                    intro: 'This agent\'s trade license expires within the next ' . self::WARNING_WINDOW_DAYS . ' days. They have been notified to renew.',
                    rows: [
                        'Agent' => $agent->name,
                        'Company' => $agent->company_name,
                        'Email' => $agent->email,
                        'Trade License #' => $agent->trade_license_number,
                        'Expiry Date' => $agent->trade_license_expiry_date->format('d M Y'),
                    ],
                    reference: (string) $agent->id,
                    replyToAddress: $agent->email,
                ));
            }
        } catch (\Throwable $e) {
            Log::error('Agent expiry-warning business email failed', [
                'agent_id' => $agent->id,
                'error' => $e->getMessage(),
            ]);
        }

        try {
            Mail::to($agent->email)->send(new AgentLicenseExpiringMail($agent));
        } catch (\Throwable $e) {
            Log::error('Agent expiry-warning agent email failed', [
                'agent_id' => $agent->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function notifyDisabled(User $agent): void
    {
        try {
            $recipients = booking_recipients(service_notification_emails('agents', $agent->company));
            if (!empty($recipients)) {
                Mail::to($recipients)->send(new \App\Mail\BookingNotificationMail(
                    heading: 'Agent auto-disabled — trade license expired',
                    intro: 'This agent\'s trade license has expired. Their account has been automatically disabled and they can no longer log in.',
                    rows: [
                        'Agent' => $agent->name,
                        'Company' => $agent->company_name,
                        'Email' => $agent->email,
                        'Trade License #' => $agent->trade_license_number,
                        'Expired On' => $agent->trade_license_expiry_date->format('d M Y'),
                    ],
                    reference: (string) $agent->id,
                    replyToAddress: $agent->email,
                ));
            }
        } catch (\Throwable $e) {
            Log::error('Agent auto-disable business email failed', [
                'agent_id' => $agent->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
