<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate a route on a tenant feature flag.
 *
 * Usage in routes/web.php:
 *   Route::get('/hajj-umrah', ...)->middleware('tenant.feature:hajj_umrah');
 *
 * If no tenant is bound (e.g. main gotrips.ai), the route is allowed.
 * If the tenant has no `features` set, treat as full-access.
 */
class EnsureTenantFeature
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $company = app()->bound('current_company') ? app('current_company') : null;

        if (!$company instanceof Company) {
            return $next($request);
        }

        if (!$company->hasFeature($feature)) {
            abort(404, 'This service is not available on this site.');
        }

        return $next($request);
    }
}
