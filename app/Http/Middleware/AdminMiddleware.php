<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     * Only allows users with the Admin role to proceed.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect('/admin');
        }

        $user = auth()->user();

        // Only the Admin role grants access to admin-restricted routes
        if ($user->isAdmin()) {
            return $next($request);
        }

        // If the user is an Activities Manager, redirect them to the activities section
        if ($user->isActivitiesManager()) {
            return redirect('/admin/uaeactivities')
                ->with('info', 'You do not have access to this section.');
        }

        // All other users are denied
        Auth::logout();
        return redirect('/admin')->withErrors(['name' => 'Access denied.']);
    }
}
