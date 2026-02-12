<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\ConventionController;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReferrerController;
use App\Http\Controllers\CCAvenueController;
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

// Admin routes (protected by auth middleware)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // User management resource routes
    Route::resource('users', UserController::class);

    // Partner status management route - THIS IS THE IMPORTANT ONE
    Route::put('/partner-status/{userId}', [UserController::class, 'updatePartnerStatus'])->name('partner.status');

    // Additional partner management routes
    Route::get('/pending-partners', [UserController::class, 'getPendingPartners'])->name('pending.partners');

});



Route::prefix('/')->group(function () {
    Route::get('hajj-umrah', fn() => view('hajj-umrah'))->name('hajj-umrah');
    Route::get('our-services', fn() => view('our-services'))->name('our-services');
    Route::get('banner0', fn() => view('banner0'));
    Route::get('countriestour', fn() => view('countriestour'));
    Route::get('ourstory', fn() => view('ourstory'));
    Route::get('contact-us', fn() => view('contact-us'));
    Route::get('termsandconditions', fn() => view('termsandconditions'));
    Route::get('cancellationandrefundpolicy', fn() => view('cancellationandrefundpolicy'));
    Route::get('privacypolicy', fn() => view('privacypolicy'));
    // Route::get('dubai-global-village', fn() => view('dubai-global-village'));
    Route::get('lotus-cruise-dubai', fn() => view('lotus-cruise-dubai'));
    Route::get('shopnow', fn() => view('shopnow'));
    Route::get('payonline', fn() => view('payonline'));
    Route::get('lookingforajob', fn() => view('lookingforajob'));
    Route::get('visaservice', fn() => view('visaservice'));
    Route::get('uaevisa', fn() => view('uaevisa'));
    Route::get('caro', fn() => view('uaecarousel'));


});




Route::get('/activities', [EmiratesController::class, 'index'])->name('emirates.index');
Route::get('/Activities', fn() => redirect()->route('emirates.index'));
Route::get('/uaeactivities', fn() => redirect()->route('emirates.index'));
Route::get('/uaeactivities/{any}', fn() => redirect()->route('emirates.index'))->where('any', '.*');
Route::get('/api/emirates', [EmiratesController::class, 'getEmiratesJson'])->name('api.emirates');

//backend
Route::group(['middleware' => ['auth', 'isAdmin'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard.index');

    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
    Route::delete('roles_mass_destroy', [\App\Http\Controllers\Admin\RoleController::class, 'massDestroy'])->name('roles.mass_destroy');

    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::delete('users_mass_destroy', [\App\Http\Controllers\Admin\UserController::class, 'massDestroy'])->name('users.mass_destroy');

    // Announcements - Added directly inside the existing admin group
    Route::resource('announcements', AnnouncementAdminController::class);
    Route::post('announcements/{announcement}/toggle-status', [AnnouncementAdminController::class, 'toggleStatus'])
        ->name('announcements.toggle-status');


    ////HOMEPAGE CASOUSEL
    Route::resource('homepageads', CarouselAdminController::class);
    Route::post('homepageads/{homepagead}/toggle-status', [CarouselAdminController::class, 'toggleStatus'])
        ->name('homepageads.toggle-status');




    /////UAEACTIVITIES
    Route::resource('uaeactivities', UAEActivityAdminController::class);





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



Route::get('/ccavenue/initiate', function () {
    return view('ccavenue.initiate');
})->name('ccavenue.initiate');

Route::post('/ccavenue/pay', [CCAvenueController::class, 'pay'])->name('ccavenue.pay');
Route::any('/ccavenue/response', [CCAvenueController::class, 'response'])->name('ccavenue.response');

Route::post('/uaev/submit', [UAEVisaController::class, 'submit'])->name('uaev.submit');
Route::post('/uae-visa/initiate-payment', [UAEVisaController::class, 'initiateCcavenue'])->name('uaevisa.initiateCcavenue');


use App\Http\Controllers\PaymentController;

Route::get('/payment', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
Route::post('/payment/initiate', [PaymentController::class, 'initiatePayment'])->name('payment.initiate');
Route::post('/payment/response', [PaymentController::class, 'paymentResponse'])->name('payment.response');
Route::post('/payment/cancel', [PaymentController::class, 'paymentCancel'])->name('payment.cancel');

Route::group(['middleware' => ['auth', 'isAdmin'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard.index');
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
    Route::delete('roles_mass_destroy', [\App\Http\Controllers\Admin\RoleController::class, 'massDestroy'])->name('roles.mass_destroy');
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::delete('users_mass_destroy', [\App\Http\Controllers\Admin\UserController::class, 'massDestroy'])->name('users.mass_destroy');
});




Route::get('/activity-details', [UAEDetailsController::class, 'show'])->name('activities.detail');
Route::get('/dubai-global-village', [UAEDetailsController::class, 'show']); // Keep for backward compatibility

Route::get('/all-uae-activities', [UAEActivityController::class, 'index'])->name('uae.activities');



use App\Http\Controllers\ActivityBookingController;

Route::post('/book-activity', [ActivityBookingController::class, 'submit'])->name('activity.book');




// Accept BOTH GET and POST for payment initiation
Route::match(['get', 'post'], '/payment/initiate', [PaymentController::class, 'initiatePayment'])->name('payment.initiate');


// Then routes:
Route::get('/activity/book/{activityId}', [ActivityBookingController::class, 'show'])->name('activity.book.form');
// Route::post('/activity/book', [ActivityBookingController::class, 'submit'])->name('activity.book');
Route::post('/activity/book', [ActivityBookingController::class, 'submit'])->name('activity.book');
Route::get('/activity/prices/{activityId?}', [ActivityBookingController::class, 'getActivityPrices']);


Route::get('/activity/pricing/{id}', function ($id) {
    $activity = DB::table('tbl_uaeactivities')->where('activityID', $id)->first();
    return response()->json([
        'activityPrice' => $activity ? (float) $activity->activityPrice : 0,
        'activityChildPrice' => ($activity && $activity->activityChildPrice && $activity->activityChildPrice > 0)
            ? (float) $activity->activityChildPrice
            : ($activity ? (float) $activity->activityPrice : 0),
        'activityTransactionCharges' => $activity ? (float) $activity->activityTransactionCharges : 0,
    ]);
})->name('activity.pricing');
//Route::post('/ccavenue/encrypt', [\App\Http\Controllers\ActivityBookingController::class, 'encryptForCcavenue'])->name('ccavenue.encrypt');
Route::post('/ccavenue/callback', [\App\Http\Controllers\ActivityBookingController::class, 'ccavenueCallback'])->name('payment.ccavenue.callback');


Route::post('/payment/ccavenue/response', [ActivityBookingController::class, 'handleResponse'])->name('payment.ccavenue.response');
Route::get('/payment/ccavenue/cancel', function () {

    return view('payment_cancel');
})->name('payment.ccavenue.cancel');

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
});


Route::post('/ccavenue/initiate', [CCAvenueController::class, 'initiatePayment']);
Route::post('/ccavenue/response', [CCAvenueController::class, 'handleResponse'])->name('ccavenue.response');
// Route::get('/payment/ccavenue/cancel', [CCAvenueController::class, 'cancel'])->name('ccavenue.cancel');
Route::match(['get', 'post'], '/payment/ccavenue/cancel', [CCAvenueController::class, 'cancel'])->name('ccavenue.cancel');

Route::get('/ccavenue-debug', function () {
    $merchantId = config('services.ccavenue.merchant_id');
    $accessCode = config('services.ccavenue.access_code');
    $workingKey = config('services.ccavenue.working_key');

    return response()->json([
        'merchant_id' => $merchantId,
        'merchant_id_length' => strlen($merchantId),
        'access_code' => $accessCode,
        'access_code_length' => strlen($accessCode),
        'working_key_prefix' => substr($workingKey, 0, 4) . '****',
        'working_key_length' => strlen($workingKey),
        'url' => config('services.ccavenue.url'),
        'redirect_url' => config('services.ccavenue.redirect_url'),
        'cancel_url' => config('services.ccavenue.cancel_url'),
    ]);
});
// Add this route alongside your existing activity routes
Route::post('/activity/payment/initiate', [ActivityBookingController::class, 'initiateActivityPayment'])->name('activity.payment.initiate');
Route::post('/agent/pay', [AgentBookingController::class, 'submit'])->name('agent.pay');

Auth::routes(['register' => false]);

// ─── Manager Dashboard Routes ───────────────────────────────────────
use App\Http\Controllers\Manager\ManagerAuthController;
use App\Http\Controllers\Manager\ManagerDashboardController;
use App\Http\Controllers\Manager\ManagerAdSlotsController;
use App\Http\Controllers\Manager\ManagerAnnouncementsController;

Route::get('/manager/login', [ManagerAuthController::class, 'showLogin'])->name('manager.login');
Route::post('/manager/login', [ManagerAuthController::class, 'login'])->name('manager.login.submit');
Route::post('/manager/logout', [ManagerAuthController::class, 'logout'])->name('manager.logout');

Route::middleware(['manager.auth'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/', [ManagerDashboardController::class, 'index'])->name('dashboard');
    Route::resource('adslots', ManagerAdSlotsController::class);
    Route::resource('announcements', ManagerAnnouncementsController::class);
});
