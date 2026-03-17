<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivitiesManagerMiddleware
{
    /**
     * Handle an incoming request.
     * Allows users with Admin role, Activities Manager role, or manage_uae_activities permission.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/admin');
        }

        $user = Auth::user();

        // Allow full admins, activities managers, and anyone with the permission
        if ($user->isAdmin() || $user->isActivitiesManager() || $user->hasPermission('manage_uae_activities')) {
            return $next($request);
        }

        abort(403, 'Unauthorized access.');
    }
}
