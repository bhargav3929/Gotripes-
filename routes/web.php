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
use App\Http\Controllers\FifaTicketsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UAEDetailsController;
use App\Http\Controllers\Admin\TravelPackageController;
use App\Http\Controllers\Admin\UmrahPackageController;
use App\Http\Controllers\UmrahPaymentController;
use App\Http\Controllers\EsimController;

// Search API
Route::get('/api/search', [SearchController::class, 'search'])->name('search');

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

// TEMPORARY: the job-application 500 persists even after the LFJLocationStatus
// column migration deployed. Confirm the column now exists, then run the exact
// same insert JobApplicationController::submit() does (including file paths)
// to capture the real exception. Read-only except the throwaway insert, which
// is deleted right after.
Route::get('/gt-diag-lfj3', function (\Illuminate\Http\Request $request) {
    if ($request->query('token') !== 'gt-diag-2026-x7k9') {
        abort(404);
    }
    $result = [];
    try {
        $cols = \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM tblLFJprofiles');
        $result['columns'] = array_map(fn ($c) => $c->Field, $cols);
    } catch (\Throwable $e) {
        $result['columns_error'] = $e->getMessage();
    }
    try {
        $result['status_rows'] = \Illuminate\Support\Facades\DB::table('tbl_LFJProfileStatus')->get()->toArray();
        $result['status_columns'] = array_map(fn ($c) => $c->Field, \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM tbl_LFJProfileStatus'));
    } catch (\Throwable $e) {
        $result['status_rows_error'] = $e->getMessage();
    }
    try {
        $p = new \App\Models\LFJprofile();
        $p->LFJCreatedBy = 'diag';
        $p->LFJCreatedDate = now();
        $p->LFJisActive = true;
        $p->LFJModifiedBy = null;
        $p->LFJModifiedDate = null;
        $p->LFJStatus = 'available';
        $p->LFJLocationStatus = 'inside_uae';
        $p->LFJName = 'Diag Test';
        $p->LFJAge = 28;
        $p->LFJNationality = 'Indian';
        $p->LFJMobile = '+971501234567';
        $p->LFJEmail = 'diag3_' . time() . '@example.com';
        $p->LFJProfession = 'Accountant';
        $p->LFJExperience = 5;
        $p->LFJVisaStatus = 'Visit Visa';
        $p->LFJExpectedSalary = '5000';
        $p->LFJLastCompany = 'Test Co';
        $p->LFJLastLocation = 'Dubai';
        $p->LFJPreferredLocation = 'Abu Dhabi';
        $p->LFJNoticePeriod = 'Immediate';
        $p->LFJReferenceName = 'Ref Test';
        $p->LFJReferencePosition = 'Manager';
        $p->LFJReferenceMobile = '+971509999999';
        $p->LFJResume = 'resumes/diag-test.png';
        $p->LFJPassport = 'passports/diag-test.png';
        $p->save();
        $result['insert'] = 'success';
        $result['id'] = $p->LFJid;
        $p->delete();
    } catch (\Throwable $e) {
        $result['insert_error'] = $e->getMessage();
    }
    return response()->json($result);
});


// Route::post('/register', [UserController::class, 'register'])->name('register');

// Admin routes (protected by auth + isAdmin middleware)
Route::middleware(['auth', 'isAdmin'])->prefix('admin')->name('admin.')->group(function () {

    // Partner status management route
    Route::put('/partner-status/{userId}', [UserController::class, 'updatePartnerStatus'])->name('partner.status');

    // Additional partner management routes
    Route::get('/pending-partners', [UserController::class, 'getPendingPartners'])->name('pending.partners');

});



Route::prefix('/')->group(function () {
    Route::middleware('tenant.feature:hajj_umrah')->group(function () {
        Route::get('hajj-umrah', function () {
            return redirect()->route('going-saudi.index');
        })->name('hajj-umrah');

        // Former URL — kept as a permanent redirect so existing bookmarks and any
        // links Google has already indexed at /umrah-visas keep working.
        Route::get('umrah-visas', function () {
            return redirect()->route('going-saudi.index', [], 301);
        });
        Route::get('umrah-visas/{id}', function ($id) {
            return redirect()->route('going-saudi.show', $id, 301);
        });

        Route::get('going-saudi', [\App\Http\Controllers\UmrahController::class, 'index'])->name('going-saudi.index');
        Route::get('going-saudi/{id}', [\App\Http\Controllers\UmrahController::class, 'show'])->name('going-saudi.show');
        Route::post('going-saudi/{id}/checkout', [\App\Http\Controllers\UmrahController::class, 'checkout'])->name('going-saudi.checkout');
        Route::post('going-saudi/passport-upload', [\App\Http\Controllers\UmrahController::class, 'uploadPassport'])->name('going-saudi.passport-upload');
        Route::get('saudi-visas', [\App\Http\Controllers\SaudiVisaController::class, 'index'])->name('saudi-visa.index');
        Route::post('saudi-visa/submit', [\App\Http\Controllers\SaudiVisaController::class, 'submit'])->name('saudi-visa.submit');
    });

    Route::get('our-services', fn() => view('our-services'))->name('our-services');
    Route::get('banner0', fn() => view('banner0'));
    Route::get('countriestour', fn() => view('countriestour'))->middleware('tenant.feature:tours');
    Route::get('tour-packages', function () {
        $all = \App\Models\TravelPackage::orderBy('country')
            ->orderBy('createdDate', 'desc')
            ->get();

        // Completed (published) packages — grouped by country, shown at the top.
        $packages = $all->where('isActive', true)
            ->groupBy(fn($p) => $p->country ?: 'Other');

        // Countries that only have pending (unpublished) packages → "Coming Soon".
        // This keeps every destination flag visible instead of hiding the others
        // the moment one package is published.
        $activeCountries = $packages->keys();
        $comingSoon = $all->where('isActive', false)
            ->map(fn($p) => $p->country ?: 'Other')
            ->unique()
            ->reject(fn($c) => $activeCountries->contains($c))
            ->values();

        return view('tour-packages', compact('packages', 'comingSoon'));
    })->middleware('tenant.feature:tours')->name('tour-packages');
    Route::get('tour-packages/{id}', function ($id) {
        $package = \App\Models\TravelPackage::where('isActive', 1)->findOrFail($id);
        $package->load('images');
        return view('tour-package-detail', compact('package'));
    })->whereNumber('id')->middleware('tenant.feature:tours')->name('tour-packages.show');

    // Customer enquiry for a tour package -> notifies the package's recipient emails.
    Route::post('tour-packages/{id}/enquire', [\App\Http\Controllers\PackageEnquiryController::class, 'submit'])
        ->whereNumber('id')->middleware('tenant.feature:tours')->name('tour-packages.enquire');

    // Dedicated country packages page — e.g. /tour-packages/canada
    // whereNumber on the route above ensures numeric IDs never reach here.
    Route::get('tour-packages/{country}', function (string $country) {
        $packages = \App\Models\TravelPackage::where('isActive', 1)
            ->get()
            ->filter(fn($p) => \Illuminate\Support\Str::slug($p->country ?? '') === $country)
            ->values();

        abort_if($packages->isEmpty(), 404);

        $countryName = $packages->first()->country;
        $flagEntry   = collect(\App\Support\CountryCodes::all())->firstWhere('name', $countryName);
        $flag        = $flagEntry['flag'] ?? '🌍';
        $heroImage   = $packages->first(fn($p) => !empty($p->image))?->image;
        $minPrice    = $packages->min('price');
        $maxPrice    = $packages->max('price');

        return view('tour-packages-country', compact('packages', 'countryName', 'flag', 'heroImage', 'minPrice', 'maxPrice'));
    })->middleware('tenant.feature:tours')->name('tour-packages.country');
    Route::get('ourstory', fn() => view('ourstory'));
    Route::get('contact-us', fn() => view('contact-us', ['enquiryPackage' => request('enquiry')]));
    Route::get('termsandconditions', fn() => view('termsandconditions'));
    Route::get('cancellationandrefundpolicy', fn() => view('cancellationandrefundpolicy'));
    Route::get('privacypolicy', fn() => view('privacypolicy'));
    // Route::get('dubai-global-village', fn() => view('dubai-global-village'));
    Route::get('lotus-cruise-dubai', fn() => view('lotus-cruise-dubai'));
    Route::get('events', fn() => view('events'))->name('events');
    Route::get('shopnow', fn() => view('shopnow'))->middleware('tenant.feature:shop');
    Route::get('payonline', fn() => view('payonline'))->middleware('tenant.feature:pay_online');
    Route::get('lookingforajob', fn() => view('lookingforajob'))->middleware('tenant.feature:careers');
    Route::get('visaservice', fn() => view('visaservice'))->middleware('tenant.feature:visas');
    Route::get('caro', fn() => view('uaecarousel'));

    // Coming Soon placeholders for newly-introduced menu items (services listed
    // after e-Visa that don't have a full page yet). Each shows a related picture.
    Route::get('coming-soon/{slug}', function ($slug) {
        $services = [
            'insurance'         => ['title' => 'Travel Insurance',        'icon' => 'bi-shield-check',  'tagline' => 'Comprehensive travel protection for every journey — launching soon.',          'img' => asset('assets/coming-soon/insurance.jpg')],
            'cruise'            => ['title' => 'Cruise',                  'icon' => 'bi-water',         'tagline' => 'Luxury cruise getaways and Dubai marina sailings — coming soon.',                'img' => asset('assets/coming-soon/cruise.jpg')],
            'events'            => ['title' => 'Events',                  'icon' => 'bi-calendar-event','tagline' => 'Concerts, shows and unforgettable live events — coming soon.',                   'img' => asset('assets/coming-soon/events.jpg')],
            'transport'         => ['title' => 'Transport',               'icon' => 'bi-car-front',     'tagline' => 'Premium chauffeur, transfers and car rentals — arriving shortly.',              'img' => asset('assets/coming-soon/transport.jpg')],
            'holiday-homes'     => ['title' => 'Holiday Homes',           'icon' => 'bi-house-heart',   'tagline' => 'Handpicked holiday homes and luxury stays — coming soon.',                       'img' => asset('assets/coming-soon/holiday-homes.jpg')],
            'business-tourism'  => ['title' => 'Business Tourism (MICE)',  'icon' => 'bi-briefcase',     'tagline' => 'Meetings, incentives, conferences and exhibitions — on the way.',                'img' => asset('assets/coming-soon/business-tourism.jpg')],
            'local-tours'       => ['title' => 'Local Tours',             'icon' => 'bi-geo-alt',       'tagline' => 'Curated local experiences and city tours — coming soon.',                        'img' => asset('assets/coming-soon/local-tours.jpg')],
            'festival-tours'    => ['title' => 'Festival Tours',          'icon' => 'bi-stars',         'tagline' => 'Travel built around the world\'s best festivals — launching soon.',              'img' => asset('assets/coming-soon/festival-tours.jpg')],
            'medical-tours'     => ['title' => 'Medical Tours',           'icon' => 'bi-heart-pulse',   'tagline' => 'Trusted medical travel and wellness journeys — arriving shortly.',               'img' => asset('assets/coming-soon/medical-tours.jpg')],
            'hotels'            => ['title' => 'Hotels',                  'icon' => 'bi-building',      'tagline' => 'Handpicked Makkah & Madinah hotels close to the Haram — coming soon.',            'img' => asset('assets/coming-soon/holiday-homes.jpg')],
            'land-packages'     => ['title' => 'Land Packages',           'icon' => 'bi-map',           'tagline' => 'Complete land-based Hajj & Umrah packages — coming soon.',                        'img' => asset('assets/coming-soon/local-tours.jpg')],
            'catering'          => ['title' => 'Catering Services',       'icon' => 'bi-cup-hot',       'tagline' => 'Full-board halal catering for your pilgrimage — coming soon.',                    'img' => asset('assets/coming-soon/events.jpg')],
            'study-abroad'      => ['title' => 'Study Abroad',            'icon' => 'bi-mortarboard',   'tagline' => 'University admissions, student visas and guidance for studying overseas — coming soon.', 'img' => asset('assets/index_files/study-overseas-educational-consultancy.jpg')],
        ];

        abort_unless(isset($services[$slug]), 404);

        return view('coming-soon', ['service' => $services[$slug]]);
    })->name('coming-soon');

});




Route::get('/activities', [EmiratesController::class, 'index'])->middleware('tenant.feature:activities')->name('emirates.index');
Route::get('/activities/{slug}', [EmiratesController::class, 'showBySlug'])->middleware('tenant.feature:activities')->name('emirates.show');
Route::get('/Activities', fn() => redirect()->route('emirates.index'));
Route::get('/uaeactivities', fn() => redirect()->route('emirates.index'));
Route::get('/uaeactivities/{any}', fn() => redirect()->route('emirates.index'))->where('any', '.*');
Route::get('/api/emirates', [EmiratesController::class, 'getEmiratesJson'])->name('api.emirates');

// FIFA World Cup 2026 — public page retired; the storefront now promotes Study Abroad.
// The GET listing redirects home so old links and search results do not 404. The POST
// endpoints, controller, models and manager screens are deliberately left in place so
// existing ticket requests and any in-flight ORDFIFA* payments can still be settled.
Route::get('/fifa-world-cup-2026', fn () => redirect('/', 302))->name('fifa.index');
Route::post('/fifa-world-cup-2026/request', [FifaTicketsController::class, 'submitRequest'])->name('fifa.request');
Route::post('/fifa-world-cup-2026/checkout', [FifaTicketsController::class, 'checkout'])->name('fifa.checkout');
// Live World Cup scores (JSON) — polled by the FIFA page.
Route::get('/fifa-world-cup-2026/live-scores', [FifaTicketsController::class, 'liveScores'])->name('fifa.live-scores');

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

    // Umrah Packages — legacy CRUD retired in favor of Manager Portal (writes a
    // conflicting `category` semantics: tier here vs. bus/air on the live schema,
    // which silently hid packages from the storefront). Manager Portal's
    // umrah-packages screens are the only supported editor now.
    // Route::resource('umrah-packages', UmrahPackageController::class)->except(['show']);
    // Route::resource('umrah-packages.departures', \App\Http\Controllers\Admin\UmrahDepartureController::class)->except(['show', 'create', 'edit']);

    // Saudi Visa types — legacy CRUD retired in favor of Manager Portal (this
    // one never validated/persisted `required_documents`, so a visa type
    // created here would silently never require the Emirates ID upload the
    // Manager-created ones do). Not linked from any admin nav either.
    // Route::resource('saudi-visas', \App\Http\Controllers\Admin\SaudiVisaAdminController::class)->except(['show', 'create', 'edit']);
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
    return view('contact-us', [
        'enquiryPackage' => request('package'),
    ]);
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

/**
 * Hands a page a fresh CSRF token without reloading it.
 *
 * Long guest forms — the UAE visa application in particular, with several
 * applicants and passport scans — can outlive the session that issued their
 * token, and the customer then hits a 419 with everything they typed lost.
 * The form pings this on a timer and before submitting, so the token it posts
 * is always current.
 */
Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()])
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
})->name('csrf.token');

// Fluxir e-visa (Global Travel Compliance) integration
use App\Http\Controllers\FluxirVisaController;
use App\Http\Controllers\FluxirEvisaController;

// Multi-country e-Visa storefront (picker + dynamic, scheme-driven form).
// Gated on the same tenant feature as the nav link that advertises it.
Route::middleware('tenant.feature:visas')->group(function () {
    Route::get('/e-visa',        [FluxirEvisaController::class, 'form'])->name('visa.evisa.form');
    Route::get('/e-visa/types',  [FluxirEvisaController::class, 'types'])->name('visa.evisa.types');
    Route::post('/e-visa/scheme',[FluxirEvisaController::class, 'scheme'])->name('visa.evisa.scheme');
    Route::post('/e-visa/apply', [FluxirEvisaController::class, 'apply'])->name('visa.evisa.apply');
    // Old UAE-only slug now redirects into the multi-country storefront.
    Route::get('/uae-evisa', fn() => redirect()->route('visa.evisa.form'))->name('visa.fluxir.form');
});
// Payment-return + status endpoints stay ungated: Stripe redirects land here
// and must work even if the feature is toggled off mid-flight.
Route::get('/visa/fluxir/success',         [FluxirVisaController::class, 'success'])->name('visa.fluxir.success');
Route::get('/visa/fluxir/cancel',          [FluxirVisaController::class, 'cancel'])->name('visa.fluxir.cancel');
Route::get('/visa/fluxir/status/{orderId}', [FluxirVisaController::class, 'status'])->name('visa.fluxir.status');


// PaymentController — DEPRECATED, replaced by NomodController (same as the
// CCAvenue routes above, which were already disabled). /payment 500'd outright
// (pointed at a showPaymentForm method that doesn't exist on the controller;
// only showForm() does), and no live page links to any of these four routes.
// use App\Http\Controllers\PaymentController;
//
// Route::get('/payment', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
// Route::match(['get', 'post'], '/payment/initiate', [PaymentController::class, 'initiatePayment'])->name('payment.initiate');
// Route::post('/payment/response', [PaymentController::class, 'paymentResponse'])->name('payment.response');
// Route::post('/payment/cancel', [PaymentController::class, 'paymentCancel'])->name('payment.cancel');


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

use App\Models\UAEVisaMaster;
use App\Models\Emirates;
use App\Models\UAEVisaPackage;

Route::get('/uaevisa', function () {
    $activeEmirates = Emirates::where('isActive', 1)
        ->whereHas('packages', function($q) {
            $q->where('isActive', 1);
        })
        ->orderBy('emiratesName')
        ->get();
    $packages = UAEVisaPackage::with([
        'prices' => function($q) {
            $q->where('isActive', 1);
        },
        'deposits' => function($q) {
            $q->where('isActive', 1);
        },
    ])->where('isActive', 1)->get();

    $pricingData = [];
    foreach ($packages as $pkg) {
        $pricingData[] = [
            'package_id'   => $pkg->id,
            'emirates_id'  => $pkg->emirates_id,
            'package_name' => $pkg->name,
            'package_type' => $pkg->package_type,
            'description'  => $pkg->description,
            // Package-wide default deposit, falling back to the company setting
            // when the package does not override it. Used when no nationality
            // is selected yet, or none of `deposits` below matches it.
            'deposit'      => $pkg->depositPerApplicant(),
            'deposit_fee'  => $pkg->depositAdminFee(),
            // Nationality-specific deposit overrides — resolved client-side the
            // same way price nationality overrides are (case-insensitive match,
            // fallback to the row with no nationality, then to `deposit` above).
            'deposits'     => $pkg->deposits->map(fn($d) => [
                'nationality' => $d->nationality,
                'deposit'     => (float) $d->security_deposit,
                'fee'         => (float) $d->deposit_admin_fee,
            ])->toArray(),
            'prices'       => $pkg->prices->map(fn($p) => [
                'entry_type'     => $p->entry_type,
                'duration'       => $p->duration,
                'traveller_type' => $p->traveller_type,
                'nationality'    => $p->nationality,
                'price'          => (float) $p->price
            ])->toArray()
        ];
    }

    $visaData = UAEVisaMaster::where('isActive', true)->get();
    $company = current_company();
    $hotelFee  = $company?->getSetting('visa_hotel_booking_fee', 25) ?? 25;
    $ticketFee = $company?->getSetting('visa_ticket_booking_fee', 25) ?? 25;
    // Sharjah deposit per applicant. Defaults to 0 (no deposit → generic
    // message, nothing charged) until a manager configures a positive amount.
    $sharjahDeposit = $company?->getSetting('visa_sharjah_deposit', 0);
    $sharjahDeposit = is_numeric($sharjahDeposit) ? (float) $sharjahDeposit : 0;
    // Non-refundable admin/processing fee deducted from the deposit at refund
    // time. Clamped to the deposit so the refundable amount is never negative.
    $sharjahAdminFee = $company?->getSetting('visa_sharjah_deposit_admin_fee', 0);
    $sharjahAdminFee = is_numeric($sharjahAdminFee) ? (float) $sharjahAdminFee : 0;
    $sharjahAdminFee = max(0, min($sharjahAdminFee, $sharjahDeposit));

    return view('uaevisa', compact('activeEmirates', 'pricingData', 'visaData', 'hotelFee', 'ticketFee', 'sharjahDeposit', 'sharjahAdminFee'));
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
Route::middleware('tenant.feature:esim')->group(function () {
    Route::get('/esim', [EsimController::class, 'index'])->name('esim.index');
    Route::get('/api/esim/countries', [EsimController::class, 'getCountries'])->name('esim.countries');
    Route::post('/esim/bundles', [EsimController::class, 'getBundles'])->name('esim.bundles');
    Route::post('/esim/purchase', [EsimController::class, 'purchase'])->name('esim.purchase');
});

// ─── Passport OCR (Groq vision) ─────────────────────────────────────
Route::get('/passport-scan', [\App\Http\Controllers\PassportOcrController::class, 'show'])->name('passport.scan');
Route::post('/passport-scan/extract', [\App\Http\Controllers\PassportOcrController::class, 'extract'])->name('passport.extract');

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
use App\Http\Controllers\Manager\ManagerFeatureFlagsController;
use App\Http\Controllers\Manager\ManagerFinanceController;
use App\Http\Controllers\Manager\OrdersController;
use App\Http\Controllers\Manager\SettingsController;
use App\Http\Controllers\Manager\ManagerAgentsController;
use App\Http\Controllers\Manager\ManagerEvisaSettingsController;
use App\Http\Controllers\Manager\ManagerB2bPartnersController;
use App\Http\Controllers\Manager\ManagerFifaTicketsController;
use App\Http\Controllers\Agent\AgentAuthController;
use App\Http\Controllers\Agent\AgentSignupController;
use App\Http\Controllers\Agent\AgentDashboardController;
use App\Http\Controllers\Agent\AgentTravelPackagesController;
use App\Http\Controllers\Agent\AgentActivitiesController;
use App\Http\Controllers\Agent\AgentEsimController;

Route::get('/manager/login', [ManagerAuthController::class, 'showLogin'])->name('manager.login');
Route::post('/manager/login', [ManagerAuthController::class, 'login'])->name('manager.login.submit');
Route::post('/manager/logout', [ManagerAuthController::class, 'logout'])->name('manager.logout');

Route::middleware(['manager.auth'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/', [ManagerDashboardController::class, 'index'])->name('dashboard');
    // ->except(['show']): none of these three controllers implement show(),
    // so the resource's default GET /{id} route pointed at a nonexistent
    // method and 500'd for anyone who navigated to it directly. Nothing in
    // the UI ever links there — managers use the edit screens.
    Route::resource('adslots', ManagerAdSlotsController::class)->except(['show']);
    Route::resource('announcements', ManagerAnnouncementsController::class)->except(['show']);
    Route::resource('activities', ManagerActivitiesController::class)->except(['show']);
    Route::post('activities/{id}/toggle-visibility', [ManagerActivitiesController::class, 'toggleVisibility'])->name('activities.toggle-visibility');

    // Tenant content: tour packages, hajj/umrah packages, visa pricing.
    // BelongsToCompany trait auto-scopes queries; CRUD is per-tenant.
    Route::resource('packages', ManagerTravelPackagesController::class)->except(['show']);
    Route::resource('umrah-packages', ManagerUmrahPackagesController::class)->except(['show']);
    Route::post('umrah-packages/{id}/toggle', [\App\Http\Controllers\Manager\ManagerUmrahPackagesController::class, 'toggle'])->name('umrah-packages.toggle');
    Route::post('umrah-packages/{id}/duplicate', [\App\Http\Controllers\Manager\ManagerUmrahPackagesController::class, 'duplicate'])->name('umrah-packages.duplicate');
    Route::resource('umrah-packages.departures', \App\Http\Controllers\Manager\ManagerUmrahDepartureController::class)->except(['show', 'create', 'edit']);
    Route::resource('saudi-visas', \App\Http\Controllers\Manager\ManagerSaudiVisaController::class)->except(['show', 'create', 'edit']);

    Route::prefix('umrah')->name('umrah.')->group(function () {
        // Same reasoning as adslots/announcements/activities above — neither
        // controller implements show(), and nothing links to it.
        Route::resource('categories', \App\Http\Controllers\Manager\ManagerUmrahCategoryController::class)->except(['show']);
        Route::resource('hotels', \App\Http\Controllers\Manager\ManagerUmrahHotelController::class)->except(['show']);
        Route::get('pricing', [\App\Http\Controllers\Manager\ManagerUmrahPricingController::class, 'index'])->name('pricing.index');
        Route::post('pricing', [\App\Http\Controllers\Manager\ManagerUmrahPricingController::class, 'update'])->name('pricing.update');
    });

    // Umrah Booking Management
    Route::prefix('umrah-bookings')->name('umrah-bookings.')->group(function () {
        Route::get('/',              [\App\Http\Controllers\Manager\ManagerUmrahBookingController::class, 'index'])->name('index');
        Route::get('/{id}',         [\App\Http\Controllers\Manager\ManagerUmrahBookingController::class, 'show'])->name('show');
        Route::post('/{id}/status', [\App\Http\Controllers\Manager\ManagerUmrahBookingController::class, 'updateStatus'])->name('status');
        Route::get('/export/csv',   [\App\Http\Controllers\Manager\ManagerUmrahBookingController::class, 'export'])->name('export');
    });

    // "Add Agent": managers create agent accounts and pick which services
    // (tours / activities / esim) each agent may manage in the /agent portal.
    Route::resource('agents', ManagerAgentsController::class)->except(['show']);
    // Visa pricing is a flat list of duration+price rows; CRUD is inline on the index page.
    Route::get('visa-pricing',                [ManagerVisaPricingController::class, 'index'])->name('visa-pricing.index');
    Route::post('visa-pricing',               [ManagerVisaPricingController::class, 'store'])->name('visa-pricing.store');
    Route::put('visa-pricing/{id}',           [ManagerVisaPricingController::class, 'update'])->name('visa-pricing.update');
    Route::delete('visa-pricing/{id}',        [ManagerVisaPricingController::class, 'destroy'])->name('visa-pricing.destroy');
    Route::put('visa-pricing-service-fees',   [ManagerVisaPricingController::class, 'updateServiceFees'])->name('visa-pricing.service-fees.update');
    Route::put('visa-pricing-evisa-markup',   [ManagerVisaPricingController::class, 'updateEvisaMarkup'])->name('visa-pricing.evisa-markup.update');

    // Dynamic UAE Visa Packages & Pricing Grid routes
    Route::post('visa-packages',              [ManagerVisaPricingController::class, 'storePackage'])->name('visa-packages.store');
    Route::put('visa-packages/{id}',          [ManagerVisaPricingController::class, 'updatePackage'])->name('visa-packages.update');
    Route::delete('visa-packages/{id}',       [ManagerVisaPricingController::class, 'destroyPackage'])->name('visa-packages.destroy');

    Route::post('visa-prices',                [ManagerVisaPricingController::class, 'storePriceRow'])->name('visa-prices.store');
    Route::post('visa-prices/bulk',           [ManagerVisaPricingController::class, 'bulkUpdatePriceRow'])->name('visa-prices.bulk-update');
    Route::put('visa-prices/{id}',            [ManagerVisaPricingController::class, 'updatePriceRow'])->name('visa-prices.update');
    Route::delete('visa-prices/{id}',         [ManagerVisaPricingController::class, 'destroyPriceRow'])->name('visa-prices.destroy');

    // Nationality-specific security deposits, same pattern as visa-prices above.
    Route::post('visa-deposits',              [ManagerVisaPricingController::class, 'storeDeposit'])->name('visa-deposits.store');
    Route::put('visa-deposits/{id}',          [ManagerVisaPricingController::class, 'updateDeposit'])->name('visa-deposits.update');
    Route::delete('visa-deposits/{id}',       [ManagerVisaPricingController::class, 'destroyDeposit'])->name('visa-deposits.destroy');

    Route::post('visa-emirates',              [ManagerVisaPricingController::class, 'storeEmirate'])->name('visa-emirates.store');
    Route::put('visa-emirates/{id}',          [ManagerVisaPricingController::class, 'updateEmirate'])->name('visa-emirates.update');
    Route::delete('visa-emirates/{id}',       [ManagerVisaPricingController::class, 'destroyEmirate'])->name('visa-emirates.destroy');

    // Global e-Visa: separate from UAE Visa package pricing above. Holds the
    // platform-wide Fluxir markup and per-agent e-Visa commissions.
    Route::get('evisa-settings',                   [ManagerEvisaSettingsController::class, 'index'])->name('evisa-settings.index');
    Route::put('evisa-settings/agent-commissions', [ManagerEvisaSettingsController::class, 'updateAgentCommissions'])->name('evisa-settings.agent-commissions.update');

    // B2B partner application review — approve/reject applications that
    // self-registered at /agency/register.
    Route::prefix('b2b-partners')->name('b2b-partners.')->group(function () {
        Route::get('/', [ManagerB2bPartnersController::class, 'index'])->name('index');
        Route::get('/{partner}', [ManagerB2bPartnersController::class, 'show'])->name('show');
        Route::post('/{partner}/approve', [ManagerB2bPartnersController::class, 'approve'])->name('approve');
        Route::post('/{partner}/reject', [ManagerB2bPartnersController::class, 'reject'])->name('reject');
        Route::post('/{partner}/confirm-renewal', [ManagerB2bPartnersController::class, 'confirmRenewal'])->name('confirm-renewal');
        Route::put('/{partner}/commission', [ManagerB2bPartnersController::class, 'updateCommission'])->name('commission.update');
    });

    // Agent application review — approve/deny applications that
    // self-registered at /agent/register, after checking the trade license.
    Route::prefix('agent-applications')->name('agent-applications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Manager\ManagerAgentApplicationsController::class, 'index'])->name('index');
        Route::get('/{application}', [\App\Http\Controllers\Manager\ManagerAgentApplicationsController::class, 'show'])->name('show');
        Route::post('/{application}/approve', [\App\Http\Controllers\Manager\ManagerAgentApplicationsController::class, 'approve'])->name('approve');
        Route::post('/{application}/reject', [\App\Http\Controllers\Manager\ManagerAgentApplicationsController::class, 'reject'])->name('reject');
    });

    // FIFA World Cup 2026 tickets — SHARED across all companies (not tenant-scoped).
    // Global markup, match + ticket inventory, and the customer request inbox.
    Route::prefix('fifa-tickets')->name('fifa-tickets.')->group(function () {
        Route::get('/',                    [ManagerFifaTicketsController::class, 'index'])->name('index');
        Route::post('/markup',             [ManagerFifaTicketsController::class, 'updateMarkup'])->name('markup');
        Route::post('/matches',            [ManagerFifaTicketsController::class, 'storeMatch'])->name('matches.store');
        Route::put('/matches/{id}',        [ManagerFifaTicketsController::class, 'updateMatch'])->name('matches.update');
        Route::delete('/matches/{id}',     [ManagerFifaTicketsController::class, 'destroyMatch'])->name('matches.destroy');
        Route::post('/listings',           [ManagerFifaTicketsController::class, 'storeTicket'])->name('listings.store');
        Route::put('/listings/{id}',       [ManagerFifaTicketsController::class, 'updateTicket'])->name('listings.update');
        Route::delete('/listings/{id}',    [ManagerFifaTicketsController::class, 'destroyTicket'])->name('listings.destroy');
        Route::get('/requests',            [ManagerFifaTicketsController::class, 'requests'])->name('requests');
        Route::post('/requests/{id}/status', [ManagerFifaTicketsController::class, 'updateRequestStatus'])->name('requests.status');
    });

    // Features are managed by super-admin via /superadmin/companies/{c}/edit.
    // Tenants get a read-only view here (no POST endpoint — see audit finding #13).
    Route::get('/settings/features', [ManagerFeatureFlagsController::class, 'features'])->name('settings.features');

    // Tenant Settings — profile/branding + preferences (Step D).
    Route::get('/settings/profile',      [SettingsController::class, 'profile'])->name('settings.profile');
    Route::post('/settings/profile',     [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::get('/settings/preferences',  [SettingsController::class, 'preferences'])->name('settings.preferences');
    Route::post('/settings/preferences', [SettingsController::class, 'updatePreferences'])->name('settings.preferences.update');
    Route::get('/settings/notifications',  [SettingsController::class, 'notifications'])->name('settings.notifications');
    Route::post('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications.update');
    Route::post('/settings/notifications/test', [SettingsController::class, 'sendTestNotification'])->name('settings.notifications.test');

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
        Route::post('/esim/{order}/resend-qr',     [OrdersController::class, 'resendEsimQr'])->name('esim.resend-qr');
        Route::post('/esim/{order}/retry',         [OrdersController::class, 'retryEsimProvisioning'])->name('esim.retry');
        Route::get('/evisa',                       [OrdersController::class, 'evisa'])->name('evisa');
        Route::get('/evisa/{application}',         [OrdersController::class, 'evisaDetail'])->name('evisa.show');
        Route::post('/evisa/{application}/retry',  [OrdersController::class, 'retryEvisaSubmission'])->name('evisa.retry');
        Route::get('/visa',                        [OrdersController::class, 'visa'])->name('visa');
        Route::get('/visa/{application}',          [OrdersController::class, 'visaDetail'])->name('visa.show');
        Route::post('/visa/{application}/refund',  [OrdersController::class, 'markUaeVisaRefunded'])->name('visa.refund');
        Route::get('/saudi-visa',                  [OrdersController::class, 'saudiVisa'])->name('saudi-visa');
        Route::get('/saudi-visa/{application}',    [OrdersController::class, 'saudiVisaDetail'])->name('saudi-visa.show');
        Route::post('/saudi-visa/{application}/status', [OrdersController::class, 'updateSaudiVisaStatus'])->name('saudi-visa.status');
        Route::get('/flights-hotels',              [OrdersController::class, 'flightsHotels'])->name('flights-hotels');
    });
});

// ─── Agent Portal ───────────────────────────────────────────────────
// Dedicated login for tenant agent accounts (role company_agent) created by
// managers via /manager/agents. Each section is gated on the per-agent
// service grant (which itself requires the tenant feature to be enabled).
Route::get('/agent/login', [AgentAuthController::class, 'showLogin'])->name('agent.login');
Route::post('/agent/login', [AgentAuthController::class, 'login'])->name('agent.login.submit');
Route::post('/agent/logout', [AgentAuthController::class, 'logout'])->name('agent.logout');

// Self-service agent registration — starts `pending`, does not log in. See
// ManagerAgentApplicationsController for the approve/deny review queue.
Route::get('/agent/register', [AgentSignupController::class, 'showRegister'])->name('agent.register');
Route::post('/agent/register', [AgentSignupController::class, 'register'])->name('agent.register.submit');
Route::get('/agent/register/submitted', [AgentSignupController::class, 'submitted'])->name('agent.register.submitted');

Route::middleware(['agent.auth'])->prefix('agent')->name('agent.')->group(function () {
    Route::get('/', [AgentDashboardController::class, 'index'])->name('dashboard');

    Route::middleware('agent.service:tours')->group(function () {
        Route::resource('packages', AgentTravelPackagesController::class)->except(['show']);
    });

    Route::middleware('agent.service:activities')->group(function () {
        Route::resource('activities', AgentActivitiesController::class)->except(['show']);
    });

    Route::middleware('agent.service:esim')->group(function () {
        Route::get('esim-orders', [AgentEsimController::class, 'index'])->name('esim.index');
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

// ─── B2B Partner Registration ───────────────────────────────────────
use App\Http\Controllers\B2bPartner\B2bPartnerAuthController;
use App\Http\Controllers\B2bPartner\B2bPartnerSignupController;
use App\Http\Controllers\B2bPartner\B2bPartnerDashboardController;
use App\Http\Controllers\B2bPartner\B2bPartnerLicenseRenewalController;

Route::get('/agency/register', [B2bPartnerSignupController::class, 'showRegister'])->name('agency.register');
Route::post('/agency/register', [B2bPartnerSignupController::class, 'register'])->name('agency.register.submit');
Route::get('/agency/register/submitted', [B2bPartnerSignupController::class, 'submitted'])->name('agency.register.submitted');

Route::get('/agency/login', [B2bPartnerAuthController::class, 'showLogin'])->name('agency.login');
Route::post('/agency/login', [B2bPartnerAuthController::class, 'login'])->name('agency.login.submit');
Route::post('/agency/logout', [B2bPartnerAuthController::class, 'logout'])->name('agency.logout');

// Self-service license renewal for a disabled partner — not behind
// b2b.auth, since a disabled partner can never pass canLogin(). Verifies
// identity via email+password directly instead.
Route::get('/agency/renew-license', [B2bPartnerLicenseRenewalController::class, 'showForm'])->name('agency.renew-license');
Route::post('/agency/renew-license', [B2bPartnerLicenseRenewalController::class, 'submit'])->name('agency.renew-license.submit');
Route::get('/agency/renew-license/submitted', [B2bPartnerLicenseRenewalController::class, 'submitted'])->name('agency.renew-license.submitted');

Route::middleware(['b2b.auth'])->prefix('agency')->name('agency.')->group(function () {
    Route::get('/', [B2bPartnerDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/contract', [B2bPartnerDashboardController::class, 'downloadContract'])->name('contract.download');
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

