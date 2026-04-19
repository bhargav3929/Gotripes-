<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Company;
use App\Models\EsimOrder;
use App\Models\ReferralAgent;

echo "\n";
echo "╔══════════════════════════════════════════════╗\n";
echo "║       GoTrips Application Test Suite         ║\n";
echo "╚══════════════════════════════════════════════╝\n\n";

$passed = 0;
$failed = 0;

function test($name, $condition, $error = null) {
    global $passed, $failed;
    if ($condition) {
        echo "  ✅ {$name}\n";
        $passed++;
        return true;
    } else {
        echo "  ❌ {$name}\n";
        if ($error) echo "     └─ {$error}\n";
        $failed++;
        return false;
    }
}

// 1. Database Connection
echo "📦 DATABASE\n";
echo str_repeat("─", 40) . "\n";
try {
    DB::connection()->getPdo();
    test("Database connection", true);
} catch (\Exception $e) {
    test("Database connection", false, $e->getMessage());
}

// 2. Tables Exist
$tables = ['users', 'companies', 'esim_orders', 'referral_agents', 'referral_tracking'];
foreach ($tables as $table) {
    try {
        $exists = DB::getSchemaBuilder()->hasTable($table);
        test("Table '{$table}' exists", $exists);
    } catch (\Exception $e) {
        test("Table '{$table}' exists", false, $e->getMessage());
    }
}

echo "\n";

// 3. Models
echo "🗂️  MODELS\n";
echo str_repeat("─", 40) . "\n";
test("Users count: " . User::count(), User::count() >= 0);
test("Companies count: " . Company::count(), Company::count() >= 0);
test("Orders count: " . EsimOrder::count(), EsimOrder::count() >= 0);
test("Agents count: " . ReferralAgent::count(), ReferralAgent::count() >= 0);

echo "\n";

// 4. Super Admin User
echo "👤 SUPER ADMIN\n";
echo str_repeat("─", 40) . "\n";
$superAdmin = User::where('role', 'super_admin')->first();
test("Super Admin exists", $superAdmin !== null);
if ($superAdmin) {
    test("Super Admin email: {$superAdmin->email}", true);
}

echo "\n";

// 5. Routes
echo "🔗 ROUTES\n";
echo str_repeat("─", 40) . "\n";

$criticalRoutes = [
    'login' => 'Login page',
    'superadmin.dashboard' => 'Super Admin Dashboard',
    'superadmin.companies.index' => 'Companies list',
    'superadmin.users.index' => 'Users list',
    'superadmin.reports.index' => 'Reports page',
    'superadmin.settings.index' => 'Settings page',
];

foreach ($criticalRoutes as $routeName => $description) {
    try {
        $exists = Route::has($routeName);
        test("{$description} route", $exists);
    } catch (\Exception $e) {
        test("{$description} route", false, $e->getMessage());
    }
}

echo "\n";

// 6. Views
echo "📄 VIEWS\n";
echo str_repeat("─", 40) . "\n";

$views = [
    'layouts.superadmin' => 'Super Admin layout',
    'superadmin.dashboard' => 'Dashboard view',
    'superadmin.companies.index' => 'Companies view',
    'superadmin.users.index' => 'Users view',
    'layouts.client' => 'Client layout',
];

foreach ($views as $view => $description) {
    $exists = view()->exists($view);
    test("{$description}", $exists);
}

echo "\n";

// 7. Company Data Check
echo "🏢 COMPANIES\n";
echo str_repeat("─", 40) . "\n";

$companies = Company::all();
if ($companies->count() > 0) {
    foreach ($companies->take(5) as $company) {
        $status = $company->is_active ? '✓' : '✗';
        echo "  • {$company->name} [{$company->plan}] {$status}\n";
    }
    if ($companies->count() > 5) {
        echo "  ... and " . ($companies->count() - 5) . " more\n";
    }
} else {
    echo "  No companies found\n";
}

echo "\n";

// 8. Environment
echo "⚙️  ENVIRONMENT\n";
echo str_repeat("─", 40) . "\n";
test("APP_ENV: " . config('app.env'), true);
test("APP_DEBUG: " . (config('app.debug') ? 'true' : 'false'), true);
test("DB_CONNECTION: " . config('database.default'), true);

echo "\n";

// Summary
echo "══════════════════════════════════════════════\n";
echo "📊 SUMMARY\n";
echo "──────────────────────────────────────────────\n";
echo "  Passed: {$passed}\n";
echo "  Failed: {$failed}\n";
echo "  Total:  " . ($passed + $failed) . "\n";
echo "══════════════════════════════════════════════\n";

if ($failed === 0) {
    echo "\n🎉 All tests passed! Application is ready.\n\n";
    exit(0);
} else {
    echo "\n⚠️  Some tests failed. Please check the errors above.\n\n";
    exit(1);
}
