@extends('layouts.client')

@section('title', 'Settings')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-cog me-2"></i>Settings</h1>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <form action="{{ route('client.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Business Settings -->
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-briefcase me-2"></i>Business Settings</div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label mb-1" style="font-size: 0.75rem;">Currency</label>
                            <select name="currency" class="form-select form-select-sm @error('currency') is-invalid @enderror">
                                <option value="AED" {{ $company->currency === 'AED' ? 'selected' : '' }}>AED - UAE Dirham</option>
                                <option value="USD" {{ $company->currency === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                <option value="EUR" {{ $company->currency === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                <option value="GBP" {{ $company->currency === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                <option value="INR" {{ $company->currency === 'INR' ? 'selected' : '' }}>INR - Indian Rupee</option>
                            </select>
                            @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label mb-1" style="font-size: 0.75rem;">Timezone</label>
                            <select name="timezone" class="form-select form-select-sm @error('timezone') is-invalid @enderror">
                                <option value="Asia/Dubai" {{ $company->timezone === 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai (GMT+4)</option>
                                <option value="UTC" {{ $company->timezone === 'UTC' ? 'selected' : '' }}>UTC</option>
                                <option value="America/New_York" {{ $company->timezone === 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                                <option value="Europe/London" {{ $company->timezone === 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                                <option value="Asia/Kolkata" {{ $company->timezone === 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata</option>
                            </select>
                            @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label mb-1" style="font-size: 0.75rem;">Markup Percentage</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="markup_percentage" class="form-control form-control-sm @error('markup_percentage') is-invalid @enderror"
                                       value="{{ old('markup_percentage', $company->markup_percentage) }}" min="0" max="100" step="0.01">
                                <span class="input-group-text">%</span>
                            </div>
                            @error('markup_percentage')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Added to base eSIM prices</small>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-save me-1"></i>Save Settings
            </button>
        </form>
    </div>

    <div class="col-lg-4">
        <!-- Subscription Info -->
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-credit-card me-2"></i>Subscription</div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Current Plan</span>
                    <span class="badge bg-{{ $company->plan === 'enterprise' ? 'primary' : ($company->plan === 'pro' ? 'info' : ($company->plan === 'basic' ? 'success' : 'warning')) }} fs-6">
                        {{ ucfirst($company->plan) }}
                    </span>
                </div>

                @if($company->isOnTrial())
                <div class="alert alert-warning py-2 mb-3">
                    <i class="fas fa-clock me-2"></i>Trial ends {{ $company->trial_ends_at->diffForHumans() }}
                </div>
                @elseif($company->subscription_ends_at)
                <div class="alert alert-info py-2 mb-3">
                    <i class="fas fa-calendar me-2"></i>
                    Renews {{ $company->subscription_ends_at->format('M d, Y') }}
                </div>
                @endif

                <a href="#" class="btn btn-outline-primary w-100">
                    <i class="fas fa-arrow-up me-2"></i>Upgrade Plan
                </a>
            </div>
        </div>

        <!-- Account Info -->
        <div class="card">
            <div class="card-header"><i class="fas fa-info-circle me-2"></i>Account Info</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Slug</td>
                        <td><code>{{ $company->slug }}</code></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Created</td>
                        <td>{{ $company->created_at->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            <span class="badge bg-{{ $company->is_active ? 'success' : 'danger' }}">
                                {{ $company->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
