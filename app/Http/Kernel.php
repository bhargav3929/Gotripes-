<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            // IdentifyTenant MUST run before SubstituteBindings — the global
            // CompanyScope on tenant-owned models reads current_company_id()
            // when route-binding resolves a model. If IdentifyTenant runs
            // after, the scope falls through to fail-closed and route binding
            // 404s on every tenant-scoped route. (Discovered Step C, after
            // /manager/orders/esim/14 returned 404 for a row owned by gotrips.)
            \App\Http\Middleware\IdentifyTenant::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\ReferralTrackingMiddleware::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'isAdmin' => \App\Http\Middleware\AdminMiddleware::class,
        'isActivitiesManager' => \App\Http\Middleware\ActivitiesManagerMiddleware::class,
        'partner.access' => \App\Http\Middleware\PartnerAccessMiddleware::class,
        'manager.auth' => \App\Http\Middleware\ManagerAuthMiddleware::class,
        'referral.agent' => \App\Http\Middleware\ReferralAgentMiddleware::class,
        'referral.tracking' => \App\Http\Middleware\ReferralTrackingMiddleware::class,
        'tenant' => \App\Http\Middleware\IdentifyTenant::class,
        'tenant.feature' => \App\Http\Middleware\EnsureTenantFeature::class,
        'super.admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
    ];
}
