@include('header')

<script>
    window.emiratesData = @json($activeEmirates->map(fn($e) => ['id' => $e->emiratesID, 'name' => $e->emiratesName])->toArray());
    window.visaPricingData = @json($pricingData);
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
        --c-card-bg: #0b0b0b;
        --c-input-bg: #111;
        --c-input-border: #222;
        --c-text-muted: #888;
        --c-text-light: #eee;
    }

    body { margin: 0; background-color: var(--c-dark-bg); }

    .visa-page {
        background: linear-gradient(180deg, #000 0%, #0a0a0a 100%);
        min-height: 100vh;
        padding-top: 170px;
        padding-bottom: 10px;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        font-family: 'Outfit', sans-serif;
    }

    .visa-wrapper {
        width: 100%;
        max-width: 1440px;
        padding: 0 28px;
    }

    .visa-header {
        display: flex;
        align-items: baseline;
        gap: 16px;
        margin-bottom: 10px;
    }

    .visa-title {
        color: var(--c-gold);
        font-weight: 800;
        font-size: 32px;
        text-transform: uppercase;
        letter-spacing: 4px;
        margin: 0;
        text-shadow: 0 0 30px rgba(255, 215, 0, 0.2);
    }

    .visa-subtitle {
        color: var(--c-text-muted);
        font-size: 13px;
        font-weight: 400;
        letter-spacing: 3px;
        text-transform: uppercase;
        margin: 0;
    }

    .visa-main {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 18px;
        align-items: start;
    }

    .card {
        background: var(--c-card-bg);
        border: 1px solid rgba(255, 215, 0, 0.1);
        border-radius: 14px;
        padding: 20px 24px;
        position: relative;
        box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.8);
    }

    .card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 40%;
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--c-gold), transparent);
        opacity: 0.5;
    }

    .card-title {
        color: var(--c-gold);
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 3px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        opacity: 0.9;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px 16px;
    }

    .form-grid-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 12px 16px;
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
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        margin-bottom: 6px;
    }

    .field-input {
        width: 100%;
        height: 48px;
        background: var(--c-input-bg);
        border: 1px solid var(--c-input-border);
        border-radius: 10px;
        padding: 0 16px;
        color: white;
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .field-input:hover {
        border-color: #333;
        background: #141414;
    }

    .field-input:focus {
        outline: none;
        border-color: var(--c-gold);
        background: #161616;
        box-shadow: 0 0 15px rgba(255, 215, 0, 0.05);
    }

    select.field-input {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='16' height='16' fill='%23FFD700'%3E%3Cpath d='M11.9997 13.1714L16.9495 8.22168L18.3637 9.63589L11.9997 15.9999L5.63574 9.63589L7.04996 8.22168L11.9997 13.1714Z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: calc(100% - 20px) center;
        cursor: pointer;
    }

    /* --- Phone with country dropdown (intl-tel-input) --- */
    .iti {
        width: 100% !important;
        display: block !important;
        position: relative;
    }
    .iti, .iti * { box-sizing: border-box; }
    .iti--separate-dial-code .iti__flag-container,
    .iti--allow-dropdown .iti__flag-container {
        height: 48px !important;
        top: 0 !important;
        bottom: auto !important;
        z-index: 2;
    }
    .iti__selected-flag {
        height: 48px !important;
        padding: 0 10px 0 14px !important;
        background: #161616 !important;
        border: 1px solid var(--c-input-border) !important;
        border-right: none !important;
        border-radius: 10px 0 0 10px !important;
        outline: none !important;
        display: flex !important;
        align-items: center !important;
        gap: 6px;
    }
    .iti__selected-flag:hover,
    .iti.iti--allow-dropdown .iti__flag-container:hover .iti__selected-flag,
    .iti--allow-dropdown .iti__flag-container:focus-within .iti__selected-flag {
        background: #1c1c1c !important;
    }
    .iti--separate-dial-code .iti__selected-dial-code {
        color: #FFD700 !important;
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 700;
        margin-left: 6px;
    }
    .iti__arrow { border-top-color: #FFD700 !important; margin-left: 6px; }
    .iti__arrow--up { border-bottom-color: #FFD700 !important; }
    .iti--separate-dial-code input.field-input[type=tel],
    .iti--allow-dropdown input.field-input[type=tel],
    .iti input.field-input[type=tel] {
        padding-left: 112px !important;
        border-radius: 0 10px 10px 0 !important;
        border-left: none !important;
    }
    .iti__country-list {
        background: #0d0d0d !important;
        border: 1px solid #2a2a2a !important;
        color: #eee !important;
        border-radius: 10px !important;
        box-shadow: 0 20px 60px rgba(0,0,0,0.7) !important;
        max-height: 260px !important;
        overflow-y: auto !important;
        z-index: 9999 !important;
        padding: 6px 0 !important;
        margin-top: 4px !important;
    }
    .iti__country {
        padding: 8px 14px !important;
        color: #ddd !important;
    }
    .iti__country.iti__highlight,
    .iti__country:hover {
        background: rgba(255, 215, 0, 0.12) !important;
        color: #fff !important;
    }
    .iti__country-name { color: inherit !important; }
    .iti__dial-code { color: #FFD700 !important; opacity: 0.9; }
    .iti__divider {
        border-bottom-color: #2a2a2a !important;
        background: #2a2a2a !important;
        margin: 4px 0 !important;
    }
    .iti__search-input {
        background: #0a0a0a !important;
        color: #fff !important;
        border: none !important;
        border-bottom: 1px solid #2a2a2a !important;
        padding: 10px 14px !important;
        width: 100% !important;
    }
    .iti__search-input::placeholder { color: #777 !important; }

    /* --- SECTION DIVIDERS --- */
    .section-divider {
        margin: 18px 0;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
    }

    .section-label {
        color: var(--c-gold);
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2.5px;
        margin-bottom: 14px;
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* --- APPLICANT BOX --- */
    .applicant-box {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 215, 0, 0.05);
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 8px;
    }

    .applicant-header {
        color: #eee;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .applicant-header span {
        color: var(--c-gold);
    }

    /* --- FILE UPLOAD --- */
    .file-input-wrapper {
        position: relative;
        width: 100%;
        height: 40px;
    }

    .file-input-real {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 2;
    }

    .file-input-custom {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #111;
        border: 1px dashed #333;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 16px;
        transition: all 0.2s ease;
        z-index: 1;
    }

    .file-input-wrapper:hover .file-input-custom {
        border-color: #555;
        background: #161616;
    }

    .file-input-wrapper:focus-within .file-input-custom {
        border-color: var(--c-gold);
        background: #161616;
    }

    .file-name {
        color: #666;
        font-size: 13px;
        font-weight: 400;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 80%;
    }

    .file-name.selected {
        color: var(--c-gold);
        font-weight: 500;
    }

    .file-icon {
        color: #444;
        font-size: 16px;
    }

    .file-input-wrapper:hover .file-icon {
        color: var(--c-gold);
    }

    /* --- CHECKBOXES --- */
    .checkbox-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 4px;
    }

    .custom-checkbox-label {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        cursor: pointer;
        user-select: none;
    }

    .custom-checkbox-input {
        display: none;
    }

    .custom-checkbox-box {
        width: 20px;
        height: 20px;
        min-width: 20px;
        margin-top: 1px;
        border: 1px solid #444;
        border-radius: 4px;
        background: #0f0f0f;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .custom-checkbox-input:checked+.custom-checkbox-box {
        background: var(--c-gold);
        border-color: var(--c-gold);
    }

    .custom-checkbox-box i {
        color: #000;
        font-size: 14px;
        opacity: 0;
        transform: scale(0.5);
        transition: all 0.2s;
    }

    .custom-checkbox-input:checked+.custom-checkbox-box i {
        opacity: 1;
        transform: scale(1);
    }

    .checkbox-text {
        color: #bbb;
        font-size: 15px;
        letter-spacing: 0.2px;
        line-height: 1.5;
    }

    /* --- ADD-ON SERVICES --- */
    .addon-card {
        background: rgba(255, 215, 0, 0.03);
        border: 1px solid rgba(255, 215, 0, 0.08);
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 10px;
        transition: all 0.2s ease;
    }

    .addon-card:hover {
        border-color: rgba(255, 215, 0, 0.15);
    }

    .addon-card.active {
        border-color: rgba(255, 215, 0, 0.3);
        background: rgba(255, 215, 0, 0.06);
    }

    .addon-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .addon-info {
        flex: 1;
    }

    .addon-title {
        color: #eee;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .addon-desc {
        color: #777;
        font-size: 12px;
        line-height: 1.5;
    }

    .addon-price {
        color: var(--c-gold);
        font-size: 16px;
        font-weight: 700;
        white-space: nowrap;
    }

    /* --- PHOTO GUIDE LINK --- */
    .photo-guide-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--c-gold);
        font-size: 11px;
        font-weight: 600;
        text-decoration: none;
        letter-spacing: 0.5px;
        margin-top: 4px;
        transition: all 0.2s ease;
    }
    .photo-guide-link:hover {
        color: #fff;
        text-decoration: none;
    }

    /* --- SUMMARY --- */
    .summary-card-wrapper {
        position: sticky;
        top: 190px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .summary-label {
        color: #777;
        font-size: 13px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .summary-value {
        color: #eee;
        font-weight: 600;
        font-size: 14px;
    }

    .summary-total {
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px solid rgba(255, 215, 0, 0.2);
        display: flex;
        justify-content: space-between;
        align-items: baseline;
    }

    .total-label {
        color: var(--c-gold);
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
    }

    .total-value {
        color: var(--c-gold);
        font-size: 28px;
        font-weight: 700;
        text-shadow: 0 0 40px rgba(255, 215, 0, 0.3);
    }

    .pay-btn {
        width: 100%;
        height: 50px;
        background: var(--c-gold-gradient);
        border: none;
        border-radius: 10px;
        margin-top: 20px;
        color: #000;
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 3px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        transition: all 0.3s ease;
        box-shadow: 0 10px 30px -5px rgba(255, 215, 0, 0.15);
    }

    .pay-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        background: #fff;
    }

    .pay-btn:disabled {
        background: #222;
        color: #555;
        cursor: not-allowed;
    }

    .secure-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 14px;
        color: #444;
        font-size: 9px;
        font-weight: 600;
        letter-spacing: 1.5px;
        text-transform: uppercase;
    }

    @media (max-width: 1024px) {
        .visa-main {
            grid-template-columns: 1fr;
        }
        .summary-card-wrapper {
            position: relative;
            top: 0;
        }
        .form-grid-3 {
            grid-template-columns: 1fr 1fr;
        }
        .visa-header {
            flex-direction: column;
            gap: 4px;
            text-align: center;
            align-items: center;
        }
    }

    @media (max-width: 600px) {
        .form-grid-3 {
            grid-template-columns: 1fr;
        }
        .form-grid {
            grid-template-columns: 1fr;
        }
    }

    /* --- Tom Select Dark Mode Theme --- */
    .ts-wrapper.field-input {
        padding: 0 !important;
        border: none !important;
        background: transparent !important;
    }
    .ts-wrapper.field-input .ts-control {
        background: var(--c-input-bg) !important;
        border: 1px solid var(--c-input-border) !important;
        border-radius: 10px !important;
        padding: 0 16px !important;
        color: #fff !important;
        font-family: 'Outfit', sans-serif !important;
        font-size: 15px !important;
        font-weight: 500 !important;
        height: 48px !important;
        display: flex !important;
        align-items: center !important;
        cursor: pointer !important;
        box-shadow: none !important;
        transition: all 0.2s ease;
        position: relative !important;
    }
    .ts-wrapper.field-input.focus .ts-control {
        border-color: var(--c-gold) !important;
        background: #161616 !important;
        box-shadow: 0 0 15px rgba(255, 215, 0, 0.05) !important;
    }
    .ts-wrapper.field-input .ts-control input {
        color: #fff !important;
        font-family: 'Outfit', sans-serif !important;
        font-size: 15px !important;
        padding: 0 !important;
    }
    .ts-wrapper.field-input.single .ts-control:after {
        border-color: var(--c-gold) transparent transparent transparent !important;
        border-width: 6px 5px 0 5px !important;
        right: 20px !important;
    }
    .ts-wrapper.field-input.single.dropdown-active .ts-control:after {
        border-color: transparent transparent var(--c-gold) transparent !important;
        border-width: 0 5px 6px 5px !important;
    }
    .ts-dropdown {
        background: #0d0d0d !important;
        border: 1px solid #2a2a2a !important;
        border-radius: 10px !important;
        box-shadow: 0 20px 60px rgba(0,0,0,0.7) !important;
        margin-top: 4px !important;
        z-index: 1000 !important;
        padding: 6px 0 !important;
    }
    .ts-dropdown .option {
        padding: 8px 14px !important;
        color: #ddd !important;
        font-family: 'Outfit', sans-serif !important;
        font-size: 14px !important;
        cursor: pointer !important;
    }
    .ts-dropdown .active,
    .ts-dropdown .option:hover {
        background: rgba(255, 215, 0, 0.12) !important;
        color: #fff !important;
    }

    /* --- Tom Select Light Mode Theme Overrides --- */
    html[data-theme="light"] .ts-wrapper.field-input .ts-control {
        background: var(--gt-surface) !important;
        border: 1px solid var(--gt-border-strong) !important;
        color: var(--gt-text) !important;
    }
    html[data-theme="light"] .ts-wrapper.field-input.focus .ts-control {
        border-color: var(--gt-gold-2) !important;
        background: #ffffff !important;
        box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.18) !important;
    }
    html[data-theme="light"] .ts-wrapper.field-input .ts-control input {
        color: var(--gt-text) !important;
    }
    html[data-theme="light"] .ts-wrapper.field-input.single .ts-control:after {
        border-color: var(--gt-gold) transparent transparent transparent !important;
    }
    html[data-theme="light"] .ts-wrapper.field-input.single.dropdown-active .ts-control:after {
        border-color: transparent transparent var(--gt-gold) transparent !important;
    }
    html[data-theme="light"] .ts-dropdown {
        background: var(--gt-surface) !important;
        border: 1px solid var(--gt-border-strong) !important;
        box-shadow: var(--gt-shadow-lg) !important;
    }
    html[data-theme="light"] .ts-dropdown .option {
        color: var(--gt-text-body) !important;
    }
    html[data-theme="light"] .ts-dropdown .active,
    html[data-theme="light"] .ts-dropdown .option:hover {
        background: var(--gt-gold-soft) !important;
        color: var(--gt-gold) !important;
    }
    html[data-theme="light"] .ts-dropdown .no-results {
        color: var(--gt-text-muted) !important;
    }

    /* --- Active Emirate Badge Styling --- */
    .emirate-active-badge {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: rgba(255, 215, 0, 0.05);
        border: 1px solid rgba(255, 215, 0, 0.22);
        border-radius: 10px;
        padding: 5px 6px 5px 14px;
        font-size: 14px;
        font-weight: 700;
        color: #FFD700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-family: 'Outfit', sans-serif;
    }
    .emirate-active-badge button {
        background: #dc3545;
        border: none;
        color: #fff !important;
        cursor: pointer;
        font-size: 13px;
        padding: 6px 14px;
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-radius: 6px;
        transition: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.35);
    }
    .emirate-active-badge button:hover {
        background: #bd2130;
        box-shadow: 0 6px 20px rgba(220, 53, 69, 0.5);
        transform: translateY(-1px);
    }
    .emirate-active-badge button:active {
        transform: translateY(0);
    }

    /* Light Theme active badge overrides */
    html[data-theme="light"] .emirate-active-badge {
        background: var(--gt-gold-soft) !important;
        border: 1px solid var(--gt-border-strong) !important;
        color: var(--gt-gold) !important;
    }
    html[data-theme="light"] .emirate-active-badge button {
        background: #dc3545 !important;
        color: #fff !important;
    }
    html[data-theme="light"] .emirate-active-badge button:hover {
        background: #bd2130 !important;
    }
</style>

<div class="visa-page">
    <div class="visa-wrapper">
        <div class="visa-header">
            <h1 class="visa-title">UAE Visa Services</h1>
            <div class="visa-subtitle-wrapper" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <p class="visa-subtitle" style="margin: 0;">Premium Processing</p>
                <div id="emirateActiveBadge" class="emirate-active-badge" style="display: none;">
                    <span class="badge-icon"></span>
                    <span class="badge-text">Dubai Processing</span>
                    <button type="button" onclick="window.showEmirateSelector()"><i class="bi bi-pencil-square"></i> Change</button>
                </div>
            </div>
        </div>

        <div class="visa-main">
            <!-- FORM -->
            <div class="card">
                <div class="card-title">
                    <span><i class="bi bi-file-earmark-text-fill"></i> Application Details</span>
                </div>

                <form id="visaForm" method="POST" action="{{ route('uaev.submit') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="selected_emirate" id="selectedEmirate" value="">

                    {{-- Scan Passport → auto-fill (Groq vision OCR) --}}
                    <div class="pp-scan">
                        <div class="pp-scan-info">
                            <span class="pp-scan-badge"><i class="bi bi-stars"></i></span>
                            <div>
                                <strong>Scan passport to auto-fill</strong>
                                <span>Upload or photograph the passport's main page — we'll detect the details for you.</span>
                            </div>
                        </div>
                        <div class="pp-scan-actions">
                            <label class="pp-scan-btn pp-scan-btn--primary" id="ppScanBtnLabel">
                                <i class="bi bi-cloud-arrow-up"></i> <span id="ppScanLabel">Upload</span>
                                <input type="file" id="ppScanFile" accept="image/jpeg,image/png,image/jpg,image/webp" hidden>
                            </label>
                            <button type="button" class="pp-scan-btn pp-scan-cam" id="ppCamBtn">
                                <i class="bi bi-camera"></i> Camera
                            </button>
                        </div>
                    </div>
                    <div id="ppScanResult" class="pp-scan-result" style="display:none;"></div>
                    {{-- Camera capture overlay --}}
                    <div id="ppCamModal" class="pp-cam-modal" style="display:none;">
                        <div class="pp-cam-box">
                            <p class="pp-cam-title"><i class="bi bi-camera"></i> Position the passport in frame</p>
                            <video id="ppCamVideo" autoplay playsinline muted></video>
                            <p class="pp-cam-hint">Hold the passport's main page flat and well-lit, then capture.</p>
                            <div class="pp-cam-actions">
                                <button type="button" id="ppCamCapture" class="pp-scan-btn pp-scan-btn--primary"><i class="bi bi-camera-fill"></i> Capture</button>
                                <button type="button" id="ppCamClose" class="pp-cam-close">Cancel</button>
                            </div>
                        </div>
                    </div>
                    <canvas id="ppCamCanvas" style="display:none;"></canvas>

                    {{-- ROW 1: Nationality, Visa Package --}}
                    <div class="form-grid-3" style="grid-template-columns: 1fr 1fr;">
                        <div class="form-field">
                            <label class="field-label">Nationality</label>
                            <select id="nationality" name="nationality" class="field-input" required>
                                <option value="">Select Nationality</option>
                                @foreach(\App\Support\CountryCodes::all() as $c)
                                    <option value="{{ $c['name'] }}" data-iso2="{{ strtolower($c['iso'] ?? '') }}">{{ $c['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-field">
                            <label class="field-label">Visa Package</label>
                            <select class="field-input" id="visaPackage" name="visa_package_id" required>
                                <option value="">Select Package</option>
                            </select>
                        </div>
                    </div>

                    {{-- ROW 2: Visa Type (Entry), Duration --}}
                    <div class="form-grid-3" style="grid-template-columns: 1fr 1fr; margin-top: 12px;">
                        <div class="form-field">
                            <label class="field-label">Visa Type</label>
                            <select class="field-input" id="visaEntryType" name="entry_type" required>
                                <option value="">Select Type</option>
                            </select>
                        </div>
                        <div class="form-field">
                            <label class="field-label">Visa Duration</label>
                            <select class="field-input" id="visaDuration" name="visaDuration" required>
                                <option value="">Select Duration</option>
                            </select>
                            <input type="hidden" name="price" id="hiddenPrice" value="0">
                        </div>
                    </div>

                    {{-- ROW 2: Adults, Children (2-12), Infants (0-2) --}}
                    <div class="form-grid-3" style="margin-top: 12px;">
                        <div class="form-field">
                            <label class="field-label">Adults</label>
                            <select class="field-input" id="visaCount" name="visa_count" required>
                                @for ($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ $i === 1 ? 'selected' : '' }}>{{ $i }} {{ $i === 1 ? 'Adult' : 'Adults' }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-field">
                            <label class="field-label">Children (2–12 yrs)</label>
                            <select class="field-input" id="visaChildren" name="children_count">
                                @for ($i = 0; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ $i === 0 ? 'selected' : '' }}>{{ $i }} {{ $i === 1 ? 'Child' : 'Children' }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-field">
                            <label class="field-label">Infants (0–2 yrs)</label>
                            <select class="field-input" id="visaInfants" name="infants_count">
                                @for ($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ $i === 0 ? 'selected' : '' }}>{{ $i }} {{ $i === 1 ? 'Infant' : 'Infants' }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    {{-- Universal Applicant Section --}}
                    <div id="universalApplicantSection" style="margin-top: 12px;">
                        <div class="section-label" id="applicantSectionLabel" style="display: none; margin-bottom: 8px;">
                            <i class="bi bi-person-fill"></i> Applicant Details
                        </div>
                        <div class="form-grid" id="applicantGrid">
                            <div class="form-field" id="applicantNameField" style="display: none;">
                                <label class="field-label">Full Name <span style="color: var(--c-gold);">*</span></label>
                                <input type="text" class="field-input" name="applicant_name" id="applicantNameInput" placeholder="Enter applicant's full name">
                            </div>
                            <div class="form-field">
                                <label class="field-label">Email Address <span style="color: var(--c-gold);">*</span></label>
                                <input type="email" class="field-input" name="email" placeholder="name@example.com" required>
                            </div>
                            <div class="form-field">
                                <label class="field-label">WhatsApp Number <span style="color: var(--c-gold);">*</span></label>
                                <input type="tel" id="phoneInput" class="field-input" name="phone" placeholder="50 000 0000" required>
                            </div>
                        </div>
                    </div>

                    <div class="section-divider"></div>

                    {{-- AGREEMENTS --}}
                    <div class="checkbox-group">
                        <label class="custom-checkbox-label">
                            <input type="checkbox" class="custom-checkbox-input" name="passport_valid" value="1" required>
                            <span class="custom-checkbox-box"><i class="bi bi-check-lg"></i></span>
                            <span class="checkbox-text">I confirm that all passports are valid for 6+ months from the date of travel.</span>
                        </label>
                        <label class="custom-checkbox-label">
                            <input type="checkbox" class="custom-checkbox-input" name="overstay_agree" value="1" required>
                            <span class="custom-checkbox-box"><i class="bi bi-check-lg"></i></span>
                            <span class="checkbox-text">I agree not to overstay. If so, I agree to pay the overstay charges per day (AED 3,200) and also the absconding fee.</span>
                        </label>
                    </div>

                    <div class="section-divider"></div>

                    {{-- DOCUMENT UPLOADS --}}
                    <div class="section-label">
                        <i class="bi bi-cloud-arrow-up-fill"></i> Document Uploads
                    </div>

                    <!-- APPLICANTS CONTAINER -->
                    <div id="applicantsContainer">
                        <!-- Dynamic fields injected by JS -->
                    </div>

                    {{-- Sharjah Refund Bank Details --}}
                    <div id="sharjahRefundFields" style="display: none; margin-top: 16px;">
                        <div class="section-divider"></div>
                        <div class="section-label">
                            <i class="bi bi-bank2"></i> Sharjah Refund Bank Details
                        </div>
                        <div style="background: rgba(255, 215, 0, 0.03); border: 1px solid rgba(255, 215, 0, 0.1); border-radius: 10px; padding: 16px; margin-bottom: 12px;">
                            <p style="color: #FFD700; font-size: 13px; font-weight: 500; margin: 0 0 12px 0; line-height: 1.4;">
                                @if(($sharjahDeposit ?? 0) > 0)
                                    A security deposit of <strong>AED {{ number_format($sharjahDeposit, 0) }} per applicant</strong> is required for Sharjah Visa processing. Please provide the bank details where the deposit should be refunded after departure.
                                @else
                                    A refundable security deposit per applicant is required for Sharjah Visa processing. Please provide the bank details where the deposit should be refunded after departure.
                                @endif
                            </p>
                            <div class="form-grid">
                                <div class="form-field">
                                    <label class="field-label">Account Holder Name</label>
                                    <input type="text" class="field-input" id="bank_account_holder" name="bank_account_holder" placeholder="Enter full name">
                                </div>
                                <div class="form-field">
                                    <label class="field-label">Bank Name</label>
                                    <input type="text" class="field-input" id="bank_name" name="bank_name" placeholder="Enter bank name">
                                </div>
                            </div>
                            <div class="form-grid" style="margin-top: 12px;">
                                <div class="form-field">
                                    <label class="field-label">Account Number / IBAN</label>
                                    <input type="text" class="field-input" id="bank_account_number" name="bank_account_number" placeholder="AE00 0000 ...">
                                </div>
                                <div class="form-field">
                                    <label class="field-label">SWIFT Code</label>
                                    <input type="text" class="field-input" id="bank_swift_code" name="bank_swift_code" placeholder="Enter SWIFT code (optional)">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-divider"></div>

                    {{-- ADD-ON SERVICES --}}
                    <div class="section-label">
                        <i class="bi bi-plus-circle-fill"></i> Optional Services
                    </div>

                    <div class="addon-card" id="addonHotel">
                        <div class="addon-header">
                            <label class="custom-checkbox-label" style="gap: 12px;">
                                <input type="checkbox" class="custom-checkbox-input" name="hotel_booking" value="1" id="hotelCheckbox">
                                <span class="custom-checkbox-box"><i class="bi bi-check-lg"></i></span>
                                <div class="addon-info">
                                    <div class="addon-title">Hotel Booking Required</div>
                                    <div class="addon-desc">If not available, we can arrange it for you. Additional cost is involved.</div>
                                </div>
                            </label>
                            <div class="addon-price" id="hotelPriceLabel">{{ number_format($hotelFee ?? 25, 0) }} AED</div>
                        </div>
                    </div>

                    <div class="addon-card" id="addonTicket">
                        <div class="addon-header">
                            <label class="custom-checkbox-label" style="gap: 12px;">
                                <input type="checkbox" class="custom-checkbox-input" name="ticket_booking" value="1" id="ticketCheckbox">
                                <span class="custom-checkbox-box"><i class="bi bi-check-lg"></i></span>
                                <div class="addon-info">
                                    <div class="addon-title">Ticket Booking Assistance</div>
                                    <div class="addon-desc">If you don't have a ticket booking, we can help you with bookings.</div>
                                </div>
                            </label>
                            <div class="addon-price" id="ticketPriceLabel">{{ number_format($ticketFee ?? 25, 0) }} AED</div>
                        </div>
                    </div>

                    <input type="hidden" name="hotel_booking_fee" value="{{ $hotelFee ?? 25 }}">
                    <input type="hidden" name="ticket_booking_fee" value="{{ $ticketFee ?? 25 }}">

                </form>
            </div>

            <!-- RIGHT: SUMMARY -->
            <div class="summary-card-wrapper">
                {{-- Photo Guidelines Card — shown first so users see it before uploading --}}
                <div class="card">
                    <div class="card-title">
                        <span><i class="bi bi-camera-fill"></i> Photo Requirements</span>
                    </div>
                    <img src="{{ asset('assets/photo-guidelines-quick-ref.jpg') }}" alt="Photo Guidelines" style="width: 100%; border-radius: 8px; margin-bottom: 12px;">
                    <a href="{{ asset('assets/uae-photo-guide.pdf') }}" target="_blank" class="photo-guide-link" style="font-size: 13px;">
                        <i class="bi bi-download"></i> Download Full Photo Guidelines (PDF)
                    </a>
                </div>

                {{-- Payment Summary --}}
                <div class="card" style="margin-top: 14px;">
                    <div class="card-title">
                        <i class="bi bi-receipt-cutoff"></i> Payment Summary
                    </div>

                    <div class="summary-row">
                        <span class="summary-label">Visa Fee (<span id="countDisplay">1</span>x)</span>
                        <span class="summary-value" id="summaryBase">AED 0.00</span>
                    </div>
                    <div class="summary-row" id="hotelRow" style="display: none;">
                        <span class="summary-label">Hotel Booking</span>
                        <span class="summary-value" id="summaryHotel">AED {{ number_format($hotelFee ?? 25, 2) }}</span>
                    </div>
                    <div class="summary-row" id="ticketRow" style="display: none;">
                        <span class="summary-label">Ticket Booking</span>
                        <span class="summary-value" id="summaryTicket">AED {{ number_format($ticketFee ?? 25, 2) }}</span>
                    </div>
                    <div class="summary-row" id="depositRow" style="display: none;">
                        <span class="summary-label" style="color: #FFD700;">Security Deposit</span>
                        <span class="summary-value" id="summaryDeposit" style="color: #FFD700;">AED 0.00</span>
                    </div>
                    <div class="summary-row" id="refundRow" style="display: none;">
                        <span class="summary-label" style="color: #22c55e;">Refundable Amount</span>
                        <span class="summary-value" id="summaryRefund" style="color: #22c55e;">AED 0.00</span>
                    </div>
                    <div class="summary-total">
                        <span class="total-label">Total Payable</span>
                        <span class="total-value" id="summaryTotal">AED 0.00</span>
                    </div>

                    <button type="submit" form="visaForm" class="pay-btn" id="submitBtn">
                        <i class="bi bi-shield-lock-fill"></i> PAY SECURELY
                    </button>

                    <div class="secure-badge">
                        <i class="bi bi-shield-check"></i> 256-BIT SSL SECURED PAYMENT
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateFileName(input) {
        const fileNameSpan = input.parentElement.querySelector('.file-name');
        if (input.files && input.files.length > 0) {
            fileNameSpan.textContent = input.files[0].name;
            fileNameSpan.classList.add('selected');
        } else {
            const name = input.getAttribute('name');
            if (name.includes('passport_copy')) fileNameSpan.textContent = 'Upload passport (PDF/Image)...';
            else if (name.includes('passport_photo')) fileNameSpan.textContent = 'Upload photo (ID Photo)...';
            else if (name.includes('airline_ticket')) fileNameSpan.textContent = 'Upload ticket (PDF/Image)...';
            else fileNameSpan.textContent = 'Upload documents (if any)...';
            fileNameSpan.classList.remove('selected');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const countrySelects = [document.getElementById('nationality')].filter(Boolean);
        let nationalitySelect = null;
        const natEl = document.getElementById('nationality');
        // Build a value→iso2 lookup from the <select>'s option[data-iso2] attributes
        // so flags work regardless of how TomSelect exposes custom data attributes.
        function buildIsoMap(selectEl) {
            var map = {};
            if (!selectEl) return map;
            Array.prototype.forEach.call(selectEl.querySelectorAll('option[data-iso2]'), function(opt) {
                var iso = opt.getAttribute('data-iso2');
                if (opt.value && iso) map[opt.value] = iso.toLowerCase();
            });
            return map;
        }

        function makeFlagHtml(iso) {
            if (!iso || iso.length !== 2) return '';
            return '<img src="https://flagcdn.com/w40/' + iso + '.png"'
                + ' style="width:20px;height:14px;margin-right:8px;vertical-align:middle;'
                + 'object-fit:cover;border-radius:2px;flex-shrink:0;" alt="" loading="lazy"'
                + ' onerror="this.style.display=\'none\'">';
        }

        if (natEl && typeof TomSelect !== 'undefined') {
            var natIsoMap = buildIsoMap(natEl);
            var renderCountryRow = function(data, escape) {
                var iso = natIsoMap[data.value] || data.iso2 || '';
                return '<div style="display:flex;align-items:center;gap:0;">'
                    + makeFlagHtml(iso)
                    + '<span>' + escape(data.text) + '</span>'
                    + '</div>';
            };
            nationalitySelect = new TomSelect(natEl, {
                create: false,
                placeholder: 'Select Nationality',
                controlInput: '<input>',
                render: {
                    option: renderCountryRow,
                    item: renderCountryRow,
                    no_results: function(data, escape) {
                        return '<div class="no-results" style="padding: 8px 14px; color: #888;">No nationality found for "' + escape(data.input) + '"</div>';
                    }
                }
            });
            window.nationalityTomSelect = nationalitySelect;
        }

        const visaCountSelect = document.getElementById('visaCount');
        const childrenCountInput = document.getElementById('visaChildren');
        const infantsCountInput = document.getElementById('visaInfants');
        const applicantsContainer = document.getElementById('applicantsContainer');
        const hotelCheckbox = document.getElementById('hotelCheckbox');
        const ticketCheckbox = document.getElementById('ticketCheckbox');

        const visaPackageSelect = document.getElementById('visaPackage');
        const visaEntryTypeSelect = document.getElementById('visaEntryType');
        const visaDurationSelect = document.getElementById('visaDuration');

        // --- Emirate selection: driven by the popup modal, no silent default ---
        // Nothing is pre-selected on load; the visitor must explicitly pick an
        // emirate in the popup every time they land on the page (no localStorage restore).
        let selectedEmirateName = null;
        const EMIRATE_FLAGS = {
            'Dubai': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 3" style="width: 16px; height: 12px; display: inline-block; border-radius: 1px; border: 1px solid rgba(255,255,255,0.2); vertical-align: middle; margin-right: 4px;"><rect width="4" height="3" fill="#D7141A" /><rect width="1" height="3" fill="#FFF" /></svg>',
            'Sharjah': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2 1" style="width: 16px; height: 12px; display: inline-block; border-radius: 1px; border: 1px solid rgba(255,255,255,0.2); vertical-align: middle; margin-right: 4px;"><rect width="2" height="1" fill="#FFF" /><rect x="0.25" y="0.125" width="1.5" height="0.75" fill="#D7141A" /></svg>'
        };

        function getAdults() { return parseInt(visaCountSelect.value, 10) || 1; }
        function getChildren() { return parseInt(childrenCountInput.value, 10) || 0; }
        function getInfants() { return parseInt(infantsCountInput.value, 10) || 0; }

        function generateApplicants(adults, children, infants) {
            applicantsContainer.innerHTML = '';
            const total = adults + children + infants;
            const isSharjah = selectedEmirateName && selectedEmirateName.toLowerCase() === 'sharjah';
            for (let i = 0; i < total; i++) {
                let label, ageField = '';
                if (i < adults) {
                    label = `Adult #${i + 1}`;
                } else if (i < adults + children) {
                    label = `Child #${i - adults + 1}`;
                    ageField = `
                         <div class="form-field">
                             <label class="field-label">Child Age (2–12)</label>
                             <select name="child_age[]" class="field-input" required>
                                 ${Array.from({length: 11}, (_, k) => k + 2).map(a => `<option value="${a}">${a} years</option>`).join('')}
                             </select>
                         </div>`;
                } else {
                    label = `Infant #${i - adults - children + 1}`;
                    ageField = `
                         <div class="form-field">
                             <label class="field-label">Infant Age (0–2)</label>
                             <select name="infant_age[]" class="field-input" required>
                                 <option value="0">Under 1 year</option>
                                 <option value="1">1 year</option>
                                 <option value="2">2 years</option>
                             </select>
                         </div>`;
                }

                const html = `
                    <div class="applicant-box" id="applicant-box-${i}">
                        <div class="applicant-header">
                            <span><i class="bi bi-person-fill"></i> ${label}</span>
                        </div>
                        <div class="form-grid app-name-grid" style="margin-bottom: 12px; ${isSharjah ? 'display: none;' : ''}">
                            <div class="form-field">
                                <label class="field-label">First Name (Given Names)</label>
                                <input type="text" name="first_name[]" class="field-input app-first-name" placeholder="As in passport" ${isSharjah ? '' : 'required'}>
                            </div>
                            <div class="form-field">
                                <label class="field-label">Last Name (Surname)</label>
                                <input type="text" name="last_name[]" class="field-input app-last-name" placeholder="As in passport" ${isSharjah ? '' : 'required'}>
                            </div>
                        </div>
                        <div class="form-grid-3" style="margin-bottom: 12px;">
                            <div class="form-field">
                                <label class="field-label">Passport Number</label>
                                <input type="text" name="passport_number[]" class="field-input app-passport-number" placeholder="Enter passport number" required>
                            </div>
                            <div class="form-field">
                                <label class="field-label">Gender</label>
                                <select name="gender[]" class="field-input app-gender" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="form-field">
                                <label class="field-label">Date of Birth</label>
                                <input type="date" name="dob[]" class="field-input app-dob" required>
                            </div>
                        </div>
                        <div class="form-grid-3">
                            <div class="form-field">
                                <label class="field-label">Passport Copy</label>
                                <div class="file-input-wrapper">
                                    <input type="file" name="passport_copy[]" class="file-input-real" accept=".pdf,.jpg,.jpeg,.png" required onchange="updateFileName(this); if (typeof window.handlePassportUpload === 'function') window.handlePassportUpload(this, ${i});">
                                    <div class="file-input-custom">
                                        <span class="file-name">Upload passport (PDF/Image)...</span>
                                        <i class="bi bi-cloud-arrow-up-fill file-icon"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="form-field">
                                <label class="field-label">Photo</label>
                                <div class="file-input-wrapper">
                                    <input type="file" name="passport_photo[]" class="file-input-real" accept="image/*" required onchange="updateFileName(this)">
                                    <div class="file-input-custom">
                                        <span class="file-name">Upload photo (ID Photo)...</span>
                                        <i class="bi bi-person-bounding-box file-icon"></i>
                                    </div>
                                </div>
                                <a href="{{ asset('assets/uae-photo-guide.pdf') }}" target="_blank" class="photo-guide-link">
                                    <i class="bi bi-info-circle"></i> View photo requirements
                                </a>
                            </div>
                            <div class="form-field">
                                <label class="field-label">Airline Ticket (Return)</label>
                                <div class="file-input-wrapper">
                                    <input type="file" name="airline_ticket[]" class="file-input-real" accept=".pdf,.jpg,.jpeg,.png" required onchange="updateFileName(this)">
                                    <div class="file-input-custom">
                                        <span class="file-name">Upload ticket (PDF/Image)...</span>
                                        <i class="bi bi-airplane file-icon"></i>
                                    </div>
                                </div>
                            </div>
                            ${ageField}

                        </div>
                    </div>`;
                applicantsContainer.insertAdjacentHTML('beforeend', html);
            }
        }

        const HOTEL_BASE = {{ $hotelFee ?? 25 }};   // hotel fee for 1–2 visas
        const TICKET_RATE = {{ $ticketFee ?? 25 }}; // air-ticket assistance fee PER visa
        const SHARJAH_DEPOSIT = {{ $sharjahDeposit ?? 0 }}; // refundable deposit per applicant (admin-configured; 0 = none)

        // Hotel fee steps up with the number of visas (all applicants):
        // 1–2 → base (25), 3–4 → 50, 5–6 → 60, then +10 per extra pair of visas.
        function hotelFeeForVisas(n) {
            if (n <= 0) return 0;
            const tier = Math.ceil(n / 2);
            if (tier <= 1) return HOTEL_BASE;
            if (tier === 2) return 50;
            return 60 + (tier - 3) * 10;
        }

        function updatePrice() {
            const adults = getAdults();
            const children = getChildren();
            const infants = getInfants();
            const totalPersons = adults + children + infants;

            const selectedPackageId = visaPackageSelect.value;
            const selectedEntryType = visaEntryTypeSelect.value;
            const selectedDuration  = visaDurationSelect.value;

            let adultPrice = 0;
            let childPrice = 0;
            let infantPrice = 0;

            if (selectedPackageId && selectedEntryType && selectedDuration) {
                const pkg = window.visaPricingData.find(p => String(p.package_id) === String(selectedPackageId));
                if (pkg && pkg.prices) {
                    let pricesForCombo = pkg.prices.filter(p => 
                        p.entry_type === selectedEntryType && 
                        p.duration === selectedDuration
                    );

                    // Filter by nationality first, fallback to null/empty nationality
                    const selectedNat = document.getElementById('nationality').value;
                    const hasNatPrice = pricesForCombo.some(p => p.nationality && p.nationality.toLowerCase() === selectedNat.toLowerCase());
                    
                    if (hasNatPrice) {
                        pricesForCombo = pricesForCombo.filter(p => p.nationality && p.nationality.toLowerCase() === selectedNat.toLowerCase());
                    } else {
                        pricesForCombo = pricesForCombo.filter(p => !p.nationality);
                    }

                    const adultRow  = pricesForCombo.find(p => p.traveller_type.toLowerCase() === 'adult');
                    const childRow  = pricesForCombo.find(p => p.traveller_type.toLowerCase() === 'child');
                    const infantRow = pricesForCombo.find(p => p.traveller_type.toLowerCase() === 'infant');

                    adultPrice = adultRow ? parseFloat(adultRow.price) : 0;
                    childPrice = childRow ? parseFloat(childRow.price) : adultPrice;
                    infantPrice = infantRow ? parseFloat(infantRow.price) : 0;
                }
            }

            const baseVisaTotal = (adultPrice * adults) + (childPrice * children) + (infantPrice * infants);
            const ticketUnit = TICKET_RATE * totalPersons;
            const hotelUnit  = hotelFeeForVisas(totalPersons);
            const ticketCost = ticketCheckbox.checked ? ticketUnit : 0;
            const hotelCost  = hotelCheckbox.checked ? hotelUnit : 0;

            // Sharjah specific calculations
            const isSharjah = (selectedEmirateName && selectedEmirateName.toLowerCase() === 'sharjah');
            const depositUnit = isSharjah ? SHARJAH_DEPOSIT : 0;
            const depositCost = depositUnit * totalPersons;
            const refundCost = depositCost;

            const grandTotal = baseVisaTotal + hotelCost + ticketCost + depositCost;

            document.getElementById('hiddenPrice').value = grandTotal.toFixed(2);
            document.getElementById('countDisplay').textContent = totalPersons;
            document.getElementById('summaryBase').textContent = 'AED ' + baseVisaTotal.toFixed(2);

            // Reflect the live amounts on the add-on cards + summary rows.
            document.getElementById('hotelPriceLabel').textContent = hotelUnit.toFixed(0) + ' AED';
            document.getElementById('ticketPriceLabel').textContent = ticketUnit.toFixed(0) + ' AED';
            document.getElementById('summaryHotel').textContent = 'AED ' + hotelCost.toFixed(2);
            document.getElementById('summaryTicket').textContent = 'AED ' + ticketCost.toFixed(2);

            const hf = document.querySelector('input[name="hotel_booking_fee"]'); if (hf) hf.value = hotelUnit.toFixed(2);
            const tf = document.querySelector('input[name="ticket_booking_fee"]'); if (tf) tf.value = ticketUnit.toFixed(2);

            document.getElementById('hotelRow').style.display = hotelCheckbox.checked ? 'flex' : 'none';
            document.getElementById('ticketRow').style.display = ticketCheckbox.checked ? 'flex' : 'none';

            // Show/hide Sharjah deposit and refund rows
            const depRow = document.getElementById('depositRow');
            const refRow = document.getElementById('refundRow');
            if (depRow && refRow) {
                if (isSharjah) {
                    depRow.style.display = 'flex';
                    refRow.style.display = 'flex';
                    document.getElementById('summaryDeposit').textContent = 'AED ' + depositCost.toFixed(2);
                    document.getElementById('summaryRefund').textContent = 'AED ' + refundCost.toFixed(2);
                } else {
                    depRow.style.display = 'none';
                    refRow.style.display = 'none';
                }
            }

            // Show/hide Sharjah refund bank details
            const refundFieldsDiv = document.getElementById('sharjahRefundFields');
            if (refundFieldsDiv) {
                refundFieldsDiv.style.display = isSharjah ? 'block' : 'none';
                
                // Toggle required attributes for Sharjah Visa bank details
                const bankHolderInput = document.getElementById('bank_account_holder');
                const bankNameInput = document.getElementById('bank_name');
                const bankNumberInput = document.getElementById('bank_account_number');
                
                if (bankHolderInput) bankHolderInput.required = isSharjah;
                if (bankNameInput) bankNameInput.required = isSharjah;
                if (bankNumberInput) bankNumberInput.required = isSharjah;
            }

            document.getElementById('summaryTotal').textContent = 'AED ' + grandTotal.toFixed(2);
        }

        function populatePackages() {
            const matchedEmirate = selectedEmirateName
                ? window.emiratesData.find(e => e.name.toLowerCase() === selectedEmirateName.toLowerCase())
                : null;
            const emirateId = matchedEmirate ? matchedEmirate.id : null;

            visaPackageSelect.innerHTML = '<option value="">Select Package</option>';
            visaEntryTypeSelect.innerHTML = '<option value="">Select Type</option>';
            visaDurationSelect.innerHTML = '<option value="">Select Duration</option>';

            if (!emirateId) return;

            const filteredPkgs = window.visaPricingData.filter(p => String(p.emirates_id) === String(emirateId));
            filteredPkgs.forEach(pkg => {
                const opt = document.createElement('option');
                opt.value = pkg.package_id;
                opt.textContent = pkg.package_name;
                visaPackageSelect.appendChild(opt);
            });

            if (filteredPkgs.length === 1) {
                visaPackageSelect.value = filteredPkgs[0].package_id;
                populateEntryTypes();
            }
        }

        function populateEntryTypes() {
            const selectedPackageId = visaPackageSelect.value;
            visaEntryTypeSelect.innerHTML = '<option value="">Select Type</option>';
            visaDurationSelect.innerHTML = '<option value="">Select Duration</option>';

            if (!selectedPackageId) return;

            const pkg = window.visaPricingData.find(p => String(p.package_id) === String(selectedPackageId));
            if (!pkg || !pkg.prices) return;

            const entryTypes = [...new Set(pkg.prices.map(p => p.entry_type))];
            entryTypes.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t;
                opt.textContent = t;
                visaEntryTypeSelect.appendChild(opt);
            });

            if (entryTypes.length === 1) {
                visaEntryTypeSelect.value = entryTypes[0];
                populateDurations();
            }
        }

        function populateDurations() {
            const selectedPackageId = visaPackageSelect.value;
            const selectedEntryType = visaEntryTypeSelect.value;
            visaDurationSelect.innerHTML = '<option value="">Select Duration</option>';

            if (!selectedPackageId || !selectedEntryType) return;

            const pkg = window.visaPricingData.find(p => String(p.package_id) === String(selectedPackageId));
            if (!pkg || !pkg.prices) return;

            const durations = [...new Set(pkg.prices.filter(p => p.entry_type === selectedEntryType).map(p => p.duration))];
            durations.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d;
                opt.textContent = d;
                visaDurationSelect.appendChild(opt);
            });

            if (durations.length === 1) {
                visaDurationSelect.value = durations[0];
            }
            updatePrice();
        }

        function updateApplicantSectionVisibility() {
            const isSharjah = selectedEmirateName && selectedEmirateName.toLowerCase() === 'sharjah';
            const nameField = document.getElementById('applicantNameField');
            const nameInput = document.getElementById('applicantNameInput');
            const grid = document.getElementById('applicantGrid');
            const sectionLabel = document.getElementById('applicantSectionLabel');
            
            if (isSharjah) {
                if (nameField) nameField.style.display = 'block';
                if (nameInput) nameInput.required = true;
                if (grid) grid.className = 'form-grid-3';
                if (sectionLabel) sectionLabel.style.display = 'block';
            } else {
                if (nameField) nameField.style.display = 'none';
                if (nameInput) {
                    nameInput.required = false;
                    nameInput.value = '';
                }
                if (grid) grid.className = 'form-grid';
                if (sectionLabel) sectionLabel.style.display = 'none';
            }
        }

        function refreshForm() {
            generateApplicants(getAdults(), getChildren(), getInfants());
            updateApplicantSectionVisibility();
            updatePrice();
        }

        // Initialize selectors and price grid
        populatePackages();
        refreshForm();

        visaCountSelect.addEventListener('change', refreshForm);
        childrenCountInput.addEventListener('change', refreshForm);
        infantsCountInput.addEventListener('change', refreshForm);

        visaPackageSelect.addEventListener('change', populateEntryTypes);
        visaEntryTypeSelect.addEventListener('change', populateDurations);
        visaDurationSelect.addEventListener('change', updatePrice);

        if (nationalitySelect) {
            nationalitySelect.on('change', function() {
                updatePrice();
            });
        }

        // Reveal (and require) one applicant's name fields — used when OCR cannot
        // read a passport, so the customer supplies the missing name instead of a
        // placeholder being saved. Sharjah hides these by default; other emirates
        // already show them, so this is a no-op there.
        window.revealNameFields = function (index, message) {
            const card = document.getElementById(`applicant-box-${index}`);
            if (!card) return;
            const grid = card.querySelector('.app-name-grid');
            if (grid) grid.style.display = '';
            const fn = card.querySelector('.app-first-name');
            const ln = card.querySelector('.app-last-name');
            if (fn) fn.required = true;
            if (ln) ln.required = true;
            if (message && grid && !card.querySelector('.app-name-note')) {
                const note = document.createElement('div');
                note.className = 'app-name-note';
                note.style.cssText = 'color:#FFB020;font-size:12.5px;margin:2px 0 10px;display:flex;align-items:center;gap:6px;';
                note.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i><span>' + message + '</span>';
                grid.parentNode.insertBefore(note, grid);
            }
        };

        // Handle automated OCR scan on individual passport copy file uploads
        window.handlePassportUpload = function(input, index) {
            if (!input.files || input.files.length === 0) return;
            const file = input.files[0];
            
            const card = document.getElementById(`applicant-box-${index}`);
            if (!card) return;
            
            const firstNameInput = card.querySelector('.app-first-name');
            const lastNameInput = card.querySelector('.app-last-name');
            const passportNumInput = card.querySelector('.app-passport-number');
            const dobInput = card.querySelector('.app-dob');
            const genderInput = card.querySelector('.app-gender');
            
            const headerText = card.querySelector('.applicant-header span');
            const originalHTML = headerText.innerHTML;
            headerText.innerHTML = `<span style="color: #FFD700;"><i class="bi bi-hourglass-split"></i> Scanning passport...</span>`;
            
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
                headerText.innerHTML = originalHTML;
                if (data.success && data.fields) {
                    const f = data.fields;
                    
                    if (firstNameInput && (f.given_names || f.full_name)) {
                        firstNameInput.value = f.given_names || f.full_name;
                    }
                    if (lastNameInput && f.surname) {
                        lastNameInput.value = f.surname;
                    }
                    if (passportNumInput && f.passport_number) {
                        passportNumInput.value = f.passport_number;
                    }
                    if (dobInput && f.date_of_birth) {
                        dobInput.value = f.date_of_birth;
                    }
                    if (genderInput && f.sex) {
                        const sex = String(f.sex).toLowerCase();
                        if (sex.startsWith('m')) {
                            genderInput.value = 'Male';
                        } else if (sex.startsWith('f')) {
                            genderInput.value = 'Female';
                        }
                    }
                    
                    // If it is the first applicant, prefill nationality too
                    if (index === 0 && f.nationality) {
                        selectNationality(f);
                    }

                    // OCR ran but could not read a name — let the customer type it.
                    if (firstNameInput && !firstNameInput.value.trim()) {
                        revealNameFields(index, "We couldn't read the name on this passport — please enter it below.");
                    }
                } else {
                    // OCR returned no usable data.
                    revealNameFields(index, "We couldn't read this passport — please enter the traveller's name below.");
                }
            })
            .catch(err => {
                headerText.innerHTML = originalHTML;
                console.error('Passport OCR error:', err);
                revealNameFields(index, "Passport scan is unavailable — please enter the traveller's name below.");
            });
        };

        document.addEventListener('emirateChanged', function(e) {
            selectedEmirateName = e.detail;

            const badge = document.getElementById('emirateActiveBadge');
            if (badge) {
                const textEl = badge.querySelector('.badge-text');
                const iconEl = badge.querySelector('.badge-icon');
                if (textEl) textEl.textContent = selectedEmirateName + ' Processing';
                if (iconEl) iconEl.innerHTML = EMIRATE_FLAGS[selectedEmirateName] || '';
                badge.style.display = 'inline-flex';
            }

            populatePackages();
            refreshForm();
        });

        // Ask the visitor to choose an emirate as soon as the page settles.
        setTimeout(function () {
            if (!selectedEmirateName && typeof window.showEmirateSelector === 'function') {
                window.showEmirateSelector();
            }
        }, 500);

        // Browsers restore this page from the back/forward cache (bfcache) when the
        // visitor navigates away and hits Back — no fresh DOMContentLoaded fires, so
        // without this the popup and badge would stay stuck on the old selection.
        // Force a clean slate and re-prompt every time that happens.
        window.addEventListener('pageshow', function (event) {
            if (!event.persisted) return;

            selectedEmirateName = null;
            const hiddenInput = document.getElementById('selectedEmirate');
            if (hiddenInput) hiddenInput.value = '';

            const badge = document.getElementById('emirateActiveBadge');
            if (badge) badge.style.display = 'none';

            populatePackages();
            updatePrice();

            if (typeof window.showEmirateSelector === 'function') {
                window.showEmirateSelector();
            }
        });

        hotelCheckbox.addEventListener('change', function() {
            document.getElementById('addonHotel').classList.toggle('active', this.checked);
            updatePrice();
        });
        ticketCheckbox.addEventListener('change', function() {
            document.getElementById('addonTicket').classList.toggle('active', this.checked);
            updatePrice();
        });

        // Phone with country dropdown
        const phoneEl = document.getElementById('phoneInput');
        const iti = window.intlTelInput(phoneEl, {
            initialCountry: 'ae',
            preferredCountries: ['ae', 'sa', 'in', 'pk', 'gb', 'us'],
            separateDialCode: true,
            utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.5.3/build/js/utils.js',
        });

        // Form Submit
        const form = document.getElementById('visaForm');
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!selectedEmirateName) {
                if (typeof window.showEmirateSelector === 'function') window.showEmirateSelector();
                alert('Please select which Emirate visa you need before continuing.');
                return;
            }

            // Sharjah hides the per-applicant name fields and fills them from passport
            // OCR. If OCR did not populate a traveller's name, reveal the fields and
            // stop here so the customer can enter it — never submit a blank name that
            // the server would otherwise reject (applicant #0 is covered by the
            // required universal Full Name field).
            if (selectedEmirateName.toLowerCase() === 'sharjah') {
                const total = getAdults() + getChildren() + getInfants();
                const missing = [];
                for (let i = 1; i < total; i++) {
                    const card = document.getElementById(`applicant-box-${i}`);
                    const fn = card ? card.querySelector('.app-first-name') : null;
                    if (fn && !fn.value.trim()) {
                        revealNameFields(i, "We couldn't read this passport — please enter the traveller's name.");
                        missing.push(i);
                    }
                }
                if (missing.length) {
                    alert('Please enter the traveller name for applicant #' + missing.map(x => x + 1).join(', #') + '.');
                    const firstCard = document.getElementById(`applicant-box-${missing[0]}`);
                    if (firstCard) firstCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }
            }

            const btn = document.getElementById('submitBtn');
            const originalText = btn.innerHTML;

            if (iti && typeof iti.getNumber === 'function') {
                const fullNumber = iti.getNumber();
                if (fullNumber) {
                    phoneEl.value = fullNumber;
                } else {
                    const dial = iti.getSelectedCountryData().dialCode;
                    if (dial && phoneEl.value) {
                        phoneEl.value = '+' + dial + phoneEl.value.replace(/^\+?/, '').replace(/\s+/g, '');
                    }
                }
            }

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> PROCESSING...';

            const formData = new FormData(this);
            formData.set('visa_count', String(getAdults()));
            formData.set('children_count', String(getChildren()));
            formData.set('infants_count', String(getInfants()));
            formData.set('selected_emirate', selectedEmirateName);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.checkout_url) {
                        window.location.href = data.checkout_url;
                    } else {
                        throw new Error(data.message || 'Submission failed');
                    }
                })
                .catch(err => {
                    alert(err.message || 'Error occurred');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
        });
    });
</script>

<style>
    .pp-scan { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;
        background:rgba(255,255,255,0.025); border:1px solid rgba(255,255,255,0.08);
        border-left:3px solid #FFD23F; border-radius:14px; padding:16px 20px; margin-bottom:14px; }
    .pp-scan-info { display:flex; align-items:center; gap:14px; }
    .pp-scan-badge { width:44px; height:44px; flex-shrink:0; border-radius:12px; display:flex; align-items:center; justify-content:center;
        background:rgba(255,215,0,0.10); border:1px solid rgba(255,215,0,0.28); }
    .pp-scan-badge i { font-size:20px; color:#FFD23F; }
    .pp-scan-info strong { display:block; color:#fff; font-size:15px; font-weight:600; letter-spacing:.2px; }
    .pp-scan-info span { color:#999; font-size:13px; }
    .pp-scan-actions { display:flex; gap:10px; }
    .pp-scan-btn { display:inline-flex; align-items:center; gap:7px; font-weight:600; font-size:13.5px;
        padding:10px 16px; border-radius:10px; cursor:pointer; white-space:nowrap; border:1px solid transparent; transition:all .15s ease; }
    .pp-scan-btn--primary { background:linear-gradient(135deg,#FFD700,#D4AF37); color:#000; }
    .pp-scan-btn--primary:hover { filter:brightness(1.05); transform:translateY(-1px); box-shadow:0 6px 16px rgba(255,215,0,0.18); }
    .pp-scan-cam { background:transparent; color:#FFD23F; border-color:rgba(255,215,0,0.4); }
    .pp-scan-cam:hover { background:rgba(255,215,0,0.08); transform:translateY(-1px); }
    .pp-cam-modal { position:fixed; inset:0; background:rgba(0,0,0,0.9); backdrop-filter:blur(4px); z-index:99999; display:flex; align-items:center; justify-content:center; padding:16px; }
    .pp-cam-box { background:#0d0d0f; border:1px solid rgba(255,255,255,0.1); border-radius:18px; padding:18px; width:560px; max-width:94vw; box-shadow:0 30px 80px rgba(0,0,0,0.6); }
    .pp-cam-title { display:flex; align-items:center; gap:8px; color:#fff; font-weight:600; font-size:15px; margin:0 0 12px; }
    .pp-cam-title i { color:#FFD23F; }
    .pp-cam-box video { width:100%; border-radius:12px; background:#000; max-height:60vh; display:block; border:1px solid rgba(255,255,255,0.08); }
    .pp-cam-hint { text-align:center; color:#888; font-size:12.5px; margin:10px 0 0; }
    .pp-cam-actions { display:flex; gap:10px; justify-content:center; margin-top:14px; }
    .pp-cam-close { background:transparent; border:1px solid #444; color:#ccc; border-radius:10px; padding:11px 20px; cursor:pointer; font-weight:600; }
    .pp-cam-close:hover { border-color:#888; color:#fff; }
    .pp-scan-result { border:1px solid rgba(255,215,0,0.2); border-radius:10px; padding:12px 16px; margin-bottom:16px;
        background:rgba(255,255,255,0.03); font-size:13.5px; color:#ddd; }
    .pp-scan-result .ok { color:#4CAF50; font-weight:600; }
    .pp-scan-result .warn { color:#e0a100; font-weight:600; }
    .pp-scan-result .err { color:#dc3545; font-weight:600; }
    .pp-scan-result .det { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:6px 18px; margin-top:8px; }
    .pp-scan-result .det b { color:#fff; }
</style>
<script>
(function () {
    const fileInput = document.getElementById('ppScanFile');
    const label = document.getElementById('ppScanLabel');
    const result = document.getElementById('ppScanResult');
    if (!fileInput) return;

    // Common nationality adjectives → country names used in the dropdown.
    const NAT_MAP = {
        'british':'United Kingdom','english':'United Kingdom','indian':'India','american':'United States',
        'pakistani':'Pakistan','emirati':'United Arab Emirates','filipino':'Philippines','egyptian':'Egypt',
        'saudi':'Saudi Arabia','bangladeshi':'Bangladesh','sri lankan':'Sri Lanka','nepalese':'Nepal',
        'chinese':'China','german':'Germany','french':'France','italian':'Italy','spanish':'Spain',
        'russian':'Russia','canadian':'Canada','australian':'Australia','nigerian':'Nigeria','kenyan':'Kenya',
        'jordanian':'Jordan','lebanese':'Lebanon','syrian':'Syria','iraqi':'Iraq','iranian':'Iran',
        'turkish':'Turkey','south african':'South Africa','indonesian':'Indonesia','malaysian':'Malaysia'
    };

    function selectNationality(fields) {
        const sel = document.getElementById('nationality');
        if (!sel) return null;
        const cands = [fields.issuing_country, fields.nationality].filter(Boolean);
        // Skip the empty "Select Nationality" placeholder. The fuzzy test below
        // includes `c.includes(option.value)`, and every string contains "" —
        // so the placeholder would always win, blanking the field and making a
        // real country (e.g. "REPUBLIC OF INDIA") look unmatched.
        const opts = Array.from(sel.options).filter(o => o.value && o.value.trim() !== '');
        for (let raw of cands) {
            let c = String(raw).trim().toLowerCase();
            if (NAT_MAP[c]) c = NAT_MAP[c].toLowerCase();
            let opt = opts.find(o => o.value.toLowerCase() === c);
            if (!opt) opt = opts.find(o => o.value.toLowerCase().includes(c) || (c.length > 3 && c.includes(o.value.toLowerCase())));
            if (opt) {
                sel.value = opt.value;
                sel.dispatchEvent(new Event('change'));
                if (window.nationalityTomSelect) {
                    window.nationalityTomSelect.setValue(opt.value);
                }
                return opt.value;
            }
        }
        return null;
    }

    function runScan(file) {
        if (!file) return;
        label.textContent = 'Scanning…';
        result.style.display = 'block';
        result.innerHTML = '<span class="warn"><i class="bi bi-hourglass-split"></i> Reading passport…</span>';

        const token = document.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}';
        const fd = new FormData();
        fd.append('passport', file);
        fd.append('_token', token);

        fetch("{{ route('passport.extract') }}", {
            method: 'POST', headers: { 'Accept':'application/json', 'X-CSRF-TOKEN': token }, body: fd
        })
        .then(r => r.json().then(d => ({ ok: r.ok, d })))
        .then(({ ok, d }) => {
            label.textContent = 'Upload';
            if (ok && d.success && d.fields) {
                const f = d.fields;
                const picked = selectNationality(f);
                
                // Auto-fill Applicant #1
                const firstCard = document.getElementById('applicant-box-0');
                if (firstCard) {
                    const fn = firstCard.querySelector('.app-first-name');
                    const ln = firstCard.querySelector('.app-last-name');
                    const pn = firstCard.querySelector('.app-passport-number');
                    const dob = firstCard.querySelector('.app-dob');
                    const gen = firstCard.querySelector('.app-gender');
                    
                    if (fn && (f.given_names || f.full_name)) fn.value = f.given_names || f.full_name;
                    if (ln && f.surname) ln.value = f.surname;
                    if (pn && f.passport_number) pn.value = f.passport_number;
                    if (dob && f.date_of_birth) dob.value = f.date_of_birth;
                    if (gen && f.sex) {
                        const sex = String(f.sex).toLowerCase();
                        if (sex.startsWith('m')) gen.value = 'Male';
                        else if (sex.startsWith('f')) gen.value = 'Female';
                    }
                }
                
                const det = [
                    ['Name', f.full_name || [f.given_names, f.surname].filter(Boolean).join(' ')],
                    ['Passport No', f.passport_number], ['Nationality', f.nationality],
                    ['Date of Birth', f.date_of_birth], ['Expiry', f.date_of_expiry]
                ].filter(x => x[1]).map(x => `<div><b>${x[0]}:</b> ${x[1]}</div>`).join('');
                result.innerHTML =
                    `<span class="ok"><i class="bi bi-check-circle"></i> Passport read.</span> ` +
                    (picked ? `Nationality set to <b>${picked}</b>.` : `Couldn't match nationality — please select it manually.`) +
                    (det ? `<div class="det">${det}</div>` : '') +
                    `<div style="margin-top:6px;color:#888;">Please verify the details before submitting.</div>`;
            } else {
                result.innerHTML = `<span class="err"><i class="bi bi-x-circle"></i> ${d.message || 'Could not read the passport. Try a clearer photo.'}</span>`;
            }
        })
        .catch(() => {
            label.textContent = 'Upload';
            result.innerHTML = '<span class="err">Network error. Please try again.</span>';
        });
    }

    fileInput.addEventListener('change', () => runScan(fileInput.files[0]));

    // ── Camera capture (getUserMedia) ──
    const camBtn = document.getElementById('ppCamBtn');
    const modal = document.getElementById('ppCamModal');
    const video = document.getElementById('ppCamVideo');
    const canvas = document.getElementById('ppCamCanvas');
    const captureBtn = document.getElementById('ppCamCapture');
    const closeBtn = document.getElementById('ppCamClose');
    let stream = null;

    function stopCam() {
        if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
        if (modal) modal.style.display = 'none';
    }
    if (camBtn) {
        camBtn.addEventListener('click', function () {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                result.style.display = 'block';
                result.innerHTML = '<span class="err">Camera not supported on this browser — please use Upload.</span>';
                return;
            }
            // Prefer the rear camera (phones) but fall back to any camera (desktop webcams).
            navigator.mediaDevices.getUserMedia({ video: { facingMode: { ideal: 'environment' } }, audio: false })
                .catch(function () { return navigator.mediaDevices.getUserMedia({ video: true, audio: false }); })
                .then(function (s) { stream = s; video.srcObject = s; modal.style.display = 'flex'; })
                .catch(function (err) {
                    result.style.display = 'block';
                    const denied = err && (err.name === 'NotAllowedError' || err.name === 'SecurityError');
                    result.innerHTML = '<span class="err">' +
                        (denied ? 'Camera permission was blocked. Allow camera access in your browser, or use Upload.'
                                : 'No camera found — please use Upload instead.') +
                        '</span>';
                });
        });
        captureBtn.addEventListener('click', function () {
            const w = video.videoWidth, h = video.videoHeight;
            if (!w || !h) return;
            canvas.width = w; canvas.height = h;
            canvas.getContext('2d').drawImage(video, 0, 0, w, h);
            canvas.toBlob(function (blob) {
                stopCam();
                if (blob) runScan(new File([blob], 'passport-photo.jpg', { type: 'image/jpeg' }));
            }, 'image/jpeg', 0.92);
        });
        closeBtn.addEventListener('click', stopCam);
    }

})();
</script>

@include('partials.emirate_selector_modal')
@include('footer')
