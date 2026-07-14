<!DOCTYPE html>
<html lang="en">

<head>
    {{-- Apply saved theme before first paint to avoid a flash of the wrong theme. Default is dark. --}}
    <script>(function(){try{var t=localStorage.getItem('gt-theme');if(t==='light'){document.documentElement.setAttribute('data-theme','light');}}catch(e){}})();</script>
    @php
        $tenantLogo = (isset($company) && $company && $company->logo) ? asset('storage/' . $company->logo) : asset('assets/index_files/logo.png');
        $tenantName = (isset($company) && $company && $company->name) ? $company->name : 'Go Trips';
        $tenantTagline = (isset($company) && $company && $company->getSetting('tagline')) ? $company->getSetting('tagline') : 'Your Gateway to Amazing Adventures';
    @endphp
    <link rel="icon" type="image/png" href="{{ $tenantLogo }}">

    <meta charset="UTF-8">
    <title>{{ $tenantName }}</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="ThemeZaa">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="description" content="{{ $tenantName }} - {{ $tenantTagline }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="{{ $tenantName }}">
    <meta property="og:description" content="{{ $tenantName }} - {{ $tenantTagline }}">
    <meta property="og:image" content="{{ $tenantLogo }}">
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
            background-color: #000 !important;
            color: #fff;
            outline: none !important;
        }

        html {
            background-color: #000 !important;
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
            min-height: 86px;
            padding: 8px 36px;
            max-width: 1640px;
            margin: 0 auto;
        }

        /* TWO-ROW SIDES (primary row on top, secondary row beneath) */
        .gt-nav-side {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 7px;
            min-width: 0;
        }
        .gt-nav-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px 16px;
            white-space: nowrap;
        }

        /* CENTER LOGO */
        .gt-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 48px;
            flex-shrink: 0;
        }

        .gt-logo img {
            height: 64px;
            width: auto;
            transition: transform 0.3s ease, filter 0.3s ease;
        }

        .gt-logo:hover img {
            transform: scale(1.05);
            filter: drop-shadow(0 0 15px rgba(255, 215, 0, 0.4));
        }

        /* Primary row = white bold; Secondary row = gold, smaller */
        .gt-nav-primary .gt-nav-link { color: #FFFFFF; }
        .gt-nav-sublink {
            font-family: 'Outfit', sans-serif;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #FFD700;
            text-decoration: none;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            transition: color 0.2s ease, text-shadow 0.2s ease;
        }
        .gt-nav-sublink:hover,
        .gt-nav-sublink.active { color: #fff; text-shadow: 0 0 10px rgba(255, 215, 0, 0.6); }

        /* Flashing ⚡ icon (manager-controlled "hot this month") */
        .gt-flash {
            color: #ff2d2d;
            font-size: 1em;
            margin-left: 5px;
            vertical-align: -1px;
            animation: gtFlash 0.9s ease-in-out infinite;
        }
        @keyframes gtFlash {
            0%, 100% { opacity: 1; filter: drop-shadow(0 0 5px rgba(255, 45, 45, 0.9)); transform: scale(1); }
            50%      { opacity: 0.4; filter: drop-shadow(0 0 1px rgba(255, 45, 45, 0.2)); transform: scale(0.9); }
        }
        @media (prefers-reduced-motion: reduce) { .gt-flash { animation: none; } }

        /* Golden "highlighted button" treatment for flagged items. Triggered by
           the presence of the flash icon (only injected when the manager flags
           the item), so no per-item markup is needed. Old browsers without
           :has() simply keep the red flashing bolt — graceful fallback. */
        .gt-nav-link:has(.gt-flash),
        .gt-nav-sublink:has(.gt-flash),
        .gt-macc-item:has(.gt-flash) {
            background: linear-gradient(135deg, #FFD700 0%, #FFB200 100%);
            color: #1a1a1a !important;
            border-radius: 999px;
            box-shadow: 0 0 0 0 rgba(255, 200, 0, 0.55);
            /* The WHOLE gold button blinks (opacity + glow), not just the bolt. */
            animation: gtFlashedPulse 1s ease-in-out infinite;
        }
        .gt-nav-link:has(.gt-flash) { padding: 5px 14px; }
        .gt-nav-sublink:has(.gt-flash) { padding: 3px 12px; }
        /* Dark bolt reads cleanly on the gold pill — and blinks in unison with
           the pill (its own icon animation is disabled so the whole thing
           flashes as a single unit). */
        .gt-nav-link:has(.gt-flash) .gt-flash,
        .gt-nav-sublink:has(.gt-flash) .gt-flash,
        .gt-macc-item:has(.gt-flash) .gt-flash { color: #1a1a1a; animation: none; }
        .gt-nav-link:has(.gt-flash):hover,
        .gt-nav-sublink:has(.gt-flash):hover { color: #000 !important; text-shadow: none; }
        /* Mobile: gold pill sits inline within the row */
        .gt-macc-item:has(.gt-flash) {
            align-self: flex-start; margin: 8px 0 8px 46px; padding: 8px 16px;
            width: auto; border-bottom: none; font-weight: 700;
        }
        @keyframes gtFlashedPulse {
            0%, 100% { opacity: 1;    box-shadow: 0 0 0 0 rgba(255, 200, 0, 0.55); }
            50%      { opacity: 0.3;  box-shadow: 0 0 16px 3px rgba(255, 200, 0, 0.35); }
        }
        @media (prefers-reduced-motion: reduce) {
            .gt-nav-link:has(.gt-flash),
            .gt-nav-sublink:has(.gt-flash),
            .gt-macc-item:has(.gt-flash) { animation: none; }
        }

        /* NAV LINKS - Premium Styling */
        .gt-nav-link {
            font-family: 'Outfit', sans-serif;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #FFD700;
            text-decoration: none;
            padding: 5px 8px;
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

        /* Events tab + seasonal "Trending" badge */
        .gt-nav-events { position: relative; }
        .gt-trending-badge {
            display: inline-block;
            margin-left: 6px;
            padding: 2px 7px;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #1a1a1a;
            background: linear-gradient(135deg, #FFD700 0%, #FF8A00 100%);
            border-radius: 999px;
            vertical-align: middle;
            line-height: 1.4;
            box-shadow: 0 0 0 0 rgba(255, 170, 0, 0.6);
            animation: gtTrendPulse 1.8s ease-in-out infinite;
        }
        @keyframes gtTrendPulse {
            0%   { box-shadow: 0 0 0 0 rgba(255, 170, 0, 0.55); transform: translateY(-0.5px) scale(1); }
            70%  { box-shadow: 0 0 0 7px rgba(255, 170, 0, 0); transform: translateY(-0.5px) scale(1.04); }
            100% { box-shadow: 0 0 0 0 rgba(255, 170, 0, 0); transform: translateY(-0.5px) scale(1); }
        }
        @media (prefers-reduced-motion: reduce) {
            .gt-trending-badge { animation: none; }
        }

        /* ===== Desktop dropdown menus ===== */
        .gt-dropdown { position: relative; display: inline-flex; }
        .gt-dd-toggle {
            background: transparent; border: none; cursor: pointer;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .gt-dd-toggle i { font-size: 0.62em; transition: transform 0.2s ease; opacity: 0.8; }
        .gt-dropdown:hover .gt-dd-toggle i,
        .gt-dropdown:focus-within .gt-dd-toggle i { transform: rotate(180deg); }
        .gt-dd-menu {
            position: absolute; top: 100%; left: 50%;
            transform: translateX(-50%) translateY(10px);
            min-width: 220px; padding: 8px;
            background: #0b0b0b; border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 14px; box-shadow: 0 24px 50px rgba(0, 0, 0, 0.55);
            opacity: 0; visibility: hidden; pointer-events: none;
            transition: opacity 0.18s ease, transform 0.18s ease;
            z-index: 10002;
        }
        .gt-dropdown:hover .gt-dd-menu,
        .gt-dropdown:focus-within .gt-dd-menu {
            opacity: 1; visibility: visible; pointer-events: auto;
            transform: translateX(-50%) translateY(0);
        }
        .gt-dd-item {
            display: flex; align-items: center; gap: 8px;
            padding: 10px 14px; border-radius: 9px;
            font-family: 'Outfit', sans-serif; font-size: 13px; font-weight: 600;
            color: #e8e8e8; text-decoration: none; white-space: nowrap;
            text-transform: none; letter-spacing: normal;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .gt-dd-item:hover { background: rgba(255, 215, 0, 0.1); color: #FFD700; }
        .gt-dd-sub { font-size: 11px; font-weight: 400; color: #8a8a8a; }


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
            max-width: 420px;
            height: 42px;
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
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 400;
            color: #FFFFFF;
            padding: 0 12px;
            height: 100%;
            caret-color: #FFD700;
            min-width: 0;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
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

        /* ESIM SELLER CTA - Mirror of partner-cta on the LEFT side */
        .gt-esim-cta {
            position: absolute;
            left: 40px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .gt-esim-label {
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 400;
            font-style: italic;
            color: #ff8a8a;
            white-space: nowrap;
        }
        .gt-esim-title {
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 700;
            color: #ffffff;
            white-space: nowrap;
        }
        .gt-esim-btn {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            color: #ffffff !important;
            border: none;
            border-radius: 50px;
            padding: 0 24px;
            height: 34px;
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
            display: inline-flex;
            align-items: center;
            text-decoration: none !important;
        }
        .gt-esim-btn:hover {
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(239, 68, 68, 0.4);
            color: #fff !important;
            text-decoration: none !important;
        }
        @media (max-width: 1200px) {
            .gt-esim-label,
            .gt-esim-title { display: none; }
        }
        @media (min-width: 1201px) {
            .gt-esim-cta {
                border-right: 1px solid rgba(255, 215, 0, 0.12);
                padding-right: 12px;
            }
        }
        @media (max-width: 991px) {
            .gt-esim-btn {
                padding: 0 18px;
                font-size: 11px;
                height: 30px;
                letter-spacing: 1.2px;
            }
            .gt-esim-cta {
                position: static;
                transform: none;
                justify-content: center;
                width: 100%;
                margin-top: 8px;
                padding-top: 8px;
                border-right: none;
                padding-right: 0;
                border-top: 1px solid rgba(255, 215, 0, 0.1);
            }
            .gt-esim-label,
            .gt-esim-title { display: inline; }
        }

        .gt-partner-label {
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 400;
            font-style: italic;
            color: #FFD23F;
            white-space: nowrap;
        }

        .gt-partner-title {
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 700;
            color: #ffffff;
            white-space: nowrap;
        }

        .gt-partner-btn {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            color: #ffffff;
            border: none;
            border-radius: 50px;
            padding: 0 28px;
            height: 38px;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(22, 163, 74, 0.25);
        }

        .gt-partner-btn:hover {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35);
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

            .gt-partner-btn {
                padding: 0 18px;
                font-size: 11px;
                height: 30px;
                letter-spacing: 1.2px;
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
                height: 30px;
                font-size: 10px;
                letter-spacing: 1.2px;
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
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
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

        /* Mobile accordion sub-menus */
        .gt-macc-toggle {
            width: 100%; background: transparent; border: none; cursor: pointer; text-align: left;
            display: flex; align-items: center; justify-content: space-between;
        }
        .gt-macc-toggle i { font-size: 0.7em; opacity: 0.7; transition: transform 0.25s ease; }
        .gt-macc.open .gt-macc-toggle i { transform: rotate(180deg); }
        .gt-macc-panel { max-height: 0; overflow: hidden; transition: max-height 0.28s ease; background: rgba(0, 0, 0, 0.25); }
        .gt-macc.open .gt-macc-panel { max-height: 520px; }
        .gt-macc-item {
            display: flex; align-items: center; gap: 8px;
            padding: 13px 30px 13px 46px;
            font-family: 'Outfit', sans-serif; font-size: 14px; font-weight: 500; letter-spacing: 0.5px;
            color: #cfcfcf; text-decoration: none; border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        }
        .gt-macc-item:hover { color: #FFD700; background: rgba(255, 215, 0, 0.07); }

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

        /* RESPONSIVE — tighten the two-row nav so it never overflows on smaller
           laptops. Tiers shrink font/gap as the viewport narrows; below 992px it
           collapses to the mobile menu. */
        /* Both rows always share the same font size; they shrink together so the
           longer second row keeps fitting, then drop to the mobile menu <=1200. */
        @media (max-width: 1600px) {
            .gt-nav-wrapper { padding: 8px 24px; }
            .gt-nav-row { gap: 4px 9px; }
            .gt-nav-link { font-size: 11px; padding: 4px 7px; letter-spacing: 0.3px; }
            .gt-nav-sublink { font-size: 11px; letter-spacing: 0.3px; }
            .gt-logo { padding: 0 30px; }
            .gt-logo img { height: 54px; }
        }

        @media (max-width: 1460px) {
            .gt-nav-wrapper { padding: 8px 18px; }
            .gt-nav-row { gap: 3px 7px; }
            .gt-nav-link { font-size: 10px; padding: 4px 6px; letter-spacing: 0.2px; }
            .gt-nav-sublink { font-size: 10px; letter-spacing: 0.2px; }
            .gt-logo { padding: 0 22px; }
            .gt-logo img { height: 48px; }
            .gt-flash { margin-left: 3px; }
        }

        @media (max-width: 1340px) {
            .gt-nav-wrapper { padding: 8px 12px; }
            .gt-nav-row { gap: 2px 5px; }
            .gt-nav-link { font-size: 9px; padding: 3px 5px; }
            .gt-nav-sublink { font-size: 9px; }
            .gt-logo { padding: 0 14px; }
            .gt-logo img { height: 44px; }
        }

        @media (max-width: 1200px) {
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

        /* Header spacing - clear the fixed header so page content is never
           hidden beneath it. Values match the measured header height per
           breakpoint (header is ~172px on desktop) plus a 2px safety gap. */
        body {
            margin-top: 174px;
        }

        /* Homepage has ticker, needs extra space */
        body.has-ticker {
            margin-top: 201px;
        }

        @media (max-width: 1200px) {
            body {
                margin-top: 150px;
            }
            body.has-ticker {
                margin-top: 185px;
            }
        }

        @media (max-width: 991px) {
            body {
                margin-top: 150px;
            }
            body.has-ticker {
                margin-top: 230px; 
            }
            .gt-partner-label, .gt-partner-title {
                display: inline;
            }
        }

        @media (max-width: 575px) {
            body {
                margin-top: 140px;
            }
            body.has-ticker {
                margin-top: 240px;
            }
        }

        /* ---- Theme toggle button (base styling; works in dark, light overrides in gt-theme.css) ---- */
        .gt-theme-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            padding: 0;
            border-radius: 50%;
            border: 1px solid rgba(255, 215, 0, 0.35);
            background: rgba(255, 215, 0, 0.06);
            color: #FFD700;
            font-size: 1rem;
            cursor: pointer;
            transition: background .2s ease, color .2s ease, transform .2s ease, border-color .2s ease;
            flex-shrink: 0;
        }
        .gt-theme-toggle:hover { transform: translateY(-1px); background: rgba(255, 215, 0, 0.16); }
        /* Show only the icon for the theme you can switch TO. Default (dark active) → show sun. */
        .gt-theme-toggle .bi-sun-fill { display: inline; }
        .gt-theme-toggle .bi-moon-stars-fill { display: none; }
        html[data-theme="light"] .gt-theme-toggle .bi-sun-fill { display: none; }
        html[data-theme="light"] .gt-theme-toggle .bi-moon-stars-fill { display: inline; }
        .gt-theme-toggle-mobile { margin-right: 6px; }

        /* The fixed header sits at z-index 9999. Bootstrap modals default to
           ~1055, so the header would paint OVER an open modal and clip its top
           (e.g. the FIFA "Request Tickets" modal). Lift modals above the header. */
        .modal { z-index: 10060 !important; }
        .modal-backdrop { z-index: 10050 !important; }

    </style>

    {{-- Light theme overrides (activated by <html data-theme="light">). Loaded last so it wins over template + page styles. --}}
    <link rel="stylesheet" href="{{ asset('css/gt-theme.css') }}?v={{ @filemtime(public_path('css/gt-theme.css')) ?: time() }}">
</head>

<body class="{{ request()->is('/') ? 'has-ticker' : '' }}">
@include('partials.intl-tel-init')
@php
    // --- Menu helpers ---
    // Per-item flashy "Hot" badge, driven by the manager Menu Highlights toggles.
    // Precomputed strings because Blade won't parse @if stuck to a word (Label@if..).
    $gtCo = current_company();
    $gtFlash = ($gtCo ? $gtCo->getSetting('menu_flash', []) : []) ?: [];
    // Flashing ⚡ icon shown next to items the manager flagged as "hot this month".
    $gtBadge = fn ($key) => !empty($gtFlash[$key])
        ? ' <i class="bi bi-lightning-charge-fill gt-flash" aria-hidden="true"></i>' : '';

    // Offerings without a dedicated page (Insurance, Transport, MICE, Holiday
    // Homes, Local/Festival/Medical Tours, Hotels) send the visitor to the
    // Contact page so they know to enquire. The service name rides along as a
    // query param for context.
    $gtEnquire = fn ($service) => url('/contact-us') . '?enquiry=' . urlencode($service);

    // Newly-introduced services (after e-Visa) that don't have a full page yet
    // point to a "Coming Soon" placeholder page with a related picture.
    $gtSoon = fn ($slug) => url('/coming-soon/' . $slug);
@endphp
    <!-- ==================== PREMIUM HEADER ==================== -->
    <header class="gt-header">

        <!-- DESKTOP NAVIGATION (two-row: primary + secondary, flanking the logo) -->
        <nav class="gt-desktop-nav">
            <div class="gt-nav-wrapper">

                <!-- LEFT SIDE (two rows) -->
                <div class="gt-nav-side gt-nav-side-left">
                    <div class="gt-nav-row gt-nav-primary">
                        <a href="/" class="gt-nav-link {{ Request::is('/') ? 'active' : '' }}">Home</a>
                        @feature('activities')<a href="/activities" class="gt-nav-link {{ Request::is('activities') ? 'active' : '' }}">Activities{!! $gtBadge('activities') !!}</a>@endfeature
                        @feature('visas')<a href="/uaevisa" class="gt-nav-link {{ Request::is('uaevisa') ? 'active' : '' }}">UAE Visa Services{!! $gtBadge('visa_services') !!}</a>@endfeature
                        @feature('tours')<a href="/tour-packages" class="gt-nav-link {{ Request::is('tour-packages') ? 'active' : '' }}">Tour Packages{!! $gtBadge('tour_packages') !!}</a>@endfeature
                        @feature('hajj_umrah')<a href="/umrah-visas" class="gt-nav-link {{ Request::is('umrah-visas') ? 'active' : '' }}">Umrah &amp; Saudi Visas{!! $gtBadge('hajj_umrah') !!}</a>@endfeature
                    </div>
                    <div class="gt-nav-row gt-nav-secondary">
                        @feature('esim')<a href="/esim" class="gt-nav-sublink {{ Request::is('esim') ? 'active' : '' }}">eSIM{!! $gtBadge('esim') !!}</a>@endfeature
                        @feature('visas')<a href="/e-visa" class="gt-nav-sublink {{ Request::is('e-visa') || Request::is('uae-evisa') ? 'active' : '' }}">e-Visa (80+ Countries){!! $gtBadge('evisa') !!}</a>@endfeature
                        <a href="{{ $gtSoon('insurance') }}" class="gt-nav-sublink">Insurance{!! $gtBadge('insurance') !!}</a>
                        @platformOnly<a href="{{ $gtSoon('cruise') }}" class="gt-nav-sublink">Cruise{!! $gtBadge('cruises') !!}</a>@endplatformOnly
                        <a href="{{ $gtSoon('transport') }}" class="gt-nav-sublink">Transport{!! $gtBadge('transport') !!}</a>
                        <a href="{{ $gtSoon('holiday-homes') }}" class="gt-nav-sublink">Holiday Homes{!! $gtBadge('holiday_homes') !!}</a>
                    </div>
                </div>

                <!-- CENTER LOGO -->
                <a href="/" class="gt-logo">
                    <img src="{{ $tenantLogo }}" alt="{{ $tenantName }}">
                </a>

                <!-- RIGHT SIDE (two rows) -->
                <div class="gt-nav-side gt-nav-side-right">
                    <div class="gt-nav-row gt-nav-primary">
                        <a href="/our-services" class="gt-nav-link {{ Request::is('our-services') ? 'active' : '' }}">Our Services</a>
                        @platformOnly<a href="/fifa-world-cup-2026" class="gt-nav-link {{ Request::is('fifa-world-cup-2026') ? 'active' : '' }}">FIFA WC 2026 <i class="bi bi-lightning-charge-fill gt-flash" aria-hidden="true"></i></a>@endplatformOnly
                        @feature('shop')<a href="/shopnow" class="gt-nav-link {{ Request::is('shopnow') ? 'active' : '' }}">Shop Online</a>@endfeature
                        @feature('pay_online')<a href="/payonline" class="gt-nav-link {{ Request::is('payonline') ? 'active' : '' }}">Pay Online</a>@endfeature
                        @feature('careers')<a href="/lookingforajob" class="gt-nav-link {{ Request::is('lookingforajob') ? 'active' : '' }}">Careers</a>@endfeature
                        <a href="/contact-us" class="gt-nav-link {{ Request::is('contact-us') ? 'active' : '' }}">Contact Us</a>
                        <button type="button" class="gt-theme-toggle" id="gtThemeToggle" aria-label="Toggle light / dark mode" title="Toggle light / dark mode">
                            <i class="bi bi-sun-fill"></i><i class="bi bi-moon-stars-fill"></i>
                        </button>
                    </div>
                    <div class="gt-nav-row gt-nav-secondary">
                        <a href="{{ $gtSoon('business-tourism') }}" class="gt-nav-sublink">Business Tourism (MICE){!! $gtBadge('mice') !!}</a>
                        @platformOnly<a href="{{ $gtSoon('events') }}" class="gt-nav-sublink">Events{!! $gtBadge('events') !!}</a>@endplatformOnly
                        <a href="{{ $gtSoon('local-tours') }}" class="gt-nav-sublink">Local Tours{!! $gtBadge('local_tours') !!}</a>
                        <a href="{{ $gtSoon('festival-tours') }}" class="gt-nav-sublink">Festival Tours{!! $gtBadge('festival_tours') !!}</a>
                        <a href="{{ $gtSoon('medical-tours') }}" class="gt-nav-sublink">Medical Tours{!! $gtBadge('medical_tours') !!}</a>
                    </div>
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
                <img src="{{ $tenantLogo }}" alt="{{ $tenantName }}">
            </a>

            <!-- Theme toggle (also balances the flex layout opposite the menu button) -->
            <button type="button" class="gt-theme-toggle gt-theme-toggle-mobile" id="gtThemeToggleMobile" aria-label="Toggle light / dark mode" title="Toggle light / dark mode">
                <i class="bi bi-sun-fill"></i><i class="bi bi-moon-stars-fill"></i>
            </button>
        </div>

        <!-- Mobile Menu Dropdown -->
        <nav class="gt-mobile-nav" id="mobileNav">
            <a href="/" class="gt-mobile-nav-link">Home</a>

            {{-- Visas --}}
            <div class="gt-macc">
                <button type="button" class="gt-mobile-nav-link gt-macc-toggle">Visas <i class="bi bi-chevron-down"></i></button>
                <div class="gt-macc-panel">
                    @feature('visas')
                    <a href="/uaevisa" class="gt-macc-item">UAE Visa Services</a>
                    <a href="/e-visa" class="gt-macc-item">e-Visa (80+ Countries){!! $gtBadge('evisa') !!}</a>
                    @endfeature
                    <a href="{{ $gtSoon('insurance') }}" class="gt-macc-item">Insurance{!! $gtBadge('insurance') !!}</a>
                </div>
            </div>

            {{-- Tours --}}
            <div class="gt-macc">
                <button type="button" class="gt-mobile-nav-link gt-macc-toggle">Tours <i class="bi bi-chevron-down"></i></button>
                <div class="gt-macc-panel">
                    @feature('tours')<a href="/tour-packages" class="gt-macc-item">Tour Packages</a>@endfeature
                    <a href="{{ $gtSoon('local-tours') }}" class="gt-macc-item">Local Tours{!! $gtBadge('local_tours') !!}</a>
                    <a href="{{ $gtSoon('festival-tours') }}" class="gt-macc-item">Festival Tours{!! $gtBadge('festival_tours') !!}</a>
                    <a href="{{ $gtSoon('medical-tours') }}" class="gt-macc-item">Medical Tours{!! $gtBadge('medical_tours') !!}</a>
                    @feature('activities')<a href="/activities" class="gt-macc-item">Activities</a>@endfeature
                    @feature('hajj_umrah')<a href="/umrah-visas" class="gt-macc-item">Umrah &amp; Saudi Visas</a>@endfeature
                </div>
            </div>

            {{-- Stays --}}
            <div class="gt-macc">
                <button type="button" class="gt-mobile-nav-link gt-macc-toggle">Stays <i class="bi bi-chevron-down"></i></button>
                <div class="gt-macc-panel">
                    <a href="{{ $gtSoon('holiday-homes') }}" class="gt-macc-item">Holiday Homes{!! $gtBadge('holiday_homes') !!}</a>
                    @platformOnly<a href="{{ $gtSoon('cruise') }}" class="gt-macc-item">Cruise{!! $gtBadge('cruises') !!}</a>@endplatformOnly
                    <a href="{{ $gtSoon('transport') }}" class="gt-macc-item">Transport{!! $gtBadge('transport') !!}</a>
                </div>
            </div>

            @platformOnly<a href="{{ $gtSoon('events') }}" class="gt-mobile-nav-link gt-nav-events">Events{!! $gtBadge('events') !!}</a>@endplatformOnly

            @platformOnly<a href="/fifa-world-cup-2026" class="gt-mobile-nav-link">FIFA WC 2026 <i class="bi bi-lightning-charge-fill gt-flash" aria-hidden="true"></i></a>@endplatformOnly

            {{-- More --}}
            <div class="gt-macc">
                <button type="button" class="gt-mobile-nav-link gt-macc-toggle">More <i class="bi bi-chevron-down"></i></button>
                <div class="gt-macc-panel">
                    @feature('esim')<a href="/esim" class="gt-macc-item">eSIM{!! $gtBadge('esim') !!}</a>@endfeature
                    <a href="/our-services" class="gt-macc-item">Our Services</a>
                    <a href="{{ $gtSoon('business-tourism') }}" class="gt-macc-item">Business Tourism (MICE){!! $gtBadge('mice') !!}</a>
                    @feature('shop')<a href="/shopnow" class="gt-macc-item">Shop Online</a>@endfeature
                    @feature('pay_online')<a href="/payonline" class="gt-macc-item">Pay Online</a>@endfeature
                    @feature('careers')<a href="/lookingforajob" class="gt-macc-item">Careers</a>@endfeature
                </div>
            </div>

            <a href="/contact-us" class="gt-mobile-nav-link">Contact Us</a>
        </nav>

        <!-- INLINE SEARCH BAR + PARTNER CTA -->
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
                            <span class="gt-quick-pill" data-query="Visa">UAE Visa Services</span>
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
            @if(request()->is('/'))
                @platformOnly
                <div class="gt-esim-cta">
                    <span class="gt-esim-label">Sell our eSIM</span>
                    <span class="gt-esim-title">Become an eSIM Seller</span>
                    <a href="{{ route('freelancer.register') }}" class="gt-esim-btn">Register Now</a>
                </div>
                <div class="gt-partner-cta">
                    <span class="gt-partner-label">Join as a</span>
                    <span class="gt-partner-title">Partner / Customer</span>
                    <button class="gt-partner-btn" id="partnerRegisterBtn">Register Now</button>
                </div>
                @endplatformOnly
            @endif
        </div>

        @if(request()->is('/'))
        <!-- NEWS TICKER - Continuous loop (duplicated content for seamless scroll) -->
        <div class="news-ticker">
            <div class="scroll text-uppercase">
                {{-- First copy --}}
                @forelse($tickerItems ?? [] as $ticker)
                    <a href="#" class="news-item">
                        @if($ticker->tagType && $ticker->tagType !== 'none')
                            <span class="{{ $ticker->tag_css_class }}">{{ $ticker->tag_label }}</span>
                        @endif
                        <span class="news-text">{{ $ticker->description }}</span>
                    </a>
                    <span class="separator">|</span>
                @empty
                    <a href="#" class="news-item">
                        <span class="news-text">Welcome to {{ $tenantName }} - {{ $tenantTagline }}</span>
                    </a>
                    <span class="separator">|</span>
                @endforelse
                {{-- Second copy (identical for seamless loop) --}}
                @forelse($tickerItems ?? [] as $ticker)
                    <a href="#" class="news-item">
                        @if($ticker->tagType && $ticker->tagType !== 'none')
                            <span class="{{ $ticker->tag_css_class }}">{{ $ticker->tag_label }}</span>
                        @endif
                        <span class="news-text">{{ $ticker->description }}</span>
                    </a>
                    <span class="separator">|</span>
                @empty
                    <a href="#" class="news-item">
                        <span class="news-text">Welcome to {{ $tenantName }} - {{ $tenantTagline }}</span>
                    </a>
                    <span class="separator">|</span>
                @endforelse
            </div>
        </div>
        @endif

    </header>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ---- Light / dark theme toggle (no reload; persisted in localStorage; default dark) ----
        (function () {
            function apply(theme) {
                if (theme === 'light') document.documentElement.setAttribute('data-theme', 'light');
                else document.documentElement.removeAttribute('data-theme');
                try { localStorage.setItem('gt-theme', theme); } catch (e) {}
            }
            function toggle() {
                var isLight = document.documentElement.getAttribute('data-theme') === 'light';
                apply(isLight ? 'dark' : 'light');
            }
            ['gtThemeToggle', 'gtThemeToggleMobile'].forEach(function (id) {
                var btn = document.getElementById(id);
                if (btn) btn.addEventListener('click', toggle);
            });
        })();
    </script>

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

                // Accordion sub-menus: expand/collapse (don't close the menu)
                document.querySelectorAll('.gt-macc-toggle').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        btn.closest('.gt-macc').classList.toggle('open');
                    });
                });

                // Close menu when clicking an actual link (not the accordion toggles)
                document.querySelectorAll('a.gt-mobile-nav-link, .gt-macc-item').forEach(link => {
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
