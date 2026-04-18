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
            --client-primary: {{ $primaryColor }};
            --client-secondary: {{ $secondaryColor }};
            --client-dark: #1a1a2e;
            --client-sidebar: #16213e;
            --client-card: #0f3460;
            --client-border: #1f4287;
            --client-text: #e2e8f0;
            --client-muted: #94a3b8;
        }

        * { font-family: 'Inter', sans-serif; }

        body {
            background: var(--client-dark);
            color: var(--client-text);
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100vh;
            background: var(--client-sidebar);
            border-right: 1px solid var(--client-border);
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--client-border);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-logo {
            max-height: 40px;
            max-width: 120px;
        }

        .sidebar-brand {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--client-primary);
        }

        .sidebar-nav { padding: 15px; }

        .nav-section { margin-bottom: 20px; }

        .nav-section-title {
            font-size: 0.65rem;
            text-transform: uppercase;
            color: var(--client-muted);
            padding: 10px 12px 5px;
            letter-spacing: 0.5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            color: var(--client-text);
            border-radius: 8px;
            margin-bottom: 4px;
            transition: all 0.2s;
            text-decoration: none;
        }

        .nav-link:hover, .nav-link.active {
            background: var(--client-primary);
            color: #000;
        }

        .nav-link i {
            width: 20px;
            margin-right: 10px;
            font-size: 0.9rem;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 20px;
            min-height: 100vh;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }

        /* Cards */
        .card {
            background: var(--client-card);
            border: 1px solid var(--client-border);
            border-radius: 12px;
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--client-border);
            padding: 15px 20px;
            font-weight: 600;
        }

        .card-body { padding: 20px; }

        /* Stats */
        .stat-card {
            background: var(--client-card);
            border: 1px solid var(--client-border);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s;
        }

        .stat-card:hover {
            border-color: var(--client-primary);
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 15px;
            background: rgba(255, 215, 0, 0.15);
            color: var(--client-primary);
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.85rem;
            color: var(--client-muted);
        }

        /* Table */
        .table {
            color: var(--client-text);
            margin: 0;
        }

        .table thead th {
            background: rgba(0,0,0,0.2);
            color: var(--client-muted);
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 500;
            padding: 12px 15px;
            border-bottom: 1px solid var(--client-border);
        }

        .table tbody td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--client-border);
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: rgba(255, 215, 0, 0.05);
        }

        /* Buttons */
        .btn-primary {
            background: var(--client-primary);
            border-color: var(--client-primary);
            color: #000;
        }

        .btn-primary:hover {
            background: var(--client-secondary);
            border-color: var(--client-secondary);
            color: #000;
        }

        .btn-sm { font-size: 0.8rem; padding: 6px 12px; }

        /* Badge */
        .badge { font-size: 0.7rem; padding: 5px 10px; }

        /* Form */
        .form-control, .form-select {
            background: var(--client-dark);
            border: 1px solid var(--client-border);
            color: var(--client-text);
        }

        .form-control:focus, .form-select:focus {
            background: var(--client-dark);
            border-color: var(--client-primary);
            color: var(--client-text);
            box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.25);
        }

        .form-label {
            font-size: 0.85rem;
            color: var(--client-muted);
            margin-bottom: 5px;
        }

        /* Subscription Banner */
        .subscription-banner {
            background: linear-gradient(135deg, var(--client-primary) 0%, var(--client-secondary) 100%);
            color: #000;
            padding: 10px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .alert { border: none; border-radius: 8px; }
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
                <div class="nav-section-title">Business</div>
                <a href="{{ route('client.orders') }}" class="nav-link {{ request()->routeIs('client.orders*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i> Orders
                </a>
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
