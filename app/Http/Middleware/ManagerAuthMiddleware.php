<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerAuthMiddleware
{
    /**
     * Route-name patterns a customer_care login may open. Everything else in
     * the manager group bounces to the support queue (logout lives outside
     * the group, so it always stays reachable).
     */
    public const CUSTOMER_CARE_ROUTES = ['manager.support.*', 'manager.help.*'];

    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('manager.login');
        }

        $user = Auth::user();
        $isSuperAdmin = $user->is_super_admin || $user->role === 'super_admin';
        $isCompanyManager = in_array($user->role, ['company_owner', 'company_admin', 'customer_care'], true);

        if (!$isSuperAdmin && !$isCompanyManager) {
            $this->logoutAndInvalidate($request);
            return redirect()->route('manager.login')
                ->withErrors(['credentials' => 'You do not have manager access.']);
        }

        if ($user->is_active === false) {
            $this->logoutAndInvalidate($request);
            return redirect()->route('manager.login')
                ->withErrors(['credentials' => 'This account has been deactivated. Contact your manager.']);
        }

        $tenant = app()->bound('current_company') ? app('current_company') : null;
        if (!$isSuperAdmin && $tenant instanceof Company && (int) $user->company_id !== (int) $tenant->id) {
            $this->logoutAndInvalidate($request);
            return redirect()->route('manager.login')
                ->withErrors(['credentials' => 'You cannot access this tenant.']);
        }

        if ($user->role === 'customer_care' && !$request->routeIs(...self::CUSTOMER_CARE_ROUTES)) {
            return redirect()->route('manager.support.index');
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
