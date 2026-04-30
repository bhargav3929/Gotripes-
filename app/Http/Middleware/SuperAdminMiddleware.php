<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('superadmin.login');
        }

        $user = auth()->user();
        if (!$user->is_super_admin && $user->role !== 'super_admin') {
            auth()->logout();
            return redirect()->route('superadmin.login')
                ->withErrors(['email' => 'This account does not have super-admin access.']);
        }

        return $next($request);
    }
}
