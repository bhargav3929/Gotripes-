@extends('layouts.superadmin')

@section('title', 'Edit ' . $company->name)

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-edit me-2"></i>Edit Company</h1>
    <a href="{{ route('superadmin.companies.show', $company) }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back
    </a>
</div>

<form action="{{ route('superadmin.companies.update', $company) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Company Details -->
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-building me-2"></i>Company Details</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Company Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $company->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                                   value="{{ old('slug', $company->slug) }}">
                            @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subdomain</label>
                            <div class="input-group">
                                <input type="text" name="subdomain" class="form-control @error('subdomain') is-invalid @enderror"
                                       value="{{ old('subdomain', $company->subdomain) }}">
                                <span class="input-group-text">.gotrips.ai</span>
                            </div>
                            @error('subdomain')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Custom Domain</label>
                            <input type="text" name="domain" class="form-control @error('domain') is-invalid @enderror"
                                   value="{{ old('domain', $company->domain) }}" placeholder="www.company.com">
                            @error('domain')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $company->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $company->phone) }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Branding -->
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-palette me-2"></i>Branding</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Logo</label>
                            @if($company->logo)
                            <div class="mb-2">
                                <img src="{{ $company->logo_url }}" alt="Current Logo" style="max-height: 50px;">
                                <small class="d-block text-muted mt-1">Current logo</small>
                            </div>
                            @endif
                            <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror"
                                   accept="image/*">
                            @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Primary Color</label>
                            <input type="color" name="primary_color" class="form-control form-control-color w-100"
                                   value="{{ old('primary_color', $company->primary_color) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Secondary Color</label>
                            <input type="color" name="secondary_color" class="form-control form-control-color w-100"
                                   value="{{ old('secondary_color', $company->secondary_color) }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-cog me-2"></i>Settings</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Currency</label>
                            <select name="currency" class="form-select @error('currency') is-invalid @enderror">
                                <option value="AED" {{ old('currency', $company->currency) === 'AED' ? 'selected' : '' }}>AED - UAE Dirham</option>
                                <option value="USD" {{ old('currency', $company->currency) === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                <option value="EUR" {{ old('currency', $company->currency) === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                <option value="GBP" {{ old('currency', $company->currency) === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                <option value="INR" {{ old('currency', $company->currency) === 'INR' ? 'selected' : '' }}>INR - Indian Rupee</option>
                            </select>
                            @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Timezone</label>
                            <select name="timezone" class="form-select @error('timezone') is-invalid @enderror">
                                <option value="Asia/Dubai" {{ old('timezone', $company->timezone) === 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai (GMT+4)</option>
                                <option value="UTC" {{ old('timezone', $company->timezone) === 'UTC' ? 'selected' : '' }}>UTC</option>
                                <option value="America/New_York" {{ old('timezone', $company->timezone) === 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                                <option value="Europe/London" {{ old('timezone', $company->timezone) === 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                                <option value="Asia/Kolkata" {{ old('timezone', $company->timezone) === 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata</option>
                            </select>
                            @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Subscription -->
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-credit-card me-2"></i>Subscription</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Plan</label>
                        <select name="plan" class="form-select @error('plan') is-invalid @enderror">
                            <option value="trial" {{ old('plan', $company->plan) === 'trial' ? 'selected' : '' }}>Trial</option>
                            <option value="basic" {{ old('plan', $company->plan) === 'basic' ? 'selected' : '' }}>Basic</option>
                            <option value="pro" {{ old('plan', $company->plan) === 'pro' ? 'selected' : '' }}>Pro</option>
                            <option value="enterprise" {{ old('plan', $company->plan) === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                        </select>
                        @error('plan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Markup Percentage</label>
                        <div class="input-group">
                            <input type="number" name="markup_percentage" class="form-control"
                                   value="{{ old('markup_percentage', $company->markup_percentage) }}" min="0" max="100" step="0.01">
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="text-muted">Applied to eSIM prices</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Subscription Ends</label>
                        <input type="date" name="subscription_ends_at" class="form-control"
                               value="{{ old('subscription_ends_at', $company->subscription_ends_at?->format('Y-m-d')) }}">
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-toggle-on me-2"></i>Status</div>
                <div class="card-body">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', $company->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Company is Active</label>
                    </div>
                    <small class="text-muted">Inactive companies cannot access the platform</small>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                    <a href="{{ route('superadmin.companies.show', $company) }}" class="btn btn-outline-secondary w-100">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
