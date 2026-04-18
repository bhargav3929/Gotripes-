<?php

namespace App\Providers;

use App\Models\Company;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class TenantServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register a singleton for current company
        $this->app->singleton('current_company', function () {
            return null;
        });
    }

    public function boot(): void
    {
        // Register Blade directives for tenant checks
        $this->registerBladeDirectives();

        // Share tenant helper globally
        View::composer('*', function ($view) {
            $view->with('currentCompany', app()->has('current_company') ? app('current_company') : null);
        });
    }

    protected function registerBladeDirectives(): void
    {
        // Check if user is super admin
        Blade::if('superadmin', function () {
            return auth()->check() && auth()->user()->is_super_admin;
        });

        // Check if user is company admin
        Blade::if('companyadmin', function () {
            return auth()->check() && in_array(auth()->user()->role, ['company_owner', 'company_admin']);
        });

        // Check if company has feature
        Blade::if('hasfeature', function (string $feature) {
            if (!app()->has('current_company')) {
                return false;
            }
            return app('current_company')->hasFeature($feature);
        });

        // Check subscription plan
        Blade::if('plan', function (string $plan) {
            if (!app()->has('current_company')) {
                return false;
            }
            return app('current_company')->plan === $plan;
        });

        // Check if on any paid plan
        Blade::if('paidplan', function () {
            if (!app()->has('current_company')) {
                return false;
            }
            return in_array(app('current_company')->plan, ['basic', 'pro', 'enterprise']);
        });
    }
}
