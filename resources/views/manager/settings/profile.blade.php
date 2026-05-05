@extends('layouts.manager')

@section('title', 'Profile & Branding')
@section('page-title', 'Profile & Branding')

@section('content')
<div class="settings-card">
    @if(session('success'))
        <div class="settings-alert settings-alert-ok">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="settings-alert settings-alert-err">
            <ul style="margin:0; padding-left:18px;">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h2>Profile & Branding</h2>
    <p class="lede">This is what your customers see on your subdomain.</p>

    <form action="{{ route('manager.settings.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-row">
            <label for="name">Company name <span class="req">*</span></label>
            <input type="text" id="name" name="name" required maxlength="255"
                   value="{{ old('name', $company->name) }}">
        </div>

        <div class="form-row">
            <label for="email">Public email <span class="req">*</span></label>
            <input type="email" id="email" name="email" required maxlength="255"
                   value="{{ old('email', $company->email) }}">
            <small>Used for contact form leads, booking confirmations, and public listings.</small>
        </div>

        <div class="form-row">
            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" maxlength="30"
                   value="{{ old('phone', $company->phone) }}"
                   placeholder="+971 50 000 0000">
            <small>Shown on the contact page and the WhatsApp button.</small>
        </div>

        <div class="form-row">
            <label for="address">Address</label>
            <textarea id="address" name="address" rows="3" maxlength="500"
                      placeholder="Street, City, Country">{{ old('address', $company->address) }}</textarea>
            <small>Used to render the embedded map on the contact page.</small>
        </div>

        <div class="form-row">
            <label for="logo">Logo</label>
            <div class="logo-row">
                <img src="{{ $company->logo_url }}" alt="Current logo" class="logo-preview">
                <div>
                    <input type="file" id="logo" name="logo" accept="image/jpeg,image/png,image/jpg,image/svg+xml,image/webp">
                    <small>PNG, JPG, SVG, or WEBP. Max 2 MB. Square logos look best.</small>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="fas fa-check"></i> Save Changes
            </button>
        </div>
    </form>
</div>

<style>
    .settings-card {
        background:#1a1a1a; border:1px solid rgba(255,215,0,0.18);
        border-radius:12px; padding:28px 32px; max-width:760px;
    }
    .settings-card h2 { font-size:18px; font-weight:600; color:#fff; margin:0 0 6px; }
    .settings-card .lede { color:#c0c0c0; font-size:13px; margin:0 0 20px; }
    .form-row { margin-bottom:18px; }
    .form-row label { display:block; font-size:13px; font-weight:600; color:#f0f0f0; margin-bottom:6px; }
    .form-row .req { color:#f87171; }
    .form-row input[type=text],
    .form-row input[type=email],
    .form-row textarea,
    .form-row select {
        width:100%; padding:10px 12px; background:#222;
        border:1px solid rgba(255,215,0,0.15); border-radius:8px;
        color:#f0f0f0; font-size:14px;
    }
    .form-row input:focus, .form-row textarea:focus, .form-row select:focus {
        outline:none; border-color:#FFD700; box-shadow:0 0 0 2px rgba(255,215,0,.25);
    }
    .form-row small { display:block; color:#888; font-size:12px; margin-top:4px; }
    .logo-row { display:flex; gap:14px; align-items:flex-start; }
    .logo-preview {
        width:64px; height:64px; border-radius:8px; object-fit:contain;
        background:#fff; padding:6px; border:1px solid rgba(255,215,0,.15);
    }
    .form-actions { margin-top:24px; }
    .btn-save {
        background:linear-gradient(135deg,#FFD700,#FFA500); color:#1a1a1a;
        font-weight:600; font-size:14px; padding:11px 28px;
        border:none; border-radius:8px; cursor:pointer;
    }
    .btn-save:hover { transform:translateY(-1px); box-shadow:0 6px 20px rgba(255,215,0,.25); }
    .settings-alert { padding:10px 14px; border-radius:8px; font-size:13px; margin-bottom:16px; }
    .settings-alert-ok  { background:rgba(34,197,94,.15); border:1px solid rgba(34,197,94,.4); color:#4ade80; }
    .settings-alert-err { background:rgba(214,54,56,.15); border:1px solid rgba(214,54,56,.4); color:#f87171; }
</style>
@endsection
