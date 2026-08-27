<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Guards the /agent portal. Mirrors ManagerAuthMiddleware but for the
 * `company_agent` role: the account must be active and belong to the
 * current tenant.
 */
class AgentAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('agent.login');
        }

        $user = Auth::user();

        if ($user->role !== 'company_agent') {
            $this->logoutAndInvalidate($request);
            return redirect()->route('agent.login')
                ->withErrors(['credentials' => 'You do not have agent access.']);
        }

        // Defensive check: enforce an expired license the same day it lapses,
        // without waiting for the next partners:check-license-expiry-style
        // scheduled run (see agents:check-license-expiry).
        if ($user->is_active && $user->isTradeLicenseExpired()) {
            $user->disableForExpiry();
        }

        if (!$user->is_active) {
            $message = $user->wasDisabledForExpiry()
                ? 'Your trade license has expired, so your agent account has been disabled. Submit your renewed license to restore access.'
                : 'Your agent account has been deactivated. Contact your manager.';

            $this->logoutAndInvalidate($request);
            return redirect()->route('agent.login')
                ->withErrors(['credentials' => $message]);
        }

        $tenant = app()->bound('current_company') ? app('current_company') : null;
        if ($tenant instanceof Company && (int) $user->company_id !== (int) $tenant->id) {
            $this->logoutAndInvalidate($request);
            return redirect()->route('agent.login')
                ->withErrors(['credentials' => 'You cannot access this tenant.']);
        }

        return $next($request);
    }

    private function logoutAndInvalidate(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
