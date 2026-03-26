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

    body { background-color: var(--c-dark-bg); }

    /* ============================================================
       HERO BANNER
       ============================================================ */
    .esim-hero {
        position: relative;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 30px 28px 0;
        margin-bottom: 0;
        background: linear-gradient(180deg, #000 0%, #080808 50%, var(--c-dark-bg) 100%);
        overflow: hidden;
    }

    .esim-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 20% 30%, rgba(255, 215, 0, 0.015) 0%, transparent 50%),
            radial-gradient(circle at 80% 70%, rgba(255, 215, 0, 0.01) 0%, transparent 50%),
            radial-gradient(1px 1px at 10% 20%, rgba(255, 215, 0, 0.08) 50%, transparent 50%),
            radial-gradient(1px 1px at 30% 60%, rgba(255, 215, 0, 0.06) 50%, transparent 50%),
            radial-gradient(1px 1px at 50% 10%, rgba(255, 215, 0, 0.07) 50%, transparent 50%),
            radial-gradient(1px 1px at 70% 40%, rgba(255, 215, 0, 0.05) 50%, transparent 50%),
            radial-gradient(1px 1px at 90% 80%, rgba(255, 215, 0, 0.06) 50%, transparent 50%),
            radial-gradient(1px 1px at 15% 85%, rgba(255, 215, 0, 0.04) 50%, transparent 50%),
            radial-gradient(1px 1px at 45% 45%, rgba(255, 215, 0, 0.05) 50%, transparent 50%),
            radial-gradient(1px 1px at 65% 15%, rgba(255, 215, 0, 0.06) 50%, transparent 50%),
            radial-gradient(1px 1px at 85% 55%, rgba(255, 215, 0, 0.04) 50%, transparent 50%),
            radial-gradient(1px 1px at 25% 75%, rgba(255, 215, 0, 0.05) 50%, transparent 50%),
            radial-gradient(1px 1px at 55% 90%, rgba(255, 215, 0, 0.03) 50%, transparent 50%),
            radial-gradient(1px 1px at 75% 25%, rgba(255, 215, 0, 0.05) 50%, transparent 50%),
            radial-gradient(1.5px 1.5px at 40% 35%, rgba(255, 215, 0, 0.04) 50%, transparent 50%),
            radial-gradient(1.5px 1.5px at 60% 65%, rgba(255, 215, 0, 0.03) 50%, transparent 50%);
        background-size:
            100% 100%, 100% 100%,
            200px 200px, 180px 220px, 250px 190px, 170px 230px, 220px 210px,
            190px 180px, 210px 250px, 230px 170px, 200px 200px, 180px 220px,
            250px 190px, 170px 230px, 240px 240px, 200px 200px;
        pointer-events: none;
        z-index: 0;
    }

    .esim-hero-inner {
        position: relative;
        z-index: 1;
        max-width: 600px;
    }

    .esim-hero-badge {
        display: inline-block;
        background: rgba(255, 215, 0, 0.08);
        border: 1px solid rgba(255, 215, 0, 0.15);
        color: var(--c-gold);
        font-family: 'Outfit', sans-serif;
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 3px;
        text-transform: uppercase;
        padding: 6px 20px;
        border-radius: 50px;
        margin-bottom: 14px;
    }

    .esim-hero-title {
        font-family: 'Outfit', sans-serif;
        font-size: 36px;
        font-weight: 700;
        color: var(--c-gold);
        letter-spacing: -0.5px;
        line-height: 1.1;
        margin: 0 0 10px;
        text-shadow: 0 0 40px rgba(255, 215, 0, 0.15);
    }

    .esim-hero-subtitle {
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 300;
        color: var(--c-text-muted);
        line-height: 1.6;
        margin: 0 auto 16px;
        max-width: 500px;
    }

    .esim-hero-cta {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--c-gold-gradient);
        color: #000;
        font-family: 'Outfit', sans-serif;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        padding: 10px 26px;
        border-radius: 50px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(255, 215, 0, 0.15);
        margin-bottom: 16px;
    }

    .esim-hero-cta:hover {
        box-shadow: 0 8px 32px rgba(255, 215, 0, 0.3);
        transform: translateY(-2px);
        filter: brightness(1.08);
    }

    .esim-hero-cta i {
        font-size: 12px;
        transition: transform 0.3s ease;
    }

    .esim-hero-cta:hover i {
        transform: translateX(3px);
    }

    .esim-trust-badges {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 32px;
        flex-wrap: wrap;
        padding-bottom: 6px;
    }

    .esim-trust-badge {
        display: flex;
        align-items: center;
        gap: 8px;
        font-family: 'Outfit', sans-serif;
        font-size: 12px;
        font-weight: 500;
        color: var(--c-text-muted);
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }

    .esim-trust-badge i {
        color: var(--c-gold);
        font-size: 14px;
    }

    /* ============================================================
       PROGRESS BAR
       ============================================================ */
    .esim-progress-wrap {
        max-width: 600px;
        margin: 0 auto;
        padding: 8px 28px 0;
        font-family: 'Outfit', sans-serif;
        opacity: 0;
        transform: translateY(-10px);
        transition: opacity 0.4s ease, transform 0.4s ease;
        pointer-events: none;
        height: 0;
        overflow: hidden;
    }

    .esim-progress-wrap.visible {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
        height: auto;
        padding-bottom: 8px;
    }

    .esim-progress-bar {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        position: relative;
    }

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
    }

    .esim-wizard-step.step-active {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
        position: relative;
        visibility: visible;
    }

    .esim-wizard-container {
        position: relative;
        min-height: 400px;
        margin-top: -20px;
    }

    /* ============================================================
       STEP 1 — CHOOSE DESTINATION
       ============================================================ */
    .esim-step1 {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 28px 40px;
        font-family: 'Outfit', sans-serif;
    }

    .esim-section-label {
        color: var(--c-gold);
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 3px;
        margin-bottom: 12px;
        opacity: 0.9;
    }

    /* Popular destinations grid */
    .esim-popular-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 12px;
        margin-bottom: 32px;
    }

    .esim-popular-card {
        background: var(--c-card-bg);
        border: 1px solid var(--c-border-subtle);
        border-radius: 12px;
        padding: 14px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .esim-popular-card:hover {
        border-color: rgba(255, 215, 0, 0.3);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(255, 215, 0, 0.06);
    }

    .esim-popular-flag {
        width: 28px;
        height: 20px;
        flex-shrink: 0;
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
        font-size: 13px;
        font-weight: 500;
        color: var(--c-gold);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Divider with "or" */
    .esim-or-divider {
        display: flex;
        align-items: center;
        gap: 16px;
        margin: 0 0 32px;
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
    }

    .esim-search-icon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #555;
        font-size: 14px;
        pointer-events: none;
        transition: color 0.3s ease;
    }

    .esim-search-input {
        width: 100%;
        height: 46px;
        background: var(--c-input-bg);
        border: 1px solid var(--c-input-border);
        border-radius: 50px;
        padding: 0 20px 0 48px;
        color: #eee;
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 400;
        transition: all 0.3s ease;
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
        gap: 8px;
        margin-top: 16px;
    }

    .esim-region-pill {
        display: inline-block;
        padding: 8px 20px;
        font-family: 'Outfit', sans-serif;
        font-size: 12px;
        font-weight: 500;
        letter-spacing: 0.5px;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s ease;
        user-select: none;
        background: rgba(255, 255, 255, 0.03);
        color: var(--c-text-muted);
        border: 1px solid rgba(255, 255, 255, 0.06);
    }

    .esim-region-pill:hover {
        color: var(--c-gold);
        border-color: rgba(255, 215, 0, 0.25);
        background: rgba(255, 215, 0, 0.04);
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
        grid-template-columns: repeat(5, 1fr);
        gap: 12px;
        margin-top: 24px;
    }

    .esim-country-card {
        background: var(--c-card-bg);
        border: 1px solid var(--c-border-subtle);
        border-radius: 12px;
        padding: 14px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .esim-country-card:hover {
        border-color: rgba(255, 215, 0, 0.3);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(255, 215, 0, 0.06);
    }

    .esim-country-card.active-country {
        border-color: var(--c-gold);
        background: rgba(255, 215, 0, 0.03);
    }

    .esim-country-flag {
        width: 28px;
        height: 20px;
        flex-shrink: 0;
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
        font-size: 13px;
        font-weight: 500;
        color: var(--c-gold);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Skeleton Loaders */
    .esim-skeleton-card {
        background: var(--c-card-bg);
        border: 1px solid var(--c-border-subtle);
        border-radius: 12px;
        padding: 14px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .esim-skeleton-flag {
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

    /* ============================================================
       STEP 2 — SELECT PLAN
       ============================================================ */
    .esim-step2 {
        max-width: 900px;
        margin: 0 auto;
        padding: 32px 28px 48px;
        font-family: 'Outfit', sans-serif;
    }

    /* Header bar */
    .esim-selected-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 32px;
    }

    .esim-selected-flag {
        width: 32px;
        height: 23px;
    }

    .esim-selected-flag img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 4px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
    }

    .esim-selected-name {
        font-family: 'Outfit', sans-serif;
        font-size: 22px;
        font-weight: 700;
        color: #fff;
    }

    .esim-change-btn {
        margin-left: auto;
        background: none;
        border: 1px solid rgba(255, 215, 0, 0.2);
        color: var(--c-gold);
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        padding: 8px 18px;
        border-radius: 50px;
        transition: all 0.3s ease;
    }

    .esim-change-btn:hover {
        background: rgba(255, 215, 0, 0.06);
        border-color: rgba(255, 215, 0, 0.4);
    }

    /* Bundle Type Tabs */
    .esim-bundle-tabs {
        display: flex;
        gap: 12px;
        margin-bottom: 28px;
        border-bottom: none;
        padding: 4px;
        background: rgba(255, 255, 255, 0.02);
        border-radius: 14px;
        border: 1px solid rgba(255, 215, 0, 0.06);
    }

    .esim-bundle-tab {
        flex: 1;
        padding: 12px 20px;
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        font-weight: 600;
        color: #666;
        text-align: center;
        cursor: pointer;
        border: 1.5px solid transparent;
        background: transparent;
        border-radius: 10px;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .esim-bundle-tab:hover {
        color: var(--c-gold);
        border-color: rgba(255, 215, 0, 0.15);
        background: rgba(255, 215, 0, 0.03);
        box-shadow: 0 0 12px rgba(255, 215, 0, 0.04);
    }

    .esim-bundle-tab.active {
        color: #000;
        background: var(--c-gold-gradient);
        border-color: var(--c-gold);
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.15), 0 4px 12px rgba(255, 215, 0, 0.1);
        font-weight: 700;
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
    }

    .esim-bundle-card {
        background: var(--c-card-bg);
        border: 1px solid var(--c-border-subtle);
        border-radius: 8px;
        padding: 10px 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        position: relative;
        overflow: visible;
    }

    .esim-bundle-card:hover {
        border-color: rgba(255, 215, 0, 0.25);
        box-shadow: 0 4px 16px rgba(255, 215, 0, 0.04);
        transform: translateY(-2px);
    }

    .esim-bundle-card.selected {
        border: 1.5px solid var(--c-gold);
        background: rgba(255, 215, 0, 0.03);
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.08);
    }

    .esim-bundle-card.selected::after {
        content: '\f00c';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        top: 8px;
        right: 8px;
        width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--c-gold);
        color: #000;
        font-size: 8px;
        border-radius: 50%;
    }

    .esim-bundle-data {
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 700;
        color: var(--c-gold);
        line-height: 1.2;
        margin-bottom: 1px;
    }

    .esim-bundle-validity {
        font-family: 'Outfit', sans-serif;
        font-size: 10px;
        font-weight: 400;
        color: var(--c-text-muted);
        margin-bottom: 6px;
    }

    .esim-bundle-divider {
        width: 60%;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 215, 0, 0.2), transparent);
        margin-bottom: 6px;
    }

    .esim-bundle-price {
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        font-weight: 700;
        color: #fff;
        line-height: 1.2;
        margin-bottom: 5px;
    }

    .esim-bundle-badges {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .esim-bundle-badge {
        display: inline-block;
        padding: 2px 7px;
        font-family: 'Outfit', sans-serif;
        font-size: 9px;
        font-weight: 600;
        letter-spacing: 0.5px;
        border-radius: 50px;
        background: rgba(255, 215, 0, 0.06);
        color: var(--c-gold-dim);
        border: 1px solid rgba(255, 215, 0, 0.1);
    }

    /* Most Popular badge */
    .esim-bundle-popular-tag {
        position: absolute;
        top: -11px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--c-gold-gradient);
        color: #000;
        font-family: 'Outfit', sans-serif;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        padding: 4px 14px;
        border-radius: 50px;
        white-space: nowrap;
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
        display: block;
        width: 100%;
        max-width: 400px;
        margin: 32px auto 0;
        height: 50px;
        background: var(--c-gold-gradient);
        color: #000;
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        border-radius: 50px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 16px rgba(255, 215, 0, 0.1);
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
       STEP 3 — CHECKOUT
       ============================================================ */
    .esim-step3 {
        max-width: 1100px;
        margin: 0 auto;
        padding: 32px 28px 48px;
        font-family: 'Outfit', sans-serif;
    }

    .esim-checkout-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 32px;
        align-items: start;
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

    /* Order Summary Card */
    .esim-checkout-right {
        position: sticky;
        top: 100px;
    }

    .esim-summary-card {
        background: var(--c-card-bg);
        border: 1px solid rgba(255, 215, 0, 0.1);
        border-radius: 14px;
        overflow: hidden;
        position: relative;
    }

    .esim-summary-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: var(--c-gold-gradient);
    }

    .esim-summary-inner {
        padding: 28px 24px;
    }

    .esim-summary-title {
        font-family: 'Outfit', sans-serif;
        font-size: 10px;
        font-weight: 700;
        color: var(--c-text-muted);
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 20px;
    }

    .esim-summary-empty {
        text-align: center;
        padding: 40px 0;
        color: #555;
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        font-weight: 400;
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
        gap: 6px;
        flex-wrap: wrap;
        margin-top: 8px;
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
        margin: 16px 0;
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

    /* Pay Button */
    .esim-pay-btn {
        width: 100%;
        height: 54px;
        background: var(--c-gold-gradient);
        color: #000;
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        border-radius: 50px;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 16px rgba(255, 215, 0, 0.1);
    }

    .esim-pay-btn:hover:not(:disabled) {
        box-shadow: 0 8px 25px rgba(255, 215, 0, 0.3);
        transform: translateY(-1px);
        filter: brightness(1.08);
    }

    .esim-pay-btn:disabled {
        background: #1a1a1a;
        color: #444;
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

    /* ============================================================
       WHY CHOOSE eSIM — Feature Cards
       ============================================================ */
    .esim-features-section {
        max-width: 1200px;
        margin: 0 auto;
        padding: 72px 28px 0;
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
        margin-bottom: 20px;
    }

    .esim-features-title {
        font-family: 'Outfit', sans-serif;
        font-size: 28px;
        font-weight: 700;
        color: #fff;
        margin: 0 0 12px;
    }

    .esim-features-subtitle {
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 300;
        color: var(--c-text-muted);
        margin: 0 0 40px;
    }

    .esim-features-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }

    .esim-feature-card {
        background: var(--c-card-bg);
        border: 1px solid rgba(255, 215, 0, 0.06);
        border-radius: 16px;
        padding: 32px 20px;
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
        padding: 80px 28px 0;
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
        margin-bottom: 20px;
    }

    .esim-how-title {
        font-family: 'Outfit', sans-serif;
        font-size: 28px;
        font-weight: 700;
        color: #fff;
        margin: 0 0 12px;
    }

    .esim-how-subtitle {
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 300;
        color: var(--c-text-muted);
        margin: 0 0 48px;
    }

    .esim-how-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        position: relative;
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
        background: var(--c-card-bg);
        border: 1px solid rgba(255, 215, 0, 0.06);
        border-radius: 16px;
        padding: 32px 20px;
        text-align: center;
        transition: all 0.35s ease;
        position: relative;
        z-index: 1;
    }

    .esim-how-card:hover {
        border-color: rgba(255, 215, 0, 0.2);
        transform: translateY(-6px);
        box-shadow: 0 12px 32px rgba(255, 215, 0, 0.06);
    }

    .esim-how-step-num {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        background: var(--c-gold-gradient);
        color: #000;
        font-family: 'Outfit', sans-serif;
        font-size: 18px;
        font-weight: 800;
        box-shadow: 0 4px 16px rgba(255, 215, 0, 0.15);
    }

    .esim-how-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        background: rgba(255, 215, 0, 0.05);
        border: 1px solid rgba(255, 215, 0, 0.08);
        font-size: 22px;
        color: var(--c-gold);
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
        padding: 80px 28px 80px;
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
        margin-bottom: 20px;
    }

    .esim-faq-title {
        font-family: 'Outfit', sans-serif;
        font-size: 28px;
        font-weight: 700;
        color: #fff;
        margin: 0 0 32px;
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
    @media (max-width: 1200px) {
        .esim-popular-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        .esim-features-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 1024px) {
        .esim-country-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        .esim-bundles-list {
            grid-template-columns: repeat(3, 1fr);
        }

        .esim-how-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .esim-how-grid::before {
            display: none;
        }

    }

    @media (max-width: 768px) {
        .esim-hero {
            padding: 60px 20px 0;
        }

        .esim-hero-title {
            font-size: 32px;
        }

        .esim-hero-subtitle {
            font-size: 14px;
        }

        .esim-trust-badges {
            gap: 16px;
        }

        .esim-trust-badge {
            font-size: 10px;
            letter-spacing: 1px;
        }

        .esim-popular-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .esim-country-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .esim-step1,
        .esim-step2,
        .esim-step3 {
            padding-left: 16px;
            padding-right: 16px;
        }

        .esim-checkout-grid {
            grid-template-columns: 1fr;
        }

        .esim-checkout-right {
            position: static;
            order: -1;
        }

        .esim-step1 {
            padding-top: 20px;
        }

        .esim-bundles-list {
            grid-template-columns: repeat(2, 1fr);
        }

        .esim-bundle-tabs {
            gap: 8px;
        }

        .esim-bundle-tab {
            padding: 10px 14px;
            font-size: 12px;
        }

        .esim-form-grid {
            grid-template-columns: 1fr;
        }

        .esim-features-grid {
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .esim-features-section {
            padding: 56px 16px 0;
        }

        .esim-feature-card {
            padding: 24px 16px;
        }

        .esim-how-grid {
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .esim-how-grid::before {
            display: none;
        }

        .esim-how-section {
            padding: 56px 16px 0;
        }

        .esim-how-card {
            padding: 24px 16px;
        }

        .esim-faq-section {
            padding: 56px 16px 64px;
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
            width: 32px;
            height: 32px;
            font-size: 12px;
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
            padding: 6px 14px;
            font-size: 11px;
            flex-shrink: 0;
        }
    }

    @media (max-width: 375px) {
        .esim-hero-title {
            font-size: 28px;
        }

        .esim-trust-badges {
            flex-direction: column;
            gap: 10px;
        }

        .esim-popular-grid {
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .esim-popular-card {
            padding: 12px;
            gap: 10px;
        }

        .esim-popular-flag {
            width: 24px;
            height: 17px;
        }

        .esim-popular-name {
            font-size: 12px;
        }

        .esim-country-grid {
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .esim-country-card {
            padding: 12px;
            gap: 10px;
        }

        .esim-country-flag {
            width: 24px;
            height: 17px;
        }

        .esim-country-name {
            font-size: 12px;
        }

        .esim-features-grid,
        .esim-how-grid {
            gap: 10px;
        }
    }
</style>

<!-- ============================================================
     HERO BANNER
     ============================================================ -->
<section class="esim-hero">
    <div class="esim-hero-inner">
        <div class="esim-hero-badge">TRAVEL eSIM</div>
        <h1 class="esim-hero-title">Stay Connected Worldwide</h1>
        <p class="esim-hero-subtitle">Instant data plans for 186+ countries</p>
        <button class="esim-hero-cta" onclick="var el=document.getElementById('esimPopularGrid');var y=el.getBoundingClientRect().top+window.pageYOffset-160;window.scrollTo({top:y,behavior:'smooth'})">
            Browse Plans <i class="fa-solid fa-arrow-right"></i>
        </button>
        <div class="esim-trust-badges">
            <span class="esim-trust-badge"><i class="fa-solid fa-earth-americas"></i> 186+ Countries</span>
            <span class="esim-trust-badge"><i class="fa-solid fa-bolt"></i> Instant Activation</span>
            <span class="esim-trust-badge"><i class="fa-solid fa-shield-halved"></i> Secure Payment</span>
        </div>
    </div>
</section>

<!-- ============================================================
     PROGRESS BAR
     ============================================================ -->
<div class="esim-progress-wrap" id="esimProgressWrap">
    <div class="esim-progress-bar">
        <div class="esim-progress-step active" id="esimProgressStep1">
            <div class="esim-progress-circle" id="esimProgressCircle1">1</div>
            <span class="esim-progress-label">Destination</span>
        </div>
        <div class="esim-progress-line esim-progress-line-1">
            <div class="esim-progress-line-fill" id="esimProgressFill1"></div>
        </div>
        <div class="esim-progress-step" id="esimProgressStep2">
            <div class="esim-progress-circle" id="esimProgressCircle2">2</div>
            <span class="esim-progress-label">Plan</span>
        </div>
        <div class="esim-progress-line esim-progress-line-2">
            <div class="esim-progress-line-fill" id="esimProgressFill2"></div>
        </div>
        <div class="esim-progress-step" id="esimProgressStep3">
            <div class="esim-progress-circle" id="esimProgressCircle3">3</div>
            <span class="esim-progress-label">Checkout</span>
        </div>
    </div>
</div>

<!-- ============================================================
     WIZARD CONTAINER
     ============================================================ -->
<div class="esim-wizard-container" id="esimWizardContainer">

    <!-- STEP 1: Choose Destination -->
    <section class="esim-wizard-step step-active" id="esimStep1">
        <div class="esim-step1">
            <div class="esim-section-label">POPULAR DESTINATIONS</div>
            <div class="esim-popular-grid" id="esimPopularGrid">
                <!-- Rendered by JS -->
            </div>

            <div class="esim-or-divider">
                <div class="esim-or-divider-line"></div>
                <span class="esim-or-divider-text">or</span>
                <div class="esim-or-divider-line"></div>
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
                <span class="esim-selected-flag" id="esimSelectedFlag"></span>
                <span class="esim-selected-name" id="esimSelectedName"></span>
                <button class="esim-change-btn" id="esimChangeBtn"><i class="fa-solid fa-arrow-left" style="margin-right:6px;font-size:11px;"></i>Change</button>
            </div>

            <div class="esim-bundle-tabs">
                <button class="esim-bundle-tab active" id="esimTabData">Data Plans</button>
                <button class="esim-bundle-tab" id="esimTabUnlimited">Unlimited Plans</button>
            </div>

            <div class="esim-bundle-loading" id="esimBundleLoading" style="display:none;">
                <div class="spinner-ring"></div>
                <span>Loading available plans...</span>
            </div>

            <div class="esim-bundles-list" id="esimBundlesList"></div>

            <button class="esim-show-all-btn" id="esimShowAllBtn">Show all plans</button>

            <button class="esim-continue-btn" id="esimContinueBtn" disabled>Continue to Checkout</button>
        </div>
    </section>

    <!-- STEP 3: Checkout -->
    <section class="esim-wizard-step" id="esimStep3">
        <div class="esim-step3">
            <button class="esim-back-btn" id="esimBackToPlans"><i class="fa-solid fa-arrow-left"></i> Back to Plans</button>

            <div class="esim-checkout-grid">
                <!-- LEFT: Form -->
                <div class="esim-checkout-left">
                    <div class="esim-form-area">
                        <div class="esim-section-label">YOUR DETAILS</div>
                        <div class="esim-form-grid">
                            <div class="esim-form-field">
                                <label class="esim-field-label">Full Name *</label>
                                <input type="text" class="esim-field-input" id="esimName" placeholder="John Doe" autocomplete="name">
                                <span class="esim-field-error" id="esimNameError">Full name is required</span>
                            </div>
                            <div class="esim-form-field">
                                <label class="esim-field-label">Phone Number *</label>
                                <input type="tel" class="esim-field-input" id="esimPhone" placeholder="+971 50 000 0000" autocomplete="tel">
                                <span class="esim-field-error" id="esimPhoneError">Phone number is required</span>
                            </div>
                            <div class="esim-form-field">
                                <label class="esim-field-label">Email Address *</label>
                                <input type="email" class="esim-field-input" id="esimEmail" placeholder="you@email.com" autocomplete="email">
                                <span class="esim-field-error" id="esimEmailError">Valid email is required</span>
                            </div>
                            <div class="esim-form-field">
                                <label class="esim-field-label">Confirm Email *</label>
                                <input type="email" class="esim-field-input" id="esimEmailConfirm" placeholder="Confirm your email" autocomplete="off">
                                <span class="esim-field-error" id="esimEmailConfirmError">Emails do not match</span>
                            </div>
                        </div>
                    </div>

                    <button class="esim-pay-btn" id="esimPayBtn" disabled>
                        <div class="btn-spinner"></div>
                        <span class="btn-label">PAY SECURELY</span>
                        <span class="btn-loading-text">Processing...</span>
                    </button>
                    <div class="esim-secure-badge">
                        <i class="fa-solid fa-lock"></i>
                        256-bit SSL Encrypted
                    </div>

                    <div class="esim-error-msg" id="esimErrorMsg"></div>
                </div>

                <!-- RIGHT: Order Summary -->
                <div class="esim-checkout-right">
                    <div class="esim-summary-card">
                        <div class="esim-summary-inner">
                            <div class="esim-summary-title">ORDER SUMMARY</div>

                            <div class="esim-summary-empty" id="esimSummaryEmpty">Select a plan to continue</div>

                            <div class="esim-summary-content" id="esimSummaryContent">
                                <div class="esim-summary-row">
                                    <div class="esim-summary-left">
                                        <div class="esim-summary-country-row">
                                            <span class="esim-summary-country-flag" id="esimSummaryFlag"></span>
                                            <span class="esim-summary-country-name" id="esimSummaryCountry"></span>
                                        </div>
                                        <div class="esim-summary-bundle-name" id="esimSummaryBundleName"></div>
                                        <div class="esim-summary-bundle-details" id="esimSummaryBundleDetails"></div>
                                        <div class="esim-summary-badges" id="esimSummaryBadges"></div>
                                    </div>
                                    <div class="esim-summary-right-price" id="esimSummaryTotal"></div>
                                </div>
                                <button class="esim-summary-change-plan" id="esimSummaryChangePlan">Change Plan</button>
                                <div class="esim-summary-divider"></div>
                                <div class="esim-summary-price-row">
                                    <span class="esim-summary-price-label">Subtotal</span>
                                    <span class="esim-summary-price-value" id="esimSummarySubtotal"></span>
                                </div>
                                <div class="esim-summary-divider"></div>
                                <div class="esim-summary-total-row">
                                    <span class="esim-summary-total-label">Total</span>
                                    <span class="esim-summary-total-value" id="esimSummaryTotalBottom"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

<!-- ============================================================
     HOW IT WORKS
     ============================================================ -->
<!-- ============================================================
     WHY CHOOSE eSIM
     ============================================================ -->
<section class="esim-features-section">
    <div class="esim-features-badge">WHY CHOOSE eSIM</div>
    <h2 class="esim-features-title">Travel Smarter, Stay Connected</h2>
    <p class="esim-features-subtitle">Skip the hassle of physical SIM cards. Our eSIM works with 186+ countries worldwide.</p>
    <div class="esim-features-grid">
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
     HOW TO ACTIVATE eSIM
     ============================================================ -->
<section class="esim-how-section">
    <div class="esim-how-badge">HOW TO ACTIVATE</div>
    <h2 class="esim-how-title">Get Online in 4 Easy Steps</h2>
    <p class="esim-how-subtitle">From purchase to browsing — it only takes a few minutes.</p>
    <div class="esim-how-grid">
        <div class="esim-how-card">
            <div class="esim-how-step-num">1</div>
            <div class="esim-how-icon"><i class="fa-solid fa-cart-shopping"></i></div>
            <div class="esim-how-card-title">Purchase eSIM</div>
            <p class="esim-how-card-desc">Choose your destination, pick a data plan, and complete the secure checkout.</p>
        </div>
        <div class="esim-how-card">
            <div class="esim-how-step-num">2</div>
            <div class="esim-how-icon"><i class="fa-solid fa-qrcode"></i></div>
            <div class="esim-how-card-title">Scan QR Code</div>
            <p class="esim-how-card-desc">Receive your unique QR code via email instantly. Scan it with your phone camera.</p>
        </div>
        <div class="esim-how-card">
            <div class="esim-how-step-num">3</div>
            <div class="esim-how-icon"><i class="fa-solid fa-gear"></i></div>
            <div class="esim-how-card-title">Activate in Settings</div>
            <p class="esim-how-card-desc">Go to Settings → Cellular → Add eSIM. Follow the on-screen prompts to activate.</p>
        </div>
        <div class="esim-how-card">
            <div class="esim-how-step-num">4</div>
            <div class="esim-how-icon"><i class="fa-solid fa-wifi"></i></div>
            <div class="esim-how-card-title">Start Using Internet</div>
            <p class="esim-how-card-desc">You're all set! Enjoy fast, reliable internet as soon as you arrive at your destination.</p>
        </div>
    </div>
</section>

<!-- ============================================================
     FAQ
     ============================================================ -->
<section class="esim-faq-section">
    <div class="esim-faq-badge">FAQ</div>
    <h2 class="esim-faq-title">Common Questions</h2>
    <div class="esim-faq-list">
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
    let selectedCountry = null; // { name, iso2, iso3 }
    let selectedBundle = null;
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
            var isActive = selectedCountry && selectedCountry.iso3 === iso3 ? ' active-country' : '';
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
        selectedCountry = { iso3: iso3, iso2: iso2, name: name };
        selectedBundle = null;
        bundlesData = [];
        showAllBundles = false;
        currentBundleTab = 'data';

        // Update Step 2 header
        document.getElementById('esimSelectedFlag').innerHTML = getFlagImg(iso2, 80);
        document.getElementById('esimSelectedName').textContent = name;

        // Reset tabs to Data Plans active
        document.getElementById('esimTabData').classList.add('active');
        document.getElementById('esimTabUnlimited').classList.remove('active');

        // Reset continue button
        document.getElementById('esimContinueBtn').disabled = true;

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
        if (!selectedBundle) return;
        updateSummary();
        goToStep(3);
    });

    // ── Event: Bundle Tabs ───────────────────────────────
    document.getElementById('esimTabData').addEventListener('click', function() {
        if (currentBundleTab === 'data') return;
        currentBundleTab = 'data';
        showAllBundles = false;
        this.classList.add('active');
        document.getElementById('esimTabUnlimited').classList.remove('active');
        renderBundles();
    });

    document.getElementById('esimTabUnlimited').addEventListener('click', function() {
        if (currentBundleTab === 'unlimited') return;
        currentBundleTab = 'unlimited';
        showAllBundles = false;
        document.getElementById('esimTabData').classList.remove('active');
        this.classList.add('active');
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

            if (data.success && data.bundles && data.bundles.length > 0) {
                bundlesData = data.bundles;
                renderBundles();
            } else {
                list.innerHTML = '<div class="esim-no-results" style="grid-column:1/-1;"><i class="fa-solid fa-sim-card"></i>No plans available for this country right now</div>';
            }
        })
        .catch(function() {
            loading.style.display = 'none';
            list.innerHTML = '<div class="esim-no-results" style="grid-column:1/-1;"><i class="fa-solid fa-triangle-exclamation"></i>Failed to load plans. Please try again.</div>';
        });
    }

    // ── Categorize bundles ───────────────────────────────
    function getDataBundles() {
        return bundlesData.filter(function(b) {
            return !b.unlimited;
        });
    }

    function getUnlimitedBundles() {
        return bundlesData.filter(function(b) {
            return !!b.unlimited;
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
        var activeBundles = currentBundleTab === 'data' ? getDataBundles() : getUnlimitedBundles();

        if (activeBundles.length === 0) {
            list.innerHTML = '<div class="esim-no-results" style="grid-column:1/-1;"><i class="fa-solid fa-sim-card"></i>No ' + (currentBundleTab === 'data' ? 'data' : 'unlimited') + ' plans available for this country</div>';
            showAllBtn.classList.remove('visible');
            return;
        }

        var mostPopularIdx = currentBundleTab === 'data' ? findMostPopularIndex(activeBundles) : -1;
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
            var isSelected = selectedBundle && selectedBundle.bundle_code === code;
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
        selectedBundle = bundlesData[index];

        // Update selected class
        document.querySelectorAll('.esim-bundle-card').forEach(function(c) { c.classList.remove('selected'); });
        var selectedCard = document.querySelector('.esim-bundle-card[data-index="' + index + '"]');
        if (selectedCard) selectedCard.classList.add('selected');

        // Enable continue button
        document.getElementById('esimContinueBtn').disabled = false;
    }

    // ── Summary: Reset ───────────────────────────────────
    function resetSummary() {
        selectedBundle = null;
        document.getElementById('esimSummaryEmpty').style.display = 'block';
        document.getElementById('esimSummaryContent').classList.remove('visible');
        document.getElementById('esimPayBtn').disabled = true;
        updatePayBtnLabel(null);
        document.getElementById('esimErrorMsg').classList.remove('visible');
    }

    // ── Summary: Update ──────────────────────────────────
    function updateSummary() {
        if (!selectedBundle || !selectedCountry) return;

        document.getElementById('esimSummaryEmpty').style.display = 'none';
        document.getElementById('esimSummaryContent').classList.add('visible');

        document.getElementById('esimSummaryFlag').innerHTML = getFlagImg(selectedCountry.iso2, 40);
        document.getElementById('esimSummaryCountry').textContent = selectedCountry.name;

        var name = selectedBundle.bundle_marketing_name || selectedBundle.bundle_name || 'eSIM Plan';
        var dataLabel = bundleDataLabel(selectedBundle);
        var validity = selectedBundle.validity || 0;
        var price = selectedBundle.selling_price || 0;
        var hasCalls = selectedBundle.supports_calls_sms || selectedBundle.voice_included || false;
        var hasTopup = selectedBundle.support_topup || selectedBundle.topup_supported || false;

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
        if (price !== null && price !== undefined) {
            label.textContent = 'PAY SECURELY \u2014 ' + fmtPrice(price);
        } else {
            label.textContent = 'PAY SECURELY';
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
        if (!selectedBundle || !selectedCountry) return;
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
            phone: document.getElementById('esimPhone').value.trim() || null,
            bundle_code: selectedBundle.bundle_code,
            country_code: selectedCountry.iso3,
            country_name: selectedCountry.name
        };

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

})();
</script>

@include('footer')
