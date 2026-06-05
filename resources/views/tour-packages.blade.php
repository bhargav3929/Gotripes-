@include('header')

@php
    $tenant = current_company();
    $hasPackages = isset($packages) && $packages->count() > 0;
    $totalCount  = $hasPackages ? collect($packages)->flatten(1)->count() : 0;
    $comingSoon  = $comingSoon ?? collect();
    $hasComingSoon = $comingSoon->count() > 0;
@endphp

<main style="background:#000; min-height:100vh; color:#fff; font-family:'Outfit',sans-serif;">
    {{-- Hero --}}
    <section class="tour-hero" style="position:relative; height:42vh; min-height:300px; background:url('{{ asset('assets/index_files/533419533.jpg') }}') center/cover no-repeat; display:flex; align-items:center; justify-content:center; text-align:center;">
        <div style="position:absolute; inset:0; background:linear-gradient(180deg, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0.8) 100%);"></div>
        <div class="container" style="position:relative; z-index:2;">
            <p style="font-family:'Outfit',sans-serif; font-weight:500; font-style:italic; letter-spacing:1px; font-size:clamp(15px,2vw,20px); color:#FFD23F; margin-bottom:8px; text-transform:uppercase;">Curated by {{ $tenant?->name ?? 'us' }}</p>
            <h1 style="font-size:clamp(32px,7vw,64px); font-weight:800; letter-spacing:3px; background:linear-gradient(135deg,#FFD700 0%, #D4AF37 50%, #B8960C 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; text-transform:uppercase; margin-bottom:16px;">Tour Packages</h1>
            <p style="font-size:clamp(14px,2vw,18px); color:#ddd; max-width:720px; margin:0 auto; line-height:1.6;">
                Handpicked tour experiences across the globe. Pick a destination below to see our packages.
            </p>
        </div>
    </section>

    {{-- Destinations overview: every country stays visible. Completed first, pending = Coming Soon. --}}
    @if($hasPackages || $hasComingSoon)
        <section style="padding:28px 0 4px;">
            <div class="container">
                <h2 class="tp-section-heading">Our Destinations</h2>
                <p class="tp-section-sub">Tap a destination to see its packages — more countries are on the way.</p>
                <div class="tp-dest-grid">
                    @foreach($packages as $country => $items)
                        @php $thumb = optional($items->first())->image; @endphp
                        <a class="tp-dest-card" href="#country-{{ Str::slug($country) }}">
                            <div class="tp-dest-thumb">
                                @if($thumb)
                                    <img src="{{ str_starts_with($thumb, 'http') ? $thumb : asset($thumb) }}" alt="{{ $country }}" loading="lazy">
                                @else
                                    <div class="tp-dest-placeholder"><i class="bi bi-geo-alt"></i></div>
                                @endif
                            </div>
                            <span class="tp-dest-name">{{ $country }}</span>
                            <span class="tp-dest-count">{{ $items->count() }} {{ Str::plural('package', $items->count()) }}</span>
                        </a>
                    @endforeach
                    @foreach($comingSoon as $country)
                        <div class="tp-dest-card tp-dest-soon">
                            <div class="tp-dest-thumb">
                                <div class="tp-dest-placeholder"><i class="bi bi-hourglass-split"></i></div>
                            </div>
                            <span class="tp-dest-name">{{ $country }}</span>
                            <span class="tp-dest-badge">Coming Soon</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if($hasPackages)
        @foreach($packages as $country => $items)
            <section id="country-{{ Str::slug($country) }}" style="scroll-margin-top:90px; padding:36px 0; background:{{ $loop->index % 2 === 0 ? '#000' : '#050505' }};">
                <div class="container">
                    <div style="display:flex; align-items:baseline; justify-content:space-between; flex-wrap:wrap; gap:12px; border-bottom:1px solid rgba(255,215,0,0.15); padding-bottom:12px; margin-bottom:22px;">
                        <h2 class="tp-country-title">{{ $country }}</h2>
                        <span style="color:#777; font-size:14px;">{{ $items->count() }} {{ Str::plural('package', $items->count()) }}</span>
                    </div>

                    <div class="row g-4">
                        @foreach($items as $pkg)
                            <div class="col-lg-4 col-md-6">
                                <article class="tour-card">
                                    <div class="tour-card-image">
                                        @if($pkg->image)
                                            <img src="{{ str_starts_with($pkg->image, 'http') ? $pkg->image : asset($pkg->image) }}" alt="{{ $pkg->title }}" loading="lazy">
                                        @else
                                            <div class="tour-card-placeholder"><i class="bi bi-image"></i></div>
                                        @endif
                                        <span class="tour-card-duration">
                                            <i class="bi bi-clock"></i> {{ $pkg->duration }}
                                        </span>
                                    </div>
                                    <div class="tour-card-body">
                                        <h3 class="tour-card-title">{{ $pkg->title }}</h3>
                                        <p class="tour-card-desc">{{ Str::limit(strip_tags($pkg->description), 140) }}</p>
                                        <div class="tour-card-footer">
                                            <div>
                                                <span class="tour-card-price-label">From</span>
                                                <span class="tour-card-price">AED {{ number_format($pkg->price, 0) }}</span>
                                            </div>
                                            <a class="tour-card-cta" href="{{ route('tour-packages.show', $pkg->id) }}">
                                                View Details
                                                <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endforeach
    @endif

    @if(!$hasPackages && !$hasComingSoon)
        <section style="padding:120px 0; text-align:center;">
            <div class="container">
                <i class="bi bi-compass" style="font-size:64px; color:rgba(255,215,0,0.25); margin-bottom:16px; display:block;"></i>
                <h2 style="font-size:28px; color:#fff;">New tour packages are on the way</h2>
                <p style="color:#888; max-width:560px; margin:12px auto 0;">
                    We're putting together our finest destinations. In the meantime, get in touch and we'll tailor a private itinerary just for you.
                </p>
                <a href="{{ route('contact') }}" class="tour-card-cta" style="display:inline-flex; margin-top:24px;">
                    Talk to us <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </section>
    @endif
</main>

<style>
    .tp-country-title {
        font-size: clamp(24px, 4vw, 36px);
        font-weight: 700;
        letter-spacing: 1px;
        color: #fff;
        margin: 0;
    }
    /* Destinations overview grid */
    .tp-section-heading {
        font-size: clamp(22px, 3.5vw, 32px);
        font-weight: 700;
        color: #fff;
        letter-spacing: 1px;
        margin: 0 0 6px;
    }
    .tp-section-sub { color: #888; font-size: 14px; margin: 0 0 18px; }
    .tp-dest-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 16px;
    }
    .tp-dest-card {
        background: linear-gradient(180deg, #0e0e10 0%, #060607 100%);
        border: 1px solid rgba(255,215,0,0.12);
        border-radius: 14px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        text-decoration: none;
        transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
    }
    a.tp-dest-card:hover {
        transform: translateY(-4px);
        border-color: rgba(255,215,0,0.45);
        box-shadow: 0 14px 32px rgba(0,0,0,0.6);
    }
    .tp-dest-thumb { aspect-ratio: 16/10; background: #111; overflow: hidden; }
    .tp-dest-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .tp-dest-placeholder {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        color: rgba(255,215,0,0.3); font-size: 34px;
    }
    .tp-dest-name {
        color: #fff; font-weight: 600; font-size: 15px;
        padding: 12px 14px 0;
        text-transform: uppercase; letter-spacing: .5px;
    }
    .tp-dest-count { color: #FFD23F; font-size: 12px; padding: 2px 14px 14px; }
    .tp-dest-soon { opacity: .6; }
    .tp-dest-soon .tp-dest-placeholder { color: rgba(255,255,255,0.18); }
    .tp-dest-badge {
        align-self: flex-start;
        margin: 4px 14px 14px;
        background: rgba(255,255,255,0.08);
        color: #bbb; font-size: 11px; font-weight: 600;
        text-transform: uppercase; letter-spacing: 1px;
        padding: 3px 10px; border-radius: 999px;
        border: 1px solid rgba(255,255,255,0.12);
    }
    .tour-card {
        background: linear-gradient(180deg, #0e0e10 0%, #060607 100%);
        border: 1px solid rgba(255,215,0,0.12);
        border-radius: 14px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
        transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease;
    }
    .tour-card:hover {
        transform: translateY(-4px);
        border-color: rgba(255,215,0,0.4);
        box-shadow: 0 18px 40px rgba(0,0,0,0.6);
    }
    .tour-card-image {
        position: relative;
        aspect-ratio: 16/10;
        overflow: hidden;
        background: #111;
    }
    .tour-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .4s ease;
    }
    .tour-card:hover .tour-card-image img { transform: scale(1.04); }
    .tour-card-placeholder {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255,215,0,0.25);
        font-size: 56px;
    }
    .tour-card-duration {
        position: absolute;
        bottom: 12px;
        left: 12px;
        background: rgba(0,0,0,0.7);
        backdrop-filter: blur(6px);
        color: #FFD23F;
        font-size: 12px;
        font-weight: 500;
        padding: 6px 10px;
        border-radius: 999px;
        border: 1px solid rgba(255,215,0,0.3);
    }
    .tour-card-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        flex: 1;
    }
    .tour-card-title {
        font-size: 18px;
        font-weight: 600;
        color: #fff;
        margin: 0;
        line-height: 1.3;
    }
    .tour-card-desc {
        font-size: 13px;
        color: #aaa;
        line-height: 1.55;
        margin: 0;
        flex: 1;
    }
    .tour-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 12px;
        border-top: 1px dashed rgba(255,215,0,0.15);
        margin-top: auto;
    }
    .tour-card-price-label {
        display: block;
        font-size: 11px;
        color: #888;
        letter-spacing: 1px;
        text-transform: uppercase;
    }
    .tour-card-price {
        font-size: 20px;
        font-weight: 700;
        color: #FFD23F;
    }
    .tour-card-cta {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #000;
        background: linear-gradient(135deg,#FFD700 0%,#D4AF37 100%);
        padding: 8px 14px;
        border-radius: 999px;
        text-decoration: none;
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .tour-card-cta:hover {
        transform: translateX(2px);
        box-shadow: 0 6px 18px rgba(212,175,55,0.35);
        color: #000;
        text-decoration: none;
    }
</style>

@include('footer')
