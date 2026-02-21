@include('header')

<style>
    /* =====================================================
       ULTRA-PREMIUM EMIRATES & ACTIVITIES
       ===================================================== */
    .emirates-page-wrapper {
        background: #000 !important;
        min-height: 100vh;
        padding-top: 140px; /* Adjust for header */
        color: #fff;
    }

    /* ─── BREADCRUMB ────────────────────────────────────── */
    .breadcrumb-nav {
        margin-bottom: 50px;
    }
    .breadcrumb-luxury {
        background: rgba(255, 255, 255, 0.03);
        padding: 10px 25px;
        border-radius: 100px;
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 215, 0, 0.15);
        display: inline-flex;
        align-items: center;
        gap: 12px;
        list-style: none;
        text-decoration: none;
    }
    .breadcrumb-luxury a {
        color: #FFD23F;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        text-decoration: none;
        transition: opacity 0.3s;
    }
    .breadcrumb-luxury span {
        color: rgba(255,255,255,0.3);
        font-size: 10px;
    }
    .breadcrumb-luxury .active {
        color: rgba(255,255,255,0.6);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        font-weight: 700;
    }

    /* ─── PAGE HEADER ───────────────────────────────────── */
    .hero-header {
        text-align: center;
        margin-bottom: 80px;
        animation: heroFade 1s ease-out;
    }
    @keyframes heroFade { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    
    .hero-header h1 {
        font-size: clamp(40px, 6vw, 68px);
        font-weight: 950;
        letter-spacing: -2px;
        margin-bottom: 20px;
        line-height: 1;
    }
    .hero-header h1 span {
        background: linear-gradient(135deg, #FFD700 0%, #B8860B 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .hero-header p {
        color: rgba(255,255,255,0.5);
        max-width: 750px;
        margin: 0 auto;
        font-size: 18px;
        font-weight: 400;
        line-height: 1.7;
    }

    /* ─── EMIRATES CINEMATIC GRID ──────────────────────── */
    .emirates-cinematic-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 30px;
        max-width: 1500px;
        margin: 0 auto;
        padding: 0 20px 100px;
    }

    .emirate-card-v2 {
        position: relative;
        height: 520px;
        border-radius: 30px;
        overflow: hidden;
        text-decoration: none !important;
        background: #111;
        border: 1px solid rgba(255,255,255,0.05);
        transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
    }
    .emirate-card-v2:hover {
        transform: translateY(-15px) scale(1.02);
        box-shadow: 0 40px 80px rgba(0,0,0,0.9), 0 0 30px rgba(255, 210, 63, 0.15);
        border-color: rgba(255, 210, 63, 0.4);
    }

    .emirate-v2-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 1.5s ease;
    }
    .emirate-card-v2:hover .emirate-v2-img {
        transform: scale(1.15);
    }

    .emirate-v2-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.2) 40%, rgba(0,0,0,0.95) 100%);
    }

    .emirate-v2-content {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 40px;
        z-index: 2;
    }

    .emirate-v2-badge {
        background: rgba(255, 210, 63, 0.15);
        border: 1px solid rgba(255, 210, 63, 0.3);
        color: #FFD23F;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2px;
        backdrop-filter: blur(10px);
        margin-bottom: 20px;
        display: inline-block;
    }

    .emirate-v2-name {
        font-size: 36px;
        font-weight: 900;
        color: #fff;
        line-height: 1.1;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .emirate-v2-desc {
        font-size: 15px;
        color: rgba(255,255,255,0.5);
        line-height: 1.6;
        opacity: 1;
        transform: none;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: 25px;
        transition: all 0.5s ease;
    }

    .emirate-v2-arrow {
        width: 50px;
        height: 50px;
        background: #FFD23F;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #000;
        font-size: 20px;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .emirate-card-v2:hover .emirate-v2-arrow {
        transform: rotate(-45deg);
        background: #fff;
    }

    /* ─── ACTIVITIES VIEW ───────────────────────────────── */
    .activities-grid-luxury {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 380px));
        gap: 35px;
        justify-content: center;
        max-width: 1400px;
        margin: 0 auto;
        padding-bottom: 100px;
    }

    .activity-v2-card {
        background: #111;
        border-radius: 24px;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.05);
        transition: all 0.4s ease;
        display: flex;
        flex-direction: column;
        height: 540px;
    }
    .activity-v2-card:hover {
        border-color: rgba(255, 210, 63, 0.3);
        box-shadow: 0 25px 50px rgba(0,0,0,0.5);
    }

    .activity-v2-img-container {
        height: 300px;
        overflow: hidden;
        position: relative;
    }
    .activity-v2-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }
    .activity-v2-card:hover .activity-v2-img {
        transform: scale(1.1);
    }

    .activity-v2-tag {
        position: absolute;
        top: 20px;
        right: 20px;
        background: rgba(0,0,0,0.7);
        backdrop-filter: blur(5px);
        color: #FFD23F;
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        border: 1px solid rgba(255,210,63,0.3);
    }

    .activity-v2-info {
        padding: 25px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .activity-v2-title {
        font-size: 22px;
        font-weight: 800;
        color: #fff;
        line-height: 1.3;
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 56px;
    }

    .activity-v2-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 20px;
        border-top: 1px solid rgba(255,255,255,0.05);
    }

    .activity-v2-price {
        display: flex;
        flex-direction: column;
    }
    .price-small { font-size: 10px; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 1px; }
    .price-big { font-size: 24px; font-weight: 900; color: #FFD23F; }

    .book-btn-v2 {
        background: #FFD23F;
        color: #000;
        padding: 12px 25px;
        border-radius: 12px;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 13px;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
    }
    .book-btn-v2:hover {
        background: #fff;
        transform: translateY(-3px);
    }

    /* WhatsApp Button Override */
    .whats-float {
        position: fixed;
        bottom: 30px;
        left: 30px;
        z-index: 9999;
        transition: transform 0.3s;
    }
    .whats-float:hover { transform: scale(1.1); }

    /* ─── MOBILE RESPONSIVE ─────────────────────────── */
    @media (max-width: 991px) {
        .emirates-page-wrapper {
            padding-top: 100px;
        }
        .hero-header {
            margin-bottom: 50px;
        }
        .hero-header h1 {
            font-size: clamp(28px, 8vw, 48px);
            letter-spacing: -1px;
        }
        .hero-header p {
            font-size: 15px;
        }
        .emirates-cinematic-grid {
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
            padding: 0 15px 60px;
        }
        .emirate-card-v2 {
            height: 400px;
        }
        .emirate-v2-content {
            padding: 25px;
        }
        .emirate-v2-name {
            font-size: 28px;
        }
        .activities-grid-luxury {
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
            padding-bottom: 60px;
        }
        .activity-v2-card {
            height: auto;
            min-height: 480px;
        }
    }

    @media (max-width: 575px) {
        .emirates-page-wrapper {
            padding-top: 80px;
        }
        .hero-header {
            margin-bottom: 30px;
        }
        .hero-header h1 {
            font-size: clamp(26px, 10vw, 38px);
            letter-spacing: -0.5px;
        }
        .hero-header p {
            font-size: 14px;
            padding: 0 5px;
        }
        .emirates-cinematic-grid {
            grid-template-columns: 1fr;
            gap: 16px;
            padding: 0 10px 50px;
        }
        .emirate-card-v2 {
            height: 340px;
            border-radius: 20px;
        }
        .emirate-v2-content {
            padding: 20px;
        }
        .emirate-v2-name {
            font-size: 24px;
        }
        .emirate-v2-badge {
            font-size: 9px;
            padding: 4px 12px;
        }
        .activities-grid-luxury {
            grid-template-columns: 1fr;
            gap: 16px;
            padding: 0 10px 50px;
        }
        .activity-v2-card {
            height: auto;
            min-height: 420px;
        }
        .activity-v2-img-container {
            height: 220px;
        }
        .activity-v2-title {
            font-size: 18px;
            min-height: auto;
        }
        .activity-v2-info {
            padding: 18px;
        }
        .price-big {
            font-size: 20px;
        }
        .book-btn-v2 {
            padding: 10px 18px;
            font-size: 12px;
        }
        .whats-float {
            bottom: 15px;
            left: 15px;
        }
        .whats-float img {
            width: 50px;
            height: 50px;
        }
    }
</style>

<div class="emirates-page-wrapper">
    <div class="container">
        @if($emirate)
            {{-- ─── ACTIVITIES VIEW ─── --}}
            <nav class="breadcrumb-nav">
                <div class="breadcrumb-luxury">
                    <a href="{{ route('emirates.index') }}">Emirates</a>
                    <span>/</span>
                    <div class="active">{{ $emirate->emiratesName }}</div>
                </div>
            </nav>

            <div class="hero-header">
                <h1>{{ $emirate->emiratesName }} <span>Experiences</span></h1>
                <p>{{ $emirate->emiratesDescription }}</p>
            </div>

            <div class="activities-grid-luxury">
                @forelse($activities as $activity)
                    <div class="activity-v2-card">
                        <div class="activity-v2-img-container">
                            <span class="activity-v2-tag">Verified Escape</span>
                            <a href="{{ route('activities.detail', ['id' => $activity->activityID, 'emirateId' => $emirate->emiratesID]) }}">
                                <img src="{{ asset($activity->activityImage) }}" 
                                     alt="{{ $activity->activityName }}" 
                                     class="activity-v2-img"
                                     onerror="this.src='https://images.unsplash.com/photo-1544911835-33052671127e?q=80&w=800';">
                            </a>
                        </div>
                        <div class="activity-v2-info">
                            <div>
                                <a href="{{ route('activities.detail', ['id' => $activity->activityID, 'emirateId' => $emirate->emiratesID]) }}" style="text-decoration: none;">
                                    <h3 class="activity-v2-title">{{ $activity->activityName }}</h3>
                                </a>
                                <div class="text-white-50" style="font-size: 14px;">
                                    <i class="bi bi-geo-alt-fill text-warning me-1"></i> {{ $activity->activityLocation }}
                                </div>
                            </div>
                            
                            <div class="activity-v2-footer">
                                <div class="activity-v2-price">
                                    <span class="price-small">Starting From</span>
                                    <span class="price-big">AED {{ number_format($activity->activityPrice, 2) }}</span>
                                </div>
                                <button type="button" class="book-btn-v2 open-booking-modal"
                                    data-id="{{ $activity->activityID }}"
                                    data-name="{{ $activity->activityName }}"
                                    data-price="{{ $activity->activityPrice }}">
                                    Book Now
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <h3 class="text-white-50">Discovery Collection Coming Soon</h3>
                        <p>We are currently vetting the finest experiences in {{ $emirate->emiratesName }}.</p>
                        <a href="{{ route('emirates.index') }}" class="book-btn-v2 mt-4 d-inline-block" style="text-decoration:none;">Explore Other Emirates</a>
                    </div>
                @endforelse
            </div>

        @else
            {{-- ─── EMIRATES SELECTION VIEW ─── --}}
            <div class="hero-header">
                <h1>EXPLORE <span>UAE EMIRATES</span></h1>
                <p>
                    From Abu Dhabi's majestic heritage to Dubai's futuristic skyline, 
                    discover the heart and soul of the UAE through our handpicked regional adventures.
                </p>
            </div>

            <div class="emirates-cinematic-grid">
                @php
                    $manualData = [
                        'Abu Dhabi'      => ['img' => asset('assets/emirates/abudhabi.png'), 'tag' => 'The Imperial Capital'],
                        'Dubai'          => ['img' => asset('assets/emirates/dubai.png'), 'tag' => 'The World City'],
                        'Sharjah'        => ['img' => asset('assets/emirates/sharjah.png'), 'tag' => 'The Cultural Heart'],
                        'Ajman'          => ['img' => asset('assets/emirates/ajman.png'), 'tag' => 'Coastal Sanctuary'],
                        'Umm Al Quwain'  => ['img' => asset('assets/emirates/ummalquwain.png'), 'tag' => 'Timeless Heritage'],
                        'Ras Al Khaimah' => ['img' => asset('assets/emirates/rasalkhaimah.png'), 'tag' => 'Nature\'s Playground'],
                        'Fujairah'       => ['img' => asset('assets/emirates/fujairah.png'), 'tag' => 'Mountain Escape'],
                        'Western Region' => ['img' => asset('assets/emirates/westernregion.png'), 'tag' => 'Desert Gateway']
                    ];
                @endphp

                @forelse($emirates as $emirateItem)
                    @php
                        $name = $emirateItem->emiratesName;
                        $hasManual = isset($manualData[$name]);
                        $displayImg = $hasManual ? $manualData[$name]['img'] : (asset($emirateItem->emiratesImage) ?: 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?q=80&w=800');
                        $displayTag = $hasManual ? $manualData[$name]['tag'] : 'Premium Experience';
                    @endphp
                    <a href="{{ route('emirates.index', ['emiratesID' => $emirateItem->emiratesID]) }}" class="emirate-card-v2">
                        <img src="{{ $displayImg }}" alt="{{ $name }}" class="emirate-v2-img" onerror="this.src='https://images.unsplash.com/photo-1512453979798-5ea266f8880c?q=80&w=800';">
                        <div class="emirate-v2-overlay"></div>
                        
                        <div class="emirate-v2-content">
                            <span class="emirate-v2-badge">
                                {{ $emirateItem->activities_count }} Collections
                            </span>
                            <div class="emirate-v2-name">{{ $name }}</div>
                            <p class="emirate-v2-desc">{{ $emirateItem->emiratesDescription }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-white-50 fw-bold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 2px;">{{ $displayTag }}</span>
                                <div class="emirate-v2-arrow">
                                    <i class="bi bi-arrow-right"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-white-50">New regions launching soon...</p>
                    </div>
                @endforelse
            </div>
        @endif
    </div>
</div>

<!-- WhatsApp Float -->
<div class="whats-float">
    <a href="https://api.whatsapp.com/send/?phone=971543651065&text=Hello+GoTrips" target="_blank">
        <img src="{{ asset('assets/uaeactivities_files/whats.gif') }}" width="65" height="65" alt="WhatsApp">
    </a>
</div>

<div class="classic-snackbar" id="mainSnackbar"></div>

@include('partials.activity_booking_modal')

<script type="text/javascript">
    // Tawk.to and other script initializations
    var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
    (function () {
        var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
        s1.async = true;
        s1.src = 'https://embed.tawk.to/67a073313a8427326078f27b/1ij5c3v7a';
        s1.charset = 'UTF-8';
        s1.setAttribute('crossorigin', '*');
        s0.parentNode.insertBefore(s1, s0);
    })();
</script>

@include('footer')