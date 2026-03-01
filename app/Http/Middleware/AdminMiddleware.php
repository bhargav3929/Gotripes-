<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect('/admin');
        }

        $user = auth()->user();

        // Allow users with the 'Admin' role OR legacy admins (null email_verified_at with no partner data)
        $isAdmin = $user->isAdmin() || (is_null($user->email_verified_at) && !str_contains($user->email_verified_at ?? '', 'rseparator'));

        if ($isAdmin) {
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
