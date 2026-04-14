<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ReferralAgentMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('referral_agent')->check()) {
            return redirect()->route('referral.login');
        }

        $agent = Auth::guard('referral_agent')->user();

        // Check if agent is still active
        if ($agent->status !== 'active') {
            Auth::guard('referral_agent')->logout();
            return redirect()->route('referral.login')
                ->withErrors(['email' => 'Your account has been deactivated. Please contact support.']);
        }

        return $next($request);
    }
}
