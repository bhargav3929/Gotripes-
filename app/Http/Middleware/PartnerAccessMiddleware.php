<?php
// app/Http/Middleware/PartnerAccessMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PartnerAccessMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Simple check using direct attribute access
        $emailVerifiedAt = $user->email_verified_at;

        // Debug logging - remove after testing
        Log::info('PartnerAccessMiddleware Debug', [
            'user_id' => $user->id,
            'email_verified_at' => $emailVerifiedAt,
            'current_route' => $request->path()
        ]);

        // If user has email_verified_at = 2 (Partner), restrict access
        if ($emailVerifiedAt == 2) {
            $allowedRoutes = [
                'emirates',
                'logout',
                'profile',
            ];

            $currentRoute = $request->path();
            
            // Check if current route is allowed or starts with emirates
            $isAllowed = false;
            
            foreach ($allowedRoutes as $route) {
                if ($route === $currentRoute || str_starts_with($currentRoute, $route)) {
                    $isAllowed = true;
                    break;
                }
            }

            if (!$isAllowed) {
                Log::info('Partner user redirected', [
                    'user_id' => $user->id,
                    'attempted_route' => $currentRoute,
                    'redirected_to' => '/emirates'
                ]);
                
                // Redirect partner users to activities page if they try to access restricted routes
                return redirect('/emirates')->with('warning', 'As a partner, you have access only to Activities section.');
            }
        }

        return $next($request);
    }
}
