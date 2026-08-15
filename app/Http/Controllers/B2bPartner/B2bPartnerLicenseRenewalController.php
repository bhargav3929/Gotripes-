<?php

namespace App\Http\Controllers\B2bPartner;

use App\Http\Controllers\Controller;
use App\Mail\BookingNotificationMail;
use App\Models\B2bPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Self-service license renewal for a UAE partner disabled by
 * partners:check-license-expiry. Deliberately NOT behind the b2b.auth
 * middleware — a disabled partner can never pass canLogin(), so this
 * verifies identity (email + password) directly instead of relying on a
 * session guard. Submitting a renewal does not restore access by itself;
 * it queues the account for manager confirmation (see B2bPartner::
 * submitLicenseRenewal()).
 */
class B2bPartnerLicenseRenewalController extends Controller
{
    public function showForm()
    {
        return view('b2b-partner.renew-license');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'trade_license_number' => 'required|string|max:100',
            'trade_license_expiry_date' => 'required|date|after:today',
            'trade_license_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $partner = B2bPartner::where('email', $validated['email'])->first();

        if (!$partner || !Hash::check($validated['password'], $partner->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

        if (!$partner->wasDisabledForExpiry()) {
            $message = match (true) {
                $partner->isPending() => 'Your application is still under initial review — there is no license to renew yet.',
                $partner->isRejected() => 'Your application was not approved. Contact support.',
                $partner->is_active => 'Your account is already active — no renewal needed.',
                default => 'This renewal form does not apply to your account. Contact support.',
            };

            return back()->withErrors(['email' => $message])->withInput();
        }

        $documentPath = $request->file('trade_license_document')->store('partners/trade_licenses', 'public');

        $partner->submitLicenseRenewal(
            $validated['trade_license_number'],
            $validated['trade_license_expiry_date'],
            $documentPath
        );

        try {
            $recipients = booking_recipients(service_notification_emails('b2b_partners', $partner->company));
            if (!empty($recipients)) {
                Mail::to($recipients)->send(new BookingNotificationMail(
                    heading: 'B2B partner submitted a renewed trade license',
                    intro: 'This partner was auto-disabled for an expired license and has now submitted a renewal. Review the new document and confirm to restore their access.',
                    rows: [
                        'Agency' => $partner->company_name,
                        'Contact' => $partner->contact_name,
                        'Email' => $partner->email,
                        'New License #' => $partner->trade_license_number,
                        'New Expiry Date' => $partner->trade_license_expiry_date->format('d M Y'),
                    ],
                    reference: (string) $partner->id,
                    replyToAddress: $partner->email,
                ));
            }
        } catch (\Throwable $e) {
            Log::error('B2B partner renewal notification failed', [
                'partner_id' => $partner->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('agency.renew-license.submitted');
    }

    public function submitted()
    {
        return view('b2b-partner.renew-license-submitted');
    }
}
