<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    private const CURRENCIES = ['AED', 'USD', 'SAR', 'EUR', 'INR', 'GBP'];

    private const TIMEZONES = [
        'Asia/Dubai',
        'Asia/Riyadh',
        'Asia/Kolkata',
        'Asia/Karachi',
        'Asia/Singapore',
        'Europe/London',
        'Europe/Paris',
        'America/New_York',
        'UTC',
    ];

    public function profile()
    {
        $company = current_company();
        abort_unless($company, 404);

        return view('manager.settings.profile', compact('company'));
    }

    public function updateProfile(Request $request)
    {
        $company = current_company();
        abort_unless($company, 404);

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
            'logo'    => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $validated['logo'] = $request->file('logo')->store('companies/logos', 'public');
        }

        $company->update($validated);

        return redirect()
            ->route('manager.settings.profile')
            ->with('success', 'Profile & branding updated.');
    }

    public function preferences()
    {
        $company = current_company();
        abort_unless($company, 404);

        return view('manager.settings.preferences', [
            'company'    => $company,
            'currencies' => self::CURRENCIES,
            'timezones'  => self::TIMEZONES,
        ]);
    }

    public function updatePreferences(Request $request)
    {
        $company = current_company();
        abort_unless($company, 404);

        $validated = $request->validate([
            'currency'          => ['required', Rule::in(self::CURRENCIES)],
            'timezone'          => ['required', Rule::in(self::TIMEZONES)],
            'markup_percentage' => 'required|numeric|min:0|max:100',
            'level9_whatsapp'   => 'nullable|string|max:30',
        ]);

        $company->update([
            'currency'          => $validated['currency'],
            'timezone'          => $validated['timezone'],
            'markup_percentage' => $validated['markup_percentage'],
        ]);

        // Homepage promotion toggles (GoTrips main site only — drive @platformOnly
        // content like the FIFA World Cup 2026 / Events trending campaign).
        $company->setSetting('events_trending_enabled', $request->boolean('events_trending_enabled'));
        $company->setSetting('fifa_promo_enabled', $request->boolean('fifa_promo_enabled'));
        $company->setSetting('level9_whatsapp', preg_replace('/[^0-9]/', '', (string) ($validated['level9_whatsapp'] ?? '')));
        $company->save();

        return redirect()
            ->route('manager.settings.preferences')
            ->with('success', 'Preferences saved.');
    }
}
