<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        // Special-case the freelancer platform subdomain. When a visitor hits
        // freelancers.gotrips.ai/ we want them on the freelancer landing,
        // not the main B2C homepage.
        $host = $request->getHost();
        $parts = explode('.', $host);
        $firstPart = $parts[0] ?? '';
        if (in_array($firstPart, ['freelancer', 'freelancers'], true) && count($parts) >= 3) {
            $path = trim($request->path(), '/');
            // Allow asset, partner, freelancer, login, logout, dashboard paths through.
            $allowPrefixes = ['freelancer', 'partner', 'assets', 'storage', 'login', 'logout', 'css', 'js', 'images'];
            $isAllowed = $path === '' ? false : false;
            foreach ($allowPrefixes as $prefix) {
                if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                    $isAllowed = true;
                    break;
                }
            }
            if ($path === '') {
                return redirect()->route('freelancer.landing');
            }
            if (!$isAllowed && !str_starts_with($path, 'api/')) {
                return redirect()->route('freelancer.landing');
            }
            return $next($request);
        }

        $company = $this->identifyCompany($request);

        if ($company) {
            // Bind company to container
            app()->instance('current_company', $company);

            // Share with all views
            view()->share('company', $company);
            view()->share('branding', $company->branding);

            // Check if company is active
            if (!$company->is_active) {
                abort(403, 'This account has been suspended. Please contact support.');
            }

            // Check subscription status (skip for trial and super admin routes)
            if ($company->isExpired() && !$company->isOnTrial()) {
                if (!$request->is('subscription*', 'billing*', 'logout', 'superadmin*', 'login*', 'admin*')) {
                    abort(402, 'Your subscription has expired. Please contact support to renew.');
                }
            }
        }

        return $next($request);
    }

    protected function identifyCompany(Request $request): ?Company
    {
        // 1. Check for company in session (if already identified)
        if (session()->has('company_id')) {
            $company = Company::find(session('company_id'));
            if ($company) {
                return $company;
            }
        }

        // 2. Check for custom domain
        $host = $request->getHost();
        $company = Company::where('domain', $host)->first();
        if ($company) {
            session(['company_id' => $company->id]);
            return $company;
        }

        // 3. Check for subdomain
        $subdomain = $this->getSubdomain($host);
        if ($subdomain && $subdomain !== 'www' && $subdomain !== 'admin') {
            $company = Company::where('subdomain', $subdomain)->first();
            if ($company) {
                session(['company_id' => $company->id]);
                return $company;
            }
        }

        // 4. Check for company slug in route parameter
        if ($request->route('company')) {
            $company = Company::where('slug', $request->route('company'))->first();
            if ($company) {
                session(['company_id' => $company->id]);
                return $company;
            }
        }

        // 5. Check logged-in user's company
        if (auth()->check() && auth()->user()->company_id) {
            $company = Company::find(auth()->user()->company_id);
            if ($company) {
                session(['company_id' => $company->id]);
                return $company;
            }
        }

        // 6. Return default/main company if exists
        $defaultCompany = Company::where('slug', 'gotrips')->first();
        if ($defaultCompany) {
            return $defaultCompany;
        }

        return null;
    }

    protected function getSubdomain(string $host): ?string
    {
        $parts = explode('.', $host);

        // If localhost or IP, no subdomain
        if (count($parts) <= 2 || $host === 'localhost') {
            return null;
        }

        // Remove www if present
        if ($parts[0] === 'www') {
            array_shift($parts);
        }

        // The first part is the subdomain
        if (count($parts) > 2) {
            return $parts[0];
        }

        return null;
    }
}
