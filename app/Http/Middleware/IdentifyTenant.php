<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        // Keep generated URLs on the current host (white-labelled subdomains
        // would otherwise bounce back to APP_URL).
        URL::forceRootUrl($request->getSchemeAndHttpHost());

        $host = $request->getHost();

        // Special-case: freelancers.gotrips.ai bypasses tenant identification
        // and only allows freelancer/partner/asset/login routes through.
        if ($this->isFreelancerSubdomain($host)) {
            return $this->handleFreelancerSubdomain($request, $next);
        }

        $company = $this->resolveCompanyFromHost($host);

        if (!$company) {
            // Fail safe: when no host matches and no default exists, do NOT
            // bind a company. Downstream scopes will fail closed and return
            // no rows — better than leaking another tenant's data.
            return $next($request);
        }

        // Suspended tenant
        if (!$company->is_active) {
            abort(403, 'This account has been suspended. Please contact support.');
        }

        // Expired subscription (allow billing/login/superadmin paths through)
        if ($company->isExpired() && !$company->isOnTrial()) {
            $allowList = ['subscription*', 'billing*', 'logout', 'superadmin*', 'login*', 'admin*'];
            if (!$request->is(...$allowList)) {
                abort(402, 'Your subscription has expired. Please contact support to renew.');
            }
        }

        // Bind into container so app('current_company') and current_company() work everywhere
        app()->instance('current_company', $company);

        // Convenience for views
        view()->share('company', $company);
        view()->share('branding', $company->branding);

        return $next($request);
    }

    /**
     * Resolve the tenant company purely from the request host.
     * Order: custom domain → subdomain → default GoTrips fallback.
     * Never reads from session or auth — those can lie across tenants.
     */
    protected function resolveCompanyFromHost(string $host): ?Company
    {
        // 1. Exact custom domain match (e.g., agency.com)
        $company = Company::where('domain', $host)->first();
        if ($company) {
            return $company;
        }

        // 2. Subdomain match (e.g., fortune.gotrips.ai → 'fortune')
        $subdomain = $this->extractSubdomain($host);
        if ($subdomain && !in_array($subdomain, ['www', 'admin'], true)) {
            $company = Company::where('subdomain', $subdomain)->first();
            if ($company) {
                return $company;
            }
        }

        // 3. Default to the GoTrips main company so the apex domain
        //    (gotrips.ai, 127.0.0.1, localhost) keeps working.
        return Company::where('slug', 'gotrips')->first();
    }

    protected function extractSubdomain(string $host): ?string
    {
        // No subdomain on bare localhost or IP addresses
        if ($host === 'localhost' || filter_var($host, FILTER_VALIDATE_IP)) {
            return null;
        }

        $parts = explode('.', $host);

        // Need at least sub.domain.tld (3 parts) to have a real subdomain
        if (count($parts) < 3) {
            return null;
        }

        // Strip leading www
        if ($parts[0] === 'www') {
            array_shift($parts);
            if (count($parts) < 3) {
                return null;
            }
        }

        return $parts[0];
    }

    protected function isFreelancerSubdomain(string $host): bool
    {
        $first = explode('.', $host)[0] ?? '';
        return in_array($first, ['freelancer', 'freelancers'], true)
            && substr_count($host, '.') >= 2;
    }

    protected function handleFreelancerSubdomain(Request $request, Closure $next): Response
    {
        $path = trim($request->path(), '/');
        $allowPrefixes = ['freelancer', 'partner', 'assets', 'storage', 'login', 'logout', 'css', 'js', 'images'];

        if ($path === '') {
            return redirect('/freelancer');
        }

        foreach ($allowPrefixes as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return $next($request);
            }
        }

        if (!str_starts_with($path, 'api/')) {
            return redirect('/freelancer');
        }

        return $next($request);
    }
}
