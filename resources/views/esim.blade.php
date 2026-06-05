@include('header')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    /* --- VARIABLES --- */
    :root {
        --c-gold: #FFD700;
        --c-gold-secondary: #D4AF37;
        --c-gold-dim: #B8960C;
        --c-gold-gradient: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%);
        --c-dark-bg: #050505;
        --c-card-bg: #0b0b0b;
        --c-input-bg: #111;
        --c-input-border: #222;
        --c-text-muted: #888;
        --c-text-light: #eee;
        --c-border-subtle: rgba(255, 215, 0, 0.08);
        --c-border-accent: #C9A227;
    }

    * { box-sizing: border-box; }

    /* ============================================================
       PARTNER CTA BANNER — used on homepage & esim page
       ============================================================ */
    .esim-partner-cta-banner {
        position: relative;
        max-width: 1240px;
        margin: 24px auto;
        padding: 0 24px;
    }
    .epcb-inner {
        position: relative;
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.08) 0%, rgba(212, 175, 55, 0.04) 100%);
        border: 1px solid rgba(255, 215, 0, 0.25);
        border-radius: 20px;
        padding: 28px 36px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        overflow: hidden;
        backdrop-filter: blur(8px);
        box-shadow: 0 12px 40px rgba(255, 215, 0, 0.06), inset 0 1px 0 rgba(255, 255, 255, 0.04);
    }
    .epcb-inner::before {
        content: '';
        position: absolute;
        top: -50%; left: -10%;
        width: 60%; height: 200%;
        background: linear-gradient(115deg, transparent 30%, rgba(255, 215, 0, 0.18) 50%, transparent 70%);
        animation: epcb-shine 6s ease-in-out infinite;
        pointer-events: none;
    }
    @keyframes epcb-shine {
        0%, 100% { transform: translateX(-30%) skewX(-15deg); opacity: 0; }
        50% { transform: translateX(180%) skewX(-15deg); opacity: 1; }
    }
    .epcb-content { display: flex; align-items: center; gap: 22px; flex: 1; min-width: 0; position: relative; z-index: 1; }
    .epcb-icon {
        flex-shrink: 0;
        width: 64px; height: 64px;
        border-radius: 16px;
        background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%);
        color: #0a0a0a;
        font-size: 26px;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 8px 24px rgba(255, 215, 0, 0.35);
    }
    .epcb-text { min-width: 0; }
    .epcb-eyebrow {
        font-family: 'Outfit', sans-serif;
        font-size: 11px; font-weight: 700;
        letter-spacing: 2.2px;
        color: #FFD700;
        text-transform: uppercase;
        display: block; margin-bottom: 4px;
    }
    .epcb-title {
        font-family: 'Outfit', sans-serif;
        font-size: 22px; font-weight: 800;
        letter-spacing: -0.01em;
        color: #ffffff;
        margin: 0 0 4px 0;
        line-height: 1.2;
    }
    .epcb-sub {
        font-size: 14px;
        color: rgba(255, 255, 255, 0.65);
        margin: 0;
        line-height: 1.5;
    }
    .epcb-btn {
        flex-shrink: 0;
        position: relative;
        z-index: 1;
        display: inline-flex; align-items: center; gap: 10px;
        background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%);
        color: #0a0a0a;
        font-family: 'Outfit', sans-serif;
        font-size: 14px; font-weight: 700;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        padding: 14px 28px;
        border-radius: 50px;
        text-decoration: none;
        transition: all 0.25s ease;
        white-space: nowrap;
        box-shadow: 0 6px 22px rgba(255, 215, 0, 0.25);
    }
    .epcb-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 28px rgba(255, 215, 0, 0.45);
        color: #0a0a0a;
    }
    @media (max-width: 768px) {
        .epcb-inner { flex-direction: column; align-items: flex-start; padding: 22px 20px; }
        .epcb-content { gap: 14px; }
        .epcb-icon { width: 52px; height: 52px; font-size: 22px; }
        .epcb-title { font-size: 18px; }
        .epcb-sub { font-size: 13px; }
        .epcb-btn { width: 100%; justify-content: center; padding: 12px 18px; }
    }

    /* ============================================================
       ANIMATED GOLDEN SECTION TITLES (between image sections)
       ============================================================ */
    .esim-golden-divider {
        max-width: 1240px;
        margin: 36px auto;
        padding: 32px 24px;
        text-align: center;
        position: relative;
    }
    .esim-golden-divider::before,
    .esim-golden-divider::after {
        content: '';
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        width: 220px; height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 215, 0, 0.4), transparent);
    }
    .esim-golden-divider::before { top: 0; }
    .esim-golden-divider::after { bottom: 0; }
    .esim-golden-eyebrow {
        font-family: 'Outfit', sans-serif;
        font-size: 12px; font-weight: 600;
        letter-spacing: 4px;
        color: rgba(255, 215, 0, 0.7);
        text-transform: uppercase;
        margin-bottom: 14px;
        display: block;
        animation: gold-fade 1.2s ease-out;
    }
    .esim-golden-title {
        font-family: 'Outfit', 'Plus Jakarta Sans', sans-serif;
        font-size: clamp(34px, 5vw, 64px);
        font-weight: 800;
        letter-spacing: -0.025em;
        line-height: 1.05;
        margin: 0 auto;
        max-width: 1000px;
        background: linear-gradient(120deg,
            #B8860B 0%,
            #FFD700 25%,
            #FFFAE5 50%,
            #FFD700 75%,
            #B8860B 100%);
        background-size: 200% auto;
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: gold-shimmer 4s linear infinite;
    }
    .esim-golden-sub {
        margin: 18px auto 0;
        max-width: 680px;
        font-size: 16px; line-height: 1.6;
        color: rgba(255, 255, 255, 0.55);
        font-weight: 400;
    }
    @keyframes gold-shimmer {
        0% { background-position: 0% center; }
        100% { background-position: 200% center; }
    }
    @keyframes gold-fade {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @media (max-width: 768px) {
        .esim-golden-divider { margin: 24px auto; padding: 24px 16px; }
        .esim-golden-divider::before, .esim-golden-divider::after { width: 140px; }
        .esim-golden-sub { font-size: 14px; }
    }

    /* ============================================================
       STEP 2 (PACKAGE COLLECTION) — LARGER, MORE READABLE
       ============================================================ */
    #esimStep2 .esim-selected-name { font-size: 22px; font-weight: 700; }
    #esimStep2 .esim-bundle-tab { font-size: 16px; font-weight: 600; padding: 14px 22px; }
    #esimStep2 .esim-bundles-list .bundle-card,
    #esimStep2 .esim-bundles-list > * { font-size: 16px; }
    #esimStep2 .bundle-name, #esimStep2 .esim-bundle-name { font-size: 19px; font-weight: 700; }
    #esimStep2 .bundle-data, #esimStep2 .esim-bundle-data { font-size: 28px; font-weight: 800; letter-spacing: -0.02em; }
    #esimStep2 .bundle-validity, #esimStep2 .esim-bundle-validity,
    #esimStep2 .bundle-meta, #esimStep2 .esim-bundle-meta { font-size: 15px; }
    #esimStep2 .bundle-price, #esimStep2 .esim-bundle-price { font-size: 26px; font-weight: 800; }
    #esimStep2 .esim-continue-btn { font-size: 17px; padding: 18px 34px; }

    /* ============================================================
       STEP 3 (CHECKOUT) — UNIFORM INPUTS, LARGER FONTS, COUNTRY CODE
       ============================================================ */
    #esimStep3 .esim-checkout-title { font-size: 30px; font-weight: 800; letter-spacing: -0.02em; }
    #esimStep3 .esim-checkout-subtitle { font-size: 16px; line-height: 1.55; }
    #esimStep3 .esim-label { font-size: 14px; font-weight: 600; letter-spacing: 0.4px; text-transform: uppercase; color: #cfcfcf; margin-bottom: 8px; display: block; }
    #esimStep3 .esim-input,
    #esimStep3 .esim-input-wrapper input,
    #esimStep3 .esim-input-wrapper select {
        font-size: 16px !important;
        height: 56px !important;
        padding: 14px 16px 14px 44px !important;
        border-radius: 12px !important;
        width: 100%;
    }
    #esimStep3 .esim-form-group { margin-bottom: 18px; }
    #esimStep3 .esim-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }
    @media (max-width: 640px) {
        #esimStep3 .esim-form-row { grid-template-columns: 1fr; }
    }
    /* Phone with country code dropdown */
    #esimStep3 .esim-phone-wrap {
        display: grid;
        grid-template-columns: 130px 1fr;
        gap: 10px;
    }
    #esimStep3 .esim-phone-wrap .esim-input-wrapper {
        position: relative;
    }
    #esimStep3 .esim-phone-cc {
        appearance: none;
        -webkit-appearance: none;
        background: var(--c-input-bg, #111);
        color: #fff;
        border: 1px solid var(--c-input-border, #222);
        border-radius: 12px;
        height: 56px;
        font-size: 16px;
        padding: 0 32px 0 16px !important;
        font-weight: 600;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23FFD700' stroke-width='3'><polyline points='6 9 12 15 18 9'/></svg>");
        background-repeat: no-repeat;
        background-position: right 12px center;
    }
    #esimStep3 .esim-phone-cc:focus {
        outline: none;
        border-color: #FFD700;
    }
    /* Side image to fill any remaining vertical space if checkout shorter than viewport */
    #esimStep3 .esim-checkout-grid {
        align-items: stretch;
    }

    html {
        background-color: #000 !important;
        overflow-x: hidden;
    }

    body {
        background-color: #000 !important;
        overflow-x: hidden;
        width: 100%;
    }

    /* ============================================================
       HERO SECTION — PREMIUM REDESIGN V2
       ============================================================ */
    .esim-hero {
        /* Pull the section up so the dark sky / signal-curve area of the
           image sits behind the header and is hidden; the visible region starts
           directly at the dense landmark / SIM-card band. */
        margin-top: -148px;       /* tuck exactly behind the 148px sticky header */
        padding: 180px 32px 28px; /* 148 behind header + 32 gap above content */
        min-height: 0;            /* content defines height — no empty artwork band */
        display: flex;
        align-items: center;
        background-color: #000;
        background-image: url('{{ asset('assets/esim/hero-esim-network.jpg') }}');
        background-image: -webkit-image-set(
            url('{{ asset('assets/esim/hero-esim-network.webp') }}') type('image/webp'),
            url('{{ asset('assets/esim/hero-esim-network.jpg') }}') type('image/jpeg')
        );
        background-image: image-set(
            url('{{ asset('assets/esim/hero-esim-network.webp') }}') type('image/webp'),
            url('{{ asset('assets/esim/hero-esim-network.jpg') }}') type('image/jpeg')
        );
        background-size: cover;
        background-position: right center;
        background-repeat: no-repeat;
        font-family: 'Outfit', sans-serif;
        position: relative;
        overflow: hidden;
    }

    /* Dark gradient overlay for text legibility — strong on the left, fading right.
       This sits ON TOP of the image but UNDER the content. */
    .esim-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            /* Tiny gold starfield filling the dark upper regions (around the SIM + upper-right gap) */
            url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1600 800' preserveAspectRatio='xMidYMid slice'>\
<g fill='%23FFD700'>\
<circle cx='710' cy='90' r='1.4' opacity='0.55'/>\
<circle cx='765' cy='150' r='1.1' opacity='0.45'/>\
<circle cx='820' cy='60' r='1.7' opacity='0.6'/>\
<circle cx='905' cy='115' r='1.2' opacity='0.5'/>\
<circle cx='980' cy='75' r='1.5' opacity='0.55'/>\
<circle cx='1050' cy='160' r='1.1' opacity='0.45'/>\
<circle cx='1120' cy='95' r='1.6' opacity='0.6'/>\
<circle cx='1190' cy='180' r='1.2' opacity='0.5'/>\
<circle cx='1260' cy='110' r='1.4' opacity='0.55'/>\
<circle cx='1340' cy='55' r='1.7' opacity='0.6'/>\
<circle cx='1410' cy='140' r='1.1' opacity='0.45'/>\
<circle cx='1480' cy='90' r='1.5' opacity='0.55'/>\
<circle cx='1540' cy='200' r='1.3' opacity='0.5'/>\
<circle cx='670' cy='220' r='1.1' opacity='0.4'/>\
<circle cx='745' cy='280' r='1.3' opacity='0.45'/>\
<circle cx='870' cy='240' r='1.5' opacity='0.55'/>\
<circle cx='1040' cy='300' r='1.2' opacity='0.5'/>\
<circle cx='1200' cy='250' r='1.4' opacity='0.55'/>\
<circle cx='1370' cy='220' r='1.6' opacity='0.6'/>\
<circle cx='1500' cy='320' r='1.2' opacity='0.5'/>\
<circle cx='800' cy='400' r='1.0' opacity='0.4'/>\
<circle cx='1100' cy='430' r='1.2' opacity='0.45'/>\
<circle cx='1330' cy='420' r='1.4' opacity='0.5'/>\
<circle cx='1480' cy='480' r='1.1' opacity='0.45'/>\
</g>\
<g fill='none' stroke='%23FFD700' stroke-width='0.7' opacity='0.25'>\
<path d='M 1000 380 Q 1180 260 1380 200' stroke-dasharray='2 6'/>\
<path d='M 1000 380 Q 1220 220 1500 130' stroke-dasharray='2 6'/>\
<path d='M 1000 380 Q 920 230 850 100' stroke-dasharray='2 6'/>\
<path d='M 1000 380 Q 800 280 680 180' stroke-dasharray='2 6'/>\
<path d='M 1000 380 Q 1100 180 1240 60' stroke-dasharray='2 6'/>\
</g>\
</svg>") center center / cover no-repeat,
            linear-gradient(
                90deg,
                rgba(0, 0, 0, 0.85) 0%,
                rgba(0, 0, 0, 0.55) 35%,
                rgba(0, 0, 0, 0.15) 60%,
                rgba(0, 0, 0, 0) 80%
            );
        pointer-events: none;
        z-index: 0;
    }

    /* Warm gold pools that flood the formerly-black regions (upper-left of SIM, upper-right corner, bottom-left vignette) */
    .esim-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 520px 340px at 70% 20%, rgba(255, 195, 0, 0.10) 0%, transparent 70%),
            radial-gradient(ellipse 480px 320px at 92% 14%, rgba(255, 180, 0, 0.09) 0%, transparent 70%),
            radial-gradient(circle 600px at 8% 92%, rgba(255, 180, 0, 0.10) 0%, transparent 65%);
        pointer-events: none;
        z-index: 0;
    }

    .esim-hero-inner {
        max-width: 1280px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        position: relative;
        z-index: 1;
        width: 100%;
    }

    .esim-hero-left {
        flex: 0 1 540px;
        max-width: 540px;
        min-width: 0;
        animation: esimFadeInUp 0.8s ease forwards;
    }

    .esim-hero-right { display: none; } /* legacy markup hook — image is now section bg */

    @keyframes esimFadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes esimFadeInRight {
        from { opacity: 0; transform: translateX(30px); }
        to { opacity: 1; transform: translateX(0); }
    }

    .esim-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.12) 0%, rgba(255, 180, 0, 0.06) 100%);
        border: 1px solid rgba(255, 215, 0, 0.2);
        color: var(--c-gold);
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 2px;
        text-transform: uppercase;
        padding: 10px 18px;
        border-radius: 100px;
        margin-bottom: 16px;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 20px rgba(255, 215, 0, 0.1);
    }

    .esim-hero-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        background: var(--c-gold);
        border-radius: 50%;
        animation: esimPulse 2s ease-in-out infinite;
    }

    @keyframes esimPulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(0.8); }
    }

    .esim-hero-title {
        font-size: clamp(32px, 4vw, 48px);
        font-weight: 800;
        color: #fff;
        margin: 0 0 12px;
        letter-spacing: -1px;
        line-height: 1.05;
    }

    .esim-hero-title-highlight {
        background: var(--c-gold-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .esim-hero-subtitle {
        font-size: 15px;
        font-weight: 400;
        color: rgba(255, 255, 255, 0.65);
        max-width: 480px;
        margin: 0 0 20px;
        line-height: 1.6;
    }

    /* Primary CTA */
    .esim-hero-cta {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 20px;
    }

    .esim-btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: var(--c-gold-gradient);
        color: #000;
        padding: 14px 28px;
        border-radius: 100px;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
        box-shadow: 0 8px 32px rgba(255, 215, 0, 0.25);
        border: none;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .esim-btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s ease;
    }

    .esim-btn-primary:hover::before {
        left: 100%;
    }

    .esim-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 40px rgba(255, 215, 0, 0.4);
    }

    .esim-btn-primary:active {
        transform: translateY(0);
    }

    .esim-btn-secondary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: transparent;
        color: rgba(255, 255, 255, 0.7);
        padding: 14px 24px;
        border-radius: 100px;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        border: 1px solid rgba(255, 255, 255, 0.15);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .esim-btn-secondary:hover {
        color: #fff;
        border-color: rgba(255, 255, 255, 0.3);
        background: rgba(255, 255, 255, 0.05);
    }

    /* Trust Row - Modern Pills */
    .esim-trust-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .esim-trust-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 16px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 100px;
        font-size: 12px;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.7);
        transition: all 0.3s ease;
    }

    .esim-trust-pill:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.15);
    }

    .esim-trust-pill i {
        color: var(--c-gold);
        font-size: 12px;
    }

    .esim-trust-pill.rating {
        background: rgba(255, 215, 0, 0.08);
        border-color: rgba(255, 215, 0, 0.15);
    }

    .esim-trust-pill.rating .stars {
        display: flex;
        gap: 2px;
        color: var(--c-gold);
        font-size: 10px;
    }

    .esim-trust-pill strong {
        color: #fff;
        font-weight: 600;
    }

    /* Legacy trust row support */
    .esim-trust-stars {
        display: none;
    }

    .esim-trust-labels {
        display: none;
    }

    .esim-stars-group {
        display: flex;
        gap: 4px;
        color: var(--c-gold);
        font-size: 14px;
    }

    .esim-rating-text {
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .esim-label-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: rgba(255,255,255,0.7);
        font-size: 13px;
        font-weight: 500;
    }

    .esim-label-item i {
        color: var(--c-gold);
        font-size: 16px;
    }

    /* Right Side Visual */
    .esim-visual-container {
        position: relative;
        width: auto;
        max-width: 320px;
    }

    .esim-visual-glow {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(255, 215, 0, 0.15) 0%, transparent 70%);
        z-index: -1;
    }

    /* ── Phone Mockup Animation ─────────────────── */
    .esim-chip-visual {
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        animation: esimHeroFloat 6s ease-in-out infinite;
    }

    @keyframes esimHeroFloat {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        33% { transform: translateY(-12px) rotate(0.5deg); }
        66% { transform: translateY(-5px) rotate(-0.5deg); }
    }

    .esim-phone-frame {
        width: 200px;
        height: 380px;
        background: linear-gradient(145deg, #1a1a1a, #0d0d0d);
        border-radius: 28px;
        border: 2px solid rgba(255,215,0,0.3);
        box-shadow: 0 30px 60px rgba(0,0,0,0.8),
                    0 0 60px rgba(255,215,0,0.06),
                    inset 0 1px 2px rgba(255,255,255,0.08);
        overflow: hidden;
        padding: 8px;
        position: relative;
    }

    .esim-phone-screen {
        width: 100%;
        height: 100%;
        background: #050505;
        border-radius: 24px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .esim-screen-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px 6px;
        background: #0a0a0a;
    }

    .esim-signal-bars {
        display: flex;
        align-items: flex-end;
        gap: 2px;
    }

    .esim-signal-bars span {
        width: 4px;
        background: #FFD700;
        border-radius: 1px;
    }
    .esim-signal-bars span:nth-child(1) { height: 5px; }
    .esim-signal-bars span:nth-child(2) { height: 8px; }
    .esim-signal-bars span:nth-child(3) { height: 11px; }
    .esim-signal-bars span:nth-child(4) { height: 14px; }

    .esim-carrier-name {
        font-size: 11px;
        font-weight: 700;
        color: #FFD700;
        font-family: 'Outfit', sans-serif;
        letter-spacing: 0.5px;
    }

    .esim-battery-icon {
        width: 22px;
        height: 11px;
        border: 1.5px solid rgba(255,215,0,0.5);
        border-radius: 3px;
        position: relative;
        padding: 2px;
    }
    .esim-battery-icon::after {
        content: '';
        position: absolute;
        right: -5px;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 6px;
        background: rgba(255,215,0,0.5);
        border-radius: 0 2px 2px 0;
    }
    .esim-battery-icon span {
        display: block;
        width: 75%;
        height: 100%;
        background: #FFD700;
        border-radius: 1px;
    }

    .esim-screen-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 8px 10px;
        gap: 6px;
    }

    .esim-globe-wrap {
        margin-bottom: 0;
    }

    .esim-globe {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: 2px solid rgba(255,215,0,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        background: radial-gradient(circle, rgba(255,215,0,0.08) 0%, transparent 70%);
    }

    .esim-globe i {
        font-size: 30px;
        color: #FFD700;
        position: relative;
        z-index: 1;
    }

    .esim-globe-ring {
        position: absolute;
        border-radius: 50%;
        border: 1px solid rgba(255,215,0,0.15);
        animation: esimRingPulse 3s ease-in-out infinite;
    }
    .esim-globe-ring.ring1 { width: 75px; height: 75px; animation-delay: 0s; }
    .esim-globe-ring.ring2 { width: 88px; height: 88px; animation-delay: 0.5s; }
    .esim-globe-ring.ring3 { width: 100px; height: 100px; animation-delay: 1s; }

    @keyframes esimRingPulse {
        0%, 100% { opacity: 0.4; transform: scale(1); }
        50% { opacity: 0.1; transform: scale(1.05); }
    }

    .esim-status-text {
        font-size: 10px;
        color: rgba(255,255,255,0.6);
        font-family: 'Outfit', sans-serif;
        text-align: center;
    }

    .esim-data-bar {
        width: 100%;
        height: 5px;
        background: rgba(255,255,255,0.08);
        border-radius: 3px;
        overflow: hidden;
    }

    .esim-data-fill {
        height: 100%;
        width: 65%;
        background: linear-gradient(90deg, #FFD700, #D4AF37);
        border-radius: 3px;
        animation: esimDataPulse 3s ease-in-out infinite;
    }

    @keyframes esimDataPulse {
        0%, 100% { width: 65%; }
        50% { width: 70%; }
    }

    .esim-data-label {
        font-size: 10px;
        color: rgba(255,255,255,0.5);
        font-family: 'Outfit', sans-serif;
        text-align: center;
    }

    .esim-activate-btn {
        background: linear-gradient(135deg, #FFD700, #D4AF37);
        color: #000;
        font-size: 11px;
        font-weight: 700;
        padding: 6px 18px;
        border-radius: 16px;
        font-family: 'Outfit', sans-serif;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 12px rgba(255,215,0,0.3);
    }

    /* Floating Badges */
    .esim-floating-badge {
        position: absolute;
        background: rgba(15,15,15,0.95);
        border: 1px solid rgba(255,215,0,0.25);
        color: #FFD700;
        font-size: 10px;
        font-weight: 600;
        padding: 6px 10px;
        border-radius: 16px;
        font-family: 'Outfit', sans-serif;
        display: flex;
        align-items: center;
        gap: 5px;
        backdrop-filter: blur(10px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.4);
        white-space: nowrap;
    }

    .badge-f1 { top: 20px; left: -35px; animation: esimBadgeFloat 5s ease-in-out infinite; }
    .badge-f2 { bottom: 60px; left: -40px; animation: esimBadgeFloat 5s ease-in-out infinite 1.2s; }
    .badge-f3 { top: 50px; right: -40px; animation: esimBadgeFloat 5s ease-in-out infinite 2.4s; }
    .badge-f4 { bottom: 20px; right: -30px; animation: esimBadgeFloat 5s ease-in-out infinite 0.6s; }

    @keyframes esimBadgeFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }

    /* ── Step Icon CSS ───────────────────────────── */
    .esim-step-icon-wrap {
        width: 110px;
        height: 110px;
        border-radius: 28px;
        border: 1px solid rgba(255,215,0,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .esim-how-card:hover .esim-step-icon-wrap {
        transform: scale(1.08) rotate(-3deg);
        box-shadow: 0 12px 40px rgba(255,215,0,0.15);
    }

    .esim-how-card-title {
        font-family: 'Outfit', sans-serif;
        font-size: 17px;
        font-weight: 700;
        color: #fff;
        margin: 0;
        text-align: center;
    }

    /* Progress bar - hidden */
    .esim-progress-wrap { display: none; }

    .esim-progress-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        position: relative;
        z-index: 1;
        flex: 0 0 auto;
    }

    .esim-progress-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        font-weight: 700;
        background: #222;
        color: #555;
        border: 2px solid #333;
        transition: all 0.4s ease;
    }

    .esim-progress-step.active .esim-progress-circle {
        background: var(--c-gold-gradient);
        color: #000;
        border-color: var(--c-gold);
        box-shadow: 0 0 16px rgba(255, 215, 0, 0.2);
    }

    .esim-progress-step.completed .esim-progress-circle {
        background: var(--c-gold-gradient);
        color: #000;
        border-color: var(--c-gold);
    }

    .esim-progress-label {
        font-size: 11px;
        font-weight: 500;
        color: #555;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: color 0.4s ease;
    }

    .esim-progress-step.active .esim-progress-label,
    .esim-progress-step.completed .esim-progress-label {
        color: var(--c-gold);
    }

    .esim-progress-line {
        position: absolute;
        top: 18px;
        height: 2px;
        background: #222;
        z-index: 0;
    }

    .esim-progress-line-fill {
        height: 100%;
        width: 0%;
        background: var(--c-gold-gradient);
        transition: width 0.5s ease;
    }

    .esim-progress-line-1 {
        left: 60px;
        right: calc(50% + 30px);
    }

    .esim-progress-line-2 {
        left: calc(50% + 30px);
        right: 60px;
    }

    /* ============================================================
       WIZARD STEPS (shared)
       ============================================================ */
    .esim-wizard-step {
        opacity: 0;
        transform: translateY(24px);
        transition: opacity 0.45s ease, transform 0.45s ease;
        pointer-events: none;
        position: absolute;
        width: 100%;
        visibility: hidden;
        display: none;
    }

    .esim-wizard-step.step-active {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
        position: relative;
        visibility: visible;
        display: block;
    }

    .esim-wizard-container {
        position: relative;
        margin-top: -10px;
        padding-top: 0;
        padding-bottom: 0 !important;
        margin-bottom: 0;
        overflow: hidden;
    }

    /* ============================================================
       STEP 1 — CHOOSE DESTINATION
       ============================================================ */
    .esim-step1 {
        max-width: 1300px;
        margin: 0 auto;
        padding: 24px 40px 32px !important;
        font-family: 'Outfit', sans-serif;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        min-height: auto;
    }

    .esim-section-label {
        color: var(--c-gold);
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 3px;
        margin-bottom: 16px;
        opacity: 0.9;
        text-align: center;
    }

    /* Popular destinations grid */
    .esim-popular-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 16px;
        width: 100%;
        justify-content: center;
    }

    .esim-popular-card {
        background: var(--c-card-bg);
        border: 1px solid var(--c-border-subtle);
        border-radius: 12px;
        height: 50px;
        padding: 0 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
        width: 100%;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }

    .esim-popular-card:hover {
        border-color: rgba(255, 215, 0, 0.6);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(255, 215, 0, 0.12);
        background: rgba(255, 215, 0, 0.03);
    }

    .esim-popular-flag {
        width: 20px;
        height: 14px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .esim-popular-flag img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 3px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }

    .esim-popular-name {
        font-family: 'Outfit', sans-serif;
        font-size: 18px;
        font-weight: 600;
        color: var(--c-gold);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: normal;
        margin: 0;
        display: flex;
        align-items: center;
        height: 18px;
        letter-spacing: -0.2px;
    }

    /* Divider with "or" */
    .esim-or-divider {
        display: flex;
        align-items: center;
        gap: 20px;
        margin: 0 0 40px;
        width: 100%;
        max-width: 800px;
    }

    .esim-or-divider-line {
        flex: 1;
        height: 1px;
        background: #333;
    }

    .esim-or-divider-text {
        font-family: 'Outfit', sans-serif;
        font-size: 12px;
        font-weight: 400;
        color: #555;
        text-transform: lowercase;
    }

    /* Search Bar */
    .esim-search-wrap {
        position: relative;
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
    }

    .esim-search-icon {
        position: absolute;
        left: 22px;
        top: 50%;
        transform: translateY(-50%);
        color: #666;
        font-size: 18px;
        pointer-events: none;
        transition: color 0.3s ease;
    }

    .esim-search-input {
        width: 100%;
        height: 56px;
        background: var(--c-input-bg);
        border: 1px solid var(--c-input-border);
        border-radius: 50px;
        padding: 0 24px 0 56px;
        color: #eee;
        font-family: 'Outfit', sans-serif;
        font-size: 16px;
        font-weight: 400;
        transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
        outline: none;
    }

    .esim-search-input::placeholder {
        color: #555;
    }

    .esim-search-input:focus {
        border-color: var(--c-gold);
        box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.1);
    }

    .esim-search-input:focus + .esim-search-icon {
        color: var(--c-gold);
    }

    /* Region Filter Pills */
    .esim-region-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin: 20px auto 0;
        justify-content: center;
        width: 100%;
        max-width: 1300px;
    }

    .esim-region-pill {
        display: inline-block;
        padding: 12px 28px;
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 600;
        letter-spacing: 0.2px;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
        user-select: none;
        background: rgba(255, 255, 255, 0.04);
        color: var(--c-text-muted);
        border: 1px solid rgba(255, 255, 255, 0.08);
        white-space: nowrap;
    }

    .esim-region-pill:hover {
        color: var(--c-gold);
        border-color: rgba(255, 215, 0, 0.4);
        background: rgba(255, 215, 0, 0.06);
        transform: translateY(-1px);
    }

    .esim-region-pill.active {
        background: var(--c-gold-gradient);
        color: #000;
        font-weight: 600;
        border-color: transparent;
    }

    /* Country Grid */
    .esim-country-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
        margin-top: 32px;
        width: 100%;
        justify-content: center;
    }

    .esim-country-card {
        background: var(--c-card-bg);
        border: 1px solid var(--c-border-subtle);
        border-radius: 12px;
        height: 50px;
        padding: 0 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
        width: 100%;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }

    .esim-country-card:hover {
        border-color: rgba(255, 215, 0, 0.6);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(255, 215, 0, 0.12);
        background: rgba(255, 215, 0, 0.03);
    }

    .esim-country-card.active-country {
        border-color: var(--c-gold);
        background: rgba(255, 215, 0, 0.05);
        box-shadow: 0 0 10px rgba(255, 215, 0, 0.1);
    }

    .esim-country-flag {
        width: 20px;
        height: 14px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .esim-country-flag img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 3px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }

    .esim-country-name {
        font-family: 'Outfit', sans-serif;
        font-size: 18px;
        font-weight: 600;
        color: var(--c-gold);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: normal;
        margin: 0;
        display: flex;
        align-items: center;
        height: 18px;
        letter-spacing: -0.2px;
    }

    /* Skeleton Loaders */
    .esim-skeleton-card {
        background: var(--c-card-bg);
        border: 1px solid var(--c-border-subtle);
        border-radius: 12px;
        height: 50px;
        padding: 0 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .esim-skeleton-flag {
        position: absolute;
        left: 15px;
        width: 24px;
        height: 24px;
        border-radius: 6px;
        background: linear-gradient(90deg, #151515 25%, #1a1a1a 50%, #151515 75%);
        background-size: 200% 100%;
        animation: esimShimmer 1.5s infinite;
        flex-shrink: 0;
    }

    .esim-skeleton-text {
        height: 14px;
        border-radius: 4px;
        background: linear-gradient(90deg, #151515 25%, #1a1a1a 50%, #151515 75%);
        background-size: 200% 100%;
        animation: esimShimmer 1.5s infinite;
        flex: 1;
        max-width: 120px;
    }

    @keyframes esimShimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* No Results */
    .esim-no-results {
        grid-column: 1 / -1;
        text-align: center;
        padding: 48px 20px;
        color: #555;
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 400;
    }

    .esim-no-results i {
        display: block;
        font-size: 28px;
        color: #333;
        margin-bottom: 12px;
    }

    /* ── How It Works Grid Layout ────────────────── */


    .esim-how-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
        max-width: 1240px;
        margin: 0 auto;
    }

    .esim-how-card {
        background: #0a0a0a;
        border: 1px solid rgba(255, 215, 0, 0.1);
        border-radius: 20px;
        padding: 30px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: all 0.4s ease;
        position: relative;
    }

    .esim-how-card:hover {
        transform: translateY(-8px);
        background: #111;
        border-color: rgba(255, 215, 0, 0.3);
        box-shadow: 0 20px 40px rgba(0,0,0,0.4);
    }

    .esim-how-icon {
        width: 100%;
        margin-bottom: 24px;
        display: flex;
        justify-content: center;
    }

    .esim-how-icon img {
        width: 100%;
        max-width: 180px;
        height: auto;
        border-radius: 12px;
    }

    .esim-how-num {
        width: 28px;
        height: 28px;
        background: var(--c-gold-gradient);
        color: #000;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 800;
        margin-bottom: 16px;
        box-shadow: 0 4px 12px rgba(255,215,0,0.3);
    }

    .esim-how-card-title {
        color: #fff;
        font-family: 'Outfit', sans-serif;
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .esim-how-card-desc {
        color: rgba(255,255,255,0.5);
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        line-height: 1.5;
        margin: 0;
    }

    @media (max-width: 1024px) {
        .esim-how-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 600px) {
        .esim-how-grid { grid-template-columns: 1fr; }
    }

    /* ============================================================
       STEP 2 — SELECT PLAN
       ============================================================ */
    .esim-step2 {
        max-width: 1100px;
        margin: 0 auto;
        padding: 10px 20px 24px;
        font-family: 'Outfit', sans-serif;
    }

    .esim-selected-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: rgba(255, 215, 0, 0.04);
        border: 1px solid rgba(255, 215, 0, 0.1);
        border-radius: 12px;
        padding: 12px 18px;
        margin-bottom: 20px;
    }

    .esim-selected-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .esim-selected-flag {
        width: 36px;
        height: 26px;
        border-radius: 4px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .esim-selected-flag img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .esim-selected-name {
        font-size: 18px;
        font-weight: 700;
        color: #fff;
    }

    .esim-change-btn {
        background: transparent;
        border: 1px solid rgba(255, 215, 0, 0.2);
        color: var(--c-gold);
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .esim-change-btn:hover {
        background: rgba(255, 215, 0, 0.08);
        border-color: var(--c-gold);
        transform: translateX(-4px);
    }

    /* Bundle Tabs */
    .esim-bundle-tabs {
        display: flex;
        background: rgba(255, 255, 255, 0.03);
        padding: 5px;
        border-radius: 50px;
        margin: 0 auto 32px;
        width: fit-content;
        border: 1px solid rgba(255, 215, 0, 0.1);
        backdrop-filter: blur(10px);
    }

    .esim-bundle-tab {
        height: 42px;
        min-width: 160px;
        padding: 0 25px;
        border-radius: 50px;
        border: none;
        background: transparent;
        color: rgba(255, 255, 255, 0.4);
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        white-space: nowrap;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Outfit', sans-serif;
    }

    .esim-bundle-tab:hover {
        color: var(--c-gold);
    }

    .esim-bundle-tab.active {
        background: var(--c-gold-gradient);
        color: #000;
        box-shadow: 0 4px 15px rgba(255, 215, 0, 0.2);
    }

    /* Bundle Loading */
    .esim-bundle-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 48px 0;
        color: #555;
        font-size: 13px;
        gap: 10px;
    }

    .esim-bundle-loading .spinner-ring {
        width: 20px;
        height: 20px;
        border: 2px solid rgba(255, 215, 0, 0.15);
        border-top-color: var(--c-gold);
        border-radius: 50%;
        animation: esimSpin 0.8s linear infinite;
    }

    @keyframes esimSpin {
        to { transform: rotate(360deg); }
    }

    /* Bundle Cards Grid */
    .esim-bundles-list {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
        margin-bottom: 20px;
        padding-top: 12px;
        width: 100%;
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
    }

    .esim-bundle-card {
        background: var(--c-card-bg);
        border: 1px solid var(--c-border-subtle);
        border-radius: 10px;
        padding: 20px 10px 12px;
        text-align: center;
        transition: all 0.3s ease;
        position: relative;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        min-height: 130px;
        max-width: 155px;
        margin: 0 auto;
        width: 100%;
        overflow: visible;
    }

    .esim-bundle-card:hover {
        transform: translateY(-8px);
        background: #111;
        border-color: rgba(255, 215, 0, 0.2);
    }

    .esim-bundle-card.selected {
        border: 2.5px solid var(--c-gold);
        background: rgba(255, 215, 0, 0.04);
        box-shadow: 0 15px 40px rgba(255, 215, 0, 0.15);
    }

    .esim-bundle-card.selected::after {
        content: '\f058';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        top: 8px;
        right: 8px;
        color: var(--c-gold);
        font-size: 12px;
    }

    .esim-bundle-popular-tag {
        position: absolute;
        top: 6px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--c-gold-gradient);
        color: #000;
        font-size: 6px;
        font-weight: 800;
        padding: 2px 6px;
        border-radius: 50px;
        white-space: nowrap;
        z-index: 10;
        text-transform: uppercase;
        letter-spacing: 1px;
        white-space: nowrap;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        z-index: 5;
    }

    .esim-bundle-data {
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 800;
        color: #fff;
        margin-bottom: 2px;
        letter-spacing: -0.5px;
    }

    .esim-bundle-validity {
        font-size: 10px;
        color: rgba(255, 255, 255, 0.4);
        font-weight: 500;
        margin-bottom: 10px;
    }

    .esim-bundle-divider {
        width: 40px;
        height: 1px;
        background: rgba(255, 215, 0, 0.15);
        margin: 0 auto 20px;
    }

    .esim-bundle-price {
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        font-weight: 700;
        color: var(--c-gold);
        margin-bottom: 6px;
    }

    .esim-bundle-badges {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .esim-bundle-badge {
        display: inline-block;
        background: rgba(255, 255, 255, 0.05);
        color: rgba(255, 255, 255, 0.6);
        font-size: 8px;
        padding: 2px 6px;
        border-radius: 3px;
        margin: 0 2px 2px;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    /* Show All button */
    .esim-show-all-btn {
        display: none;
        background: none;
        border: none;
        color: var(--c-gold);
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        padding: 16px 0;
        text-align: center;
        width: 100%;
        transition: opacity 0.3s ease;
    }

    .esim-show-all-btn:hover {
        opacity: 0.8;
    }

    .esim-show-all-btn.visible {
        display: block;
    }

    /* Continue Button */
    .esim-continue-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        max-width: 420px;
        margin: 0 auto;
        height: 56px;
        background: var(--c-gold-gradient);
        color: #000;
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2px;
        border-radius: 50px;
        border: none;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
        box-shadow: 0 10px 30px rgba(255, 215, 0, 0.2);
    }

    .esim-continue-btn:hover:not(:disabled) {
        box-shadow: 0 8px 25px rgba(255, 215, 0, 0.3);
        transform: translateY(-1px);
        filter: brightness(1.08);
    }

    .esim-continue-btn:disabled {
        background: #1a1a1a;
        color: #444;
        cursor: not-allowed;
        box-shadow: none;
    }

    /* ============================================================
       STEP 3 — CHECKOUT (Premium Redesign)
       ============================================================ */
    .esim-step3 {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 28px 24px;
        font-family: 'Outfit', sans-serif;
    }

    /* Flexbox Grid Layout */
    .esim-checkout-grid {
        display: flex;
        align-items: flex-start;
        gap: 28px;
        max-width: 960px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* Back button */
    .esim-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: none;
        border: none;
        color: var(--c-gold);
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        padding: 0;
        margin-bottom: 24px;
        transition: opacity 0.3s ease;
    }

    .esim-back-btn:hover {
        opacity: 0.7;
    }

    .esim-back-btn i {
        font-size: 11px;
    }

    /* Order Summary Card - Right Side */
    .esim-checkout-right {
        position: sticky;
        top: 100px;
    }

    .esim-summary-card {
        background: linear-gradient(180deg, rgba(18, 18, 18, 1) 0%, rgba(12, 12, 12, 1) 100%);
        border: 1px solid rgba(255, 215, 0, 0.12);
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        padding: 20px;
        box-sizing: border-box;
    }

    .esim-summary-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--c-gold-gradient);
    }

    .esim-summary-inner {
        padding: 24px;
    }

    .esim-summary-title {
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 700;
        color: #fff;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        letter-spacing: 0.3px;
    }

    .esim-summary-empty {
        text-align: center;
        padding: 30px 0;
        color: #555;
        font-family: 'Outfit', sans-serif;
        font-size: 12px;
        font-weight: 400;
    }

    .esim-summary-empty i {
        font-size: 24px;
        color: #333;
        margin-bottom: 8px;
        display: block;
    }

    .esim-summary-empty p {
        margin: 0;
    }

    .esim-summary-content {
        display: none;
    }

    .esim-summary-content.visible {
        display: block;
    }

    .esim-summary-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
    }

    .esim-summary-left {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1;
        min-width: 0;
    }

    .esim-summary-country-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
    }

    .esim-summary-country-flag {
        width: 24px;
        height: 17px;
    }

    .esim-summary-country-flag img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 3px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }

    .esim-summary-country-name {
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 500;
        color: #fff;
    }

    .esim-summary-bundle-name {
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 600;
        color: #fff;
    }

    .esim-summary-bundle-details {
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        color: var(--c-text-muted);
        margin-top: 2px;
    }

    .esim-summary-badges {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
        margin-top: 6px;
    }

    .esim-summary-right-price {
        font-family: 'Outfit', sans-serif;
        font-size: 24px;
        font-weight: 700;
        color: var(--c-gold);
        text-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
        flex-shrink: 0;
        text-align: right;
    }

    .esim-summary-change-plan {
        display: block;
        margin-top: 16px;
        background: none;
        border: none;
        color: var(--c-gold);
        font-family: 'Outfit', sans-serif;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        padding: 0;
        transition: opacity 0.3s ease;
    }

    .esim-summary-change-plan:hover {
        opacity: 0.7;
    }

    .esim-summary-divider {
        height: 1px;
        background: rgba(255, 255, 255, 0.06);
        margin: 10px 0;
    }

    .esim-summary-price-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
    }

    .esim-summary-price-label {
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        font-weight: 400;
        color: var(--c-text-muted);
    }

    .esim-summary-price-value {
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 500;
        color: #fff;
    }

    .esim-summary-total-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-top: 4px;
    }

    .esim-summary-total-label {
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 600;
        color: #fff;
    }

    .esim-summary-total-value {
        font-family: 'Outfit', sans-serif;
        font-size: 22px;
        font-weight: 700;
        color: var(--c-gold);
        text-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
    }

    /* Checkout Summary Side */
    .esim-checkout-summary-side {
        flex: 1;
        min-width: 0;
    }

    .esim-summary-section {
        margin-bottom: 12px;
    }

    .esim-summary-section:last-of-type {
        margin-bottom: 0;
    }

    .esim-summary-label {
        font-family: 'Outfit', sans-serif;
        font-size: 9px;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.4);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 6px;
    }

    .esim-summary-destination {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 10px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 6px;
        border: 1px solid rgba(255, 255, 255, 0.04);
    }

    .esim-summary-plan {
        padding: 10px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 6px;
        border: 1px solid rgba(255, 255, 255, 0.04);
    }

    .esim-summary-plan-name {
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 600;
        color: #fff;
        margin-bottom: 2px;
    }

    .esim-summary-plan-meta {
        font-family: 'Outfit', sans-serif;
        font-size: 11px;
        color: var(--c-text-muted);
    }

    .esim-summary-change {
        display: inline-block;
        margin-top: 8px;
        background: none;
        border: none;
        color: var(--c-gold);
        font-family: 'Outfit', sans-serif;
        font-size: 10px;
        font-weight: 500;
        cursor: pointer;
        padding: 0;
        transition: opacity 0.3s ease;
    }

    .esim-summary-change:hover {
        opacity: 0.7;
    }

    /* Pricing & payment section */
    .esim-summary-footer {
        margin-top: 6px;
    }

    .esim-summary-pricing {
        padding-top: 12px;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    .esim-price-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-family: 'Outfit', sans-serif;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.5);
        margin-bottom: 8px;
    }

    .esim-price-row:last-child {
        margin-bottom: 0;
    }

    .esim-price-row.total {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        margin-bottom: 12px;
    }

    .esim-price-row.total span:first-child {
        font-size: 13px;
        font-weight: 600;
        color: #fff;
    }

    .esim-price-row.total span:last-child {
        font-size: 20px;
        font-weight: 700;
        color: var(--c-gold);
    }

    .esim-price-row .free {
        background: linear-gradient(135deg, rgba(76, 175, 80, 0.15) 0%, rgba(76, 175, 80, 0.06) 100%);
        color: #4CAF50;
        font-size: 9px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 4px;
        border: 1px solid rgba(76, 175, 80, 0.2);
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    /* Customer Form */
    .esim-form-area {
        margin-bottom: 32px;
    }

    .esim-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-top: 16px;
    }

    .esim-form-field {
        display: flex;
        flex-direction: column;
    }

    .esim-form-field.full-width {
        grid-column: 1 / -1;
    }

    .esim-field-label {
        font-family: 'Outfit', sans-serif;
        font-size: 11px;
        font-weight: 600;
        color: var(--c-text-muted);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 6px;
    }

    .esim-field-input {
        width: 100%;
        height: 44px;
        background: var(--c-input-bg);
        border: 1px solid var(--c-input-border);
        border-radius: 8px;
        padding: 0 14px;
        color: var(--c-text-light);
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 400;
        transition: all 0.3s ease;
        outline: none;
    }

    .esim-field-input::placeholder {
        color: #444;
    }

    .esim-field-input:hover {
        border-color: #333;
        background: #141414;
    }

    .esim-field-input:focus {
        border-color: var(--c-gold);
        box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.1);
        background: #161616;
    }

    .esim-field-input:-webkit-autofill,
    .esim-field-input:-webkit-autofill:hover,
    .esim-field-input:-webkit-autofill:focus {
        -webkit-box-shadow: 0 0 0 1000px #111 inset !important;
        -webkit-text-fill-color: var(--c-text-light) !important;
        border-color: var(--c-input-border);
        transition: background-color 5000s ease-in-out 0s;
    }

    .esim-field-input.input-error {
        border-color: #dc3545;
        box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.15);
        animation: esimShake 0.4s ease;
    }

    @keyframes esimShake {
        0%, 100% { transform: translateX(0); }
        20%, 60% { transform: translateX(-4px); }
        40%, 80% { transform: translateX(4px); }
    }

    .esim-field-error {
        font-family: 'Outfit', sans-serif;
        font-size: 11px;
        color: #dc3545;
        margin-top: 4px;
        display: none;
    }

    .esim-field-error.visible {
        display: block;
    }

    /* Checkout Form Styles - Left Side */
    .esim-checkout-form-side {
        flex: 1;
        min-width: 0;
        background: linear-gradient(180deg, rgba(18, 18, 18, 1) 0%, rgba(12, 12, 12, 1) 100%);
        border: 1px solid rgba(255, 215, 0, 0.12);
        border-radius: 12px;
        padding: 24px;
        box-sizing: border-box;
        position: relative;
    }

    .esim-checkout-form-side::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--c-gold-gradient);
        border-radius: 12px 12px 0 0;
    }

    .esim-checkout-title {
        font-family: 'Outfit', sans-serif;
        font-size: 18px;
        font-weight: 700;
        color: #fff;
        margin: 0 0 6px;
        letter-spacing: 0.3px;
    }

    .esim-checkout-subtitle {
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        font-weight: 400;
        color: rgba(255, 255, 255, 0.5);
        margin: 0 0 24px;
        line-height: 1.5;
    }

    .esim-form-fields {
        flex: 1;
    }

    .esim-form-group {
        margin-bottom: 18px;
    }

    .esim-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .esim-label {
        display: block;
        font-family: 'Outfit', sans-serif;
        font-size: 11px;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.45);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
    }

    .esim-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .esim-input-wrapper i {
        position: absolute;
        left: 14px;
        color: var(--c-gold);
        font-size: 13px;
        z-index: 1;
        opacity: 0.8;
    }

    .esim-input {
        width: 100%;
        height: 48px;
        background: rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(255, 215, 0, 0.15);
        border-radius: 8px;
        padding: 0 14px 0 42px;
        color: var(--c-text-light);
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 400;
        transition: all 0.25s ease;
        outline: none;
    }

    .esim-input::placeholder {
        color: rgba(255, 255, 255, 0.25);
    }

    .esim-input:hover {
        border-color: rgba(255, 215, 0, 0.3);
        background: rgba(0, 0, 0, 0.5);
    }

    .esim-input:focus {
        border-color: var(--c-gold);
        box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.08);
        background: rgba(0, 0, 0, 0.6);
    }

    .esim-input:-webkit-autofill,
    .esim-input:-webkit-autofill:hover,
    .esim-input:-webkit-autofill:focus {
        -webkit-box-shadow: 0 0 0 1000px #0a0a0a inset !important;
        -webkit-text-fill-color: var(--c-text-light) !important;
        border-color: rgba(255, 215, 0, 0.2);
        transition: background-color 5000s ease-in-out 0s;
    }

    .esim-input.input-error {
        border-color: #dc3545;
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.12);
        animation: esimShake 0.4s ease;
    }

    .esim-error {
        font-family: 'Outfit', sans-serif;
        font-size: 11px;
        color: #dc3545;
        margin-top: 6px;
        display: none;
    }

    .esim-error.visible {
        display: block;
    }

    .esim-checkout-notice {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        background: rgba(255, 215, 0, 0.04);
        border: 1px solid rgba(255, 215, 0, 0.08);
        border-radius: 8px;
        padding: 12px 14px;
        margin-top: 24px;
    }

    .esim-checkout-notice i {
        color: var(--c-gold);
        font-size: 14px;
        flex-shrink: 0;
        margin-top: 1px;
        opacity: 0.9;
    }

    .esim-checkout-notice span {
        font-family: 'Outfit', sans-serif;
        font-size: 12px;
        font-weight: 400;
        color: rgba(255, 255, 255, 0.6);
        line-height: 1.5;
    }

    /* Pay Button - Premium Style */
    .esim-pay-btn {
        width: 100%;
        height: 46px;
        background: var(--c-gold-gradient);
        color: #000;
        font-family: 'Outfit', sans-serif;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.25s ease;
        box-shadow: 0 4px 20px rgba(255, 215, 0, 0.15);
        position: relative;
        overflow: hidden;
    }

    .esim-pay-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .esim-pay-btn:hover:not(:disabled)::before {
        left: 100%;
    }

    .esim-pay-btn:hover:not(:disabled) {
        box-shadow: 0 6px 28px rgba(255, 215, 0, 0.35);
        transform: translateY(-2px);
    }

    .esim-pay-btn:active:not(:disabled) {
        transform: translateY(0);
        box-shadow: 0 4px 16px rgba(255, 215, 0, 0.2);
    }

    .esim-pay-btn:disabled {
        background: rgba(255, 255, 255, 0.06);
        color: rgba(255, 255, 255, 0.25);
        cursor: not-allowed;
        box-shadow: none;
    }

    .esim-pay-btn .btn-spinner {
        width: 16px;
        height: 16px;
        border: 2px solid rgba(0, 0, 0, 0.2);
        border-top-color: #000;
        border-radius: 50%;
        animation: esimSpin 0.6s linear infinite;
        display: none;
    }

    .esim-pay-btn.loading .btn-spinner {
        display: inline-block;
    }

    .esim-pay-btn.loading .btn-label {
        display: none;
    }

    .esim-pay-btn.loading .btn-loading-text {
        display: inline;
    }

    .esim-pay-btn .btn-loading-text {
        display: none;
    }

    .esim-secure-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 12px;
        color: #555;
        font-family: 'Outfit', sans-serif;
        font-size: 11px;
        font-weight: 400;
    }

    .esim-secure-badge i {
        font-size: 10px;
    }

    /* Error Message */
    .esim-error-msg {
        background: rgba(220, 53, 69, 0.08);
        border: 1px solid rgba(220, 53, 69, 0.2);
        border-radius: 8px;
        padding: 12px 16px;
        margin-top: 16px;
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        color: #dc3545;
        display: none;
    }

    .esim-error-msg.visible {
        display: block;
    }

    /* Payment Error */
    .esim-payment-error {
        background: rgba(220, 53, 69, 0.06);
        border: 1px solid rgba(220, 53, 69, 0.15);
        border-radius: 6px;
        padding: 8px 12px;
        margin-top: 10px;
        font-family: 'Outfit', sans-serif;
        font-size: 11px;
        color: #e05260;
        display: none;
        align-items: center;
        gap: 6px;
    }

    .esim-payment-error.visible {
        display: flex;
    }

    .esim-payment-error i {
        font-size: 12px;
        flex-shrink: 0;
    }

    /* Secure Footer */
    .esim-secure-footer {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        margin-top: 10px;
        color: rgba(255, 255, 255, 0.3);
        font-family: 'Outfit', sans-serif;
        font-size: 10px;
        font-weight: 400;
        letter-spacing: 0.2px;
    }

    .esim-secure-footer i {
        color: rgba(76, 175, 80, 0.6);
        font-size: 10px;
    }

    /* Back Link */
    .esim-back-link {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 20px;
        background: none;
        border: none;
        color: var(--c-gold);
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        padding: 0;
        transition: opacity 0.3s ease;
    }

    .esim-back-link:hover {
        opacity: 0.7;
    }

    .esim-back-link i {
        font-size: 12px;
    }

    /* ============================================================
       WHY CHOOSE eSIM — Feature Cards
       ============================================================ */
    .esim-features-section {
        max-width: 1200px;
        margin: 0 auto !important;
        margin-top: 0 !important;
        padding: 0 28px 0 !important;
        text-align: center;
        font-family: 'Outfit', sans-serif;
    }

    .esim-features-badge {
        display: inline-block;
        background: rgba(255, 215, 0, 0.08);
        border: 1px solid rgba(255, 215, 0, 0.15);
        color: var(--c-gold);
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 3px;
        text-transform: uppercase;
        padding: 6px 20px;
        border-radius: 50px;
        margin-bottom: 14px;
    }

    .esim-features-title {
        font-family: 'Outfit', sans-serif;
        font-size: 26px;
        font-weight: 700;
        color: #fff;
        margin: 0 0 10px;
    }

    .esim-features-subtitle {
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 300;
        color: var(--c-text-muted);
        margin: 0 0 28px;
    }

    .esim-features-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    .esim-feature-card {
        background: var(--c-card-bg);
        border: 1px solid rgba(255, 215, 0, 0.06);
        border-radius: 14px;
        padding: 24px 16px;
        text-align: center;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .esim-feature-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: var(--c-gold-gradient);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .esim-feature-card:hover {
        border-color: rgba(255, 215, 0, 0.2);
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(255, 215, 0, 0.06);
    }

    .esim-feature-card:hover::before {
        opacity: 1;
    }

    .esim-feature-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 18px;
        background: rgba(255, 215, 0, 0.06);
        border: 1px solid rgba(255, 215, 0, 0.1);
        font-size: 22px;
        color: var(--c-gold);
        transition: all 0.3s ease;
    }

    .esim-feature-card:hover .esim-feature-icon {
        background: rgba(255, 215, 0, 0.1);
        box-shadow: 0 0 16px rgba(255, 215, 0, 0.08);
    }

    .esim-feature-card-title {
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 600;
        color: #fff;
        margin-bottom: 8px;
    }

    .esim-feature-card-desc {
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        font-weight: 300;
        color: var(--c-text-muted);
        line-height: 1.6;
    }

    /* ============================================================
       HOW TO ACTIVATE — 4-Step Tutorial
       ============================================================ */
    .esim-how-section {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 28px 0;
        margin-bottom: 0 !important;
        text-align: center;
        font-family: 'Outfit', sans-serif;
    }

    .esim-how-badge {
        display: inline-block;
        background: rgba(255, 215, 0, 0.08);
        border: 1px solid rgba(255, 215, 0, 0.15);
        color: var(--c-gold);
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 3px;
        text-transform: uppercase;
        padding: 6px 20px;
        border-radius: 50px;
        margin-bottom: 16px;
    }

    .esim-how-title {
        font-family: 'Outfit', sans-serif;
        font-size: 26px;
        font-weight: 700;
        color: #fff;
        margin: 0 0 10px;
    }

    .esim-how-subtitle {
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 300;
        color: var(--c-text-muted);
        margin: 0 0 20px;
    }

    .esim-how-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        position: relative;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    /* Connector line between steps */
    .esim-how-grid::before {
        content: '';
        position: absolute;
        top: 56px;
        left: calc(12.5% + 10px);
        right: calc(12.5% + 10px);
        height: 2px;
        background: linear-gradient(90deg, rgba(255, 215, 0, 0.06), rgba(255, 215, 0, 0.15), rgba(255, 215, 0, 0.15), rgba(255, 215, 0, 0.06));
        z-index: 0;
    }

    .esim-how-card {
        background: #0d0d0d;
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        padding: 28px 20px;
        text-align: center;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        height: 100%;
        position: relative;
        z-index: 1;
    }

    .esim-how-card:hover {
        transform: translateY(-10px);
        border-color: rgba(255, 215, 0, 0.25);
        box-shadow: 0 30px 60px rgba(0,0,0,0.5);
    }

    .esim-how-icon {
        width: 100%;
        max-width: 200px;
        aspect-ratio: 16/9;
        margin-bottom: 32px;
        transition: all 0.5s ease;
        filter: drop-shadow(0 10px 20px rgba(0,0,0,0.4));
    }

    .esim-how-card:hover .esim-how-icon {
        transform: scale(1.08) translateY(-5px);
        filter: drop-shadow(0 15px 30px rgba(255, 215, 0, 0.15));
    }

    .esim-how-icon img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 12px;
    }

    .esim-how-num {
        width: 32px;
        height: 32px;
        background: var(--c-gold-gradient);
        color: #000;
        font-size: 14px;
        font-weight: 800;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        box-shadow: 0 4px 10px rgba(255, 215, 0, 0.2);
    }

    .esim-how-card-title {
        font-family: 'Outfit', sans-serif;
        font-size: 16px;
        font-weight: 600;
        color: #fff;
        margin-top: 0;
        margin-bottom: 8px;
    }

    .esim-how-card-desc {
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        font-weight: 300;
        color: var(--c-text-muted);
        line-height: 1.6;
    }

    /* ============================================================
       FAQ
       ============================================================ */
    .esim-faq-section {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 28px 48px;
        text-align: center;
        font-family: 'Outfit', sans-serif;
    }

    .esim-faq-badge {
        display: inline-block;
        background: rgba(255, 215, 0, 0.08);
        border: 1px solid rgba(255, 215, 0, 0.15);
        color: var(--c-gold);
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 3px;
        text-transform: uppercase;
        padding: 6px 20px;
        border-radius: 50px;
        margin-bottom: 16px;
    }

    .esim-faq-title {
        font-family: 'Outfit', sans-serif;
        font-size: 26px;
        font-weight: 700;
        color: #fff;
        margin: 0 0 24px;
    }

    .esim-faq-list {
        text-align: left;
    }

    .esim-faq-item {
        background: var(--c-card-bg);
        border: 1px solid rgba(255, 215, 0, 0.06);
        border-radius: 12px;
        margin-bottom: 8px;
        overflow: hidden;
        transition: border-color 0.3s ease;
    }

    .esim-faq-item.active {
        border-color: rgba(255, 215, 0, 0.15);
    }

    .esim-faq-question {
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        user-select: none;
        transition: background 0.2s ease;
    }

    .esim-faq-question:hover {
        background: rgba(255, 255, 255, 0.01);
    }

    .esim-faq-question-text {
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 500;
        color: #fff;
        padding-right: 16px;
    }

    .esim-faq-toggle {
        font-family: 'Outfit', sans-serif;
        font-size: 20px;
        color: var(--c-gold);
        flex-shrink: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s ease;
    }

    .esim-faq-item.active .esim-faq-toggle {
        transform: rotate(45deg);
    }

    .esim-faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }

    .esim-faq-answer-inner {
        padding: 0 24px 18px;
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 300;
        color: var(--c-text-muted);
        line-height: 1.7;
    }

    /* ============================================================
       RESPONSIVE
       ============================================================ */
    @media (min-width: 992px) {
        .esim-bundles-list {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (max-width: 1024px) {
        .esim-hero {
            padding: 180px 24px 28px;
            min-height: 0;
        }
        .esim-hero-left {
            flex: 0 1 460px;
            max-width: 460px;
        }
        .esim-step1 {
            min-height: auto;
            padding: 20px 16px 28px !important;
        }
    }

    /* Tablet — image stays as bg, text gets stronger overlay so it sits cleanly over the artwork */
    @media (max-width: 900px) {
        .esim-hero {
            background-position: 70% center;
            margin-top: -224px;
            min-height: 704px;
            padding: 272px 24px 48px;
        }
        .esim-hero::before {
            background: linear-gradient(
                90deg,
                rgba(0, 0, 0, 0.92) 0%,
                rgba(0, 0, 0, 0.75) 40%,
                rgba(0, 0, 0, 0.35) 70%,
                rgba(0, 0, 0, 0.1) 100%
            );
        }
    }

    /* Mobile — artwork sits cleanly in the top third, text sits in the dark lower portion.
       Avoids text overlapping the chip. */
    @media (max-width: 768px) {
        .esim-hero {
            margin-top: -248px;
            min-height: 878px;
            padding: 266px 18px 32px;
            background-size: auto 44%;
            background-position: center 266px; /* shift image down to clear the header band */
            display: flex;
            align-items: flex-end;
        }
        .esim-hero::before {
            background: linear-gradient(
                180deg,
                rgba(0, 0, 0, 0) 0%,
                rgba(0, 0, 0, 0.05) 30%,
                rgba(0, 0, 0, 0.7) 48%,
                rgba(0, 0, 0, 0.95) 65%,
                rgba(0, 0, 0, 0.98) 100%
            );
        }
        .esim-hero::after {
            display: none;
        }
        .esim-hero-inner {
            flex-direction: column;
            text-align: center;
        }
        .esim-hero-left {
            flex: 0 0 auto;
            max-width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .esim-hero-title {
            font-size: clamp(26px, 7.5vw, 34px);
            margin-bottom: 10px;
        }
        .esim-hero-subtitle {
            font-size: 13px;
            margin: 0 auto 16px;
            line-height: 1.55;
            max-width: 92%;
        }
        .esim-hero-cta {
            flex-direction: row;
            gap: 8px;
            justify-content: center;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }
        .esim-btn-primary {
            font-size: 11px;
            padding: 11px 18px;
        }
        .esim-btn-secondary {
            font-size: 11px;
            padding: 11px 16px;
        }
        .esim-trust-row {
            justify-content: center;
        }
        .esim-trust-pill {
            font-size: 10px;
            padding: 6px 10px;
        }
    }

    @media (max-width: 480px) {
        .esim-hero {
            min-height: 620px;
            background-size: auto 40%;
            background-position: center 14px;
        }
    }

    @media (max-width: 420px) {
        .esim-hero-badge {
            font-size: 10px;
            padding: 8px 14px;
            letter-spacing: 1.5px;
        }
        .esim-btn-secondary {
            display: none;
        }
    }

    @media (max-width: 991px) {
        .esim-bundles-list {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 767px) {
        .esim-step1 {
            padding: 20px 16px 24px !important;
        }

        .esim-features-grid,
        .esim-how-grid {
            gap: 12px;
        }

        .esim-feature-card,
        .esim-how-card {
            padding: 18px 14px;
        }

        .esim-wizard-container {
            margin-top: 0;
        }

        .esim-popular-grid {
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 6px;
        }

        .esim-popular-card {
            padding: 0 10px;
            height: 48px;
            gap: 10px;
            border-radius: 12px;
            min-width: 0;
            justify-content: center;
        }

        .esim-popular-flag {
            width: 22px;
            height: 16px;
        }

        .esim-popular-name {
            font-size: 17px;
            font-weight: 600;
        }

        .esim-country-grid {
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 12px;
        }

        .esim-country-card {
            padding: 0 10px;
            height: 48px;
            gap: 10px;
            border-radius: 12px;
            min-width: 0;
            overflow: hidden;
            justify-content: center;
        }

        .esim-country-flag {
            width: 22px;
            height: 16px;
        }

        .esim-country-name {
            font-size: 17px;
            font-weight: 600;
        }

        .esim-step1,
        .esim-step2,
        .esim-step3 {
            padding-left: 16px;
            padding-right: 16px;
        }

        .esim-step1 {
            padding-top: 8px;
        }

        .esim-section-label {
            font-size: 9px;
            letter-spacing: 2px;
            margin-bottom: 10px;
            text-align: center;
        }

        .esim-checkout-grid {
            flex-direction: column;
            gap: 20px;
            padding: 0 16px;
        }

        .esim-checkout-right {
            position: static;
            order: -1;
        }

        .esim-checkout-form-side {
            padding: 20px;
            flex: none;
        }

        .esim-checkout-summary-side {
            position: static;
            flex: none;
        }

        .esim-summary-card {
            padding: 20px;
        }

        .esim-form-row {
            grid-template-columns: 1fr;
            gap: 0;
        }

        .esim-checkout-title {
            font-size: 17px;
        }

        .esim-checkout-subtitle {
            font-size: 12px;
            margin-bottom: 20px;
        }

        .esim-input {
            height: 46px;
        }

        .esim-pay-btn {
            height: 48px;
            font-size: 12px;
        }

        .esim-summary-title {
            font-size: 15px;
            margin-bottom: 16px;
            padding-bottom: 12px;
        }

        .esim-price-row.total span:last-child {
            font-size: 20px;
        }

        .esim-features-grid {
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .esim-features-section {
            padding: 0 16px 0;
        }

        .esim-feature-card {
            padding: 14px 10px;
        }

        .esim-feature-icon {
            width: 36px;
            height: 36px;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .esim-feature-card-title {
            font-size: 13px;
            margin-bottom: 4px;
        }

        .esim-feature-card-desc {
            font-size: 11px;
            line-height: 1.4;
        }

        .esim-how-grid {
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .esim-how-grid::before {
            display: none;
        }

        .esim-how-section {
            padding: 0 16px 0;
        }

        .esim-how-card {
            padding: 14px 10px;
        }

        .esim-how-icon {
            width: 40px;
            height: 40px;
            margin-bottom: 8px;
        }

        .esim-how-icon img {
            width: 24px;
            height: 24px;
        }

        .esim-how-num {
            font-size: 10px;
            width: 20px;
            height: 20px;
            margin-bottom: 8px;
        }

        .esim-how-card-title {
            font-size: 12px;
            margin-bottom: 4px;
        }

        .esim-how-card-desc {
            font-size: 10px;
            line-height: 1.4;
        }

        .esim-faq-section {
            padding: 0 16px 28px;
        }

        .esim-faq-question {
            padding: 14px 16px;
        }

        .esim-faq-question-text {
            font-size: 13px;
        }

        .esim-faq-answer-inner {
            padding: 0 16px 14px;
            font-size: 12px;
        }

        .esim-selected-header {
            flex-wrap: wrap;
        }

        .esim-change-btn {
            margin-left: 0;
            margin-top: 8px;
        }

        .esim-progress-label {
            font-size: 9px;
        }

        .esim-progress-circle {
            width: 28px;
            height: 28px;
            font-size: 11px;
        }

        .esim-region-pills {
            gap: 6px;
            overflow-x: auto;
            flex-wrap: nowrap;
            padding-bottom: 8px;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }

        .esim-region-pills::-webkit-scrollbar {
            display: none;
        }

        .esim-region-pill {
            padding: 8px 18px;
            font-size: 12px;
            flex-shrink: 0;
            font-weight: 600;
        }

        .esim-summary-inner {
            padding: 16px;
        }

        .esim-pay-btn {
            font-size: 13px;
            padding: 14px;
        }
    }

    /* Tablet Bundle Grid (2 columns) */
    @media (max-width: 1024px) {
        /* Consolidated above */
        .esim-bundles-list {
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }
        .esim-bundle-card {
            width: auto;
            padding: 24px 15px 18px;
        }
    }

    /* Mobile Compact Grid (2 columns) */
    @media (max-width: 576px) {
        .esim-step2 {
            padding-top: 0;
        }
        .esim-selected-header {
            padding: 10px 15px;
            margin-bottom: 15px;
        }
        .esim-selected-name {
            font-size: 15px;
        }
        .esim-bundle-tabs {
            margin-bottom: 15px;
        }
        .esim-bundle-tab {
            padding: 0 15px;
            font-size: 10px;
            height: 34px;
        }
        .esim-bundles-list {
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
            margin-bottom: 20px;
        }
        .esim-bundle-card {
            padding: 24px 10px 15px;
            border-radius: 12px;
        }
        .esim-bundle-card.selected::after {
            top: 10px;
            right: 10px;
            font-size: 14px;
        }
        .esim-bundle-data {
            font-size: 20px;
            margin-bottom: 2px;
        }
        .esim-bundle-validity {
            font-size: 11px;
            margin-bottom: 15px;
        }
        .esim-bundle-divider {
            margin-bottom: 15px;
            width: 40px;
        }
        .esim-bundle-price {
            font-size: 15px;
            margin-bottom: 12px;
        }
        .esim-bundle-badge {
            font-size: 9px;
            padding: 2px 6px;
        }
        .esim-bundle-popular-tag {
            font-size: 6px;
            padding: 2px 6px;
            top: -8px;
        }
        .esim-continue-btn {
            width: 100%;
            padding: 14px 20px !important;
            font-size: 13px !important;
        }
        .esim-golden-divider {
            margin: 20px auto;
            padding: 20px 16px;
        }
        .esim-partner-cta-banner {
            margin: 16px auto;
        }
    }

    @media (max-width: 375px) {
        .esim-hero-title {
            font-size: 20px;
        }

        .esim-hero-subtitle {
            font-size: 10px;
        }

        .esim-phone-frame {
            width: 120px;
            height: 220px;
        }

        .esim-trust-pill {
            display: none;
        }

        .esim-trust-badges {
            flex-direction: column;
            gap: 6px;
        }

        .esim-popular-grid {
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 6px;
        }

        .esim-country-grid {
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 6px;
        }

        .esim-bundles-list {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 4px;
        }

        .esim-bundle-data {
            font-size: 13px;
        }

        .esim-bundle-price {
            font-size: 11px;
        }

        .esim-features-grid,
        .esim-how-grid {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .esim-feature-card,
        .esim-how-card {
            padding: 16px;
        }

        .esim-how-section,
        .esim-features-section {
            padding: 0 14px 0;
        }

        .esim-how-badge,
        .esim-features-badge {
            font-size: 8px;
            padding: 5px 14px;
            margin-bottom: 10px;
        }

        .esim-how-title,
        .esim-features-title {
            font-size: 20px;
            margin-bottom: 6px;
        }

        .esim-how-subtitle,
        .esim-features-subtitle {
            font-size: 12px;
            margin-bottom: 16px;
        }

        .esim-faq-section {
            padding: 0 14px 24px;
        }

        .esim-faq-badge {
            font-size: 8px;
            padding: 5px 14px;
        }

        .esim-faq-title {
            font-size: 20px;
            margin-bottom: 16px;
        }

        .esim-faq-question {
            padding: 14px 16px;
        }

        .esim-faq-question-text {
            font-size: 13px;
        }

        .esim-checkout-grid {
            gap: 14px;
            padding: 0 12px;
        }

        .esim-checkout-form-side,
        .esim-summary-card {
            padding: 16px;
        }

        .esim-checkout-title {
            font-size: 16px;
        }

        .esim-checkout-subtitle {
            font-size: 11px;
            margin-bottom: 16px;
        }

        .esim-input {
            height: 44px;
            font-size: 13px;
        }

        .esim-label {
            font-size: 9px;
        }

        .esim-trust-pill {
            font-size: 8px;
            padding: 5px 8px;
        }
        .esim-golden-divider {
            margin: 16px auto;
            padding: 16px 12px;
        }
        .esim-partner-cta-banner {
            margin: 12px auto;
        }
    }

    /* ============================================================
       ENTRANCE ANIMATIONS & WOW-FACTOR SYSTEM
       ============================================================ */

    /* --- Scroll-triggered reveal base (dramatic) --- */
    .esim-reveal {
        opacity: 0;
        transform: translateY(70px) scale(0.96);
        filter: blur(4px);
        transition: opacity 1s cubic-bezier(0.16, 1, 0.3, 1),
                    transform 1s cubic-bezier(0.16, 1, 0.3, 1),
                    filter 0.8s ease-out;
        will-change: opacity, transform, filter;
    }
    .esim-reveal.revealed {
        opacity: 1;
        transform: translateY(0) scale(1);
        filter: blur(0);
    }

    /* --- Slide-in from left (dramatic) --- */
    .esim-reveal-left {
        opacity: 0;
        transform: translateX(-80px);
        filter: blur(4px);
        transition: opacity 1s cubic-bezier(0.16, 1, 0.3, 1),
                    transform 1s cubic-bezier(0.16, 1, 0.3, 1),
                    filter 0.8s ease-out;
        will-change: opacity, transform, filter;
    }
    .esim-reveal-left.revealed {
        opacity: 1;
        transform: translateX(0);
        filter: blur(0);
    }

    /* --- Staggered children delay (for grids) --- */
    .esim-stagger > * {
        opacity: 0;
        transform: translateY(24px);
        transition: opacity 0.45s cubic-bezier(0.16, 1, 0.3, 1),
                    transform 0.45s cubic-bezier(0.16, 1, 0.3, 1);
        will-change: opacity, transform;
    }
    .esim-stagger.revealed > * {
        opacity: 1;
        transform: translateY(0);
    }
    /* Stagger delays — generated for up to 20 children */
    .esim-stagger.revealed > *:nth-child(1)  { transition-delay: 0.03s; }
    .esim-stagger.revealed > *:nth-child(2)  { transition-delay: 0.06s; }
    .esim-stagger.revealed > *:nth-child(3)  { transition-delay: 0.09s; }
    .esim-stagger.revealed > *:nth-child(4)  { transition-delay: 0.12s; }
    .esim-stagger.revealed > *:nth-child(5)  { transition-delay: 0.15s; }
    .esim-stagger.revealed > *:nth-child(6)  { transition-delay: 0.18s; }
    .esim-stagger.revealed > *:nth-child(7)  { transition-delay: 0.21s; }
    .esim-stagger.revealed > *:nth-child(8)  { transition-delay: 0.24s; }
    .esim-stagger.revealed > *:nth-child(9)  { transition-delay: 0.27s; }
    .esim-stagger.revealed > *:nth-child(10) { transition-delay: 0.30s; }
    .esim-stagger.revealed > *:nth-child(11) { transition-delay: 0.33s; }
    .esim-stagger.revealed > *:nth-child(12) { transition-delay: 0.36s; }
    .esim-stagger.revealed > *:nth-child(13) { transition-delay: 0.39s; }
    .esim-stagger.revealed > *:nth-child(14) { transition-delay: 0.42s; }
    .esim-stagger.revealed > *:nth-child(15) { transition-delay: 0.45s; }
    .esim-stagger.revealed > *:nth-child(16) { transition-delay: 0.48s; }
    .esim-stagger.revealed > *:nth-child(n+17) { transition-delay: 0.50s; }

    /* --- Hero section: dramatic staged entrance --- */
    .esim-hero-left .esim-hero-badge {
        opacity: 0;
        transform: translateY(60px) scale(0.9);
        animation: esimHeroStage 1s cubic-bezier(0.16, 1, 0.3, 1) 0.3s forwards;
    }
    .esim-hero-left .esim-hero-title {
        opacity: 0;
        transform: translateY(80px) scale(0.95);
        animation: esimHeroStage 1.1s cubic-bezier(0.16, 1, 0.3, 1) 0.5s forwards;
    }
    .esim-hero-left .esim-hero-subtitle {
        opacity: 0;
        transform: translateY(60px);
        animation: esimHeroStage 1s cubic-bezier(0.16, 1, 0.3, 1) 0.8s forwards;
    }
    .esim-hero-left .esim-hero-cta {
        opacity: 0;
        transform: translateY(50px) scale(0.9);
        animation: esimHeroStage 1s cubic-bezier(0.16, 1, 0.3, 1) 1.1s forwards;
    }
    .esim-hero-left .esim-trust-row {
        opacity: 0;
        transform: translateY(40px);
        animation: esimHeroStage 0.8s cubic-bezier(0.16, 1, 0.3, 1) 1.4s forwards;
    }
    @keyframes esimHeroStage {
        from { opacity: 0; transform: translateY(80px) scale(0.95); filter: blur(4px); }
        to   { opacity: 1; transform: translateY(0) scale(1); filter: blur(0); }
    }

    /* --- Animated counter glow/pulse for "186" --- */
    .esim-counter-number {
        display: inline-block;
        position: relative;
        font-variant-numeric: tabular-nums;
    }
    .esim-counter-number::after {
        content: '';
        position: absolute;
        inset: -12px -18px;
        border-radius: 16px;
        background: radial-gradient(ellipse, rgba(255, 215, 0, 0.15) 0%, transparent 70%);
        animation: esimCounterGlow 2.4s ease-in-out infinite;
        pointer-events: none;
        z-index: -1;
    }
    @keyframes esimCounterGlow {
        0%, 100% { opacity: 0.4; transform: scale(1); }
        50%      { opacity: 1;   transform: scale(1.12); }
    }

    /* --- Gold shimmer line divider between sections --- */
    .esim-shimmer-line {
        display: block;
        width: 100%;
        max-width: 600px;
        height: 1px;
        margin: 0 auto;
        position: relative;
        overflow: hidden;
        background: rgba(255, 215, 0, 0.06);
    }
    .esim-shimmer-line::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 60%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 215, 0, 0.6), rgba(255, 215, 0, 0.9), rgba(255, 215, 0, 0.6), transparent);
        animation: esimShimmerSlide 3s ease-in-out infinite;
    }
    @keyframes esimShimmerSlide {
        0%   { left: -60%; }
        100% { left: 100%; }
    }

    /* --- Parallax-like float for golden divider --- */
    .esim-golden-float {
        animation: esimGoldenFloat 6s ease-in-out infinite;
    }
    @keyframes esimGoldenFloat {
        0%, 100% { transform: translateY(0); }
        50%      { transform: translateY(-10px); }
    }

    /* --- FAQ smooth accordion height transition upgrade --- */
    .esim-faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.45s cubic-bezier(0.4, 0, 0.2, 1),
                    opacity 0.35s ease;
        opacity: 0;
    }
    .esim-faq-item.active .esim-faq-answer {
        opacity: 1;
    }
    .esim-faq-toggle {
        transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                    color 0.3s ease;
    }
    .esim-faq-item.active .esim-faq-question-text {
        color: var(--c-gold);
        transition: color 0.3s ease;
    }
    .esim-faq-item {
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    .esim-faq-item.active {
        box-shadow: 0 4px 24px rgba(255, 215, 0, 0.06);
    }

    /* --- Feature card stagger --- */
    .esim-features-grid.esim-stagger.revealed .esim-feature-card:nth-child(1) { transition-delay: 0.05s; }
    .esim-features-grid.esim-stagger.revealed .esim-feature-card:nth-child(2) { transition-delay: 0.12s; }
    .esim-features-grid.esim-stagger.revealed .esim-feature-card:nth-child(3) { transition-delay: 0.19s; }
    .esim-features-grid.esim-stagger.revealed .esim-feature-card:nth-child(4) { transition-delay: 0.26s; }

    /* --- How-to card slide-in-from-left stagger --- */
    .esim-how-grid.esim-stagger-left > * {
        opacity: 0;
        transform: translateX(-50px);
        transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1),
                    transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        will-change: opacity, transform;
    }
    .esim-how-grid.esim-stagger-left.revealed > * {
        opacity: 1;
        transform: translateX(0);
    }
    .esim-how-grid.esim-stagger-left.revealed > *:nth-child(1) { transition-delay: 0.05s; }
    .esim-how-grid.esim-stagger-left.revealed > *:nth-child(2) { transition-delay: 0.15s; }
    .esim-how-grid.esim-stagger-left.revealed > *:nth-child(3) { transition-delay: 0.25s; }
    .esim-how-grid.esim-stagger-left.revealed > *:nth-child(4) { transition-delay: 0.35s; }

    /* --- Reduce motion: respect user preference --- */
    @media (prefers-reduced-motion: reduce) {
        .esim-reveal,
        .esim-reveal-left,
        .esim-stagger > *,
        .esim-how-grid.esim-stagger-left > *,
        .esim-hero-left .esim-hero-badge,
        .esim-hero-left .esim-hero-title,
        .esim-hero-left .esim-hero-subtitle,
        .esim-hero-left .esim-hero-cta,
        .esim-hero-left .esim-trust-row {
            opacity: 1 !important;
            transform: none !important;
            animation: none !important;
            transition: none !important;
        }
        .esim-counter-number::after,
        .esim-shimmer-line::after {
            animation: none !important;
        }
        .esim-golden-float {
            animation: none !important;
        }
    }
</style>

<!-- ============================================================
     HERO SECTION — VERTICALLY CENTERED
     ============================================================ -->
<section class="esim-hero">
    <div class="esim-hero-inner">
        <!-- Text Content -->
        <div class="esim-hero-left">
            <div class="esim-hero-badge">TRAVEL eSIM</div>
            <h1 class="esim-hero-title">Your eSIM,<br><span class="esim-hero-title-highlight">Ready in 2 Minutes</span></h1>
            <p class="esim-hero-subtitle">Pick your destination below, choose a data plan, and we'll email your QR code instantly — no SIM swap, no roaming bills.</p>

            <!-- Primary CTA — jumps to the picker right below -->
            <div class="esim-hero-cta">
                <button class="esim-btn-primary" onclick="document.getElementById('esimWizardContainer').scrollIntoView({ behavior: 'smooth' })">
                    <i class="fa-solid fa-location-dot"></i>
                    Choose Your Destination
                </button>
            </div>

            <!-- Trust Indicators - Modern Pills -->
            <div class="esim-trust-row">
                <div class="esim-trust-pill rating">
                    <span class="stars">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                    </span>
                    <strong>4.9</strong> Rating
                </div>
                <div class="esim-trust-pill">
                    <i class="fa-solid fa-bolt"></i>
                    Instant Delivery
                </div>
                <div class="esim-trust-pill">
                    <i class="fa-solid fa-globe"></i>
                    180+ Countries
                </div>
                <div class="esim-trust-pill">
                    <i class="fa-solid fa-shield-check"></i>
                    Secure Payment
                </div>
            </div>
        </div>

    </div>
    {{-- Preload hero artwork so the section background paints fast (LCP) --}}
    <link rel="preload" as="image" href="{{ asset('assets/esim/hero-esim-network.webp') }}" type="image/webp" fetchpriority="high">
</section>

<!-- ============================================================
     WIZARD CONTAINER — practical buy box, directly under the hero
     ============================================================ -->
<div class="esim-wizard-container" id="esimWizardContainer">

    <!-- Progress Bar -->
    <div class="esim-progress-wrap" id="esimProgressWrap">
        <div class="esim-progress-step active" id="esimProgressStep1">
            <div class="esim-progress-circle" id="esimProgressCircle1">1</div>
            <div class="esim-progress-label">Choose Country</div>
        </div>
        <div class="esim-progress-line esim-progress-line-1">
            <div class="esim-progress-line-fill" id="esimProgressFill1"></div>
        </div>
        <div class="esim-progress-step" id="esimProgressStep2">
            <div class="esim-progress-circle" id="esimProgressCircle2">2</div>
            <div class="esim-progress-label">Select Plan</div>
        </div>
        <div class="esim-progress-line esim-progress-line-2">
            <div class="esim-progress-line-fill" id="esimProgressFill2"></div>
        </div>
        <div class="esim-progress-step" id="esimProgressStep3">
            <div class="esim-progress-circle" id="esimProgressCircle3">3</div>
            <div class="esim-progress-label">Payment</div>
        </div>
    </div>

    <!-- STEP 1: Choose Destination -->
    <section class="esim-wizard-step step-active" id="esimStep1">
        <div class="esim-step1">
            <div class="esim-section-label">POPULAR DESTINATIONS</div>
            <div class="esim-popular-grid" id="esimPopularGrid">
                <!-- Rendered by JS -->
            </div>

            <div class="esim-section-label">ALL COUNTRIES</div>
            <div class="esim-search-wrap">
                <input type="text" class="esim-search-input" id="esimSearchInput" placeholder="Search 186+ countries..." autocomplete="off">
                <i class="fa-solid fa-magnifying-glass esim-search-icon"></i>
            </div>

            <div class="esim-region-pills" id="esimRegionPills">
                <span class="esim-region-pill active" data-region="all">All</span>
                <span class="esim-region-pill" data-region="middle_east">Middle East</span>
                <span class="esim-region-pill" data-region="asia">Asia</span>
                <span class="esim-region-pill" data-region="europe">Europe</span>
                <span class="esim-region-pill" data-region="africa">Africa</span>
                <span class="esim-region-pill" data-region="north_america">North America</span>
                <span class="esim-region-pill" data-region="south_america">South America</span>
            </div>

            <div class="esim-country-grid" id="esimCountryGrid">
                <!-- Skeleton loaders rendered by JS on load -->
            </div>
        </div>
    </section>

    <!-- STEP 2: Select Plan -->
    <section class="esim-wizard-step" id="esimStep2">
        <div class="esim-step2">
            <div class="esim-selected-header">
                <div class="esim-selected-info">
                    <span class="esim-selected-flag" id="esimSelectedFlag"></span>
                    <span class="esim-selected-name" id="esimSelectedName"></span>
                </div>
                <button class="esim-change-btn" id="esimChangeBtn">
                    <i class="fa-solid fa-arrow-left" style="margin-right:8px;font-size:11px;"></i>Change Country
                </button>
            </div>

            <div class="esim-bundle-tabs">
                <button class="esim-bundle-tab active" id="esimTabData">Data Plans</button>
                <button class="esim-bundle-tab" id="esimTabUnlimited">Unlimited Plans</button>
            </div>

            <div class="esim-bundle-loading" id="esimBundleLoading" style="display:none;">
                <div class="spinner-ring"></div>
                <span>Loading available plans...</span>
            </div>

            <div class="esim-bundles-list" id="esimBundlesList">
                <!-- Bundles will be rendered here by JS -->
            </div>

            <button class="esim-show-all-btn" id="esimShowAllBtn">Show all plans</button>

            <div class="esim-continue-wrap" style="display: flex; justify-content: center; margin-top: 40px; width: 100%;">
                <button class="esim-continue-btn" id="esimContinueBtn" disabled>
                    Proceed to Checkout
                </button>
            </div>
        </div>
    </section>

    <!-- STEP 3: Checkout -->
    <section class="esim-wizard-step" id="esimStep3">
        <div class="esim-activation" id="esimActivation">
            <div class="esim-checkout-grid">
                <!-- Left: Form -->
                <div class="esim-checkout-form-side">
                    <h3 class="esim-checkout-title">Activation Details</h3>
                    <p class="esim-checkout-subtitle">Where should we send your eSIM QR code and activation instructions?</p>

                    <div class="esim-form-fields">
                        <div class="esim-form-group">
                            <label class="esim-label">Full Name</label>
                            <div class="esim-input-wrapper">
                                <i class="fa-solid fa-user"></i>
                                <input type="text" id="esimName" class="esim-input" placeholder="Enter your full name">
                            </div>
                            <span class="esim-error" id="esimNameError">Please enter your full name</span>
                        </div>

                        <div class="esim-form-row">
                            <div class="esim-form-group">
                                <label class="esim-label">Email Address</label>
                                <div class="esim-input-wrapper">
                                    <i class="fa-solid fa-envelope"></i>
                                    <input type="email" id="esimEmail" class="esim-input" placeholder="you@example.com">
                                </div>
                                <span class="esim-error" id="esimEmailError">Please enter a valid email address</span>
                            </div>
                            <div class="esim-form-group">
                                <label class="esim-label">Confirm Email</label>
                                <div class="esim-input-wrapper">
                                    <i class="fa-solid fa-check-double"></i>
                                    <input type="email" id="esimEmailConfirm" class="esim-input" placeholder="Re-enter email">
                                </div>
                                <span class="esim-error" id="esimEmailConfirmError">Emails do not match</span>
                            </div>
                        </div>

                        <div class="esim-form-group">
                            <label class="esim-label">WhatsApp / Phone Number</label>
                            <div class="esim-phone-wrap">
                                <select id="esimPhoneCC" class="esim-phone-cc" aria-label="Country code">
                                    <option value="+971" selected>🇦🇪 +971</option>
                                    <option value="+966">🇸🇦 +966</option>
                                    <option value="+974">🇶🇦 +974</option>
                                    <option value="+965">🇰🇼 +965</option>
                                    <option value="+973">🇧🇭 +973</option>
                                    <option value="+968">🇴🇲 +968</option>
                                    <option value="+91">🇮🇳 +91</option>
                                    <option value="+92">🇵🇰 +92</option>
                                    <option value="+880">🇧🇩 +880</option>
                                    <option value="+44">🇬🇧 +44</option>
                                    <option value="+1">🇺🇸 +1</option>
                                    <option value="+1">🇨🇦 +1</option>
                                    <option value="+61">🇦🇺 +61</option>
                                    <option value="+49">🇩🇪 +49</option>
                                    <option value="+33">🇫🇷 +33</option>
                                    <option value="+39">🇮🇹 +39</option>
                                    <option value="+34">🇪🇸 +34</option>
                                    <option value="+20">🇪🇬 +20</option>
                                    <option value="+962">🇯🇴 +962</option>
                                    <option value="+961">🇱🇧 +961</option>
                                    <option value="+90">🇹🇷 +90</option>
                                    <option value="+63">🇵🇭 +63</option>
                                    <option value="+62">🇮🇩 +62</option>
                                    <option value="+60">🇲🇾 +60</option>
                                    <option value="+65">🇸🇬 +65</option>
                                    <option value="+94">🇱🇰 +94</option>
                                    <option value="+977">🇳🇵 +977</option>
                                    <option value="+234">🇳🇬 +234</option>
                                    <option value="+254">🇰🇪 +254</option>
                                    <option value="+27">🇿🇦 +27</option>
                                    <option value="+55">🇧🇷 +55</option>
                                    <option value="+52">🇲🇽 +52</option>
                                    <option value="+54">🇦🇷 +54</option>
                                    <option value="+7">🇷🇺 +7</option>
                                    <option value="+86">🇨🇳 +86</option>
                                    <option value="+81">🇯🇵 +81</option>
                                    <option value="+82">🇰🇷 +82</option>
                                </select>
                                <div class="esim-input-wrapper">
                                    <i class="fa-solid fa-phone"></i>
                                    <input type="tel" id="esimPhone" class="esim-input" placeholder="50 123 4567" inputmode="tel" data-no-intl>
                                </div>
                            </div>
                            <span class="esim-error" id="esimPhoneError">Please enter a valid phone number</span>
                        </div>
                    </div>

                    <div class="esim-checkout-notice">
                        <i class="fa-solid fa-bolt"></i>
                        <span>Your eSIM QR code will be delivered instantly to your email after payment.</span>
                    </div>
                </div>

                <!-- Right: Summary -->
                <div class="esim-checkout-summary-side">
                    <div class="esim-summary-card">
                        <h4 class="esim-summary-title">Order Summary</h4>

                        <div id="esimSummaryEmpty" class="esim-summary-empty">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <p>Select a plan to see summary</p>
                        </div>

                        <div id="esimSummaryContent" class="esim-summary-content">
                            <!-- Top: Order Details -->
                            <div class="esim-summary-details">
                                <!-- Destination -->
                                <div class="esim-summary-section">
                                    <div class="esim-summary-label">DESTINATION</div>
                                    <div class="esim-summary-destination">
                                        <span id="esimSummaryFlag"></span>
                                        <span id="esimSummaryCountry">---</span>
                                    </div>
                                </div>

                                <!-- Plan Details -->
                                <div class="esim-summary-section">
                                    <div class="esim-summary-label">PLAN DETAILS</div>
                                    <div class="esim-summary-plan">
                                        <div class="esim-summary-plan-name" id="esimSummaryBundleName">---</div>
                                        <div class="esim-summary-plan-meta" id="esimSummaryBundleDetails">---</div>
                                        <div class="esim-summary-badges" id="esimSummaryBadges"></div>
                                    </div>
                                    <button class="esim-summary-change" id="esimSummaryChangePlan">Change Plan</button>
                                </div>
                            </div>

                            <!-- Bottom: Pricing & Payment (sticky to bottom) -->
                            <div class="esim-summary-footer">
                                <div class="esim-summary-pricing">
                                    <div class="esim-price-row">
                                        <span>Subtotal</span>
                                        <span id="esimSummarySubtotal">AED 0.00</span>
                                    </div>
                                    <div class="esim-price-row">
                                        <span>Activation Fee</span>
                                        <span class="free">FREE</span>
                                    </div>
                                    <div class="esim-price-row total">
                                        <span>Total</span>
                                        <span id="esimSummaryTotal">AED 0.00</span>
                                    </div>
                                </div>

                                <button class="esim-pay-btn" id="esimPayBtn" disabled>
                                    <span class="btn-label"><i class="fa-solid fa-lock"></i> PAY SECURELY — <span id="esimSummaryTotalBottom">AED 0.00</span></span>
                                    <span class="btn-loader">
                                        <i class="fa-solid fa-circle-notch fa-spin"></i>
                                    </span>
                                </button>

                                <div class="esim-payment-error" id="esimErrorMsg">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    <span></span>
                                </div>

                                <div class="esim-secure-footer">
                                    <i class="fa-solid fa-shield-halved"></i>
                                    Secure 256-bit SSL encrypted payment
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <button class="esim-back-link" id="esimBackToPlans">
                    <i class="fa-solid fa-arrow-left"></i> Back to Plans
                </button>
            </div>
        </div>
    </section>


</div>

<!-- ============================================================
     HOW TO ACTIVATE eSIM
     ============================================================ -->
<section class="esim-how-section">
    <div class="esim-how-badge esim-reveal">TUTORIAL</div>
    <h2 class="esim-how-title esim-reveal">How to Get Your eSIM</h2>
    <p class="esim-how-subtitle esim-reveal">A simple step-by-step guide to get you connected in minutes.</p>

    <div class="esim-how-grid esim-stagger-left">
        <!-- Step 1: Installation Part A -->
        <div class="esim-how-card">
            <div class="esim-how-icon">
                <img src="/assets/img/step1.png" alt="Check Compatibility">
            </div>
            <div class="esim-how-num">1</div>
            <h3 class="esim-how-card-title">Check Compatibility</h3>
            <p class="esim-how-card-desc">Make sure your device supports eSIM. Most modern smartphones (iPhone XS+, Pixel 3+, Samsung S20+) are compatible.</p>
        </div>

        <!-- Step 2: Installation Part B -->
        <div class="esim-how-card">
            <div class="esim-how-icon">
                <img src="/assets/img/step2.png" alt="Scan & Install eSIM">
            </div>
            <div class="esim-how-num">2</div>
            <h3 class="esim-how-card-title">Scan & Install eSIM</h3>
            <p class="esim-how-card-desc">Open your phone Settings → Cellular → Add eSIM. Scan the QR code we send to your email to install your profile.</p>
        </div>

        <!-- Step 3: Buying Part A -->
        <div class="esim-how-card">
            <div class="esim-how-icon">
                <img src="/assets/img/step3.png" alt="Choose Your Plan">
            </div>
            <div class="esim-how-num">3</div>
            <h3 class="esim-how-card-title">Choose Your Plan</h3>
            <p class="esim-how-card-desc">Select your destination country and pick a data plan that suits your travel duration and usage needs.</p>
        </div>

        <!-- Step 4: Buying Part B -->
        <div class="esim-how-card">
            <div class="esim-how-icon">
                <img src="/assets/img/step4.png" alt="Pay & Go Online">
            </div>
            <div class="esim-how-num">4</div>
            <h3 class="esim-how-card-title">Pay & Go Online</h3>
            <p class="esim-how-card-desc">Complete your secure payment. Your QR code is emailed instantly — activate and start browsing right away!</p>
        </div>
    </div>
</section>

<!-- ============================================================
     WHY CHOOSE eSIM
     ============================================================ -->
<section class="esim-features-section">
    <div class="esim-features-badge esim-reveal">WHY CHOOSE eSIM</div>
    <h2 class="esim-features-title esim-reveal">Travel Smarter, Stay Connected</h2>
    <p class="esim-features-subtitle esim-reveal">Skip the hassle of physical SIM cards. Our eSIM works with 186+ countries worldwide.</p>
    <div class="esim-features-grid esim-stagger">
        <div class="esim-feature-card">
            <div class="esim-feature-icon"><i class="fa-solid fa-bolt"></i></div>
            <div class="esim-feature-card-title">Instant Setup</div>
            <p class="esim-feature-card-desc">Activate your eSIM in seconds. No waiting for delivery or store visits.</p>
        </div>
        <div class="esim-feature-card">
            <div class="esim-feature-icon"><i class="fa-solid fa-piggy-bank"></i></div>
            <div class="esim-feature-card-title">Save Up to 90%</div>
            <p class="esim-feature-card-desc">Avoid expensive roaming charges. Pay local rates wherever you travel.</p>
        </div>
        <div class="esim-feature-card">
            <div class="esim-feature-icon"><i class="fa-solid fa-mobile-screen-button"></i></div>
            <div class="esim-feature-card-title">Dual SIM Ready</div>
            <p class="esim-feature-card-desc">Keep your original number active for calls while using eSIM for data.</p>
        </div>
        <div class="esim-feature-card">
            <div class="esim-feature-icon"><i class="fa-solid fa-headset"></i></div>
            <div class="esim-feature-card-title">24/7 Support</div>
            <p class="esim-feature-card-desc">Our travel connectivity experts are always ready to assist you.</p>
        </div>
    </div>
</section>


<!-- ============================================================
     FAQ
     ============================================================ -->
<section class="esim-faq-section">
    <div class="esim-faq-badge esim-reveal">FAQ</div>
    <h2 class="esim-faq-title esim-reveal">Common Questions</h2>
    <div class="esim-faq-list esim-stagger">
        <div class="esim-faq-item">
            <div class="esim-faq-question">
                <span class="esim-faq-question-text">What is an eSIM?</span>
                <span class="esim-faq-toggle">+</span>
            </div>
            <div class="esim-faq-answer">
                <div class="esim-faq-answer-inner">An eSIM is a digital SIM that allows you to activate a mobile data plan without a physical SIM card. It's built into most modern smartphones and can be activated by scanning a QR code.</div>
            </div>
        </div>
        <div class="esim-faq-item">
            <div class="esim-faq-question">
                <span class="esim-faq-question-text">Which devices support eSIM?</span>
                <span class="esim-faq-toggle">+</span>
            </div>
            <div class="esim-faq-answer">
                <div class="esim-faq-answer-inner">Most recent smartphones support eSIM, including iPhone XS and newer, Samsung Galaxy S20 and newer, Google Pixel 3 and newer, and many other Android devices. Check your device settings for eSIM compatibility.</div>
            </div>
        </div>
        <div class="esim-faq-item">
            <div class="esim-faq-question">
                <span class="esim-faq-question-text">When does my data plan activate?</span>
                <span class="esim-faq-toggle">+</span>
            </div>
            <div class="esim-faq-answer">
                <div class="esim-faq-answer-inner">Your eSIM data plan activates when you first connect to a supported network in your destination country. The validity period starts from the moment of first use, not from the time of purchase.</div>
            </div>
        </div>
        <div class="esim-faq-item">
            <div class="esim-faq-question">
                <span class="esim-faq-question-text">Can I use my regular SIM at the same time?</span>
                <span class="esim-faq-toggle">+</span>
            </div>
            <div class="esim-faq-answer">
                <div class="esim-faq-answer-inner">Yes! With eSIM, your phone runs dual SIM — your regular number stays active for calls and texts while the eSIM handles your data abroad. No need to swap SIM cards.</div>
            </div>
        </div>
        <div class="esim-faq-item">
            <div class="esim-faq-question">
                <span class="esim-faq-question-text">What happens if I run out of data?</span>
                <span class="esim-faq-toggle">+</span>
            </div>
            <div class="esim-faq-answer">
                <div class="esim-faq-answer-inner">If your plan supports top-up, you can purchase additional data through our platform. Otherwise, you can buy a new plan for the same destination.</div>
            </div>
        </div>
        <div class="esim-faq-item">
            <div class="esim-faq-question">
                <span class="esim-faq-question-text">How do I install the eSIM?</span>
                <span class="esim-faq-toggle">+</span>
            </div>
            <div class="esim-faq-answer">
                <div class="esim-faq-answer-inner">After purchase, you'll receive a QR code via email. Go to your phone's Settings, then Cellular/Mobile, then Add eSIM, then Scan QR Code. We recommend installing before your trip and activating on arrival.</div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     PARTNER CTA BANNER (E-SIM page) — main GoTrips site only
     ============================================================ -->
@platformOnly
<section class="esim-partner-cta-banner">
    <div class="epcb-inner">
        <div class="epcb-content">
            <div class="epcb-icon"><i class="fa-solid fa-handshake"></i></div>
            <div class="epcb-text">
                <span class="epcb-eyebrow">Partner Program</span>
                <h3 class="epcb-title">E-SIM Partner Login &amp; Earn Commissions</h3>
                <p class="epcb-sub">Refer travellers, earn on every eSIM sale — paid directly to your bank.</p>
            </div>
        </div>
        <a href="{{ route('referral.login') }}" class="epcb-btn">
            Click Here <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>
</section>
@endplatformOnly

<!-- ============================================================
     JAVASCRIPT
     ============================================================ -->
<script>
(function() {
    'use strict';

    // ── Globals ──────────────────────────────────────────
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    let allCountries = [];
    let currentRegion = 'all';
    let currentSearch = '';
    window.selectedCountry = null; // { name, iso2, iso3 }
    window.selectedBundle = null;
    let bundlesData = [];
    let currentStep = 1;
    let currentBundleTab = 'data';
    let showAllBundles = false;
    let progressShown = false;
    const BUNDLE_LIMIT = 6;

    // ── Popular Destinations ─────────────────────────────
    const POPULAR_DESTINATIONS = [
        { iso3: 'ARE', iso2: 'AE', name: 'United Arab Emirates', short: 'UAE' },
        { iso3: 'TUR', iso2: 'TR', name: 'Turkey', short: 'Turkey' },
        { iso3: 'THA', iso2: 'TH', name: 'Thailand', short: 'Thailand' },
        { iso3: 'GBR', iso2: 'GB', name: 'United Kingdom', short: 'UK' },
        { iso3: 'USA', iso2: 'US', name: 'United States', short: 'USA' },
        { iso3: 'SAU', iso2: 'SA', name: 'Saudi Arabia', short: 'Saudi Arabia' },
        { iso3: 'IND', iso2: 'IN', name: 'India', short: 'India' },
        { iso3: 'EGY', iso2: 'EG', name: 'Egypt', short: 'Egypt' },
        { iso3: 'FRA', iso2: 'FR', name: 'France', short: 'France' },
        { iso3: 'SGP', iso2: 'SG', name: 'Singapore', short: 'Singapore' },
    ];

    // ── Region mapping (ISO3 codes) ──────────────────────
    const REGION_MAP = {
        middle_east: ['ARE','SAU','QAT','BHR','KWT','OMN','JOR','LBN','EGY','IRQ','PSE','YEM','SYR','IRN','ISR','TUR'],
        asia: ['IND','PAK','BGD','LKA','NPL','BTN','MDV','AFG','CHN','JPN','KOR','PRK','MNG','TWN','HKG','MAC','THA','VNM','KHM','LAO','MMR','MYS','SGP','IDN','PHL','BRN','TLS','KAZ','UZB','TKM','TJK','KGZ','GEO','ARM','AZE'],
        europe: ['GBR','FRA','DEU','ITA','ESP','PRT','NLD','BEL','LUX','AUT','CHE','IRL','ISL','NOR','SWE','FIN','DNK','POL','CZE','SVK','HUN','ROU','BGR','HRV','SVN','SRB','BIH','MNE','MKD','ALB','GRC','CYP','MLT','EST','LVA','LTU','UKR','MDA','BLR','RUS'],
        africa: ['ZAF','NGA','KEN','GHA','TZA','UGA','ETH','RWA','SEN','CIV','CMR','AGO','MOZ','ZMB','ZWE','BWA','NAM','MUS','MDG','TUN','MAR','DZA','LBY','SDN','SSD','COD','COG','GAB','BEN','TGO','BFA','MLI','NER','TCD','GIN','SLE','LBR','MWI','SOM','DJI','ERI','CPV','SYC','COM','STP','GNQ','GMB','GNB','SWZ','LSO'],
        north_america: ['USA','CAN','MEX','GTM','BLZ','SLV','HND','NIC','CRI','PAN','CUB','JAM','HTI','DOM','TTO','BHS','BRB','ATG','DMA','GRD','KNA','LCA','VCT','PRI'],
        south_america: ['BRA','ARG','CHL','COL','PER','VEN','ECU','BOL','PRY','URY','GUY','SUR','GUF']
    };

    // ── Utility: ISO2 → Flag image HTML ──────────────────
    function getFlagImg(iso2, size) {
        if (!iso2 || iso2.length !== 2) return '';
        var w = size || 40;
        return '<img src="https://flagcdn.com/w' + w + '/' + iso2.toLowerCase() + '.png" alt="' + iso2 + '" loading="lazy" onerror="this.style.display=\'none\'">';
    }

    // ── Utility: Format price to AED ─────────────────────
    function fmtPrice(val) {
        var n = parseFloat(val);
        if (isNaN(n)) return 'AED 0.00';
        return 'AED ' + n.toFixed(2);
    }

    // ── Utility: Get data label for bundle ───────────────
    function bundleDataLabel(b) {
        if (b.unlimited) return 'Unlimited';
        var amount = b.gprs_limit || b.data_amount || 0;
        var unit = b.data_unit || 'GB';
        return amount + ' ' + unit;
    }

    // ── Wizard Navigation ────────────────────────────────
    function goToStep(n) {
        if (n < 1 || n > 3) return;
        currentStep = n;

        // Hide all steps
        var steps = document.querySelectorAll('.esim-wizard-step');
        steps.forEach(function(s) {
            s.classList.remove('step-active');
        });

        // Show target step
        var target = document.getElementById('esimStep' + n);
        if (target) {
            // Tiny delay for transition effect
            setTimeout(function() {
                target.classList.add('step-active');
            }, 50);
        }

        // Update progress bar
        updateProgressBar(n);

        // Show progress bar after first interaction
        if (!progressShown && n >= 1) {
            progressShown = true;
            document.getElementById('esimProgressWrap').classList.add('visible');
        }

        // Scroll to top of wizard
        setTimeout(function() {
            var heroEl = document.querySelector('.esim-hero');
            if (heroEl) {
                var progressEl = document.getElementById('esimProgressWrap');
                var scrollTarget = progressEl && progressEl.classList.contains('visible') ? progressEl : heroEl;
                scrollTarget.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 100);
    }

    function updateProgressBar(step) {
        var step1El = document.getElementById('esimProgressStep1');
        var step2El = document.getElementById('esimProgressStep2');
        var step3El = document.getElementById('esimProgressStep3');
        var circle1 = document.getElementById('esimProgressCircle1');
        var circle2 = document.getElementById('esimProgressCircle2');
        var circle3 = document.getElementById('esimProgressCircle3');
        var fill1 = document.getElementById('esimProgressFill1');
        var fill2 = document.getElementById('esimProgressFill2');

        // Reset all
        [step1El, step2El, step3El].forEach(function(el) {
            el.classList.remove('active', 'completed');
        });

        // Step 1
        if (step === 1) {
            step1El.classList.add('active');
            circle1.innerHTML = '1';
            circle2.innerHTML = '2';
            circle3.innerHTML = '3';
            fill1.style.width = '0%';
            fill2.style.width = '0%';
        }

        // Step 2
        if (step === 2) {
            step1El.classList.add('completed');
            step2El.classList.add('active');
            circle1.innerHTML = '<i class="fa-solid fa-check" style="font-size:12px;"></i>';
            circle2.innerHTML = '2';
            circle3.innerHTML = '3';
            fill1.style.width = '100%';
            fill2.style.width = '0%';
        }

        // Step 3
        if (step === 3) {
            step1El.classList.add('completed');
            step2El.classList.add('completed');
            step3El.classList.add('active');
            circle1.innerHTML = '<i class="fa-solid fa-check" style="font-size:12px;"></i>';
            circle2.innerHTML = '<i class="fa-solid fa-check" style="font-size:12px;"></i>';
            circle3.innerHTML = '3';
            fill1.style.width = '100%';
            fill2.style.width = '100%';
        }
    }

    // ── Render: Popular Destinations ─────────────────────
    function renderPopularDestinations() {
        var grid = document.getElementById('esimPopularGrid');
        var html = '';

        POPULAR_DESTINATIONS.forEach(function(d) {
            html += '<div class="esim-popular-card" data-iso3="' + d.iso3 + '" data-iso2="' + d.iso2 + '" data-name="' + d.name.replace(/"/g, '&quot;') + '">';
            html += '<span class="esim-popular-flag">' + getFlagImg(d.iso2, 40) + '</span>';
            html += '<span class="esim-popular-name">' + d.short + '</span>';
            html += '</div>';
        });

        grid.innerHTML = html;

        // Bind clicks
        grid.querySelectorAll('.esim-popular-card').forEach(function(card) {
            card.addEventListener('click', function() {
                onCountryClick(this.dataset.iso3, this.dataset.iso2, this.dataset.name);
            });
        });
    }

    // ── Render: Skeleton Loaders ─────────────────────────
    function renderSkeletons() {
        var grid = document.getElementById('esimCountryGrid');
        var html = '';
        for (var i = 0; i < 20; i++) {
            html += '<div class="esim-skeleton-card"><div class="esim-skeleton-flag"></div><div class="esim-skeleton-text"></div></div>';
        }
        grid.innerHTML = html;
    }

    // ── Render: Country Grid ─────────────────────────────
    function renderCountries() {
        var grid = document.getElementById('esimCountryGrid');
        var filtered = filterCountries();

        if (filtered.length === 0) {
            grid.innerHTML = '<div class="esim-no-results"><i class="fa-solid fa-earth-americas"></i>No countries found matching your search</div>';
            return;
        }

        var html = '';
        filtered.forEach(function(c) {
            var name = c.country_name || c.name || '';
            var iso3 = c.iso3_code || c.iso3 || '';
            var iso2 = c.iso2_code || c.iso2 || '';
            var isActive = window.selectedCountry && window.selectedCountry.iso3 === iso3 ? ' active-country' : '';
            html += '<div class="esim-country-card' + isActive + '" data-iso3="' + iso3 + '" data-iso2="' + iso2 + '" data-name="' + name.replace(/"/g, '&quot;') + '">';
            html += '<span class="esim-country-flag">' + getFlagImg(iso2, 40) + '</span>';
            html += '<span class="esim-country-name">' + name + '</span>';
            html += '</div>';
        });
        grid.innerHTML = html;

        // Bind click events
        grid.querySelectorAll('.esim-country-card').forEach(function(card) {
            card.addEventListener('click', function() {
                onCountryClick(
                    this.dataset.iso3,
                    this.dataset.iso2,
                    this.dataset.name
                );
            });
        });
    }

    // ── Filter: Countries ────────────────────────────────
    function filterCountries() {
        var list = allCountries;

        // Region filter
        if (currentRegion !== 'all' && REGION_MAP[currentRegion]) {
            var codes = REGION_MAP[currentRegion];
            list = list.filter(function(c) {
                var iso3 = c.iso3_code || c.iso3 || '';
                return codes.indexOf(iso3) !== -1;
            });
        }

        // Search filter
        if (currentSearch.trim() !== '') {
            var q = currentSearch.trim().toLowerCase();
            list = list.filter(function(c) {
                var name = (c.country_name || c.name || '').toLowerCase();
                return name.indexOf(q) !== -1;
            });
        }

        return list;
    }

    // ── Event: Search Input ──────────────────────────────
    document.getElementById('esimSearchInput').addEventListener('input', function() {
        currentSearch = this.value;
        renderCountries();
    });

    // ── Event: Region Pills ──────────────────────────────
    document.getElementById('esimRegionPills').addEventListener('click', function(e) {
        var pill = e.target.closest('.esim-region-pill');
        if (!pill) return;
        this.querySelectorAll('.esim-region-pill').forEach(function(p) { p.classList.remove('active'); });
        pill.classList.add('active');
        currentRegion = pill.dataset.region;
        renderCountries();
    });

    // ── Event: Country Click ─────────────────────────────
    function onCountryClick(iso3, iso2, name) {
        window.selectedCountry = { iso3: iso3, iso2: iso2, name: name };
        window.selectedBundle = null;
        bundlesData = [];
        showAllBundles = false;
        currentBundleTab = 'data';

        // Update Step 2 header
        document.getElementById('esimSelectedFlag').innerHTML = getFlagImg(iso2, 80);
        document.getElementById('esimSelectedName').textContent = name;

        // Reset tabs to Data Plans active
        document.getElementById('esimTabData').classList.add('active');
        document.getElementById('esimTabUnlimited').classList.remove('active');

        // Keep continue button enabled (demo bundles are always available)
        // document.getElementById('esimContinueBtn').disabled = true;

        // Reset summary
        resetSummary();

        // Navigate to step 2
        goToStep(2);

        // Load bundles
        loadBundles(iso3);
    }

    // ── Event: Change Button (Step 2 → Step 1) ──────────
    document.getElementById('esimChangeBtn').addEventListener('click', function() {
        goToStep(1);
    });

    // ── Event: Back to Plans (Step 3 → Step 2) ──────────
    document.getElementById('esimBackToPlans').addEventListener('click', function() {
        goToStep(2);
    });

    // ── Event: Summary Change Plan (Step 3 → Step 2) ────
    document.getElementById('esimSummaryChangePlan').addEventListener('click', function() {
        goToStep(2);
    });

    // ── Event: Continue Button (Step 2 → Step 3) ────────
    document.getElementById('esimContinueBtn').addEventListener('click', function() {
        if (!window.selectedBundle && bundlesData.length > 0) {
            // Find most popular or first bundle as default if none selected
            var popularIdx = findMostPopularIndex(currentBundleTab === 'data' ? getDataBundles() : getUnlimitedBundles());
            if (popularIdx !== -1) {
                var activeBundles = currentBundleTab === 'data' ? getDataBundles() : getUnlimitedBundles();
                window.selectedBundle = activeBundles[popularIdx];
            } else {
                window.selectedBundle = bundlesData[0];
            }
        }

        if (!window.selectedBundle) {
            alert('Please select a plan to continue');
            return;
        }

        console.log('Continuing to checkout with:', window.selectedBundle, window.selectedCountry);
        updateSummary();
        goToStep(3);

        // Smooth scroll to checkout form with header offset
        setTimeout(function() {
            var checkoutSection = document.getElementById('esimStep3');
            var headerHeight = 70;
            var elementTop = checkoutSection.getBoundingClientRect().top + window.pageYOffset;
            window.scrollTo({
                top: elementTop - headerHeight,
                behavior: 'smooth'
            });
        }, 50);
    });


    // ── Event: Bundle Tabs ───────────────────────────────
    document.getElementById('esimTabData').addEventListener('click', function() {
        currentBundleTab = 'data';
        showAllBundles = false;
        window.selectedBundle = null; // Clear selection when switching tabs to force fresh selection
        this.classList.add('active');
        document.getElementById('esimTabUnlimited').classList.remove('active');
        renderBundles();
    });

    document.getElementById('esimTabUnlimited').addEventListener('click', function() {
        currentBundleTab = 'unlimited';
        showAllBundles = false;
        window.selectedBundle = null; // Clear selection when switching tabs
        this.classList.add('active');
        document.getElementById('esimTabData').classList.remove('active');
        renderBundles();
    });

    // ── Event: Show All Button ───────────────────────────
    document.getElementById('esimShowAllBtn').addEventListener('click', function() {
        showAllBundles = true;
        renderBundles();
    });

    // ── Load: Bundles ────────────────────────────────────
    function loadBundles(countryCode) {
        var loading = document.getElementById('esimBundleLoading');
        var list = document.getElementById('esimBundlesList');
        var showAllBtn = document.getElementById('esimShowAllBtn');

        console.log('Loading bundles for country:', countryCode);

        loading.style.display = 'flex';
        list.innerHTML = '';
        showAllBtn.classList.remove('visible');

        fetch('/esim/bundles', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ country_code: countryCode })
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            loading.style.display = 'none';
            console.log('eSIM API Response:', data);

            if (data.success && data.bundles && data.bundles.length > 0) {
                console.log('Using API bundles:', data.bundles.length);
                bundlesData = data.bundles;
                renderBundles();
            } else {
                console.log('No API bundles found or success=false, loading demo plans...');
                loadDemoBundles();
            }
        })
        .catch(function(err) {
            console.error('Bundle fetch error:', err);
            loading.style.display = 'none';
            loadDemoBundles();
        });
    }

    // ── Demo Bundles Fallback ────────────────────────────
    function loadDemoBundles() {
        var loading = document.getElementById('esimBundleLoading');
        if (loading) loading.style.display = 'none';

        bundlesData = [
            { 
                bundle_code: 'esim_1GB_7D', 
                bundle_name: '1 GB - 7 Days', 
                bundle_marketing_name: '1 GB Starter Plan', 
                gprs_limit: 1, 
                data_unit: 'GB', 
                validity: 7, 
                selling_price: 5.00, 
                cost_price: 3.50, 
                unlimited: false, 
                supports_calls_sms: false, 
                support_topup: false,
                isMockData: true 
            },
            { 
                bundle_code: 'esim_3GB_15D', 
                bundle_name: '3 GB - 15 Days', 
                bundle_marketing_name: '3 GB Explorer Plan', 
                gprs_limit: 3, 
                data_unit: 'GB', 
                validity: 15, 
                selling_price: 10.00, 
                cost_price: 7.00, 
                unlimited: false, 
                supports_calls_sms: false, 
                support_topup: true,
                isMockData: true 
            },
            { 
                bundle_code: 'esim_5GB_30D', 
                bundle_name: '5 GB - 30 Days', 
                bundle_marketing_name: '5 GB Voyager Plan', 
                gprs_limit: 5, 
                data_unit: 'GB', 
                validity: 30, 
                selling_price: 15.00, 
                cost_price: 11.00, 
                unlimited: false, 
                supports_calls_sms: true, 
                support_topup: true,
                isMockData: true 
            },
            { 
                bundle_code: 'esim_UNL_30D', 
                bundle_name: 'Unlimited - 30 Days', 
                bundle_marketing_name: 'Unlimited Monthly Elite', 
                gprs_limit: 0, 
                data_unit: 'GB', 
                validity: 30, 
                selling_price: 25.00, 
                cost_price: 18.00, 
                unlimited: true, 
                supports_calls_sms: true, 
                support_topup: true,
                isMockData: true 
            }
        ];
        console.log('Demo bundles loaded:', bundlesData.length);
        renderBundles();
    }

    // ── Categorize bundles ───────────────────────────────
    function getDataBundles() {
        if (!bundlesData || !Array.isArray(bundlesData)) return [];
        return bundlesData.filter(function(b) {
            return !b.unlimited || b.unlimited === '0' || b.unlimited === 0 || b.unlimited === false;
        });
    }

    function getUnlimitedBundles() {
        if (!bundlesData || !Array.isArray(bundlesData)) return [];
        return bundlesData.filter(function(b) {
            return b.unlimited === true || b.unlimited === 1 || b.unlimited === '1' || b.unlimited === 'true';
        });
    }

    // ── Find "Most Popular" bundle ───────────────────────
    function findMostPopularIndex(bundles) {
        for (var i = 0; i < bundles.length; i++) {
            var b = bundles[i];
            var validity = b.validity || 0;
            var gprs = b.gprs_limit || b.data_amount || 0;
            if (!b.unlimited && validity >= 7 && validity <= 15 && gprs >= 5 && gprs <= 10) {
                return i;
            }
        }
        return -1;
    }

    // ── Render: Bundle Cards ─────────────────────────────
    function renderBundles() {
        var list = document.getElementById('esimBundlesList');
        var showAllBtn = document.getElementById('esimShowAllBtn');
        
        console.log('Rendering bundles. currentTab:', currentBundleTab, 'total bundles:', (bundlesData ? bundlesData.length : 0));

        var activeBundles = currentBundleTab === 'data' ? getDataBundles() : getUnlimitedBundles();
        console.log('Active bundles:', activeBundles.length);

        if (activeBundles.length === 0) {
            list.innerHTML = '<div class="esim-no-results" style="grid-column:1/-1;"><i class="fa-solid fa-sim-card"></i>No ' + (currentBundleTab === 'data' ? 'data' : 'unlimited') + ' plans available for this country</div>';
            showAllBtn.classList.remove('visible');
            return;
        }

        var mostPopularIdx = currentBundleTab === 'data' ? findMostPopularIndex(activeBundles) : -1;
        
        // Auto-select most popular on first render if nothing selected
        if (!window.selectedBundle && mostPopularIdx !== -1) {
            window.selectedBundle = activeBundles[mostPopularIdx];
            document.getElementById('esimContinueBtn').disabled = false;
        } else if (!window.selectedBundle && activeBundles.length > 0) {
            window.selectedBundle = activeBundles[0];
            document.getElementById('esimContinueBtn').disabled = false;
        }

        var displayBundles = activeBundles;
        var truncated = false;

        if (!showAllBundles && activeBundles.length > BUNDLE_LIMIT) {
            displayBundles = activeBundles.slice(0, BUNDLE_LIMIT);
            truncated = true;
        }

        var html = '';

        displayBundles.forEach(function(b, idx) {
            var dataLabel = bundleDataLabel(b);
            var validity = b.validity || 0;
            var price = b.selling_price || 0;
            var name = b.bundle_marketing_name || b.bundle_name || 'eSIM Plan';
            var code = b.bundle_code || '';
            var hasCalls = b.supports_calls_sms || b.voice_included || false;
            var hasTopup = b.support_topup || b.topup_supported || false;
            // Find the global index in bundlesData for this bundle
            var globalIndex = bundlesData.indexOf(b);
            
            // Check if selected by code
            var isSelected = (window.selectedBundle && window.selectedBundle.bundle_code === code);
            var isPopular = (idx === mostPopularIdx);

            html += '<div class="esim-bundle-card' + (isSelected ? ' selected' : '') + '" data-index="' + globalIndex + '" data-code="' + code + '">';
            if (isPopular) {
                html += '<div class="esim-bundle-popular-tag">MOST POPULAR</div>';
            }
            html += '<div class="esim-bundle-data">' + dataLabel + '</div>';
            html += '<div class="esim-bundle-validity">' + validity + ' Days</div>';
            html += '<div class="esim-bundle-divider"></div>';
            html += '<div class="esim-bundle-price">' + fmtPrice(price) + '</div>';
            html += '<div class="esim-bundle-badges">';
            if (hasCalls) {
                html += '<span class="esim-bundle-badge">Calls & SMS</span>';
            } else {
                html += '<span class="esim-bundle-badge">Data Only</span>';
            }
            if (hasTopup) {
                html += '<span class="esim-bundle-badge">Top-up</span>';
            }
            html += '</div>';
            html += '</div>';
        });

        list.innerHTML = html;

        // Show/hide "Show all" button
        if (truncated) {
            showAllBtn.textContent = 'Show all ' + activeBundles.length + ' plans';
            showAllBtn.classList.add('visible');
        } else {
            showAllBtn.classList.remove('visible');
        }

        // Bind click events
        list.querySelectorAll('.esim-bundle-card').forEach(function(card) {
            card.addEventListener('click', function() {
                onBundleClick(parseInt(this.dataset.index));
            });
        });
    }

    // ── Event: Bundle Click ──────────────────────────────
    function onBundleClick(index) {
        window.selectedBundle = bundlesData[index];

        // Update selected class
        document.querySelectorAll('.esim-bundle-card').forEach(function(c) { c.classList.remove('selected'); });
        var selectedCard = document.querySelector('.esim-bundle-card[data-index="' + index + '"]');
        if (selectedCard) selectedCard.classList.add('selected');

        // Enable continue button
        document.getElementById('esimContinueBtn').disabled = false;
    }

    // ── Summary: Reset ───────────────────────────────────
    function resetSummary() {
        window.selectedBundle = null;
        document.getElementById('esimSummaryEmpty').style.display = 'block';
        document.getElementById('esimSummaryContent').classList.remove('visible');
        document.getElementById('esimPayBtn').disabled = true;
        updatePayBtnLabel(null);
        document.getElementById('esimErrorMsg').classList.remove('visible');
    }

    // ── Summary: Update ──────────────────────────────────
    function updateSummary() {
        var bundle = window.selectedBundle;
        var country = window.selectedCountry;

        if (!bundle || !country) {
            console.log('Missing bundle or country for summary', bundle, country);
            return;
        }

        document.getElementById('esimSummaryEmpty').style.display = 'none';
        document.getElementById('esimSummaryContent').classList.add('visible');

        document.getElementById('esimSummaryFlag').innerHTML = getFlagImg(country.iso2, 40);
        document.getElementById('esimSummaryCountry').textContent = country.name;

        var name = bundle.bundle_marketing_name || bundle.bundle_name || 'eSIM Plan';
        var dataLabel = bundleDataLabel(bundle);
        var validity = bundle.validity || 0;
        var price = bundle.selling_price || 0;
        var hasCalls = bundle.supports_calls_sms || bundle.voice_included || false;
        var hasTopup = bundle.support_topup || bundle.topup_supported || false;

        document.getElementById('esimSummaryBundleName').textContent = name;
        document.getElementById('esimSummaryBundleDetails').textContent = dataLabel + ' \u2022 ' + validity + ' Days';

        // Badges
        var badgeHtml = '';
        if (hasCalls) {
            badgeHtml += '<span class="esim-bundle-badge">Calls & SMS</span>';
        } else {
            badgeHtml += '<span class="esim-bundle-badge">Data Only</span>';
        }
        if (hasTopup) {
            badgeHtml += '<span class="esim-bundle-badge">Top-up</span>';
        }
        document.getElementById('esimSummaryBadges').innerHTML = badgeHtml;

        document.getElementById('esimSummarySubtotal').textContent = fmtPrice(price);
        document.getElementById('esimSummaryTotal').textContent = fmtPrice(price);
        document.getElementById('esimSummaryTotalBottom').textContent = fmtPrice(price);

        // Update pay button label with price
        updatePayBtnLabel(price);

        // Enable pay button
        document.getElementById('esimPayBtn').disabled = false;
    }

    // ── Pay Button Label ─────────────────────────────────
    function updatePayBtnLabel(price) {
        var label = document.querySelector('#esimPayBtn .btn-label');
        var priceSpan = document.getElementById('esimSummaryTotalBottom');
        
        if (priceSpan) {
            priceSpan.textContent = fmtPrice(price);
        } else if (label) {
            if (price !== null && price !== undefined) {
                label.textContent = 'PAY SECURELY \u2014 ' + fmtPrice(price);
            } else {
                label.textContent = 'PAY SECURELY';
            }
        }
    }

    // ── Validation ───────────────────────────────────────
    function validateForm() {
        var valid = true;

        // Name
        var name = document.getElementById('esimName').value.trim();
        if (!name) {
            showFieldError('esimName', 'esimNameError');
            valid = false;
        } else {
            clearFieldError('esimName', 'esimNameError');
        }

        // Phone
        var phone = document.getElementById('esimPhone').value.trim();
        if (!phone) {
            showFieldError('esimPhone', 'esimPhoneError');
            valid = false;
        } else {
            clearFieldError('esimPhone', 'esimPhoneError');
        }

        // Email
        var email = document.getElementById('esimEmail').value.trim();
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email || !emailRegex.test(email)) {
            showFieldError('esimEmail', 'esimEmailError');
            valid = false;
        } else {
            clearFieldError('esimEmail', 'esimEmailError');
        }

        // Confirm Email
        var emailConfirm = document.getElementById('esimEmailConfirm').value.trim();
        if (!emailConfirm || emailConfirm !== email) {
            showFieldError('esimEmailConfirm', 'esimEmailConfirmError');
            valid = false;
        } else {
            clearFieldError('esimEmailConfirm', 'esimEmailConfirmError');
        }

        return valid;
    }

    function showFieldError(inputId, errorId) {
        document.getElementById(inputId).classList.add('input-error');
        document.getElementById(errorId).classList.add('visible');
    }

    function clearFieldError(inputId, errorId) {
        document.getElementById(inputId).classList.remove('input-error');
        document.getElementById(errorId).classList.remove('visible');
    }

    // Clear errors on input
    ['esimName', 'esimEmail', 'esimEmailConfirm', 'esimPhone'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', function() {
                this.classList.remove('input-error');
                var errEl = document.getElementById(id + 'Error');
                if (errEl) errEl.classList.remove('visible');
            });
        }
    });

    // ── Event: Pay Button ────────────────────────────────
    document.getElementById('esimPayBtn').addEventListener('click', function() {
        var bundle = window.selectedBundle;
        var country = window.selectedCountry;

        if (!bundle || !country) {
            console.log('Missing bundle or country for payment');
            return;
        }
        if (this.disabled) return;

        // Validate
        if (!validateForm()) return;

        var btn = this;
        var errorMsg = document.getElementById('esimErrorMsg');
        errorMsg.classList.remove('visible');

        // Loading state
        btn.disabled = true;
        btn.classList.add('loading');

        var payload = {
            name: document.getElementById('esimName').value.trim(),
            email: document.getElementById('esimEmail').value.trim(),
            phone: (function() {
                var cc = document.getElementById('esimPhoneCC');
                var num = document.getElementById('esimPhone').value.trim();
                if (!num) return null;
                // Build a clean E.164 number (Nomod requires it): de-dupe the
                // country code if the user typed it into the number field, and
                // drop any national trunk-prefix zero.
                var ccDigits = (cc ? cc.value : '').replace(/\D/g, '');
                var pnDigits = num.replace(/\D/g, '');
                if (ccDigits && pnDigits.indexOf(ccDigits) === 0) pnDigits = pnDigits.slice(ccDigits.length);
                pnDigits = pnDigits.replace(/^0+/, '');
                return '+' + ccDigits + pnDigits;
            })(),
            bundle_code: bundle.bundle_code,
            country_code: country.iso3,
            country_name: country.name
        };

        console.log('Sending payment request:', payload);

        fetch('/esim/purchase', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success && data.checkout_url) {
                window.location.href = data.checkout_url;
            } else {
                btn.disabled = false;
                btn.classList.remove('loading');
                errorMsg.textContent = data.error || 'Something went wrong. Please try again.';
                errorMsg.classList.add('visible');
            }
        })
        .catch(function() {
            btn.disabled = false;
            btn.classList.remove('loading');
            errorMsg.textContent = 'Network error. Please check your connection and try again.';
            errorMsg.classList.add('visible');
        });
    });

    // ── FAQ Accordion ────────────────────────────────────
    document.querySelectorAll('.esim-faq-question').forEach(function(q) {
        q.addEventListener('click', function() {
            var item = this.closest('.esim-faq-item');
            var answer = item.querySelector('.esim-faq-answer');
            var inner = item.querySelector('.esim-faq-answer-inner');
            var isOpen = item.classList.contains('active');

            // Close all others
            document.querySelectorAll('.esim-faq-item.active').forEach(function(other) {
                if (other !== item) {
                    other.classList.remove('active');
                    other.querySelector('.esim-faq-answer').style.maxHeight = '0';
                }
            });

            if (isOpen) {
                item.classList.remove('active');
                answer.style.maxHeight = '0';
            } else {
                item.classList.add('active');
                answer.style.maxHeight = inner.scrollHeight + 20 + 'px';
            }
        });
    });

    // ── Init ─────────────────────────────────────────────
    function init() {
        // Render popular destinations
        renderPopularDestinations();

        // Render skeleton loaders for all countries
        renderSkeletons();

        // Fetch countries
        fetch('/api/esim/countries', {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success && data.countries && data.countries.length > 0) {
                allCountries = data.countries;
                // Sort alphabetically
                allCountries.sort(function(a, b) {
                    var nameA = (a.country_name || a.name || '').toLowerCase();
                    var nameB = (b.country_name || b.name || '').toLowerCase();
                    return nameA < nameB ? -1 : nameA > nameB ? 1 : 0;
                });
                renderCountries();
            } else {
                document.getElementById('esimCountryGrid').innerHTML = '<div class="esim-no-results"><i class="fa-solid fa-triangle-exclamation"></i>Unable to load countries. Please refresh the page.</div>';
            }
        })
        .catch(function() {
            document.getElementById('esimCountryGrid').innerHTML = '<div class="esim-no-results"><i class="fa-solid fa-triangle-exclamation"></i>Unable to load countries. Please refresh the page.</div>';
        });
    }

    // Kick off
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // ── ENTRANCE ANIMATIONS — IntersectionObserver ───────
    (function initAnimations() {
        // Respect reduced-motion preference
        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.querySelectorAll('.esim-reveal, .esim-reveal-left, .esim-stagger, .esim-stagger-left').forEach(function(el) {
                el.classList.add('revealed');
            });
            // Set counter directly
            var ctr = document.getElementById('esimCounterNum');
            if (ctr) ctr.textContent = ctr.dataset.target || '186';
            return;
        }

        var revealObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

        // Observe all reveal elements
        document.querySelectorAll('.esim-reveal, .esim-reveal-left, .esim-stagger, .esim-stagger-left').forEach(function(el) {
            revealObserver.observe(el);
        });

        // ── Animated Counter for "186 Countries" ─────────
        var counterEl = document.getElementById('esimCounterNum');
        if (counterEl) {
            var counterDone = false;
            var counterObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting && !counterDone) {
                        counterDone = true;
                        animateCounter(counterEl, parseInt(counterEl.dataset.target) || 186, 1800);
                        counterObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.3 });
            counterObserver.observe(counterEl);
        }

        function animateCounter(el, target, duration) {
            var start = 0;
            var startTime = null;
            // Ease-out-cubic for a satisfying deceleration
            function easeOutCubic(t) { return 1 - Math.pow(1 - t, 3); }
            function step(timestamp) {
                if (!startTime) startTime = timestamp;
                var progress = Math.min((timestamp - startTime) / duration, 1);
                var easedProgress = easeOutCubic(progress);
                var current = Math.floor(easedProgress * target);
                el.textContent = current;
                if (progress < 1) {
                    requestAnimationFrame(step);
                } else {
                    el.textContent = target;
                }
            }
            requestAnimationFrame(step);
        }

        // ── Dynamic stagger for country grid cards ───────
        // Re-observe after countries render (MutationObserver)
        var countryGrid = document.getElementById('esimCountryGrid');
        if (countryGrid) {
            countryGrid.classList.add('esim-stagger');
            var gridMutObs = new MutationObserver(function() {
                // Re-trigger stagger when grid content changes
                countryGrid.classList.remove('revealed');
                // Small RAF delay so the browser registers the removal
                requestAnimationFrame(function() {
                    requestAnimationFrame(function() {
                        countryGrid.classList.add('revealed');
                    });
                });
            });
            gridMutObs.observe(countryGrid, { childList: true });
        }

        // ── Dynamic stagger for popular destinations grid ─
        var popularGrid = document.getElementById('esimPopularGrid');
        if (popularGrid) {
            popularGrid.classList.add('esim-stagger');
            var popMutObs = new MutationObserver(function() {
                popularGrid.classList.remove('revealed');
                requestAnimationFrame(function() {
                    requestAnimationFrame(function() {
                        popularGrid.classList.add('revealed');
                    });
                });
            });
            popMutObs.observe(popularGrid, { childList: true });
        }
    })();

})();
</script>

@include('footer')
