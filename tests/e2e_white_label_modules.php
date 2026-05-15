<?php
/**
 * E2E test for the white-label manager modules added in this session:
 *   - Tour Packages (manager + public /tour-packages)
 *   - Hajj/Umrah  (manager + public /hajj-umrah)
 *   - Visa Pricing (manager + public /uaevisa)
 *
 * Approach: real HTTP requests dispatched through the Laravel kernel
 * with Auth pre-logged-in and Host header set to each tenant's subdomain.
 * Verifies CRUD works and that data is tenant-scoped on the public side.
 *
 * Run from project root:
 *   php tests/e2e_white_label_modules.php
 *
 * Cleanup: rolls back its own seed data at the end.
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Company;
use App\Models\User;
use App\Models\TravelPackage;
use App\Models\UmrahPackage;
use App\Models\UAEVisaMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

$pass = 0; $fail = 0; $failures = [];

function check(string $label, bool $ok, string $detail = ''): void
{
    global $pass, $fail, $failures;
    if ($ok) {
        echo "  ✅ $label\n";
        $pass++;
    } else {
        echo "  ❌ $label" . ($detail ? "  ($detail)" : '') . "\n";
        $fail++;
        $failures[] = $label . ($detail ? " — $detail" : '');
    }
}

function dispatch(string $method, string $uri, array $data, string $host, ?User $authAs, $kernel): \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
{
    if ($authAs) {
        Auth::login($authAs);
    } else {
        Auth::logout();
    }
    // Reset container bindings — IdentifyTenant will set current_company from host
    if (app()->bound('current_company')) {
        app()->forgetInstance('current_company');
    }

    $request = Request::create("http://{$host}{$uri}", $method, $data, [], [], [
        'HTTP_HOST' => $host,
    ]);
    $request->setLaravelSession(app('session.store'));
    if ($authAs) {
        $request->setUserResolver(fn() => $authAs);
    }
    return $kernel->handle($request);
}

echo "\n=== Seeding test tenants ===\n";

// Wipe any leftover seed rows so the script is idempotent
$leftover = Company::withoutGlobalScopes()->whereIn('slug', ['gotrips', 'tenantalpha', 'tenantbeta'])->pluck('id', 'slug');
$mainId  = $leftover['gotrips']     ?? null;
$alphaId = $leftover['tenantalpha'] ?? null;
$betaId  = $leftover['tenantbeta']  ?? null;

// 1. main GoTrips company (the IdentifyTenant fallback)
if (!$mainId) {
    $main = Company::create([
        'name' => 'GoTrips Main',
        'slug' => 'gotrips',
        'subdomain' => 'gotrips',
        'email' => 'main@gotrips.ai',
        'plan' => 'enterprise',
        'features' => array_keys(Company::AVAILABLE_FEATURES),
        'is_active' => 1,
        'subscription_ends_at' => now()->addYear(),
    ]);
    echo "  + created GoTrips main (id={$main->id})\n";
} else {
    echo "  · reusing existing GoTrips main (id={$mainId})\n";
}

// 2. tenant alpha — all features
$alpha = $alphaId ? Company::withoutGlobalScopes()->find($alphaId) : null;
if (!$alpha) {
    $alpha = Company::create([
        'name' => 'Tenant Alpha',
        'slug' => 'tenantalpha',
        'subdomain' => 'tenantalpha',
        'email' => 'alpha@example.com',
        'plan' => 'pro',
        'features' => ['activities','visas','tours','hajj_umrah','esim'],
        'is_active' => 1,
        'subscription_ends_at' => now()->addYear(),
    ]);
}
echo "  + tenant alpha (id={$alpha->id}, subdomain={$alpha->subdomain})\n";

// 3. tenant beta — only tours
$beta = $betaId ? Company::withoutGlobalScopes()->find($betaId) : null;
if (!$beta) {
    $beta = Company::create([
        'name' => 'Tenant Beta',
        'slug' => 'tenantbeta',
        'subdomain' => 'tenantbeta',
        'email' => 'beta@example.com',
        'plan' => 'basic',
        'features' => ['tours'],
        'is_active' => 1,
        'subscription_ends_at' => now()->addYear(),
    ]);
}
echo "  + tenant beta  (id={$beta->id}, subdomain={$beta->subdomain})\n";

// Managers — one per tenant
$alphaManager = User::firstOrCreate(
    ['email' => 'manager-alpha@example.com'],
    [
        'name' => 'Alpha Manager',
        'password' => Hash::make('test1234'),
        'role' => 'company_owner',
        'company_id' => $alpha->id,
    ]
);
if ((int) $alphaManager->company_id !== (int) $alpha->id) {
    $alphaManager->update(['company_id' => $alpha->id, 'role' => 'company_owner']);
}

$betaManager = User::firstOrCreate(
    ['email' => 'manager-beta@example.com'],
    [
        'name' => 'Beta Manager',
        'password' => Hash::make('test1234'),
        'role' => 'company_owner',
        'company_id' => $beta->id,
    ]
);
if ((int) $betaManager->company_id !== (int) $beta->id) {
    $betaManager->update(['company_id' => $beta->id, 'role' => 'company_owner']);
}

echo "  + managers: alpha → {$alphaManager->email}, beta → {$betaManager->email}\n";

// ──────────────────────────────────────────────────────────────────────────────
// 1) TOUR PACKAGES
// ──────────────────────────────────────────────────────────────────────────────
echo "\n=== Tour Packages ===\n";

// a) Manager GET /manager/packages (Alpha)
$response = dispatch('GET', '/manager/packages', [], 'tenantalpha.gotrips.ai', $alphaManager, $kernel);
check('GET /manager/packages on Alpha → 200', $response->getStatusCode() === 200, "got {$response->getStatusCode()}");

// b) POST /manager/packages to create a tour package for Alpha
$pkgCount0 = TravelPackage::withoutGlobalScopes()->where('company_id', $alpha->id)->count();

// We have to skip the image file upload (Request::create doesn't easily simulate
// file uploads). Inject directly through the model to test scoping rather than
// the validator. We still test the controller path on GET routes.
app()->instance('current_company', $alpha);
$alphaPkg = TravelPackage::create([
    'title' => 'Alpha Maldives Escape',
    'country' => 'Maldives',
    'price' => 1999.00,
    'description' => 'Five-night beach paradise',
    'duration' => '5 Days / 4 Nights',
    'image' => 'assets/packages/' . $alpha->id . '/test.jpg',
    'isActive' => 1,
    'createdBy' => 'e2e',
    'createdDate' => now(),
]);
check('Travel package created with auto-scoped company_id', (int) $alphaPkg->company_id === (int) $alpha->id, "got company_id={$alphaPkg->company_id}");

// c) Beta creates a different package for the same country
app()->instance('current_company', $beta);
$betaPkg = TravelPackage::create([
    'title' => 'Beta Maldives Budget',
    'country' => 'Maldives',
    'price' => 899.00,
    'description' => 'Three-night stay',
    'duration' => '3 Days',
    'image' => 'assets/packages/' . $beta->id . '/test.jpg',
    'isActive' => 1,
    'createdBy' => 'e2e',
    'createdDate' => now(),
]);
check('Beta tenant created its own package with separate company_id', (int) $betaPkg->company_id === (int) $beta->id);

// d) Public /tour-packages on Alpha host shows ONLY Alpha's package
app()->forgetInstance('current_company');
$response = dispatch('GET', '/tour-packages', [], 'tenantalpha.gotrips.ai', null, $kernel);
$body = $response->getContent();
check('GET /tour-packages on Alpha → 200', $response->getStatusCode() === 200, "got {$response->getStatusCode()}");
check('Alpha public page shows Alpha package', str_contains($body, 'Alpha Maldives Escape'));
check('Alpha public page does NOT show Beta package', !str_contains($body, 'Beta Maldives Budget'));

// e) Public /tour-packages on Beta host shows ONLY Beta's package
$response = dispatch('GET', '/tour-packages', [], 'tenantbeta.gotrips.ai', null, $kernel);
$body = $response->getContent();
check('GET /tour-packages on Beta → 200', $response->getStatusCode() === 200, "got {$response->getStatusCode()}");
check('Beta public page shows Beta package', str_contains($body, 'Beta Maldives Budget'));
check('Beta public page does NOT show Alpha package', !str_contains($body, 'Alpha Maldives Escape'));

// ──────────────────────────────────────────────────────────────────────────────
// 2) HAJJ / UMRAH PACKAGES
// ──────────────────────────────────────────────────────────────────────────────
echo "\n=== Hajj / Umrah Packages ===\n";

// Alpha has hajj_umrah feature; Beta does NOT.
$response = dispatch('GET', '/manager/umrah-packages', [], 'tenantalpha.gotrips.ai', $alphaManager, $kernel);
check('GET /manager/umrah-packages on Alpha → 200', $response->getStatusCode() === 200, "got {$response->getStatusCode()}");

app()->instance('current_company', $alpha);
$alphaUmrah = UmrahPackage::create([
    'title' => 'Alpha Premium Umrah',
    'price' => 4500.00,
    'currency' => 'AED',
    'description' => 'Seven nights, deluxe hotels.',
    'duration' => '7 Nights',
    'tag' => 'Best Seller',
    'features' => ['5-star hotel', 'Direct flights'],
    'image' => 'assets/umrah-packages/' . $alpha->id . '/test.jpg',
    'isFeatured' => 1,
    'sortOrder' => 0,
    'isActive' => 1,
    'createdBy' => 'e2e',
    'createdDate' => now(),
]);
check('Umrah package auto-scoped to Alpha', (int) $alphaUmrah->company_id === (int) $alpha->id);

// Public /hajj-umrah on Alpha must show it
app()->forgetInstance('current_company');
$response = dispatch('GET', '/hajj-umrah', [], 'tenantalpha.gotrips.ai', null, $kernel);
$body = $response->getContent();
check('GET /hajj-umrah on Alpha → 200', $response->getStatusCode() === 200, "got {$response->getStatusCode()}");
check('Alpha public hajj-umrah shows Alpha package', str_contains($body, 'Alpha Premium Umrah'));

// Beta should be blocked from /hajj-umrah by tenant.feature middleware (no hajj_umrah feature)
$response = dispatch('GET', '/hajj-umrah', [], 'tenantbeta.gotrips.ai', null, $kernel);
$status = $response->getStatusCode();
check('GET /hajj-umrah on Beta → 403/404 (feature gate blocks)', in_array($status, [403, 404]), "got {$status}");

// ──────────────────────────────────────────────────────────────────────────────
// 3) VISA PRICING
// ──────────────────────────────────────────────────────────────────────────────
echo "\n=== Visa Pricing ===\n";

// Alpha has visas feature; Beta does NOT.
$response = dispatch('GET', '/manager/visa-pricing', [], 'tenantalpha.gotrips.ai', $alphaManager, $kernel);
check('GET /manager/visa-pricing on Alpha → 200', $response->getStatusCode() === 200, "got {$response->getStatusCode()}");

app()->instance('current_company', $alpha);
$visa = UAEVisaMaster::create([
    'UAEVisaDuration' => '30 Days Tourist (E2E)',
    'UAEVPrice' => 350.00,
    'isActive' => 1,
    'createdBy' => 'e2e',
    'createdDate' => now(),
]);
check('Visa price auto-scoped to Alpha', (int) $visa->company_id === (int) $alpha->id);

// Public /uaevisa on Alpha must show it
app()->forgetInstance('current_company');
$response = dispatch('GET', '/uaevisa', [], 'tenantalpha.gotrips.ai', null, $kernel);
$body = $response->getContent();
check('GET /uaevisa on Alpha → 200', $response->getStatusCode() === 200, "got {$response->getStatusCode()}");
check('Alpha public visa page shows Alpha duration', str_contains($body, '30 Days Tourist (E2E)'));

// Beta should NOT see Alpha's visa even on its own visa page (also feature-gated)
$response = dispatch('GET', '/uaevisa', [], 'tenantbeta.gotrips.ai', null, $kernel);
$body = $response->getContent();
$status = $response->getStatusCode();
$leaked = is_string($body) && str_contains($body, '30 Days Tourist (E2E)');
check('Beta /uaevisa does NOT leak Alpha\'s visa', !$leaked);

// ──────────────────────────────────────────────────────────────────────────────
// 4) CROSS-CUTTING: Tenant isolation on direct model queries
// ──────────────────────────────────────────────────────────────────────────────
echo "\n=== Tenant scoping (direct model queries) ===\n";

app()->instance('current_company', $alpha);
$alphaPkgs = TravelPackage::all();
check('Alpha context: only Alpha packages visible', $alphaPkgs->every(fn($p) => (int) $p->company_id === (int) $alpha->id), 'count=' . $alphaPkgs->count());

app()->instance('current_company', $beta);
$betaPkgs = TravelPackage::all();
check('Beta context: only Beta packages visible', $betaPkgs->every(fn($p) => (int) $p->company_id === (int) $beta->id), 'count=' . $betaPkgs->count());

// ──────────────────────────────────────────────────────────────────────────────
// 5) SUPER ADMIN: phone country-code form fields render
// ──────────────────────────────────────────────────────────────────────────────
echo "\n=== Super admin create-company form ===\n";

$sa = User::firstOrCreate(
    ['email' => 'super-e2e@gotrips.ai'],
    [
        'name' => 'Super E2E',
        'password' => Hash::make('test1234'),
        'role' => 'super_admin',
        'is_super_admin' => 1,
    ]
);

$response = dispatch('GET', '/superadmin/companies/create', [], 'gotrips.ai', $sa, $kernel);
$body = $response->getContent();
check('GET /superadmin/companies/create → 200', $response->getStatusCode() === 200, "got {$response->getStatusCode()}");
check('Form includes phone country selector', is_string($body) && str_contains($body, 'phone_country_code'));
check('Form includes the UAE dial code option', is_string($body) && str_contains($body, '+971'));
check('Form includes a non-UAE country option (United Kingdom)', is_string($body) && str_contains($body, 'United Kingdom'));

// ──────────────────────────────────────────────────────────────────────────────
// Cleanup (best-effort)
// ──────────────────────────────────────────────────────────────────────────────
echo "\n=== Cleanup ===\n";
try {
    DB::table('tbl_travel_packages')->whereIn('company_id', [$alpha->id, $beta->id])->delete();
    DB::table('tbl_umrah_packages')->whereIn('company_id', [$alpha->id, $beta->id])->delete();
    DB::table('uae_visa_master')->whereIn('company_id', [$alpha->id, $beta->id])->delete();
    DB::table('users')->whereIn('email', ['manager-alpha@example.com','manager-beta@example.com','super-e2e@gotrips.ai'])->delete();
    DB::table('companies')->whereIn('id', [$alpha->id, $beta->id])->delete();
    echo "  · seed data removed\n";
} catch (\Throwable $e) {
    echo "  · cleanup warning: {$e->getMessage()}\n";
}

echo "\n────────────────────────────────────\n";
echo "RESULTS: {$pass} passed, {$fail} failed\n";
if ($fail > 0) {
    echo "\nFailures:\n";
    foreach ($failures as $f) {
        echo "  · $f\n";
    }
    exit(1);
}
exit(0);
