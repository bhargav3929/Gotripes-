<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" href="{{ asset('assets/index_files/logo.png') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- jQuery UI -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <!-- Complete Mobile-First Black & Gold Responsive Layout with Improved Contrast -->
    <style>
        :root {
            --primary-gold: #FFD700;
            --secondary-gold: #FFA500;
            --dark-bg: #1a1a1a;
            --darker-bg: #0d0d0d;
            --light-dark: #2d2d2d;
            --text-gold: #FFD700;
            --text-white: #ffffff;
            --text-light: #f8f9fa;
            --text-muted: #d6d8db;
            --shadow-gold: rgba(255, 215, 0, 0.3);
            --gradient-primary: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            --gradient-dark: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 50%, #1a1a1a 100%);
            --sidebar-width: 280px;
            --sidebar-width-mobile: 260px;
            --topbar-height: 60px;
        }

        /* MOBILE-FIRST RESET AND BASE STYLES */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark-bg);
            color: var(--text-light);
            overflow-x: hidden;
            font-size: 14px;
            line-height: 1.5;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: transparent;
        }

        /* MOBILE-FIRST WRAPPER */
        #wrapper {
            display: flex;
            min-height: 100vh;
            background: var(--dark-bg);
            position: relative;
        }

        /* MOBILE-FIRST SIDEBAR */
        .sidebar {
            position: fixed;
            top: 0;
            left: -100%;
            width: var(--sidebar-width-mobile);
            height: 100vh;
            background: var(--gradient-dark);
            border-right: 3px solid var(--primary-gold);
            box-shadow: 2px 0 15px rgba(255, 215, 0, 0.2);
            z-index: 1060;
            overflow-y: auto;
            overflow-x: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            -webkit-overflow-scrolling: touch;
        }

        .sidebar.show {
            left: 0;
            box-shadow: 2px 0 25px rgba(0, 0, 0, 0.5);
        }

        /* Mobile Sidebar Overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1055;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        /* Sidebar Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: var(--darker-bg);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--primary-gold);
            border-radius: 2px;
        }

        /* Mobile-First Sidebar Brand */
        .sidebar-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem 0.75rem;
            margin: 0.75rem;
            background: var(--gradient-primary);
            color: var(--darker-bg) !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            min-height: 48px;
        }

        .sidebar-brand:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.4);
            color: var(--darker-bg) !important;
            text-decoration: none;
        }

        /* Mobile-First Navigation */
        .sidebar .nav-item {
            margin-bottom: 0.25rem;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            padding: 0.875rem 1rem;
            color: var(--text-white) !important;
            text-decoration: none;
            border-radius: 6px;
            margin: 0 0.75rem;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.875rem;
            min-height: 44px;
            position: relative;
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 215, 0, 0.15) !important;
            color: var(--text-white) !important;
            box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
            transform: translateX(2px);
        }

        .sidebar .nav-link.active,
        .sidebar .nav-item.active .nav-link {
            background: var(--gradient-primary) !important;
            color: var(--darker-bg) !important;
            font-weight: 600;
            box-shadow: 0 3px 12px rgba(255, 215, 0, 0.4);
        }

        .sidebar .nav-link i {
            width: 18px;
            text-align: center;
            margin-right: 0.75rem;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .sidebar-divider {
            border-color: rgba(255, 215, 0, 0.3) !important;
            margin: 0.75rem 1.5rem;
        }

        /* MOBILE-FIRST CONTENT WRAPPER */
        #content-wrapper {
            margin-left: 0;
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            transition: margin-left 0.3s ease;
        }

        /* MOBILE-FIRST TOPBAR */
        .topbar {
            background: var(--gradient-dark) !important;
            border-bottom: 2px solid var(--primary-gold);
            box-shadow: 0 2px 15px rgba(255, 215, 0, 0.2);
            padding: 0.75rem 1rem;
            position: sticky;
            top: 0;
            z-index: 1040;
            height: var(--topbar-height);
            display: flex;
            align-items: center;
        }

        .topbar .btn {
            color: var(--text-white);
            border: none;
            background: transparent;
            padding: 0.5rem;
            border-radius: 50%;
            min-width: 44px;
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .topbar .btn:hover {
            color: var(--primary-gold);
            background: rgba(255, 215, 0, 0.1);
        }

        .topbar .navbar-nav .nav-link {
            color: var(--text-white) !important;
            padding: 0.5rem;
            min-width: 44px;
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .topbar .navbar-nav .nav-link:hover {
            color: var(--primary-gold) !important;
            background: rgba(255, 215, 0, 0.1);
            border-radius: 50%;
        }

        .topbar .h5 {
            color: var(--text-white) !important;
            font-weight: 600;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
            font-size: 1rem;
            margin: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Mobile User Dropdown */
        .topbar .dropdown-menu {
            background: var(--light-dark);
            border: 1px solid var(--primary-gold);
            box-shadow: 0 4px 20px rgba(255, 215, 0, 0.3);
            min-width: 180px;
        }

        .topbar .dropdown-item {
            color: var(--text-white);
            transition: all 0.3s ease;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
        }

        .topbar .dropdown-item:hover {
            background: var(--gradient-primary);
            color: var(--darker-bg);
        }

        .img-profile {
            border: 2px solid var(--primary-gold);
            box-shadow: 0 2px 8px rgba(255, 215, 0, 0.4);
            width: 32px;
            height: 32px;
        }

        /* MOBILE-FIRST MAIN CONTENT */
        #content {
            flex: 1;
            padding: 0;
            margin-top: 0;
        }

        .container-fluid {
            padding: 1rem;
        }

        /* MOBILE-FIRST FOOTER */
        .sticky-footer {
            background: var(--gradient-dark) !important;
            border-top: 2px solid var(--primary-gold);
            color: var(--text-white);
            padding: 1rem 0;
            margin-top: auto;
        }

        .sticky-footer .text-primary {
            color: var(--primary-gold) !important;
            font-weight: 600;
            font-size: 0.875rem;
        }

        /* MOBILE-FIRST COMPONENTS */
        .card {
            background: var(--light-dark);
            border: 1px solid rgba(255, 215, 0, 0.3);
            box-shadow: 0 4px 20px rgba(255, 215, 0, 0.1);
            color: var(--text-white);
            margin-bottom: 1rem;
        }

        .card-header {
            background: var(--gradient-primary);
            color: var(--darker-bg);
            font-weight: 600;
            border-bottom: 1px solid var(--primary-gold);
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }

        .card-body {
            padding: 1rem;
            color: var(--text-white);
        }

        .card-footer {
            padding: 0.75rem 1rem;
            color: var(--text-white);
        }

        /* Mobile-First Buttons */
        .btn {
            min-height: 44px;
            padding: 0.6rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--gradient-primary);
            border: 1px solid var(--primary-gold);
            color: var(--darker-bg);
        }

        .btn-primary:hover {
            background: var(--secondary-gold);
            border-color: var(--secondary-gold);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.4);
        }

        .btn-outline-primary {
            border-color: var(--primary-gold);
            color: var(--primary-gold);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--gradient-primary);
            border-color: var(--primary-gold);
            color: var(--darker-bg);
        }

        /* Mobile-First Forms */
        .form-control {
            background: rgba(45, 45, 45, 0.8);
            border: 1px solid rgba(255, 215, 0, 0.3);
            color: var(--text-white);
            font-size: 16px; /* Prevents zoom on iOS */
            min-height: 44px;
            padding: 0.6rem 0.75rem;
        }

        .form-control:focus {
            background: rgba(45, 45, 45, 0.9);
            border-color: var(--primary-gold);
            box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.25);
            color: var(--text-white);
        }

        .form-control::placeholder {
            color: #adb5bd;
            opacity: 1;
        }

        .form-label {
            color: var(--text-white);
            font-weight: 500;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        /* Mobile-First Tables */
        .table {
            color: var(--text-white);
            font-size: 0.875rem;
        }

        .table-responsive {
            border-radius: 8px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-dark {
            background: var(--light-dark);
        }

        .table-dark th {
            background: var(--gradient-primary);
            color: var(--darker-bg);
            font-weight: 600;
            border-color: var(--primary-gold);
            font-size: 0.8rem;
            padding: 0.75rem 0.5rem;
        }

        .table-dark td {
            border-color: rgba(255, 215, 0, 0.2);
            padding: 0.75rem 0.5rem;
            color: var(--text-white);
        }

        .table-hover tbody tr:hover {
            background: rgba(255, 215, 0, 0.1);
        }

        /* Mobile-First Alerts */
        .alert {
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            border-color: rgba(40, 167, 69, 0.5);
            color: #5cbf70;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.2);
            border-color: rgba(220, 53, 69, 0.5);
            color: #f56565;
        }

        .alert-warning {
            background: rgba(255, 193, 7, 0.2);
            border-color: rgba(255, 193, 7, 0.5);
            color: #fbd835;
        }

        .alert-info {
            background: rgba(23, 162, 184, 0.2);
            border-color: rgba(23, 162, 184, 0.5);
            color: #4fd1c7;
        }

        /* Mobile-First Modals */
        .modal-content {
            background: var(--light-dark);
            border: 1px solid var(--primary-gold);
            color: var(--text-white);
            border-radius: 8px;
        }

        .modal-header {
            background: var(--gradient-primary);
            color: var(--darker-bg);
            border-bottom: 1px solid var(--primary-gold);
        }

        .modal-footer {
            border-top: 1px solid rgba(255, 215, 0, 0.3);
        }

        /* Mobile-First DataTables */
        .dataTables_wrapper {
            color: var(--text-white);
            font-size: 0.875rem;
        }

        .dataTables_filter input,
        .dataTables_length select {
            background: rgba(45, 45, 45, 0.8);
            border: 1px solid rgba(255, 215, 0, 0.3);
            color: var(--text-white);
            font-size: 14px;
            min-height: 40px;
        }

        .page-link {
            background: var(--light-dark);
            border-color: rgba(255, 215, 0, 0.3);
            color: var(--primary-gold);
            padding: 0.5rem 0.75rem;
            min-width: 40px;
            min-height: 40px;
        }

        .page-link:hover {
            background: var(--gradient-primary);
            border-color: var(--primary-gold);
            color: var(--darker-bg);
        }

        .page-item.active .page-link {
            background: var(--gradient-primary);
            border-color: var(--primary-gold);
            color: var(--darker-bg);
        }

        /* Scroll to Top - Mobile Friendly */
        .scroll-to-top {
            position: fixed;
            bottom: 1.5rem;
            right: 1rem;
            width: 48px;
            height: 48px;
            background: var(--gradient-primary);
            border: 1px solid var(--primary-gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--darker-bg);
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.4);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .scroll-to-top:hover {
            background: var(--secondary-gold);
            transform: scale(1.1);
            color: var(--darker-bg);
        }

        /* Animations */
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-scale {
            transition: transform 0.3s ease;
        }

        /* IMPROVED TEXT CONTRAST */
        .text-muted {
            color: var(--text-muted) !important;
        }

        .text-light {
            color: var(--text-white) !important;
        }

        small {
            color: var(--text-muted) !important;
        }

        .badge {
            color: var(--darker-bg) !important;
            font-weight: 600;
        }

        /* Navigation Badge Improvements */
        .nav-badge {
            border-left: 4px solid;
            border-radius: 6px !important;
            margin: 0.3rem 0.5rem;
            padding: 0.75rem 1rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-white) !important;
        }

        .nav-badge-admin {
            background: rgba(40, 167, 69, 0.15) !important;
            border-color: #28a745 !important;
            color: #5cbf70 !important;
        }

        .nav-badge-partner {
            background: rgba(23, 162, 184, 0.15) !important;
            border-color: #17a2b8 !important;
            color: #4fd1c7 !important;
        }

        .nav-badge-approved {
            background: rgba(40, 167, 69, 0.15) !important;
            border-color: #28a745 !important;
            color: #5cbf70 !important;
        }

        .nav-badge-pending {
            background: rgba(255, 193, 7, 0.15) !important;
            border-color: #ffc107 !important;
            color: #fbd835 !important;
        }

        .nav-badge-restricted {
            background: rgba(220, 53, 69, 0.15) !important;
            border-color: #dc3545 !important;
            color: #f56565 !important;
        }

        .nav-badge-info {
            background: rgba(108, 117, 125, 0.1) !important;
            color: var(--text-muted) !important;
            font-size: 0.7rem;
            padding: 0.5rem 1rem;
        }

        /* BREAKPOINT: Small Phones (up to 575px) */
        @media (max-width: 575.98px) {
            :root {
                --sidebar-width-mobile: 240px;
            }

            .container-fluid {
                padding: 0.75rem;
            }

            .topbar {
                padding: 0.5rem;
            }

            .topbar .h5 {
                font-size: 0.9rem;
            }

            .card-header h3 {
                font-size: 1rem;
            }

            .btn {
                font-size: 0.8rem;
                padding: 0.5rem 0.75rem;
            }

            .table-responsive {
                font-size: 0.75rem;
            }

            .modal-dialog {
                margin: 0.5rem;
                max-width: calc(100% - 1rem);
            }

            .sidebar-brand {
                font-size: 0.8rem;
                padding: 0.75rem;
                margin: 0.5rem;
            }

            .sidebar .nav-link {
                padding: 0.75rem 0.75rem;
                font-size: 0.8rem;
                margin: 0 0.5rem;
            }
        }

        /* BREAKPOINT: Large Phones / Small Tablets (576px - 767px) */
        @media (min-width: 576px) and (max-width: 767.98px) {
            .container-fluid {
                padding: 1rem;
            }

            .topbar .h5 {
                font-size: 1rem;
            }
        }

        /* BREAKPOINT: Tablets (768px - 991px) */
        @media (min-width: 768px) {
            body {
                font-size: 15px;
            }

            .sidebar {
                left: -100%;
            }

            .sidebar.show {
                left: 0;
            }

            .container-fluid {
                padding: 1.5rem;
            }

            .topbar .h5 {
                font-size: 1.1rem;
            }

            .card-body {
                padding: 1.5rem;
            }
        }

        /* BREAKPOINT: Desktop (992px - 1199px) */
        @media (min-width: 992px) {
            :root {
                --sidebar-width: 280px;
            }

            body {
                font-size: 16px;
            }

            .sidebar {
                position: fixed;
                left: 0;
                width: var(--sidebar-width);
                transform: translateX(0);
            }

            .sidebar-overlay {
                display: none;
            }

            #content-wrapper {
                margin-left: var(--sidebar-width);
                width: calc(100% - var(--sidebar-width));
            }

            .topbar {
                padding: 0.75rem 1.5rem;
            }

            .container-fluid {
                padding: 2rem 1.5rem;
            }

            .sidebar .nav-link {
                padding: 1rem 1.5rem;
                font-size: 0.9rem;
            }

            .sidebar-brand {
                font-size: 1rem;
                padding: 1.25rem 1rem;
                margin: 1rem;
            }
        }

        /* BREAKPOINT: Large Desktop (1200px+) */
        @media (min-width: 1200px) {
            .container-fluid {
                max-width: 1400px;
                margin: 0 auto;
                padding: 2rem 2rem;
            }

            .topbar .h5 {
                font-size: 1.25rem;
            }

            .sidebar .nav-link {
                font-size: 1rem;
            }

            .sidebar-brand {
                font-size: 1.1rem;
            }
        }

        /* SIDEBAR TOGGLE STATES (Desktop) */
        @media (min-width: 992px) {
            .sidebar.toggled {
                width: 80px;
            }

            .sidebar.toggled .sidebar-brand-text,
            .sidebar.toggled .nav-link span {
                display: none;
            }

            .sidebar.toggled ~ #content-wrapper {
                margin-left: 80px;
                width: calc(100% - 80px);
            }

            .sidebar.toggled .sidebar-brand {
                padding: 1.25rem 0.5rem;
            }

            .sidebar.toggled .nav-link {
                padding: 1rem 0.5rem;
                justify-content: center;
            }

            .sidebar.toggled .nav-link i {
                margin-right: 0;
            }
        }

        /* Touch Device Optimizations */
        @media (hover: none) and (pointer: coarse) {
            .animate-scale:hover {
                transform: none;
            }

            .sidebar .nav-link:hover {
                transform: none;
            }

            .btn:hover {
                transform: none;
            }
        }

        /* High DPI Displays */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .img-profile {
                image-rendering: -webkit-optimize-contrast;
            }
        }

        /* Reduced Motion */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Landscape Mobile */
        @media (max-height: 500px) and (orientation: landscape) and (max-width: 991px) {
            .sidebar {
                width: 220px;
            }

            .topbar {
                padding: 0.5rem;
            }

            .sidebar-brand {
                padding: 0.75rem;
                margin: 0.5rem;
            }

            .sidebar .nav-link {
                padding: 0.6rem 1rem;
            }
        }
    </style>

    @stack('styles')
    @stack('style-alt')
</head>

<body id="page-top">
    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Page Wrapper with Mobile-First Layout -->
    <div id="wrapper">
        
        <!-- SIDEBAR - MOBILE RESPONSIVE -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/') }}">
                <div class="sidebar-brand-text mx-2">
                    <i class="fas fa-home me-2 d-none d-sm-inline"></i>{{ __('Homepage') }}
                </div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Navigation Items -->
            @auth
                @php
                    // Use session values for user type detection
                    $isPartnerRestricted = session('is_partner_restricted', false);
                    $userType = session('user_type', 'unknown');
                    $user = Auth::user();
                @endphp

                @if(!$isPartnerRestricted && $userType === 'admin')
                    {{-- 👑 ADMIN USERS - Show ALL menus --}}
                    
                    <!-- Admin Badge -->
                    <li class="nav-item mb-2">
                        <div class="nav-link nav-badge nav-badge-admin">
                            <i class="fas fa-user-shield me-2"></i>
                            <strong>Admin:</strong> {{ $user->name }}
                        </div>
                    </li>

                    <!-- Admin Users -->
                    <li class="nav-item {{ (request()->is('admin/users') || request()->is('admin/users*')) ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.users.index') }}">
                            <i class="fas fa-desktop"></i>
                            <span>Admin Users</span>
                        </a>
                    </li>

                    <!-- Announcements -->
                    <li class="nav-item">
                        <a href="{{ route('admin.announcements.index') }}" class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                            <i class="fas fa-bullhorn"></i>
                            <span>Announcements</span>
                        </a>
                    </li>

                    <!-- Travel Ads -->
                    <li class="nav-item">
                        <a href="{{ route('admin.homepageads.index') }}" class="nav-link {{ request()->routeIs('admin.homepageads.*') ? 'active' : '' }}">
                            <i class="fas fa-images"></i>
                            <span>Travel Ads</span>
                        </a>
                    </li>

                    <!-- UAE Activities -->
                    <li class="nav-item">
                        <a href="{{ route('admin.uaeactivities.index') }}" class="nav-link {{ request()->routeIs('admin.uaeactivities.*') ? 'active' : '' }}">
                            <i class="fas fa-map-marked-alt"></i>
                            <span>UAE Activities</span>
                        </a>
                    </li>

                @elseif($isPartnerRestricted && $userType === 'approved_partner')
                    {{-- 🤝 APPROVED PARTNERS - Show ONLY UAE Activities --}}
                    
                    <!-- Partner Badge -->
                    <li class="nav-item mb-2">
                        <div class="nav-link nav-badge nav-badge-partner">
                            <i class="fas fa-handshake me-2"></i>
                            <strong>Partner:</strong> {{ $user->name }}
                        </div>
                    </li>

                    <!-- Status Badge -->
                    <li class="nav-item mb-2">
                        <div class="nav-link nav-badge nav-badge-approved">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Status:</strong> ✅ Approved
                        </div>
                    </li>

                    <!-- UAE Activities ONLY -->
                    <li class="nav-item">
                        <a href="{{ route('admin.uaeactivities.index') }}" class="nav-link {{ request()->routeIs('admin.uaeactivities.*') ? 'active' : '' }}">
                            <i class="fas fa-map-marked-alt"></i>
                            <span>UAE Activities</span>
                        </a>
                    </li>

                    <!-- Partner Note -->
                    {{-- <li class="nav-item mt-3">
                        <div class="nav-link nav-badge nav-badge-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>You have access to UAE Activities management only.</small>
                        </div>
                    </li> --}}

                @elseif($isPartnerRestricted && $userType === 'pending_partner')
                    {{-- ⏳ PENDING PARTNERS --}}
                    
                    <!-- Partner Badge -->
                    <li class="nav-item mb-2">
                        <div class="nav-link nav-badge nav-badge-partner">
                            <i class="fas fa-handshake me-2"></i>
                            <strong>Partner:</strong> {{ $user->name }}
                        </div>
                    </li>

                    <!-- Status Badge -->
                    <li class="nav-item mb-2">
                        <div class="nav-link nav-badge nav-badge-pending">
                            <i class="fas fa-clock me-2"></i>
                            <strong>Status:</strong> ⏳ Pending Approval
                        </div>
                    </li>

                    <!-- Pending Note -->
                    <li class="nav-item mt-3">
                        <div class="nav-link nav-badge nav-badge-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>Your partner account is pending approval. Please wait for admin approval.</small>
                        </div>
                    </li>

                @elseif(!$isPartnerRestricted && $userType === 'regular_user')
                    {{-- 👤 REGULAR USERS --}}
                    
                    <!-- Regular User Badge -->
                    <li class="nav-item mb-2">
                        <div class="nav-link nav-badge nav-badge-info">
                            <i class="fas fa-user me-2"></i>
                            <strong>User:</strong> {{ $user->name }}
                        </div>
                    </li>

                    <!-- Regular User Note -->
                    <li class="nav-item mt-3">
                        <div class="nav-link nav-badge nav-badge-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>You have regular user access. Contact support for additional permissions.</small>
                        </div>
                    </li>

                @else
                    {{-- ❌ RESTRICTED OR UNKNOWN USERS --}}
                    
                    <li class="nav-item mb-2">
                        <div class="nav-link nav-badge nav-badge-restricted">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>❌ Access Restricted</strong>
                        </div>
                    </li>

                    <li class="nav-item">
                        <div class="nav-link nav-badge nav-badge-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>Please contact support for assistance.</small>
                        </div>
                    </li>

                @endif

            @endauth

            <!-- Bottom Divider -->
            <hr class="sidebar-divider">

        </ul>
        <!-- End of Sidebar -->

        <!-- CONTENT WRAPPER - MOBILE RESPONSIVE -->
        <div id="content-wrapper" class="d-flex flex-column">
            
            <!-- TOPBAR - MOBILE RESPONSIVE -->
            <nav class="navbar navbar-expand bg-gradient-dark topbar static-top shadow">
                <!-- Mobile Sidebar Toggle -->
                <button id="sidebarToggleTop" class="btn btn-link rounded-circle me-2 me-md-3 d-lg-none" type="button">
                    <i class="fa fa-bars"></i>
                </button>

                <!-- Desktop Sidebar Toggle -->
                <button id="sidebarToggleDesktop" class="btn btn-link rounded-circle me-2 me-md-3 d-none d-lg-inline-block" type="button">
                    <i class="fa fa-bars"></i>
                </button>

                <!-- Page Title -->
                <div class="d-none d-md-inline h5 mb-0 fw-bold flex-grow-1">
                    @yield('page-title', 'Admin Panel')
                </div>

                <!-- Topbar Navbar -->
                <ul class="navbar-nav ms-auto">
                    <!-- Header Actions -->
                    @yield('header-actions')

                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" 
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="me-2 d-none d-lg-inline fw-medium">{{ auth()->user()->name }}</span>
                            <img class="img-profile rounded-circle" width="32" height="32"
                                 src="{{ asset('backend/img/undraw_profile.svg') }}" alt="Profile">
                        </a>
                        <!-- Dropdown - User Information -->
                        <ul class="dropdown-menu dropdown-menu-end shadow animate-fade-in">
                            <li>
                                <a class="dropdown-item" href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw me-2"></i>
                                    Logout
                                </a>
                            </li>
                        </ul>
                        <form id="logout-form" action="{{ route('logout') }}" method="post" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </nav>
            <!-- End of Topbar -->

            <!-- MAIN CONTENT - MOBILE RESPONSIVE -->
            <div id="content">
                
                <!-- Session Messages -->
                @if(session()->has('message'))
                    <div class="container-fluid">
                        <div class="alert alert-{{ session()->get('alert-type', 'info') }} alert-dismissible fade show animate-fade-in" 
                             role="alert" id="alert-message">
                            <i class="fas fa-{{ session()->get('alert-type') == 'success' ? 'check-circle' : 'info-circle' }} me-2"></i>
                            {{ session()->get('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                @endif

                <!-- Dynamic Alert Container for AJAX -->
                <div class="container-fluid">
                    <div id="alertContainer"></div>
                </div>

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- /.container-fluid -->
                
            </div>
            <!-- End of Main Content -->

            <!-- FOOTER - MOBILE RESPONSIVE -->
            <footer class="sticky-footer bg-dark">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span class="text-primary fw-bold">
                            <i class="fas fa-copyright me-1"></i>
                            <span class="d-none d-sm-inline">Copyright &copy; {{ date('Y') }} ScoriaIT - Premium Admin Panel</span>
                            <span class="d-sm-none">&copy; {{ date('Y') }} ScoriaIT</span>
                        </span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->
            
        </div>
        <!-- End of Content Wrapper -->
        
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button - Mobile Friendly -->
    <a class="scroll-to-top rounded animate-scale" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal - Mobile Responsive -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="logoutModalLabel">
                        <i class="fas fa-sign-out-alt me-2"></i>Ready to Leave?
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Select "Logout" below if you are ready to end your current session.</p>
                </div>
                <div class="modal-footer d-flex gap-2">
                    <form action="{{ route('logout') }}" method="POST" class="d-flex gap-2 flex-grow-1">
                        @csrf
                        <button class="btn btn-secondary flex-fill" type="button" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button class="btn btn-primary flex-fill" type="submit">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables Scripts -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <!-- Enhanced Mobile-Responsive Script -->
    <script>
        $(function() {
            // Global CSRF token setup for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Mobile-Responsive DataTable Configuration
            const isMobile = window.innerWidth < 768;
            
            $.extend(true, $.fn.dataTable.defaults, {
                language: {
                    search: "Search:",
                    lengthMenu: isMobile ? "Show _MENU_" : "Show _MENU_ entries",
                    info: isMobile ? "_START_-_END_ of _TOTAL_" : "Showing _START_ to _END_ of _TOTAL_ entries",
                    paginate: {
                        first: "««",
                        last: "»»",
                        next: "»",
                        previous: "«"
                    }
                },
                responsive: true,
                pageLength: isMobile ? 10 : 25,
                lengthMenu: isMobile ? [[5, 10, 25, -1], [5, 10, 25, "All"]] : [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                dom: isMobile 
                    ? '<"row"<"col-12"f>><"row"<"col-12"tr>><"row"<"col-12"p>>'
                    : '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>><"row"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>><"row"<"col-sm-12"B>>',
                buttons: !isMobile ? [
                    {
                        extend: 'excel',
                        className: 'btn btn-outline-primary btn-sm me-2',
                        text: '<i class="fas fa-file-excel me-1"></i>Excel'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-outline-primary btn-sm me-2',
                        text: '<i class="fas fa-file-pdf me-1"></i>PDF'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-outline-primary btn-sm',
                        text: '<i class="fas fa-print me-1"></i>Print'
                    }
                ] : []
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('#alert-message').fadeOut('slow');
            }, 5000);

            // Mobile Sidebar Toggle
            const sidebar = $('#accordionSidebar');
            const overlay = $('#sidebarOverlay');
            
            // Mobile sidebar toggle
            $('#sidebarToggleTop').click(function(e) {
                e.preventDefault();
                sidebar.toggleClass('show');
                overlay.toggleClass('show');
                $('body').toggleClass('overflow-hidden');
            });

            // Desktop sidebar toggle
            $('#sidebarToggleDesktop').click(function(e) {
                e.preventDefault();
                sidebar.toggleClass('toggled');
            });

            // Close sidebar when clicking overlay (mobile)
            overlay.click(function() {
                sidebar.removeClass('show');
                overlay.removeClass('show');
                $('body').removeClass('overflow-hidden');
            });

            // Close sidebar when clicking outside (mobile)
            $(document).on('click', function(e) {
                if ($(window).width() < 992) {
                    if (!$(e.target).closest('#accordionSidebar, #sidebarToggleTop').length && sidebar.hasClass('show')) {
                        sidebar.removeClass('show');
                        overlay.removeClass('show');
                        $('body').removeClass('overflow-hidden');
                    }
                }
            });

            // Handle window resize
            $(window).resize(function() {
                if ($(window).width() >= 992) {
                    sidebar.removeClass('show');
                    overlay.removeClass('show');
                    $('body').removeClass('overflow-hidden');
                }
            });

            // Swipe gesture for mobile sidebar
            let touchStartX = null;
            let touchEndX = null;

            $('body').on('touchstart', function(e) {
                touchStartX = e.originalEvent.touches[0].clientX;
            });

            $('body').on('touchend', function(e) {
                touchEndX = e.originalEvent.changedTouches[0].clientX;
                handleSwipe();
            });

            function handleSwipe() {
                if (!touchStartX || !touchEndX) return;
                
                const swipeDistance = touchEndX - touchStartX;
                const minSwipeDistance = 100;

                if ($(window).width() < 768) {
                    // Swipe right to open sidebar
                    if (swipeDistance > minSwipeDistance && touchStartX < 50) {
                        sidebar.addClass('show');
                        overlay.addClass('show');
                        $('body').addClass('overflow-hidden');
                    }
                    // Swipe left to close sidebar
                    else if (swipeDistance < -minSwipeDistance && sidebar.hasClass('show')) {
                        sidebar.removeClass('show');
                        overlay.removeClass('show');
                        $('body').removeClass('overflow-hidden');
                    }
                }

                touchStartX = null;
                touchEndX = null;
            }
        });

        // Enhanced Mobile-Responsive Alert Function
        function showAlert(message, type = 'info') {
            const icons = {
                success: 'check-circle',
                error: 'exclamation-circle',
                warning: 'exclamation-triangle',
                info: 'info-circle'
            };

            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show animate-fade-in" role="alert">
                    <i class="fas fa-${icons[type] || 'info-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            $('#alertContainer').html(alertHtml);
            
            // Auto-hide after 5 seconds
            setTimeout(function() {
                $('#alertContainer .alert').fadeOut('slow');
            }, 5000);
            
            // Scroll to top to show alert
            $('html, body').animate({
                scrollTop: 0
            }, 300);
        }

        // Enhanced error display function
        function displayErrors(errors) {
            clearErrors();
            
            for (const field in errors) {
                const errorElement = $(`#${field}Error`);
                const inputElement = $(`#${field}`);
                
                if (errorElement.length && inputElement.length) {
                    errorElement.text(errors[field][0]);
                    inputElement.addClass('is-invalid');
                }
            }
        }

        // Enhanced error clear function
        function clearErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
        }

        // Mobile-Responsive Loading State Management
        function setLoadingState(element, loading = true) {
            if (loading) {
                element.prop('disabled', true);
                const isMobile = window.innerWidth < 576;
                element.html(`<span class="spinner-border spinner-border-sm me-${isMobile ? '1' : '2'}" role="status"></span>${isMobile ? 'Loading...' : 'Loading...'}`);
            } else {
                element.prop('disabled', false);
            }
        }

        // Prevent zoom on iOS when focusing inputs
        if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
            $('input[type="text"], input[type="email"], input[type="password"], textarea').on('focus', function() {
                $(this).css('font-size', '16px');
            }).on('blur', function() {
                $(this).css('font-size', '');
            });
        }
    </script>

    @stack('scripts')
    @stack('script-alt')
</body>
</html>
