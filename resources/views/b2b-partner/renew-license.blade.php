<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Renew Trade License - GoTrips</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #1a1a1a; color: #f0f0f0; padding: 40px 16px;
        }
        .wrap { max-width: 480px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 24px; }
        .header h1 { color: #FFD700; font-size: 22px; margin-bottom: 8px; }
        .header p { color: #999; font-size: 13px; }
        .card {
            background: #232323; border: 1px solid rgba(255,215,0,0.25); border-radius: 10px;
            box-shadow: 0 0 0 1px rgba(255,215,0,0.06), 0 0 40px rgba(255,215,0,0.08), 0 16px 40px rgba(0,0,0,0.45);
            padding: 24px;
        }
        label { font-size: 13px; font-weight: 600; margin-bottom: 6px; display: block; }
        .required { color: #ef4444; }
        input {
            width: 100%; background: #1a1a1a; border: 1px solid rgba(255,215,0,0.25); color: #f0f0f0;
            border-radius: 6px; padding: 9px 12px; font-size: 14px; margin-bottom: 16px;
        }
        input:focus { outline: none; border-color: #FFD700; box-shadow: 0 0 0 2px rgba(255,215,0,0.2); }
        .alert-danger {
            background: rgba(214,54,56,0.15); border-left: 4px solid #d63638; padding: 10px 12px;
            border-radius: 0 4px 4px 0; font-size: 13px; color: #f56565; margin-bottom: 16px;
        }
        .btn-submit {
            width: 100%; background: linear-gradient(135deg, #FFD700, #FFA500); border: none; color: #1a1a1a;
            font-weight: 700; padding: 12px; border-radius: 6px; font-size: 15px; text-transform: uppercase;
        }
        .footer-link { text-align: center; margin-top: 16px; }
        .footer-link a { color: #FFD700; text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <h1><i class="fas fa-rotate"></i> Renew Trade License</h1>
        <p>Your account was disabled because your trade license expired. Submit your renewed license below — a manager will confirm it before your access is restored.</p>
    </div>
    <div class="card">
        @if($errors->any())
            <div class="alert-danger">
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('agency.renew-license.submit') }}" enctype="multipart/form-data">
            @csrf
            <label>Account Email <span class="required">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}" required>

            <label>Password <span class="required">*</span></label>
            <input type="password" name="password" required>

            <label>New Trade License Number <span class="required">*</span></label>
            <input type="text" name="trade_license_number" value="{{ old('trade_license_number') }}" required>

            <label>New Expiry Date <span class="required">*</span></label>
            <input type="date" name="trade_license_expiry_date" value="{{ old('trade_license_expiry_date') }}" required>

            <label>New Trade License Document <span class="required">*</span></label>
            <input type="file" name="trade_license_document" accept=".pdf,.jpg,.jpeg,.png" required>

            <button type="submit" class="btn-submit"><i class="fas fa-paper-plane"></i> Submit Renewal</button>
        </form>
    </div>
    <div class="footer-link">
        <a href="{{ route('agency.login') }}">Back to login</a>
    </div>
</div>
</body>
</html>
