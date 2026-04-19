<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ $company->name ?? 'Client Portal' }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @php
        $company = app('current_company');
        $primaryColor = $company->primary_color ?? '#FFD700';
        $secondaryColor = $company->secondary_color ?? '#FFA500';
    @endphp

    <style>
        :root {
            --gold: #FFD700;
            --gold-dark: #B8860B;
            --gold-light: #FFED4A;
            --dark-1: #0a0a0a;
            --dark-2: #111111;
            --dark-3: #1a1a1a;
            --dark-4: #222222;
            --border: #333333;
            --text: #ffffff;
            --text-muted: #888888;
        }

        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }

        body {
            background: var(--dark-1);
            color: var(--text);
            min-height: 100vh;
            margin: 0;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 240px;
            height: 100vh;
            background: var(--dark-2);
            border-right: 1px solid var(--border);
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 16px;
            border-bottom: 1px solid var(--border);
            text-align: center;
        }

        .sidebar-logo {
            max-height: 50px;
            max-width: 140px;
        }

        .sidebar-brand {
            font-size: 1rem;
            font-weight: 700;
            color: var(--gold);
            text-decoration: none;
        }

        .sidebar-nav { padding: 12px 8px; }

        .nav-section { margin-bottom: 16px; }

        .nav-section-title {
            font-size: 0.65rem;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 8px 12px 4px;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            color: var(--text-muted);
            border-radius: 6px;
            margin-bottom: 2px;
            transition: all 0.2s;
            text-decoration: none;
            font-size: 0.85rem;
        }

        .nav-link:hover {
            background: var(--dark-3);
            color: var(--text);
        }

        .nav-link.active {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: #000;
            font-weight: 600;
        }

        .nav-link i {
            width: 20px;
            margin-right: 10px;
            font-size: 0.85rem;
        }

        /* Main Content */
        .main-content {
            margin-left: 240px;
            padding: 20px 24px;
            min-height: 100vh;
            background: var(--dark-1);
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            color: var(--gold);
        }

        /* Cards */
        .card {
            background: var(--dark-2);
            border: 1px solid var(--border);
            border-radius: 10px;
        }

        .card-header {
            background: var(--dark-3);
            border-bottom: 1px solid var(--border);
            padding: 12px 16px;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text);
        }

        .card-body { padding: 16px; }

        .card-footer {
            background: var(--dark-3);
            border-top: 1px solid var(--border);
            padding: 12px 16px;
        }

        /* Stats */
        .stat-card {
            background: linear-gradient(145deg, var(--dark-2) 0%, var(--dark-3) 100%);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 16px;
            transition: all 0.3s;
        }

        .stat-card:hover {
            border-color: var(--gold);
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(255, 215, 0, 0.1);
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            margin-bottom: 12px;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: #000;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 4px;
            color: var(--text);
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* Table */
        .table {
            color: #ffffff !important;
            margin: 0;
        }

        .table thead th {
            background: var(--dark-3) !important;
            color: #aaaaaa !important;
            font-size: 0.7rem;
            text-transform: uppercase;
            font-weight: 600;
            padding: 10px 12px;
            border-bottom: 1px solid var(--border);
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            background: var(--dark-2) !important;
            color: #ffffff !important;
        }

        .table tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            font-size: 0.85rem;
            background: var(--dark-2) !important;
            color: #ffffff !important;
        }

        .table tbody td div,
        .table tbody td span:not(.badge) {
            color: #ffffff !important;
        }

        .table tbody td small,
        .table tbody td .text-muted {
            color: #888888 !important;
        }

        .table tbody tr:hover {
            background: var(--dark-3) !important;
        }

        .table tbody tr:hover td {
            background: var(--dark-3) !important;
        }

        .table-responsive {
            background: var(--dark-2);
        }

        /* Ensure all text is visible */
        .card, .card-body, .card-header, .card-footer {
            color: #ffffff;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            border: none;
            color: #000;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--gold-light) 0%, var(--gold) 100%);
            color: #000;
        }

        .btn-outline-primary {
            border: 1px solid var(--gold);
            color: var(--gold);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--gold);
            color: #000;
        }

        .btn-outline-secondary {
            border: 1px solid var(--border);
            color: var(--text-muted);
            background: transparent;
        }

        .btn-outline-secondary:hover {
            background: var(--dark-3);
            color: var(--text);
            border-color: var(--text-muted);
        }

        .btn-sm { font-size: 0.75rem; padding: 6px 12px; }

        /* Badge */
        .badge { font-size: 0.7rem; padding: 4px 8px; font-weight: 500; }
        .badge.bg-success { background: #10b981 !important; }
        .badge.bg-warning { background: #f59e0b !important; color: #000 !important; }
        .badge.bg-danger { background: #ef4444 !important; }
        .badge.bg-info { background: #3b82f6 !important; }

        /* Form */
        .form-control, .form-select {
            background: var(--dark-3);
            border: 1px solid var(--border);
            color: var(--text);
            font-size: 0.85rem;
            padding: 8px 12px;
        }

        .form-control:focus, .form-select:focus {
            background: var(--dark-3);
            border-color: var(--gold);
            color: var(--text);
            box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.2);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .form-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 4px;
            font-weight: 500;
        }

        /* Subscription Banner */
        .subscription-banner {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: #000;
            padding: 10px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
        }

        .subscription-banner .btn-dark {
            background: #000;
            border: none;
            font-size: 0.75rem;
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 8px;
            font-size: 0.85rem;
            padding: 10px 16px;
        }

        .alert-success { background: rgba(16, 185, 129, 0.15); color: #10b981; }
        .alert-danger { background: rgba(239, 68, 68, 0.15); color: #ef4444; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--dark-2); }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-muted); }

        /* Pagination */
        .pagination { margin: 0; }
        .page-link,
        nav[aria-label="Pagination Navigation"] a,
        nav[aria-label="Pagination Navigation"] span,
        nav[aria-label="Pagination Navigation"] button {
            background: #222 !important;
            border-color: #333 !important;
            color: #fff !important;
            font-size: 0.8rem;
            padding: 6px 12px;
        }
        .page-link:hover,
        nav[aria-label="Pagination Navigation"] a:hover {
            background: #333 !important;
            border-color: var(--gold) !important;
            color: var(--gold) !important;
        }
        .page-item.active .page-link {
            background: var(--gold) !important;
            border-color: var(--gold) !important;
            color: #000 !important;
        }
        .page-link svg,
        nav svg {
            width: 12px !important;
            height: 12px !important;
            max-width: 12px !important;
            max-height: 12px !important;
            display: inline-block !important;
            fill: currentColor !important;
        }
        .page-item.disabled .page-link,
        nav[aria-label="Pagination Navigation"] span[aria-disabled="true"] {
            background: #1a1a1a !important;
            border-color: #333 !important;
            color: #666 !important;
        }
        .w-5, .h-5 {
            width: 12px !important;
            height: 12px !important;
        }
        nav[aria-label="Pagination Navigation"] {
            background: transparent !important;
        }
        nav[aria-label="Pagination Navigation"] > div {
            background: transparent !important;
        }
        nav[aria-label="Pagination Navigation"] p {
            color: #888 !important;
        }

        /* Text utilities */
        .text-gold { color: var(--gold) !important; }
        .text-muted { color: var(--text-muted) !important; }
        .fw-500 { font-weight: 500; }
        .fw-600 { font-weight: 600; }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            @if($company && $company->logo)
            <img src="{{ $company->logo_url }}" alt="{{ $company->name }}" class="sidebar-logo">
            @else
            <span class="sidebar-brand">{{ $company->name ?? 'Client Portal' }}</span>
            @endif
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Overview</div>
                <a href="{{ route('client.dashboard') }}" class="nav-link {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i> Dashboard
                </a>
                <a href="{{ route('client.analytics') }}" class="nav-link {{ request()->routeIs('client.analytics') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i> Analytics
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Bookings</div>
                <a href="{{ route('client.orders') }}" class="nav-link {{ request()->routeIs('client.orders*') ? 'active' : '' }}">
                    <i class="fas fa-sim-card"></i> eSIM Orders
                </a>
                <a href="{{ route('client.visa') }}" class="nav-link {{ request()->routeIs('client.visa') ? 'active' : '' }}">
                    <i class="fas fa-passport"></i> Visa Applications
                </a>
                <a href="{{ route('client.activities') }}" class="nav-link {{ request()->routeIs('client.activities') ? 'active' : '' }}">
                    <i class="fas fa-ticket-alt"></i> Activities
                </a>
                <a href="{{ route('client.flights-hotels') }}" class="nav-link {{ request()->routeIs('client.flights-hotels') ? 'active' : '' }}">
                    <i class="fas fa-plane"></i> Flights & Hotels
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Referrals</div>
                <a href="{{ route('admin.referrals.dashboard') }}" class="nav-link {{ request()->routeIs('admin.referrals.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Referral Agents
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Settings</div>
                <a href="{{ route('client.branding') }}" class="nav-link {{ request()->routeIs('client.branding') ? 'active' : '' }}">
                    <i class="fas fa-palette"></i> Branding
                </a>
                <a href="{{ route('client.settings') }}" class="nav-link {{ request()->routeIs('client.settings') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Account</div>
                <a href="{{ url('/admin') }}" class="nav-link">
                    <i class="fas fa-arrow-left"></i> Legacy Admin
                </a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        @if($company && $company->isOnTrial())
        <div class="subscription-banner">
            <span>
                <i class="fas fa-clock me-2"></i>
                <strong>Trial Period:</strong> {{ $company->trial_ends_at->diffForHumans() }} remaining
            </span>
            <a href="#" class="btn btn-dark btn-sm">Upgrade Now</a>
        </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
