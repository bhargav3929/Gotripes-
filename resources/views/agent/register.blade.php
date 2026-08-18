<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/index_files/logo.png') }}">

    <title>Become an Agent - GoTrips</title>

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

        .services-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 10px;
            margin-bottom: 4px;
        }
        .service-option {
            border: 1px solid var(--border); border-radius: 6px; padding: 10px 12px;
            display: flex; align-items: center; gap: 8px; cursor: pointer;
        }
        .service-option:hover { border-color: var(--gold); }
        .service-option input { accent-color: var(--gold); }
        .service-option label { margin: 0; font-weight: 500; cursor: pointer; }

        .license-block {
            border: 1px solid var(--border);
            border-left: 3px solid var(--gold);
            border-radius: 6px;
            padding: 16px;
            margin: 4px 0 16px;
            background: rgba(255,215,0,0.04);
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--gold), var(--gold-hover));
            border: none; color: #1a1a1a; font-weight: 700;
            padding: 12px; border-radius: 6px; font-size: 15px;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .btn-submit:hover { box-shadow: 0 4px 15px rgba(255,215,0,0.4); }

        .footer-link { text-align: center; margin-top: 20px; }
        .footer-link a { color: var(--gold); text-decoration: none; font-size: 13px; }
        .footer-link a:hover { color: var(--gold-hover); text-decoration: underline; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <h1><i class="fas fa-user-tie"></i> Become a GoTrips Agent</h1>
        <p>Register, tell us which services you're interested in selling, and upload your trade license.</p>
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

        <form method="POST" action="{{ route('agent.register.submit') }}" enctype="multipart/form-data">
            @csrf

            <div class="field-group-label">Your Details</div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Full Name <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required maxlength="255">
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
                    <label>Country</label>
                    <input type="text" name="country" class="form-control" value="{{ old('country') }}" maxlength="100">
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

            <div class="field-group-label">What are you interested in selling? <span class="required">*</span></div>
            <div class="services-grid mb-3">
                @foreach($services as $key => $label)
                    <div class="service-option">
                        <input type="checkbox" id="service_{{ $key }}" name="services[]" value="{{ $key }}" {{ in_array($key, old('services', [])) ? 'checked' : '' }}>
                        <label for="service_{{ $key }}">{{ $label }}</label>
                    </div>
                @endforeach
            </div>
            <p class="form-text mb-3">Once approved, your agent account is automatically set up with access to exactly what you select here.</p>

            <div class="field-group-label">Trade License</div>
            <div class="license-block">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Trade License Number <span class="required">*</span></label>
                        <input type="text" name="trade_license_number" class="form-control" value="{{ old('trade_license_number') }}" required maxlength="100">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Trade License Expiry Date <span class="required">*</span></label>
                        <input type="date" name="trade_license_expiry_date" class="form-control" value="{{ old('trade_license_expiry_date') }}" required>
                    </div>
                    <div class="col-md-12 mb-0">
                        <label>Trade License Document <span class="required">*</span></label>
                        <input type="file" name="trade_license_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                        <p class="form-text">PDF, JPG or PNG, up to 5MB. An admin reviews this before your account is activated.</p>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-submit mt-2">
                <i class="fas fa-paper-plane"></i> Submit Application
            </button>
        </form>
    </div>

    <div class="footer-link">
        Already an approved agent? <a href="{{ route('agent.login') }}">Log in</a> ·
        <a href="{{ url('/') }}">Back to GoTrips</a>
    </div>
</div>
</body>
</html>
