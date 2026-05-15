<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\ConventionController;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReferrerController;
// use App\Http\Controllers\CCAvenueController; // Deprecated — replaced by Nomod
use App\Http\Controllers\NomodController;
use App\Http\Controllers\AgentBookingController;
use App\Http\Controllers\ApiProxyController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\UAEActivityController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\AnnouncementAdminController;
use App\Http\Controllers\Admin\CarouselAdminController;
use App\Http\Controllers\Admin\UAEActivityAdminController;
use App\Http\Controllers\EmiratesController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UAEDetailsController;
use App\Http\Controllers\Admin\TravelPackageController;
use App\Http\Controllers\Admin\UmrahPackageController;
use App\Http\Controllers\UmrahPaymentController;
use App\Http\Controllers\EsimController;

// Search API
Route::get('/api/search', [SearchController::class, 'search'])->name('search');

Route::get('/', function () {

    return view('welcome');
});
Route::get('/admin', function () {
    return view('auth.login');
});


use App\Http\Controllers\UserController;






Route::get('/admin/', [AdminController::class, 'index'])
    ->middleware('auth')
    ->name('admin.dashboard');


Route::post('/register', [UserController::class, 'register'])->name('register');

// Public registration routes
Route::get('/get-emirates', [UserController::class, 'getEmirates'])->name('get.emirates');
// Route::post('/register', [UserController::class, 'register'])->name('register');

// Admin routes (protected by auth + isAdmin middleware)
Route::middleware(['auth', 'isAdmin'])->prefix('admin')->name('admin.')->group(function () {

    // Partner status management route
    Route::put('/partner-status/{userId}', [UserController::class, 'updatePartnerStatus'])->name('partner.status');

    // Additional partner management routes
    Route::get('/pending-partners', [UserController::class, 'getPendingPartners'])->name('pending.partners');

});



Route::prefix('/')->group(function () {
    Route::get('hajj-umrah', function () {
        $umrahPackages = \App\Models\UmrahPackage::where('isActive', 1)
            ->orderBy('sortOrder', 'asc')
            ->orderBy('createdDate', 'desc')
            ->get();
        return view('hajj-umrah', compact('umrahPackages'));
    })->middleware('tenant.feature:hajj_umrah')->name('hajj-umrah');
    Route::get('our-services', fn() => view('our-services'))->name('our-services');
    Route::get('banner0', fn() => view('banner0'));
    Route::get('countriestour', fn() => view('countriestour'))->middleware('tenant.feature:tours');
    Route::get('tour-packages', function () {
        $packages = \App\Models\TravelPackage::where('isActive', 1)
            ->orderBy('country')
            ->orderBy('createdDate', 'desc')
            ->get()
            ->groupBy(fn($p) => $p->country ?: 'Other');
        return view('tour-packages', compact('packages'));
    })->middleware('tenant.feature:tours')->name('tour-packages');
    Route::get('ourstory', fn() => view('ourstory'));
    Route::get('contact-us', fn() => view('contact-us'));
    Route::get('termsandconditions', fn() => view('termsandconditions'));
    Route::get('cancellationandrefundpolicy', fn() => view('cancellationandrefundpolicy'));
    Route::get('privacypolicy', fn() => view('privacypolicy'));
    // Route::get('dubai-global-village', fn() => view('dubai-global-village'));
    Route::get('lotus-cruise-dubai', fn() => view('lotus-cruise-dubai'));
    Route::get('shopnow', fn() => view('shopnow'))->middleware('tenant.feature:shop');
    Route::get('payonline', fn() => view('payonline'))->middleware('tenant.feature:pay_online');
    Route::get('lookingforajob', fn() => view('lookingforajob'))->middleware('tenant.feature:careers');
    Route::get('visaservice', fn() => view('visaservice'))->middleware('tenant.feature:visas');
    Route::get('uaevisa', fn() => view('uaevisa'))->middleware('tenant.feature:visas');
    Route::get('caro', fn() => view('uaecarousel'));


});




Route::get('/activities', [EmiratesController::class, 'index'])->middleware('tenant.feature:activities')->name('emirates.index');
Route::get('/activities/{slug}', [EmiratesController::class, 'showBySlug'])->middleware('tenant.feature:activities')->name('emirates.show');
Route::get('/Activities', fn() => redirect()->route('emirates.index'));
Route::get('/uaeactivities', fn() => redirect()->route('emirates.index'));
Route::get('/uaeactivities/{any}', fn() => redirect()->route('emirates.index'))->where('any', '.*');
Route::get('/api/emirates', [EmiratesController::class, 'getEmiratesJson'])->name('api.emirates');

//backend
// Admin routes restricted to full Admin only
Route::group(['middleware' => ['auth', 'isAdmin'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
    Route::delete('roles_mass_destroy', [\App\Http\Controllers\Admin\RoleController::class, 'massDestroy'])->name('roles.mass_destroy');

    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::delete('users_mass_destroy', [\App\Http\Controllers\Admin\UserController::class, 'massDestroy'])->name('users.mass_destroy');

    // Announcements
    Route::resource('announcements', AnnouncementAdminController::class);
    Route::post('announcements/{announcement}/toggle-status', [AnnouncementAdminController::class, 'toggleStatus'])
        ->name('announcements.toggle-status');

    // Homepage Carousel
    Route::resource('homepageads', CarouselAdminController::class);
    Route::post('homepageads/{homepagead}/toggle-status', [CarouselAdminController::class, 'toggleStatus'])
        ->name('homepageads.toggle-status');

    // Travel Packages
    Route::resource('packages', TravelPackageController::class)->except(['show']);

    // Umrah Packages
    Route::resource('umrah-packages', UmrahPackageController::class)->except(['show']);
});

// Admin routes accessible to both full Admin and Activities Manager
Route::group(['middleware' => ['auth', 'isActivitiesManager'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard.index');

    ///// UAE ACTIVITIES
    Route::get('uaeactivities', [UAEActivityAdminController::class, 'index'])->name('uaeactivities.index');
    Route::get('uaeactivities/create', [UAEActivityAdminController::class, 'create'])->name('uaeactivities.create');
    Route::post('uaeactivities', [UAEActivityAdminController::class, 'store'])->name('uaeactivities.store');
    Route::get('uaeactivities/{id}', [UAEActivityAdminController::class, 'show'])->name('uaeactivities.show');
    Route::get('uaeactivities/{id}/edit', [UAEActivityAdminController::class, 'edit'])->name('uaeactivities.edit');
    Route::put('uaeactivities/{id}', [UAEActivityAdminController::class, 'update'])->name('uaeactivities.update');
    Route::delete('uaeactivities/{id}', [UAEActivityAdminController::class, 'destroy'])->name('uaeactivities.destroy');
});



Route::get('/contact', function () {
    return view('contact'); // This should point to your contact form view file
})->name('contact');


Route::post('/contact-submit', [ContactController::class, 'submit'])->name('contact.submit');

use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\UAEVisaController;

Route::post('/job-application-submit', [JobApplicationController::class, 'submit'])->name('job.application.submit');
Route::post('/send-acknowledgement', [JobApplicationController::class, 'sendAcknowledgement'])->name('send.acknowledgement');
Route::post('/send-to-uae-recruiter', [JobApplicationController::class, 'sendToUaeRecruiter'])->name('send.to.uae.recruiter');

// Route::post('/iata-search', [LocationController::class, 'select2Search'])->name('banner1.auto');
// Route::post('/iata-form', fn() => view('banner1.auto'));


// Route to handle Select2 AJAX request
Route::get('/iata-search', [LocationController::class, 'select2Search'])->name('banner1.auto');


Route::get('/proxy-prices', [ApiProxyController::class, 'getPrices']);



// CCAvenue routes (deprecated — replaced by Nomod)
// Route::get('/ccavenue/initiate', function () { return view('ccavenue.initiate'); })->name('ccavenue.initiate');
// Route::post('/ccavenue/pay', [CCAvenueController::class, 'pay'])->name('ccavenue.pay');
// Route::any('/ccavenue/response', [CCAvenueController::class, 'response'])->name('ccavenue.response');

// Nomod payment callback routes
Route::get('/payment/nomod/success', [NomodController::class, 'success'])->name('nomod.success');
Route::get('/payment/nomod/failure', [NomodController::class, 'failure'])->name('nomod.failure');
Route::get('/payment/nomod/cancelled', [NomodController::class, 'cancelled'])->name('nomod.cancelled');

// Umrah package payment
Route::post('/umrah/payment/initiate', [UmrahPaymentController::class, 'initiate'])->name('umrah.payment.initiate');

Route::post('/uaev/submit', [UAEVisaController::class, 'submit'])->name('uaev.submit');


use App\Http\Controllers\PaymentController;

Route::get('/payment', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
Route::match(['get', 'post'], '/payment/initiate', [PaymentController::class, 'initiatePayment'])->name('payment.initiate');
Route::post('/payment/response', [PaymentController::class, 'paymentResponse'])->name('payment.response');
Route::post('/payment/cancel', [PaymentController::class, 'paymentCancel'])->name('payment.cancel');


Route::get('/activities/{emirateSlug}/{activitySlug}', [UAEDetailsController::class, 'showBySlug'])->name('activities.detail.slug');
Route::get('/activity-details', [UAEDetailsController::class, 'show'])->name('activities.detail');
Route::get('/dubai-global-village', [UAEDetailsController::class, 'show']); // Keep for backward compatibility

Route::get('/all-uae-activities', fn() => redirect()->route('emirates.index'));



use App\Http\Controllers\ActivityBookingController;

Route::get('/activity/book/{activityId}', [ActivityBookingController::class, 'show'])->name('activity.book.form');
Route::post('/activity/book', [ActivityBookingController::class, 'submit'])->name('activity.book');
Route::get('/activity/prices/{activityId?}', [ActivityBookingController::class, 'getActivityPrices']);


Route::get('/activity/pricing/{id}', function ($id) {
    $activity = DB::table('tbl_UAEActivities')->where('activityID', $id)->first();
    return response()->json([
        'activityPrice' => $activity ? (float) $activity->activityPrice : 0,
        'activityChildPrice' => ($activity && $activity->activityChildPrice && $activity->activityChildPrice > 0)
            ? (float) $activity->activityChildPrice
            : ($activity ? (float) $activity->activityPrice : 0),
        'activityTransactionCharges' => $activity ? (float) $activity->activityTransactionCharges : 0,
    ]);
})->name('activity.pricing');
// CCAvenue activity routes (deprecated — replaced by Nomod)
// Route::post('/ccavenue/callback', [\App\Http\Controllers\ActivityBookingController::class, 'ccavenueCallback'])->name('payment.ccavenue.callback');
// Route::post('/payment/ccavenue/response', [ActivityBookingController::class, 'handleResponse'])->name('payment.ccavenue.response');
// Route::get('/payment/ccavenue/cancel', function () { return view('payment_cancel'); })->name('payment.ccavenue.cancel');

use App\Http\Controllers\HomepageAdsController;

Route::get('/banner', [HomepageAdsController::class, 'showCarousel']);
Route::get('/', [HomepageAdsController::class, 'index']);

use App\Http\Controllers\AnnouncementController;

Route::get('/news-ticker', [AnnouncementController::class, 'index']);


use App\Models\UAEVisaMaster;

Route::get('/uaevisa', function () {
    $visaData = UAEVisaMaster::where('isActive', true)->get();
    // Add other variables as needed by your form
    return view('uaevisa', compact('visaData'));
})->middleware('tenant.feature:visas');


// CCAvenue initiate/response/cancel routes (deprecated — replaced by Nomod)
// Route::post('/ccavenue/initiate', [CCAvenueController::class, 'initiatePayment']);
// Route::post('/ccavenue/response', [CCAvenueController::class, 'handleResponse'])->name('ccavenue.response');
// Route::match(['get', 'post'], '/payment/ccavenue/cancel', [CCAvenueController::class, 'cancel'])->name('ccavenue.cancel');
// Route::get('/ccavenue-debug', function () { ... });
// Add this route alongside your existing activity routes
Route::post('/activity/payment/initiate', [ActivityBookingController::class, 'initiateActivityPayment'])->name('activity.payment.initiate');
Route::post('/agent/pay', [AgentBookingController::class, 'submit'])->name('agent.pay');

// ─── eSIM Routes ────────────────────────────────────────────────────
Route::get('/esim', [EsimController::class, 'index'])->middleware('tenant.feature:esim')->name('esim.index');
Route::get('/api/esim/countries', [EsimController::class, 'getCountries'])->name('esim.countries');
Route::post('/esim/bundles', [EsimController::class, 'getBundles'])->name('esim.bundles');
Route::post('/esim/purchase', [EsimController::class, 'purchase'])->name('esim.purchase');

Auth::routes(['register' => false]);

// ─── Manager Dashboard Routes ───────────────────────────────────────
use App\Http\Controllers\Manager\ManagerAuthController;
use App\Http\Controllers\Manager\ManagerDashboardController;
use App\Http\Controllers\Manager\ManagerAdSlotsController;
use App\Http\Controllers\Manager\ManagerAnnouncementsController;
use App\Http\Controllers\Manager\ManagerActivitiesController;
use App\Http\Controllers\Manager\ManagerTravelPackagesController;
use App\Http\Controllers\Manager\ManagerUmrahPackagesController;
use App\Http\Controllers\Manager\ManagerVisaPricingController;
use App\Http\Controllers\Manager\ManagerSettingsController;
use App\Http\Controllers\Manager\ManagerFinanceController;
use App\Http\Controllers\Manager\OrdersController;
use App\Http\Controllers\Manager\SettingsController;

Route::get('/manager/login', [ManagerAuthController::class, 'showLogin'])->name('manager.login');
Route::post('/manager/login', [ManagerAuthController::class, 'login'])->name('manager.login.submit');
Route::post('/manager/logout', [ManagerAuthController::class, 'logout'])->name('manager.logout');

Route::middleware(['manager.auth'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/', [ManagerDashboardController::class, 'index'])->name('dashboard');
    Route::resource('adslots', ManagerAdSlotsController::class);
    Route::resource('announcements', ManagerAnnouncementsController::class);
    Route::resource('activities', ManagerActivitiesController::class);

    // Tenant content: tour packages, hajj/umrah packages, visa pricing.
    // BelongsToCompany trait auto-scopes queries; CRUD is per-tenant.
    Route::resource('packages', ManagerTravelPackagesController::class)->except(['show']);
    Route::resource('umrah-packages', ManagerUmrahPackagesController::class)->except(['show']);
    // Visa pricing is a flat list of duration+price rows; CRUD is inline on the index page.
    Route::get('visa-pricing',                [ManagerVisaPricingController::class, 'index'])->name('visa-pricing.index');
    Route::post('visa-pricing',               [ManagerVisaPricingController::class, 'store'])->name('visa-pricing.store');
    Route::put('visa-pricing/{id}',           [ManagerVisaPricingController::class, 'update'])->name('visa-pricing.update');
    Route::delete('visa-pricing/{id}',        [ManagerVisaPricingController::class, 'destroy'])->name('visa-pricing.destroy');

    // Features are managed by super-admin via /superadmin/companies/{c}/edit.
    // Tenants get a read-only view here (no POST endpoint — see audit finding #13).
    Route::get('/settings/features', [ManagerSettingsController::class, 'features'])->name('settings.features');

    // Tenant Settings — profile/branding + preferences (Step D).
    Route::get('/settings/profile',      [SettingsController::class, 'profile'])->name('settings.profile');
    Route::post('/settings/profile',     [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::get('/settings/preferences',  [SettingsController::class, 'preferences'])->name('settings.preferences');
    Route::post('/settings/preferences', [SettingsController::class, 'updatePreferences'])->name('settings.preferences.update');

    // Finance: earnings, bookings, bank accounts, withdrawals
    Route::get('/finance', [ManagerFinanceController::class, 'index'])->name('finance.index');
    Route::get('/finance/bookings', [ManagerFinanceController::class, 'bookings'])->name('finance.bookings');
    Route::get('/finance/bank-accounts', [ManagerFinanceController::class, 'bankAccounts'])->name('finance.bank-accounts');
    Route::post('/finance/bank-accounts', [ManagerFinanceController::class, 'storeBankAccount'])->name('finance.bank-accounts.store');
    Route::delete('/finance/bank-accounts/{bank}', [ManagerFinanceController::class, 'deleteBankAccount'])->name('finance.bank-accounts.destroy');
    Route::get('/finance/withdrawals', [ManagerFinanceController::class, 'withdrawals'])->name('finance.withdrawals');
    Route::post('/finance/withdrawals', [ManagerFinanceController::class, 'requestWithdrawal'])->name('finance.withdrawals.request');

    // Orders & Bookings (Step C). All queries are tenant-scoped automatically
    // by the BelongsToCompany trait + CompanyScope global scope.
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/activities',                  [OrdersController::class, 'activities'])->name('activities');
        Route::get('/activities/{booking}',        [OrdersController::class, 'activityDetail'])->name('activities.show');
        Route::get('/esim',                        [OrdersController::class, 'esim'])->name('esim');
        Route::get('/esim/{order}',                [OrdersController::class, 'esimDetail'])->name('esim.show');
        Route::get('/visa',                        [OrdersController::class, 'visa'])->name('visa');
        Route::get('/visa/{application}',          [OrdersController::class, 'visaDetail'])->name('visa.show');
        Route::get('/flights-hotels',              [OrdersController::class, 'flightsHotels'])->name('flights-hotels');
    });
});

// GitHub auto-deploy webhook
Route::post('/deploy/github-webhook', function (\Illuminate\Http\Request $request) {
    $secret = env('GITHUB_WEBHOOK_SECRET', '');
    if (empty($secret)) {
        return response()->json(['error' => 'Webhook secret not configured'], 500);
    }

    $signature = $request->header('X-Hub-Signature-256', '');
    $payload = $request->getContent();

    $expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);
    if (!hash_equals($expected, $signature)) {
        return response()->json(['error' => 'Invalid signature'], 403);
    }

    // Handle ping
    if ($request->header('X-GitHub-Event') === 'ping') {
        return response()->json(['message' => 'pong']);
    }

    // Only deploy on push to main
    $data = $request->json()->all();
    if (($data['ref'] ?? '') !== 'refs/heads/main') {
        return response()->json(['message' => 'Skipped: not main branch']);
    }

    $projectDir = base_path();
    $logFile = storage_path('logs/deploy.log');
    $pusher = $data['pusher']['name'] ?? 'unknown';
    $commit = substr($data['head_commit']['message'] ?? 'unknown', 0, 80);

    $log = "\n===== WEBHOOK DEPLOY: " . date('Y-m-d H:i:s') . " =====\n";
    $log .= "Pusher: {$pusher}\nCommit: {$commit}\n\n";

    putenv('HOME=/home/u705168859');
    $commands = [
        "/usr/bin/git pull origin main",
        "/usr/local/bin/composer dump-autoload --no-interaction",
        "/usr/bin/php artisan route:clear",
        "/usr/bin/php artisan config:clear",
        "/usr/bin/php artisan cache:clear",
        "/usr/bin/php artisan view:clear",
        "/usr/bin/php artisan migrate --force",
    ];

    foreach ($commands as $cmd) {
        $descriptors = [1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $process = proc_open("cd {$projectDir} && {$cmd}", $descriptors, $pipes);
        $output = '';
        if (is_resource($process)) {
            $output = stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);
        }
        $log .= "> {$cmd}\n{$output}\n";
    }

    $log .= "Deploy completed at " . date('Y-m-d H:i:s') . "\n";
    file_put_contents($logFile, $log, FILE_APPEND);

    return response()->json(['success' => true, 'message' => 'Deploy completed', 'pusher' => $pusher]);
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// ─── Referral System Routes ─────────────────────────────────────────
use App\Http\Controllers\ReferralAgentDashboardController;
use App\Http\Controllers\ReferralAgentSignupController;
use App\Http\Controllers\ReferralBankAccountController;
use App\Http\Controllers\ReferralWithdrawalController;
use App\Http\Controllers\Admin\ReferralAgentController;
use App\Http\Controllers\Admin\ReferralCommissionController;
use App\Http\Controllers\Admin\ReferralSettingsController;
use App\Http\Controllers\Admin\ReferralWithdrawalAdminController;

// Referral Agent Authentication
Route::get('/partner/login', [ReferralAgentDashboardController::class, 'showLoginForm'])->name('referral.login');
Route::post('/partner/login', [ReferralAgentDashboardController::class, 'login'])->name('referral.login.submit');
Route::post('/partner/logout', [ReferralAgentDashboardController::class, 'logout'])->name('referral.logout');

// Referral Agent Public Signup
Route::get('/partner/register', [ReferralAgentSignupController::class, 'showRegister'])->name('referral.register');
Route::post('/partner/register', [ReferralAgentSignupController::class, 'register'])->name('referral.register.submit');

// ─── Freelancer Platform (freelancers.gotrips.ai) ──────────────────
use App\Http\Controllers\FreelancerSignupController;
Route::get('/freelancer', [FreelancerSignupController::class, 'landing'])->name('freelancer.landing');
Route::get('/freelancer/register', [FreelancerSignupController::class, 'showRegister'])->name('freelancer.register');
Route::post('/freelancer/register', [FreelancerSignupController::class, 'register'])->name('freelancer.register.submit');

// Referral Agent Dashboard (Protected)
Route::middleware(['referral.agent'])->prefix('partner')->name('referral.')->group(function () {
    Route::get('/', [ReferralAgentDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [ReferralAgentDashboardController::class, 'orders'])->name('orders');
    Route::get('/earnings', [ReferralAgentDashboardController::class, 'earnings'])->name('earnings');
    Route::get('/profile', [ReferralAgentDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [ReferralAgentDashboardController::class, 'updateProfile'])->name('profile.update');

    // Bank Accounts
    Route::get('/bank-accounts', [ReferralBankAccountController::class, 'index'])->name('bank-accounts');
    Route::post('/bank-accounts', [ReferralBankAccountController::class, 'store'])->name('bank-accounts.store');
    Route::delete('/bank-accounts/{account}', [ReferralBankAccountController::class, 'destroy'])->name('bank-accounts.destroy');
    Route::patch('/bank-accounts/{account}/primary', [ReferralBankAccountController::class, 'setPrimary'])->name('bank-accounts.primary');

    // Withdrawal Requests
    Route::get('/withdraw', [ReferralWithdrawalController::class, 'index'])->name('withdraw');
    Route::post('/withdraw', [ReferralWithdrawalController::class, 'store'])->name('withdraw.store');
});

// Admin Referral Management Routes
Route::middleware(['auth', 'isAdmin'])->prefix('admin/referrals')->name('admin.referrals.')->group(function () {
    // Dashboard
    Route::get('/', [ReferralAgentController::class, 'dashboard'])->name('dashboard');

    // Agents Management
    Route::get('/agents', [ReferralAgentController::class, 'index'])->name('agents.index');
    Route::get('/agents/create', [ReferralAgentController::class, 'create'])->name('agents.create');
    Route::post('/agents', [ReferralAgentController::class, 'store'])->name('agents.store');
    Route::get('/agents/{agent}', [ReferralAgentController::class, 'show'])->name('agents.show');
    Route::get('/agents/{agent}/edit', [ReferralAgentController::class, 'edit'])->name('agents.edit');
    Route::put('/agents/{agent}', [ReferralAgentController::class, 'update'])->name('agents.update');
    Route::delete('/agents/{agent}', [ReferralAgentController::class, 'destroy'])->name('agents.destroy');
    Route::post('/agents/{agent}/toggle-status', [ReferralAgentController::class, 'toggleStatus'])->name('agents.toggle-status');
    Route::post('/agents/{agent}/regenerate-code', [ReferralAgentController::class, 'regenerateCode'])->name('agents.regenerate-code');
    Route::get('/agents-export', [ReferralCommissionController::class, 'exportAgents'])->name('agents.export');

    // Commissions Management
    Route::get('/commissions', [ReferralCommissionController::class, 'index'])->name('commissions.index');
    Route::get('/commissions/{commission}', [ReferralCommissionController::class, 'show'])->name('commissions.show');
    Route::post('/commissions/{commission}/approve', [ReferralCommissionController::class, 'approve'])->name('commissions.approve');
    Route::post('/commissions/{commission}/reject', [ReferralCommissionController::class, 'reject'])->name('commissions.reject');
    Route::post('/commissions/{commission}/mark-paid', [ReferralCommissionController::class, 'markPaid'])->name('commissions.mark-paid');
    Route::post('/commissions/bulk-approve', [ReferralCommissionController::class, 'bulkApprove'])->name('commissions.bulk-approve');
    Route::post('/commissions/bulk-paid', [ReferralCommissionController::class, 'bulkMarkPaid'])->name('commissions.bulk-paid');
    Route::get('/commissions-export', [ReferralCommissionController::class, 'export'])->name('commissions.export');

    // Settings
    Route::get('/settings', [ReferralSettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [ReferralSettingsController::class, 'update'])->name('settings.update');

    // Withdrawal Requests (Admin)
    Route::get('/withdrawals', [ReferralWithdrawalAdminController::class, 'index'])->name('withdrawals.index');
    Route::post('/withdrawals/{withdrawal}/approve', [ReferralWithdrawalAdminController::class, 'approve'])->name('withdrawals.approve');
    Route::post('/withdrawals/{withdrawal}/complete', [ReferralWithdrawalAdminController::class, 'complete'])->name('withdrawals.complete');
    Route::post('/withdrawals/{withdrawal}/reject', [ReferralWithdrawalAdminController::class, 'reject'])->name('withdrawals.reject');
});

// ─── Super Admin Routes ─────────────────────────────────────────────
require __DIR__.'/superadmin.php';

// ─── Client Admin Routes ────────────────────────────────────────────
require __DIR__.'/client.php';

