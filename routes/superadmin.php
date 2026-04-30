<?php

use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\CompanyController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdmin\ReportController;
use App\Http\Controllers\SuperAdmin\SettingsController;
use App\Http\Controllers\SuperAdmin\SuperAdminAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
*/

// Dedicated super-admin login (no auth required)
Route::middleware(['web'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('login', [SuperAdminAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [SuperAdminAuthController::class, 'login'])->name('login.submit');
    Route::post('logout', [SuperAdminAuthController::class, 'logout'])->name('logout');
});

Route::middleware(['web', 'auth', 'super.admin'])->prefix('superadmin')->name('superadmin.')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Companies Management
    Route::resource('companies', CompanyController::class);

    // Company Actions
    Route::post('companies/{company}/toggle-status', [CompanyController::class, 'toggleStatus'])
        ->name('companies.toggle-status');

    Route::post('companies/{company}/impersonate', [CompanyController::class, 'impersonate'])
        ->name('companies.impersonate');

    Route::post('companies/{company}/extend-subscription', [CompanyController::class, 'extendSubscription'])
        ->name('companies.extend-subscription');

    Route::post('companies/{company}/change-plan', [CompanyController::class, 'changePlan'])
        ->name('companies.change-plan');

    // Provision the Hostinger subdomain + symlink for this company
    Route::post('companies/{company}/provision-subdomain', [CompanyController::class, 'provisionSubdomain'])
        ->name('companies.provision-subdomain');

    // Return from impersonation
    Route::get('stop-impersonation', function () {
        if (session()->has('impersonating_from')) {
            $originalUserId = session('impersonating_from');
            session()->forget(['impersonating_from', 'company_id']);

            $user = \App\Models\User::find($originalUserId);
            if ($user) {
                auth()->login($user);
                return redirect()->route('superadmin.dashboard')
                    ->with('success', 'Returned to Super Admin account.');
            }
        }
        return redirect('/');
    })->name('stop-impersonation');

    // Users Management
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('settings/clear-cache', [SettingsController::class, 'clearCache'])->name('settings.clear-cache');
});
