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

    body { margin: 0; background-color: var(--c-dark-bg); }

    /* ============================================================
       SECTION 1 — HERO BANNER
       ============================================================ */
    .esim-hero {
        position: relative;
        width: 100%;
        min-height: 280px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 200px 28px 40px;
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
        margin-bottom: 24px;
    }

    .esim-hero-title {
        font-family: 'Outfit', sans-serif;
        font-size: 38px;
        font-weight: 700;
        color: var(--c-gold);
        letter-spacing: -0.5px;
        line-height: 1.1;
        margin: 0 0 16px;
        text-shadow: 0 0 40px rgba(255, 215, 0, 0.15);
    }

    .esim-hero-subtitle {
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 300;
        color: var(--c-text-muted);
        line-height: 1.6;
        margin: 0 auto 28px;
        max-width: 500px;
    }

    .esim-hero-divider {
        width: 120px;
        height: 1px;
        margin: 0 auto;
        background: linear-gradient(90deg, transparent, var(--c-gold), transparent);
        opacity: 0.4;
    }

    /* ============================================================
       SECTION 2 — COUNTRY SELECTION
       ============================================================ */
    .esim-countries-section {
        max-width: 1440px;
        margin: 0 auto;
        padding: 24px 28px 0;
        font-family: 'Outfit', sans-serif;
    }

    .esim-section-label {
        color: var(--c-gold);
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 3px;
        margin-bottom: 24px;
        opacity: 0.9;
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
        padding: 16px;
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
        font-size: 28px;
        line-height: 1;
        flex-shrink: 0;
    }

    .esim-country-name {
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 500;
        color: #fff;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Skeleton Loaders */
    .esim-skeleton-card {
        background: var(--c-card-bg);
        border: 1px solid var(--c-border-subtle);
        border-radius: 12px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .esim-skeleton-flag {
        width: 28px;
        height: 28px;
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
       SECTION 3 — BUNDLE SELECTION + CHECKOUT
       ============================================================ */
    .esim-checkout-section {
        max-width: 1440px;
        margin: 0 auto;
        padding: 56px 28px 0;
        font-family: 'Outfit', sans-serif;
        display: none;
    }

    .esim-checkout-section.visible {
        display: block;
    }

    .esim-checkout-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 32px;
        align-items: start;
    }

    /* Selected Country Header */
    .esim-selected-header {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .esim-selected-flag {
        font-size: 32px;
        line-height: 1;
    }

    .esim-selected-name {
        font-family: 'Outfit', sans-serif;
        font-size: 24px;
        font-weight: 700;
        color: #fff;
    }

    .esim-change-btn {
        margin-left: auto;
        background: none;
        border: none;
        color: var(--c-gold);
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        padding: 6px 12px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .esim-change-btn:hover {
        background: rgba(255, 215, 0, 0.06);
    }

    /* Bundle Cards */
    .esim-bundles-area {
        margin-top: 24px;
    }

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

    .esim-bundles-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .esim-bundle-card {
        background: var(--c-card-bg);
        border: 1px solid var(--c-border-subtle);
        border-radius: 14px;
        padding: 20px 24px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
    }

    .esim-bundle-card:hover {
        border-color: rgba(255, 215, 0, 0.25);
        box-shadow: 0 4px 16px rgba(255, 215, 0, 0.04);
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
        top: 10px;
        right: 14px;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--c-gold);
        color: #000;
        font-size: 10px;
        border-radius: 50%;
    }

    .esim-bundle-left {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .esim-bundle-data {
        font-family: 'Outfit', sans-serif;
        font-size: 22px;
        font-weight: 700;
        color: var(--c-gold);
        line-height: 1.2;
    }

    .esim-bundle-validity {
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        font-weight: 400;
        color: var(--c-text-muted);
    }

    .esim-bundle-badges {
        display: flex;
        gap: 8px;
        margin-top: 6px;
        flex-wrap: wrap;
    }

    .esim-bundle-badge {
        display: inline-block;
        padding: 3px 10px;
        font-family: 'Outfit', sans-serif;
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.5px;
        border-radius: 50px;
        background: rgba(255, 215, 0, 0.06);
        color: var(--c-gold-dim);
        border: 1px solid rgba(255, 215, 0, 0.1);
    }

    .esim-bundle-right {
        text-align: right;
        flex-shrink: 0;
        padding-left: 16px;
    }

    .esim-bundle-price {
        font-family: 'Outfit', sans-serif;
        font-size: 20px;
        font-weight: 700;
        color: #fff;
        line-height: 1.2;
    }

    .esim-bundle-currency {
        font-family: 'Outfit', sans-serif;
        font-size: 11px;
        font-weight: 400;
        color: #666;
    }

    /* Customer Form */
    .esim-form-area {
        margin-top: 32px;
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

    /* ============================================================
       ORDER SUMMARY (Right Column)
       ============================================================ */
    .esim-summary-sticky {
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

    .esim-summary-country {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .esim-summary-country-flag {
        font-size: 20px;
        line-height: 1;
    }

    .esim-summary-country-name {
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 500;
        color: #fff;
    }

    .esim-summary-divider {
        height: 1px;
        background: rgba(255, 255, 255, 0.06);
        margin: 16px 0;
    }

    .esim-summary-bundle-name {
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 600;
        color: #fff;
        margin-bottom: 4px;
    }

    .esim-summary-bundle-details {
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        color: var(--c-text-muted);
    }

    .esim-summary-badges {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-top: 8px;
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

    /* Pay Button */
    .esim-pay-btn {
        width: 100%;
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
        margin-top: 20px;
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
       SECTION 4 — HOW IT WORKS
       ============================================================ */
    .esim-how-section {
        max-width: 1440px;
        margin: 0 auto;
        padding: 96px 28px 0;
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
        margin: 0 0 40px;
    }

    .esim-how-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }

    .esim-how-card {
        background: var(--c-card-bg);
        border: 1px solid rgba(255, 215, 0, 0.06);
        border-radius: 14px;
        padding: 32px 24px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .esim-how-card:hover {
        border-color: rgba(255, 215, 0, 0.15);
        transform: translateY(-4px);
    }

    .esim-how-step-num {
        font-family: 'Outfit', sans-serif;
        font-size: 40px;
        font-weight: 800;
        background: var(--c-gold-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1.2;
    }

    .esim-how-icon {
        font-size: 24px;
        color: var(--c-gold);
        margin-top: 8px;
    }

    .esim-how-card-title {
        font-family: 'Outfit', sans-serif;
        font-size: 16px;
        font-weight: 600;
        color: #fff;
        margin-top: 16px;
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
       SECTION 5 — FAQ
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
    @media (max-width: 1024px) {
        .esim-country-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        .esim-checkout-grid {
            grid-template-columns: 1fr;
        }

        .esim-summary-sticky {
            position: relative;
            top: 0;
        }

        .esim-how-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .esim-hero {
            padding: 170px 20px 36px;
            min-height: auto;
        }

        .esim-hero-title {
            font-size: 30px;
        }

        .esim-hero-subtitle {
            font-size: 14px;
        }

        .esim-countries-section,
        .esim-checkout-section {
            padding-left: 16px;
            padding-right: 16px;
        }

        .esim-country-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .esim-how-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .esim-how-section {
            padding: 64px 16px 0;
        }

        .esim-faq-section {
            padding: 56px 16px 64px;
        }

        .esim-form-grid {
            grid-template-columns: 1fr;
        }

        .esim-bundle-card {
            padding: 16px 18px;
        }

        .esim-bundle-data {
            font-size: 18px;
        }

        .esim-bundle-price {
            font-size: 17px;
        }

        .esim-region-pills {
            gap: 6px;
        }

        .esim-region-pill {
            padding: 6px 14px;
            font-size: 11px;
        }
    }

    @media (max-width: 600px) {
        .esim-country-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 375px) {
        .esim-hero-title {
            font-size: 26px;
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
            font-size: 24px;
        }

        .esim-country-name {
            font-size: 12px;
        }
    }
</style>

<!-- ============================================================
     SECTION 1 — HERO BANNER
     ============================================================ -->
<section class="esim-hero">
    <div class="esim-hero-inner">
        <div class="esim-hero-badge">TRAVEL DATA PLANS</div>
        <h1 class="esim-hero-title">Stay Connected Worldwide</h1>
        <p class="esim-hero-subtitle">Instant eSIM activation for 186+ countries. No physical SIM needed.</p>
        <div class="esim-hero-divider"></div>
    </div>
</section>

<!-- ============================================================
     SECTION 2 — COUNTRY SELECTION
     ============================================================ -->
<section class="esim-countries-section" id="esimCountriesSection">
    <div class="esim-section-label">SELECT YOUR DESTINATION</div>

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
        <!-- Skeleton loaders — rendered by JS on load -->
    </div>
</section>

<!-- ============================================================
     SECTION 3 — BUNDLE SELECTION + CHECKOUT
     ============================================================ -->
<section class="esim-checkout-section" id="esimCheckoutSection">
    <div class="esim-checkout-grid">
        <!-- Left Column -->
        <div class="esim-checkout-left">
            <div class="esim-selected-header">
                <span class="esim-selected-flag" id="esimSelectedFlag"></span>
                <span class="esim-selected-name" id="esimSelectedName"></span>
                <button class="esim-change-btn" id="esimChangeBtn"><i class="fa-solid fa-arrow-left" style="margin-right:6px;font-size:11px;"></i>Change</button>
            </div>

            <div class="esim-bundles-area" id="esimBundlesArea">
                <div class="esim-bundle-loading" id="esimBundleLoading">
                    <div class="spinner-ring"></div>
                    <span>Loading available plans...</span>
                </div>
                <div class="esim-bundles-list" id="esimBundlesList"></div>
            </div>

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

            <div class="esim-error-msg" id="esimErrorMsg"></div>
        </div>

        <!-- Right Column — Order Summary -->
        <div class="esim-checkout-right">
            <div class="esim-summary-sticky">
                <div class="esim-summary-card">
                    <div class="esim-summary-inner">
                        <div class="esim-summary-title">ORDER SUMMARY</div>

                        <div class="esim-summary-empty" id="esimSummaryEmpty">Select a plan to continue</div>

                        <div class="esim-summary-content" id="esimSummaryContent">
                            <div class="esim-summary-country">
                                <span class="esim-summary-country-flag" id="esimSummaryFlag"></span>
                                <span class="esim-summary-country-name" id="esimSummaryCountry"></span>
                            </div>
                            <div class="esim-summary-divider"></div>
                            <div class="esim-summary-bundle-name" id="esimSummaryBundleName"></div>
                            <div class="esim-summary-bundle-details" id="esimSummaryBundleDetails"></div>
                            <div class="esim-summary-badges" id="esimSummaryBadges"></div>
                            <div class="esim-summary-divider"></div>
                            <div class="esim-summary-price-row">
                                <span class="esim-summary-price-label">Subtotal</span>
                                <span class="esim-summary-price-value" id="esimSummarySubtotal"></span>
                            </div>
                            <div class="esim-summary-divider"></div>
                            <div class="esim-summary-total-row">
                                <span class="esim-summary-total-label">Total</span>
                                <span class="esim-summary-total-value" id="esimSummaryTotal"></span>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     SECTION 4 — HOW IT WORKS
     ============================================================ -->
<section class="esim-how-section">
    <div class="esim-how-badge">HOW IT WORKS</div>
    <h2 class="esim-how-title">Three Simple Steps</h2>
    <div class="esim-how-grid">
        <div class="esim-how-card">
            <div class="esim-how-step-num">01</div>
            <div class="esim-how-icon"><i class="fa-solid fa-globe-americas"></i></div>
            <div class="esim-how-card-title">Choose Your Plan</div>
            <p class="esim-how-card-desc">Select your destination country and pick the perfect data plan for your trip.</p>
        </div>
        <div class="esim-how-card">
            <div class="esim-how-step-num">02</div>
            <div class="esim-how-icon"><i class="fa-solid fa-qrcode"></i></div>
            <div class="esim-how-card-title">Pay & Receive QR Code</div>
            <p class="esim-how-card-desc">Complete secure payment. Your eSIM QR code arrives instantly via email.</p>
        </div>
        <div class="esim-how-card">
            <div class="esim-how-step-num">03</div>
            <div class="esim-how-icon"><i class="fa-solid fa-wifi"></i></div>
            <div class="esim-how-card-title">Scan & Connect</div>
            <p class="esim-how-card-desc">Scan the QR code with your phone. Activate on arrival — instant connectivity.</p>
        </div>
    </div>
</section>

<!-- ============================================================
     SECTION 5 — FAQ
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

    // ── Region mapping (ISO3 codes) ──────────────────────
    const REGION_MAP = {
        middle_east: ['ARE','SAU','QAT','BHR','KWT','OMN','JOR','LBN','EGY','IRQ','PSE','YEM','SYR','IRN','ISR','TUR'],
        asia: ['IND','PAK','BGD','LKA','NPL','BTN','MDV','AFG','CHN','JPN','KOR','PRK','MNG','TWN','HKG','MAC','THA','VNM','KHM','LAO','MMR','MYS','SGP','IDN','PHL','BRN','TLS','KAZ','UZB','TKM','TJK','KGZ','GEO','ARM','AZE'],
        europe: ['GBR','FRA','DEU','ITA','ESP','PRT','NLD','BEL','LUX','AUT','CHE','IRL','ISL','NOR','SWE','FIN','DNK','POL','CZE','SVK','HUN','ROU','BGR','HRV','SVN','SRB','BIH','MNE','MKD','ALB','GRC','CYP','MLT','EST','LVA','LTU','UKR','MDA','BLR','RUS'],
        africa: ['ZAF','NGA','KEN','GHA','TZA','UGA','ETH','RWA','SEN','CIV','CMR','AGO','MOZ','ZMB','ZWE','BWA','NAM','MUS','MDG','TUN','MAR','DZA','LBY','SDN','SSD','COD','COG','GAB','BEN','TGO','BFA','MLI','NER','TCD','GIN','SLE','LBR','MWI','SOM','DJI','ERI','CPV','SYC','COM','STP','GNQ','GMB','GNB','SWZ','LSO'],
        north_america: ['USA','CAN','MEX','GTM','BLZ','SLV','HND','NIC','CRI','PAN','CUB','JAM','HTI','DOM','TTO','BHS','BRB','ATG','DMA','GRD','KNA','LCA','VCT','PRI'],
        south_america: ['BRA','ARG','CHL','COL','PER','VEN','ECU','BOL','PRY','URY','GUY','SUR','GUF']
    };

    // ── Utility: ISO2 → Flag emoji ───────────────────────
    function getFlag(iso2) {
        if (!iso2 || iso2.length !== 2) return '';
        return String.fromCodePoint(
            ...[...iso2.toUpperCase()].map(c => 0x1F1E6 - 65 + c.charCodeAt(0))
        );
    }

    // ── Utility: Format price to AED ─────────────────────
    function fmtPrice(val) {
        const n = parseFloat(val);
        if (isNaN(n)) return 'AED 0.00';
        return 'AED ' + n.toFixed(2);
    }

    // ── Utility: Get data label for bundle ───────────────
    function bundleDataLabel(b) {
        if (b.unlimited) return 'Unlimited';
        const amount = b.gprs_limit || b.data_amount || 0;
        const unit = b.data_unit || 'GB';
        return amount + ' ' + unit;
    }

    // ── Render: Skeleton Loaders ─────────────────────────
    function renderSkeletons() {
        const grid = document.getElementById('esimCountryGrid');
        let html = '';
        for (let i = 0; i < 20; i++) {
            html += '<div class="esim-skeleton-card"><div class="esim-skeleton-flag"></div><div class="esim-skeleton-text"></div></div>';
        }
        grid.innerHTML = html;
    }

    // ── Render: Country Grid ─────────────────────────────
    function renderCountries() {
        const grid = document.getElementById('esimCountryGrid');
        const filtered = filterCountries();

        if (filtered.length === 0) {
            grid.innerHTML = '<div class="esim-no-results"><i class="fa-solid fa-earth-americas"></i>No countries found matching your search</div>';
            return;
        }

        let html = '';
        filtered.forEach(function(c) {
            const flag = getFlag(c.iso2_code || c.iso2 || '');
            const name = c.country_name || c.name || '';
            const iso3 = c.iso3_code || c.iso3 || '';
            const iso2 = c.iso2_code || c.iso2 || '';
            const isActive = selectedCountry && selectedCountry.iso3 === iso3 ? ' active-country' : '';
            html += '<div class="esim-country-card' + isActive + '" data-iso3="' + iso3 + '" data-iso2="' + iso2 + '" data-name="' + name.replace(/"/g, '&quot;') + '">';
            html += '<span class="esim-country-flag">' + flag + '</span>';
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
        let list = allCountries;

        // Region filter
        if (currentRegion !== 'all' && REGION_MAP[currentRegion]) {
            const codes = REGION_MAP[currentRegion];
            list = list.filter(function(c) {
                const iso3 = c.iso3_code || c.iso3 || '';
                return codes.indexOf(iso3) !== -1;
            });
        }

        // Search filter
        if (currentSearch.trim() !== '') {
            const q = currentSearch.trim().toLowerCase();
            list = list.filter(function(c) {
                const name = (c.country_name || c.name || '').toLowerCase();
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
        const pill = e.target.closest('.esim-region-pill');
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

        // Update UI
        renderCountries(); // re-render to show active state
        document.getElementById('esimSelectedFlag').textContent = getFlag(iso2);
        document.getElementById('esimSelectedName').textContent = name;

        // Show checkout section
        const section = document.getElementById('esimCheckoutSection');
        section.classList.add('visible');

        // Scroll into view
        setTimeout(function() {
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);

        // Reset summary
        resetSummary();

        // Load bundles
        loadBundles(iso3);
    }

    // ── Event: Change Button ─────────────────────────────
    document.getElementById('esimChangeBtn').addEventListener('click', function() {
        document.getElementById('esimCountriesSection').scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    // ── Load: Bundles ────────────────────────────────────
    function loadBundles(countryCode) {
        const loading = document.getElementById('esimBundleLoading');
        const list = document.getElementById('esimBundlesList');

        loading.style.display = 'flex';
        list.innerHTML = '';

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
                list.innerHTML = '<div class="esim-no-results"><i class="fa-solid fa-sim-card"></i>No plans available for this country right now</div>';
            }
        })
        .catch(function() {
            loading.style.display = 'none';
            list.innerHTML = '<div class="esim-no-results"><i class="fa-solid fa-triangle-exclamation"></i>Failed to load plans. Please try again.</div>';
        });
    }

    // ── Render: Bundle Cards ─────────────────────────────
    function renderBundles() {
        const list = document.getElementById('esimBundlesList');
        let html = '';

        bundlesData.forEach(function(b, idx) {
            const dataLabel = bundleDataLabel(b);
            const validity = b.validity || 0;
            const price = b.selling_price || 0;
            const name = b.bundle_marketing_name || b.bundle_name || 'eSIM Plan';
            const code = b.bundle_code || '';
            const hasCalls = b.supports_calls_sms || b.voice_included || false;
            const hasTopup = b.support_topup || b.topup_supported || false;

            html += '<div class="esim-bundle-card" data-index="' + idx + '" data-code="' + code + '">';
            html += '<div class="esim-bundle-left">';
            html += '<div class="esim-bundle-data">' + dataLabel + '</div>';
            html += '<div class="esim-bundle-validity">' + validity + ' Days</div>';
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
            html += '<div class="esim-bundle-right">';
            html += '<div class="esim-bundle-price">' + fmtPrice(price) + '</div>';
            html += '<div class="esim-bundle-currency">AED</div>';
            html += '</div>';
            html += '</div>';
        });

        list.innerHTML = html;

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
        document.querySelector('.esim-bundle-card[data-index="' + index + '"]').classList.add('selected');

        // Update summary
        updateSummary();
    }

    // ── Summary: Reset ───────────────────────────────────
    function resetSummary() {
        selectedBundle = null;
        document.getElementById('esimSummaryEmpty').style.display = 'block';
        document.getElementById('esimSummaryContent').classList.remove('visible');
        document.getElementById('esimPayBtn').disabled = true;
        document.getElementById('esimErrorMsg').classList.remove('visible');
    }

    // ── Summary: Update ──────────────────────────────────
    function updateSummary() {
        if (!selectedBundle || !selectedCountry) return;

        document.getElementById('esimSummaryEmpty').style.display = 'none';
        document.getElementById('esimSummaryContent').classList.add('visible');

        const flag = getFlag(selectedCountry.iso2);
        document.getElementById('esimSummaryFlag').textContent = flag;
        document.getElementById('esimSummaryCountry').textContent = selectedCountry.name;

        const name = selectedBundle.bundle_marketing_name || selectedBundle.bundle_name || 'eSIM Plan';
        const dataLabel = bundleDataLabel(selectedBundle);
        const validity = selectedBundle.validity || 0;
        const price = selectedBundle.selling_price || 0;
        const hasCalls = selectedBundle.supports_calls_sms || selectedBundle.voice_included || false;
        const hasTopup = selectedBundle.support_topup || selectedBundle.topup_supported || false;

        document.getElementById('esimSummaryBundleName').textContent = name;
        document.getElementById('esimSummaryBundleDetails').textContent = dataLabel + ' \u2022 ' + validity + ' Days';

        // Badges
        let badgeHtml = '';
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

        // Enable pay button (will be further validated on click)
        document.getElementById('esimPayBtn').disabled = false;
    }

    // ── Validation ───────────────────────────────────────
    function validateForm() {
        let valid = true;

        // Name
        const name = document.getElementById('esimName').value.trim();
        if (!name) {
            showFieldError('esimName', 'esimNameError');
            valid = false;
        } else {
            clearFieldError('esimName', 'esimNameError');
        }

        // Phone
        const phone = document.getElementById('esimPhone').value.trim();
        if (!phone) {
            showFieldError('esimPhone', 'esimPhoneError');
            valid = false;
        } else {
            clearFieldError('esimPhone', 'esimPhoneError');
        }

        // Email
        const email = document.getElementById('esimEmail').value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email || !emailRegex.test(email)) {
            showFieldError('esimEmail', 'esimEmailError');
            valid = false;
        } else {
            clearFieldError('esimEmail', 'esimEmailError');
        }

        // Confirm Email
        const emailConfirm = document.getElementById('esimEmailConfirm').value.trim();
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

        const btn = this;
        const errorMsg = document.getElementById('esimErrorMsg');
        errorMsg.classList.remove('visible');

        // Loading state
        btn.disabled = true;
        btn.classList.add('loading');

        const payload = {
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
            const item = this.closest('.esim-faq-item');
            const answer = item.querySelector('.esim-faq-answer');
            const inner = item.querySelector('.esim-faq-answer-inner');
            const isOpen = item.classList.contains('active');

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

    // ── Init: Load Countries ─────────────────────────────
    function init() {
        renderSkeletons();

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
