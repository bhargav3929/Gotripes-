<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin') - GoTrips SaaS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sa-primary: #6366f1;
            --sa-primary-dark: #4f46e5;
            --sa-success: #22c55e;
            --sa-warning: #f59e0b;
            --sa-danger: #ef4444;
            --sa-info: #3b82f6;
            --sa-dark: #0f172a;
            --sa-sidebar: #1e293b;
            --sa-card: #1e293b;
            --sa-border: #334155;
            --sa-text: #e2e8f0;
            --sa-muted: #94a3b8;
        }

        * { font-family: 'Inter', sans-serif; }

        body {
            background: var(--sa-dark);
            color: var(--sa-text);
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100vh;
            background: var(--sa-sidebar);
            border-right: 1px solid var(--sa-border);
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--sa-border);
        }

        .sidebar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--sa-primary);
            text-decoration: none;
        }

        .sidebar-nav {
            padding: 15px;
        }

        .nav-section {
            margin-bottom: 20px;
        }

        .nav-section-title {
            font-size: 0.65rem;
            text-transform: uppercase;
            color: var(--sa-muted);
            padding: 10px 12px 5px;
            letter-spacing: 0.5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            color: var(--sa-text);
            border-radius: 8px;
            margin-bottom: 4px;
            transition: all 0.2s;
            text-decoration: none;
        }

        .nav-link:hover, .nav-link.active {
            background: var(--sa-primary);
            color: #fff;
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

        /* Header */
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
            background: var(--sa-card);
            border: 1px solid var(--sa-border);
            border-radius: 12px;
        }

        .card-header {
            background: rgba(99, 102, 241, 0.15);
            border-bottom: 1px solid var(--sa-border);
            padding: 15px 20px;
            font-weight: 600;
            color: #ffffff;
        }

        .card-body { padding: 20px; }

        /* Stats */
        .stat-card {
            background: var(--sa-card);
            border: 1px solid var(--sa-border);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s;
        }

        .stat-card:hover {
            border-color: var(--sa-primary);
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
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.85rem;
            color: var(--sa-muted);
        }

        /* Table */
        .table {
            color: var(--sa-text);
            margin: 0;
        }

        .table thead th {
            background: rgba(0,0,0,0.2);
            color: var(--sa-muted);
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 500;
            padding: 12px 15px;
            border-bottom: 1px solid var(--sa-border);
        }

        .table tbody td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--sa-border);
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: rgba(99, 102, 241, 0.05);
        }

        /* Buttons */
        .btn-primary {
            background: var(--sa-primary);
            border-color: var(--sa-primary);
        }

        .btn-primary:hover {
            background: var(--sa-primary-dark);
            border-color: var(--sa-primary-dark);
        }

        .btn-sm { font-size: 0.8rem; padding: 6px 12px; }
        .btn-xs { font-size: 0.7rem; padding: 4px 8px; }

        /* Badge */
        .badge { font-size: 0.7rem; padding: 5px 10px; }

        /* Form */
        .form-control, .form-select {
            background: var(--sa-dark);
            border: 1px solid var(--sa-border);
            color: var(--sa-text);
        }

        .form-control:focus, .form-select:focus {
            background: var(--sa-dark);
            border-color: var(--sa-primary);
            color: var(--sa-text);
            box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
        }

        .form-label {
            font-size: 0.85rem;
            color: var(--sa-muted);
            margin-bottom: 5px;
        }

        /* Impersonation banner */
        .impersonation-banner {
            background: var(--sa-warning);
            color: #000;
            padding: 8px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
        }

        /* Alert */
        .alert { border: none; border-radius: 8px; }
    </style>
</head>
<body>
    @if(session('impersonating_from'))
    <div class="impersonation-banner">
        <span><i class="fas fa-user-secret me-2"></i>You are impersonating: <strong>{{ app('current_company')->name ?? 'Unknown' }}</strong></span>
        <a href="{{ route('superadmin.stop-impersonation') }}" class="btn btn-sm btn-dark">
            <i class="fas fa-sign-out-alt me-1"></i>Return to Super Admin
        </a>
    </div>
    @endif

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('superadmin.dashboard') }}" class="sidebar-brand">
                <i class="fas fa-shield-alt me-2"></i>Super Admin
            </a>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Overview</div>
                <a href="{{ route('superadmin.dashboard') }}" class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i> Dashboard
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Management</div>
                <a href="{{ route('superadmin.companies.index') }}" class="nav-link {{ request()->routeIs('superadmin.companies.*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i> Companies
                </a>
                <a href="{{ route('superadmin.users.index') }}" class="nav-link {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> All Users
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Analytics</div>
                <a href="{{ route('superadmin.reports.index') }}" class="nav-link {{ request()->routeIs('superadmin.reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">System</div>
                <a href="{{ route('superadmin.settings.index') }}" class="nav-link {{ request()->routeIs('superadmin.settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <a href="{{ url('/admin') }}" class="nav-link">
                    <i class="fas fa-arrow-left"></i> Back to Admin
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
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
