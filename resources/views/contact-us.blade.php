@include('header')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

    :root {
        --premium-gold: #FFD700;
        --premium-gold-gradient: linear-gradient(135deg, #FFD700 0%, #D4AF37 50%, #B8960C 100%);
        --dark-bg: #050505;
        --card-bg: #111111;
        --card-border: rgba(255, 215, 0, 0.15);
        --text-muted: #aaaaaa;
    }

    body {
        background-color: var(--dark-bg);
        font-family: 'Outfit', sans-serif;
        color: #fff;
    }

    h1,
    h2,
    h3,
    h4,
    h5 {
        font-family: 'Outfit', sans-serif;
    }

    /* Premium Section Title */
    .premium-title {
        font-size: 38px;
        font-weight: 800;
        letter-spacing: 2px;
        margin-bottom: 30px;
        background: var(--premium-gold-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-transform: uppercase;
        display: inline-block;
    }

    .premium-subtitle {
        font-size: 18px;
        color: var(--text-muted);
        line-height: 1.8;
        font-weight: 400;
    }

    /* About Us Section */
    .about-section {
        padding: 80px 0;
        background: radial-gradient(circle at top right, rgba(30, 30, 30, 0.3), transparent);
        border-bottom: 1px solid #222;
    }

    .premium-image-wrapper {
        position: relative;
        padding: 10px;
        border: 1px solid var(--card-border);
        border-radius: 20px;
    }

    .premium-image-wrapper img {
        border-radius: 12px;
        width: 100%;
        display: block;
        transition: transform 0.5s ease;
    }

    .premium-image-wrapper:hover img {
        transform: scale(1.02);
    }

    /* Contact Section */
    .contact-section-wrapper {
        position: relative;
        padding: 80px 0;
        background: url('{{ asset('assets/index_files/kalifa.jpg') }}') center center / cover no-repeat fixed;
    }

    .contact-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.85);
        /* Dark overlay */
        backdrop-filter: blur(5px);
    }

    .glass-card {
        background: rgba(20, 20, 20, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        border-radius: 24px;
        padding: 40px;
        height: 100%;
    }

    .glass-card h4 {
        font-size: 22px !important;
        font-weight: 700 !important;
        letter-spacing: 1.5px !important;
    }

    /* Form Styles */
    .premium-form .form-control,
    .premium-form .form-select {
        background: rgba(0, 0, 0, 0.6);
        border: 1px solid #333;
        color: #fff;
        height: 55px;
        border-radius: 12px;
        padding: 0 20px;
        font-size: 15px;
        transition: all 0.3s ease;
    }

    .premium-form textarea.form-control {
        height: auto;
        padding-top: 15px;
    }

    .premium-form .form-control:focus {
        border-color: var(--premium-gold);
        box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.1);
        outline: none;
    }

    /* Button */
    .btn-gold {
        background: var(--premium-gold-gradient);
        border: none;
        color: #000;
        font-weight: 700;
        font-size: 15px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        padding: 16px 30px;
        border-radius: 50px;
        width: 100%;
        transition: all 0.3s ease;
    }

    .btn-gold:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(255, 215, 0, 0.2);
        color: #000;
    }

    /* QR Code Section */
    .qr-card {
        text-align: center;
        padding: 30px;
        background: #111;
        border: 1px solid #333;
        border-radius: 20px;
        transition: transform 0.3s ease;
        height: 100%;
    }

    .qr-card:hover {
        border-color: var(--premium-gold);
        transform: translateY(-5px);
    }

    .qr-img {
        width: 120px;
        height: 120px;
        margin: 0 auto 20px;
        border-radius: 50%;
        border: 3px solid var(--premium-gold);
        padding: 3px;
        background: #000;
    }

    .qr-img img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 50%;
    }

    .qr-title {
        color: #fff;
        font-weight: 700;
        font-size: 16px;
        letter-spacing: 1px;
        margin-bottom: 10px;
        text-transform: uppercase;
    }

    .qr-text {
        color: var(--premium-gold);
        font-size: 14px;
        margin: 0;
    }

    /* Core Values */
    .values-section {
        padding: 80px 0;
        background: #080808;
    }

    .value-box {
        background: #111;
        border: 1px solid #222;
        padding: 40px 30px;
        text-align: center;
        border-radius: 20px;
        height: 100%;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .value-box:hover {
        background: #161616;
        transform: translateY(-5px);
    }

    .value-icon {
        font-size: 40px;
        color: var(--premium-gold);
        margin-bottom: 25px;
        display: inline-block;
    }

    .value-title {
        color: #fff;
        font-size: 18px;
        font-weight: 700;
        letter-spacing: 1px;
        margin-bottom: 15px;
        text-transform: uppercase;
    }

    .value-text {
        color: #999;
        font-size: 15px;
        line-height: 1.6;
    }

    .map-frame {
        border-radius: 24px;
        overflow: hidden;
        border: 2px solid #333;
        height: 100%;
        min-height: 480px;
        filter: invert(90%) hue-rotate(180deg);
        transition: all 0.5s;
    }

    .map-frame:hover {
        filter: none;
    }

    /* MOBILE OPTIMIZATION */
    @media (max-width: 991px) {
        .premium-title {
            font-size: 32px;
        }

        .about-section,
        .contact-section-wrapper,
        .values-section {
            padding: 50px 0;
        }

        .map-frame {
            min-height: 350px;
            margin-bottom: 20px;
        }
    }

    @media (max-width: 768px) {

        /* Mobile Typography */
        .premium-title {
            font-size: 26px;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }

        .premium-subtitle {
            font-size: 16px;
        }

        /* Mobile Spacing */
        .about-section,
        .contact-section-wrapper,
        .values-section {
            padding: 40px 0;
        }

        /* Glass Card Mobile */
        .glass-card {
            padding: 25px 20px;
            border-radius: 16px;
        }

        /* Form Inputs Mobile */
        .premium-form .form-control,
        .premium-form .form-select {
            height: 48px;
            font-size: 14px;
        }

        /* Buttons */
        .btn-gold {
            padding: 14px;
            font-size: 14px;
        }

        /* QR Cards */
        .qr-card {
            padding: 25px 20px;
            margin-bottom: 15px;
        }

        .qr-img {
            width: 90px;
            height: 90px;
        }

        /* Values Mobile */
        .value-box {
            padding: 30px 20px;
        }

        .value-icon {
            font-size: 32px;
            margin-bottom: 15px;
        }

        .value-title {
            font-size: 16px;
        }

    }

    @media (max-width: 575px) {
        .premium-title {
            font-size: 22px;
            letter-spacing: 0.5px;
        }
        .premium-subtitle {
            font-size: 14px;
        }
        .glass-card {
            padding: 20px 15px;
            border-radius: 12px;
        }
        .map-frame {
            min-height: 250px;
        }
        .premium-form .form-control,
        .premium-form .form-select {
            height: 44px;
            font-size: 13px;
            padding: 0 14px;
        }
        .btn-gold {
            padding: 12px;
            font-size: 13px;
        }
        .qr-img {
            width: 70px;
            height: 70px;
        }
        .qr-title {
            font-size: 14px;
        }
        .qr-text {
            font-size: 12px;
        }
        .value-box {
            padding: 20px 12px;
        }
        .value-icon {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .value-title {
            font-size: 14px;
        }
    }
</style>

<!-- About Us Section -->
<section class="about-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-5 mb-lg-0">
                <div class="premium-image-wrapper">
                    <img src="assets/ourstory_files/1.jpg" alt="About Gotrips">
                </div>
            </div>
            <div class="col-lg-7 ps-lg-5">
                <h2 class="premium-title">About Us</h2>
                <div class="premium-subtitle">
                    @php
                        $cuName = (isset($company) && $company && $company->name) ? $company->name : 'GoTrips';
                        $cuAbout = (isset($company) && $company && $company->getSetting('about')) ? $company->getSetting('about') : null;
                    @endphp
                    @if($cuAbout)
                        {!! nl2br(e($cuAbout)) !!}
                    @else
                        @platformOnly
                            <p class="mb-4">
                                Welcome to <strong class="text-white">GOTRIPS</strong>, a part of <strong class="text-white">Ayn Al Amir
                                    Tourism</strong>, a dynamic travel agency dedicated to providing unparalleled solutions.
                                Established in 2024 by <strong class="text-white">Mr. Amer Ali Mohammed</strong>, we are committed to excellence
                                with over 13 years of expertise.
                            </p>
                            <p class="mb-4">
                                We pride ourselves on customer satisfaction and innovation. Our partnership with <strong class="text-white">Portway
                                    Systems</strong> enables state-of-the-art Agency Management, ensuring a seamless experience
                                for every client.
                            </p>
                        @endplatformOnly
                        @unless(app()->bound('current_company') && app('current_company')->slug === 'gotrips')
                        @if(isset($company) && $company && $company->slug !== 'gotrips')
                            <p class="mb-4">
                                Welcome to <strong class="text-white">{{ strtoupper($cuName) }}</strong>, your trusted travel partner. We help you discover unforgettable experiences with personalised service and end-to-end booking support.
                            </p>
                            <p class="mb-4">
                                Reach out using the form, the email or the phone number below — we usually respond within a few hours during business days.
                            </p>
                        @endif
                        @endunless
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Us Section -->
<section class="contact-section-wrapper">
    <div class="contact-overlay"></div>
    <div class="container position-relative" style="z-index: 2;">

        <div class="text-center mb-4 mb-md-5">
            <h2 class="premium-title">Contact Us</h2>
            <p class="premium-subtitle mx-auto" style="max-width: 800px;">
                Begin your journey with a conversation. Our team is here to help you every step of the way.
            </p>
        </div>

        <div class="row g-4 d-flex align-items-stretch">
            <!-- Left: Map -->
            @php
                $contactTenant = current_company();
                $contactMapUrl = $contactTenant?->googleMapsEmbedUrl();
                // Fallback: on the main GoTrips site, always show the head-office map
                // even when no company address/embed URL is configured.
                $isMainSite = !$contactTenant || $contactTenant->slug === 'gotrips';
                if (!$contactMapUrl && $isMainSite) {
                    $contactMapUrl = 'https://www.google.com/maps?q='
                        . urlencode('Sanaiya, Beda Zayed Al Dhafra, Abu Dhabi, U.A.E')
                        . '&output=embed';
                }
            @endphp
            <div class="col-lg-6">
                <div class="glass-card p-0 overflow-hidden h-100">
                    @if($contactMapUrl)
                        <div class="map-frame">
                            <iframe src="{{ $contactMapUrl }}"
                                    class="w-100 h-100 border-0"
                                    allowfullscreen=""
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"
                                    title="{{ $contactTenant?->name ?? 'GoTrips' }} location"></iframe>
                        </div>
                    @else
                        <div class="d-flex flex-column justify-content-center align-items-center h-100 p-4 text-center" style="min-height:300px;">
                            <i class="bi bi-geo-alt" style="font-size:48px;color:#FFD700;opacity:.5;"></i>
                            <p class="text-white mt-3 mb-0" style="opacity:.7;">Visit us by appointment.</p>
                            <p class="text-white" style="opacity:.5;font-size:13px;">Get in touch via the form to schedule.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right: Form -->
            <div class="col-lg-6">
                <div class="glass-card">
                    <h4 class="text-white text-uppercase font-weight-bold mb-4 d-none d-md-block">Send Us A Message</h4>
                    <h4 class="text-white text-uppercase font-weight-bold mb-4 d-md-none text-center"
                        style="font-size: 18px;">Send Us A Message</h4>

                    <!-- Form -->
                    <form id="contactForm" action="{{ route('contact.submit') }}" method="POST" class="premium-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <input class="form-control" type="text" name="name" placeholder="Your Name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <input class="form-control" type="tel" name="phone" placeholder="Mobile Number"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <input class="form-control" type="email" name="email" placeholder="Email Address"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <select class="form-select" name="booking-city" required>
                                    <option value="" selected disabled>Select Service</option>
                                    <option value="Airline Tickets">Airline Tickets</option>
                                    <option value="Hotel Bookings">Hotel Bookings</option>
                                    <option value="Holiday Packages">Holiday Packages</option>
                                    <option value="Car Rentals">Car Rentals</option>
                                    <option value="Travel Insurance">Travel Insurance</option>
                                    <option value="Visa Assistance">Visa Assistance</option>
                                </select>
                            </div>
                            <div class="col-12 mb-4">
                                <textarea class="form-control" rows="4" name="booking-address"
                                    placeholder="Tell us about your travel plans...">@if(!empty($enquiryPackage)){{ 'I would like to enquire about: ' . $enquiryPackage }}@endif</textarea>
                            </div>
                            <div class="col-12">
                                <button class="btn-gold" type="submit" id="sendButton">
                                    <span id="buttonText">Send Message</span>
                                    <span id="buttonSpinner" class="spinner-border spinner-border-sm ms-2" role="status"
                                        aria-hidden="true" style="display:none;"></span>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Snackbar -->
                    <div id="snackbar"
                        style="display: none; width: 100%; text-align: center; margin-top: 15px; padding: 10px; border-radius: 8px;">
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Code / Locations Section -->
        <div class="row mt-5 pt-4 g-4 justify-content-center">
            <h3 class="premium-title text-center mb-4 text-white d-block w-100" style="font-size: 28px;">Connect With Us
            </h3>

            @php
                $isMain = !isset($company) || !$company || $company->slug === 'gotrips';
                $cuLogo = (isset($company) && $company && $company->logo)
                    ? asset('storage/' . $company->logo)
                    : ($isMain ? asset('assets/index_files/logo.png') : null);
                // On tenant subdomains, only show contact info that the tenant has actually set —
                // never fall back to GoTrips' info.
                $cuEmail   = (isset($company) && $company && $company->email)   ? $company->email   : ($isMain ? 'info@aynalamirtourism.com' : null);
                $cuPhone   = (isset($company) && $company && $company->phone)   ? $company->phone   : ($isMain ? '+971-54 365 1065' : null);
                $cuAddress = (isset($company) && $company && $company->address) ? $company->address : ($isMain ? 'Sanaiya, Beda Zayed Al Dhafra, Abu Dhabi, U.A.E' : null);
            @endphp
            <!-- Address -->
            @if($cuAddress)
            <div class="col-md-4 col-sm-6">
                <div class="qr-card">
                    <div class="qr-img">
                        @if($isMain)
                            <img src="{{ asset('assets/index_files/LocationQR-2.jpg') }}" alt="Location QR">
                        @else
                            <i class="fa-solid fa-location-dot" style="font-size:64px; color:#FFD700;"></i>
                        @endif
                    </div>
                    <div class="qr-title">Visit Us</div>
                    <p class="qr-text" style="line-height: 1.5;">{{ $cuAddress }}</p>
                </div>
            </div>
            @endif

            <!-- Email -->
            @if($cuEmail)
            <div class="col-md-4 col-sm-6">
                <div class="qr-card">
                    <div class="qr-img" style="background: transparent;">
                        @if($cuLogo)
                            <img src="{{ $cuLogo }}" alt="{{ $cuName }} logo">
                        @else
                            <i class="fa-solid fa-envelope" style="font-size:64px; color:#FFD700;"></i>
                        @endif
                    </div>
                    <div class="qr-title">Email Us</div>
                    <p class="qr-text" style="word-break: break-all;">
                        <a href="mailto:{{ $cuEmail }}" style="color:inherit; text-decoration:none;">{{ $cuEmail }}</a>
                    </p>
                </div>
            </div>
            @endif

            <!-- WhatsApp / Phone -->
            @if($cuPhone)
            <div class="col-md-4 col-sm-6">
                <div class="qr-card">
                    <div class="qr-img">
                        @if($isMain)
                            <img src="{{ asset('assets/index_files/WhatsappQR.jpg') }}" alt="WA QR">
                        @else
                            <i class="fa-brands fa-whatsapp" style="font-size:64px; color:#25D366;"></i>
                        @endif
                    </div>
                    <div class="qr-title">Call / WhatsApp</div>
                    <p class="qr-text">
                        <a href="tel:{{ preg_replace('/\s+/', '', $cuPhone) }}" style="color:inherit; text-decoration:none;">{{ $cuPhone }}</a>
                    </p>
                </div>
            </div>
            @endif

            @if(!$isMain && !$cuEmail && !$cuPhone && !$cuAddress)
            <div class="col-12 text-center" style="padding: 40px 20px; color: #ccc;">
                <i class="fa-solid fa-circle-info" style="font-size: 32px; color: #FFD700; margin-bottom: 16px;"></i>
                <p>Contact information is being set up. Please check back shortly.</p>
            </div>
            @endif
        </div>

    </div>
</section>

<!-- Values Section -->
<section class="values-section">
    <div class="container">

        <div class="text-center mb-5">
            <h2 class="premium-title">Our Core Values</h2>
        </div>

        <div class="row g-4 justify-content-center">
            <!-- Excellence -->
            <div class="col-6 col-lg-3">
                <div class="value-box">
                    <i class="bi bi-trophy-fill value-icon"></i>
                    <h4 class="value-title">Excellence</h4>
                    <p class="value-text d-none d-md-block">Delivering superior service and value to our clients.</p>
                </div>
            </div>
            <!-- Integrity -->
            <div class="col-6 col-lg-3">
                <div class="value-box">
                    <i class="bi bi-shield-check value-icon" style="color: #4CAF50;"></i>
                    <h4 class="value-title">Integrity</h4>
                    <p class="value-text d-none d-md-block">Upholding the highest ethical standards.</p>
                </div>
            </div>
            <!-- Innovation -->
            <div class="col-6 col-lg-3">
                <div class="value-box">
                    <i class="bi bi-lightbulb-fill value-icon" style="color: #2196F3;"></i>
                    <h4 class="value-title">Innovation</h4>
                    <p class="value-text d-none d-md-block">Leveraging technology to enhance experiences.</p>
                </div>
            </div>
            <!-- Collaboration -->
            <div class="col-6 col-lg-3">
                <div class="value-box">
                    <i class="bi bi-people-fill value-icon" style="color: #00BCD4;"></i>
                    <h4 class="value-title">Teamwork</h4>
                    <p class="value-text d-none d-md-block">Fostering strong partnerships for success.</p>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    document.getElementById('contactForm').addEventListener('submit', function (e) {
        e.preventDefault();
        document.getElementById('buttonText').style.display = 'none';
        document.getElementById('buttonSpinner').style.display = 'inline-block';

        let formData = new FormData(this);
        let snackbar = document.getElementById('snackbar');

        fetch(this.action, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value },
            body: formData
        })
            .then(async response => {
                document.getElementById('buttonText').style.display = '';
                document.getElementById('buttonSpinner').style.display = 'none';

                if (response.ok) {
                    this.reset();
                    snackbar.style.display = 'block';
                    snackbar.style.background = '#198754';
                    snackbar.innerHTML = '<span class="text-white">Message Sent Successfully! We will revert shortly.</span>';
                } else {
                    snackbar.style.display = 'block';
                    snackbar.style.background = '#dc3545';
                    snackbar.innerHTML = '<span class="text-white">Failed to send message. Please try again.</span>';
                }

                setTimeout(() => snackbar.style.display = 'none', 3000);
            })
            .catch(() => {
                document.getElementById('buttonText').style.display = '';
                document.getElementById('buttonSpinner').style.display = 'none';
                snackbar.style.display = 'block';
                snackbar.style.background = '#dc3545';
                snackbar.innerHTML = '<span class="text-white">Network error. Please try again.</span>';
            });
    });
</script>

@include('footer')