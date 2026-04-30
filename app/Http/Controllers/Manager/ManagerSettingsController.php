<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class ManagerSettingsController extends Controller
{
    /**
     * Show the feature-toggle page for the current tenant.
     */
    public function features()
    {
        $company = app()->bound('current_company') ? app('current_company') : null;

        $allFeatures = Company::AVAILABLE_FEATURES;
        $enabled = $company && is_array($company->features) ? $company->features : array_keys($allFeatures);

        return view('manager.settings.features', [
            'company'     => $company,
            'allFeatures' => $allFeatures,
            'enabled'     => $enabled,
        ]);
    }

    /**
     * Persist the toggled feature list.
     */
    public function updateFeatures(Request $request)
    {
        $company = app()->bound('current_company') ? app('current_company') : null;

        if (!$company instanceof Company) {
            return back()->with('error', 'No tenant identified for this session.');
        }

        $allowed = array_keys(Company::AVAILABLE_FEATURES);
        $submitted = (array) $request->input('features', []);
        $features = array_values(array_intersect($allowed, $submitted));

        $company->features = $features;
        $company->save();

        return back()->with('success', 'Feature settings updated.');
    }
}
