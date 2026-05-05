<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('manager.login');
        }

        $user = Auth::user();
        $isSuperAdmin = $user->is_super_admin || $user->role === 'super_admin';
        $isCompanyManager = in_array($user->role, ['company_owner', 'company_admin'], true);

        if (!$isSuperAdmin && !$isCompanyManager) {
            $this->logoutAndInvalidate($request);
            return redirect()->route('manager.login')
                ->withErrors(['credentials' => 'You do not have manager access.']);
        }

        $tenant = app()->bound('current_company') ? app('current_company') : null;
        if (!$isSuperAdmin && $tenant instanceof Company && (int) $user->company_id !== (int) $tenant->id) {
            $this->logoutAndInvalidate($request);
            return redirect()->route('manager.login')
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
