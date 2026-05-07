@extends('layouts.superadmin')

@section('title', 'Create Company')

@section('content')
@php
    $allFeatures = \App\Models\Company::AVAILABLE_FEATURES;
    $oldFeatures = old('features', array_keys($allFeatures)); // default: all checked
@endphp
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-plus-circle"></i>Create New Company</h1>
    <a href="{{ route('superadmin.companies.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Back
    </a>
</div>

<form action="{{ route('superadmin.companies.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Company Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-building"></i>
                    Company Information
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Company Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="Enter company name" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" class="form-control form-control-sm @error('slug') is-invalid @enderror"
                                   value="{{ old('slug') }}" placeholder="Auto-generated if empty">
                            @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subdomain</label>
                            <div class="input-group">
                                <input type="text" name="subdomain" class="form-control form-control-sm @error('subdomain') is-invalid @enderror"
                                       value="{{ old('subdomain') }}" placeholder="company">
                                <span class="input-group-text">.gotrips.ai</span>
                            </div>
                            @error('subdomain')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Custom Domain</label>
                            <input type="text" name="domain" class="form-control form-control-sm @error('domain') is-invalid @enderror"
                                   value="{{ old('domain') }}" placeholder="www.company.com">
                            @error('domain')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control form-control-sm @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" placeholder="company@example.com" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control form-control-sm @error('phone') is-invalid @enderror"
                                   value="{{ old('phone') }}" placeholder="+971 XX XXX XXXX">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Used on tenant's contact page + WhatsApp button.</small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" rows="2" class="form-control form-control-sm @error('address') is-invalid @enderror"
                                      placeholder="Street, City, Country">{{ old('address') }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Renders the embedded map on the tenant's contact page. Leave blank to hide the map.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Website</label>
                            <input type="url" name="website" class="form-control form-control-sm @error('website') is-invalid @enderror"
                                   value="{{ old('website') }}" placeholder="https://example.com">
                            @error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin User -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user-shield"></i>
                    Admin Account
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">This user will be the company owner with full access to the client panel.</p>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Admin Name <span class="text-danger">*</span></label>
                            <input type="text" name="admin_name" class="form-control form-control-sm @error('admin_name') is-invalid @enderror"
                                   value="{{ old('admin_name') }}" placeholder="Full name" required>
                            @error('admin_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Admin Email <span class="text-danger">*</span></label>
                            <input type="email" name="admin_email" class="form-control form-control-sm @error('admin_email') is-invalid @enderror"
                                   value="{{ old('admin_email') }}" placeholder="admin@company.com" required>
                            @error('admin_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Admin Password <span class="text-danger">*</span></label>
                            <input type="password" name="admin_password" class="form-control form-control-sm @error('admin_password') is-invalid @enderror"
                                   placeholder="Minimum 8 characters" required minlength="8">
                            @error('admin_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enabled Services / Features -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-toggle-on"></i>
                    Enabled Services
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3" style="font-size:13px;">
                        Pick which services this partner can sell. Disabled services are hidden from menus and return 404 to visitors of <code>{{ '{subdomain}' }}.gotrips.ai</code>.
                    </p>
                    <div class="row g-3">
                        @foreach($allFeatures as $key => $label)
                            <div class="col-md-6">
                                <div class="form-check" style="padding: 10px 14px 10px 38px; background: #fafafa; border: 1px solid #eee; border-radius: 8px;">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="{{ $key }}"
                                           id="feat-{{ $key }}"
                                           {{ in_array($key, (array) $oldFeatures, true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="feat-{{ $key }}" style="font-weight:500;">
                                        {{ $label }}
                                        <small class="text-muted d-block" style="font-size:11px; font-weight:400;">{{ $key }}</small>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <small class="text-muted d-block mt-2">Tip: leave all checked for full-access agents (like <em>amer</em>); uncheck for restricted resellers (like <em>bhargav</em> = activities only).</small>
                </div>
            </div>

            <!-- Tenant Type + Auto-provision -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-globe"></i>
                    Subdomain Provisioning
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tenant Type</label>
                            <select name="type" class="form-select form-select-sm">
                                <option value="agency" {{ old('type', 'agency') === 'agency' ? 'selected' : '' }}>Agency / B2B Partner</option>
                                <option value="freelancer" {{ old('type') === 'freelancer' ? 'selected' : '' }}>Freelancer</option>
                                <option value="corporate" {{ old('type') === 'corporate' ? 'selected' : '' }}>Corporate</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check" style="width:100%;">
                                <input class="form-check-input" type="checkbox" name="auto_provision" value="1" id="autoProvision" {{ old('auto_provision', '1') ? 'checked' : '' }}>
                                <label class="form-check-label" for="autoProvision">
                                    <strong>Auto-provision subdomain on Hostinger</strong>
                                    <small class="text-muted d-block" style="font-size:11px;">Calls Hostinger API + symlinks to main Laravel app. Requires <code>HOSTINGER_API_TOKEN</code> + <code>HOSTINGER_ORDER_ID</code> in <code>.env</code>.</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Branding -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-palette"></i>
                    White-Label Branding
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Company Logo</label>
                            <input type="file" name="logo" class="form-control form-control-sm @error('logo') is-invalid @enderror"
                                   accept="image/*">
                            <small class="text-muted d-block mt-2">Recommended: 200x200px, PNG or JPG format</small>
                            @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Primary Color</label>
                            <input type="color" name="primary_color" class="form-control form-control-sm form-control-color w-100"
                                   value="{{ old('primary_color', '#F6C343') }}" style="height: 50px;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Secondary Color</label>
                            <input type="color" name="secondary_color" class="form-control form-control-sm form-control-color w-100"
                                   value="{{ old('secondary_color', '#3B82F6') }}" style="height: 50px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Subscription -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-crown"></i>
                    Subscription Plan
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label">Select Plan <span class="text-danger">*</span></label>
                        <select name="plan" class="form-select form-select-sm @error('plan') is-invalid @enderror" required>
                            <option value="trial" {{ old('plan') === 'trial' ? 'selected' : '' }}>Trial (14 days free)</option>
                            <option value="basic" {{ old('plan') === 'basic' ? 'selected' : '' }}>Basic</option>
                            <option value="pro" {{ old('plan') === 'pro' ? 'selected' : '' }}>Professional</option>
                            <option value="enterprise" {{ old('plan') === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                        </select>
                        @error('plan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Markup Percentage</label>
                        <div class="input-group">
                            <input type="number" name="markup_percentage" class="form-control form-control-sm"
                                   value="{{ old('markup_percentage', 20) }}" min="0" max="100" step="0.01">
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="text-muted d-block mt-2">Markup on eSIM cost prices for this company</small>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Commission to Partner</label>
                        <small class="text-muted d-block mb-2" style="font-size:12px;">What this partner earns on every booking from their subdomain.</small>
                        <select name="commission_type" class="form-select form-select-sm mb-2">
                            <option value="percentage" {{ old('commission_type', 'percentage') === 'percentage' ? 'selected' : '' }}>Percentage of order</option>
                            <option value="flat" {{ old('commission_type') === 'flat' ? 'selected' : '' }}>Flat per order</option>
                        </select>
                        <div class="input-group">
                            <input type="number" name="commission_value" class="form-control form-control-sm"
                                   value="{{ old('commission_value', 15) }}" min="0" step="0.01" placeholder="e.g. 15">
                            <span class="input-group-text">% / AED</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cog"></i>
                    Regional Settings
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label">Currency</label>
                        <select name="currency" class="form-select form-select-sm">
                            <option value="AED" {{ old('currency', 'AED') === 'AED' ? 'selected' : '' }}>AED - UAE Dirham</option>
                            <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                            <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            <option value="GBP" {{ old('currency') === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                            <option value="SAR" {{ old('currency') === 'SAR' ? 'selected' : '' }}>SAR - Saudi Riyal</option>
                            <option value="INR" {{ old('currency') === 'INR' ? 'selected' : '' }}>INR - Indian Rupee</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Timezone</label>
                        <select name="timezone" class="form-select form-select-sm">
                            <option value="Asia/Dubai" {{ old('timezone', 'Asia/Dubai') === 'Asia/Dubai' ? 'selected' : '' }}>Dubai (GMT+4)</option>
                            <option value="UTC" {{ old('timezone') === 'UTC' ? 'selected' : '' }}>UTC (GMT+0)</option>
                            <option value="America/New_York" {{ old('timezone') === 'America/New_York' ? 'selected' : '' }}>New York (EST)</option>
                            <option value="Europe/London" {{ old('timezone') === 'Europe/London' ? 'selected' : '' }}>London (GMT)</option>
                            <option value="Asia/Kolkata" {{ old('timezone') === 'Asia/Kolkata' ? 'selected' : '' }}>India (IST)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary btn-sm w-100 mb-2">
                        <i class="fas fa-plus me-1"></i>Create Company
                    </button>
                    <a href="{{ route('superadmin.companies.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
