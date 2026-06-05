<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    private const CURRENCIES = ['AED', 'USD', 'SAR', 'EUR', 'INR', 'GBP'];

    // Menu items that can show a seasonal flashy "Hot/Trending" badge.
    // key => label (label shown in the manager Menu Highlights UI).
    public const MENU_FLASH_ITEMS = [
        'events'         => 'Events',
        'evisa'          => 'e-Visa (30 countries)',
        'cruises'        => 'Cruises',
        'holiday_homes'  => 'Holiday Homes',
        'local_tours'    => 'Local Tours',
        'festival_tours' => 'Festival Tours',
        'medical_tours'  => 'Medical Tours',
        'hotels'         => 'Hotels',
        'esim'           => 'eSIM',
    ];

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
            'company'        => $company,
            'currencies'     => self::CURRENCIES,
            'timezones'      => self::TIMEZONES,
            'menuFlashItems' => self::MENU_FLASH_ITEMS,
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
            'enquiry_whatsapp'  => 'nullable|string|max:30',
        ]);

        $company->update([
            'currency'          => $validated['currency'],
            'timezone'          => $validated['timezone'],
            'markup_percentage' => $validated['markup_percentage'],
        ]);

        // Per-item flashy "Hot/Trending" menu badges (seasonal on/off).
        $flash = [];
        foreach (array_keys(self::MENU_FLASH_ITEMS) as $key) {
            $flash[$key] = $request->boolean("flash_{$key}");
        }
        $company->setSetting('menu_flash', $flash);

        // Homepage FIFA promo section (GoTrips main site only, @platformOnly).
        $company->setSetting('fifa_promo_enabled', $request->boolean('fifa_promo_enabled'));

        // Contact numbers for "Enquire on WhatsApp" CTAs.
        $company->setSetting('level9_whatsapp', preg_replace('/[^0-9]/', '', (string) ($validated['level9_whatsapp'] ?? '')));
        $company->setSetting('enquiry_whatsapp', preg_replace('/[^0-9]/', '', (string) ($validated['enquiry_whatsapp'] ?? '')));
        $company->save();

        return redirect()
            ->route('manager.settings.preferences')
            ->with('success', 'Preferences saved.');
    }
}
