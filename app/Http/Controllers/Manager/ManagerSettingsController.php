<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Company;

class ManagerSettingsController extends Controller
{
    /**
     * Show the (read-only) feature list for the current tenant.
     *
     * Tenants cannot edit their own features. Only the platform super-admin
     * can grant or revoke features via /superadmin/companies/{c}/edit.
     */
    public function features()
    {
        $company = current_company();

        $allFeatures = Company::AVAILABLE_FEATURES;
        $enabled = $company && is_array($company->features) ? $company->features : [];

        return view('manager.settings.features', [
            'company'     => $company,
            'allFeatures' => $allFeatures,
            'enabled'     => $enabled,
        ]);
    }
}
