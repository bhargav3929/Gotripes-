@extends('layouts.manager')

@section('title', 'Preferences')
@section('page-title', 'Preferences')

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

    <h2>Preferences</h2>
    <p class="lede">Currency, timezone, and pricing markup for this account.</p>

    <form action="{{ route('manager.settings.preferences.update') }}" method="POST">
        @csrf

        <div class="form-row">
            <label for="currency">Currency <span class="req">*</span></label>
            <select id="currency" name="currency" required>
                @foreach($currencies as $c)
                    <option value="{{ $c }}" @selected(old('currency', $company->currency ?? 'AED') === $c)>{{ $c }}</option>
                @endforeach
            </select>
            <small>Default currency for prices and finance reports.</small>
        </div>

        <div class="form-row">
            <label for="timezone">Timezone <span class="req">*</span></label>
            <select id="timezone" name="timezone" required>
                @foreach($timezones as $tz)
                    <option value="{{ $tz }}" @selected(old('timezone', $company->timezone ?? 'Asia/Dubai') === $tz)>{{ $tz }}</option>
                @endforeach
            </select>
            <small>Affects how booking timestamps are displayed in your dashboards.</small>
        </div>

        <div class="form-row">
            <label for="markup_percentage">Markup % <span class="req">*</span></label>
            <input type="number" step="0.01" min="0" max="100"
                   id="markup_percentage" name="markup_percentage" required
                   value="{{ old('markup_percentage', (float) ($company->markup_percentage ?? 0)) }}">
            <small>Applied on top of supplier cost when displaying prices to customers (0–100%).</small>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="fas fa-check"></i> Save Preferences
            </button>
        </div>
    </form>
</div>

<style>
    .settings-card {
        background:#1a1a1a; border:1px solid rgba(255,215,0,0.18);
        border-radius:12px; padding:28px 32px; max-width:680px;
    }
    .settings-card h2 { font-size:18px; font-weight:600; color:#fff; margin:0 0 6px; }
    .settings-card .lede { color:#c0c0c0; font-size:13px; margin:0 0 20px; }
    .form-row { margin-bottom:18px; }
    .form-row label { display:block; font-size:13px; font-weight:600; color:#f0f0f0; margin-bottom:6px; }
    .form-row .req { color:#f87171; }
    .form-row input[type=text], .form-row input[type=email], .form-row input[type=number],
    .form-row textarea, .form-row select {
        width:100%; padding:10px 12px; background:#222;
        border:1px solid rgba(255,215,0,0.15); border-radius:8px;
        color:#f0f0f0; font-size:14px;
    }
    .form-row input:focus, .form-row textarea:focus, .form-row select:focus {
        outline:none; border-color:#FFD700; box-shadow:0 0 0 2px rgba(255,215,0,.25);
    }
    .form-row small { display:block; color:#888; font-size:12px; margin-top:4px; }
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
