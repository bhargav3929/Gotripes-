@include('header')

<script>
    window.visaTypesData = @json($visaTypes);
</script>

<!-- intl-tel-input (phone with country dropdown) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.5.3/build/css/intlTelInput.css">
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.5.3/build/js/intlTelInput.min.js"></script>

<!-- Tom Select (searchable select dropdown) -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

    * { box-sizing: border-box; }

    :root {
        --c-gold: #FFD700;
        --c-gold-dim: #b89c12;
        --c-gold-gradient: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%);
        --c-dark-bg: #050505;
        --c-card-bg: #0d0d0d;
        --c-input-bg: #111;
        --c-input-border: #222;
        --c-text-muted: #888;
        --c-text-light: #eee;
        --c-section-bg: #0a0a0a;

        --gt-surface: #0a0a0a;
        --gt-text: #fff;
        --gt-text-secondary: #ccc;
        --gt-text-muted: #888;
        --gt-border-strong: rgba(255, 215, 0, 0.22);
        --gt-border-soft: rgba(255, 215, 0, 0.1);
        --gt-gold: #FFD700;
        --gt-gold-2: #FFA500;
        --gt-gold-soft: rgba(255, 215, 0, 0.05);
    }

    html[data-theme="light"] {
        --c-dark-bg: #f4f5f7;
        --c-card-bg: #ffffff;
        --c-section-bg: #f9f9fb;
        --c-input-bg: #ffffff;
        --c-input-border: #e0e0e0;
        --c-text-muted: #666;
        --c-text-light: #222;
        --gt-surface: #ffffff;
        --gt-text: #1a1a1a;
        --gt-text-secondary: #444;
        --gt-text-muted: #666;
        --gt-border-strong: rgba(212, 175, 55, 0.4);
        --gt-border-soft: rgba(212, 175, 55, 0.18);
        --gt-gold: #D4AF37;
        --gt-gold-2: #C5A028;
        --gt-gold-soft: rgba(212, 175, 55, 0.07);
    }

    body { margin: 0; background-color: var(--c-dark-bg); color: var(--gt-text); }

    /* ================================================
       PAGE SHELL
    ================================================ */
    .visa-page {
        background: var(--c-dark-bg);
        min-height: 100vh;
        padding-top: 100px;
        padding-bottom: 48px;
        font-family: 'Outfit', sans-serif;
    }

    .visa-wrapper {
        width: 100%;
        max-width: 1300px;
        margin: 0 auto;
        padding: 0 24px;
    }

    /* ================================================
       PAGE HEADER
    ================================================ */
    .visa-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 22px;
        padding-bottom: 18px;
        border-bottom: 1px solid var(--gt-border-soft);
    }

    .visa-header-icon {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, rgba(255,215,0,0.15), rgba(255,215,0,0.05));
        border: 1px solid var(--gt-border-strong);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .visa-header-text {}

    .visa-title {
        color: var(--c-gold);
        font-weight: 800;
        font-size: 26px;
        text-transform: uppercase;
        letter-spacing: 3px;
        margin: 0;
        line-height: 1;
        text-shadow: 0 0 30px rgba(255, 215, 0, 0.18);
    }

    .visa-subtitle {
        color: var(--c-text-muted);
        font-size: 12px;
        font-weight: 400;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin: 4px 0 0;
        display: block;
    }

    /* ================================================
       TWO-COLUMN LAYOUT
    ================================================ */
    .visa-main {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 20px;
        align-items: start;
    }

    @media (max-width: 1024px) {
        .visa-main {
            grid-template-columns: 1fr 300px;
        }
    }

    @media (max-width: 860px) {
        .visa-main {
            grid-template-columns: 1fr;
        }
        /* On mobile, sidebar moves below the form */
        .checkout-col {
            order: 2;
        }
        .form-col {
            order: 1;
        }
    }

    @media (max-width: 860px) {
        .visa-page {
            padding-top: 90px;
        }
    }

    /* ================================================
       FORM SECTION CARDS
    ================================================ */
    .form-section {
        background: var(--c-card-bg);
        border: 1px solid var(--gt-border-soft);
        border-radius: 14px;
        padding: 18px 20px;
        position: relative;
        margin-bottom: 14px;
        overflow: hidden;
        transition: border-color 0.2s ease;
    }

    .form-section:last-child {
        margin-bottom: 0;
    }

    /* Top shimmer line */
    .form-section::before {
        content: '';
        position: absolute;
        top: 0; left: 50%;
        transform: translateX(-50%);
        width: 50%;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255,215,0,0.5), transparent);
    }

    /* Section header row */
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 14px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--gt-border-soft);
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--c-gold);
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2.5px;
    }

    .section-badge {
        width: 24px;
        height: 24px;
        background: linear-gradient(135deg, rgba(255,215,0,0.18), rgba(255,215,0,0.08));
        border: 1px solid var(--gt-border-strong);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: var(--c-gold);
        flex-shrink: 0;
    }

    /* ================================================
       FORM FIELDS
    ================================================ */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px 16px;
    }

    @media (max-width: 575.98px) {
        .form-grid {
            grid-template-columns: 1fr;
            gap: 10px;
        }
    }

    .form-field {
        display: flex;
        flex-direction: column;
    }

    .form-field.full {
        grid-column: 1 / -1;
    }

    .field-label {
        color: var(--c-text-muted);
        font-size: 10.5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 6px;
    }

    .field-input {
        width: 100%;
        height: 42px;
        background: var(--c-input-bg);
        border: 1px solid var(--c-input-border);
        border-radius: 9px;
        padding: 0 14px;
        color: var(--gt-text);
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 500;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .field-input:hover {
        border-color: var(--gt-border-strong);
    }

    .field-input:focus {
        outline: none;
        border-color: var(--c-gold);
        box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.07);
    }

    select.field-input {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='14' height='14' fill='%23FFD700'%3E%3Cpath d='M11.9997 13.1714L16.9495 8.22168L18.3637 9.63589L11.9997 15.9999L5.63574 9.63589L7.04996 8.22168L11.9997 13.1714Z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: calc(100% - 14px) center;
        padding-right: 36px;
        cursor: pointer;
    }

    /* ================================================
       intl-tel-input overrides
    ================================================ */
    .iti { width: 100% !important; }
    .iti__selected-flag {
        background: var(--c-input-bg) !important;
        border-right: 1px solid var(--c-input-border) !important;
        border-radius: 9px 0 0 9px !important;
    }
    .iti__country-list {
        background: var(--c-card-bg) !important;
        border: 1px solid var(--c-input-border) !important;
        color: var(--gt-text) !important;
    }
    .iti__country:hover {
        background: var(--gt-gold-soft) !important;
        color: var(--c-gold) !important;
    }
    .iti input { height: 42px !important; }

    /* ================================================
       TomSelect Theme overrides
    ================================================ */
    .ts-wrapper.field-input {
        padding: 0 !important;
        background: var(--c-input-bg) !important;
        border: 1px solid var(--c-input-border) !important;
        border-radius: 9px !important;
    }
    .ts-control {
        background: transparent !important;
        border: none !important;
        color: var(--gt-text) !important;
        padding: 0 14px !important;
        height: 40px !important;
        display: flex !important;
        align-items: center !important;
        font-family: 'Outfit', sans-serif !important;
        font-size: 14px !important;
    }
    .ts-control input { color: #fff !important; font-family: inherit !important; font-size: 14px !important; }
    .ts-control .item { color: #fff !important; }
    html[data-theme="light"] .ts-control input { color: var(--gt-text) !important; }
    html[data-theme="light"] .ts-control .item { color: var(--gt-text) !important; }
    .ts-dropdown {
        background: var(--c-card-bg) !important;
        border: 1px solid var(--c-input-border) !important;
        color: var(--gt-text) !important;
        border-radius: 10px !important;
        box-shadow: 0 20px 50px rgba(0,0,0,0.6) !important;
    }
    html[data-theme="light"] .ts-dropdown {
        background: var(--gt-surface) !important;
        border: 1px solid var(--gt-border-strong) !important;
    }
    .ts-dropdown .option { padding: 9px 14px !important; color: var(--gt-text-secondary) !important; }
    .ts-dropdown .active { background: var(--gt-gold-soft) !important; color: var(--c-gold) !important; }

    /* ================================================
       DOCUMENT UPLOADS
    ================================================ */
    .file-upload-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-top: 12px;
    }

    @media (max-width: 575.98px) {
        .file-upload-container { grid-template-columns: 1fr; }
    }

    .file-box {
        position: relative;
        border: 1.5px dashed var(--gt-border-strong);
        border-radius: 10px;
        padding: 14px 12px;
        text-align: center;
        cursor: pointer;
        transition: all 0.22s ease;
        background: var(--gt-gold-soft);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 90px;
        gap: 4px;
    }

    .file-box:hover {
        border-color: var(--c-gold);
        background: rgba(255, 215, 0, 0.08);
    }

    .file-box input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        z-index: 10;
        width: 100%;
        height: 100%;
    }

    .file-icon {
        font-size: 20px;
        color: var(--c-gold);
    }

    .file-text {
        font-size: 12px;
        font-weight: 600;
        color: var(--gt-text);
    }

    .file-sub {
        font-size: 10.5px;
        color: var(--c-text-muted);
    }

    .file-name-span {
        display: block;
        margin-top: 4px;
        font-size: 11px;
        font-weight: 500;
        color: var(--c-gold);
        word-break: break-all;
    }

    /* ================================================
       VISA TYPE META BOX
    ================================================ */
    .visa-type-meta {
        background: var(--gt-gold-soft);
        border: 1px dashed var(--gt-border-soft);
        border-radius: 9px;
        padding: 12px 14px;
        margin-top: 12px;
        font-size: 12.5px;
        line-height: 1.6;
        display: none;
    }
    .visa-type-meta h4 {
        margin: 0 0 6px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--c-gold);
    }
    .visa-type-meta ul {
        margin: 6px 0 0;
        padding-left: 18px;
    }

    /* ================================================
       SCAN RESULT
    ================================================ */
    .scan-result-card {
        padding: 10px 14px;
        border-radius: 9px;
        font-size: 12.5px;
        margin-top: 12px;
        display: none;
        line-height: 1.5;
    }
    .scan-result-card.success { background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.25); color: #2cde64; }
    .scan-result-card.scanning { background: rgba(255,215,0,0.08); border: 1px solid rgba(255,215,0,0.25); color: #FFD700; }
    .scan-result-card.error { background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.25); color: #ff4a4a; }

    /* ================================================
       CHECKOUT SIDEBAR
    ================================================ */
    .checkout-col {
        position: sticky;
        top: 100px;
    }

    .checkout-card {
        background: var(--c-card-bg);
        border: 1px solid var(--gt-border-strong);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 24px 60px rgba(0,0,0,0.35), 0 0 0 1px rgba(255,215,0,0.04) inset;
    }

    /* Gold header bar */
    .checkout-card-header {
        background: linear-gradient(135deg, rgba(255,215,0,0.12) 0%, rgba(255,215,0,0.04) 100%);
        border-bottom: 1px solid var(--gt-border-strong);
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .checkout-card-title {
        color: var(--c-gold);
        font-size: 11.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2.5px;
        margin: 0;
    }

    .checkout-card-body {
        padding: 16px 18px 18px;
    }

    /* Summary destination badge */
    .checkout-dest-badge {
        display: flex;
        align-items: center;
        gap: 10px;
        background: var(--gt-gold-soft);
        border: 1px solid var(--gt-border-soft);
        border-radius: 10px;
        padding: 10px 12px;
        margin-bottom: 14px;
    }
    .checkout-dest-flag {
        font-size: 26px;
        line-height: 1;
    }
    .checkout-dest-name {
        font-size: 13px;
        font-weight: 700;
        color: var(--gt-text);
    }
    .checkout-dest-sub {
        font-size: 11px;
        color: var(--c-text-muted);
    }

    .sum-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        padding: 8px 0;
        border-bottom: 1px solid var(--gt-border-soft);
        font-size: 13px;
    }

    .sum-row:first-child { padding-top: 0; }

    .sum-row.total {
        border-bottom: none;
        padding-top: 14px;
        margin-top: 6px;
    }

    .sum-label { color: var(--c-text-muted); font-weight: 500; }
    .sum-val { color: var(--gt-text); font-weight: 700; text-align: right; max-width: 56%; word-break: break-word; }
    .sum-total-val { color: var(--c-gold); font-size: 22px; font-weight: 800; }

    /* Progress steps in sidebar */
    .checkout-steps {
        display: flex;
        align-items: center;
        gap: 0;
        margin-bottom: 14px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--gt-border-soft);
    }
    .checkout-step {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        font-size: 9.5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--c-text-muted);
    }
    .checkout-step.active { color: var(--c-gold); }
    .checkout-step-dot {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        border: 2px solid var(--c-input-border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        color: var(--c-text-muted);
        background: var(--c-input-bg);
    }
    .checkout-step.active .checkout-step-dot {
        border-color: var(--c-gold);
        background: linear-gradient(135deg, rgba(255,215,0,0.2), rgba(255,215,0,0.05));
        color: var(--c-gold);
    }
    .checkout-step-line {
        flex: 1;
        height: 1px;
        background: var(--c-input-border);
        margin-top: -20px;
    }

    /* Submit button */
    .btn-submit {
        width: 100%;
        height: 48px;
        background: var(--c-gold-gradient);
        border: none;
        border-radius: 10px;
        color: #000;
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        cursor: pointer;
        transition: all 0.25s ease;
        margin-top: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 6px 20px rgba(255, 215, 0, 0.22);
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 28px rgba(255, 215, 0, 0.38);
    }
    .btn-submit:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* Trust badges below submit */
    .checkout-trust {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
        margin-top: 12px;
        flex-wrap: wrap;
    }
    .trust-item {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 10.5px;
        color: var(--c-text-muted);
    }
    .trust-item i { font-size: 11px; color: var(--c-gold); }

    /* ================================================
       LEGAL CHECKBOXES
    ================================================ */
    .legal-checkbox {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 10px 12px;
        border: 1px solid var(--gt-border-soft);
        border-radius: 9px;
        background: var(--gt-gold-soft);
        cursor: pointer;
        transition: border-color 0.2s ease;
        font-size: 12.5px;
        color: var(--gt-text-secondary);
        line-height: 1.5;
    }
    .legal-checkbox:hover {
        border-color: var(--gt-border-strong);
    }
    .legal-checkbox input[type="checkbox"] {
        width: 15px;
        height: 15px;
        margin-top: 2px;
        flex-shrink: 0;
        accent-color: var(--c-gold);
        cursor: pointer;
    }

    /* ================================================
       MOBILE STICKY FOOTER PAY BUTTON
    ================================================ */
    @media (max-width: 860px) {
        .mobile-pay-bar {
            display: flex;
        }
    }
    .mobile-pay-bar {
        display: none;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 999;
        background: var(--c-card-bg);
        border-top: 1px solid var(--gt-border-strong);
        padding: 12px 20px;
        gap: 12px;
        align-items: center;
        box-shadow: 0 -8px 30px rgba(0,0,0,0.5);
    }
    .mobile-pay-bar .pay-amount {
        flex: 1;
    }
    .mobile-pay-bar .pay-amount-label {
        font-size: 10px;
        color: var(--c-text-muted);
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .mobile-pay-bar .pay-amount-val {
        font-size: 18px;
        font-weight: 800;
        color: var(--c-gold);
    }
    .mobile-pay-bar .btn-submit {
        margin: 0;
        width: auto;
        flex-shrink: 0;
        min-width: 160px;
    }
    @media (max-width: 860px) {
        .visa-page {
            padding-bottom: 80px;
        }
    }
</style>

<div class="visa-page">
    <div class="visa-wrapper">

        {{-- Page Header --}}
        <div class="visa-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div class="visa-header-icon">🇸🇦</div>
                <div class="visa-header-text">
                    <h1 class="visa-title">Saudi Visa</h1>
                    <span class="visa-subtitle">Standalone Application</span>
                </div>
            </div>
            <a href="/umrah-visas" class="btn btn-outline-warning" style="font-weight: 700; border-radius: 8px; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; border: 1.5px solid var(--c-gold); color: var(--c-gold); padding: 8px 16px; text-decoration: none; transition: all 0.2s;">
                <i class="bi bi-arrow-left"></i> Back to Hajj & Umrah
            </a>
        </div>

        {{-- Two-Column Layout --}}
        <div class="visa-main">

            {{-- LEFT: Form Column --}}
            <div class="form-col">
                <form id="saudiVisaForm" action="{{ route('saudi-visa.submit') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Section 1: Contact & Visa Options --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-title">
                                <span class="section-badge"><i class="bi bi-envelope-fill"></i></span>
                                Your Details
                            </div>
                        </div>

                        {{-- Passport details are never typed. They are read from the
                             passport copy uploaded below (and from an optional early
                             scan), then posted in these hidden fields. Anything the
                             scan cannot read is filled in server-side after
                             submission, so a poor photo never blocks a booking. --}}
                        <input type="hidden" name="first_name" id="first_name">
                        <input type="hidden" name="last_name" id="last_name">
                        <input type="hidden" name="passport_number" id="passport_number">
                        <input type="hidden" name="dob" id="dob">
                        <input type="hidden" name="passport_expiry" id="passport_expiry">
                        <input type="hidden" name="gender" id="gender">
                        <input type="hidden" name="nationality" id="nationality">

                        <div class="form-grid">
                            <div class="form-field">
                                <label class="field-label">Full Name *</label>
                                <input type="text" name="full_name" id="full_name" class="field-input" required placeholder="John Doe" autocomplete="name">
                            </div>
                            <div class="form-field">
                                <label class="field-label">Email Address *</label>
                                <input type="email" name="email" id="email" class="field-input" required placeholder="john.doe@example.com" autocomplete="email">
                            </div>
                            <div class="form-field">
                                <label class="field-label">WhatsApp Number *</label>
                                <input type="tel" name="phone" id="phone" class="field-input" required autocomplete="tel">
                            </div>
                            <div class="form-field">
                                <label class="field-label">Visa Type *</label>
                                <select name="saudi_visa_type_id" id="saudi_visa_type_id" class="field-input" required>
                                    <option value="">Select Visa Type</option>
                                    @foreach($visaTypes as $vt)
                                        <option value="{{ $vt->id }}" data-price="{{ $vt->price }}" data-days="{{ $vt->processing_days }}" data-desc="{{ $vt->description }}" data-docs="{{ json_encode($vt->required_documents) }}">
                                            {{ $vt->name }} (AED {{ number_format($vt->price, 0) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="visa-type-meta" id="visaTypeMeta">
                            <h4 id="metaVisaName"></h4>
                            <div id="metaVisaDesc"></div>
                            <div id="metaVisaDocsContainer" style="margin-top: 8px;">
                                <strong>Required Documents:</strong>
                                <ul id="metaVisaDocsList"></ul>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Document Uploads --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-title">
                                <span class="section-badge"><i class="bi bi-upload"></i></span>
                                Document Uploads
                            </div>
                        </div>

                        <p style="color: #9a9a9a; font-size: 12.5px; margin: 0 0 12px; line-height: 1.5; display: flex; gap: 8px; align-items: flex-start;">
                            <i class="bi bi-magic" style="color: var(--c-gold); flex: none; margin-top: 2px;"></i>
                            <span>Passport details are read automatically from the copy you upload — there is nothing to type in.</span>
                        </p>

                        <div class="scan-result-card" id="scanStatusCard"></div>

                        <div class="file-upload-container">
                            <div class="form-field">
                                <label class="field-label">Passport Copy *</label>
                                <div class="file-box" id="fileBoxPassport">
                                    <i class="bi bi-file-earmark-pdf-fill file-icon"></i>
                                    <span class="file-text">Passport Copy</span>
                                    <span class="file-sub">PDF or Image, max 4MB</span>
                                    <input type="file" name="passport_copy" id="passport_copy" required accept="image/*,application/pdf" onchange="updateFileName(this, 'fileBoxPassport'); if (typeof window.scanPassportCopy === 'function') window.scanPassportCopy(this);">
                                    <span class="file-name-span" id="span_passport_copy"></span>
                                </div>
                            </div>
                            <div class="form-field">
                                <label class="field-label">Passport Photo *</label>
                                <div class="file-box" id="fileBoxPhoto">
                                    <i class="bi bi-image-fill file-icon"></i>
                                    <span class="file-text">Passport Photo</span>
                                    <span class="file-sub">White Background, max 4MB</span>
                                    <input type="file" name="passport_photo" id="passport_photo" required accept="image/*" onchange="updateFileName(this, 'fileBoxPhoto')">
                                    <span class="file-name-span" id="span_passport_photo"></span>
                                </div>
                            </div>
                            <div class="form-field full">
                                <label class="field-label">Additional Documents (Optional)</label>
                                <div class="file-box" id="fileBoxAdditional">
                                    <i class="bi bi-folder-fill file-icon"></i>
                                    <span class="file-text">Upload any supporting documents</span>
                                    <span class="file-sub">PDF or Image, max 4MB</span>
                                    <input type="file" name="additional_document" id="additional_document" accept="image/*,application/pdf" onchange="updateFileName(this, 'fileBoxAdditional')">
                                    <span class="file-name-span" id="span_additional_document"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: Terms & Conditions --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-title">
                                <span class="section-badge"><i class="bi bi-shield-check"></i></span>
                                Terms & Conditions
                            </div>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label class="legal-checkbox">
                                <input type="checkbox" name="passport_valid" value="1" required>
                                <span>I confirm that the applicant's passport is valid for <strong>6+ months</strong> from the date of travel.</span>
                            </label>
                            <label class="legal-checkbox">
                                <input type="checkbox" name="not_stay_long" value="1" required>
                                <span>I agree not to overstay in Saudi Arabia. If I overstay, I agree to pay the overstay charges per day and the absconding fee.</span>
                            </label>
                        </div>
                    </div>

                </form>
            </div>{{-- /form-col --}}

            {{-- RIGHT: Checkout Sidebar --}}
            <div class="checkout-col">
                <div class="checkout-card">

                    {{-- Card Header --}}
                    <div class="checkout-card-header">
                        <i class="bi bi-receipt" style="color: var(--c-gold); font-size: 15px;"></i>
                        <h3 class="checkout-card-title">Booking Summary</h3>
                    </div>

                    <div class="checkout-card-body">

                        {{-- Progress steps --}}
                        <div class="checkout-steps">
                            <div class="checkout-step active">
                                <div class="checkout-step-dot">1</div>
                                <span>Details</span>
                            </div>
                            <div class="checkout-step-line"></div>
                            <div class="checkout-step">
                                <div class="checkout-step-dot">2</div>
                                <span>Payment</span>
                            </div>
                            <div class="checkout-step-line"></div>
                            <div class="checkout-step">
                                <div class="checkout-step-dot">3</div>
                                <span>Done</span>
                            </div>
                        </div>

                        {{-- Destination badge --}}
                        <div class="checkout-dest-badge">
                            <div class="checkout-dest-flag">🇸🇦</div>
                            <div>
                                <div class="checkout-dest-name">Saudi Arabia</div>
                                <div class="checkout-dest-sub">Standalone e-Visa Application</div>
                            </div>
                        </div>

                        {{-- Summary rows --}}
                        <div class="sum-row">
                            <span class="sum-label">Visa Type</span>
                            <span class="sum-val" id="summaryVisaType">—</span>
                        </div>
                        <div class="sum-row">
                            <span class="sum-label">Applicant</span>
                            <span class="sum-val" id="summaryApplicant">—</span>
                        </div>
                        <div class="sum-row">
                            <span class="sum-label">Processing</span>
                            <span class="sum-val" id="summaryProcessing">—</span>
                        </div>
                        <div class="sum-row">
                            <span class="sum-label">Visa Fee</span>
                            <span class="sum-val" id="summaryVisaFee">AED 0.00</span>
                        </div>

                        <div class="sum-row total">
                            <span class="sum-label" style="font-size: 14px; font-weight: 700;">Total Payable</span>
                            <span class="sum-total-val" id="summaryTotal">AED 0.00</span>
                        </div>

                        <button type="submit" form="saudiVisaForm" class="btn-submit" id="btnSubmitForm">
                            <span>Proceed to Pay</span>
                            <i class="bi bi-arrow-right"></i>
                        </button>

                        {{-- Trust badges --}}
                        <div class="checkout-trust">
                            <span class="trust-item"><i class="bi bi-shield-lock-fill"></i> Secure Payment</span>
                            <span class="trust-item"><i class="bi bi-patch-check-fill"></i> Official Application</span>
                        </div>

                    </div>{{-- /checkout-card-body --}}
                </div>{{-- /checkout-card --}}
            </div>{{-- /checkout-col --}}

        </div>{{-- /visa-main --}}
    </div>{{-- /visa-wrapper --}}
</div>{{-- /visa-page --}}

{{-- Mobile sticky pay bar (visible only on small screens) --}}
<div class="mobile-pay-bar" id="mobilePayBar">
    <div class="pay-amount">
        <div class="pay-amount-label">Total</div>
        <div class="pay-amount-val" id="mobileSummaryTotal">AED 0.00</div>
    </div>
    <button type="submit" form="saudiVisaForm" class="btn-submit" style="margin:0; width:auto; min-width:150px;">
        <span>Pay Now</span> <i class="bi bi-arrow-right"></i>
    </button>
</div>

<script>
    // Map nationality name mappings
    const NAT_MAP = {
        'indian': 'india',
        'pakistani': 'pakistan',
        'british': 'united kingdom',
        'american': 'united states'
    };

    function updateFileName(input, boxId) {
        const box = document.getElementById(boxId);
        const span = document.getElementById('span_' + input.id);
        if (input.files && input.files[0]) {
            span.textContent = input.files[0].name;
            box.style.borderColor = 'var(--c-gold)';
        } else {
            span.textContent = '';
            box.style.borderColor = 'var(--gt-border-strong)';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Phone Input
        const phoneEl = document.getElementById('phone');
        let iti = null;
        if (phoneEl) {
            iti = window.intlTelInput(phoneEl, {
                separateDialCode: true,
                initialCountry: "ae",
                preferredCountries: ["ae", "sa", "qa", "in", "pk"],
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.5.3/build/js/utils.js"
            });
        }

        // Nationality is no longer asked for — it is read off the passport and
        // posted in a hidden field.
        const natEl = document.getElementById('nationality');
        const fullNameEl = document.getElementById('full_name');

        // Pricing Summary fields
        const visaTypeSelect = document.getElementById('saudi_visa_type_id');
        const summaryVisaType = document.getElementById('summaryVisaType');
        const summaryApplicant = document.getElementById('summaryApplicant');
        const summaryProcessing = document.getElementById('summaryProcessing');
        const summaryVisaFee = document.getElementById('summaryVisaFee');
        const summaryTotal = document.getElementById('summaryTotal');

        const metaContainer = document.getElementById('visaTypeMeta');
        const metaVisaName = document.getElementById('metaVisaName');
        const metaVisaDesc = document.getElementById('metaVisaDesc');
        const metaVisaDocsList = document.getElementById('metaVisaDocsList');
        const metaVisaDocsContainer = document.getElementById('metaVisaDocsContainer');

        function updateSummary() {
            const selectedOpt = visaTypeSelect.options[visaTypeSelect.selectedIndex];

            summaryApplicant.textContent = (fullNameEl && fullNameEl.value.trim()) || '—';

            if (selectedOpt && selectedOpt.value !== '') {
                const visaName = selectedOpt.text.split('(')[0].trim();
                const price = parseFloat(selectedOpt.getAttribute('data-price') || 0);
                const days = selectedOpt.getAttribute('data-days') || '3';
                const desc = selectedOpt.getAttribute('data-desc') || '';
                const docsRaw = selectedOpt.getAttribute('data-docs');
                const docs = docsRaw ? JSON.parse(docsRaw) : [];

                summaryVisaType.textContent = visaName;
                summaryProcessing.textContent = days + ' Days';
                summaryVisaFee.textContent = 'AED ' + price.toFixed(2);
                summaryTotal.textContent = 'AED ' + price.toFixed(2);
                const mobileTot = document.getElementById('mobileSummaryTotal');
                if (mobileTot) mobileTot.textContent = 'AED ' + price.toFixed(2);

                // Meta box
                metaVisaName.textContent = visaName;
                metaVisaDesc.textContent = desc;
                if (docs && docs.length > 0) {
                    metaVisaDocsContainer.style.display = 'block';
                    metaVisaDocsList.innerHTML = docs.map(d => `<li>${d}</li>`).join('');
                } else {
                    metaVisaDocsContainer.style.display = 'none';
                }
                metaContainer.style.display = 'block';
            } else {
                summaryVisaType.textContent = '—';
                summaryProcessing.textContent = '—';
                summaryVisaFee.textContent = 'AED 0.00';
                summaryTotal.textContent = 'AED 0.00';
                const mobileTot2 = document.getElementById('mobileSummaryTotal');
                if (mobileTot2) mobileTot2.textContent = 'AED 0.00';

                metaContainer.style.display = 'none';
            }
        }

        if (visaTypeSelect) visaTypeSelect.addEventListener('change', updateSummary);
        if (fullNameEl) fullNameEl.addEventListener('input', updateSummary);

        // Read the passport details straight off the uploaded copy. The customer
        // never types them, so a failed scan is not an error they have to fix —
        // the server reads the same file again after submission.
        const scanStatusCard = document.getElementById('scanStatusCard');

        function setScanStatus(state, icon, message) {
            if (!scanStatusCard) return;
            scanStatusCard.className = 'scan-result-card ' + state;
            scanStatusCard.style.display = 'block';
            scanStatusCard.innerHTML = '<span><i class="bi bi-' + icon + '"></i> ' + message + '</span>';
        }

        window.scanPassportCopy = function (input) {
            if (!input.files || input.files.length === 0) return;
            const file = input.files[0];

            // A PDF is a valid passport copy but cannot be scanned in the browser.
            // The server reads it after submission, so say so rather than warn.
            const isPdf = (file.type || '').toLowerCase() === 'application/pdf'
                || (file.name || '').toLowerCase().endsWith('.pdf');
            if (isPdf) {
                setScanStatus('scanning', 'info-circle-fill', 'We will read the passport details from your PDF after you submit.');
                return;
            }

            setScanStatus('scanning', 'hourglass-split', 'Reading passport details…');

            const token = document.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}';
            const fd = new FormData();
            fd.append('passport', file);
            fd.append('_token', token);

            fetch("{{ route('passport.extract') }}", {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                body: fd
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success || !data.fields) {
                    setScanStatus('scanning', 'info-circle-fill', 'We will read the passport details after you submit.');
                    return;
                }

                const f = data.fields;
                const set = (id, value) => {
                    const el = document.getElementById(id);
                    if (el && value) el.value = value;
                };

                set('first_name', f.given_names || f.full_name);
                set('last_name', f.surname || f.given_names || f.full_name);
                set('passport_number', f.passport_number);
                set('dob', f.date_of_birth);
                set('passport_expiry', f.date_of_expiry);

                if (f.sex) {
                    const sex = String(f.sex).toLowerCase();
                    if (sex.startsWith('m')) set('gender', 'Male');
                    else if (sex.startsWith('f')) set('gender', 'Female');
                }

                // Nationality is stored as free text now that the dropdown is gone.
                // Map the common adjective forms so the portal shows a country.
                const rawNat = f.nationality || f.issuing_country;
                if (rawNat && natEl) {
                    const key = String(rawNat).trim().toLowerCase();
                    natEl.value = NAT_MAP[key]
                        ? NAT_MAP[key].replace(/\b\w/g, c => c.toUpperCase())
                        : String(rawNat).trim();
                }

                // Offer the scanned name when the customer has not typed one yet.
                const scannedName = f.full_name
                    || [f.given_names, f.surname].filter(Boolean).join(' ');
                if (fullNameEl && !fullNameEl.value.trim() && scannedName) {
                    fullNameEl.value = scannedName;
                }

                setScanStatus('success', 'check-circle-fill', 'Passport details read successfully.');
                updateSummary();
            })
            .catch(err => {
                console.error('Passport scan failed:', err);
                setScanStatus('scanning', 'info-circle-fill', 'We will read the passport details after you submit.');
            });
        };

        // Submitting form: intercept, send via fetch, then redirect to checkout URL
        const form = document.getElementById('saudiVisaForm');
        const btnSubmitForm = document.getElementById('btnSubmitForm');

        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                if (iti && phoneEl) {
                    phoneEl.value = iti.getNumber();
                }

                btnSubmitForm.disabled = true;
                btnSubmitForm.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> SUBMITTING...';

                const fd = new FormData(form);
                const token = document.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}';

                fetch(form.action, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                    body: fd
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.checkout_url) {
                        window.location.href = data.checkout_url;
                    } else {
                        const msg = data.message || data.error || 'Submission failed. Please try again.';
                        alert('❌ ' + msg);
                        btnSubmitForm.disabled = false;
                        btnSubmitForm.innerHTML = '<span>Proceed to Pay</span> <i class="bi bi-arrow-right"></i>';
                    }
                })
                .catch(err => {
                    console.error('Submit error:', err);
                    alert('❌ Network error. Please check your connection and try again.');
                    btnSubmitForm.disabled = false;
                    btnSubmitForm.innerHTML = '<span>Proceed to Pay</span> <i class="bi bi-arrow-right"></i>';
                });
            });
        }
    });
</script>

@include('footer')
