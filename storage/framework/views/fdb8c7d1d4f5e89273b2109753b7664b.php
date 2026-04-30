<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Super Admin'); ?> - GoTrips SaaS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --gold: #FFD700;
            --gold-dark: #D4A72C;
            --gold-light: #FFF3CD;
            --dark-1: #0a0a0a;
            --dark-2: #111111;
            --dark-3: #1a1a1a;
            --dark-4: #222222;
            --border: rgba(255,255,255,0.1);
            --text-white: #FFFFFF;
            --text-light: #E5E7EB;
            --text-muted: #888888;
            --success: #22C55E;
            --warning: #F59E0B;
            --danger: #EF4444;
            --info: #3B82F6;
        }

        * {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            box-sizing: border-box;
        }

        body {
            background: var(--dark-1);
            color: var(--text-light);
            min-height: 100vh;
            margin: 0;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background: var(--dark-2);
            border-right: 1px solid var(--border);
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--border);
        }

        .sidebar-brand {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--text-white);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-brand i {
            color: var(--gold);
            font-size: 1.2rem;
        }

        .sidebar-nav {
            padding: 16px 12px;
            flex: 1;
            overflow-y: auto;
        }

        .nav-section {
            margin-bottom: 20px;
        }

        .nav-section-title {
            font-size: 0.65rem;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 0 12px 8px;
            letter-spacing: 1px;
            font-weight: 700;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 10px 14px;
            color: var(--text-muted);
            border-radius: 10px;
            margin-bottom: 4px;
            transition: all 0.2s ease;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
        }

        .nav-link:hover {
            background: var(--dark-3);
            color: var(--text-white);
        }

        .nav-link.active {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: var(--dark-1);
            font-weight: 600;
            box-shadow: 0 4px 20px rgba(246, 195, 67, 0.25);
        }

        .nav-link i {
            width: 24px;
            margin-right: 14px;
            font-size: 1.1rem;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 24px;
            min-height: 100vh;
        }

        /* Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .page-title {
            font-size: 1.85rem;
            font-weight: 800;
            margin: 0;
            color: var(--text-white);
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .page-title i {
            color: var(--gold);
        }

        /* Cards */
        .card {
            background: var(--dark-2);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }

        .card-header {
            background: var(--dark-3) !important;
            border-bottom: 1px solid var(--border) !important;
            padding: 14px 18px !important;
            font-weight: 700 !important;
            font-size: 0.85rem !important;
            color: var(--text-white) !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }

        .card-header i {
            color: var(--gold) !important;
            font-size: 0.9rem;
        }

        .card-body {
            padding: 18px;
            color: var(--text-light);
        }

        .card-footer {
            background: var(--dark-3);
            border-top: 1px solid var(--border);
            padding: 12px 18px;
        }

        /* Stats Cards */
        .stat-card {
            background: var(--dark-2);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 100%;
            background: var(--gold);
        }

        .stat-card:hover {
            border-color: var(--gold);
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            margin-bottom: 12px;
            background: rgba(246, 195, 67, 0.1);
            color: var(--gold);
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 4px;
            color: var(--text-white);
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Table */
        .table-responsive {
            border-radius: 0 0 16px 16px;
            overflow: hidden;
        }

        .table {
            color: var(--text-light);
            margin: 0;
        }

        .table thead th {
            background: var(--dark-3);
            color: var(--text-muted) !important;
            font-size: 0.7rem;
            text-transform: uppercase;
            font-weight: 700;
            padding: 12px 16px;
            border: none;
            letter-spacing: 0.5px;
        }

        .table tbody {
            background: var(--dark-2);
        }

        .table tbody td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            color: var(--text-light);
            font-size: 0.85rem;
            background: transparent;
        }

        .table tbody tr {
            transition: all 0.2s ease;
            background: var(--dark-2);
        }

        .table tbody tr:hover {
            background: var(--dark-3);
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .table-responsive {
            background: var(--dark-2);
        }

        /* Text helpers */
        .text-muted { color: var(--text-muted) !important; }
        .text-white { color: var(--text-white) !important; }
        .text-gold { color: var(--gold) !important; }
        .text-success { color: var(--success) !important; }
        .text-warning { color: var(--warning) !important; }
        .text-danger { color: var(--danger) !important; }
        .text-info { color: var(--info) !important; }

        .fw-500 { font-weight: 500; }
        .fw-600 { font-weight: 600; }
        .fw-700 { font-weight: 700; }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            border: none;
            color: var(--dark-1);
            font-weight: 700;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--gold-dark) 0%, #B8941F 100%);
            color: var(--dark-1);
            box-shadow: 0 4px 15px rgba(246, 195, 67, 0.3);
        }

        .btn-outline-secondary {
            border: 1px solid var(--border);
            color: var(--text-light);
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.85rem;
        }

        .btn-outline-secondary:hover {
            background: var(--dark-3);
            border-color: var(--text-muted);
            color: var(--text-white);
        }

        .btn-outline-info { border-color: var(--info); color: var(--info); }
        .btn-outline-info:hover { background: var(--info); color: #fff; }

        .btn-outline-warning { border-color: var(--gold); color: var(--gold); }
        .btn-outline-warning:hover { background: var(--gold); color: var(--dark-1); }

        .btn-outline-danger { border-color: var(--danger); color: var(--danger); }
        .btn-outline-danger:hover { background: var(--danger); color: #fff; }

        .btn-outline-success { border-color: var(--success); color: var(--success); }
        .btn-outline-success:hover { background: var(--success); color: #fff; }

        .btn-outline-primary { border-color: var(--gold); color: var(--gold); }
        .btn-outline-primary:hover { background: var(--gold); color: var(--dark-1); }

        .btn-sm { font-size: 0.85rem; padding: 10px 18px; border-radius: 10px; }
        .btn-xs { font-size: 0.75rem; padding: 8px 12px; border-radius: 8px; }

        .btn-group { gap: 6px; }
        .btn-group .btn { border-radius: 8px !important; }

        /* Badge */
        .badge {
            font-size: 0.75rem;
            padding: 8px 14px;
            border-radius: 8px;
            font-weight: 700;
        }

        .badge.bg-primary { background: var(--gold) !important; color: var(--dark-1) !important; }
        .badge.bg-success { background: rgba(34, 197, 94, 0.15) !important; color: var(--success) !important; border: 1px solid var(--success); }
        .badge.bg-warning { background: rgba(245, 158, 11, 0.15) !important; color: var(--warning) !important; border: 1px solid var(--warning); }
        .badge.bg-danger { background: rgba(239, 68, 68, 0.15) !important; color: var(--danger) !important; border: 1px solid var(--danger); }
        .badge.bg-info { background: rgba(59, 130, 246, 0.15) !important; color: var(--info) !important; border: 1px solid var(--info); }

        /* Form */
        .form-control, .form-select {
            background: var(--dark-3);
            border: 1px solid var(--border);
            color: var(--text-white);
            border-radius: 8px;
            padding: 10px 14px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .form-control:focus, .form-select:focus {
            background: var(--dark-4);
            border-color: var(--gold);
            color: var(--text-white);
            box-shadow: 0 0 0 4px rgba(246, 195, 67, 0.15);
        }

        .form-select option {
            background: var(--dark-2);
            color: var(--text-white);
        }

        .form-label {
            font-size: 0.85rem;
            color: var(--text-light);
            margin-bottom: 10px;
            font-weight: 600;
        }

        .input-group-text {
            background: var(--dark-4);
            border: 2px solid var(--border);
            border-left: none;
            color: var(--text-muted);
            font-weight: 600;
        }

        /* Company Avatar */
        .company-avatar {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.9rem;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: var(--dark-1);
        }

        /* Impersonation banner */
        .impersonation-banner {
            background: linear-gradient(135deg, var(--warning) 0%, #D97706 100%);
            color: var(--dark-1);
            padding: 14px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            font-weight: 600;
        }

        /* Alert */
        .alert {
            border: none;
            border-radius: 14px;
            padding: 18px 24px;
            font-weight: 500;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            color: var(--success);
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        /* Links */
        a { color: var(--info); text-decoration: none; }
        a:hover { color: var(--gold); }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: var(--dark-1); }
        ::-webkit-scrollbar-thumb { background: var(--dark-4); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-muted); }

        /* Pagination */
        .pagination { margin: 0; gap: 6px; }
        .page-link {
            background: var(--dark-3);
            border: 1px solid var(--border);
            color: var(--text-light);
            border-radius: 8px;
            padding: 10px 16px;
            font-weight: 600;
        }
        .page-link:hover {
            background: var(--gold);
            border-color: var(--gold);
            color: var(--dark-1);
        }
        .page-item.active .page-link {
            background: var(--gold);
            border-color: var(--gold);
            color: var(--dark-1);
        }
        .page-item.disabled .page-link {
            background: var(--dark-2);
            border-color: var(--border);
            color: var(--text-muted);
        }

        /* Empty State */
        .empty-state {
            padding: 60px 20px;
            text-align: center;
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--dark-4);
            margin-bottom: 24px;
        }

        .empty-state h5 {
            color: var(--text-light);
            margin-bottom: 12px;
        }

        .empty-state p {
            color: var(--text-muted);
            margin-bottom: 24px;
        }
    </style>
</head>
<body>
    <?php if(session('impersonating_from')): ?>
    <div class="impersonation-banner">
        <span><i class="fas fa-user-secret me-2"></i>You are impersonating: <strong><?php echo e(app('current_company')->name ?? 'Unknown'); ?></strong></span>
        <a href="<?php echo e(route('superadmin.stop-impersonation')); ?>" class="btn btn-sm btn-dark">
            <i class="fas fa-sign-out-alt me-1"></i>Return to Super Admin
        </a>
    </div>
    <?php endif; ?>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="<?php echo e(route('superadmin.dashboard')); ?>" class="sidebar-brand">
                <i class="fas fa-shield-alt"></i>
                <span>GoTrips Admin</span>
            </a>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Overview</div>
                <a href="<?php echo e(route('superadmin.dashboard')); ?>" class="nav-link <?php echo e(request()->routeIs('superadmin.dashboard') ? 'active' : ''); ?>">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Management</div>
                <a href="<?php echo e(route('superadmin.companies.index')); ?>" class="nav-link <?php echo e(request()->routeIs('superadmin.companies.*') ? 'active' : ''); ?>">
                    <i class="fas fa-building"></i> Companies
                </a>
                <a href="<?php echo e(route('superadmin.users.index')); ?>" class="nav-link <?php echo e(request()->routeIs('superadmin.users.*') ? 'active' : ''); ?>">
                    <i class="fas fa-users"></i> All Users
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Analytics</div>
                <a href="<?php echo e(route('superadmin.reports.index')); ?>" class="nav-link <?php echo e(request()->routeIs('superadmin.reports.*') ? 'active' : ''); ?>">
                    <i class="fas fa-chart-line"></i> Reports
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">System</div>
                <a href="<?php echo e(route('superadmin.settings.index')); ?>" class="nav-link <?php echo e(request()->routeIs('superadmin.settings.*') ? 'active' : ''); ?>">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <a href="<?php echo e(url('/admin')); ?>" class="nav-link">
                    <i class="fas fa-arrow-left"></i> Back to Admin
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/layouts/superadmin.blade.php ENDPATH**/ ?>