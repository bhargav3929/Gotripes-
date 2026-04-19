@extends('layouts.superadmin')

@section('title', 'Create Company')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-plus-circle"></i>Create Company</h1>
    <a href="{{ route('superadmin.companies.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back
    </a>
</div>

<form action="{{ route('superadmin.companies.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Company Details -->
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-building"></i>Company Details</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Company Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="Enter company name" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                                   value="{{ old('slug') }}" placeholder="Auto-generated if empty">
                            @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subdomain</label>
                            <div class="input-group">
                                <input type="text" name="subdomain" class="form-control @error('subdomain') is-invalid @enderror"
                                       value="{{ old('subdomain') }}" placeholder="company">
                                <span class="input-group-text">.gotrips.ai</span>
                            </div>
                            @error('subdomain')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Custom Domain</label>
                            <input type="text" name="domain" class="form-control @error('domain') is-invalid @enderror"
                                   value="{{ old('domain') }}" placeholder="www.company.com">
                            @error('domain')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" placeholder="company@example.com" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone') }}" placeholder="+971 XX XXX XXXX">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin User -->
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-user-shield"></i>Admin User (Company Owner)</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Admin Name <span class="text-danger">*</span></label>
                            <input type="text" name="admin_name" class="form-control @error('admin_name') is-invalid @enderror"
                                   value="{{ old('admin_name') }}" placeholder="Full name" required>
                            @error('admin_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Admin Email <span class="text-danger">*</span></label>
                            <input type="email" name="admin_email" class="form-control @error('admin_email') is-invalid @enderror"
                                   value="{{ old('admin_email') }}" placeholder="admin@company.com" required>
                            @error('admin_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Admin Password <span class="text-danger">*</span></label>
                            <input type="password" name="admin_password" class="form-control @error('admin_password') is-invalid @enderror"
                                   placeholder="Minimum 8 characters" required minlength="8">
                            @error('admin_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Branding -->
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-palette"></i>Branding</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Company Logo</label>
                            <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror"
                                   accept="image/*">
                            <small class="text-muted">Recommended: 200x200px, PNG or JPG</small>
                            @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Primary Color</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" name="primary_color" class="form-control form-control-color"
                                       value="{{ old('primary_color', '#8b5cf6') }}" style="width: 50px; height: 44px;">
                                <input type="text" class="form-control" value="{{ old('primary_color', '#8b5cf6') }}" readonly style="flex: 1;">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Secondary Color</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" name="secondary_color" class="form-control form-control-color"
                                       value="{{ old('secondary_color', '#06b6d4') }}" style="width: 50px; height: 44px;">
                                <input type="text" class="form-control" value="{{ old('secondary_color', '#06b6d4') }}" readonly style="flex: 1;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Subscription -->
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-credit-card"></i>Subscription</div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label">Plan <span class="text-danger">*</span></label>
                        <select name="plan" class="form-select @error('plan') is-invalid @enderror" required>
                            <option value="trial" {{ old('plan') === 'trial' ? 'selected' : '' }}>Trial (14 days)</option>
                            <option value="basic" {{ old('plan') === 'basic' ? 'selected' : '' }}>Basic</option>
                            <option value="pro" {{ old('plan') === 'pro' ? 'selected' : '' }}>Pro</option>
                            <option value="enterprise" {{ old('plan') === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                        </select>
                        @error('plan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="form-label">Markup Percentage</label>
                        <div class="input-group">
                            <input type="number" name="markup_percentage" class="form-control"
                                   value="{{ old('markup_percentage', 20) }}" min="0" max="100" step="0.01">
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="text-muted">Markup applied to eSIM cost prices</small>
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-cog"></i>Settings</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Currency</label>
                        <select name="currency" class="form-select">
                            <option value="AED" {{ old('currency', 'AED') === 'AED' ? 'selected' : '' }}>AED - UAE Dirham</option>
                            <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                            <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            <option value="GBP" {{ old('currency') === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Timezone</label>
                        <select name="timezone" class="form-select">
                            <option value="Asia/Dubai" {{ old('timezone', 'Asia/Dubai') === 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai (GMT+4)</option>
                            <option value="UTC" {{ old('timezone') === 'UTC' ? 'selected' : '' }}>UTC (GMT+0)</option>
                            <option value="America/New_York" {{ old('timezone') === 'America/New_York' ? 'selected' : '' }}>America/New_York (EST)</option>
                            <option value="Europe/London" {{ old('timezone') === 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="fas fa-plus me-2"></i>Create Company
                    </button>
                    <a href="{{ route('superadmin.companies.index') }}" class="btn btn-outline-secondary w-100">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
