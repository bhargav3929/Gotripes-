<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Mail\BookingNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    private const CURRENCIES = ['AED', 'USD', 'SAR', 'EUR', 'INR', 'GBP'];

    // Menu items that can show a seasonal flashing ⚡ icon (manager-controlled).
    // key => label (label shown in the manager Menu Highlights UI).
    public const MENU_FLASH_ITEMS = [
        'activities'     => 'Activities',
        'visa_services'  => 'Visa Services',
        'tour_packages'  => 'Tour Packages',
        'hajj_umrah'     => 'Hajj & Umrah',
        'esim'           => 'eSIM',
        'evisa'          => 'e-Visa (30 countries)',
        'insurance'      => 'Insurance',
        'cruises'        => 'Cruise',
        'transport'      => 'Transport',
        'holiday_homes'  => 'Holiday Homes',
        'mice'           => 'Business Tourism (MICE)',
        'events'         => 'Events',
        'local_tours'    => 'Local Tours',
        'festival_tours' => 'Festival Tours',
        'medical_tours'  => 'Medical Tours',
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

    // Booking products that notify a configurable recipient list when booked.
    // key => label shown on the Booking Notifications settings page.
    public const NOTIFY_SERVICES = [
        'esim' => 'eSIM orders',
        'visa' => 'e-Visa applications',
        'fifa' => 'FIFA ticket enquiries',
    ];

    public function notifications()
    {
        $company = current_company();
        abort_unless($company, 404);

        return view('manager.settings.notifications', [
            'company'  => $company,
            'services' => self::NOTIFY_SERVICES,
            'emails'   => $company->getSetting('booking_notification_emails', []),
        ]);
    }

    public function updateNotifications(Request $request)
    {
        $company = current_company();
        abort_unless($company, 404);

        $rules = [];
        foreach (array_keys(self::NOTIFY_SERVICES) as $key) {
            $rules["emails.$key"] = ['nullable', new \App\Rules\EmailList];
        }
        $request->validate($rules);

        // Store the raw text per service; resolution/parsing happens at send time
        // via service_notification_emails(). Keep only the known service keys.
        $clean = [];
        foreach (array_keys(self::NOTIFY_SERVICES) as $key) {
            $clean[$key] = trim((string) $request->input("emails.$key", ''));
        }
        $company->setSetting('booking_notification_emails', $clean);

        return redirect()
            ->route('manager.settings.notifications')
            ->with('success', 'Booking notification recipients saved.');
    }

    /**
     * Send a sample notification to the currently-saved recipients so the owner
     * can confirm delivery without placing a real booking or payment.
     */
    public function sendTestNotification()
    {
        $company = current_company();
        abort_unless($company, 404);

        // Union of all configured service recipients; falls back to the company
        // email if nothing is configured yet (so the test still proves delivery).
        $configured = [];
        foreach (array_keys(self::NOTIFY_SERVICES) as $key) {
            $configured = array_merge($configured, service_notification_emails($key));
        }
        $recipients = booking_recipients($configured);

        if (empty($recipients)) {
            return back()->with('error', 'No recipients found. Add at least one email above, click Save, then try again.');
        }

        try {
            Mail::to($recipients)->send(new BookingNotificationMail(
                heading: 'Test booking notification',
                intro: 'This is a test from your Booking Notifications settings. If you can read this, your booking notification emails are working — no real booking or payment was involved.',
                rows: [
                    'Account'   => $company->name,
                    'Sent at'   => now()->format('d M Y, H:i'),
                    'Recipients'=> implode(', ', $recipients),
                    'Status'    => 'TEST — delivery check only',
                ],
                reference: 'TEST-' . now()->format('Hi'),
            ));
        } catch (\Throwable $e) {
            return back()->with('error', 'Could not send the test email: ' . $e->getMessage());
        }

        return back()->with('success', 'Test email sent to: ' . implode(', ', $recipients) . '. Check the inbox (and the spam folder).');
    }
}
