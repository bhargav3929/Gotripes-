<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $renewedTenant = current_company();
        $renewedTenantName = $renewedTenant?->name ?? 'GoTrips';
        $renewedTenantLogo = $renewedTenant?->logo_url ?? asset('assets/index_files/logo.png');
        $renewedTenantFavicon = $renewedTenant?->favicon_url ?? asset('assets/index_files/logo.png');
    @endphp
    <title>Renewal Submitted - {{ $renewedTenantName }}</title>
    <link rel="icon" type="image/png" href="{{ $renewedTenantFavicon }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #1a1a1a; color: #f0f0f0;
            min-height: 100vh; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 20px;
        }
        .brand-logo { text-align: center; margin-bottom: 20px; }
        .brand-logo img {
            width: 72px; height: 72px;
            border-radius: 50%; object-fit: cover;
            border: 3px solid #FFD700;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
        }
        .card {
            max-width: 480px; text-align: center;
            background: #232323; border: 1px solid rgba(255,215,0,0.25); border-radius: 10px;
            box-shadow: 0 0 0 1px rgba(255,215,0,0.06), 0 0 40px rgba(255,215,0,0.08), 0 16px 40px rgba(0,0,0,0.45);
            padding: 40px 32px;
        }
        .icon { font-size: 48px; color: #FFD700; margin-bottom: 16px; }
        h1 { font-size: 20px; margin-bottom: 12px; }
        p { color: #999; font-size: 14px; line-height: 1.6; }
        a { color: #FFD700; text-decoration: none; }
    </style>
</head>
<body>
    <div class="brand-logo">
        <img src="{{ $renewedTenantLogo }}" alt="{{ $renewedTenantName }}">
    </div>
    <div class="card">
        <div class="icon"><i class="fas fa-circle-check"></i></div>
        <h1>Renewal Submitted</h1>
        <p>Your renewed trade license has been received and is now awaiting manager confirmation. Your account stays disabled until it's reviewed — you'll be able to log in again once it's confirmed.</p>
        <p style="margin-top:20px;"><a href="{{ url('/') }}">Back to {{ $renewedTenantName }}</a></p>
    </div>
</body>
</html>
