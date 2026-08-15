<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Guards the /agency portal. Re-checks on every request (not just at login)
 * so a partner who gets rejected, disabled, or whose license expires mid
 * session is force-logged-out immediately rather than waiting for their
 * session to naturally expire.
 */
class B2bPartnerAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $guard = Auth::guard('b2b_partner');

        if (!$guard->check()) {
            return redirect()->route('agency.login');
        }

        $partner = $guard->user();

        if (!$partner->canLogin()) {
            $showRenewalLink = $partner->wasDisabledForExpiry() && !$partner->pending_license_review;

            $message = match (true) {
                $partner->isRejected() => 'Your application was not approved. Contact support.',
                $partner->isPending() => 'Your application is still under review.',
                $partner->pending_license_review => 'Your renewed license is awaiting manager confirmation.',
                $showRenewalLink => 'Your account has been disabled because your trade license has expired.',
                default => 'Your account has been disabled. Contact your account manager.',
            };

            $this->logoutAndInvalidate($request);

            $redirect = redirect()->route('agency.login')->withErrors(['email' => $message]);

            return $showRenewalLink ? $redirect->with('show_renewal_link', true) : $redirect;
        }

        $tenant = app()->bound('current_company') ? app('current_company') : null;
        if ($tenant instanceof Company && (int) $partner->company_id !== (int) $tenant->id) {
            $this->logoutAndInvalidate($request);

            return redirect()->route('agency.login')->withErrors(['email' => 'You cannot access this tenant.']);
        }

        return $next($request);
    }

    private function logoutAndInvalidate(Request $request): void
    {
        Auth::guard('b2b_partner')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
