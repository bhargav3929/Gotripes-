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

        // Keep the Fluxir catalog warm. The storefront builds its country list
        // from a 24h cache; letting it expire on a real visitor costs them a
        // ~12s page load, and an upstream hiccup at that moment shows the whole
        // page as unavailable.
        $schedule->command('evisa:warm-catalog')
            ->hourly()
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
