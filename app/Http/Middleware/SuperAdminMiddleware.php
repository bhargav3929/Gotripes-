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
            return redirect()->route('login');
        }

        $user = auth()->user();
        if (!$user->is_super_admin && $user->role !== 'super_admin') {
            abort(403, 'Access denied. Super Admin privileges required.');
        }

        return $next($request);
    }
}
