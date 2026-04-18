@extends('layouts.superadmin')

@section('title', 'Settings')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-cog me-2"></i>Platform Settings</h1>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- General Settings -->
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-globe me-2"></i>General Settings</div>
            <div class="card-body">
                <form action="{{ route('superadmin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Platform Name</label>
                            <input type="text" name="platform_name" class="form-control"
                                   value="{{ $settings['platform_name'] ?? 'GoTrips SaaS' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Support Email</label>
                            <input type="email" name="support_email" class="form-control"
                                   value="{{ $settings['support_email'] ?? 'support@gotrips.ai' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Default Currency</label>
                            <select name="default_currency" class="form-select">
                                <option value="AED" {{ ($settings['default_currency'] ?? 'AED') === 'AED' ? 'selected' : '' }}>AED - UAE Dirham</option>
                                <option value="USD" {{ ($settings['default_currency'] ?? '') === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                <option value="EUR" {{ ($settings['default_currency'] ?? '') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Default Timezone</label>
                            <select name="default_timezone" class="form-select">
                                <option value="Asia/Dubai" {{ ($settings['default_timezone'] ?? 'Asia/Dubai') === 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai</option>
                                <option value="UTC" {{ ($settings['default_timezone'] ?? '') === 'UTC' ? 'selected' : '' }}>UTC</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save General Settings
                    </button>
                </form>
            </div>
        </div>

        <!-- Trial Settings -->
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-clock me-2"></i>Trial Settings</div>
            <div class="card-body">
                <form action="{{ route('superadmin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Trial Duration (days)</label>
                            <input type="number" name="trial_days" class="form-control"
                                   value="{{ $settings['trial_days'] ?? 14 }}" min="1" max="90">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Default Trial Plan Features</label>
                            <select name="trial_features" class="form-select" multiple>
                                <option value="esim_sales" selected>eSIM Sales</option>
                                <option value="referral_system" selected>Referral System</option>
                                <option value="custom_branding">Custom Branding</option>
                                <option value="api_access">API Access</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Trial Settings
                    </button>
                </form>
            </div>
        </div>

        <!-- Email Settings -->
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-envelope me-2"></i>Email Settings</div>
            <div class="card-body">
                <form action="{{ route('superadmin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">SMTP Host</label>
                            <input type="text" name="smtp_host" class="form-control"
                                   value="{{ $settings['smtp_host'] ?? '' }}" placeholder="smtp.example.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">SMTP Port</label>
                            <input type="number" name="smtp_port" class="form-control"
                                   value="{{ $settings['smtp_port'] ?? 587 }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">SMTP Username</label>
                            <input type="text" name="smtp_username" class="form-control"
                                   value="{{ $settings['smtp_username'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">SMTP Password</label>
                            <input type="password" name="smtp_password" class="form-control"
                                   placeholder="••••••••">
                        </div>
                    </div>

                    <hr class="my-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Email Settings
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-bolt me-2"></i>Quick Actions</div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <form action="{{ route('superadmin.settings.clear-cache') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-warning w-100">
                            <i class="fas fa-broom me-2"></i>Clear All Caches
                        </button>
                    </form>
                    <a href="{{ route('superadmin.companies.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-2"></i>Create New Company
                    </a>
                    <a href="{{ route('superadmin.reports.index') }}" class="btn btn-outline-info">
                        <i class="fas fa-chart-bar me-2"></i>View Reports
                    </a>
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="card">
            <div class="card-header"><i class="fas fa-server me-2"></i>System Information</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">PHP Version</td>
                        <td>{{ PHP_VERSION }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Laravel Version</td>
                        <td>{{ app()->version() }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Environment</td>
                        <td><span class="badge bg-{{ app()->environment('production') ? 'danger' : 'warning' }}">{{ app()->environment() }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Debug Mode</td>
                        <td><span class="badge bg-{{ config('app.debug') ? 'danger' : 'success' }}">{{ config('app.debug') ? 'ON' : 'OFF' }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Cache Driver</td>
                        <td>{{ config('cache.default') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Queue Driver</td>
                        <td>{{ config('queue.default') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
