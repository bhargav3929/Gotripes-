<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate an /agent route on a per-agent service grant.
 *
 * Usage in routes/web.php:
 *   Route::resource('packages', ...)->middleware('agent.service:tours');
 *
 * hasService() also checks the tenant feature flag, so revoking a feature
 * from the company instantly revokes it from every agent.
 */
class EnsureAgentService
{
    public function handle(Request $request, Closure $next, string $service): Response
    {
        $user = $request->user();

        if (!$user || !$user->hasService($service)) {
            abort(403, 'Your agent account does not have access to this service.');
        }

        return $next($request);
    }
}
