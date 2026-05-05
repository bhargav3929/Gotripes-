@extends('layouts.manager')

@section('title', 'Feature Settings')
@section('page-title', 'Feature Settings')

@section('content')
<style>
    .features-card {
        background: #1a1a1a;
        border: 1px solid rgba(255, 215, 0, 0.18);
        border-radius: 12px;
        padding: 28px 32px;
        max-width: 880px;
    }
    .features-card h2 {
        font-size: 18px; font-weight: 600;
        color: #fff;
        margin: 0 0 6px;
    }
    .features-card .lede {
        color: #c0c0c0; font-size: 13px; margin: 0 0 20px;
        line-height: 1.55;
    }
    .features-tenant {
        display: inline-flex; align-items: center; gap: 8px;
        background: rgba(255, 215, 0, 0.08);
        border: 1px solid rgba(255, 215, 0, 0.25);
        color: #FFD700;
        padding: 6px 12px;
        border-radius: 99px;
        font-size: 12px; font-weight: 600;
        margin-bottom: 20px;
    }
    .features-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin: 20px 0 24px;
    }
    .feature-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 16px;
        background: #232323;
        border: 1px solid rgba(255, 215, 0, 0.10);
        border-radius: 10px;
        transition: all 0.15s;
    }
    .feature-row:hover { border-color: rgba(255, 215, 0, 0.28); background: #262626; }
    .feature-row label {
        margin: 0; flex: 1; cursor: pointer;
        color: #f0f0f0; font-size: 14px; font-weight: 500;
    }
    .feature-row .feat-key { display:block; color:#888; font-size:11px; font-weight:400; margin-top:2px; letter-spacing:0.5px; }
    .toggle {
        position: relative; width: 42px; height: 24px;
        background: #444; border-radius: 99px; cursor: pointer;
        transition: background 0.2s;
    }
    .toggle::before {
        content: ''; position: absolute; top: 2px; left: 2px;
        width: 20px; height: 20px; border-radius: 50%;
        background: #fff; transition: left 0.2s;
    }
    input[type="checkbox"]:checked + .toggle { background: #FFD700; }
    input[type="checkbox"]:checked + .toggle::before { left: 20px; }
    .feature-row input[type="checkbox"] { display: none; }
    .save-btn {
        background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        color: #000; border: none;
        font-weight: 600; font-size: 14px;
        padding: 11px 28px;
        border-radius: 8px; cursor: pointer;
        transition: all 0.2s;
    }
    .save-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(255, 215, 0, 0.25); }
    .alert-ok { background: rgba(0, 163, 42, 0.15); border: 1px solid rgba(0, 163, 42, 0.4); color: #4ade80; padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
    .alert-err { background: rgba(214, 54, 56, 0.15); border: 1px solid rgba(214, 54, 56, 0.4); color: #f87171; padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
    @media (max-width: 640px) { .features-grid { grid-template-columns: 1fr; } }
</style>

<div class="features-card">
    @if(session('success'))<div class="alert-ok">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-err">{{ session('error') }}</div>@endif

    @if($company)
        <div class="features-tenant">
            <i class="fas fa-building"></i>
            {{ $company->name }} <span style="opacity:0.6;">·</span> {{ $company->subdomain }}.gotrips.ai
        </div>
    @else
        <div class="alert-err">
            No tenant company is bound to this session. Feature toggles can only be edited from a tenant subdomain (e.g. <code>bhargav.gotrips.ai/manager</code>).
        </div>
    @endif

    <h2>Enabled Services</h2>
    <p class="lede">
        These are the services your account is currently allowed to sell.
        To enable or disable a service, contact your platform administrator —
        tenants cannot change their own access.
    </p>

    <div class="features-grid">
        @foreach($allFeatures as $key => $label)
            @php $isOn = in_array($key, $enabled, true); @endphp
            <div class="feature-row">
                <label>
                    {{ $label }}
                    <span class="feat-key">{{ $key }}</span>
                </label>
                <span class="feat-status {{ $isOn ? 'on' : 'off' }}">
                    <i class="fas {{ $isOn ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                    {{ $isOn ? 'Enabled' : 'Disabled' }}
                </span>
            </div>
        @endforeach
    </div>
</div>

<style>
    .feat-status {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 12px; font-weight: 600;
        padding: 6px 12px; border-radius: 99px;
    }
    .feat-status.on  { background: rgba(34,197,94,0.15);  color: #4ade80; border: 1px solid rgba(34,197,94,0.3); }
    .feat-status.off { background: rgba(214,54,56,0.10);  color: #888;     border: 1px solid rgba(214,54,56,0.2); }
</style>
@endsection
