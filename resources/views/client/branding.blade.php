@extends('layouts.client')

@section('title', 'Branding')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-palette me-2"></i>Branding</h1>
</div>

<form action="{{ route('client.branding.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Company Info -->
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-building me-2"></i>Company Information</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Company Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $company->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
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

            <!-- Visual Branding -->
            <div class="card">
                <div class="card-header"><i class="fas fa-paint-brush me-2"></i>Visual Branding</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Logo</label>
                            @if($company->logo)
                            <div class="mb-3 p-3 rounded" style="background: rgba(0,0,0,0.3);">
                                <img src="{{ $company->logo_url }}" alt="Current Logo" style="max-height: 60px;">
                                <small class="d-block text-muted mt-2">Current logo</small>
                            </div>
                            @endif
                            <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror"
                                   accept="image/*">
                            @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Recommended: PNG or SVG, max 2MB</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Primary Color</label>
                            <input type="color" name="primary_color" class="form-control form-control-color w-100"
                                   value="{{ old('primary_color', $company->primary_color) }}" style="height: 50px;">
                            <small class="text-muted">Main accent color</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Secondary Color</label>
                            <input type="color" name="secondary_color" class="form-control form-control-color w-100"
                                   value="{{ old('secondary_color', $company->secondary_color) }}" style="height: 50px;">
                            <small class="text-muted">Gradient/hover color</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Preview -->
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-eye me-2"></i>Live Preview</div>
                <div class="card-body text-center">
                    <div class="p-4 rounded mb-3" style="background: linear-gradient(135deg, {{ $company->primary_color }} 0%, {{ $company->secondary_color }} 100%);">
                        @if($company->logo)
                        <img src="{{ $company->logo_url }}" alt="Logo" style="max-height: 40px;">
                        @else
                        <span class="text-dark fw-600 fs-5">{{ $company->name }}</span>
                        @endif
                    </div>
                    <button type="button" class="btn w-100 mb-2" style="background: {{ $company->primary_color }}; color: #000;">
                        Primary Button
                    </button>
                    <button type="button" class="btn w-100" style="background: {{ $company->secondary_color }}; color: #000;">
                        Secondary Button
                    </button>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-save me-2"></i>Save Branding
                    </button>
                </div>
            </div>

            <!-- Domain Info -->
            <div class="card mt-4">
                <div class="card-header"><i class="fas fa-globe me-2"></i>Your Domain</div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Subdomain:</strong><br>
                        <code>{{ $company->subdomain }}.gotrips.ai</code>
                    </p>
                    @if($company->domain)
                    <p class="mb-0">
                        <strong>Custom Domain:</strong><br>
                        <code>{{ $company->domain }}</code>
                    </p>
                    @else
                    <p class="text-muted mb-0">
                        <small>Contact support to set up a custom domain.</small>
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
