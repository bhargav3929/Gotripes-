@extends('layouts.manager')

@section('title', 'Booking Notifications')
@section('page-title', 'Booking Notifications')

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

    <h2>Booking Notifications</h2>
    <p class="lede">
        Who gets emailed when a customer books. Enter one or more addresses per product,
        separated by commas. Leave a box empty to fall back to your account email
        (<strong>{{ $company->email ?? 'not set' }}</strong>).
    </p>

    <form action="{{ route('manager.settings.notifications.update') }}" method="POST">
        @csrf

        @foreach($services as $key => $label)
            <div class="form-row">
                <label for="emails_{{ $key }}">{{ $label }}</label>
                <input type="text"
                       id="emails_{{ $key }}"
                       name="emails[{{ $key }}]"
                       value="{{ old('emails.'.$key, $emails[$key] ?? '') }}"
                       placeholder="ops@yourbusiness.com, owner@yourbusiness.com">
                <small>Notified the moment an {{ $label }} booking is placed.</small>
            </div>
        @endforeach

        <p class="lede" style="margin-top:8px;font-size:13px;">
            <strong>Activities</strong> use their own per-activity recipient list, set on each
            activity's edit page — not here.
        </p>

        <div class="form-actions" style="margin-top:18px;">
            <button type="submit" class="btn-save">Save recipients</button>
        </div>
    </form>

    <div class="test-block">
        <h3>Test delivery</h3>
        <p>Save your recipients above, then send a sample email to confirm it lands in the inbox.
           No booking or payment is made — it's just a delivery check.</p>
        <form action="{{ route('manager.settings.notifications.test') }}" method="POST">
            @csrf
            <button type="submit" class="btn-test">Send test email now</button>
        </form>
    </div>
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
    .form-row input[type=text] {
        width:100%; padding:10px 12px; background:#222;
        border:1px solid rgba(255,215,0,0.15); border-radius:8px;
        color:#f0f0f0; font-size:14px;
    }
    .form-row input:focus {
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
    .test-block { margin-top:28px; padding-top:22px; border-top:1px solid rgba(255,215,0,.15); }
    .test-block h3 { font-size:15px; font-weight:600; color:#fff; margin:0 0 6px; }
    .test-block p { color:#9a9a9a; font-size:12.5px; margin:0 0 14px; line-height:1.6; }
    .btn-test {
        background:transparent; color:#FFD700; font-weight:600; font-size:13px;
        padding:9px 20px; border:1px solid rgba(255,215,0,.5); border-radius:8px; cursor:pointer;
    }
    .btn-test:hover { background:rgba(255,215,0,.1); }
</style>
@endsection
