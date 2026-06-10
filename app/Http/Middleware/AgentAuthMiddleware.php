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

        if (!$user->is_active) {
            $this->logoutAndInvalidate($request);
            return redirect()->route('agent.login')
                ->withErrors(['credentials' => 'Your agent account has been deactivated. Contact your manager.']);
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
