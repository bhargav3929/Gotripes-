@extends('suppliers._layout')

@section('title', 'Preview')

@php
    $photos = array_values((array) $s->photos);
    $hotels = $s->hotels();
    krsort($hotels);
    $tripDays = collect($s->get('tours', []))->pluck('days')->filter()->sort()->values();
    $days = collect($s->get('days', []))->sortBy('day')->values();
    $free = $s->get('child_free_age');
    $pct = $s->get('child_percent');
    $adultAge = $s->get('child_adult_age');
    $paras = array_filter(preg_split('/\R{2,}/', trim((string) $s->get('package.description'))));
@endphp

@section('styles')
    .pv { max-width: 1080px; margin: 0 auto; padding: 32px 16px 96px; }
    .pv-note { display: flex; gap: 16px; align-items: center; justify-content: space-between; flex-wrap: wrap; margin-bottom: 32px; }
    .pv-note p { margin: 0; }
    .site { background: #0f0e0c; color: #ece7da; border-radius: var(--r-lg); overflow: hidden; }
    .hero { position: relative; min-height: 360px; display: flex; align-items: flex-end; padding: 48px 40px; background: #1c1a16 center/cover no-repeat; }
    .hero::before { content: ''; position: absolute; inset: 0; background: linear-gradient(180deg, rgba(15,14,12,.1) 30%, rgba(15,14,12,.92)); }
    .hero > div { position: relative; }
    .kicker { font-size: 13px; letter-spacing: .14em; text-transform: uppercase; color: #f2c200; font-weight: 600; margin-bottom: 8px; }
    .hero h1 { color: #fff; margin: 0; max-width: 760px; }
    .hero .meta { margin-top: 16px; color: #cfc8b6; font-size: 15px; }
    .strip { display: grid; grid-auto-flow: column; grid-auto-columns: minmax(160px, 1fr); gap: 8px; padding: 8px; overflow-x: auto; }
    .strip img { width: 100%; aspect-ratio: 4/3; object-fit: cover; border-radius: var(--r); display: block; }
    .site-body { padding: 48px 40px 56px; display: grid; gap: 56px; }
    .site h2 { color: #fff; font-size: 26px; margin-bottom: 16px; }
    .about p { color: #cfc8b6; max-width: 680px; margin: 0 0 16px; }
    .inc { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
    .inc-box { border: 1px solid #2c2922; border-radius: var(--r-lg); padding: 24px; }
    .inc-box h3 { margin: 0 0 16px; font-size: 13px; letter-spacing: .12em; text-transform: uppercase; color: #a39d8c; font-weight: 600; }
    .inc-box ul { list-style: none; margin: 0; padding: 0; display: grid; gap: 12px; }
    .inc-box li { display: flex; gap: 12px; color: #e3ddcd; }
    .inc-box svg { flex: none; margin-top: 3px; }
    .tl { border-left: 2px solid #3a362c; margin-left: 8px; padding-left: 24px; display: grid; gap: 32px; }
    .tl-day { position: relative; }
    .tl-day::before { content: ''; position: absolute; left: -33px; top: 4px; width: 14px; height: 14px; border-radius: 50%; border: 3px solid #f2c200; background: #0f0e0c; }
    .tl-n { font-size: 13px; letter-spacing: .12em; text-transform: uppercase; color: #f2c200; font-weight: 600; }
    .tl-day h3 { color: #fff; margin: 4px 0 8px; font-size: 19px; font-weight: 700; }
    .tl-day p { margin: 0; color: #cfc8b6; white-space: pre-line; }
    .stays { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px; }
    .stay { border: 1px solid #2c2922; border-radius: var(--r-lg); padding: 24px; }
    .stay .stars { color: #f2c200; letter-spacing: 2px; }
    .stay h3 { color: #fff; margin: 8px 0 4px; font-size: 18px; }
    .stay p { margin: 0; color: #a39d8c; font-size: 14px; }
    .muted { color: #a39d8c; }
    .actions { display: flex; gap: 16px; flex-wrap: wrap; margin-top: 32px; }
    @media (max-width: 720px) {
        .hero { min-height: 280px; padding: 32px 16px; }
        .site-body { padding: 32px 16px 40px; gap: 40px; }
        .inc { grid-template-columns: 1fr; }
    }
@endsection

@section('bar')
    <div class="bar-meta"><span><strong>{{ $invite->supplier_name }}</strong> · Preview</span></div>
@endsection

@section('content')
<main class="pv rise">
    <div class="pv-note notice notice-info">
        <p><strong>This is a preview.</strong> Travellers see your package like this once our team has checked it and put it live. They see prices in AED, with our margin added.</p>
        <a href="{{ route('suppliers.form') }}" class="btn btn-primary" style="padding:12px 16px;">Change what I sent</a>
    </div>

    <article class="site">
        <header class="hero" @if($photos) style="background-image:url('{{ route('suppliers.photo', 0) }}')" @endif>
            <div>
                <div class="kicker">{{ $s->get('package.country') }}@if($tripDays->isNotEmpty()) · {{ $tripDays->join(', ', ' or ') }} days @endif</div>
                <h1>{{ $s->get('package.name') }}</h1>
                @if(filled($s->get('package.places')))<div class="meta">{{ $s->get('package.places') }}</div>@endif
            </div>
        </header>
        @if(count($photos) > 1)
            <div class="strip">
                @foreach(array_slice($photos, 1, 7, true) as $i => $p)
                    <img src="{{ route('suppliers.photo', $i) }}" alt="{{ $p['name'] }}" loading="lazy">
                @endforeach
            </div>
        @endif

        <div class="site-body">
            @if($paras)
                <section class="about">
                    <h2>About this trip</h2>
                    @foreach($paras as $para)<p>{!! nl2br(e(trim($para))) !!}</p>@endforeach
                </section>
            @endif

            @if($included || $excluded)
                <section>
                    <h2>What the price covers</h2>
                    <div class="inc">
                        <div class="inc-box">
                            <h3>Included</h3>
                            @if($included)
                                <ul>@foreach($included as $l)<li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4cc27a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>{{ $l }}</li>@endforeach</ul>
                            @else<p class="muted" style="margin:0;">Nothing listed yet.</p>@endif
                        </div>
                        <div class="inc-box">
                            <h3>Not included</h3>
                            @if($excluded)
                                <ul>@foreach($excluded as $l)<li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#e5675a" stroke-width="3" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>{{ $l }}</li>@endforeach</ul>
                            @else<p class="muted" style="margin:0;">Nothing listed yet.</p>@endif
                        </div>
                    </div>
                </section>
            @endif

            @if($days->isNotEmpty())
                <section>
                    <h2>Your trip, day by day</h2>
                    <div class="tl">
                        @foreach($days as $d)
                            <div class="tl-day">
                                <div class="tl-n">Day {{ $d['day'] }}</div>
                                <h3>{{ $d['title'] }}</h3>
                                @if(filled($d['description'] ?? null))<p>{{ $d['description'] }}</p>@endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($hotels)
                <section>
                    <h2>Where you stay</h2>
                    <div class="stays">
                        @foreach($hotels as $stars => $h)
                            <div class="stay">
                                <div class="stars" aria-label="{{ $stars }} star">{{ str_repeat('★', (int) $stars) }}</div>
                                <h3>{{ $h['name'] }}</h3>
                                @if(filled($h['location'] ?? null))<p>{{ $h['location'] }}</p>@endif
                                <p style="margin-top:8px;">{{ collect($h['meals'] ?? [])->map(fn ($m) => $mealPlans[$m] ?? $m)->join(', ') ?: 'Room only' }}</p>
                                <p>Check-in {{ ($h['check_in'] ?? null) ?: '14:00' }} · check-out {{ ($h['check_out'] ?? null) ?: '12:00' }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($free !== null || $pct !== null || $adultAge !== null)
                <section>
                    <h2>Children</h2>
                    <p class="muted" style="margin:0;">
                        @if($free !== null)Ages 0 to {{ $free }} travel free. @endif
                        @if($pct !== null)Ages {{ $free !== null ? $free + 1 : 0 }}{{ $adultAge ? ' to ' . ($adultAge - 1) : ' and up' }} pay {{ rtrim(rtrim(number_format($pct, 2), '0'), '.') }}% of the adult price. @endif
                        @if($adultAge)From age {{ $adultAge }}, the adult price.@endif
                    </p>
                </section>
            @endif
        </div>
    </article>

    <div class="actions">
        <a href="{{ route('suppliers.form') }}" class="btn btn-primary">Change what I sent</a>
        <form method="POST" action="{{ route('suppliers.leave') }}">@csrf<button class="btn btn-ghost" style="padding:16px 24px;">Close</button></form>
    </div>
</main>
@endsection
