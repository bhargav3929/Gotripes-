<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="icon" type="image/png" href="<?php echo e(asset('assets/index_files/logo.png')); ?>">

    <title><?php echo $__env->yieldContent('title', 'Referral Dashboard'); ?> - GoTrips</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-gold: #FFD700;
            --secondary-gold: #FFA500;
            --dark-bg: #0a0a0b;
            --darker-bg: #060608;
            --light-dark: #131316;
            --card-bg: #16161a;
            --border-color: rgba(255, 255, 255, 0.06);
            --border-gold: rgba(255, 215, 0, 0.15);
            --text-main: #e2e8f0;
            --text-muted: #8a8f98;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --success: #22c55e;
            --danger: #ef4444;
            --info: #3b82f6;
            --warning: #f59e0b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--dark-bg);
            color: var(--text-main);
            min-height: 100vh;
        }

        /* Navbar */
        .navbar-custom {
            background: var(--darker-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-gold) !important;
        }

        .navbar-brand span {
            color: var(--text-main);
        }

        .nav-link-custom {
            color: var(--text-muted) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: var(--radius-sm);
            transition: all 0.2s ease;
        }

        .nav-link-custom:hover,
        .nav-link-custom.active {
            color: var(--primary-gold) !important;
            background: rgba(255, 215, 0, 0.1);
        }

        .user-dropdown .dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-main);
            text-decoration: none;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #000;
        }

        .dropdown-menu-dark {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
        }

        .dropdown-item {
            color: var(--text-main);
        }

        .dropdown-item:hover {
            background: rgba(255, 215, 0, 0.1);
            color: var(--primary-gold);
        }

        /* Cards */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.25rem;
        }

        /* Buttons */
        .btn-gold {
            background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
            border: none;
            color: #000;
            font-weight: 600;
        }

        .btn-gold:hover {
            background: linear-gradient(135deg, var(--secondary-gold), var(--primary-gold));
            color: #000;
            transform: translateY(-1px);
        }

        .btn-outline-gold {
            border: 1px solid var(--primary-gold);
            color: var(--primary-gold);
            background: transparent;
        }

        .btn-outline-gold:hover {
            background: var(--primary-gold);
            color: #000;
        }

        /* Forms */
        .form-control, .form-select {
            background: var(--light-dark);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 0.625rem 1rem;
        }

        .form-control:focus, .form-select:focus {
            background: var(--light-dark);
            border-color: var(--primary-gold);
            color: var(--text-main);
            box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.15);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        /* Tables */
        .table-dark {
            --bs-table-bg: transparent;
            --bs-table-hover-bg: rgba(255, 255, 255, 0.03);
        }

        .table-dark th {
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border-color);
        }

        .table-dark td {
            color: var(--text-main);
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        /* Stat Card */
        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            border-color: var(--border-gold);
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        /* Alert */
        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.2);
            color: var(--success);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: var(--danger);
        }

        /* Referral Link Box */
        .referral-box {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(255, 165, 0, 0.05));
            border: 1px solid var(--border-gold);
            border-radius: var(--radius-md);
            padding: 1.5rem;
        }

        .referral-url {
            background: var(--darker-bg);
            border-radius: var(--radius-sm);
            padding: 0.75rem 1rem;
            font-family: monospace;
            word-break: break-all;
            color: var(--primary-gold);
        }

        /* Footer */
        .footer {
            background: var(--darker-bg);
            border-top: 1px solid var(--border-color);
            padding: 1.5rem 0;
            margin-top: auto;
        }

        /* Mobile menu toggle */
        .navbar-toggler {
            border-color: var(--border-color);
            padding: 0.5rem;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 215, 0, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: var(--card-bg);
                padding: 1rem;
                border-radius: var(--radius-md);
                margin-top: 1rem;
                border: 1px solid var(--border-color);
            }
        }

        /* Text Visibility Fixes */
        body, p, span, div, li, td, th, label {
            color: #e2e8f0;
        }
        h1, h2, h3, h4, h5, h6 {
            color: #ffffff !important;
        }
        .text-muted {
            color: #a0aec0 !important;
        }
        strong, b {
            color: #ffffff;
        }
        small {
            color: #a0aec0;
        }
        .card-header h5, .card-header h6 {
            color: #ffffff !important;
        }
        .form-label {
            color: #e2e8f0 !important;
        }
        code {
            color: var(--primary-gold);
        }
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="<?php echo e(route('referral.dashboard')); ?>">
                Go<span>Trips</span> <small class="fs-6 text-muted">Partner</small>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom <?php echo e(request()->routeIs('referral.dashboard') ? 'active' : ''); ?>"
                           href="<?php echo e(route('referral.dashboard')); ?>">
                            <i class="fas fa-chart-line me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom <?php echo e(request()->routeIs('referral.orders') ? 'active' : ''); ?>"
                           href="<?php echo e(route('referral.orders')); ?>">
                            <i class="fas fa-receipt me-1"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom <?php echo e(request()->routeIs('referral.earnings') ? 'active' : ''); ?>"
                           href="<?php echo e(route('referral.earnings')); ?>">
                            <i class="fas fa-coins me-1"></i> Earnings
                        </a>
                    </li>
                </ul>

                <div class="dropdown user-dropdown">
                    <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            <?php echo e(strtoupper(substr(Auth::guard('referral_agent')->user()->name, 0, 1))); ?>

                        </div>
                        <span class="d-none d-lg-inline"><?php echo e(Auth::guard('referral_agent')->user()->name); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
                        <li>
                            <a class="dropdown-item" href="<?php echo e(route('referral.profile')); ?>">
                                <i class="fas fa-user me-2"></i>Profile
                            </a>
                        </li>
                        <li><hr class="dropdown-divider border-dark"></li>
                        <li>
                            <form method="POST" action="<?php echo e(route('referral.logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if(session('message')): ?>
    <div class="container mt-3">
        <div class="alert alert-<?php echo e(session('alert-type', 'success')); ?> alert-dismissible fade show" role="alert">
            <?php echo e(session('message')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="py-4">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <small class="text-muted">&copy; <?php echo e(date('Y')); ?> GoTrips. All rights reserved.</small>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <small class="text-muted">Partner Portal v1.0</small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\Pragathi\Desktop\GoTrips-Complete\resources\views/layouts/referral.blade.php ENDPATH**/ ?>