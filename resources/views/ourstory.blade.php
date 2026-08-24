@include('header')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

    :root {
        --premium-gold: #FFD700;
        --premium-gold-gradient: linear-gradient(135deg, #FFD700 0%, #D4AF37 50%, #B8960C 100%);
        --dark-bg: #050505;
        --card-border: rgba(255, 215, 0, 0.15);
        --text-muted: #aaaaaa;
    }

    body {
        background-color: var(--dark-bg);
        font-family: 'Outfit', sans-serif;
        color: #fff;
    }

    h1, h2, h3, h4, h5 {
        font-family: 'Outfit', sans-serif;
    }

    .ourstory-hero {
        position: relative;
        padding: 120px 0 70px;
        background-size: cover;
        background-position: center;
    }

    .ourstory-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0.85) 100%);
    }

    .ourstory-hero-content {
        position: relative;
        z-index: 2;
    }

    .ourstory-hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: var(--premium-gold);
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 14px;
    }

    .ourstory-hero-kicker span {
        width: 30px;
        height: 2px;
        background: var(--premium-gold);
        display: inline-block;
    }

    .ourstory-hero h1 {
        font-size: 44px;
        font-weight: 800;
        color: #fff;
        margin-bottom: 6px;
    }

    .ourstory-hero p {
        font-size: 20px;
        font-weight: 300;
        color: var(--text-muted);
        margin: 0;
    }

    /* Premium Section Title — matches contact-us.blade.php / lookingforajob.blade.php */
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

    @media (max-width: 767px) {
        .ourstory-hero { padding: 100px 0 50px; }
        .ourstory-hero h1 { font-size: 30px; }
        .ourstory-hero p { font-size: 16px; }
        .premium-title { font-size: 28px; }
    }
</style>

<!-- Hero -->
<section class="ourstory-hero" style="background-image: url('{{ asset('assets/index_files/s5.jpg') }}');">
    <div class="container ourstory-hero-content">
        <div class="ourstory-hero-kicker"><span></span>Our Company</div>
        <h1>Embark on a Journey of Discovery</h1>
        <p>The people and the platform behind GoTrips</p>
    </div>
</section>

<!-- About -->
<section class="about-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-5 mb-lg-0">
                <div class="premium-image-wrapper">
                    <img src="{{ asset('assets/ourstory_files/1.jpg') }}" alt="Ayn Al Amir Tourism">
                </div>
            </div>
            <div class="col-lg-7 ps-lg-5">
                <h2 class="premium-title">About Us</h2>
                <div class="premium-subtitle">
                    <p class="mb-4">
                        Welcome to <strong class="text-white">Ayn Al Amir Tourism L.L.C</strong>, a dynamic and
                        innovative travel agency dedicated to providing unparalleled travel solutions and
                        consultancy services. Established in January 2024 by
                        <strong class="text-white">Mr. Amer Ali Mohammed</strong>, our company is committed to
                        excellence and driven by a passion for travel. With over 13 years of industry experience,
                        Mr. Mohammed has built Ayn Al Amir Tourism L.L.C into a trusted partner for individuals and
                        businesses seeking comprehensive travel solutions.
                    </p>
                    <p class="mb-0">
                        At Ayn Al Amir Tourism L.L.C, we pride ourselves on our commitment to customer satisfaction,
                        innovation, and continuous improvement. We build and run our own travel technology: the
                        GoTrips platform brings visas, eSIMs, activities and Hajj &amp; Umrah bookings under one
                        roof, so every request is handled quickly and tracked from enquiry to confirmation.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

@include('footer')
