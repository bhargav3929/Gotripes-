<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Announcement;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production
        if (env('APP_ENV') === 'production') {
            \URL::forceScheme('https');
        } else {
            \URL::forceRootUrl(env('APP_URL'));
        }

        // Share ticker items with header view
        View::composer('header', function ($view) {
            $tickerItems = Announcement::where('isActive', true)
                ->orderBy('createdDate', 'desc')
                ->limit(6)
                ->get();
            $view->with('tickerItems', $tickerItems);
        });

        // @feature('hajj_umrah') ... @endfeature — gates a block on tenant feature flag
        Blade::if('feature', function (string $feature) {
            $company = app()->bound('current_company') ? app('current_company') : null;
            // No tenant bound (main gotrips.ai or unidentified) = full access
            if (!$company instanceof \App\Models\Company) {
                return true;
            }
            return $company->hasFeature($feature);
        });

        // @platformOnly ... @endplatformOnly — content visible ONLY on the main
        // gotrips.ai site, never on white-label tenant subdomains.
        Blade::if('platformOnly', function () {
            $company = app()->bound('current_company') ? app('current_company') : null;
            if (!$company instanceof \App\Models\Company) {
                return true; // no tenant = main site
            }
            return $company->slug === 'gotrips';
        });
    }
}
