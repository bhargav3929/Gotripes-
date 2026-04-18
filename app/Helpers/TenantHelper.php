<?php

namespace App\Helpers;

use App\Models\Company;

class TenantHelper
{
    /**
     * Get current company
     */
    public static function company(): ?Company
    {
        return app()->has('current_company') ? app('current_company') : null;
    }

    /**
     * Get current company ID
     */
    public static function companyId(): ?int
    {
        $company = self::company();
        return $company ? $company->id : null;
    }

    /**
     * Check if running in tenant context
     */
    public static function hasTenant(): bool
    {
        return self::company() !== null;
    }

    /**
     * Check if current user is super admin
     */
    public static function isSuperAdmin(): bool
    {
        return auth()->check() && auth()->user()->is_super_admin;
    }

    /**
     * Check if current user is company admin
     */
    public static function isCompanyAdmin(): bool
    {
        if (!auth()->check()) {
            return false;
        }
        return in_array(auth()->user()->role, ['company_owner', 'company_admin']);
    }

    /**
     * Get branding for current company
     */
    public static function branding(): array
    {
        $company = self::company();
        if (!$company) {
            return self::defaultBranding();
        }
        return $company->branding;
    }

    /**
     * Default branding when no company
     */
    public static function defaultBranding(): array
    {
        return [
            'name' => config('app.name', 'GoTrips'),
            'logo' => asset('images/logo.png'),
            'favicon' => asset('favicon.ico'),
            'colors' => [
                'primary' => '#FFD700',
                'secondary' => '#FFA500',
                'text' => '#FFFFFF',
                'background' => '#16161a',
            ],
            'contact' => [
                'email' => config('mail.from.address'),
                'phone' => null,
                'address' => null,
                'website' => config('app.url'),
            ],
        ];
    }

    /**
     * Generate CSS variables from branding
     */
    public static function brandingCSS(): string
    {
        $branding = self::branding();
        $colors = $branding['colors'];

        return "
            :root {
                --primary-gold: {$colors['primary']};
                --secondary-gold: {$colors['secondary']};
                --text-main: {$colors['text']};
                --dark-bg: {$colors['background']};
                --company-name: '{$branding['name']}';
            }
        ";
    }

    /**
     * Get company setting
     */
    public static function setting(string $key, $default = null)
    {
        $company = self::company();
        if (!$company) {
            return $default;
        }
        return $company->getSetting($key, $default);
    }

    /**
     * Check if company has feature
     */
    public static function hasFeature(string $feature): bool
    {
        $company = self::company();
        if (!$company) {
            return false;
        }
        return $company->hasFeature($feature);
    }

    /**
     * Get markup percentage for pricing
     */
    public static function markupPercentage(): float
    {
        $company = self::company();
        return $company ? (float) $company->markup_percentage : 20.0;
    }

    /**
     * Get currency for current company
     */
    public static function currency(): string
    {
        $company = self::company();
        return $company ? $company->currency : 'AED';
    }

    /**
     * Format price with company currency
     */
    public static function formatPrice(float $amount): string
    {
        return self::currency() . ' ' . number_format($amount, 2);
    }
}
