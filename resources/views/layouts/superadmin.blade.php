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
            --sa-primary: #8b5cf6;
            --sa-primary-dark: #7c3aed;
            --sa-primary-light: rgba(139, 92, 246, 0.15);
            --sa-success: #10b981;
            --sa-warning: #f59e0b;
            --sa-danger: #ef4444;
            --sa-info: #06b6d4;
            --sa-dark: #0c0f1a;
            --sa-sidebar: #111827;
            --sa-card: #1f2937;
            --sa-card-hover: #374151;
            --sa-border: #374151;
            --sa-text: #f9fafb;
            --sa-text-secondary: #d1d5db;
            --sa-muted: #9ca3af;
        }

        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--sa-dark) 0%, #1a1f35 100%);
            color: var(--sa-text);
            min-height: 100vh;
            margin: 0;
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
            padding: 24px 20px;
            border-bottom: 1px solid var(--sa-border);
            background: linear-gradient(135deg, var(--sa-primary-light) 0%, transparent 100%);
        }

        .sidebar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--sa-text);
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .sidebar-brand i {
            color: var(--sa-primary);
            margin-right: 10px;
        }

        .sidebar-nav {
            padding: 20px 15px;
        }

        .nav-section {
            margin-bottom: 24px;
        }

        .nav-section-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: var(--sa-muted);
            padding: 8px 12px;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 14px;
            color: var(--sa-text-secondary);
            border-radius: 10px;
            margin-bottom: 4px;
            transition: all 0.2s ease;
            text-decoration: none;
            font-weight: 500;
        }

        .nav-link:hover {
            background: var(--sa-card);
            color: var(--sa-text);
        }

        .nav-link.active {
            background: linear-gradient(135deg, var(--sa-primary) 0%, var(--sa-primary-dark) 100%);
            color: #fff;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }

        .nav-link i {
            width: 22px;
            margin-right: 12px;
            font-size: 1rem;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 30px;
            min-height: 100vh;
        }

        /* Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
            color: var(--sa-text);
        }

        .page-title i {
            color: var(--sa-primary);
            margin-right: 12px;
        }

        /* Cards */
        .card {
            background: var(--sa-card);
            border: 1px solid var(--sa-border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            background: linear-gradient(135deg, var(--sa-primary-light) 0%, rgba(139, 92, 246, 0.05) 100%) !important;
            border-bottom: 1px solid var(--sa-border) !important;
            padding: 18px 24px !important;
            font-weight: 600 !important;
            font-size: 1rem !important;
            color: var(--sa-text) !important;
        }

        .card-header i {
            color: var(--sa-primary) !important;
            margin-right: 10px;
        }

        .card-body {
            padding: 24px;
            color: var(--sa-text);
        }

        .card-footer {
            background: rgba(0, 0, 0, 0.2);
            border-top: 1px solid var(--sa-border);
            padding: 15px 24px;
        }

        /* Stats */
        .stat-card {
            background: var(--sa-card);
            border: 1px solid var(--sa-border);
            border-radius: 16px;
            padding: 24px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            border-color: var(--sa-primary);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.15);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 16px;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 6px;
            color: var(--sa-text);
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--sa-muted);
            font-weight: 500;
        }

        /* Table */
        .table {
            color: var(--sa-text);
            margin: 0;
        }

        .table thead th {
            background: rgba(0, 0, 0, 0.3);
            color: var(--sa-text-secondary) !important;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 600;
            padding: 16px 20px;
            border-bottom: 1px solid var(--sa-border);
            letter-spacing: 0.5px;
        }

        .table tbody td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--sa-border);
            vertical-align: middle;
            color: var(--sa-text);
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background: rgba(139, 92, 246, 0.08);
        }

        .table .text-muted {
            color: var(--sa-muted) !important;
        }

        .table small.text-muted {
            color: var(--sa-muted) !important;
        }

        /* Global text-muted fix */
        .text-muted {
            color: var(--sa-muted) !important;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--sa-primary) 0%, var(--sa-primary-dark) 100%);
            border: none;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--sa-primary-dark) 0%, #6d28d9 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4);
        }

        .btn-outline-secondary {
            border-color: var(--sa-border);
            color: var(--sa-text-secondary);
        }

        .btn-outline-secondary:hover {
            background: var(--sa-card);
            border-color: var(--sa-muted);
            color: var(--sa-text);
        }

        .btn-outline-info {
            border-color: var(--sa-info);
            color: var(--sa-info);
        }

        .btn-outline-info:hover {
            background: var(--sa-info);
            color: #fff;
        }

        .btn-outline-warning {
            border-color: var(--sa-warning);
            color: var(--sa-warning);
        }

        .btn-outline-warning:hover {
            background: var(--sa-warning);
            color: #000;
        }

        .btn-outline-danger {
            border-color: var(--sa-danger);
            color: var(--sa-danger);
        }

        .btn-outline-danger:hover {
            background: var(--sa-danger);
            color: #fff;
        }

        .btn-outline-success {
            border-color: var(--sa-success);
            color: var(--sa-success);
        }

        .btn-outline-success:hover {
            background: var(--sa-success);
            color: #fff;
        }

        .btn-outline-primary {
            border-color: var(--sa-primary);
            color: var(--sa-primary);
        }

        .btn-outline-primary:hover {
            background: var(--sa-primary);
            color: #fff;
        }

        .btn-sm { font-size: 0.85rem; padding: 8px 16px; border-radius: 8px; }
        .btn-xs { font-size: 0.75rem; padding: 6px 10px; border-radius: 6px; }

        .btn-group .btn {
            border-radius: 8px !important;
            margin: 0 2px;
        }

        /* Badge */
        .badge {
            font-size: 0.75rem;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
        }

        .badge.bg-primary { background: var(--sa-primary) !important; }
        .badge.bg-success { background: var(--sa-success) !important; }
        .badge.bg-warning { background: var(--sa-warning) !important; color: #000 !important; }
        .badge.bg-danger { background: var(--sa-danger) !important; }
        .badge.bg-info { background: var(--sa-info) !important; }

        /* Form */
        .form-control, .form-select {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--sa-border);
            color: var(--sa-text);
            border-radius: 10px;
            padding: 12px 16px;
        }

        .form-control::placeholder {
            color: var(--sa-muted);
        }

        .form-control:focus, .form-select:focus {
            background: rgba(0, 0, 0, 0.4);
            border-color: var(--sa-primary);
            color: var(--sa-text);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2);
        }

        .form-select option {
            background: var(--sa-card);
            color: var(--sa-text);
        }

        .form-label {
            font-size: 0.85rem;
            color: var(--sa-text-secondary);
            margin-bottom: 8px;
            font-weight: 500;
        }

        .input-group-text {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--sa-border);
            color: var(--sa-muted);
        }

        /* Impersonation banner */
        .impersonation-banner {
            background: linear-gradient(135deg, var(--sa-warning) 0%, #d97706 100%);
            color: #000;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Alert */
        .alert {
            border: none;
            border-radius: 12px;
            padding: 16px 20px;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            color: var(--sa-success);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            color: var(--sa-danger);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        /* Company Avatar */
        .company-avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            background: linear-gradient(135deg, var(--sa-primary) 0%, var(--sa-primary-dark) 100%);
            color: #fff;
        }

        /* Text colors */
        .text-success { color: var(--sa-success) !important; }
        .text-warning { color: var(--sa-warning) !important; }
        .text-danger { color: var(--sa-danger) !important; }
        .text-info { color: var(--sa-info) !important; }
        .text-primary { color: var(--sa-primary) !important; }

        .fw-500 { font-weight: 500; }
        .fw-600 { font-weight: 600; }

        /* Links */
        a { color: var(--sa-info); }
        a:hover { color: var(--sa-primary); }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: var(--sa-dark); }
        ::-webkit-scrollbar-thumb { background: var(--sa-border); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--sa-muted); }

        /* Pagination */
        .pagination { margin: 0; }
        .page-link {
            background: var(--sa-card);
            border-color: var(--sa-border);
            color: var(--sa-text);
        }
        .page-link:hover {
            background: var(--sa-primary);
            border-color: var(--sa-primary);
            color: #fff;
        }
        .page-item.active .page-link {
            background: var(--sa-primary);
            border-color: var(--sa-primary);
        }
        .page-item.disabled .page-link {
            background: rgba(0,0,0,0.2);
            border-color: var(--sa-border);
            color: var(--sa-muted);
        }
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
                <i class="fas fa-shield-alt"></i>Super Admin
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
