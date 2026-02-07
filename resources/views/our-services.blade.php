@include('header')

<main style="background: #000; min-height: 100vh;">
    <!------ services section ------>
    <section class="services-section" style="background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%); padding: 120px 0 80px 0;">
        <div class="container">

            <!-- Section Title -->
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <div class="services-section-badge">
                        <span>15+ Years of Excellence</span>
                    </div>
                    <h2 class="services-section-title">OUR SERVICES</h2>
                    <p class="services-section-subtitle">Powered by industry legends, serving travelers worldwide with premium solutions</p>
                </div>
            </div>

            <div class="row g-4">
                @php
                    $services = [
                        ['title' => 'ACTIVITIES', 'img' => 'assets/index_files/service_activities_1767532687858.png', 'link' => '/activities'],
                        ['title' => 'CITY TOUR', 'img' => 'assets/index_files/service_city_tour_1767532704738.png', 'link' => '/'],
                        ['title' => 'PICK & DROP GUESTS', 'img' => 'assets/index_files/service_pick_drop_1767532722482.png', 'link' => '/'],
                        ['title' => 'LIWA GUESTS ASSISTANCE', 'img' => 'assets/index_files/service_liwa_assistance_1767532741495.png', 'link' => '/'],
                        ['title' => 'WORLD TRAVEL ESIM AVAILABLE', 'img' => 'assets/index_files/service_esim_1767532759373.png', 'link' => '/'],
                        ['title' => 'TRIPS ORGANISING', 'img' => 'assets/index_files/service_trips_organizing_1767532787246.png', 'link' => '/'],
                        ['title' => 'HOTEL BOOKINGS', 'img' => 'assets/index_files/service_hotel_bookings_1767532833171.png', 'link' => '/'],
                        ['title' => 'BUSINESS WHATSAPP INTEGRATION', 'img' => 'assets/index_files/service_whatsapp_integration_1767532912170.png', 'link' => '/'],
                        ['title' => 'WEBSITE DEVELOPMENT', 'img' => 'assets/index_files/service_website_development_1767532932173.png', 'link' => '/'],
                        ['title' => 'VISA SERVICES', 'img' => 'assets/index_files/service_visa_services_1767532949283.png', 'link' => '/uaevisa'],
                        ['title' => 'HAJJ UMRAH SERVICES', 'img' => 'assets/index_files/service_hajj_umrah_1767532980525.png', 'link' => '/hajj-umrah'],
                        ['title' => 'CAR RENTALS', 'img' => 'assets/index_files/service_car_rentals_1767532996883.png', 'link' => '/'],
                        ['title' => 'RECRUITMENT SERVICES', 'img' => 'assets/index_files/service_recruitment_1767533016184.png', 'link' => '/'],
                        ['title' => 'INTERNSHIPS', 'img' => 'assets/index_files/service_internships_1767533036518.png', 'link' => '/'],
                        ['title' => 'WORLD CLASS TOUR PACKAGES', 'img' => 'assets/index_files/service_tour_packages_1767533057318.png', 'link' => '/countriestour'],
                        ['title' => 'TRAVEL AGENCY WORKFLOW SETUP', 'img' => 'assets/index_files/service_travel_workflow_1767533087171.png', 'link' => '/'],
                        ['title' => 'NEW BUSINESS SETUP ASSISTANCE', 'img' => 'assets/index_files/service_business_setup_1767533129733.png', 'link' => '/'],
                        ['title' => 'GDS SUPPORT', 'img' => 'assets/index_files/service_gds_1767533146390.png', 'link' => '/'],
                        ['title' => 'BUSINESS CONSULTANTS', 'img' => 'assets/index_files/service_business_consultants_1767533163259.png', 'link' => '/'],
                        ['title' => 'AI, AR, VR INTEGRATION', 'img' => 'assets/index_files/service_ai_ar_vr_1767533181726.png', 'link' => '/'],
                        ['title' => 'STUDY ABROAD', 'img' => 'assets/index_files/study-overseas-educational-consultancy.jpg', 'link' => '/']
                    ];
                @endphp

                @foreach($services as $service)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="service-card">
                        <a href="{{ $service['link'] }}" class="service-card-link">
                            <div class="service-card-image-wrapper">
                                <img src="{{ asset($service['img']) }}" alt="{{ $service['title'] }}" class="service-card-img">
                                <div class="service-card-overlay"></div>
                            </div>
                            <div class="service-card-content">
                                <h3 class="service-card-title">{{ $service['title'] }}</h3>
                            </div>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
</main>

<style>
    /* Reuse styles from welcome.blade.php */
    .services-section { position: relative; overflow: hidden; }
    .services-section-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 24px;
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.15) 0%, rgba(212, 175, 55, 0.05) 100%);
        border: 1px solid rgba(212, 175, 55, 0.3);
        border-radius: 50px;
        margin-bottom: 24px;
    }
    .services-section-badge span {
        font-family: 'Outfit', sans-serif;
        font-size: 12px;
        font-weight: 700;
        color: #FFD700;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    .services-section-title {
        font-family: 'Outfit', sans-serif;
        font-size: 48px;
        font-weight: 800;
        letter-spacing: 4px;
        margin-bottom: 16px;
        background: linear-gradient(135deg, #FFD700 0%, #D4AF37 50%, #B8960C 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-transform: uppercase;
    }
    .services-section-subtitle {
        font-family: 'Outfit', sans-serif;
        font-size: 16px;
        color: #888;
        font-weight: 400;
        line-height: 1.6;
        max-width: 600px;
        margin: 0 auto;
    }
    .services-section .col-lg-3 { width: 25%; }
    .service-card {
        position: relative;
        height: 100%;
        border-radius: 16px;
        overflow: hidden;
        background: #000;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        border: 1px solid #C9A227;
    }
    .service-card:hover { transform: translateY(-8px) scale(1.05); box-shadow: 0 12px 40px rgba(201, 162, 39, 0.4); border: 2px solid #C9A227; }
    .service-card-link { display: block; text-decoration: none; color: inherit; height: 100%; }
    .service-card-image-wrapper { position: relative; width: 100%; padding-top: 75%; overflow: hidden; background: #1a1a1a; }
    .service-card-img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1); }
    .service-card:hover .service-card-img { transform: scale(1.1); }
    .service-card-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.8) 100%); transition: opacity 0.4s ease; }
    .service-card-content { position: absolute; bottom: 0; left: 0; right: 0; padding: 20px; z-index: 2; }
    .service-card-title { font-family: 'Outfit', sans-serif; font-size: 14px; font-weight: 700; color: #fff; text-transform: uppercase; text-shadow: 0 2px 8px rgba(0,0,0,0.8); }
    
    @media (max-width: 1199px) { .services-section .col-lg-3 { width: 33.333%; } }
    @media (max-width: 768px) {
        .services-section .col-lg-3 { width: 50%; }
        .services-section-title { font-size: 32px; letter-spacing: 2px; }
    }
    @media (max-width: 575px) { .services-section .col-lg-3 { width: 100%; } }
</style>

@include('footer')
