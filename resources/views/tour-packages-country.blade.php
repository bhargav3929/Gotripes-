@include('header')

@php
    $tenant      = current_company();
    $pkgCount    = $packages->count();
    $priceFrom   = $minPrice ? 'AED ' . number_format($minPrice, 0) : null;
    $priceTo     = $maxPrice && $maxPrice != $minPrice ? ' – AED ' . number_format($maxPrice, 0) : '';
    $slug        = \Illuminate\Support\Str::slug($countryName);
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@200;300;400;500;600;700;800&display=swap');

    .tpc * { box-sizing: border-box; font-family: 'Outfit', sans-serif; }
    .tpc { background: #000; min-height: 100vh; color: #fff; }

    /* ── HERO ── */
    .tpc-hero {
        position: relative;
        min-height: 420px;
        display: flex;
        align-items: flex-end;
        overflow: hidden;
    }
    .tpc-hero-bg {
        position: absolute; inset: 0;
        background-size: cover;
        background-position: center;
        background-color: #080808;
        transition: transform 8s ease;
    }
    .tpc-hero:hover .tpc-hero-bg { transform: scale(1.03); }
    .tpc-hero-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(0deg, rgba(0,0,0,0.92) 0%, rgba(0,0,0,0.55) 50%, rgba(0,0,0,0.3) 100%);
    }
    .tpc-hero-inner {
        position: relative; z-index: 2;
        width: 100%; max-width: 1200px;
        margin: 0 auto;
        padding: 48px 24px 40px;
    }
    .tpc-back {
        display: inline-flex; align-items: center; gap: 7px;
        font-size: 13px; font-weight: 500; color: rgba(255,255,255,.7);
        text-decoration: none; margin-bottom: 32px;
        transition: color .15s ease;
    }
    .tpc-back:hover { color: #FFD700; text-decoration: none; }
    .tpc-flag { font-size: 64px; line-height: 1; display: block; margin-bottom: 16px; }
    .tpc-hero-title {
        font-size: clamp(2.4rem, 6vw, 4.2rem); font-weight: 800;
        letter-spacing: -0.02em; line-height: 1.0; margin: 0 0 20px;
        color: #fff;
    }
    .tpc-hero-title span {
        background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%);
        -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;
    }
    .tpc-meta-row {
        display: flex; flex-wrap: wrap; gap: 12px; align-items: center;
    }
    .tpc-pill {
        display: inline-flex; align-items: center; gap: 7px;
        background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.12);
        border-radius: 999px; padding: 7px 16px;
        font-size: 13px; font-weight: 500; color: #ddd;
    }
    .tpc-pill i { color: #FFD700; font-size: 14px; }
    .tpc-pill.gold { background: rgba(255,215,0,.12); border-color: rgba(255,215,0,.3); color: #FFD700; }

    /* ── PACKAGES GRID ── */
    .tpc-body { max-width: 1200px; margin: 0 auto; padding: 48px 24px 80px; }
    .tpc-section-label {
        font-size: 0.72rem; font-weight: 700; letter-spacing: 0.14em;
        text-transform: uppercase; color: #FFD700; margin: 0 0 24px;
    }

    .tpc-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
    @media (max-width: 900px)  { .tpc-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 560px)  { .tpc-grid { grid-template-columns: 1fr; } }

    .tpc-card {
        background: linear-gradient(160deg, #0e0e10, #080809);
        border: 1px solid rgba(255,215,0,.12);
        border-radius: 16px; overflow: hidden;
        display: flex; flex-direction: column;
        transition: transform .22s ease, border-color .22s ease, box-shadow .22s ease;
    }
    .tpc-card:hover {
        transform: translateY(-5px);
        border-color: rgba(255,215,0,.45);
        box-shadow: 0 20px 48px rgba(0,0,0,.7);
    }
    .tpc-card-img {
        position: relative; aspect-ratio: 16/10; overflow: hidden; background: #111;
    }
    .tpc-card-img img {
        width: 100%; height: 100%; object-fit: cover; display: block;
        transition: transform .4s ease;
    }
    .tpc-card:hover .tpc-card-img img { transform: scale(1.05); }
    .tpc-card-placeholder {
        height: 100%; display: flex; align-items: center; justify-content: center;
        color: rgba(255,215,0,.2); font-size: 56px;
    }
    .tpc-duration-badge {
        position: absolute; bottom: 10px; left: 10px;
        background: rgba(0,0,0,.75); backdrop-filter: blur(6px);
        color: #FFD700; font-size: 11px; font-weight: 600;
        padding: 5px 10px; border-radius: 999px;
        border: 1px solid rgba(255,215,0,.3);
        display: flex; align-items: center; gap: 5px;
    }
    .tpc-type-badge {
        position: absolute; top: 10px; right: 10px;
        font-size: 10px; font-weight: 700; letter-spacing: .8px; text-transform: uppercase;
        padding: 4px 10px; border-radius: 999px;
    }
    .tpc-type-badge.enquire { background: rgba(59,130,246,.25); color: #93c5fd; border: 1px solid rgba(59,130,246,.4); }
    .tpc-type-badge.purchase { background: rgba(34,197,94,.2); color: #86efac; border: 1px solid rgba(34,197,94,.35); }

    .tpc-card-body { padding: 20px; display: flex; flex-direction: column; gap: 10px; flex: 1; }
    .tpc-card-title { font-size: 17px; font-weight: 700; color: #fff; margin: 0; line-height: 1.3; letter-spacing: -.01em; }
    .tpc-card-desc { font-size: 13px; color: #999; line-height: 1.6; margin: 0; flex: 1; }
    .tpc-card-footer {
        display: flex; align-items: center; justify-content: space-between;
        padding-top: 14px; border-top: 1px dashed rgba(255,215,0,.15); margin-top: auto;
    }
    .tpc-price-label { display: block; font-size: 10px; color: #666; letter-spacing: 1px; text-transform: uppercase; }
    .tpc-price { font-size: 20px; font-weight: 800; color: #FFD700; letter-spacing: -.01em; }
    .tpc-cta {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 13px; font-weight: 700; color: #000;
        background: linear-gradient(135deg, #FFD700, #D4AF37);
        padding: 9px 16px; border-radius: 999px; text-decoration: none;
        transition: transform .14s ease, box-shadow .14s ease;
    }
    .tpc-cta:hover { transform: translateX(2px); box-shadow: 0 6px 18px rgba(212,175,55,.4); color: #000; text-decoration: none; }

    /* ── EMPTY STATE ── */
    .tpc-empty { text-align: center; padding: 96px 24px; }
    .tpc-empty i { font-size: 56px; color: rgba(255,215,0,.2); display: block; margin-bottom: 20px; }
    .tpc-empty h2 { font-size: 22px; font-weight: 700; color: #fff; margin: 0 0 10px; }
    .tpc-empty p  { color: #666; font-size: 14px; max-width: 480px; margin: 0 auto; }

    /* ── RESPONSIVE HERO ── */
    @media (max-width: 768px) {
        .tpc-hero { min-height: 320px; }
        .tpc-flag { font-size: 48px; }
    }
</style>

<div class="tpc">

    {{-- HERO --}}
    <section class="tpc-hero">
        <div class="tpc-hero-bg"
             @if($heroImage)
             style="background-image: url('{{ str_starts_with($heroImage,'http') ? $heroImage : asset($heroImage) }}')"
             @else
             style="background: radial-gradient(ellipse 900px 500px at 60% 40%, rgba(255,170,0,.14), transparent 70%), linear-gradient(160deg,#080808,#0d0d0d)"
             @endif
        ></div>
        <div class="tpc-hero-overlay"></div>
        <div class="tpc-hero-inner">
            <a href="{{ route('tour-packages') }}" class="tpc-back">
                <i class="bi bi-arrow-left"></i> All destinations
            </a>
            <span class="tpc-flag">{{ $flag }}</span>
            <h1 class="tpc-hero-title">
                <span>{{ $countryName }}</span> Tour Packages
            </h1>
            <div class="tpc-meta-row">
                <span class="tpc-pill">
                    <i class="bi bi-suitcase-lg"></i>
                    {{ $pkgCount }} {{ Str::plural('package', $pkgCount) }} available
                </span>
                @if($priceFrom)
                <span class="tpc-pill gold">
                    <i class="bi bi-tag"></i>
                    {{ $priceFrom }}{{ $priceTo }}
                </span>
                @endif
                @if($tenant)
                <span class="tpc-pill">
                    <i class="bi bi-buildings"></i>
                    Curated by {{ $tenant->name }}
                </span>
                @endif
            </div>
        </div>
    </section>

    {{-- PACKAGES GRID --}}
    <div class="tpc-body">
        @if($packages->isNotEmpty())
            <p class="tpc-section-label">Choose your package</p>
            <div class="tpc-grid">
                @foreach($packages as $pkg)
                <article class="tpc-card">
                    <div class="tpc-card-img">
                        @if($pkg->image)
                            <img src="{{ str_starts_with($pkg->image,'http') ? $pkg->image : asset($pkg->image) }}"
                                 alt="{{ $pkg->title }}" loading="lazy">
                        @else
                            <div class="tpc-card-placeholder"><i class="bi bi-image"></i></div>
                        @endif
                        <span class="tpc-duration-badge">
                            <i class="bi bi-clock"></i> {{ $pkg->duration }}
                        </span>
                        <span class="tpc-type-badge {{ $pkg->package_type === 'purchase' ? 'purchase' : 'enquire' }}">
                            {{ $pkg->package_type === 'purchase' ? 'Book Online' : 'Enquire' }}
                        </span>
                    </div>
                    <div class="tpc-card-body">
                        <h2 class="tpc-card-title">{{ $pkg->title }}</h2>
                        <p class="tpc-card-desc">{{ Str::limit(strip_tags($pkg->description), 150) }}</p>
                        <div class="tpc-card-footer">
                            <div>
                                <span class="tpc-price-label">From</span>
                                <span class="tpc-price">AED {{ number_format($pkg->price, 0) }}</span>
                            </div>
                            <a href="{{ route('tour-packages.show', $pkg->id) }}" class="tpc-cta">
                                View Details <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        @else
            <div class="tpc-empty">
                <i class="bi bi-compass"></i>
                <h2>No packages yet for {{ $countryName }}</h2>
                <p>Our team is putting together tours for this destination. Get in touch and we'll build a private itinerary.</p>
                <a href="{{ route('contact') }}" class="tpc-cta" style="display:inline-flex;margin-top:24px;">
                    Contact us <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        @endif
    </div>

</div>

@include('footer')
