<?php

namespace App\Http\Controllers\B2bPartner;

use App\Http\Controllers\Controller;
use App\Mail\B2bPartnerWelcomeMail;
use App\Models\B2bPartner;
use App\Services\B2bPartnerContractService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class B2bPartnerSignupController extends Controller
{
    public function showRegister()
    {
        if (Auth::guard('b2b_partner')->check()) {
            return redirect()->route('agency.dashboard');
        }

        return view('b2b-partner.auth.register');
    }

    /**
     * Registration does NOT log the partner in — unlike the ReferralAgent
     * signup this is modeled on, a B2B partner starts `pending` and needs a
     * manager to approve them before they can access the portal.
     */
    public function register(Request $request, B2bPartnerContractService $contracts)
    {
        $isUae = $request->input('country') === B2bPartner::UAE_COUNTRY_NAME;

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email' => 'required|email|unique:b2b_partners,email',
            'phone' => 'required|string|max:30',
            'country' => 'required|string|max:100',
            'password' => 'required|string|min:8|confirmed',
            'trade_license_number' => 'required_if:country,' . B2bPartner::UAE_COUNTRY_NAME . '|nullable|string|max:100',
            'trade_license_expiry_date' => 'required_if:country,' . B2bPartner::UAE_COUNTRY_NAME . '|nullable|date|after:today',
            'trade_license_document' => 'required_if:country,' . B2bPartner::UAE_COUNTRY_NAME . '|nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'signature_full_name' => 'required|string|max:255',
            'signature_agreed' => 'required|accepted',
        ], [
            'signature_agreed.accepted' => 'You must agree to the partner agreement to register.',
        ]);

        $partner = DB::transaction(function () use ($request, $validated, $isUae, $contracts) {
            $licensePath = null;
            if ($isUae && $request->hasFile('trade_license_document')) {
                $licensePath = $request->file('trade_license_document')->store('partners/trade_licenses', 'public');
            }

            $partner = B2bPartner::create([
                'company_name' => $validated['company_name'],
                'contact_name' => $validated['contact_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'],
                'country' => $validated['country'],
                'trade_license_number' => $isUae ? $validated['trade_license_number'] : null,
                'trade_license_expiry_date' => $isUae ? $validated['trade_license_expiry_date'] : null,
                'trade_license_document_path' => $licensePath,
                'contract_type' => $isUae ? 'national' : 'international',
                'signature_full_name' => $validated['signature_full_name'],
                'signature_agreed' => true,
                'signature_ip' => $request->ip(),
                'signed_at' => now(),
                'status' => 'pending',
            ]);

            $contractPath = $contracts->generate($partner);
            $partner->update(['contract_pdf_path' => $contractPath]);

            return $partner;
        });

        try {
            Mail::to($partner->email)->send(new B2bPartnerWelcomeMail($partner));
        } catch (\Throwable $e) {
            Log::error('B2B partner welcome email failed', [
                'partner_id' => $partner->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('agency.register.submitted');
    }

    public function submitted()
    {
        return view('b2b-partner.register-submitted');
    }
}
