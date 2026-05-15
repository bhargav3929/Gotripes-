@include('header')

@php
    $tenant = current_company();
    $hasPackages = isset($packages) && $packages->count() > 0;
    $totalCount  = $hasPackages ? collect($packages)->flatten(1)->count() : 0;
@endphp

<main style="background:#000; min-height:100vh; color:#fff; font-family:'Outfit',sans-serif;">
    {{-- Hero --}}
    <section class="tour-hero" style="position:relative; height:60vh; background:url('{{ asset('assets/index_files/533419533.jpg') }}') center/cover no-repeat; display:flex; align-items:center; justify-content:center; text-align:center;">
        <div style="position:absolute; inset:0; background:linear-gradient(180deg, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0.8) 100%);"></div>
        <div class="container" style="position:relative; z-index:2;">
            <p style="font-family:'Satisfy',cursive; font-size:clamp(20px,3vw,32px); color:#FFD23F; margin-bottom:6px;">Curated by {{ $tenant?->name ?? 'us' }}</p>
            <h1 style="font-size:clamp(32px,7vw,64px); font-weight:800; letter-spacing:3px; background:linear-gradient(135deg,#FFD700 0%, #D4AF37 50%, #B8960C 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; text-transform:uppercase; margin-bottom:16px;">Tour Packages</h1>
            <p style="font-size:clamp(14px,2vw,18px); color:#ddd; max-width:720px; margin:0 auto; line-height:1.6;">
                Handpicked tour experiences across the globe. Pick a destination below to see our packages.
            </p>
        </div>
    </section>

    @if($hasPackages)
        @foreach($packages as $country => $items)
            <section style="padding:60px 0; background:{{ $loop->index % 2 === 0 ? '#000' : '#050505' }};">
                <div class="container">
                    <div style="display:flex; align-items:baseline; justify-content:space-between; flex-wrap:wrap; gap:12px; border-bottom:1px solid rgba(255,215,0,0.15); padding-bottom:16px; margin-bottom:32px;">
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
                                            <a class="tour-card-cta" href="{{ route('contact') }}?package={{ urlencode($pkg->title) }}">
                                                Enquire
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
    @else
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
