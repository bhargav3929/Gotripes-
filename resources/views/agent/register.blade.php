<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $registerTenant = current_company();
        $registerTenantName = $registerTenant?->name ?? 'GoTrips';
        $registerTenantLogo = $registerTenant?->logo_url ?? asset('assets/index_files/logo.png');
        $registerTenantFavicon = $registerTenant?->favicon_url ?? asset('assets/index_files/logo.png');
    @endphp
    <link rel="icon" type="image/png" href="{{ $registerTenantFavicon }}">

    <title>Become an Agent - {{ $registerTenantName }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --gold: #FFD700;
            --gold-hover: #FFA500;
            --bg: #1a1a1a;
            --card-bg: #232323;
            --col-bg: #1e1e1e;
            --border: rgba(255, 215, 0, 0.25);
            --border-light: rgba(255, 215, 0, 0.12);
            --text: #f0f0f0;
            --text-muted: #999;
        }
        * { box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
            padding: 14px 16px;
        }
        .wrap { max-width: 1180px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 10px; }
        .header img {
            width: 56px; height: 56px; border-radius: 50%; object-fit: cover;
            border: 2px solid var(--gold);
            box-shadow: 0 0 16px rgba(255, 215, 0, 0.3);
            margin-bottom: 6px;
        }
        .header h1 { color: var(--gold); font-size: 19px; font-weight: 700; margin-bottom: 2px; }
        .header p { color: var(--text-muted); font-size: 12px; }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: 0 0 0 1px rgba(255,215,0,0.06), 0 0 40px rgba(255,215,0,0.08), 0 16px 40px rgba(0,0,0,0.45);
            padding: 14px;
        }

        /* The 3 required sections, kept equal-height and equal-weight so the
           form reads as one balanced screen instead of one tall column. */
        .columns {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            align-items: stretch;
        }
        .col {
            background: var(--col-bg);
            border: 1px solid var(--border-light);
            border-radius: 8px;
            padding: 12px 14px;
            display: flex;
            flex-direction: column;
        }
        .col-title {
            font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
            color: var(--gold); margin-bottom: 8px; padding-bottom: 5px;
            border-bottom: 1px solid var(--border-light);
        }

        .field-row {
            display: grid; grid-template-columns: 1fr 1fr; gap: 8px;
        }

        label { font-size: 11.5px; font-weight: 600; color: var(--text); margin-bottom: 3px; display: block; }
        .required { color: #ef4444; }
        .form-control, .form-select {
            background: #141414; border: 1px solid var(--border); color: var(--text);
            border-radius: 6px; padding: 6px 10px; font-size: 13px; width: 100%;
        }
        .form-control:focus, .form-select:focus {
            background: #141414; color: var(--text); border-color: var(--gold);
            box-shadow: 0 0 0 2px rgba(255,215,0,0.2);
        }
        /* Chrome/Edge paint autofilled fields with their own white background
           by default, ignoring the input's own background-color entirely
           unless overridden this way. */
        .form-control:-webkit-autofill,
        .form-control:-webkit-autofill:hover,
        .form-control:-webkit-autofill:focus,
        .form-control:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 1000px #141414 inset !important;
            -webkit-text-fill-color: var(--text) !important;
            caret-color: var(--text) !important;
            transition: background-color 9999s ease-in-out 0s;
        }
        .form-text { color: var(--text-muted); font-size: 10.5px; }
        .invalid-feedback { display: block; font-size: 11px; }
        .mb-2t { margin-bottom: 8px; }

        .uae-toggle {
            display: flex; gap: 8px; margin-bottom: 8px;
        }
        .uae-toggle label {
            flex: 1; margin: 0; display: flex; align-items: center; gap: 6px;
            border: 1px solid var(--border); border-radius: 6px; padding: 7px 9px;
            cursor: pointer; font-weight: 500; font-size: 12.5px;
        }
        .uae-toggle input { accent-color: var(--gold); }
        .uae-toggle label:has(input:checked) { border-color: var(--gold); background: rgba(255,215,0,0.06); }

        .services-grid {
            display: grid; grid-template-columns: 1fr; gap: 6px;
        }
        .service-option {
            border: 1px solid var(--border); border-radius: 6px; padding: 6px 9px;
            display: flex; align-items: center; gap: 8px; cursor: pointer;
        }
        .service-option:hover { border-color: var(--gold); }
        .service-option input { accent-color: var(--gold); }
        .service-option label { margin: 0; font-weight: 500; cursor: pointer; font-size: 12.5px; }

        /* Secondary row: document upload + credentials, kept compact and
           below the 3 main sections so no single column grows tall. */
        .extra-row {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;
            margin-top: 12px;
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--gold), var(--gold-hover));
            border: none; color: #1a1a1a; font-weight: 700;
            padding: 11px; border-radius: 6px; font-size: 14.5px;
            text-transform: uppercase; letter-spacing: 0.5px;
            margin-top: 14px;
        }
        .btn-submit:hover { box-shadow: 0 4px 15px rgba(255,215,0,0.4); }

        .footer-link { text-align: center; margin-top: 10px; }
        .footer-link a { color: var(--gold); text-decoration: none; font-size: 12.5px; }
        .footer-link a:hover { color: var(--gold-hover); text-decoration: underline; }

        @media (max-width: 900px) {
            .columns, .extra-row { grid-template-columns: 1fr; }
            .field-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <img src="{{ $registerTenantLogo }}" alt="{{ $registerTenantName }}">
        <h1>Become a {{ $registerTenantName }} Agent</h1>
        <p>Register your details, tell us where you operate and what you'd like to sell.</p>
    </div>

    <div class="card">
        @if($errors->any())
            <div class="alert alert-danger py-2">
                <ul class="mb-0" style="font-size: 13px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('agent.register.submit') }}" enctype="multipart/form-data">
            @csrf

            <div class="columns">
                {{-- Section 1: Company / Contact Details --}}
                <div class="col">
                    <div class="col-title">Company / Contact Details</div>

                    <div class="field-row mb-2t">
                        <div>
                            <label>Contact Name <span class="required">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required maxlength="255">
                        </div>
                        <div>
                            <label>Contact No. <span class="required">*</span></label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required maxlength="30">
                        </div>
                    </div>
                    <div class="field-row mb-2t">
                        <div>
                            <label>Email Address <span class="required">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required maxlength="255">
                        </div>
                        <div>
                            <label>Business Name / Company <span class="required">*</span></label>
                            <input type="text" name="company_name" class="form-control" value="{{ old('company_name') }}" required maxlength="255">
                        </div>
                    </div>
                    <div class="field-row mb-2t">
                        <div>
                            <label>Trade License No. <span class="required">*</span></label>
                            <input type="text" name="trade_license_number" class="form-control" value="{{ old('trade_license_number') }}" required maxlength="100">
                        </div>
                        <div>
                            <label>Trade License Expiry <span class="required">*</span></label>
                            <input type="date" name="trade_license_expiry_date" class="form-control" value="{{ old('trade_license_expiry_date') }}" required>
                        </div>
                    </div>
                    <div class="mb-2t">
                        <label>Address <span class="required">*</span></label>
                        <input type="text" name="address" class="form-control" value="{{ old('address') }}" required maxlength="500">
                    </div>
                </div>

                {{-- Section 2: Registration Location --}}
                <div class="col">
                    <div class="col-title">Registering From</div>

                    <div class="mb-2t">
                        <label>Registering from UAE? <span class="required">*</span></label>
                        <div class="uae-toggle">
                            <label>
                                <input type="radio" name="registering_from_uae" id="uaeYes" value="1" {{ old('registering_from_uae', '1') === '1' ? 'checked' : '' }}>
                                In UAE
                            </label>
                            <label>
                                <input type="radio" name="registering_from_uae" id="uaeNo" value="0" {{ old('registering_from_uae') === '0' ? 'checked' : '' }}>
                                Outside UAE
                            </label>
                        </div>
                    </div>

                    <div class="mb-2t" id="emirateBlock">
                        <label>Emirate <span class="required">*</span></label>
                        <select name="emirate" id="emirateSelect" class="form-select">
                            <option value="">Select an Emirate...</option>
                            @foreach($emirates as $e)
                                <option value="{{ $e->emiratesName }}" {{ old('emirate') === $e->emiratesName ? 'selected' : '' }}>{{ $e->emiratesName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2t" id="countryBlock" style="display:none;">
                        <label>Country <span class="required">*</span></label>
                        <select name="country" id="countrySelect" class="form-select">
                            <option value="">Select country...</option>
                            @foreach($countries as $c)
                                <option value="{{ $c }}" {{ old('country') === $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Section 3: Services / Products --}}
                <div class="col">
                    <div class="col-title">Which products or services do you want to sell through us?</div>

                    <div class="services-grid">
                        @foreach($services as $key => $label)
                            <div class="service-option">
                                <input type="checkbox" id="service_{{ $key }}" name="services[]" value="{{ $key }}" {{ in_array($key, old('services', [])) ? 'checked' : '' }}>
                                <label for="service_{{ $key }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                    <p class="form-text mt-2">Once approved, your account gets access to exactly what you select here.</p>
                </div>
            </div>

            {{-- Secondary row: license document + account credentials --}}
            <div class="extra-row">
                <div>
                    <label>Trade License Document <span class="required">*</span></label>
                    <input type="file" name="trade_license_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                    <p class="form-text">PDF, JPG or PNG, up to 5MB.</p>
                </div>
                <div>
                    <label>Password <span class="required">*</span></label>
                    <input type="password" name="password" class="form-control" required minlength="8">
                </div>
                <div>
                    <label>Confirm Password <span class="required">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-paper-plane"></i> Click to Register
            </button>
        </form>
    </div>

    <div class="footer-link">
        Already an approved agent? <a href="{{ route('agent.login') }}">Log in</a> ·
        <a href="{{ url('/') }}">Back to {{ $registerTenantName }}</a>
    </div>
</div>

<script>
    const uaeYes = document.getElementById('uaeYes');
    const uaeNo = document.getElementById('uaeNo');
    const emirateBlock = document.getElementById('emirateBlock');
    const countryBlock = document.getElementById('countryBlock');
    const emirateSelect = document.getElementById('emirateSelect');
    const countrySelect = document.getElementById('countrySelect');

    function syncLocationFields() {
        const isUae = uaeYes.checked;
        emirateBlock.style.display = isUae ? '' : 'none';
        countryBlock.style.display = isUae ? 'none' : '';
        emirateSelect.required = isUae;
        countrySelect.required = !isUae;
    }

    uaeYes.addEventListener('change', syncLocationFields);
    uaeNo.addEventListener('change', syncLocationFields);
    syncLocationFields();
</script>
</body>
</html>
