<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agency Dashboard - GoTrips</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #1a1a1a; color: #f0f0f0; padding: 32px 16px;
        }
        .wrap { max-width: 640px; margin: 0 auto; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .topbar h1 { font-size: 20px; color: #FFD700; }
        .topbar a { color: #999; font-size: 13px; text-decoration: none; }
        .topbar a:hover { color: #FFD700; }
        .card {
            background: #232323; border: 1px solid rgba(255,215,0,0.25); border-radius: 10px;
            padding: 24px; margin-bottom: 20px;
        }
        .badge {
            display: inline-block; padding: 4px 10px; border-radius: 4px;
            font-size: 12px; font-weight: 600; text-transform: uppercase;
        }
        .badge-approved { background: rgba(0,163,42,0.15); color: #5cbf70; }
        .badge-warning { background: rgba(219,166,23,0.15); color: #fbd835; }
        .row-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid rgba(255,215,0,0.08); font-size: 14px; }
        .row-item:last-child { border-bottom: none; }
        .row-item span:first-child { color: #999; }
        .btn { display: inline-block; background: linear-gradient(135deg, #FFD700, #FFA500); color: #1a1a1a; font-weight: 700; padding: 10px 18px; border-radius: 6px; text-decoration: none; font-size: 14px; }
        .btn:hover { color: #1a1a1a; box-shadow: 0 4px 15px rgba(255,215,0,0.4); }
        .expiry-banner { background: rgba(214,54,56,0.15); border-left: 4px solid #d63638; padding: 12px 16px; border-radius: 0 6px 6px 0; margin-bottom: 20px; font-size: 14px; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="topbar">
        <h1><i class="fas fa-handshake"></i> {{ $partner->company_name }}</h1>
        <form method="POST" action="{{ route('agency.logout') }}">
            @csrf
            <a href="#" onclick="this.closest('form').submit(); return false;">Log out</a>
        </form>
    </div>

    @if($daysUntilExpiry !== null && $daysUntilExpiry <= 45)
        <div class="expiry-banner">
            <i class="fas fa-triangle-exclamation"></i>
            Your trade license expires in {{ $daysUntilExpiry }} day(s) ({{ $partner->trade_license_expiry_date->format('d M Y') }}).
            Please renew and contact your account manager to update your details before it lapses.
        </div>
    @endif

    <div class="card">
        <span class="badge badge-approved">Approved</span>
        <div class="row-item"><span>Contact</span><span>{{ $partner->contact_name }}</span></div>
        <div class="row-item"><span>Email</span><span>{{ $partner->email }}</span></div>
        <div class="row-item"><span>Country</span><span>{{ $partner->country }}</span></div>
        <div class="row-item"><span>Contract Type</span><span>{{ ucfirst($partner->contract_type) }}</span></div>
        @if($partner->isUae())
            <div class="row-item"><span>Trade License</span><span>{{ $partner->trade_license_number }}</span></div>
            <div class="row-item"><span>License Expiry</span><span>{{ $partner->trade_license_expiry_date->format('d M Y') }}</span></div>
        @endif
        <div class="row-item"><span>Approved On</span><span>{{ optional($partner->reviewed_at)->format('d M Y') }}</span></div>
    </div>

    <a href="{{ route('agency.contract.download') }}" class="btn"><i class="fas fa-file-pdf"></i> Download Signed Contract</a>
</div>
</body>
</html>
