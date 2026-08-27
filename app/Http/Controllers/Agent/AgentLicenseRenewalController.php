<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Mail\BookingNotificationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Self-service license renewal for an agent disabled by
 * agents:check-license-expiry. Deliberately NOT behind the agent.auth
 * middleware — a disabled agent can never pass that gate, so this verifies
 * identity (email + password) directly instead of relying on a session
 * guard. Submitting a renewal does not restore access by itself; it queues
 * the account for manager confirmation (see User::submitLicenseRenewal()).
 * Mirrors B2bPartnerLicenseRenewalController exactly.
 */
class AgentLicenseRenewalController extends Controller
{
    public function showForm()
    {
        return view('agent.renew-license');
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

        $agent = User::where('email', $validated['email'])
            ->where('role', 'company_agent')
            ->first();

        if (!$agent || !Hash::check($validated['password'], $agent->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

        if (!$agent->wasDisabledForExpiry()) {
            $message = match (true) {
                $agent->is_active => 'Your account is already active — no renewal needed.',
                default => 'This renewal form does not apply to your account. Contact your manager.',
            };

            return back()->withErrors(['email' => $message])->withInput();
        }

        $documentPath = $request->file('trade_license_document')->store('agents/trade_licenses', 'public');

        $agent->submitLicenseRenewal(
            $validated['trade_license_number'],
            $validated['trade_license_expiry_date'],
            $documentPath
        );

        try {
            $recipients = booking_recipients(service_notification_emails('agents', $agent->company));
            if (!empty($recipients)) {
                Mail::to($recipients)->send(new BookingNotificationMail(
                    heading: 'Agent submitted a renewed trade license',
                    intro: 'This agent was auto-disabled for an expired license and has now submitted a renewal. Review the new document and confirm to restore their access.',
                    rows: [
                        'Agent' => $agent->name,
                        'Company' => $agent->company_name,
                        'Email' => $agent->email,
                        'New License #' => $agent->trade_license_number,
                        'New Expiry Date' => $agent->trade_license_expiry_date->format('d M Y'),
                    ],
                    reference: (string) $agent->id,
                    replyToAddress: $agent->email,
                ));
            }
        } catch (\Throwable $e) {
            Log::error('Agent renewal notification failed', [
                'agent_id' => $agent->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('agent.renew-license.submitted');
    }

    public function submitted()
    {
        return view('agent.renew-license-submitted');
    }
}
