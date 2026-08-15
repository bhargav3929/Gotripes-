<?php

namespace App\Http\Controllers\B2bPartner;

use App\Http\Controllers\Controller;
use App\Models\B2bPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class B2bPartnerAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('b2b_partner')->check()) {
            return redirect()->route('agency.dashboard');
        }

        return view('b2b-partner.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $partner = B2bPartner::where('email', $credentials['email'])->first();

        if (!$partner || !Auth::guard('b2b_partner')->attempt($credentials)) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
        }

        if (!$partner->canLogin()) {
            Auth::guard('b2b_partner')->logout();

            if ($partner->wasDisabledForExpiry() && !$partner->pending_license_review) {
                return back()
                    ->withErrors(['email' => 'Your account has been disabled because your trade license has expired.'])
                    ->with('show_renewal_link', true)
                    ->withInput();
            }

            $message = match (true) {
                $partner->isRejected() => 'Your application was not approved. Contact support.',
                $partner->isPending() => 'Your application is still under review.',
                $partner->pending_license_review => 'Your renewed license is awaiting manager confirmation.',
                default => 'Your account has been disabled. Contact your account manager.',
            };

            return back()->withErrors(['email' => $message])->withInput();
        }

        $request->session()->regenerate();
        $partner->update(['last_login_at' => now()]);

        return redirect()->intended(route('agency.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('b2b_partner')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('agency.login');
    }
}
