<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" type="image/png" href="{{ asset('assets/index_files/logo.png') }}">

    <meta charset="UTF-8">
    <title>Go Trips</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="ThemeZaa">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="description" content="Go Trips - Your Gateway to Amazing Adventures">

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="Go Trips - Al Amir YN">
    <meta property="og:description" content="Go Trips - Your Gateway to Amazing Adventures">
    <meta property="og:image" content="{{ asset('assets/index_files/social_sharing_logo.png') }}">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="1200">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- External Styles -->
    <link rel="stylesheet" href="{{ asset('assets/index_files/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('assets/index_files/vendors.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/index_files/icon.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/index_files/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/index_files/responsive.css') }}">

    <style>
        /* =====================================================
           GO TRIPS - PREMIUM HEADER DESIGN
           Built from scratch for perfect alignment
           ===================================================== */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* =====================================================
           GLOBAL TYPOGRAPHY SYSTEM
           Outfit as the single consistent font across the site
           ===================================================== */
        :root {
            --primary-font: 'Outfit', sans-serif !important;
            --alt-font: 'Outfit', sans-serif !important;
        }

        body {
            font-family: 'Outfit', sans-serif !important;
        }

        /* Override template font classes */
        .alt-font,
        [class*="alt-font"] {
            font-family: 'Outfit', sans-serif !important;
        }

        /* Global heading hierarchy - consistent sizing */
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif !important;
        }

        /* Body text / descriptions - consistent baseline */
        p, span, a, li, label, input, textarea, select, button {
            font-family: 'Outfit', sans-serif;
        }

        /* HEADER CONTAINER */
        .gt-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.98) 0%, rgba(10, 10, 10, 0.95) 100%);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 215, 0, 0.15);
        }

        /* MAIN NAV WRAPPER */
        .gt-nav-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 80px;
            padding: 0 40px;
            max-width: 1600px;
            margin: 0 auto;
        }

        /* LEFT NAVIGATION */
        .gt-nav-left {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            justify-content: flex-end;
        }

        /* CENTER LOGO */
        .gt-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 50px;
            flex-shrink: 0;
        }

        .gt-logo img {
            height: 60px;
            width: auto;
            transition: transform 0.3s ease, filter 0.3s ease;
        }

        .gt-logo:hover img {
            transform: scale(1.05);
            filter: drop-shadow(0 0 15px rgba(255, 215, 0, 0.4));
        }

        /* RIGHT NAVIGATION */
        .gt-nav-right {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            justify-content: flex-start;
        }

        /* NAV LINKS - Premium Styling */
        .gt-nav-link {
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            /* Slightly reduced font size */
            font-weight: 700;
            letter-spacing: 0.5px;
            /* Reduced spacing */
            text-transform: uppercase;
            color: #FFD700;
            text-decoration: none;
            padding: 10px 14px;
            /* Reduced padding */
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
        }

        .gt-nav-link::before {
            content: '';
            position: absolute;
            bottom: 6px;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #FFD700, transparent);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateX(-50%);
        }

        .gt-nav-link:hover {
            color: #FFFFFF;
            text-shadow: 0 0 20px rgba(255, 215, 0, 0.6);
        }

        .gt-nav-link:hover::before {
            width: 70%;
        }

        /* Active State */
        .gt-nav-link.active {
            color: #FFFFFF;
        }

        .gt-nav-link.active::before {
            width: 70%;
        }


        /* =====================================================
           INLINE SEARCH BAR + PARTNER CTA
           ===================================================== */
        .gt-inline-search-wrapper {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 12px 40px;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.95) 0%, rgba(5, 5, 5, 0.95) 100%);
            border-top: 1px solid rgba(255, 215, 0, 0.08);
        }

        .gt-inline-search {
            position: relative;
            display: flex;
            align-items: center;
            background: rgba(20, 20, 20, 0.9);
            border: 1px solid rgba(255, 215, 0, 0.25);
            border-radius: 50px;
            padding: 0 6px 0 20px;
            width: 100%;
            max-width: 580px;
            height: 46px;
            transition: all 0.3s ease;
        }

        .gt-inline-search:hover {
            border-color: rgba(255, 215, 0, 0.5);
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.08);
        }

        .gt-inline-search-icon {
            color: rgba(255, 255, 255, 0.5);
            font-size: 16px;
            flex-shrink: 0;
        }

        /* gt-inline-search-placeholder replaced by gt-inline-search-input */

        .gt-inline-search-btn {
            background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%);
            color: #000;
            border: none;
            border-radius: 50px;
            padding: 0 24px;
            height: 34px;
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1.5px;
            cursor: pointer;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .gt-inline-search-btn:hover {
            background: linear-gradient(135deg, #fff 0%, #f0f0f0 100%);
        }

        /* =====================================================
           SEARCH INPUT FIELD
           ===================================================== */
        .gt-inline-search-input {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 400;
            color: #FFFFFF;
            padding: 0 12px;
            height: 100%;
            caret-color: #FFD700;
            min-width: 0;
        }

        .gt-inline-search-input::placeholder {
            color: rgba(255, 255, 255, 0.35);
        }

        .gt-inline-search:focus-within {
            border-color: rgba(255, 215, 0, 0.5);
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.08);
        }

        /* Loading spinner */
        .gt-search-loader {
            display: flex;
            align-items: center;
            padding: 0 8px;
        }

        .gt-search-spinner {
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 215, 0, 0.2);
            border-top-color: #FFD700;
            border-radius: 50%;
            animation: gt-spin 0.6s linear infinite;
        }

        @keyframes gt-spin {
            to { transform: rotate(360deg); }
        }

        /* =====================================================
           SEARCH DROPDOWN PANEL
           ===================================================== */
        .gt-search-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: rgba(12, 12, 12, 0.96);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 215, 0, 0.15);
            border-radius: 16px;
            padding: 10px 0;
            max-height: 420px;
            overflow-y: auto;
            z-index: 10002;
            display: none;
            box-shadow:
                0 25px 60px rgba(0, 0, 0, 0.7),
                0 0 0 1px rgba(255, 215, 0, 0.05),
                inset 0 1px 0 rgba(255, 255, 255, 0.04);
            animation: gt-dropdown-in 0.2s ease-out;
        }

        .gt-search-dropdown.active {
            display: block;
        }

        @keyframes gt-dropdown-in {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .gt-search-dropdown::-webkit-scrollbar { width: 5px; }
        .gt-search-dropdown::-webkit-scrollbar-track { background: transparent; }
        .gt-search-dropdown::-webkit-scrollbar-thumb {
            background: rgba(255, 215, 0, 0.12);
            border-radius: 3px;
        }

        /* Category titles */
        .gt-dropdown-cat-title {
            font-family: 'Outfit', sans-serif;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: rgba(255, 215, 0, 0.5);
            padding: 10px 20px 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .gt-dropdown-cat-title i { font-size: 11px; }

        /* Quick suggestion pills */
        .gt-quick-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding: 8px 20px 6px;
        }

        .gt-quick-pill {
            padding: 6px 16px;
            background: rgba(255, 215, 0, 0.06);
            border: 1px solid rgba(255, 215, 0, 0.12);
            border-radius: 20px;
            font-family: 'Outfit', sans-serif;
            font-size: 12px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.65);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .gt-quick-pill:hover {
            background: rgba(255, 215, 0, 0.12);
            border-color: rgba(255, 215, 0, 0.35);
            color: #FFD700;
            transform: translateY(-1px);
        }

        /* Search result items */
        .gt-dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 20px;
            text-decoration: none !important;
            color: #fff !important;
            transition: all 0.15s ease;
            cursor: pointer;
            border-left: 3px solid transparent;
        }

        .gt-dropdown-item:hover,
        .gt-dropdown-item.gt-selected {
            background: rgba(255, 215, 0, 0.05);
            border-left-color: #FFD700;
        }

        .gt-dropdown-item-icon {
            width: 36px;
            height: 36px;
            background: rgba(255, 215, 0, 0.08);
            border: 1px solid rgba(255, 215, 0, 0.1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFD700;
            font-size: 15px;
            flex-shrink: 0;
            transition: all 0.2s;
        }

        .gt-dropdown-item:hover .gt-dropdown-item-icon,
        .gt-dropdown-item.gt-selected .gt-dropdown-item-icon {
            background: rgba(255, 215, 0, 0.12);
            border-color: rgba(255, 215, 0, 0.25);
        }

        .gt-dropdown-item-content {
            flex: 1;
            min-width: 0;
        }

        .gt-dropdown-item-title {
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 500;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.3;
        }

        .gt-highlight {
            color: #FFD700;
            font-weight: 600;
        }

        .gt-dropdown-item-desc {
            font-family: 'Outfit', sans-serif;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.35);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin: 2px 0 0;
            line-height: 1.3;
        }

        .gt-dropdown-item-arrow {
            color: rgba(255, 215, 0, 0.2);
            font-size: 12px;
            opacity: 0;
            transition: opacity 0.15s;
            flex-shrink: 0;
        }

        .gt-dropdown-item:hover .gt-dropdown-item-arrow,
        .gt-dropdown-item.gt-selected .gt-dropdown-item-arrow {
            opacity: 1;
        }

        /* No results state */
        .gt-search-empty {
            text-align: center;
            padding: 25px 20px;
        }

        .gt-search-empty > i {
            font-size: 28px;
            color: rgba(255, 255, 255, 0.12);
            display: block;
            margin-bottom: 10px;
        }

        .gt-empty-title {
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.5);
            margin: 0 0 4px;
        }

        .gt-empty-sub {
            font-family: 'Outfit', sans-serif;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.25);
            margin: 0;
        }

        /* Keyboard hints footer */
        .gt-search-kbd-hint {
            display: flex;
            justify-content: center;
            gap: 16px;
            padding: 8px 20px 4px;
            border-top: 1px solid rgba(255, 215, 0, 0.06);
            margin-top: 4px;
        }

        .gt-search-kbd-hint span {
            font-family: 'Outfit', sans-serif;
            font-size: 10px;
            color: rgba(255, 255, 255, 0.2);
        }

        .gt-search-kbd-hint kbd {
            background: rgba(255, 255, 255, 0.07);
            color: rgba(255, 255, 255, 0.4);
            padding: 1px 5px;
            border-radius: 3px;
            font-family: monospace;
            font-size: 10px;
            margin-right: 3px;
        }

        /* Mobile dropdown adjustments */
        @media (max-width: 991px) {
            .gt-search-dropdown {
                border-radius: 12px;
                max-height: 350px;
            }
            .gt-search-kbd-hint { display: none; }
            .gt-inline-search-input { font-size: 13px; }
        }

        @media (max-width: 575px) {
            .gt-search-dropdown { max-height: 300px; }
            .gt-dropdown-item { padding: 8px 15px; gap: 10px; }
            .gt-dropdown-item-icon { width: 30px; height: 30px; font-size: 13px; }
            .gt-dropdown-item-title { font-size: 13px; }
            .gt-quick-pills { gap: 6px; padding: 6px 15px; }
            .gt-quick-pill { padding: 5px 12px; font-size: 11px; }
        }

        /* PARTNER CTA - Positioned right, search stays centered */
        .gt-partner-cta {
            position: absolute;
            right: 40px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .gt-partner-label {
            font-family: 'Outfit', sans-serif;
            font-size: 11px;
            font-weight: 400;
            font-style: italic;
            color: #FFD23F;
            white-space: nowrap;
        }

        .gt-partner-title {
            font-family: 'Outfit', sans-serif;
            font-size: 12px;
            font-weight: 700;
            color: #ffffff;
            white-space: nowrap;
        }

        .gt-partner-btn {
            background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%);
            color: #000;
            border: none;
            border-radius: 50px;
            padding: 0 16px;
            height: 30px;
            font-family: 'Outfit', sans-serif;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(255, 215, 0, 0.15);
        }

        .gt-partner-btn:hover {
            background: linear-gradient(135deg, #fff 0%, #f0f0f0 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 215, 0, 0.25);
        }

        /* Hide text labels on mid screens, keep button */
        @media (max-width: 1200px) {
            .gt-partner-label,
            .gt-partner-title {
                display: none;
            }
        }

        @media (min-width: 1201px) {
            .gt-partner-cta {
                border-left: 1px solid rgba(255, 215, 0, 0.12);
                padding-left: 12px;
            }
        }

        @media (max-width: 991px) {
            .gt-inline-search-wrapper {
                padding: 10px 20px;
            }

            .gt-inline-search {
                max-width: 100%;
                height: 42px;
            }

            .gt-inline-search-placeholder {
                font-size: 13px;
            }

            .gt-inline-search-btn {
                padding: 0 18px;
                font-size: 11px;
                height: 30px;
            }

            .gt-partner-cta {
                position: static;
                transform: none;
                justify-content: center;
                width: 100%;
                margin-top: 8px;
                padding-top: 8px;
                border-left: none;
                padding-left: 0;
                border-top: 1px solid rgba(255, 215, 0, 0.1);
            }

            .gt-partner-label,
            .gt-partner-title {
                display: inline;
            }

            .gt-inline-search-wrapper {
                flex-wrap: wrap;
            }
        }

        @media (max-width: 575px) {
            .gt-inline-search-wrapper {
                padding: 8px 15px;
            }

            .gt-inline-search {
                height: 38px;
                padding: 0 5px 0 15px;
            }

            .gt-inline-search-btn {
                padding: 0 14px;
                font-size: 10px;
            }

            .gt-partner-label {
                font-size: 10px;
            }

            .gt-partner-title {
                font-size: 11px;
            }

            .gt-partner-btn {
                padding: 0 14px;
                height: 28px;
                font-size: 9px;
            }
        }

        /* =====================================================
           NEWS TICKER - Keep existing styles
           ===================================================== */
        .news-ticker {
            background: linear-gradient(90deg, #0a0a0a 0%, #111 50%, #0a0a0a 100%);
            border-top: 1px solid rgba(255, 215, 0, 0.1);
            border-bottom: 1px solid rgba(255, 215, 0, 0.2);
            height: 45px;
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .scroll {
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.5px;
            animation: scroll-left 50s linear infinite;
            display: inline-block;
            white-space: nowrap;
            padding-left: 100%;
        }

        .news-item {
            color: #fff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin: 0 20px;
            transition: all 0.3s ease;
        }

        .news-item:hover .news-text {
            color: #FFD700;
        }

        .news-text {
            color: #c4c4c4;
            transition: color 0.3s ease;
        }

        .badge-new {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            background: linear-gradient(135deg, #e53935 0%, #c62828 100%);
            color: #fff;
            text-transform: uppercase;
        }

        .tag-gold {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            background: linear-gradient(135deg, #FFD700 0%, #B8860B 100%);
            color: #000;
            text-transform: uppercase;
        }

        .tag-green {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            color: #fff;
            text-transform: uppercase;
        }

        .tag-blue {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: #fff;
            text-transform: uppercase;
        }

        .tag-yellow {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            background: linear-gradient(135deg, #FFE600 0%, #FFC107 100%);
            color: #000;
            text-transform: uppercase;
        }

        .separator {
            color: rgba(255, 215, 0, 0.3);
            margin: 0 5px;
        }

        @keyframes scroll-left {
            0% {
                transform: translateX(0%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        /* =====================================================
           MOBILE STYLES
           ===================================================== */
        .gt-mobile-header {
            display: none;
        }

        .gt-menu-toggle {
            display: none;
            width: 48px;
            height: 48px;
            background: transparent;
            border: 1px solid rgba(255, 215, 0, 0.3);
            border-radius: 10px;
            cursor: pointer;
            position: relative;
            z-index: 10001;
            transition: all 0.3s ease;
        }

        .gt-menu-toggle:hover {
            border-color: #FFD700;
            background: rgba(255, 215, 0, 0.1);
        }

        .gt-menu-toggle i {
            font-size: 24px;
            color: #FFD700;
        }

        .gt-mobile-nav {
            display: none;
            position: fixed;
            top: 80px;
            left: 0;
            right: 0;
            background: rgba(5, 5, 5, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 2px solid #FFD700;
            padding: 20px 0;
            max-height: calc(100vh - 80px);
            overflow-y: auto;
            z-index: 9998;
        }

        .gt-mobile-nav.active {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .gt-mobile-nav-link {
            display: block;
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #FFFFFF;
            text-decoration: none;
            padding: 16px 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .gt-mobile-nav-link:hover,
        .gt-mobile-nav-link:active {
            background: linear-gradient(90deg, rgba(255, 215, 0, 0.15), transparent);
            color: #FFD700;
            padding-left: 40px;
        }

        .gt-mobile-nav-link:last-child {
            border-bottom: none;
        }

        /* Mobile Logo */
        .gt-mobile-logo {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }

        .gt-mobile-logo img {
            height: 55px;
            width: auto;
        }

        /* RESPONSIVE */
        @media (max-width: 1200px) {
            .gt-nav-link {
                font-size: 11px;
                padding: 10px 12px;
                letter-spacing: 2px;
            }

            .gt-logo {
                padding: 0 30px;
            }

            .gt-logo img {
                height: 50px;
            }
        }

        @media (max-width: 991px) {
            .gt-desktop-nav {
                display: none !important;
            }

            .gt-mobile-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                height: 80px;
                padding: 0 20px;
                position: relative;
            }

            .gt-menu-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .news-ticker {
                height: 38px;
            }

            .scroll {
                font-size: 12px;
                animation-duration: 40s;
            }
        }

        @media (max-width: 575px) {
            .gt-mobile-header {
                height: 70px;
                padding: 0 15px;
            }

            .gt-mobile-logo img {
                height: 45px;
            }

            .gt-menu-toggle {
                width: 42px;
                height: 42px;
            }

            .gt-mobile-nav {
                top: 70px;
            }

            .gt-mobile-nav-link {
                font-size: 14px;
                padding: 14px 20px;
            }
        }

        /* BODY SPACING */
        body {
            margin-top: 80px;
        }

        @media (max-width: 991px) {
            body {
                margin-top: 80px;
            }
        }

        @media (max-width: 575px) {
            body {
                margin-top: 70px;
            }
        }

        /* When ticker + search bar are shown on homepage */
        @if(Request::is('/'))
            body {
                margin-top: 195px;
            }

            @media (max-width: 991px) {
                body {
                    margin-top: 180px;
                }
            }

            @media (max-width: 575px) {
                body {
                    margin-top: 162px;
                }
            }

        @endif

    </style>
</head>

<body>
    <!-- ==================== PREMIUM HEADER ==================== -->
    <header class="gt-header">

        <!-- DESKTOP NAVIGATION -->
        <nav class="gt-desktop-nav">
            <div class="gt-nav-wrapper">
                <!-- Left Menu -->
                <div class="gt-nav-left">
                    <a href="/" class="gt-nav-link {{ Request::is('/') ? 'active' : '' }}">Home</a>
                    <a href="/activities"
                        class="gt-nav-link {{ Request::is('activities') ? 'active' : '' }}">Activities</a>
                    <a href="/uaevisa" class="gt-nav-link {{ Request::is('uaevisa') ? 'active' : '' }}">Visa
                        Services</a>
                    <a href="/countriestour" class="gt-nav-link {{ Request::is('countriestour') ? 'active' : '' }}">Tour
                        Packages</a>
                    <a href="/hajj-umrah" class="gt-nav-link {{ Request::is('hajj-umrah') ? 'active' : '' }}">Hajj &
                        Umrah</a>
                </div>

                <!-- Center Logo -->
                <a href="/" class="gt-logo">
                    <img src="{{ asset('assets/index_files/logo.png') }}" alt="Go Trips">
                </a>

                <!-- Right Menu -->
                <div class="gt-nav-right">
                    <a href="/our-services" class="gt-nav-link {{ Request::is('our-services') ? 'active' : '' }}">Our Services</a>
                    <a href="/shopnow" class="gt-nav-link {{ Request::is('shopnow') ? 'active' : '' }}">Shop Online</a>
                    <a href="/payonline" class="gt-nav-link {{ Request::is('payonline') ? 'active' : '' }}">Pay
                        Online</a>
                    <a href="/lookingforajob"
                        class="gt-nav-link {{ Request::is('lookingforajob') ? 'active' : '' }}">Careers</a>
                    <a href="/contact-us" class="gt-nav-link {{ Request::is('contact-us') ? 'active' : '' }}">Contact
                        Us</a>
                </div>
            </div>
        </nav>

        <!-- MOBILE NAVIGATION -->
        <div class="gt-mobile-header">
            <!-- Mobile Menu Toggle -->
            <button class="gt-menu-toggle" id="mobileMenuToggle" aria-label="Toggle Menu">
                <i class="bi bi-list" id="menuIcon"></i>
            </button>

            <!-- Mobile Logo (Centered) -->
            <a href="/" class="gt-mobile-logo">
                <img src="{{ asset('assets/index_files/logo.png') }}" alt="Go Trips">
            </a>

            <!-- Empty div for flexbox spacing -->
            <div style="width: 48px;"></div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <nav class="gt-mobile-nav" id="mobileNav">
            <a href="/" class="gt-mobile-nav-link">Home</a>
            <a href="/activities" class="gt-mobile-nav-link">Activities</a>
            <a href="/uaevisa" class="gt-mobile-nav-link">Visa Services</a>
            <a href="/countriestour" class="gt-mobile-nav-link">Tour Packages</a>
            <a href="/hajj-umrah" class="gt-mobile-nav-link">Hajj & Umrah</a>
            <a href="/our-services" class="gt-mobile-nav-link">Our Services</a>
            <a href="/shopnow" class="gt-mobile-nav-link">Shop Online</a>
            <a href="/payonline" class="gt-mobile-nav-link">Pay Online</a>
            <a href="/lookingforajob" class="gt-mobile-nav-link">Careers</a>
            <a href="/contact-us" class="gt-mobile-nav-link">Contact Us</a>
        </nav>

        <!-- INLINE SEARCH BAR + PARTNER CTA (Homepage Only) -->
        @if(Request::is('/'))
            <div class="gt-inline-search-wrapper">
                <div class="gt-inline-search" id="gtInlineSearch">
                    <i class="bi bi-search gt-inline-search-icon"></i>
                    <input type="text"
                           class="gt-inline-search-input"
                           id="gtSearchInput"
                           placeholder="Search destinations, activities, services..."
                           autocomplete="off"
                           autocorrect="off"
                           spellcheck="false">
                    <div class="gt-search-loader" id="gtSearchLoader" style="display:none;">
                        <div class="gt-search-spinner"></div>
                    </div>
                    <button class="gt-inline-search-btn" id="gtSearchBtn">SEARCH</button>

                    <!-- Search Dropdown Results Panel -->
                    <div class="gt-search-dropdown" id="gtSearchDropdown">
                        <div class="gt-search-quick" id="gtSearchQuick">
                            <div class="gt-dropdown-cat-title"><i class="bi bi-lightning"></i> Popular Searches</div>
                            <div class="gt-quick-pills">
                                <span class="gt-quick-pill" data-query="Dubai">Dubai</span>
                                <span class="gt-quick-pill" data-query="Visa">UAE Visa</span>
                                <span class="gt-quick-pill" data-query="Abu Dhabi">Abu Dhabi</span>
                                <span class="gt-quick-pill" data-query="Desert Safari">Desert Safari</span>
                                <span class="gt-quick-pill" data-query="Hajj">Hajj & Umrah</span>
                                <span class="gt-quick-pill" data-query="Cruise">Cruise</span>
                            </div>
                        </div>
                        <div class="gt-search-results-list" id="gtSearchResultsList"></div>
                        <div class="gt-search-empty" id="gtSearchEmpty" style="display:none;">
                            <i class="bi bi-search"></i>
                            <p class="gt-empty-title">No results found</p>
                            <p class="gt-empty-sub">Try different keywords or browse our services</p>
                        </div>
                    </div>
                </div>
                <div class="gt-partner-cta">
                    <span class="gt-partner-label">Join as a</span>
                    <span class="gt-partner-title">Partner / Customer</span>
                    <button class="gt-partner-btn" id="partnerRegisterBtn">Register Now</button>
                </div>
            </div>
        @endif

        <!-- NEWS TICKER (Homepage Only) -->
        @if(Request::is('/'))
            <div class="news-ticker">
                <div class="scroll text-uppercase">
                    @forelse($tickerItems ?? [] as $ticker)
                        <a href="#" class="news-item">
                            @if($ticker->tagType && $ticker->tagType !== 'none')
                                <span class="{{ $ticker->tag_css_class }}">{{ $ticker->tag_label }}</span>
                            @endif
                            <span class="news-text">{{ $ticker->description }}</span>
                        </a>
                        @if(!$loop->last)
                            <span class="separator">|</span>
                        @endif
                    @empty
                        <a href="#" class="news-item">
                            <span class="news-text">Welcome to Go Trips - Your Gateway to Amazing Adventures</span>
                        </a>
                    @endforelse
                </div>
            </div>
        @endif

    </header>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menuToggle = document.getElementById('mobileMenuToggle');
            const mobileNav = document.getElementById('mobileNav');
            const menuIcon = document.getElementById('menuIcon');

            if (menuToggle && mobileNav) {
                menuToggle.addEventListener('click', function () {
                    const isOpen = mobileNav.classList.contains('active');

                    if (isOpen) {
                        mobileNav.classList.remove('active');
                        menuIcon.classList.remove('bi-x');
                        menuIcon.classList.add('bi-list');
                    } else {
                        mobileNav.classList.add('active');
                        menuIcon.classList.remove('bi-list');
                        menuIcon.classList.add('bi-x');
                    }
                });

                // Close menu when clicking a link
                document.querySelectorAll('.gt-mobile-nav-link').forEach(link => {
                    link.addEventListener('click', () => {
                        mobileNav.classList.remove('active');
                        menuIcon.classList.remove('bi-x');
                        menuIcon.classList.add('bi-list');
                    });
                });

                // Close menu on scroll
                let lastScroll = 0;
                window.addEventListener('scroll', () => {
                    const currentScroll = window.pageYOffset;
                    if (Math.abs(currentScroll - lastScroll) > 50) {
                        mobileNav.classList.remove('active');
                        menuIcon.classList.remove('bi-x');
                        menuIcon.classList.add('bi-list');
                        lastScroll = currentScroll;
                    }
                });
            }
        });
    </script>

    <!-- ==================== INLINE SEARCH ENGINE ==================== -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var searchInput = document.getElementById('gtSearchInput');
        var searchBtn = document.getElementById('gtSearchBtn');
        var dropdown = document.getElementById('gtSearchDropdown');
        var resultsList = document.getElementById('gtSearchResultsList');
        var quickSection = document.getElementById('gtSearchQuick');
        var emptyState = document.getElementById('gtSearchEmpty');
        var loader = document.getElementById('gtSearchLoader');
        var searchWrapper = document.getElementById('gtInlineSearch');

        if (!searchInput || !dropdown) return;

        var debounceTimer;
        var selectedIndex = -1;
        var currentItems = [];

        var categoryConfig = {
            countries:  { label: 'Popular Destinations', icon: 'bi-airplane',  order: 1 },
            emirates:   { label: 'UAE Emirates',         icon: 'bi-building',  order: 2 },
            activities: { label: 'Activities',           icon: 'bi-geo-alt',   order: 3 },
            services:   { label: 'Our Services',         icon: 'bi-briefcase', order: 4 },
            visas:      { label: 'Visa Services',        icon: 'bi-passport',  order: 5 },
            pages:      { label: 'Website Pages',        icon: 'bi-globe',     order: 6 }
        };

        function openDropdown() {
            dropdown.classList.add('active');
        }

        function closeDropdown() {
            dropdown.classList.remove('active');
            selectedIndex = -1;
            updateSelection();
        }

        function showQuickSuggestions() {
            quickSection.style.display = 'block';
            resultsList.innerHTML = '';
            emptyState.style.display = 'none';
            currentItems = [];
        }

        // Focus opens dropdown with quick suggestions
        searchInput.addEventListener('focus', function() {
            if (!searchInput.value.trim()) {
                showQuickSuggestions();
            }
            openDropdown();
        });

        // Click outside closes dropdown
        document.addEventListener('click', function(e) {
            if (searchWrapper && !searchWrapper.contains(e.target)) {
                closeDropdown();
            }
        });

        // Keyboard navigation
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDropdown();
                searchInput.blur();
                return;
            }

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (currentItems.length > 0) {
                    selectedIndex = (selectedIndex + 1) % currentItems.length;
                    updateSelection();
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (currentItems.length > 0) {
                    selectedIndex = (selectedIndex - 1 + currentItems.length) % currentItems.length;
                    updateSelection();
                }
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (selectedIndex >= 0 && currentItems[selectedIndex]) {
                    window.location.href = currentItems[selectedIndex].url;
                } else if (searchInput.value.trim().length >= 2) {
                    performSearch(searchInput.value.trim());
                }
            }
        });

        function updateSelection() {
            var items = dropdown.querySelectorAll('.gt-dropdown-item');
            for (var i = 0; i < items.length; i++) {
                if (i === selectedIndex) {
                    items[i].classList.add('gt-selected');
                    items[i].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                } else {
                    items[i].classList.remove('gt-selected');
                }
            }
        }

        // Debounced input handler
        searchInput.addEventListener('input', function() {
            var query = this.value.trim();
            clearTimeout(debounceTimer);
            selectedIndex = -1;
            currentItems = [];

            if (!query || query.length < 2) {
                showQuickSuggestions();
                openDropdown();
                return;
            }

            loader.style.display = 'flex';
            debounceTimer = setTimeout(function() {
                performSearch(query);
            }, 300);
        });

        // Search button click
        searchBtn.addEventListener('click', function(e) {
            e.preventDefault();
            var query = searchInput.value.trim();
            if (query.length >= 2) {
                performSearch(query);
            } else {
                searchInput.focus();
                openDropdown();
            }
        });

        // Quick pill click
        var pills = document.querySelectorAll('.gt-quick-pill');
        for (var p = 0; p < pills.length; p++) {
            pills[p].addEventListener('click', function() {
                var q = this.getAttribute('data-query');
                searchInput.value = q;
                performSearch(q);
            });
        }

        function performSearch(query) {
            loader.style.display = 'flex';
            openDropdown();

            fetch('/api/search?q=' + encodeURIComponent(query))
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    loader.style.display = 'none';
                    renderResults(data, query);
                })
                .catch(function() {
                    loader.style.display = 'none';
                    showError();
                });
        }

        function renderResults(data, query) {
            quickSection.style.display = 'none';
            resultsList.innerHTML = '';
            currentItems = [];

            if (data.total === 0) {
                emptyState.style.display = 'block';
                return;
            }

            emptyState.style.display = 'none';
            var html = '';
            var itemIndex = 0;
            var totalShown = 0;
            var maxResults = 12;

            var sortedKeys = Object.keys(categoryConfig).sort(function(a, b) {
                return categoryConfig[a].order - categoryConfig[b].order;
            });

            for (var k = 0; k < sortedKeys.length; k++) {
                if (totalShown >= maxResults) break;

                var cat = sortedKeys[k];
                var items = data[cat];
                if (!items || items.length === 0) continue;

                var config = categoryConfig[cat];
                html += '<div class="gt-dropdown-cat-title"><i class="bi ' + config.icon + '"></i> ' + config.label + '</div>';

                var limit = Math.min(items.length, maxResults - totalShown);
                for (var i = 0; i < limit; i++) {
                    var item = items[i];
                    var icon = item.icon || config.icon;
                    var highlightedTitle = highlightMatch(item.title, query);
                    var desc = item.description ? '<p class="gt-dropdown-item-desc">' + escapeHtml(item.description) + '</p>' : '';

                    html += '<a href="' + escapeHtml(item.url) + '" class="gt-dropdown-item" data-index="' + itemIndex + '">'
                        + '<div class="gt-dropdown-item-icon"><i class="bi ' + icon + '"></i></div>'
                        + '<div class="gt-dropdown-item-content">'
                        + '<div class="gt-dropdown-item-title">' + highlightedTitle + '</div>'
                        + desc
                        + '</div>'
                        + '<i class="bi bi-chevron-right gt-dropdown-item-arrow"></i>'
                        + '</a>';

                    currentItems.push({ url: item.url, title: item.title });
                    itemIndex++;
                    totalShown++;
                }
            }

            // Keyboard hint footer
            html += '<div class="gt-search-kbd-hint">'
                + '<span><kbd>&uarr;&darr;</kbd> Navigate</span>'
                + '<span><kbd>Enter</kbd> Select</span>'
                + '<span><kbd>Esc</kbd> Close</span>'
                + '</div>';

            resultsList.innerHTML = html;
        }

        function highlightMatch(text, query) {
            if (!query) return escapeHtml(text);
            var escaped = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            var regex = new RegExp('(' + escaped + ')', 'gi');
            // Escape the text first, then apply highlight
            return escapeHtml(text).replace(regex, '<span class="gt-highlight">$1</span>');
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function showError() {
            quickSection.style.display = 'none';
            resultsList.innerHTML = '';
            emptyState.style.display = 'block';
            var title = emptyState.querySelector('.gt-empty-title');
            var sub = emptyState.querySelector('.gt-empty-sub');
            if (title) title.textContent = 'Search unavailable';
            if (sub) sub.textContent = 'Please try again in a moment';
        }
    });
    </script>
</body>

</html>
