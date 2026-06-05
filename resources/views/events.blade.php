@include('header')

@php
    $level9Wa = current_company()?->getSetting('level9_whatsapp', '');
    $trending = current_company()?->getSetting('events_trending_enabled', false);
    $waMsg = rawurlencode("Hi LEVEL9 — I'm interested in the FIFA World Cup 2026 luxury experience. Please share package details.");
    $waLink = $level9Wa ? "https://wa.me/{$level9Wa}?text={$waMsg}" : url('/contact-us');
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@200;300;400;500;600;700;800&display=swap');

    .evt * { box-sizing: border-box; }
    .evt {
        --gold: #FFD700;
        --gold-2: #FF8A00;
        --grad: linear-gradient(135deg, #FFD700 0%, #FF8A00 100%);
        --bg: #050505;
        --card: #0c0c0c;
        --border: #1e1e1e;
        --muted: #8a8a8a;
        --light: #efefef;
        font-family: 'Outfit', sans-serif;
        background: var(--bg);
        color: var(--light);
    }

    /* HERO */
    .evt-hero {
        position: relative;
        padding: 190px 24px 110px;
        text-align: center;
        overflow: hidden;
        background:
            radial-gradient(1100px 520px at 50% -10%, rgba(255,170,0,0.14), transparent 60%),
            linear-gradient(180deg, #000 0%, #0a0a0a 100%);
    }
    .evt-kicker {
        display: inline-flex; align-items: center; gap: 8px;
        font-size: 0.78rem; font-weight: 700; letter-spacing: 0.18em; text-transform: uppercase;
        color: var(--gold); border: 1px solid rgba(255,215,0,0.3); border-radius: 999px;
        padding: 7px 16px; margin-bottom: 26px;
    }
    .evt-kicker .dot { width: 7px; height: 7px; border-radius: 50%; background: var(--gold); animation: evtPulse 1.6s infinite; }
    @keyframes evtPulse { 0%{box-shadow:0 0 0 0 rgba(255,170,0,.6)} 70%{box-shadow:0 0 0 8px rgba(255,170,0,0)} 100%{box-shadow:0 0 0 0 rgba(255,170,0,0)} }
    .evt-hero h1 {
        font-size: clamp(2.6rem, 7vw, 5.4rem); font-weight: 800; line-height: 0.98; letter-spacing: -0.03em;
        margin: 0 0 18px;
    }
    .evt-hero h1 .hl { background: var(--grad); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }
    .evt-hero p.sub { max-width: 640px; margin: 0 auto 36px; color: var(--muted); font-weight: 300; font-size: 1.15rem; line-height: 1.6; }

    .evt-cta-row { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
    .evt-btn {
        display: inline-flex; align-items: center; gap: 9px; padding: 15px 30px; border-radius: 12px;
        font-weight: 700; font-size: 1rem; text-decoration: none; transition: transform .14s ease, box-shadow .14s ease; cursor: pointer;
    }
    .evt-btn-primary { background: var(--grad); color: #111; box-shadow: 0 10px 30px rgba(255,138,0,.25); }
    .evt-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 16px 40px rgba(255,138,0,.35); color: #111; }
    .evt-btn-ghost { background: transparent; color: var(--light); border: 1px solid var(--border); }
    .evt-btn-ghost:hover { border-color: var(--gold); color: var(--gold); }

    /* SECTIONS */
    .evt-section { max-width: 1140px; margin: 0 auto; padding: 96px 24px; }
    .evt-section-head { max-width: 680px; margin: 0 0 56px; }
    .evt-section-head .tag { font-size: 0.76rem; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; color: var(--gold); }
    .evt-section-head h2 { font-size: clamp(1.9rem, 4vw, 2.8rem); font-weight: 800; letter-spacing: -0.02em; margin: 12px 0 14px; line-height: 1.05; }
    .evt-section-head p { color: var(--muted); font-weight: 300; font-size: 1.05rem; line-height: 1.6; margin: 0; }

    .evt-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    @media (max-width: 900px) { .evt-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 560px) { .evt-grid { grid-template-columns: 1fr; } }
    .evt-card {
        background: var(--card); border: 1px solid var(--border); border-radius: 16px; padding: 30px 26px;
        transition: transform .16s ease, border-color .16s ease;
    }
    .evt-card:hover { transform: translateY(-4px); border-color: rgba(255,215,0,0.35); }
    .evt-card .ic {
        width: 52px; height: 52px; border-radius: 12px; display: grid; place-items: center;
        background: rgba(255,215,0,0.08); color: var(--gold); font-size: 1.5rem; margin-bottom: 18px;
    }
    .evt-card h3 { font-size: 1.18rem; font-weight: 700; margin: 0 0 8px; letter-spacing: -0.01em; }
    .evt-card p { color: var(--muted); font-weight: 300; font-size: 0.95rem; line-height: 1.6; margin: 0; }

    /* CITIES */
    .evt-cities { background: linear-gradient(180deg, #000 0%, #0b0b0b 100%); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
    .evt-city-row { display: flex; flex-wrap: wrap; gap: 12px; }
    .evt-city {
        flex: 1 1 auto; min-width: 150px; background: var(--card); border: 1px solid var(--border); border-radius: 12px;
        padding: 22px 24px;
    }
    .evt-city .nm { font-size: 1.1rem; font-weight: 700; }
    .evt-city .ct { color: var(--muted); font-size: 0.85rem; margin-top: 3px; }

    /* CLOSING CTA */
    .evt-final { text-align: center; padding: 110px 24px; background: radial-gradient(900px 400px at 50% 120%, rgba(255,170,0,0.12), transparent 60%); }
    .evt-final h2 { font-size: clamp(2rem, 5vw, 3.4rem); font-weight: 800; letter-spacing: -0.02em; margin: 0 0 16px; }
    .evt-final p { color: var(--muted); font-weight: 300; max-width: 560px; margin: 0 auto 34px; font-size: 1.1rem; }
    .evt-brand { margin-top: 40px; font-size: 0.82rem; letter-spacing: 0.16em; text-transform: uppercase; color: #666; }
    .evt-brand b { color: var(--gold); font-weight: 700; }
</style>

<div class="evt">

    <!-- HERO -->
    <section class="evt-hero">
        @if($trending)
        <span class="evt-kicker"><span class="dot"></span> Trending now</span>
        @else
        <span class="evt-kicker">Signature event</span>
        @endif
        <h1>FIFA World Cup 2026<br><span class="hl">The Luxury Experience</span></h1>
        <p class="sub">VIP tickets, private jets, penthouse suites, chauffeurs and exclusive after-parties — the World Cup, done the right way. Curated end-to-end by LEVEL9 CONCIERGERIE across the United States, Mexico and Canada.</p>
        <div class="evt-cta-row">
            <a href="{{ $waLink }}" target="_blank" rel="noopener" class="evt-btn evt-btn-primary"><i class="bi bi-whatsapp"></i> Enquire on WhatsApp</a>
            <a href="#evt-included" class="evt-btn evt-btn-ghost">See what's included</a>
        </div>
    </section>

    <!-- WHAT'S INCLUDED -->
    <section class="evt-section" id="evt-included">
        <div class="evt-section-head">
            <span class="tag">The full experience</span>
            <h2>Everything in between, handled</h2>
            <p>One concierge, every detail — from the stadium seats to the suite you return to. Whether you're watching in New York, LA, Dallas or Mexico City, we've got you covered.</p>
        </div>
        <div class="evt-grid">
            <div class="evt-card"><div class="ic"><i class="bi bi-ticket-perforated"></i></div><h3>VIP Match Tickets</h3><p>Premium seating and hospitality access to the matches that matter most — including the biggest fixtures of the tournament.</p></div>
            <div class="evt-card"><div class="ic"><i class="bi bi-airplane-engines"></i></div><h3>Private Jets</h3><p>Fly between host cities on your schedule. Seamless private aviation across the US, Mexico and Canada.</p></div>
            <div class="evt-card"><div class="ic"><i class="bi bi-building"></i></div><h3>Penthouse Suites</h3><p>Stay in the finest penthouses and luxury residences, positioned in the heart of each host city.</p></div>
            <div class="evt-card"><div class="ic"><i class="bi bi-car-front"></i></div><h3>Chauffeur Service</h3><p>Private chauffeurs on call throughout your stay — to the stadium, dinner, and everywhere between.</p></div>
            <div class="evt-card"><div class="ic"><i class="bi bi-stars"></i></div><h3>Exclusive After-Parties</h3><p>Access to invitation-only celebrations and the after-parties where the night really begins.</p></div>
            <div class="evt-card"><div class="ic"><i class="bi bi-gem"></i></div><h3>End-to-End Concierge</h3><p>A dedicated concierge plans, books and adapts everything around you — so all you do is enjoy the football.</p></div>
        </div>
    </section>

    <!-- HOST CITIES -->
    <section class="evt-cities">
        <div class="evt-section" style="padding-top:80px; padding-bottom:80px;">
            <div class="evt-section-head">
                <span class="tag">Where you'll be</span>
                <h2>Across three nations</h2>
                <p>Based in Toronto with solid connections throughout the US and Mexico — wherever the tournament takes you.</p>
            </div>
            <div class="evt-city-row">
                <div class="evt-city"><div class="nm">New York</div><div class="ct">United States</div></div>
                <div class="evt-city"><div class="nm">Los Angeles</div><div class="ct">United States</div></div>
                <div class="evt-city"><div class="nm">Dallas</div><div class="ct">United States</div></div>
                <div class="evt-city"><div class="nm">Mexico City</div><div class="ct">Mexico</div></div>
                <div class="evt-city"><div class="nm">Toronto</div><div class="ct">Canada · home base</div></div>
            </div>
        </div>
    </section>

    <!-- CLOSING CTA -->
    <section class="evt-final">
        <h2>Experience the World Cup<br>the right way</h2>
        <p>Serious packages for serious fans. If you, your clients, or anyone you know wants in — let's talk.</p>
        <a href="{{ $waLink }}" target="_blank" rel="noopener" class="evt-btn evt-btn-primary"><i class="bi bi-whatsapp"></i> Start your enquiry</a>
        <div class="evt-brand">In partnership with <b>LEVEL9 CONCIERGERIE</b></div>
    </section>

</div>

@include('footer')
