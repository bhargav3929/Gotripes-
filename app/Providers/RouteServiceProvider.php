<?php
// app/Providers/RouteServiceProvider.php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * 🔥 SIMPLE FIX: Direct redirect based on user type
     */
    public const HOME = '/admin/'; // ✅ New route

    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Tour package wizard. Named limiters keep their own counters: plain
        // throttle:N,M middleware all share one counter per IP, so the many
        // live price quotes a customer triggers would use up checkout's limit.
        RateLimiter::for('tour-quote', fn (Request $request) => Limit::perMinute(120)->by('tour-quote|' . $request->ip()));
        RateLimiter::for('tour-checkout', fn (Request $request) => Limit::perMinute(10)->by('tour-checkout|' . $request->ip()));
    }
}
