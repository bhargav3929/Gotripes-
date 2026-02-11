<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/index_files/logo.png') }}">

    <title>@yield('title', 'Manager Panel') - Go Trips</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        :root {
            --wp-sidebar: #0d0d0d;
            --wp-sidebar-hover: #2d2d2d;
            --wp-sidebar-active: #FFD700;
            --wp-sidebar-text: #c3c4c7;
            --wp-sidebar-text-hover: #FFD700;
            --wp-body-bg: #1a1a1a;
            --wp-white: #2d2d2d;
            --wp-border: rgba(255, 215, 0, 0.25);
            --wp-border-light: rgba(255, 215, 0, 0.12);
            --wp-primary: #FFD700;
            --wp-primary-hover: #FFA500;
            --wp-text: #f0f0f0;
            --wp-text-secondary: #d6d8db;
            --wp-text-muted: #999;
            --wp-success: #00a32a;
            --wp-danger: #d63638;
            --wp-warning: #dba617;
            --wp-info: #72aee6;
            --sidebar-w: 220px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--wp-body-bg);
            color: var(--wp-text);
            font-size: 13px;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* ── Sidebar ────────────────────────────── */
        .wp-sidebar {
            position: fixed; top: 0; left: -100%;
            width: var(--sidebar-w); height: 100vh;
            background: var(--wp-sidebar);
            border-right: 2px solid var(--wp-primary);
            z-index: 1060; overflow-y: auto;
            transition: left 0.25s ease;
        }
        .wp-sidebar.show { left: 0; }

        .sidebar-overlay {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.5); z-index: 1055;
            opacity: 0; visibility: hidden; transition: all 0.25s ease;
        }
        .sidebar-overlay.show { opacity: 1; visibility: visible; }

        .wp-sidebar::-webkit-scrollbar { width: 0; }

        /* Brand */
        .sidebar-brand {
            display: flex; align-items: center; gap: 10px;
            padding: 16px 14px;
            text-decoration: none;
            border-bottom: 1px solid rgba(255,215,0,0.15);
        }
        .sidebar-brand:hover { text-decoration: none; }
        .sidebar-brand-logo {
            width: 32px; height: 32px; border-radius: 6px;
            object-fit: cover; background: #fff;
        }
        .sidebar-brand-text {
            font-size: 14px; font-weight: 600; color: #fff;
            letter-spacing: -0.2px;
        }

        /* Nav */
        .wp-nav { list-style: none; padding: 8px 0; margin: 0; }
        .wp-nav-separator {
            height: 1px; background: rgba(255,215,0,0.15);
            margin: 6px 12px;
        }
        .wp-nav-label {
            padding: 10px 14px 4px;
            font-size: 11px; font-weight: 600; text-transform: uppercase;
            color: rgba(255,255,255,0.35); letter-spacing: 0.5px;
        }
        .wp-nav-item a,
        .wp-nav-item button {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 14px;
            color: var(--wp-sidebar-text);
            text-decoration: none;
            font-size: 13px; font-weight: 400;
            transition: all 0.15s ease;
            border: none; background: none; width: 100%;
            text-align: left; cursor: pointer;
        }
        .wp-nav-item a:hover,
        .wp-nav-item button:hover {
            background: var(--wp-sidebar-hover);
            color: var(--wp-sidebar-text-hover);
        }
        .wp-nav-item a.active {
            background: var(--wp-sidebar-active);
            color: #1a1a1a; font-weight: 500;
        }
        .wp-nav-item i {
            width: 20px; text-align: center;
            font-size: 14px; opacity: 0.85; flex-shrink: 0;
        }
        .wp-nav-item a.active i { opacity: 1; }

        /* ── Top Bar ────────────────────────────── */
        .wp-topbar {
            position: sticky; top: 0; z-index: 1040;
            background: #111111;
            border-bottom: 2px solid var(--wp-primary);
            padding: 0 20px;
            height: 48px;
            display: flex; align-items: center;
            gap: 12px;
        }
        .topbar-toggle {
            background: none; border: none; color: #fff;
            font-size: 18px; padding: 6px; cursor: pointer;
            border-radius: 4px;
        }
        .topbar-toggle:hover { background: rgba(255,215,0,0.1); }
        .topbar-title {
            font-size: 14px; font-weight: 600;
            color: #fff; flex-grow: 1;
        }
        .topbar-user {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; color: #d6d8db;
        }
        .topbar-user-avatar {
            width: 28px; height: 28px; border-radius: 50%;
            background: var(--wp-primary); color: #1a1a1a;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 600;
        }

        /* ── Content Area ───────────────────────── */
        #content-wrapper {
            margin-left: 0; width: 100%;
            min-height: 100vh; display: flex; flex-direction: column;
            transition: margin-left 0.25s ease;
        }
        .wp-content { flex: 1; padding: 20px; }

        /* ── WordPress-style Cards ──────────────── */
        .wp-card {
            background: var(--wp-white);
            border: 1px solid var(--wp-border);
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .wp-card-header {
            padding: 12px 16px;
            border-bottom: 1px solid var(--wp-border-light);
            font-size: 14px; font-weight: 600;
            color: var(--wp-text);
            display: flex; align-items: center; gap: 8px;
        }
        .wp-card-body { padding: 16px; }
        .wp-card-footer {
            padding: 12px 16px;
            border-top: 1px solid var(--wp-border-light);
            background: #222;
        }

        /* ── WordPress-style Tables ─────────────── */
        .wp-table {
            width: 100%; border-collapse: collapse;
            font-size: 13px;
        }
        .wp-table thead th {
            background: rgba(255, 215, 0, 0.1);
            border-bottom: 1px solid var(--wp-border);
            padding: 10px 12px;
            font-weight: 600; font-size: 12px;
            color: var(--wp-primary);
            text-align: left;
            text-transform: uppercase; letter-spacing: 0.3px;
        }
        .wp-table tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid var(--wp-border-light);
            color: var(--wp-text);
            vertical-align: middle;
        }
        .wp-table tbody tr:hover { background: rgba(255, 215, 0, 0.06); }
        .wp-table .empty-row td {
            text-align: center; padding: 40px 12px;
            color: var(--wp-text-muted);
        }

        /* ── WordPress-style Buttons ────────────── */
        .wp-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 16px; font-size: 13px; font-weight: 500;
            border-radius: 4px; border: 1px solid;
            cursor: pointer; text-decoration: none;
            transition: all 0.15s ease;
            line-height: 1.6; min-height: 32px;
        }
        .wp-btn-primary {
            background: var(--wp-primary); border-color: var(--wp-primary);
            color: #1a1a1a;
        }
        .wp-btn-primary:hover {
            background: var(--wp-primary-hover); border-color: var(--wp-primary-hover);
            color: #1a1a1a;
        }
        .wp-btn-secondary {
            background: #333; border-color: var(--wp-border);
            color: var(--wp-primary);
        }
        .wp-btn-secondary:hover {
            background: #444; border-color: var(--wp-primary);
            color: var(--wp-primary-hover);
        }
        .wp-btn-danger {
            background: transparent; border-color: var(--wp-danger);
            color: var(--wp-danger);
        }
        .wp-btn-danger:hover {
            background: var(--wp-danger); border-color: var(--wp-danger);
            color: #fff;
        }
        .wp-btn-sm { padding: 3px 10px; font-size: 12px; min-height: 28px; }
        .wp-btn i { font-size: 12px; }

        /* ── WordPress-style Forms ──────────────── */
        .wp-form-group { margin-bottom: 20px; }
        .wp-form-label {
            display: block; margin-bottom: 6px;
            font-size: 13px; font-weight: 600;
            color: var(--wp-text);
        }
        .wp-form-label .required { color: var(--wp-danger); }
        .wp-form-help {
            margin-top: 4px; font-size: 12px;
            color: var(--wp-text-muted); line-height: 1.5;
        }
        .wp-input,
        .wp-select,
        .wp-textarea {
            width: 100%; padding: 6px 10px;
            border: 1px solid var(--wp-border);
            border-radius: 4px; font-size: 14px;
            color: var(--wp-text);
            background: var(--wp-white);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            font-family: inherit;
        }
        .wp-input:focus,
        .wp-select:focus,
        .wp-textarea:focus {
            outline: none;
            border-color: var(--wp-primary);
            box-shadow: 0 0 0 1px var(--wp-primary);
        }
        .wp-input { height: 36px; }
        .wp-select { height: 36px; }
        .wp-textarea { min-height: 100px; resize: vertical; }

        /* ── Alerts ─────────────────────────────── */
        .wp-notice {
            padding: 10px 14px;
            border-left: 4px solid;
            background: var(--wp-white);
            margin-bottom: 16px;
            font-size: 13px;
            display: flex; align-items: center; justify-content: space-between;
            border-radius: 0 4px 4px 0;
            box-shadow: 0 1px 1px rgba(0,0,0,0.04);
        }
        .wp-notice-success { border-left-color: var(--wp-success); color: #5cbf70; }
        .wp-notice-error { border-left-color: var(--wp-danger); color: #f56565; }
        .wp-notice-info { border-left-color: var(--wp-info); color: var(--wp-text); }
        .wp-notice .btn-close { font-size: 10px; opacity: 0.5; }

        /* ── Badges ─────────────────────────────── */
        .wp-badge {
            display: inline-flex; align-items: center;
            padding: 2px 8px; border-radius: 3px;
            font-size: 11px; font-weight: 600;
            line-height: 1.5;
        }
        .wp-badge-blue { background: rgba(114, 174, 230, 0.15); color: #72aee6; }
        .wp-badge-green { background: rgba(0, 163, 42, 0.15); color: #5cbf70; }
        .wp-badge-amber { background: rgba(219, 166, 23, 0.15); color: #fbd835; }
        .wp-badge-red { background: rgba(214, 54, 56, 0.15); color: #f56565; }

        /* ── Pagination ─────────────────────────── */
        .wp-pagination {
            display: flex; align-items: center; gap: 4px;
            padding: 12px 16px;
        }
        .wp-pagination .page-link {
            padding: 4px 10px; font-size: 13px;
            border: 1px solid var(--wp-border); border-radius: 3px;
            color: var(--wp-primary); background: var(--wp-white);
            text-decoration: none;
        }
        .wp-pagination .page-link:hover { background: var(--wp-body-bg); }
        .wp-pagination .page-item.active .page-link {
            background: var(--wp-primary); border-color: var(--wp-primary);
            color: #1a1a1a;
        }

        /* ── Footer ─────────────────────────────── */
        .wp-footer {
            padding: 16px 20px; margin-top: auto;
            text-align: center; font-size: 12px;
            color: var(--wp-text-muted);
        }

        /* ── Page Header ────────────────────────── */
        .wp-page-header {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 12px;
            margin-bottom: 20px;
        }
        .wp-page-title {
            font-size: 22px; font-weight: 400;
            color: var(--wp-text); line-height: 1.3;
        }

        /* ── Desktop ────────────────────────────── */
        @media (min-width: 992px) {
            .wp-sidebar { left: 0; }
            .sidebar-overlay { display: none; }
            #content-wrapper { margin-left: var(--sidebar-w); width: calc(100% - var(--sidebar-w)); }
            .topbar-toggle { display: none; }
        }

        @media (max-width: 991.98px) {
            .wp-content { padding: 16px; }
        }

        @media (max-width: 575.98px) {
            .wp-content { padding: 12px; }
            .wp-page-title { font-size: 18px; }
            .wp-page-header { gap: 8px; }
        }

        /* ── Utilities ──────────────────────────── */
        .text-muted-wp { color: var(--wp-text-muted); }
        .text-secondary-wp { color: var(--wp-text-secondary); }
    </style>
    @stack('styles')
</head>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div style="display: flex; min-height: 100vh;">
        <!-- Sidebar -->
        <nav class="wp-sidebar" id="managerSidebar">
            <a class="sidebar-brand" href="{{ route('manager.dashboard') }}">
                <img src="{{ asset('assets/index_files/logo.png') }}" alt="Logo" class="sidebar-brand-logo">
                <span class="sidebar-brand-text">Go Trips</span>
            </a>

            <ul class="wp-nav">
                <li class="wp-nav-item">
                    <a href="{{ route('manager.dashboard') }}" class="{{ request()->routeIs('manager.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-th-large"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <div class="wp-nav-separator"></div>
                <li class="wp-nav-label">Content</li>

                <li class="wp-nav-item">
                    <a href="{{ route('manager.adslots.index') }}" class="{{ request()->routeIs('manager.adslots.*') ? 'active' : '' }}">
                        <i class="fas fa-photo-video"></i>
                        <span>Hero Ad Slots</span>
                    </a>
                </li>
                <li class="wp-nav-item">
                    <a href="{{ route('manager.announcements.index') }}" class="{{ request()->routeIs('manager.announcements.*') ? 'active' : '' }}">
                        <i class="fas fa-rss"></i>
                        <span>News Ticker</span>
                    </a>
                </li>

                <div class="wp-nav-separator"></div>

                <li class="wp-nav-item">
                    <form action="{{ route('manager.logout') }}" method="POST" style="margin:0;">
                        @csrf
                        <button type="submit">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Log Out</span>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>

        <!-- Content Wrapper -->
        <div id="content-wrapper">
            <!-- Top Bar -->
            <header class="wp-topbar">
                <button class="topbar-toggle d-lg-none" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
                <div class="topbar-user">
                    <span class="d-none d-sm-inline">{{ session('manager_name', 'Manager') }}</span>
                    <div class="topbar-user-avatar">
                        {{ strtoupper(substr(session('manager_name', 'M'), 0, 1)) }}
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="wp-content">
                @if(session('success'))
                    <div class="wp-notice wp-notice-success" id="alert-message">
                        <span><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</span>
                        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="wp-notice wp-notice-error">
                        <span>
                            <i class="fas fa-exclamation-circle me-2"></i>
                            @foreach($errors->all() as $error)
                                {{ $error }}@if(!$loop->last)<br>@endif
                            @endforeach
                        </span>
                        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="wp-footer">
                Go Trips Manager Panel
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(function() {
            const sidebar = $('#managerSidebar');
            const overlay = $('#sidebarOverlay');

            $('#sidebarToggle').click(function(e) {
                e.preventDefault();
                sidebar.toggleClass('show');
                overlay.toggleClass('show');
            });

            overlay.click(function() {
                sidebar.removeClass('show');
                overlay.removeClass('show');
            });

            setTimeout(function() { $('#alert-message').fadeOut('slow'); }, 4000);
        });
    </script>
    @stack('scripts')
</body>
</html>
