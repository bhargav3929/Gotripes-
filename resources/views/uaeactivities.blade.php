@include('header')

@php
// Helper: get flag emoji for a country name
$flag = fn($c) => ($flagMap[$c]['flag'] ?? '🌍');

// Currency to display per activity
$currency = fn($a) => $a->activityCurrency ?: 'AED';
@endphp

<style>
    html, body { background: #000 !important; }

    /* ─── Country Switcher (modes 2 & 3) ─── */
    .country-switcher { text-align: center; margin-bottom: 48px; }

    /* Mode 2 — two flag tabs */
    .ctabs { display: inline-flex; gap: 0; border: 1px solid rgba(255,215,0,0.2); border-radius: 10px; overflow: hidden; }
    .ctab {
        background: transparent; border: none; color: rgba(255,255,255,0.55);
        padding: 14px 32px; font-size: 17px; font-weight: 700; cursor: pointer;
        transition: all 0.2s; letter-spacing: 0.01em; display: flex; align-items: center; gap: 10px;
    }
    .ctab:not(:last-child) { border-right: 1px solid rgba(255,215,0,0.2); }
    .ctab.active { background: rgba(255,215,0,0.12); color: #FFD700; }
    .ctab:hover:not(.active) { background: rgba(255,255,255,0.04); color: rgba(255,255,255,0.8); }
    .ctab .cflag { font-size: 24px; line-height: 1; }
    .ctab .ccount { font-size: 12px; font-weight: 500; color: rgba(255,255,255,0.3); margin-left: 4px; }
    .ctab.active .ccount { color: rgba(255,215,0,0.5); }

    /* Mode 3 — country grid */
    .country-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px; max-width: 960px; margin: 0 auto;
    }
    .country-card {
        background: linear-gradient(145deg, #161616, #0d0d0d);
        border: 1px solid rgba(255,215,0,0.08); border-radius: 14px;
        padding: 28px 20px; text-align: center; cursor: pointer;
        transition: all 0.3s cubic-bezier(0.23,1,0.32,1);
    }
    .country-card:hover {
        border-color: rgba(255,215,0,0.4); transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.6), 0 0 16px rgba(255,215,0,0.1);
    }
    .country-card .cc-flag   { font-size: 52px; line-height: 1; display: block; margin-bottom: 14px; }
    .country-card .cc-name   { font-size: 15px; font-weight: 700; color: #fff; margin-bottom: 6px; }
    .country-card .cc-count  { font-size: 12px; color: rgba(255,215,0,0.6); font-weight: 500; }

    /* Back button (mode 3) */
    .back-btn {
        display: inline-flex; align-items: center; gap: 8px;
        background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12);
        color: rgba(255,255,255,0.7); padding: 10px 20px; border-radius: 8px;
        font-size: 14px; font-weight: 600; cursor: pointer; margin-bottom: 32px;
        transition: all 0.2s;
    }
    .back-btn:hover { background: rgba(255,255,255,0.1); color: #fff; }

    /* Selected country header (mode 3) */
    .selected-country-header {
        text-align: center; margin-bottom: 40px;
    }
    .selected-country-header .sc-flag { font-size: 48px; display: block; margin-bottom: 10px; }
    .selected-country-header h2 {
        font-size: 32px; font-weight: 800; color: #fff; margin: 0;
        letter-spacing: -0.02em;
    }

    /* ─── Activity Cards ─── */
    .activities-grid {
        display: grid; grid-template-columns: repeat(3, 1fr);
        gap: 30px; max-width: 1200px; margin: 0 auto; padding: 0 15px;
    }
    @media (max-width: 1199px) { .activities-grid { grid-template-columns: repeat(2,1fr); gap: 25px; } }
    @media (max-width: 767px)  { .activities-grid { grid-template-columns: 1fr; gap: 20px; } }

    .blog_inner_page { margin-bottom: 40px; height: 100%; }

    .activity-box-container {
        display: flex; flex-direction: column; height: 340px;
        background: linear-gradient(180deg, #161616 0%, #0c0c0c 100%);
        border-radius: 16px; overflow: hidden;
        transition: all 0.5s cubic-bezier(0.23,1,0.32,1);
        border: 1px solid rgba(255,215,0,0.05);
        box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        opacity: 0; animation: fadeInUp 0.6s ease forwards;
    }
    .activity-box-container:hover {
        transform: translateY(-12px);
        box-shadow: 0 25px 50px rgba(0,0,0,0.8), 0 0 20px rgba(255,215,0,0.15);
        border-color: rgba(255,215,0,0.4);
    }
    .box_images { width: 100%; height: 220px; background: #000; overflow: hidden; flex-shrink: 0; position: relative; }
    .box_images::after {
        content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 40%;
        background: linear-gradient(to top, rgba(0,0,0,0.6), transparent); z-index: 1;
    }
    .box_images img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.8s cubic-bezier(0.2,0,0.2,1); }
    .activity-box-container:hover .box_images img { transform: scale(1.15); }

    .activity-badge {
        position: absolute; top: 14px; left: 14px;
        background: rgba(0,0,0,0.6); backdrop-filter: blur(8px);
        color: #FFD700; padding: 5px 12px; border-radius: 50px;
        font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;
        z-index: 5; border: 1px solid rgba(255,215,0,0.2);
    }

    .blog_box { padding: 20px; flex: 1; display: flex; flex-direction: column; justify-content: space-between; }
    .blog_box h3 {
        font-size: 17px; font-weight: 700; color: #fff; margin: 0 0 6px;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        line-height: 1.3; letter-spacing: -0.2px;
    }
    .location-tag { color: rgba(255,255,255,0.5); font-size: 13px; margin: 0 0 16px; display: flex; align-items: center; gap: 6px; }
    .location-icon { color: #FFD23F; font-size: 12px; }
    .author {
        margin-top: auto; padding-top: 16px;
        border-top: 1px solid rgba(255,215,0,0.12) !important;
        display: flex !important; align-items: center !important; justify-content: space-between !important;
    }
    .price-wrapper { display: flex; flex-direction: column; }
    .price-label { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.4); margin-bottom: 2px; }
    .price { font-size: 20px !important; font-weight: 800 !important; color: #FFD700 !important; text-shadow: 0 0 20px rgba(255,215,0,0.2); }

    .book-now-overlay {
        background: linear-gradient(135deg, #FFD700 0%, #FFB800 100%);
        color: #000 !important; padding: 7px 16px; border-radius: 6px;
        font-weight: 800; font-size: 12px; text-transform: uppercase; letter-spacing: 0.8px;
        cursor: pointer; transition: all 0.3s ease; border: none;
        box-shadow: 0 4px 14px rgba(0,0,0,0.4);
    }
    .book-now-overlay:hover { background: #fff; color: #000 !important; transform: translateY(-3px); }

    /* Page header */
    .page-hero { text-align: center; padding: 60px 20px 40px; }
    .page-hero h1 { font-size: 42px; font-weight: 800; color: #fff; margin: 0 0 12px; letter-spacing: -0.03em; }
    .page-hero p  { font-size: 17px; color: rgba(255,255,255,0.5); max-width: 560px; margin: 0 auto; line-height: 1.6; }
    @media(max-width:767px) { .page-hero h1 { font-size: 28px; } .page-hero p { font-size: 15px; } }

    /* UAE intro block (single-country mode) */
    .static-content {
        color: #ccc; font-size: 17px; line-height: 1.8;
        max-width: 960px; margin: 0 auto 48px;
        padding: 28px 40px;
        background: rgba(24,24,24,0.8); border-radius: 12px;
        border-left: 4px solid #FFD23F;
    }
    .static-content h2 { color: #FFD23F; text-align: center; margin-bottom: 20px; font-size: 26px; font-weight: 800; }
    @media(max-width:767px) { .static-content { padding: 20px; font-size: 15px; } }

    /* Snackbar */
    .classic-snackbar {
        position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%);
        z-index: 20000; color: #111; border-radius: 5px; font-size: 16px; font-weight: 600;
        padding: 12px 25px; text-align: center; display: none; opacity: 0; transition: opacity 0.4s;
        box-shadow: 0 3px 15px rgba(0,0,0,0.3); background: #ffef8e; border-bottom: 4px solid #FFD700;
    }
    .classic-snackbar.success { background: #50cb4a; color: #111; border-bottom: 4px solid #9be58d; }
    .classic-snackbar.failed  { background: #fa5353; color: #fff; border-bottom: 4px solid #c44f4f; }
    .classic-snackbar.show    { display: block; opacity: 1; }

    .whats { width: 60px; position: fixed; bottom: 3%; left: 1%; z-index: 10000; border-radius: 90px; overflow: hidden; transition: transform 0.3s ease; }
    .whats:hover { transform: scale(1.1); }
    .whats img { height: 60px; width: 60px; }

    @keyframes fadeInUp { from { opacity:0; transform: translateY(30px); } to { opacity:1; transform: translateY(0); } }
    .activity-item:nth-child(1) .activity-box-container { animation-delay: 0.05s; }
    .activity-item:nth-child(2) .activity-box-container { animation-delay: 0.1s; }
    .activity-item:nth-child(3) .activity-box-container { animation-delay: 0.15s; }
    .activity-item:nth-child(4) .activity-box-container { animation-delay: 0.2s; }
    .activity-item:nth-child(5) .activity-box-container { animation-delay: 0.25s; }
    .activity-item:nth-child(6) .activity-box-container { animation-delay: 0.3s; }

    .country-panel { display: none; }
    .country-panel.active { display: block; }
</style>

<section class="about industries" style="padding-top: 0; background: #000;">
    <div class="container">

    @if($countryCount === 0)
        {{-- ─── EMPTY STATE ─── --}}
        <div class="page-hero">
            <h1>Activities</h1>
            <p>No activities available yet. Check back soon!</p>
        </div>

    @elseif($countryCount === 1)
        {{-- ─── MODE 1: single country ─── --}}
        @php $singleCountry = $countries[0]; $activities = $sorted[$singleCountry]; @endphp

        @if($singleCountry === 'United Arab Emirates')
        <div class="static-content">
            <h2>Discover Amazing UAE Activities</h2>
            <p style="margin-bottom: 16px;">The UAE offers a wide range of activities, from desert safaris and dune bashing to luxury shopping in world-class malls. Visitors can explore iconic landmarks like the Burj Khalifa and Sheikh Zayed Grand Mosque.</p>
            <p style="margin-bottom: 0;">Water sports, cultural festivals, and vibrant souks — there's something for every traveler. Choose your adventure and book instantly.</p>
        </div>
        @else
        <div class="page-hero">
            <h1>{{ $flag($singleCountry) }} Activities in {{ $singleCountry }}</h1>
            <p>Discover curated experiences and book your next adventure.</p>
        </div>
        @endif

        <div class="activities-grid">
            @forelse($activities as $activity)
            <div class="activity-item">
                <div class="blog_inner_page">
                    <div class="activity-box-container position-relative">
                        <div class="activity-badge">{{ $flag($singleCountry) }} {{ $singleCountry }}</div>
                        <a href="/dubai-global-village?id={{ $activity->activityID }}" style="text-decoration:none;">
                            <div class="box_images">
                                <img src="{{ !empty($activity->activityImage) ? (str_starts_with($activity->activityImage,'http') ? $activity->activityImage : asset($activity->activityImage)) : 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?q=80&w=800' }}"
                                     alt="{{ $activity->activityName }}" loading="lazy"
                                     onerror="if(!this.dataset.retried){this.dataset.retried='1';this.src='https://images.unsplash.com/photo-1518684079-3c830dcef090?q=80&w=800';}">
                            </div>
                        </a>
                        <div class="blog_box">
                            <a href="/dubai-global-village?id={{ $activity->activityID }}" style="text-decoration:none;">
                                <h3>{{ $activity->activityName }}</h3>
                                <div class="location-tag">
                                    <i class="fas fa-map-marker-alt location-icon"></i>
                                    {{ $activity->activityLocation }}
                                </div>
                            </a>
                            <div class="author">
                                <div class="price-wrapper">
                                    <span class="price-label">Starting From</span>
                                    <span class="price">{{ $currency($activity) }} {{ number_format($activity->activityPrice, 2) }}</span>
                                </div>
                                <button type="button" class="book-now-overlay open-booking-modal"
                                    data-id="{{ $activity->activityID }}"
                                    data-name="{{ $activity->activityName }}"
                                    data-price="{{ $activity->activityPrice }}"
                                    data-currency="{{ $currency($activity) }}">
                                    Book Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div style="text-align:center; color:#fff; font-size:18px; grid-column:1/-1; padding:60px 0;">
                <i class="fas fa-exclamation-circle" style="font-size:48px; color:#FFD23F; display:block; margin-bottom:20px;"></i>
                <p>No activities found at the moment.</p>
            </div>
            @endforelse
        </div>

    @elseif($countryCount === 2)
        {{-- ─── MODE 2: two flag tabs ─── --}}
        <div class="page-hero">
            <h1>Discover Activities</h1>
            <p>Select a destination and find your perfect experience.</p>
        </div>

        <div class="country-switcher">
            <div class="ctabs">
                @foreach($countries as $i => $cName)
                <button class="ctab {{ $i === 0 ? 'active' : '' }}"
                        onclick="switchTab('{{ Str::slug($cName) }}', this)">
                    <span class="cflag">{{ $flag($cName) }}</span>
                    {{ $cName }}
                    <span class="ccount">({{ $sorted[$cName]->count() }})</span>
                </button>
                @endforeach
            </div>
        </div>

        @foreach($countries as $i => $cName)
        <div id="panel-{{ Str::slug($cName) }}" class="country-panel {{ $i === 0 ? 'active' : '' }}">
            <div class="activities-grid">
                @foreach($sorted[$cName] as $activity)
                <div class="activity-item">
                    <div class="blog_inner_page">
                        <div class="activity-box-container position-relative">
                            <div class="activity-badge">{{ $flag($cName) }} {{ $cName }}</div>
                            <a href="/dubai-global-village?id={{ $activity->activityID }}" style="text-decoration:none;">
                                <div class="box_images">
                                    <img src="{{ !empty($activity->activityImage) ? (str_starts_with($activity->activityImage,'http') ? $activity->activityImage : asset($activity->activityImage)) : 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?q=80&w=800' }}"
                                         alt="{{ $activity->activityName }}" loading="lazy"
                                         onerror="if(!this.dataset.retried){this.dataset.retried='1';this.src='https://images.unsplash.com/photo-1518684079-3c830dcef090?q=80&w=800';}">
                                </div>
                            </a>
                            <div class="blog_box">
                                <a href="/dubai-global-village?id={{ $activity->activityID }}" style="text-decoration:none;">
                                    <h3>{{ $activity->activityName }}</h3>
                                    <div class="location-tag">
                                        <i class="fas fa-map-marker-alt location-icon"></i>
                                        {{ $activity->activityLocation }}
                                    </div>
                                </a>
                                <div class="author">
                                    <div class="price-wrapper">
                                        <span class="price-label">Starting From</span>
                                        <span class="price">{{ $currency($activity) }} {{ number_format($activity->activityPrice, 2) }}</span>
                                    </div>
                                    <button type="button" class="book-now-overlay open-booking-modal"
                                        data-id="{{ $activity->activityID }}"
                                        data-name="{{ $activity->activityName }}"
                                        data-price="{{ $activity->activityPrice }}"
                                        data-currency="{{ $currency($activity) }}">
                                        Book Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

    @else
        {{-- ─── MODE 3: 3+ countries — flag grid first ─── --}}
        <div class="page-hero">
            <h1>Discover Activities Worldwide</h1>
            <p>Choose a destination to explore curated local experiences.</p>
        </div>

        {{-- Country selection grid --}}
        <div id="countrySelectionGrid">
            <div class="country-grid">
                @foreach($countries as $cName)
                <div class="country-card" onclick="openCountry('{{ Str::slug($cName) }}', '{{ addslashes($cName) }}', '{{ $flag($cName) }}')">
                    <span class="cc-flag">{{ $flag($cName) }}</span>
                    <div class="cc-name">{{ $cName }}</div>
                    <div class="cc-count">{{ $sorted[$cName]->count() }} {{ Str::plural('activity', $sorted[$cName]->count()) }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Activities section (hidden until a country is picked) --}}
        <div id="activitiesSection" style="display:none;">
            <button class="back-btn" onclick="backToCountries()">
                <i class="fas fa-arrow-left"></i> All Destinations
            </button>

            <div class="selected-country-header" id="selectedHeader">
                <span class="sc-flag" id="selectedFlag"></span>
                <h2 id="selectedName"></h2>
            </div>

            @foreach($countries as $cName)
            <div id="panel-{{ Str::slug($cName) }}" class="country-panel">
                <div class="activities-grid">
                    @foreach($sorted[$cName] as $activity)
                    <div class="activity-item">
                        <div class="blog_inner_page">
                            <div class="activity-box-container position-relative">
                                <div class="activity-badge">{{ $flag($cName) }} {{ $cName }}</div>
                                <a href="/dubai-global-village?id={{ $activity->activityID }}" style="text-decoration:none;">
                                    <div class="box_images">
                                        <img src="{{ !empty($activity->activityImage) ? (str_starts_with($activity->activityImage,'http') ? $activity->activityImage : asset($activity->activityImage)) : 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?q=80&w=800' }}"
                                             alt="{{ $activity->activityName }}" loading="lazy"
                                             onerror="if(!this.dataset.retried){this.dataset.retried='1';this.src='https://images.unsplash.com/photo-1518684079-3c830dcef090?q=80&w=800';}">
                                    </div>
                                </a>
                                <div class="blog_box">
                                    <a href="/dubai-global-village?id={{ $activity->activityID }}" style="text-decoration:none;">
                                        <h3>{{ $activity->activityName }}</h3>
                                        <div class="location-tag">
                                            <i class="fas fa-map-marker-alt location-icon"></i>
                                            {{ $activity->activityLocation }}
                                        </div>
                                    </a>
                                    <div class="author">
                                        <div class="price-wrapper">
                                            <span class="price-label">Starting From</span>
                                            <span class="price">{{ $currency($activity) }} {{ number_format($activity->activityPrice, 2) }}</span>
                                        </div>
                                        <button type="button" class="book-now-overlay open-booking-modal"
                                            data-id="{{ $activity->activityID }}"
                                            data-name="{{ $activity->activityName }}"
                                            data-price="{{ $activity->activityPrice }}"
                                            data-currency="{{ $currency($activity) }}">
                                            Book Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    @endif

    </div>
</section>

<img src="{{ asset('assets/uaeactivities_files/matomo.php') }}" alt="" style="border:0;" data-no-retina="">
<script src="{{ asset('assets/uaeactivities_files/twk-main.js.download') }}" charset="UTF-8" crossorigin="*"></script>
<script src="{{ asset('assets/uaeactivities_files/twk-vendor.js.download') }}" charset="UTF-8" crossorigin="*"></script>
<script src="{{ asset('assets/uaeactivities_files/twk-chunk-vendors.js.download') }}" charset="UTF-8" crossorigin="*"></script>
<script src="{{ asset('assets/uaeactivities_files/twk-chunk-common.js.download') }}" charset="UTF-8" crossorigin="*"></script>
<script src="{{ asset('assets/uaeactivities_files/twk-runtime.js.download') }}" charset="UTF-8" crossorigin="*"></script>
@include('partials.activity_booking_modal')

<div class="classic-snackbar" id="mainSnackbar"></div>

<script>
// Mode 2 — tab switching
function switchTab(slug, btn) {
    document.querySelectorAll('.ctab').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');
    document.querySelectorAll('.country-panel').forEach(function(p) { p.classList.remove('active'); });
    var panel = document.getElementById('panel-' + slug);
    if (panel) panel.classList.add('active');
}

// Mode 3 — country selection
var _activeSlug = null;
function openCountry(slug, name, flag) {
    // Hide all panels then show the right one
    document.querySelectorAll('.country-panel').forEach(function(p) { p.classList.remove('active'); });
    var panel = document.getElementById('panel-' + slug);
    if (panel) panel.classList.add('active');

    document.getElementById('selectedFlag').textContent = flag;
    document.getElementById('selectedName').textContent = name;
    document.getElementById('countrySelectionGrid').style.display = 'none';
    document.getElementById('activitiesSection').style.display    = '';
    _activeSlug = slug;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
function backToCountries() {
    document.getElementById('activitiesSection').style.display    = 'none';
    document.getElementById('countrySelectionGrid').style.display = '';
    if (_activeSlug) {
        var panel = document.getElementById('panel-' + _activeSlug);
        if (panel) panel.classList.remove('active');
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Tawk.to
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/67a073313a8427326078f27b/1ij5c3v7a';
    s1.charset='UTF-8'; s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
})();
</script>

@include('footer')
