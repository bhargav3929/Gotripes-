<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Super admins can access everything
        if ($user->is_super_admin) {
            return $next($request);
        }

        // Check if user has company admin role
        if (!in_array($user->role, ['company_owner', 'company_admin'])) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        // Ensure user belongs to a company
        if (!$user->company_id) {
            abort(403, 'No company assigned to this account.');
        }

        return $next($request);
    }
}
