<?php

namespace App\Providers;

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
    }
}
