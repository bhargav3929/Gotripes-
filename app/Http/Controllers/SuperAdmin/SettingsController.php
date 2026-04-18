<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Cache::get('platform_settings', [
            'platform_name' => 'GoTrips SaaS',
            'support_email' => 'support@gotrips.ai',
            'default_currency' => 'AED',
            'default_timezone' => 'Asia/Dubai',
            'trial_days' => 14,
        ]);

        return view('superadmin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = Cache::get('platform_settings', []);

        $newSettings = array_merge($settings, $request->only([
            'platform_name',
            'support_email',
            'default_currency',
            'default_timezone',
            'trial_days',
            'smtp_host',
            'smtp_port',
            'smtp_username',
        ]));

        if ($request->smtp_password && $request->smtp_password !== '••••••••') {
            $newSettings['smtp_password'] = encrypt($request->smtp_password);
        }

        Cache::forever('platform_settings', $newSettings);

        return back()->with('success', 'Settings updated successfully.');
    }

    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        return back()->with('success', 'All caches cleared successfully.');
    }
}
