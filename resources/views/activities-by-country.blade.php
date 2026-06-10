@include('header')

@php use Illuminate\Support\Str; @endphp

<main style="background:#000; min-height:100vh; color:#fff; font-family:'Outfit',sans-serif; padding-bottom:60px;">
    <div class="container" style="max-width:1140px; padding-top:32px;">
        <a href="{{ route('emirates.index') }}" style="color:#FFD23F; text-decoration:none; font-size:14px;">
            <i class="bi bi-arrow-left"></i> All destinations
        </a>

        <div style="text-align:center; margin:18px 0 30px;">
            <h1 style="font-size:clamp(26px,5vw,44px); font-weight:800; letter-spacing:1px; background:linear-gradient(135deg,#FFD700 0%,#D4AF37 50%,#B8960C 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; text-transform:uppercase; margin:0;">{{ $country }} <span style="-webkit-text-fill-color:#fff;">Experiences</span></h1>
        </div>

        @if($activities->count())
            <div class="abc-grid">
                @foreach($activities as $activity)
                    @php
                        $img = $activity->activityImage;
                        $src = $img ? (str_starts_with($img,'http') ? $img : asset($img)) : null;
                        $emName = optional($activity->emirate)->emiratesName;
                        $link = $emName
                            ? route('activities.detail.slug', ['emirateSlug' => Str::slug($emName), 'activitySlug' => Str::slug($activity->activityName)])
                            : '#';
                    @endphp
                    <a class="abc-card" href="{{ $link }}">
                        <div class="abc-img">
                            @if($src)
                                <img src="{{ $src }}" alt="{{ $activity->activityName }}" loading="lazy">
                            @else
                                <div class="abc-ph"><i class="bi bi-image"></i></div>
                            @endif
                        </div>
                        <div class="abc-body">
                            <h3 class="abc-title">{{ $activity->activityName }}</h3>
                            @if($activity->activityLocation)
                                <p class="abc-loc"><i class="bi bi-geo-alt"></i> {{ $activity->activityLocation }}</p>
                            @endif
                            <div class="abc-foot">
                                <span class="abc-price">{{ $activity->activityCurrency ?: 'AED' }} {{ number_format((float) $activity->activityPrice, 0) }}</span>
                                <span class="abc-cta">View <i class="bi bi-arrow-right"></i></span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div style="text-align:center; padding:80px 0; color:#888;">
                <i class="bi bi-compass" style="font-size:48px; color:rgba(255,215,0,0.25);"></i>
                <p style="margin-top:14px;">New experiences in {{ $country }} are coming soon.</p>
            </div>
        @endif
    </div>
</main>

<style>
    .abc-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(260px,1fr)); gap:20px; }
    .abc-card { background:linear-gradient(180deg,#0e0e10 0%,#070708 100%); border:1px solid rgba(255,215,0,0.12); border-radius:14px; overflow:hidden; text-decoration:none; display:flex; flex-direction:column; transition:transform .2s ease, border-color .2s ease, box-shadow .2s ease; }
    .abc-card:hover { transform:translateY(-4px); border-color:rgba(255,215,0,0.45); box-shadow:0 16px 36px rgba(0,0,0,0.6); }
    .abc-img { aspect-ratio:16/10; background:#111; overflow:hidden; }
    .abc-img img { width:100%; height:100%; object-fit:cover; display:block; }
    .abc-ph { width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:rgba(255,215,0,0.25); font-size:44px; }
    .abc-body { padding:16px; display:flex; flex-direction:column; gap:8px; flex:1; }
    .abc-title { font-size:17px; font-weight:600; color:#fff; margin:0; }
    .abc-loc { font-size:13px; color:#999; margin:0; }
    .abc-foot { display:flex; align-items:center; justify-content:space-between; margin-top:auto; padding-top:10px; border-top:1px dashed rgba(255,215,0,0.15); }
    .abc-price { color:#FFD23F; font-weight:700; font-size:18px; }
    .abc-cta { color:#FFD23F; font-size:13px; font-weight:600; }
</style>

@include('footer')
