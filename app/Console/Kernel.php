<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Flip pending commissions to available once their hold period elapses.
        // Runs hourly so released commissions appear within ~1h of becoming due.
        // Production needs `php artisan schedule:run` in cron every minute.
        $schedule->command('commissions:release')
            ->hourly()
            ->withoutOverlapping()
            ->onOneServer();

        // Rescue e-visa applications that were paid but never finalized because
        // the customer did not return from the payment page, and relay provider
        // decisions to travellers. Every ten minutes so a stranded payment is
        // recovered while the customer is still expecting a confirmation.
        $schedule->command('evisa:reconcile')
            ->everyTenMinutes()
            ->withoutOverlapping()
            ->onOneServer();

        // Repair eSIM orders the provider and we disagree about: paid but never
        // assigned, assigned but no QR emailed, or issued upstream against an
        // order we never marked. Every repair is idempotent.
        $schedule->command('esim:reconcile')
            ->everyTenMinutes()
            ->withoutOverlapping()
            ->onOneServer();

        // Rescue eSIM orders paid via Nomod but never confirmed because the
        // customer did not return from the checkout redirect. esim:reconcile
        // above only compares against MontyeSIM, which never heard about these
        // orders in the first place — this asks Nomod directly instead.
        $schedule->command('esim:reconcile-payments')
            ->everyTenMinutes()
            ->withoutOverlapping()
            ->onOneServer();

        // Rescue activity bookings that were paid via Nomod but never
        // finalized because the customer did not return from the checkout
        // redirect. Same failure mode as evisa:reconcile above, just for the
        // activities order type. Every ten minutes for the same reason.
        $schedule->command('activity:reconcile')
            ->everyTenMinutes()
            ->withoutOverlapping()
            ->onOneServer();

        // Keep the Fluxir catalog warm. The storefront builds its country list
        // from a 24h cache; letting it expire on a real visitor costs them a
        // ~12s page load, and an upstream hiccup at that moment shows the whole
        // page as unavailable.
        $schedule->command('evisa:warm-catalog')
            ->hourly()
            ->withoutOverlapping()
            ->onOneServer();

        // Warn approved UAE B2B partners (and the business) ~30 days before
        // their trade license expires, then hard-disable login the moment it
        // actually expires. Daily is enough — license expiry is a calendar
        // date event, not something needing the 10-minute cadence used for
        // payment reconciliation above.
        $schedule->command('partners:check-license-expiry')
            ->dailyAt('08:00')
            ->withoutOverlapping()
            ->onOneServer();

        // Same expiry lifecycle as partners:check-license-expiry above, for
        // company_agent Users instead of B2B partners.
        $schedule->command('agents:check-license-expiry')
            ->dailyAt('08:05')
            ->withoutOverlapping()
            ->onOneServer();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
