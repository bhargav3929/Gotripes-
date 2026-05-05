<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| /client/* — DEPRECATED, redirects to /manager
|--------------------------------------------------------------------------
| The /client dashboard was consolidated into /manager. These redirects
| keep old bookmarks, links, and partner integrations working.
|
| GET requests are 301-redirected to the equivalent /manager URL where one
| exists, otherwise to the manager dashboard root.
|
| Non-GET requests (POST/PUT/DELETE) return 410 Gone — we deliberately do
| NOT silently redirect a state-changing request to a different endpoint.
| If a script is hitting `PUT /client/branding`, it would otherwise post
| arbitrary fields into the manager namespace which doesn't accept them.
|
| Watch the access log for `/client/*` hits for ~1 release cycle, then
| this whole file (and the redirects) can be deleted.
*/

Route::prefix('client')->group(function () {
    // Specific GET mappings to the closest /manager equivalent
    Route::get('/',                          fn() => redirect()->route('manager.dashboard', [], 301));
    Route::get('/activities',                fn() => redirect()->route('manager.finance.bookings', [], 301));
    Route::get('/branding',                  fn() => redirect()->route('manager.dashboard', [], 301));
    Route::get('/settings',                  fn() => redirect()->route('manager.settings.features', [], 301));

    // Everything else under /client (orders, visa, flights-hotels, analytics,
    // detail pages, anything we forgot) → manager dashboard
    Route::get('{any}', fn() => redirect()->route('manager.dashboard', [], 301))
        ->where('any', '.*');

    // Block writes deliberately rather than silently redirecting them.
    Route::match(['post', 'put', 'patch', 'delete'], '{any}', function () {
        abort(410, 'The /client API has been retired. Please use /manager.');
    })->where('any', '.*');
});
