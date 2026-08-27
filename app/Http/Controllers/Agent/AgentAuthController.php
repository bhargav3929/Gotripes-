<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Dedicated /agent/login for tenant agent accounts (role company_agent).
 * Managers create these accounts and share the credentials; agents never
 * log in through the manager portal.
 */
class AgentAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && $this->canAccessAgentPortal(Auth::user())) {
            return redirect()->route('agent.dashboard');
        }
        return view('agent.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['credentials' => 'Invalid email or password.']);
        }

        $user = Auth::user();

        if ($user->role !== 'company_agent') {
            $this->rejectLogin($request);
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['credentials' => 'This account does not have agent access.']);
        }

        // Defensive check: enforce an expired license the same day it lapses,
        // without waiting for the next agents:check-license-expiry run.
        if ($user->is_active && $user->isTradeLicenseExpired()) {
            $user->disableForExpiry();
        }

        if (!$user->is_active) {
            $message = $user->wasDisabledForExpiry()
                ? 'Your trade license has expired, so your agent account has been disabled. Submit your renewed license to restore access.'
                : 'Your agent account has been deactivated. Contact your manager.';

            $this->rejectLogin($request);
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['credentials' => $message]);
        }

        $tenant = app()->bound('current_company') ? app('current_company') : null;
        if ($tenant instanceof Company && (int) $user->company_id !== (int) $tenant->id) {
            $this->rejectLogin($request);
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['credentials' => 'This account does not belong to ' . ($tenant->subdomain ?? 'this site') . '.']);
        }

        $user->update(['last_login_at' => now()]);
        $request->session()->regenerate();

        return redirect()->intended(route('agent.dashboard'));
    }

    public function logout(Request $request)
    {
        $this->logoutAndInvalidate($request);
        return redirect()->route('agent.login');
    }

    private function canAccessAgentPortal($user): bool
    {
        return $user && $user->role === 'company_agent' && $user->is_active && !$user->isTradeLicenseExpired();
    }

    // Soft logout for login rejections — does NOT invalidate the session so
    // flash error data survives the redirect back to the login form.
    private function rejectLogin(Request $request): void
    {
        Auth::logout();
        $request->session()->regenerateToken();
    }

    private function logoutAndInvalidate(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
