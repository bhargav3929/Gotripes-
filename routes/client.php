<?php

use App\Http\Controllers\Client\ClientDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Client Admin Routes
|--------------------------------------------------------------------------
| Routes for company owners/admins to manage their business
*/

Route::middleware(['web', 'auth', 'company.admin'])->prefix('client')->name('client.')->group(function () {

    // Dashboard
    Route::get('/', [ClientDashboardController::class, 'index'])->name('dashboard');

    // Orders
    Route::get('/orders', [ClientDashboardController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [ClientDashboardController::class, 'showOrder'])->name('orders.show');

    // Branding
    Route::get('/branding', [ClientDashboardController::class, 'branding'])->name('branding');
    Route::put('/branding', [ClientDashboardController::class, 'updateBranding'])->name('branding.update');

    // Settings
    Route::get('/settings', [ClientDashboardController::class, 'settings'])->name('settings');
    Route::put('/settings', [ClientDashboardController::class, 'updateSettings'])->name('settings.update');

    // Analytics
    Route::get('/analytics', [ClientDashboardController::class, 'analytics'])->name('analytics');
});
