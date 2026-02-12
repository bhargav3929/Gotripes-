<?php

namespace App\Providers;
use Illuminate\Support\Facades\View;
use App\Models\Announcement;

use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
{
    View::composer('header', function ($view) {
        $announcements = Announcement::where('isActive', true)
                        ->orderBy('AnnouncementImportance', 'desc')
                        ->orderBy('createdDate', 'desc')
                        ->get();
        $view->with('announcements', $announcements);
    });
}

}
