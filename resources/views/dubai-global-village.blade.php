@include('header')
<meta name="csrf-token" content="{{ csrf_token() }}">

<link href="https://fonts.googleapis.com/css?family=Rajdhani&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

<style>
    /* Premium Themes & Layout */
    body, html { overflow-x: hidden; background: #000 !important; font-family: 'Rajdhani', sans-serif; }
    
    .golden-heading-alt {
        color: #FFD23F;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 25px;
        position: relative;
        padding-bottom: 15px;
    }
    .golden-heading-alt::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 3px;
        background: #FFD23F;
    }

    .custom-list {
        list-style: none;
        padding: 0;
    }
    .custom-list li {
        position: relative;
        padding-left: 35px;
        margin-bottom: 18px;
        font-size: 18px;
        color: #ccc;
        line-height: 1.6;
    }
    .custom-list li::before {
        content: '\F633';
        font-family: 'bootstrap-icons';
        position: absolute;
        left: 0;
        color: #FFD23F;
        font-weight: bold;
    }

    /* Booking Sidebar Card */
    .booking-sidebar-card {
        background: #111;
        border: 1px solid rgba(255, 210, 63, 0.3);
        border-radius: 15px;
        padding: 35px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        transition: border 0.3s ease;
    }
    .booking-sidebar-card:hover {
        border-color: #FFD23F;
    }
    .price-label {
        font-size: 14px;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 5px;
    }
    .main-price {
        font-size: 38px;
        font-weight: 800;
        color: #FFD23F;
    }
    .main-price span {
        font-size: 18px;
        font-weight: 500;
        margin-left: 5px;
    }
    
    .sidebar-features {
        list-style: none;
        padding: 0;
        margin-top: 20px;
    }
    .sidebar-features li {
        font-size: 15px;
        color: #ddd;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .sidebar-features li i {
        color: #FFD23F;
        font-size: 18px;
    }

    .btn-book-large {
        width: 100%;
        background: linear-gradient(135deg, #FFD700 0%, #FFB800 100%);
        color: #000;
        border: none;
        padding: 22px;
        font-weight: 800;
        font-size: 18px;
        border-radius: 10px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        letter-spacing: 1.5px;
        margin-top: 25px;
        text-transform: uppercase;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        cursor: pointer;
    }
    .btn-book-large:hover {
        background: #fff;
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(255, 215, 0, 0.4);
    }

    /* Hero Slider Styles */
    .hero-slider { width: 100%; height: 650px; position: relative; display: flex; z-index: 0; }
    .swiper-container { width: 100%; height: 100%; }
    .slide-inner { width: 100%; height: 100%; position: relative; display: flex; justify-content: center; align-items: center; overflow: hidden; }
    .slide-img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 0; }
    .overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.4); z-index: 1; }
    .slide-title h2 { font-size: 80px; font-weight: 700; color: #fff; margin-bottom: 10px; text-transform: uppercase; z-index: 2; position: relative; }
    .slide-text p { font-size: 24px; color: #FFD23F; font-weight: 500; z-index: 2; position: relative; letter-spacing: 2px; }

    @media (max-width: 991px) {
        .hero-slider { height: 500px; }
        .slide-title h2 { font-size: 45px; }
        .slide-text p { font-size: 18px; }
    }

    .activity-overview-content {
        color: #afafaf;
        font-size: 18px;
        line-height: 1.8;
    }
    .activity-overview-content p { margin-bottom: 20px; }
    
    .classic-snackbar {
        display: none;
        position: fixed;
        left: 50%;
        bottom: 35px;
        transform: translateX(-50%);
        min-width: 300px;
        z-index: 9999;
        color: #111;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        padding: 18px 30px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        background: #FFD23F;
        border-bottom: 4px solid #b8860b;
    }
    .classic-snackbar.success { background: #50cb4a; border-bottom-color: #3b9636; color: #fff; }
    .classic-snackbar.failed { background: #fa5353; border-bottom-color: #c44f4f; color: #fff; }
    .classic-snackbar.show { display: block; animation: fadeInUp 0.4s ease; }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translate(-50%, 20px); }
        to { opacity: 1; transform: translate(-50%, 0); }
    }
</style>

<!-- Hero Slider -->
@if($detail && !empty($detail->activityImage))
<section class="hero-slider hero-style">
  <div class="swiper-container">
    <div class="swiper-wrapper">
        @foreach($activityImages as $img)
            <div class="swiper-slide">
              <div class="slide-inner">
                <div class="overlay"></div>
                <img class="slide-img" src="{{ asset(trim($img)) }}" alt="{{ $activity->activityName }}">
                <div class="container text-center">
                  <div data-swiper-parallax="300" class="slide-title">
                    <h2>{{ $activity->activityName }}</h2>
                  </div>
                  <div data-swiper-parallax="400" class="slide-text">
                    <p>{{ $activity->activityLocation }}</p>
                  </div>
                </div>
              </div>
            </div>
          @endif
        @endforeach
    </div>
    <div class="swiper-pagination"></div>
  </div>
</section>
@else
<div style="height: 120px; background: #000;"></div>
@endif

<section class="pt-80px pb-80px" style="background: #000; color: #fff;">
  <div class="container">
    <div class="row">
      <!-- Left Column: Information -->
      <div class="col-lg-8 pe-lg-5">
        <h1 class="fw-700 mb-4" style="color: #FFD23F; font-size: 48px; letter-spacing: 1px;">{{ $activity->activityName }}<span style="color: #fff;">.</span></h1>
        
        <div class="d-flex align-items-center mb-5 opacity-7 text-uppercase" style="letter-spacing: 3px; font-size: 13px; font-weight: 600;">
            <i class="fas fa-map-marker-alt me-2" style="color: #FFD23F;"></i>
            {{ $activity->activityLocation }}
        </div>

        <!-- Overview -->
        @if($detail && $detail->detailsOverview)
            <div class="mb-5">
                <h3 class="golden-heading-alt">Overview</h3>
                <div class="activity-overview-content">
                    {!! $detail->detailsOverview !!}
                </div>
            </div>
        @endif

        <!-- Important Information -->
        @if($detail && $detail->detailsIminfo)
            <div class="mb-5">
                <h3 class="golden-heading-alt">Important Information</h3>
                <ul class="custom-list">
                    @foreach(explode('#cseparator', $detail->detailsIminfo) as $info)
                        @if(trim($info) !== '')
                            <li>{{ $info }}</li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Highlights -->
        @if($detail && $detail->detailsHighlights)
            <div class="mb-5">
                <h3 class="golden-heading-alt">Highlights</h3>
                <ul class="custom-list">
                    @foreach(explode('#cseparator', $detail->detailsHighlights) as $highlight)
                        @if(trim($highlight) !== '')
                            <li>{{ $highlight }}</li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endif
      </div>

      <!-- Right Column: Pricing & CTA -->
      <div class="col-lg-4">
        <div class="sticky-top" style="top: 100px; z-index: 10;">
            <div class="booking-sidebar-card">
                <div class="price-label">Starting from</div>
                <div class="main-price">AED {{ number_format($activityAdultPrice, 2) }}<span>/ Adult</span></div>
                
                <hr style="border-top: 1px solid rgba(255,210,63,0.2); margin: 25px 0;">
                
                <ul class="sidebar-features mb-4">
                    <li><i class="bi bi-patch-check-fill"></i> Instant Confirmation</li>
                    <li><i class="bi bi-patch-check-fill"></i> Professional Guides</li>
                    <li><i class="bi bi-patch-check-fill"></i> Best Price Guarantee</li>
                    <li><i class="bi bi-patch-check-fill"></i> Secure Online Payment</li>
                </ul>

                <button type="button" class="btn-book-large open-booking-modal" 
                    data-id="{{ $activity->activityID }}" 
                    data-name="{{ $activity->activityName }}">
                    BOOK YOUR ADVENTURE
                </button>

                <div class="text-center mt-3 opacity-5" style="font-size: 11px; letter-spacing: 0.5px;">
                    *Prices are subject to availability and group size.
                </div>
            </div>

            <!-- Support Bar -->
            <div class="mt-4 p-4 text-center" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px;">
                <p class="mb-3" style="font-size: 14px; color: #aaa;">Need assistance with your booking?</p>
                <a target="_blank" href="https://api.whatsapp.com/send/?phone=971543651065" class="btn btn-outline-light w-100 d-flex align-items-center justify-content-center gap-3 py-2" style="border-radius: 6px; font-weight: 600;">
                    <i class="fab fa-whatsapp" style="color: #25D366; font-size: 20px;"></i>
                    CHAT ON WHATSAPP
                </a>
            </div>
        </div>
      </div>
    </div>
  </div>
</section>

@include('partials.activity_booking_modal')

<div class="classic-snackbar" id="mainSnackbar"></div>

@if($detail && !empty($detail->activityImage))
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new Swiper(".swiper-container", {
        loop: true,
        speed: 1200,
        parallax: true,
        autoplay: { delay: 4000, disableOnInteraction: false },
        pagination: { el: '.swiper-pagination', clickable: true },
    });
    
    // Auto-remove session snackbars
    const sessionBar = document.getElementById('sessionSnackbar');
    if(sessionBar){
        setTimeout(() => sessionBar.classList.remove('show'), 4000);
    }
});
</script>
@endif

@if(session('success'))
  <div class="classic-snackbar success show" id="sessionSnackbar">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="classic-snackbar failed show" id="sessionSnackbar">{{ session('error') }}</div>
@endif

@include('footer')
