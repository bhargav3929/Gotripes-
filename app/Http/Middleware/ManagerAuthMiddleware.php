<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ManagerAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('manager_authenticated')) {
            return redirect()->route('manager.login');
        }

        return $next($request);
    }
}
