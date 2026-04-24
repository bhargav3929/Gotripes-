<?php

namespace App\Http\Controllers;

use App\Models\ReferralAgent;
use App\Models\ReferralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ReferralAgentSignupController extends Controller
{
    public function showRegister()
    {
        $settings = ReferralSetting::getSettings();
        if (!$settings->signup_enabled) {
            return redirect()->route('referral.login')->with('error', 'Registrations are currently closed.');
        }
        if (Auth::guard('referral_agent')->check()) {
            return redirect()->route('referral.dashboard');
        }
        return view('referral.auth.register');
    }

    public function register(Request $request)
    {
        $settings = ReferralSetting::getSettings();
        if (!$settings->signup_enabled) {
            return redirect()->route('referral.login')->with('error', 'Registrations are currently closed.');
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:referral_agents,email',
            'phone'    => 'required|string|max:20',
            'password' => 'required|min:8|confirmed',
        ]);

        $agent = ReferralAgent::create([
            'name'             => $validated['name'],
            'email'            => $validated['email'],
            'phone'            => $validated['phone'],
            'password'         => Hash::make($validated['password']),
            'commission_type'  => $settings->commission_type,
            'commission_value' => $settings->commission_value,
            'status'           => 'active',
            'referral_code'    => ReferralAgent::generateCodeFromName($validated['name']),
        ]);

        Auth::guard('referral_agent')->login($agent);
        $agent->update(['last_login_at' => now()]);

        return redirect()->route('referral.dashboard')
            ->with('success', 'Welcome to GoTrips Partner Program! Your referral link is ready.');
    }
}
