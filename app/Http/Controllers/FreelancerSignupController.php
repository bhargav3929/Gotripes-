<?php

namespace App\Http\Controllers;

use App\Models\ReferralAgent;
use App\Models\ReferralBankAccount;
use App\Models\ReferralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Freelancer signup — accessible at freelancers.gotrips.ai/register
 * Stores agent with is_freelancer=true; reuses partner referral plumbing.
 */
class FreelancerSignupController extends Controller
{
    public function landing()
    {
        if (Auth::guard('referral_agent')->check()) {
            return redirect()->route('referral.dashboard');
        }
        return view('freelancer.landing');
    }

    public function showRegister()
    {
        $settings = ReferralSetting::getSettings();
        if (!$settings->signup_enabled) {
            return redirect()->route('freelancer.landing')->with('error', 'Registrations are currently closed.');
        }
        if (Auth::guard('referral_agent')->check()) {
            return redirect()->route('referral.dashboard');
        }
        return view('freelancer.register');
    }

    public function register(Request $request)
    {
        $settings = ReferralSetting::getSettings();
        if (!$settings->signup_enabled) {
            return redirect()->route('freelancer.landing')->with('error', 'Registrations are currently closed.');
        }

        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'email'               => 'required|email|unique:referral_agents,email',
            'phone'               => 'required|string|max:20',
            'country'             => 'required|string|max:100',
            'profile_headline'    => 'nullable|string|max:160',
            'services_offered'    => 'nullable|string|max:2000',
            'password'            => 'required|min:8|confirmed',
            'bank_name'           => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'account_number'      => 'required|string|max:64',
            'iban'                => 'nullable|string|max:64',
            'swift_code'          => 'nullable|string|max:32',
        ]);

        $agent = DB::transaction(function () use ($validated, $settings) {
            $agent = ReferralAgent::create([
                'name'             => $validated['name'],
                'email'            => $validated['email'],
                'phone'            => $validated['phone'],
                'country'          => $validated['country'],
                'is_freelancer'    => true,
                'profile_headline' => $validated['profile_headline'] ?? null,
                'services_offered' => $validated['services_offered'] ?? null,
                'password'         => Hash::make($validated['password']),
                'commission_type'  => $settings->commission_type,
                'commission_value' => $settings->commission_value,
                'status'           => 'active',
                'referral_code'    => ReferralAgent::generateCodeFromName($validated['name']),
            ]);

            ReferralBankAccount::create([
                'referral_agent_id'   => $agent->id,
                'bank_name'           => $validated['bank_name'],
                'account_holder_name' => $validated['account_holder_name'],
                'account_number'      => $validated['account_number'],
                'iban'                => $validated['iban'] ?? null,
                'swift_code'          => $validated['swift_code'] ?? null,
                'country'             => $validated['country'],
                'is_primary'          => true,
            ]);

            return $agent;
        });

        Auth::guard('referral_agent')->login($agent);
        $agent->update(['last_login_at' => now()]);

        return redirect()->route('referral.dashboard')
            ->with('success', 'Welcome to GoTrips Freelancers! Your storefront is live.');
    }
}
