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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- jQuery UI -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <!-- Complete Mobile-First Black & Gold Responsive Layout with Improved Contrast -->
    <style>
        :root {
            --primary-gold: #FFD700;
            --accent-gold: #FFD700;
            --secondary-gold: #FFA500;
            --dark-bg: #0a0a0b;
            --darker-bg: #060608;
            --light-dark: #131316;
            --card-bg: #16161a;
            --border-color: rgba(255, 255, 255, 0.06);
            --border-gold: rgba(255, 215, 0, 0.12);
            --sidebar-width: 272px;
            --sidebar-width-mobile: 260px;
            --topbar-height: 64px;
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

        /* MOBILE-FIRST RESET AND BASE STYLES */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
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

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            top: 0;
            left: -100%;
            width: var(--sidebar-width-mobile);
            height: 100vh;
            background: var(--darker-bg);
            border-right: 1px solid var(--border-color);
            z-index: 1060;
            overflow-y: auto;
            overflow-x: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 1.25rem 0;
        }

        .sidebar.show {
            left: 0;
            box-shadow: 20px 0 50px rgba(0, 0, 0, 0.5);
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

        /* Sidebar Logo */
        .sidebar-logo-container {
            display: flex;
            justify-content: center;
            padding: 1.25rem 1rem;
            margin-bottom: 0.5rem;
        }

        .sidebar-logo-link {
            display: block;
            transition: transform 0.2s ease, opacity 0.2s ease;
        }

        .sidebar-logo-link:hover {
            transform: scale(1.02);
            opacity: 0.9;
        }

        .sidebar-logo {
            max-width: 160px;
            height: auto;
            filter: drop-shadow(0 2px 8px rgba(255, 215, 0, 0.15));
        }

        /* Sidebar Navigation */
        .sidebar .nav-item {
            margin-bottom: 2px;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            padding: 0.625rem 1rem;
            color: var(--text-muted) !important;
            text-decoration: none;
            margin: 0 0.75rem;
            border-radius: var(--radius-sm);
            transition: all 0.15s ease;
            font-weight: 500;
            font-size: 0.8125rem;
            letter-spacing: 0.01em;
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 215, 0, 0.05);
            color: var(--primary-gold) !important;
            transform: translateX(2px);
        }

        .sidebar .nav-link.active,
        .sidebar .nav-item.active .nav-link {
            background: rgba(255, 215, 0, 0.08) !important;
            color: var(--accent-gold) !important;
            font-weight: 600;
        }

        .sidebar .nav-link .nav-icon {
            width: 18px;
            text-align: center;
            font-size: 0.875rem;
            opacity: 0.6;
            transition: opacity 0.15s;
        }

        .sidebar .nav-link:hover .nav-icon,
        .sidebar .nav-link.active .nav-icon {
            opacity: 1;
        }

        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
            font-size: 0.875rem;
            opacity: 0.6;
        }

        .sidebar .nav-link:hover i,
        .sidebar .nav-link.active i {
            opacity: 1;
        }

        .sidebar-divider {
            border-color: var(--border-color) !important;
            margin: 0.75rem 1.25rem;
        }

        /* CONTENT WRAPPER */
        #content-wrapper {
            margin-left: 0;
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: var(--dark-bg);
            transition: margin-left 0.3s ease;
        }

        /* TOPBAR */
        .topbar {
            background: var(--darker-bg) !important;
            border-bottom: 1px solid var(--border-color);
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1040;
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
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
            color: #e2e8f0 !important;
            transition: all 0.3s ease;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
        }

        .topbar .dropdown-item:hover {
            background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
            color: #000 !important;
        }

        .topbar .dropdown-item i {
            color: var(--primary-gold);
        }

        .topbar .dropdown-item:hover i {
            color: #000;
        }

        .img-profile {
            border: 1px solid var(--border-color);
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            padding: 2px;
        }

        /* MAIN CONTENT */
        #content {
            flex: 1;
            padding: 0;
            margin-top: 0;
        }

        .container-fluid {
            padding: 1rem;
        }

        /* Shared utility classes used across admin views */
        .text-light-muted {
            color: var(--text-muted) !important;
        }

        /* Action buttons - subtle ghost style matching gold/black theme */
        .btn-warning-custom {
            background: rgba(255, 215, 0, 0.06) !important;
            border: 1px solid rgba(255, 215, 0, 0.15) !important;
            color: var(--primary-gold) !important;
            font-weight: 600;
        }

        .btn-warning-custom:hover {
            background: var(--primary-gold) !important;
            border-color: var(--primary-gold) !important;
            color: #000 !important;
            transform: translateY(-1px);
        }

        .btn-danger-custom {
            background: rgba(255, 255, 255, 0.03) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: var(--text-muted) !important;
            font-weight: 600;
        }

        .btn-danger-custom:hover {
            background: var(--danger) !important;
            border-color: var(--danger) !important;
            color: #fff !important;
            transform: translateY(-1px);
        }

        .btn-success {
            background: var(--primary-gold);
            border-color: var(--primary-gold);
            color: #000;
            font-weight: 700;
        }

        .btn-success:hover {
            background: #ffe44d;
            border-color: #ffe44d;
            color: #000;
        }

        /* Shared delete modal styling */
        .delete-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
        }

        .delete-modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .delete-modal-content {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            width: 90%;
            max-width: 440px;
            overflow: hidden;
            animation: modalSlideUp 0.2s ease;
        }

        @keyframes modalSlideUp {
            from { opacity: 0; transform: translateY(16px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .delete-modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .delete-modal-title {
            font-size: 1rem;
            font-weight: 700;
            margin: 0;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .delete-modal-title i {
            color: var(--danger);
        }

        .delete-modal-close {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1rem;
            cursor: pointer;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-sm);
            transition: all 0.15s;
        }

        .delete-modal-close:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-main);
        }

        .delete-modal-body {
            padding: 1.5rem;
            text-align: center;
        }

        .delete-modal-icon {
            color: var(--danger);
            font-size: 2.5rem;
            margin-bottom: 1rem;
            opacity: 0.8;
        }

        .delete-modal-text {
            color: var(--text-main);
            font-size: 0.9375rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .delete-modal-subtext {
            color: var(--text-muted);
            font-size: 0.8125rem;
            line-height: 1.5;
        }

        .delete-modal-user,
        .delete-modal-announcement,
        .delete-modal-image {
            background: rgba(255, 215, 0, 0.06);
            border: 1px solid var(--border-gold);
            border-radius: var(--radius-sm);
            padding: 0.75rem 1rem;
            margin: 1rem 0;
            color: var(--primary-gold);
            font-weight: 600;
            font-size: 0.875rem;
        }

        .delete-modal-footer {
            display: flex;
            gap: 0.75rem;
            padding: 0 1.5rem 1.5rem;
        }

        .modal-btn {
            flex: 1;
            padding: 0.625rem 1rem;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: 0.8125rem;
            cursor: pointer;
            transition: all 0.15s;
        }

        .modal-btn-cancel {
            background: rgba(255, 255, 255, 0.06);
            color: var(--text-muted);
        }

        .modal-btn-cancel:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-main);
        }

        .modal-btn-delete {
            background: var(--danger);
            color: #fff;
        }

        .modal-btn-delete:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        .modal-btn-delete.processing {
            background: var(--text-muted);
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* Status badges */
        .bg-success-custom {
            background: rgba(34, 197, 94, 0.12) !important;
            color: var(--success) !important;
            font-weight: 600;
        }

        .bg-info-custom {
            background: rgba(59, 130, 246, 0.12) !important;
            color: var(--info) !important;
            font-weight: 600;
        }

        .status-badge {
            font-size: 0.6875rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }

        /* FOOTER */
        .sticky-footer {
            background: var(--darker-bg) !important;
            border-top: 1px solid var(--border-color);
            color: var(--text-muted);
            padding: 0.75rem 0;
            margin-top: auto;
            font-size: 0.75rem;
        }

        .sticky-footer .text-primary {
            color: var(--primary-gold) !important;
            font-weight: 600;
            font-size: 0.75rem;
        }

        /* COMPONENTS */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: none;
            margin-bottom: 1.5rem;
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-color);
            padding: 1.25rem 1.5rem;
        }

        .card-header .card-title {
            color: var(--text-main);
            font-weight: 700;
            font-size: 1rem;
            margin: 0;
            letter-spacing: -0.02em;
        }

        .card-header .card-title i {
            color: var(--accent-gold);
            opacity: 0.8;
        }

        /* Card header flex layout when using card-tools */
        .card-header:has(.card-tools) {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .card-body {
            padding: 1.25rem;
        }

        .card-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--border-color);
        }

        /* Gold header variant */
        .card-header.bg-gold {
            background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold)) !important;
            border-bottom: none;
        }

        .card-header.bg-gold .card-title {
            color: #000 !important;
        }

        /* Buttons */
        .btn {
            min-height: 40px;
            padding: 0.5rem 1rem;
            font-size: 0.8125rem;
            font-weight: 600;
            border-radius: var(--radius-sm);
            transition: all 0.15s ease;
            letter-spacing: 0.01em;
        }

        .btn-sm {
            min-height: auto;
            padding: 0.3rem 0.625rem;
            font-size: 0.75rem;
        }

        .btn-xs {
            min-height: auto;
            padding: 0.2rem 0.5rem;
            font-size: 0.6875rem;
        }

        .btn-lg {
            min-height: 48px;
            padding: 0.75rem 1.5rem;
            font-size: 0.9375rem;
        }

        .btn-primary {
            background: var(--accent-gold);
            border: 1px solid var(--accent-gold);
            color: #000;
            border-radius: var(--radius-sm);
            font-weight: 700;
        }

        .btn-primary:hover {
            background: #ffe44d;
            border-color: #ffe44d;
            color: #000;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 215, 0, 0.25);
        }

        .btn-outline-primary {
            border-color: rgba(255, 215, 0, 0.3);
            color: var(--primary-gold);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: rgba(255, 215, 0, 0.08);
            border-color: var(--primary-gold);
            color: var(--primary-gold);
        }

        /* Forms */
        .form-control {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            font-size: 16px; /* Prevents zoom on iOS */
            min-height: 42px;
            padding: 0.5rem 0.75rem;
            border-radius: var(--radius-sm);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.06);
            border-color: var(--primary-gold);
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
            color: var(--text-main);
        }

        .form-control::placeholder {
            color: var(--text-muted);
            opacity: 0.6;
        }

        .form-label {
            color: var(--text-main);
            font-weight: 500;
            margin-bottom: 0.375rem;
            font-size: 0.8125rem;
        }

        .form-select {
            background-color: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            border-radius: var(--radius-sm);
        }

        .form-select:focus {
            border-color: var(--primary-gold);
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
        }

        /* Tables */
        .table {
            color: var(--text-main);
            font-size: 0.8125rem;
        }

        .table-responsive {
            border-radius: var(--radius-md);
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-dark {
            background: transparent !important;
            --bs-table-bg: transparent;
        }

        .table-dark th {
            background: transparent;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.6875rem;
            letter-spacing: 0.08em;
            border-bottom: 1px solid var(--border-color) !important;
            border-top: none !important;
            padding: 0.625rem 1rem 0.75rem;
            white-space: nowrap;
        }

        .table-dark td {
            border-color: transparent !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03) !important;
            padding: 0.75rem 1rem;
            color: var(--text-main);
            vertical-align: middle;
        }

        .table-hover tbody tr {
            transition: background-color 0.12s ease;
        }

        .table-hover tbody tr:hover {
            background: rgba(255, 215, 0, 0.03) !important;
            --bs-table-hover-bg: rgba(255, 215, 0, 0.03);
        }

        /* Alerts */
        .alert {
            border-radius: var(--radius-sm);
            margin-bottom: 1rem;
            font-size: 0.8125rem;
            font-weight: 500;
            border-width: 1px;
        }

        .alert-success, .alert-success-custom {
            background: rgba(34, 197, 94, 0.08) !important;
            border-color: rgba(34, 197, 94, 0.2) !important;
            color: var(--success) !important;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.08);
            border-color: rgba(239, 68, 68, 0.2);
            color: var(--danger);
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.08);
            border-color: rgba(245, 158, 11, 0.2);
            color: var(--warning);
        }

        .alert-info {
            background: rgba(59, 130, 246, 0.08);
            border-color: rgba(59, 130, 246, 0.2);
            color: var(--info);
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

        /* ========================================
           PREMIUM DATATABLES STYLING
           ======================================== */

        .dataTables_wrapper {
            color: var(--text-main);
            font-size: 0.875rem;
            padding: 0;
        }

        /* Hide default DataTables info text */
        .dataTables_info {
            color: var(--text-muted) !important;
            font-size: 0.8125rem;
            padding-top: 1rem !important;
        }

        /* Top controls container */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1.5rem;
        }

        /* Show entries dropdown - Premium Style */
        .dataTables_length {
            display: none !important; /* Hide the ugly "Show X entries" */
        }

        /* Search Input - Premium Style */
        .dataTables_filter {
            float: right !important;
        }

        .dataTables_filter label {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.8125rem;
            margin: 0;
        }

        .dataTables_filter input {
            background: var(--card-bg) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: var(--radius-sm) !important;
            color: var(--text-main) !important;
            font-size: 0.875rem !important;
            padding: 0.625rem 1rem !important;
            min-width: 240px;
            transition: all 0.2s ease;
        }

        .dataTables_filter input:focus {
            border-color: var(--primary-gold) !important;
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1) !important;
            outline: none !important;
        }

        .dataTables_filter input::placeholder {
            color: var(--text-muted);
        }

        /* Premium Table Styling */
        table.dataTable {
            border-collapse: separate !important;
            border-spacing: 0 !important;
            width: 100% !important;
            margin: 0 !important;
        }

        table.dataTable thead th {
            background: var(--darker-bg) !important;
            border-bottom: 1px solid var(--border-gold) !important;
            border-top: none !important;
            color: var(--text-muted) !important;
            font-weight: 600 !important;
            font-size: 0.75rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            padding: 1rem 0.75rem !important;
            white-space: nowrap;
        }

        table.dataTable thead th:first-child {
            border-radius: var(--radius-sm) 0 0 0;
        }

        table.dataTable thead th:last-child {
            border-radius: 0 var(--radius-sm) 0 0;
        }

        table.dataTable tbody tr {
            background: transparent !important;
            transition: background 0.15s ease;
        }

        table.dataTable tbody tr:hover {
            background: rgba(255, 215, 0, 0.03) !important;
        }

        table.dataTable tbody td {
            border-bottom: 1px solid var(--border-color) !important;
            border-top: none !important;
            color: var(--text-main) !important;
            padding: 1rem 0.75rem !important;
            font-size: 0.875rem;
            vertical-align: middle !important;
        }

        table.dataTable tbody tr:last-child td {
            border-bottom: none !important;
        }

        /* Sorting Icons */
        table.dataTable thead .sorting,
        table.dataTable thead .sorting_asc,
        table.dataTable thead .sorting_desc {
            cursor: pointer;
            position: relative;
        }

        table.dataTable thead .sorting:after,
        table.dataTable thead .sorting_asc:after,
        table.dataTable thead .sorting_desc:after {
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            right: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.4;
            font-size: 0.625rem;
        }

        table.dataTable thead .sorting:after {
            content: "\f0dc";
        }

        table.dataTable thead .sorting_asc:after {
            content: "\f0de";
            opacity: 1;
            color: var(--primary-gold);
        }

        table.dataTable thead .sorting_desc:after {
            content: "\f0dd";
            opacity: 1;
            color: var(--primary-gold);
        }

        /* PREMIUM PAGINATION */
        .dataTables_wrapper .dataTables_paginate {
            padding-top: 1.25rem !important;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 0.25rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 0.75rem !important;
            margin: 0 2px !important;
            border-radius: var(--radius-sm) !important;
            border: 1px solid transparent !important;
            background: transparent !important;
            color: var(--text-muted) !important;
            font-size: 0.8125rem !important;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: rgba(255, 215, 0, 0.08) !important;
            color: var(--primary-gold) !important;
            border-color: rgba(255, 215, 0, 0.2) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary-gold) !important;
            color: #000 !important;
            border-color: var(--primary-gold) !important;
            font-weight: 700;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.3 !important;
            cursor: not-allowed;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background: transparent !important;
            color: var(--text-muted) !important;
            border-color: transparent !important;
        }

        /* Previous/Next buttons */
        .dataTables_wrapper .dataTables_paginate .paginate_button.previous,
        .dataTables_wrapper .dataTables_paginate .paginate_button.next {
            font-size: 0 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.previous:before {
            content: "\f053";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            font-size: 0.75rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.next:before {
            content: "\f054";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            font-size: 0.75rem;
        }

        /* Premium Badge Styles */
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.01em;
        }

        .badge-status-regular {
            background: rgba(255, 215, 0, 0.12);
            color: var(--primary-gold);
            border: 1px solid rgba(255, 215, 0, 0.2);
        }

        .badge-status-partner {
            background: rgba(59, 130, 246, 0.12);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .badge-access-full {
            background: rgba(34, 197, 94, 0.12);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.2);
        }

        .badge-access-specific {
            background: rgba(255, 215, 0, 0.12);
            color: var(--primary-gold);
            border: 1px solid rgba(255, 215, 0, 0.2);
        }

        .badge-role {
            background: rgba(139, 92, 246, 0.12);
            color: #a78bfa;
            border: 1px solid rgba(139, 92, 246, 0.2);
        }

        /* No data message */
        table.dataTable tbody td.dataTables_empty {
            color: var(--text-muted) !important;
            text-align: center;
            padding: 3rem 1rem !important;
            font-style: italic;
        }

        /* Scroll to Top */
        .scroll-to-top {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #d4a017, #f0c040);
            border: 1px solid #d4a017;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1a1a2e;
            font-size: 0.75rem;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(212, 160, 23, 0.35);
            transition: all 0.3s ease;
            z-index: 1050;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
        }

        .scroll-to-top.show {
            opacity: 0.85;
            visibility: visible;
            transform: translateY(0);
        }

        .scroll-to-top:hover {
            opacity: 1;
            background: linear-gradient(135deg, #f0c040, #ffe066);
            transform: translateY(-2px);
            color: #1a1a2e;
            box-shadow: 0 4px 12px rgba(212, 160, 23, 0.5);
        }

        /* Animations */
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-scale {
            transition: transform 0.15s ease;
        }

        /* TEXT & CONTRAST */
        .text-muted {
            color: var(--text-muted) !important;
        }

        .text-light {
            color: var(--text-main) !important;
        }

        small {
            color: var(--text-muted) !important;
        }

        .badge {
            font-weight: 600;
            font-size: 0.6875rem;
            letter-spacing: 0.02em;
        }

        .text-gold {
            color: var(--primary-gold) !important;
        }

        .border-gold {
            border-color: var(--border-gold) !important;
        }

        .bg-light-dark {
            background-color: var(--card-bg) !important;
        }

        /* Shared responsive font size classes */
        .fs-8 { font-size: 0.7rem !important; }
        .fs-7 { font-size: 0.8rem !important; }

        /* Required field asterisk */
        .text-required { color: var(--danger); }

        /* Section header (gold uppercase label) */
        .section-label {
            color: var(--accent-gold);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 0.75rem;
        }

        /* Section divider */
        .section-divider {
            border-color: var(--border-color);
            margin: 1.5rem 0;
            opacity: 1;
        }

        /* Role/access badges */
        .badge-gold {
            background: rgba(255, 215, 0, 0.12);
            color: var(--primary-gold);
            font-weight: 600;
            font-size: 0.6875rem;
        }
        .badge-role {
            background: rgba(23, 162, 184, 0.12);
            color: #4fd1c7;
            font-weight: 500;
            font-size: 0.6875rem;
        }

        /* Access type selection cards (used in user create/edit) */
        .access-type-card {
            cursor: pointer;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1.25rem;
            transition: all 0.2s ease;
            background: rgba(255, 255, 255, 0.02);
        }
        .access-type-card:hover {
            border-color: rgba(255, 215, 0, 0.3);
            background: rgba(255, 215, 0, 0.03);
        }
        .access-type-card.selected {
            border-color: var(--accent-gold);
            background: rgba(255, 215, 0, 0.08);
        }
        .access-card-inner {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .access-card-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            background: rgba(255, 215, 0, 0.1);
            color: var(--accent-gold);
            flex-shrink: 0;
        }
        .access-type-card.selected .access-card-icon {
            background: var(--accent-gold);
            color: #000;
        }

        /* Module checkbox cards (used in user create/edit) */
        .module-checkbox-card {
            display: block;
            cursor: pointer;
            margin: 0;
        }
        .module-checkbox-card input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        .module-card-inner {
            border: 2px solid var(--border-color);
            border-radius: 10px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s ease;
            background: rgba(255, 255, 255, 0.02);
            color: var(--text-main);
        }
        .module-card-inner:hover {
            border-color: rgba(255, 215, 0, 0.3);
        }
        .module-card-inner .module-icon {
            color: var(--text-muted);
            font-size: 1rem;
            width: 20px;
            text-align: center;
        }
        .module-card-inner .check-icon {
            margin-left: auto;
            color: transparent;
            transition: color 0.15s ease;
        }
        .module-checkbox-card input:checked + .module-card-inner {
            border-color: var(--accent-gold);
            background: rgba(255, 215, 0, 0.08);
        }
        .module-checkbox-card input:checked + .module-card-inner .module-icon {
            color: var(--accent-gold);
        }
        .module-checkbox-card input:checked + .module-card-inner .check-icon {
            color: var(--accent-gold);
        }

        /* Shared form-control-mobile (used by announcement forms) */
        .form-control-mobile {
            font-size: 16px !important; /* Prevents zoom on iOS */
        }

        /* Mobile-friendly touch target */
        .btn-mobile {
            min-height: 44px !important;
            padding: 0.5rem 1rem !important;
        }

        /* Input Group dark theme */
        .input-group-text {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            font-size: 0.8125rem;
        }

        /* Global accent for native radios/checkboxes */
        input[type="radio"],
        input[type="checkbox"] {
            accent-color: #FFD700;
        }

        /* Form Check dark theme */
        .form-check-input {
            background-color: rgba(255, 255, 255, 0.06);
            border-color: var(--border-color);
        }

        .form-check-input:checked {
            background-color: var(--primary-gold);
            border-color: var(--primary-gold);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.15);
            border-color: var(--primary-gold);
        }

        .form-check-label {
            color: var(--text-main);
            font-size: 0.8125rem;
        }

        .form-text {
            color: var(--text-muted);
            font-size: 0.75rem;
        }

        /* btn-close dark theme (white icon) */
        .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
            opacity: 0.5;
        }
        .btn-close:hover {
            opacity: 0.8;
        }

        /* Secondary button */
        .btn-secondary {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid var(--border-color);
            color: var(--text-muted);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.12);
            color: var(--text-main);
        }

        .btn-outline-secondary {
            border-color: var(--border-color);
            color: var(--text-muted);
        }

        .btn-outline-secondary:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.12);
            color: var(--text-main);
        }

        /* File upload - styled container */
        input[type="file"].form-control {
            padding: 0;
            border: 1px dashed rgba(255, 215, 0, 0.2);
            background: rgba(255, 215, 0, 0.02);
            overflow: hidden;
        }

        input[type="file"].form-control:hover {
            border-color: rgba(255, 215, 0, 0.4);
            background: rgba(255, 215, 0, 0.04);
        }

        input[type="file"].form-control:focus {
            border-color: var(--primary-gold);
            border-style: solid;
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
        }

        input[type="file"]::-webkit-file-upload-button {
            background: var(--accent-gold);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0;
            color: #000;
            font-weight: 700;
            cursor: pointer;
            margin-right: 0.75rem;
            font-size: 0.8125rem;
            transition: background 0.15s;
        }

        input[type="file"]::-webkit-file-upload-button:hover {
            background: #ffe44d;
        }

        input[type="file"]::file-selector-button {
            background: var(--accent-gold);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0;
            color: #000;
            font-weight: 700;
            cursor: pointer;
            margin-right: 0.75rem;
            font-size: 0.8125rem;
            transition: background 0.15s;
        }

        input[type="file"]::file-selector-button:hover {
            background: #ffe44d;
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

        /* BREAKPOINT: Desktop (992px+) */
        @media (min-width: 992px) {
            :root {
                --sidebar-width: 256px;
            }

            body {
                font-size: 14px;
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
                padding: 0 1.5rem;
            }

            .container-fluid {
                padding: 1.5rem;
            }

            .sidebar .nav-link {
                padding: 0.625rem 1rem;
                font-size: 0.8125rem;
            }

            .sidebar-brand {
                font-size: 0.875rem;
                padding: 0.75rem 1.25rem;
                margin: 0.75rem 1rem 0;
            }

            .card-body {
                padding: 1.5rem;
            }
        }

        /* BREAKPOINT: Large Desktop (1200px+) */
        @media (min-width: 1200px) {
            .container-fluid {
                padding: 1.5rem 2rem;
            }

            .topbar .h5 {
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
            <!-- Sidebar - Brand Logo -->
            <li class="sidebar-logo-container">
                <a href="{{ route('admin.users.index') }}" class="sidebar-logo-link">
                    <img src="{{ asset('assets/index_files/logo.png') }}" alt="Go Trips" class="sidebar-logo">
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Navigation Items -->
            @auth
                @php
                    $user = Auth::user();
                    $userType = session('user_type', 'unknown');
                    $isPartnerRestricted = session('is_partner_restricted', false);
                @endphp

                {{-- Permission-based menu items --}}
                @if($user->isAdmin())
                    <li class="nav-item {{ (request()->is('admin/users') || request()->is('admin/users*')) ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.users.index') }}">
                            <i class="fas fa-desktop"></i>
                            <span>Admin Users</span>
                        </a>
                    </li>
                @endif

                @if($user->hasPermission('manage_announcements'))
                    <li class="nav-item">
                        <a href="{{ route('admin.announcements.index') }}" class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                            <i class="fas fa-bullhorn"></i>
                            <span>Announcements</span>
                        </a>
                    </li>
                @endif

                @if($user->hasPermission('manage_carousel'))
                    <li class="nav-item">
                        <a href="{{ route('admin.homepageads.index') }}" class="nav-link {{ request()->routeIs('admin.homepageads.*') ? 'active' : '' }}">
                            <i class="fas fa-images"></i>
                            <span>Travel Ads</span>
                        </a>
                    </li>
                @endif

                @if($user->hasPermission('manage_uae_activities'))
                    <li class="nav-item">
                        <a href="{{ route('admin.uaeactivities.index') }}" class="nav-link {{ request()->routeIs('admin.uaeactivities.*') ? 'active' : '' }}">
                            <i class="fas fa-map-marked-alt"></i>
                            <span>UAE Activities</span>
                        </a>
                    </li>
                @endif

                {{-- Fallback for partners/legacy users who have UAE Activities access via session --}}
                @if($isPartnerRestricted && $userType === 'approved_partner' && !$user->hasPermission('manage_uae_activities'))
                    <li class="nav-item">
                        <a href="{{ route('admin.uaeactivities.index') }}" class="nav-link {{ request()->routeIs('admin.uaeactivities.*') ? 'active' : '' }}">
                            <i class="fas fa-map-marked-alt"></i>
                            <span>UAE Activities</span>
                        </a>
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

            <div id="content">
                <div class="container-fluid pt-0">
                    <!-- Session Messages -->
                    @if(session()->has('message'))
                        <div class="alert alert-{{ session()->get('alert-type', 'info') }} alert-dismissible fade show animate-fade-in mt-3" 
                             role="alert" id="alert-message">
                            <i class="fas fa-{{ session()->get('alert-type') == 'success' ? 'check-circle' : 'info-circle' }} me-2"></i>
                            {{ session()->get('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Dynamic Alert Container for AJAX -->
                    <div id="alertContainer"></div>

                    <!-- Begin Page Content -->
                    <div class="mt-4">
                        @yield('content')
                    </div>
                </div>
            </div>
            <!-- End of Main Content -->

            <!-- FOOTER - MOBILE RESPONSIVE -->
            <footer class="sticky-footer bg-dark">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span class="text-primary fw-bold">
                            <i class="fas fa-copyright me-1"></i>
                            <span class="d-none d-sm-inline">Copyright &copy; {{ date('Y') }} Go Trips. All rights reserved.</span>
                            <span class="d-sm-none">&copy; {{ date('Y') }} Go Trips</span>
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

        // Scroll-to-top button: show/hide on scroll, smooth scroll on click
        $(function() {
            var $btn = $('.scroll-to-top');
            $(window).on('scroll', function() {
                if ($(this).scrollTop() > 300) {
                    $btn.addClass('show');
                } else {
                    $btn.removeClass('show');
                }
            });
            $btn.on('click', function(e) {
                e.preventDefault();
                $('html, body').animate({ scrollTop: 0 }, 400);
            });
        });
    </script>

    @stack('scripts')
    @stack('script-alt')
</body>
</html>
