<?php

use App\Models\Company;

if (!function_exists('current_company')) {
    function current_company(): ?Company
    {
        return app()->bound('current_company') ? app('current_company') : null;
    }
}

if (!function_exists('current_company_id')) {
    function current_company_id(): ?int
    {
        return current_company()?->id;
    }
}

if (!function_exists('has_tenant')) {
    function has_tenant(): bool
    {
        return current_company() !== null;
    }
}
