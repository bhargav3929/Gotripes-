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
    <meta property="og:title" content="Go Trips">
    <meta property="og:description" content="Go Trips - Your Gateway to Amazing Adventures">
    <meta property="og:image" content="{{ asset('assets/index_files/transparent_logo.png') }}">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1024">
    <meta property="og:image:height" content="1024">
    <link rel="stylesheet" href="{{ asset('assets/index_files/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com/" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link rel="stylesheet" href="{{ asset('assets/index_files/vendors.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/index_files/icon.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/index_files/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/index_files/responsive.css') }}">

    <style>
        @import url("https://fonts.cdnfonts.com/css/ranade?styles=134682,134685,134689");

        @font-face {
            font-family: "BB";
            src: url("https://gotrips.ai/assets/fonts/Gordita-Black.woff2") format("woff2")
        }

        /* Header & Navbar fix: REMOVE gap between nav and ticker */
        nav.navbar {
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }

        .news-ticker {
            margin-top: 0 !important;
            padding-top: 0 !important;
            border-top: none !important;
        }

        /* Base navbar styles */
        .navbar-nav .nav-item {
            text-align: center;
            white-space: normal;
            word-break: break-word;
        }

        .navbar-nav .nav-link {
            display: block;
            text-align: center;
            white-space: normal;
        }

        .navbar .dropdown-menu {
            background-color: rgba(0, 0, 0, 0.95) !important;
            border: none;
        }

        .navbar .dropdown-menu a {
            color: #fff !important;
        }

        .navbar .dropdown-menu a:hover {
            color: #FFD23F !important;
            background-color: transparent !important;
        }

        /* Currency dropdown */
        #currencyDropdown {
            font-weight: 600;
            width: 85px;
            background-color: white;
            color: #f2b80a;
            border: none;
            font-size: 12px;
            outline: none;
            cursor: pointer;
            padding: 2px 6px;
            border-radius: 6px;
            margin-left: 6px;
        }

        #currencyDropdown option {
            background-color: white;
            color: black;
        }

        .nav-currency {
            display: flex;
            align-items: center;
            padding-left: 8px;
        }

        /* Desktop logo styles */
        .navbar-brand img {
            height: 80px;
            width: auto;
        }

        .navbar-nav .nav-link {
            font-size: 13px;
            padding: 6px 8px;
            line-height: 1;
        }

        /* ENHANCED: Mobile toggle button styles with smooth icon transitions */
        .navbar-toggler {
            position: relative;
            width: 40px;
            height: 40px;
            border: 1px solid white !important;
            padding: 0;
            background: transparent;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .hamburger-icon,
        .close-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            transition: all 0.3s ease;
            opacity: 1;
        }

        .hamburger-icon {
            opacity: 1;
        }

        .close-icon {
            opacity: 0;
            font-size: 20px;
            font-weight: bold;
            color: white;
            line-height: 1;
            transform: translate(-50%, -50%) rotate(90deg);
        }

        /* Icon state when menu is expanded */
        .navbar-toggler[aria-expanded="true"] .hamburger-icon {
            opacity: 0;
            transform: translate(-50%, -50%) rotate(-90deg);
        }

        .navbar-toggler[aria-expanded="true"] .close-icon {
            opacity: 1;
            transform: translate(-50%, -50%) rotate(0deg);
        }

        /* Mobile logo - hidden on desktop */
        .mobile-navbar-brand {
            display: none;
        }

        /* News ticker */
        .news-ticker {
            background-color: #222;
            overflow: hidden;
            display: flex;
            align-items: center;
            padding: 4px 0;
        }

        .scroll {
            display: inline-block;
            white-space: nowrap;
            animation: scroll-left 40s linear infinite;
            font-size: 17px;
            color: white;
            padding-left: 100%;
        }

        .scroll a {
            color: #debb55;
            text-decoration: none;
            margin-right: 25px;
        }

        @keyframes scroll-left {
            0% {
                transform: translateX(0%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        body {
            margin-top: 100px;
        }

        /* Desktop layout styles - 5+1+5 structure */
        @media (min-width: 992px) {
            .navbar-nav {
                display: flex;
                justify-content: center;
                align-items: center;
                width: 100%;
            }

            /* Left menu items (first 5) */
            .navbar-nav .nav-item:nth-child(1),
            .navbar-nav .nav-item:nth-child(2),
            .navbar-nav .nav-item:nth-child(3),
            .navbar-nav .nav-item:nth-child(4),
            .navbar-nav .nav-item:nth-child(5) {
                order: 1;
            }

            /* Center logo (6th item) */
            .navbar-nav .nav-item:nth-child(6) {
                order: 2;
                flex: 0 0 auto;
                margin: 0 20px;
            }

            /* Right menu items (last 5) */
            .navbar-nav .nav-item:nth-child(7),
            .navbar-nav .nav-item:nth-child(8),
            .navbar-nav .nav-item:nth-child(9),
            .navbar-nav .nav-item:nth-child(10),
            .navbar-nav .nav-item:nth-child(11) {
                order: 3;
            }
        }

        /* Large desktop responsive adjustments */
        @media (min-width: 1600px) {
            .navbar-brand img {
                height: 100px;
            }

            .navbar-nav .nav-link {
                font-size: 15px;
                padding: 8px 12px;
            }

            #currencyDropdown {
                font-size: 13px;
                width: 90px;
            }
        }

        @media (min-width: 1400px) and (max-width: 1599px) {
            .navbar-brand img {
                height: 90px;
            }

            .navbar-nav .nav-link {
                font-size: 14px;
                padding: 7px 10px;
            }
        }

        @media (min-width: 1200px) and (max-width: 1399px) {
            .navbar-brand img {
                height: 85px;
            }

            .navbar-nav .nav-link {
                font-size: 13px;
                padding: 6px 9px;
            }
        }

        @media (min-width: 992px) and (max-width: 1199px) {
            .navbar-brand img {
                height: 75px;
            }

            .navbar-nav .nav-link {
                font-size: 12px;
                padding: 5px 7px;
            }

            #currencyDropdown {
                font-size: 11px;
                width: 80px;
            }
        }

        /* Mobile and tablet styles */
        @media (max-width: 991.98px) {

            /* Show mobile logo */
            .mobile-navbar-brand {
                display: block !important;
                position: absolute;
                left: 50%;
                transform: translateX(-50%);
                top: 50%;
                transform: translate(-50%, -50%);
                z-index: 999;
            }

            .mobile-navbar-brand img {
                height: 50px;
                width: auto;
            }

            /* Mobile navbar styles */
            .navbar-collapse {
                background-color: rgba(0, 0, 0, 0.95) !important;
                padding: 1rem;
                border-radius: 0 0 8px 8px;
                margin-top: 10px;
            }

            /* Toggle button positioning */
            .navbar-toggler {
                position: relative;
                z-index: 1001;
                margin-left: auto;
                order: 3;
            }

            /* Mobile navigation layout */
            .navbar-nav {
                flex-direction: column !important;
                align-items: flex-start !important;
                width: 100%;
            }

            .navbar-nav .nav-item {
                width: 100%;
                order: initial !important;
            }

            .navbar-nav .nav-link {
                display: block;
                width: 100%;
                text-align: left;
                padding: 12px 16px;
                font-size: 16px;
                color: white !important;
            }

            /* Hide desktop logo in mobile */
            .navbar-nav .nav-item.d-none.d-lg-block {
                display: none !important;
            }

            /* Mobile currency dropdown */
            .nav-currency {
                margin-top: 10px;
                padding-left: 16px;
            }

            /* Column adjustments */
            .col-md-5,
            .col-md-2 {
                width: 100% !important;
                display: block;
                text-align: left;
            }

            /* Container positioning for mobile logo */
            .container-fluid {
                position: relative;
            }
        }

        /* Small mobile devices */
        @media (max-width: 576px) {
            .mobile-navbar-brand img {
                height: 45px;
            }

            .navbar-nav .nav-link {
                font-size: 15px;
                padding: 10px 12px;
            }
        }
    </style>
</head>

<body data-mobile-nav-style="classic">
    <!-- start header -->
    <header>
        <nav class="navbar navbar-expand-lg header-light center-logo header-reverse flex-column p-0"
            style="background-color: rgba(0, 0, 0, 0.9);">
            <div class="container-fluid w-100">
                <!-- Mobile Logo (centered, visible only on mobile) -->
                <a class="mobile-navbar-brand d-lg-none" href="/">
                    <img src="{{ asset('assets/index_files/logo.png') }}" alt="Go Trips">
                </a>

                <!-- FIXED: Mobile Toggle Button -->
                <button class="navbar-toggler collapsed d-lg-none" type="button" aria-controls="mainNav"
                    aria-expanded="false" aria-label="Toggle navigation" id="customNavToggler">
                    <span class="hamburger-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="white" class="bi bi-list"
                            viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M2.5 12.5a.5.5 0 0 1 0-1h11a.5.5 0 0 1 0 1h-11zm0-4a.5.5 0 0 1 0-1h11a.5.5 0 0 1 0 1h-11zm0-4a.5.5 0 0 1 0-1h11a.5.5 0 0 1 0 1h-11z" />
                        </svg>
                    </span>
                    <span class="close-icon">✕</span>
                </button>

                <div class="collapse navbar-collapse justify-content-center" id="mainNav">
                    <ul class="navbar-nav w-100 text-center text-lg-start">
                        <!-- Left Menu (5 items) -->
                        <li class="nav-item">
                            <a href="/" class="nav-link"
                                style="font-family: var(--primary-font); font-weight: 700;">Home</a>
                        </li>
                        <li class="nav-item">
                            <a href="/Activities" class="nav-link"
                                style="font-family: var(--primary-font); font-weight: 700;">Activities</a>
                        </li>

                        <li class="nav-item">
                            <a href="/uaevisa" class="nav-link"
                                style="font-family: var(--primary-font); font-weight: 700;">Visa Services</a>
                        </li>
                        <li class="nav-item">
                            <a href="/countriestour" class="nav-link"
                                style="font-family: var(--primary-font); font-weight: 700;">Tour Packages</a>
                        </li>
                        <li class="nav-item">
                            <a href="/shopnow" class="nav-link"
                                style="font-family: var(--primary-font); font-weight: 700;">Shop Online</a>
                        </li>

                        <!-- Desktop Center Logo (6th item) -->
                        <li class="nav-item d-none d-lg-block col-md-2 text-center">
                            <a class="navbar-brand" href="/">
                                <img src="assets/index_files/logo.png" alt="Logo" width="75" height="73"
                                    style="margin-top: -50px;">
                            </a>
                        </li>

                        <!-- Right Menu (5 items including currency) -->
                        <li class="nav-item">
                            <a href="/shopnow" class="nav-link"
                                style="font-family: var(--primary-font); font-weight: 700;">Hajj & Umrah</a>
                        </li>
                        <li class="nav-item">
                            <a href="/shopnow" class="nav-link"
                                style="font-family: var(--primary-font); font-weight: 700;">Pay Online</a>
                        </li>
                        <li class="nav-item">
                            <a href="/lookingforajob" class="nav-link"
                                style="font-family: var(--primary-font); font-weight: 700;">Looking for a Job</a>
                        </li>
                        <li class="nav-item">
                            <a href="/contact-us" class="nav-link"
                                style="font-family: var(--primary-font); font-weight: 700;">Contact Us</a>
                        </li>
                        <li class="nav-item nav-currency">
                            <select id="currencyDropdown">
                                <option value="AED" selected>د.إ AED</option>
                            </select>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- News Ticker -->
            <div class="news-ticker w-100 px-3">
                <div class="scroll text-uppercase">
                    @foreach($announcements as $announcement)
                        <a href="#">
                            @if($announcement->AnnouncementImportance == 1)
                                <img src="{{ asset('assets/index_files/new_indicator.gif') }}" alt="New" width="34" height="18">
                            @endif
                            @if($announcement->flagImgPath && file_exists(public_path($announcement->flagImgPath)))
                                <img src="{{ $announcement->flagImgPath }}" alt="Flag" width="24" height="18">
                            @endif
                            {{ $announcement->description }}
                        </a>
                        @if (!$loop->last)
                            |
                        @endif
                    @endforeach
                </div>
            </div>

        </nav>
    </header>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var navToggler = document.getElementById('customNavToggler');
            var navCollapse = document.getElementById('mainNav');
            var navLinks = document.querySelectorAll('.navbar-nav .nav-link:not(.dropdown-toggle)');

            if (navToggler && navCollapse) {
                navToggler.addEventListener('click', function (e) {
                    navCollapse.classList.toggle('show');
                    var isExpanded = navCollapse.classList.contains('show');
                    navToggler.setAttribute('aria-expanded', isExpanded);
                    navToggler.classList.toggle('collapsed', !isExpanded);
                });

                navLinks.forEach(function (link) {
                    link.addEventListener('click', function () {
                        if (window.innerWidth <= 991) {
                            navCollapse.classList.remove('show');
                            navToggler.setAttribute('aria-expanded', 'false');
                            navToggler.classList.add('collapsed');
                        }
                    });
                });
            }
        });
    </script>
</body>

</html>