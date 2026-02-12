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

        body {
            background-color: #000;
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

        /* HOMEPAGE SEARCH BAR Styles */
        @if(Request::is('/'))
            /* Body padding to accommodate fixed header + search bar */
            body { margin-top: 185px; }
            @media (max-width: 991px) { body { margin-top: 178px; } }
            @media (max-width: 575px) { body { margin-top: 160px !important; } }

            .gt-header-search-bar {
                background: linear-gradient(180deg, rgba(10, 10, 10, 0.95) 0%, rgba(0, 0, 0, 0.98) 100%);
                border-bottom: 1px solid rgba(255, 215, 0, 0.05);
                height: 60px;
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
                z-index: 9990;
            }

            .gt-hero-search-input-wrapper {
                position: relative;
                width: 100%;
                max-width: 600px;
                margin: 0 20px;
            }

            /* Real Input Styles */
            .gt-hero-search-input {
                width: 100%;
                height: 48px; /* Slightly taller for premium feel */
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 215, 0, 0.2);
                border-radius: 50px;
                padding: 0 110px 0 45px; /* Right padding for button */
                color: #fff;
                font-family: 'Outfit', sans-serif;
                font-size: 15px;
                outline: none;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .gt-hero-search-input:focus {
                background: rgba(255, 255, 255, 0.08);
                border-color: #FFD700;
                box-shadow: 0 0 25px rgba(255, 215, 0, 0.15), inset 0 0 10px rgba(255, 215, 0, 0.05);
            }

            .gt-hero-search-icon {
                position: absolute;
                left: 18px;
                top: 50%;
                transform: translateY(-50%);
                color: #FFD700;
                font-size: 18px;
                pointer-events: none;
                transition: all 0.3s ease;
            }
            
            .gt-hero-search-input:focus + .gt-hero-search-icon {
                color: #fff;
                text-shadow: 0 0 10px #FFD700;
            }

            .gt-hero-search-btn {
                position: absolute;
                right: 5px;
                top: 5px;
                bottom: 5px;
                background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%);
                color: #000;
                border: none;
                border-radius: 40px;
                padding: 0 20px;
                font-family: 'Outfit', sans-serif;
                font-weight: 700;
                font-size: 13px;
                letter-spacing: 0.5px;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 4px 10px rgba(212, 175, 55, 0.3);
            }

            .gt-hero-search-btn:hover {
                transform: scale(1.05);
                box-shadow: 0 6px 15px rgba(212, 175, 55, 0.5);
                background: linear-gradient(135deg, #FFE600 0%, #FFC107 100%);
            }

            /* Results Dropdown */
            .gt-hero-search-results {
                position: absolute;
                top: 110%; /* Slight gap */
                left: 0;
                width: 100%;
                background: rgba(15, 15, 15, 0.95); /* Darker premium bg */
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 215, 0, 0.15);
                border-radius: 16px;
                max-height: 400px;
                overflow-y: auto;
                display: none;
                z-index: 9999;
                box-shadow: 0 20px 50px rgba(0,0,0,0.6);
                padding: 15px;
                animation: slideDownFade 0.3s ease;
            }
            
            @keyframes slideDownFade {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .gt-hero-search-results.active {
                display: block;
            }

            /* Reuse modal result styles */
            .gt-search-category-title {
                font-size: 11px;
                font-weight: 700;
                letter-spacing: 1px;
                text-transform: uppercase;
                color: rgba(255, 215, 0, 0.6);
                margin: 15px 0 8px 10px;
            }
            .gt-search-category-title:first-child { margin-top: 0; }
            
            .gt-search-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 12px;
                border-radius: 10px;
                text-decoration: none;
                color: #e0e0e0;
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                border: 1px solid transparent;
            }

            .gt-search-item:hover {
                background: rgba(255, 255, 255, 0.05);
                border-color: rgba(255, 215, 0, 0.1);
                transform: translateX(5px);
            }

            .gt-search-item-icon {
                width: 32px;
                height: 32px;
                background: rgba(255, 215, 0, 0.08);
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #FFD700;
                font-size: 15px;
                flex-shrink: 0;
            }

            .gt-search-item-title { font-size: 15px; font-weight: 500; color: #fff; }
            .gt-search-item-desc { font-size: 12px; color: rgba(255,255,255,0.4); margin: 0; }

            /* MOBILE OPTIMIZATIONS */
            @media (max-width: 575px) {
                .gt-header-search-bar {
                    height: 52px;
                    background: rgba(10, 10, 10, 0.98);
                    border-bottom: 1px solid rgba(255, 215, 0, 0.1);
                }
                .gt-hero-search-input-wrapper {
                    margin: 0 10px;
                }
                .gt-hero-search-input {
                    height: 40px;
                    padding: 0 55px 0 38px; /* Room for icon on right */
                    font-size: 14px;
                    background: rgba(255, 255, 255, 0.08);
                }
                .gt-hero-search-btn {
                    width: 34px;
                    height: 34px;
                    padding: 0;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    right: 4px;
                    top: 3px;
                    font-size: 16px;
                    border-radius: 50%; /* Circular button on mobile */
                }
                .gt-hero-search-icon {
                    left: 12px;
                    font-size: 14px;
                    opacity: 0.7;
                }
                .gt-hero-search-results {
                    width: calc(100vw - 20px);
                    left: -10px; /* Center it back since wrapper has margin */
                    top: 120%;
                    border-radius: 12px;
                    padding: 10px;
                    box-shadow: 0 15px 40px rgba(0,0,0,0.8);
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

        <!-- SEARCH BAR (Homepage Only) -->
        @if(Request::is('/'))
        <div class="gt-header-search-bar">
            <div class="gt-hero-search-input-wrapper">
                <input type="text" class="gt-hero-search-input" id="heroSearchInput" placeholder="Search destinations...">
                <i class="bi bi-search gt-hero-search-icon"></i>
                
                <!-- Search Button -->
                <button class="gt-hero-search-btn" id="heroSearchBtn">
                    <span class="d-none d-sm-inline">SEARCH</span>
                    <i class="bi bi-search d-inline d-sm-none"></i>
                </button>

                <!-- Results Dropdown -->
                <div id="heroSearchResults" class="gt-hero-search-results"></div>
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

    @if(Request::is('/'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('heroSearchInput');
            const results = document.getElementById('heroSearchResults');
            const searchBtn = document.getElementById('heroSearchBtn');
            let debounceTimer;

            if (input) {
                // Typewriter effect
                const placeholders = [
                    'Search "Dubai Safari"...',
                    'Try "UAE Visa Services"...',
                    'Search "Luxury Hotels"...',
                    'Try "Egypt Tour Packages"...'
                ];
                let currentIdx = 0;
                let charIdx = 0;
                let isDeleting = false;
                let typeSpeed = 100;

                function type() {
                    const currentTip = placeholders[currentIdx];
                    if (isDeleting) {
                        input.setAttribute('placeholder', currentTip.substring(0, charIdx--));
                        typeSpeed = 50;
                    } else {
                        input.setAttribute('placeholder', currentTip.substring(0, charIdx++));
                        typeSpeed = 100;
                    }

                    if (!isDeleting && charIdx === currentTip.length + 1) {
                        isDeleting = true;
                        typeSpeed = 2000; // Pause at end
                    } else if (isDeleting && charIdx === 0) {
                        isDeleting = false;
                        currentIdx = (currentIdx + 1) % placeholders.length;
                        typeSpeed = 500;
                    }

                    if (document.activeElement !== input) {
                        setTimeout(type, typeSpeed);
                    }
                }
                type();

                // Stop animation on focus
                input.addEventListener('focus', () => {
                    input.setAttribute('placeholder', 'Type to search...');
                });

                // Search Logic
                input.addEventListener('input', function() {
                    const query = this.value.trim();
                    clearTimeout(debounceTimer);

                    if (query.length < 2) {
                        results.classList.remove('active');
                        results.innerHTML = '';
                        return;
                    }

                    debounceTimer = setTimeout(() => {
                        performSearch(query);
                    }, 300);
                });

                // Search Button Click
                if(searchBtn) {
                    searchBtn.addEventListener('click', function() {
                        const query = input.value.trim();
                        if(query.length >= 2) {
                            performSearch(query);
                        } else {
                            input.focus();
                        }
                    });
                }

                // Close on click outside
                document.addEventListener('click', function(e) {
                    if (input && !input.contains(e.target) && results && !results.contains(e.target) && e.target !== searchBtn) {
                        results.classList.remove('active');
                    }
                });
            }

            function performSearch(query) {
                fetch(`/api/search?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        if(data.total > 0) {
                            let html = '';
                            if (data.countries?.length) html += renderGroup('Destinations', data.countries, 'globe-americas');
                            if (data.emirates?.length) html += renderGroup('Emirates', data.emirates, 'geo-alt');
                            if (data.activities?.length) html += renderGroup('Activities', data.activities, 'ticket-perforated');
                            if (data.visas?.length) html += renderGroup('Visas', data.visas, 'passport');
                            
                            results.innerHTML = html;
                            results.classList.add('active');
                        } else {
                            results.innerHTML = '<div style="padding:15px; text-align:center; color:rgba(255,255,255,0.5); font-size:13px;">No results found</div>';
                            results.classList.add('active');
                        }
                    });
            }

            function renderGroup(title, items, icon) {
                let chunk = `<div class="gt-search-category-title">${title}</div>`;
                items.forEach(item => {
                    chunk += `
                        <a href="${item.url}" class="gt-search-item">
                            <div class="gt-search-item-icon"><i class="bi bi-${icon}"></i></div>
                            <div class="gt-search-item-content">
                                <div class="gt-search-item-title">${item.title}</div>
                                ${item.description ? `<p class="gt-search-item-desc">${item.description}</p>` : ''}
                            </div>
                        </a>
                    `;
                });
                return chunk;
            }
        });
    </script>
    @endif
</body>

</html>
