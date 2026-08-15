<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/index_files/logo.png') }}">

    <title>Become a B2B Partner - GoTrips</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --gold: #FFD700;
            --gold-hover: #FFA500;
            --bg: #1a1a1a;
            --card-bg: #232323;
            --border: rgba(255, 215, 0, 0.25);
            --border-light: rgba(255, 215, 0, 0.12);
            --text: #f0f0f0;
            --text-muted: #999;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
            padding: 40px 16px;
        }
        .wrap { max-width: 760px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 28px; }
        .header h1 { color: var(--gold); font-size: 24px; font-weight: 700; margin-bottom: 8px; }
        .header p { color: var(--text-muted); font-size: 14px; }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: 0 0 0 1px rgba(255,215,0,0.06), 0 0 40px rgba(255,215,0,0.08), 0 16px 40px rgba(0,0,0,0.45);
            padding: 28px;
        }

        .field-group-label {
            font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
            color: var(--gold); margin: 20px 0 12px; padding-bottom: 6px;
            border-bottom: 1px solid var(--border-light);
        }
        .field-group-label:first-child { margin-top: 0; }

        label { font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 6px; display: block; }
        .required { color: #ef4444; }
        .form-control, .form-select {
            background: #1a1a1a; border: 1px solid var(--border); color: var(--text);
            border-radius: 6px; padding: 9px 12px; font-size: 14px;
        }
        .form-control:focus, .form-select:focus {
            background: #1a1a1a; color: var(--text); border-color: var(--gold);
            box-shadow: 0 0 0 2px rgba(255,215,0,0.2);
        }
        .form-text { color: var(--text-muted); font-size: 12px; }
        .invalid-feedback { display: block; font-size: 12px; }
        .mb-3 { margin-bottom: 16px !important; }

        .uae-block {
            display: none;
            border: 1px solid var(--border);
            border-left: 3px solid var(--gold);
            border-radius: 6px;
            padding: 16px;
            margin: 4px 0 16px;
            background: rgba(255,215,0,0.04);
        }
        .uae-block.show { display: block; }

        .signature-box {
            border: 1px dashed var(--border);
            border-radius: 6px;
            padding: 16px;
            margin-top: 8px;
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--gold), var(--gold-hover));
            border: none; color: #1a1a1a; font-weight: 700;
            padding: 12px; border-radius: 6px; font-size: 15px;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .btn-submit:hover { box-shadow: 0 4px 15px rgba(255,215,0,0.4); }

        .form-check-input:checked { background-color: var(--gold); border-color: var(--gold); }

        .footer-link { text-align: center; margin-top: 20px; }
        .footer-link a { color: var(--gold); text-decoration: none; font-size: 13px; }
        .footer-link a:hover { color: var(--gold-hover); text-decoration: underline; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <h1><i class="fas fa-handshake"></i> Become a B2B Partner</h1>
        <p>Register your agency, upload your trade license (UAE only), and sign your partner agreement online.</p>
    </div>

    <div class="card">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('agency.register.submit') }}" enctype="multipart/form-data">
            @csrf

            <div class="field-group-label">Your Agency</div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Company Name <span class="required">*</span></label>
                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name') }}" required maxlength="255">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Contact Person <span class="required">*</span></label>
                    <input type="text" name="contact_name" class="form-control" value="{{ old('contact_name') }}" required maxlength="255">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Email <span class="required">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required maxlength="255">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Phone <span class="required">*</span></label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required maxlength="30">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Country <span class="required">*</span></label>
                    <select name="country" id="partnerCountry" class="form-select" required>
                        <option value="">Select country...</option>
                        @foreach(['United Arab Emirates','Saudi Arabia','Qatar','Kuwait','Bahrain','Oman','India','Pakistan','Bangladesh','United Kingdom','United States','Canada','Australia','Germany','France','Italy','Spain','Egypt','Jordan','Lebanon','Turkey','Philippines','Indonesia','Malaysia','Singapore','Sri Lanka','Nepal','Nigeria','Kenya','South Africa','Brazil','Mexico','Argentina','Russia','China','Japan','South Korea'] as $c)
                            <option value="{{ $c }}" {{ old('country') === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Password <span class="required">*</span></label>
                    <input type="password" name="password" class="form-control" required minlength="8">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Confirm Password <span class="required">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                </div>
            </div>

            <div class="uae-block" id="uaeFields">
                <div class="field-group-label" style="margin-top:0;"><i class="fas fa-shield-halved"></i> Trade License (UAE only)</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Trade License Number</label>
                        <input type="text" name="trade_license_number" class="form-control" value="{{ old('trade_license_number') }}" maxlength="100">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Trade License Expiry Date</label>
                        <input type="date" name="trade_license_expiry_date" class="form-control" value="{{ old('trade_license_expiry_date') }}">
                    </div>
                    <div class="col-md-12 mb-0">
                        <label>Trade License Document</label>
                        <input type="file" name="trade_license_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        <p class="form-text">PDF, JPG or PNG, up to 5MB.</p>
                    </div>
                </div>
            </div>

            <div class="field-group-label">Partner Agreement</div>
            <p class="form-text">
                Based on your country, a <strong id="contractLabel">National</strong> Partner Agreement will be generated for you to review and sign below.
                Sample/placeholder contract text — the final legal wording will be provided by GoTrips.
            </p>
            <div class="signature-box">
                <div class="mb-3">
                    <label>Type your full legal name to sign <span class="required">*</span></label>
                    <input type="text" name="signature_full_name" class="form-control" value="{{ old('signature_full_name') }}" required maxlength="255" placeholder="e.g. Ahmed Al Mansouri">
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="signatureAgreed" name="signature_agreed" value="1" required>
                    <label class="form-check-label" for="signatureAgreed" style="font-weight:400;">
                        I have read and agree to the Partner Agreement generated for my account, and I understand this typed signature, together with my IP address and timestamp, constitutes my electronic signature.
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-submit mt-4">
                <i class="fas fa-paper-plane"></i> Submit Application
            </button>
        </form>
    </div>

    <div class="footer-link">
        Already registered? <a href="{{ route('agency.login') }}">Log in</a> ·
        <a href="{{ url('/') }}">Back to GoTrips</a>
    </div>
</div>

<script>
    const UAE_COUNTRY = @json(\App\Models\B2bPartner::UAE_COUNTRY_NAME);
    const countrySelect = document.getElementById('partnerCountry');
    const uaeBlock = document.getElementById('uaeFields');
    const contractLabel = document.getElementById('contractLabel');

    function syncUaeFields() {
        const isUae = countrySelect.value === UAE_COUNTRY;
        uaeBlock.classList.toggle('show', isUae);
        contractLabel.textContent = isUae ? 'National' : 'International';

        uaeBlock.querySelectorAll('input').forEach(function (el) {
            el.required = isUae;
        });
    }

    countrySelect.addEventListener('change', syncUaeFields);
    syncUaeFields();
</script>
</body>
</html>
