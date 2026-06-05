@include('header')

@php
    use Illuminate\Support\Str;
    // Map country name -> ISO2 so we can show a real flag image (renders everywhere,
    // unlike emoji flags which show as letters on Windows).
    $isoMap = collect(\App\Support\CountryCodes::all())->keyBy('name');
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
                        $iso = strtolower($isoMap[$c['country']]['iso'] ?? '');
                        $flag = $iso ? "https://flagcdn.com/{$iso}.svg" : null;
                    @endphp
                    <a class="ac-card" href="{{ route('emirates.index') }}?country={{ urlencode($c['country']) }}">
                        <div class="ac-flag">
                            @if($flag)
                                <img src="{{ $flag }}" alt="{{ $c['country'] }} flag" loading="lazy">
                            @else
                                <div class="ac-flag-fallback"><i class="bi bi-geo-alt"></i></div>
                            @endif
                        </div>
                        <span class="ac-name">{{ $c['country'] }}</span>
                        <span class="ac-count">{{ $c['activity_count'] }} {{ Str::plural('activity', $c['activity_count']) }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</main>

<style>
    .ac-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(180px,1fr)); gap:18px; max-width:1100px; margin:0 auto; }
    .ac-card {
        background:linear-gradient(180deg,#0e0e10 0%,#070708 100%);
        border:1px solid rgba(255,215,0,0.14);
        border-radius:16px; overflow:hidden; text-decoration:none;
        display:flex; flex-direction:column;
        transition:transform .2s ease, border-color .2s ease, box-shadow .2s ease;
    }
    .ac-card:hover { transform:translateY(-4px); border-color:rgba(255,215,0,0.5); box-shadow:0 16px 36px rgba(0,0,0,0.6); }
    .ac-flag { aspect-ratio:3/2; background:#111; overflow:hidden; }
    .ac-flag img { width:100%; height:100%; object-fit:cover; display:block; }
    .ac-flag-fallback { width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:rgba(255,215,0,0.3); font-size:38px; }
    .ac-name { color:#fff; font-weight:700; font-size:16px; padding:14px 16px 0; }
    .ac-count { color:#FFD23F; font-size:13px; padding:3px 16px 16px; }
</style>

@include('footer')
