@include('header')

@php
    use Illuminate\Support\Str;
    use App\Support\CountryCodes;
    $isoMap = collect(CountryCodes::all())->keyBy('name');
@endphp

<main style="background:#000; min-height:100vh; color:#fff; font-family:'Outfit',sans-serif; padding-bottom:60px;">
    <section style="position:relative; padding:64px 0 24px; text-align:center;">
        <div class="container">
            <p style="font-family:'Satisfy',cursive; font-size:clamp(18px,2.5vw,28px); color:#FFD23F; margin-bottom:4px;">Explore activities</p>
            <h1 style="font-size:clamp(28px,6vw,52px); font-weight:800; letter-spacing:2px; background:linear-gradient(135deg,#FFD700 0%,#D4AF37 50%,#B8960C 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; text-transform:uppercase; margin:0;">Choose a Destination</h1>
            <p style="color:#aaa; font-size:15px; max-width:620px; margin:12px auto 0;">Pick a country to see the activities and experiences available there.</p>
        </div>
    </section>

    <section style="padding:8px 0 40px;">
        <div class="container">
            <div class="ac-grid">
                @foreach($countries as $c)
                    @php
                        $cName   = $c['country'];
                        $iso     = strtolower($isoMap[$cName]['iso'] ?? '');
                        $flagSrc = $iso ? "https://flagcdn.com/w640/{$iso}.png" : null;
                        $emoji   = collect(CountryCodes::all())->firstWhere('name', $cName)['flag'] ?? null;
                    @endphp
                    <a class="ac-card" href="{{ route('emirates.index') }}?country={{ urlencode($cName) }}">
                        <div class="ac-flag">
                            @if($flagSrc)
                                <img src="{{ $flagSrc }}"
                                     srcset="https://flagcdn.com/w320/{{ $iso }}.png 320w, https://flagcdn.com/w640/{{ $iso }}.png 640w"
                                     sizes="(max-width:480px) 320px, 640px"
                                     alt="{{ $cName }} flag" loading="lazy">
                            @elseif($emoji)
                                <div class="ac-flag-emoji">{{ $emoji }}</div>
                            @else
                                <div class="ac-flag-fallback"><i class="bi bi-geo-alt"></i></div>
                            @endif
                        </div>
                        <div class="ac-info">
                            <span class="ac-name">{{ $cName }}</span>
                            <span class="ac-count">{{ $c['activity_count'] }} {{ Str::plural('activity', $c['activity_count']) }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</main>

<style>
    .ac-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
        max-width: 1100px;
        margin: 0 auto;
    }
    .ac-card {
        background: linear-gradient(180deg, #0e0e10 0%, #070708 100%);
        border: 1px solid rgba(255,215,0,0.14);
        border-radius: 16px;
        overflow: hidden;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
    }
    .ac-card:hover {
        transform: translateY(-5px);
        border-color: rgba(255,215,0,0.55);
        box-shadow: 0 20px 40px rgba(0,0,0,0.7);
    }
    .ac-flag {
        width: 100%;
        aspect-ratio: 3/2;
        background: #111;
        overflow: hidden;
        position: relative;
    }
    .ac-flag img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .3s ease;
    }
    .ac-card:hover .ac-flag img {
        transform: scale(1.04);
    }
    .ac-flag-emoji {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 80px;
        line-height: 1;
    }
    .ac-flag-fallback {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255,215,0,0.3);
        font-size: 48px;
    }
    .ac-info {
        padding: 14px 16px 16px;
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .ac-name {
        color: #fff;
        font-weight: 700;
        font-size: 16px;
    }
    .ac-count {
        color: #FFD23F;
        font-size: 13px;
    }
</style>

@include('footer')
