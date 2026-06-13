@include('header')

<!-- intl-tel-input (phone with country dropdown) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.5.3/build/css/intlTelInput.css">
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.5.3/build/js/intlTelInput.min.js"></script>

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
        font-size: 13px;
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
</style>

<div class="visa-page">
    <div class="visa-wrapper">
        <div class="visa-header">
            <h1 class="visa-title">UAE Visa</h1>
            <p class="visa-subtitle">Premium Processing</p>
        </div>

        <div class="visa-main">
            <!-- FORM -->
            <div class="card">
                <div class="card-title">
                    <span><i class="bi bi-file-earmark-text-fill"></i> Application Details</span>
                </div>

                <form id="visaForm" method="POST" action="{{ route('uaev.submit') }}" enctype="multipart/form-data">
                    @csrf

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

                    {{-- ROW 1: Nationality, Duration --}}
                    <div class="form-grid-3" style="grid-template-columns: 1fr 1fr;">
                        <div class="form-field">
                            <label class="field-label">Nationality</label>
                            <select id="nationality" name="nationality" class="field-input" required>
                                <option value="">Select Nationality</option>
                            </select>
                        </div>
                        <div class="form-field">
                            <label class="field-label">Visa Duration</label>
                            <select class="field-input" id="visaDuration" name="visaDuration" required>
                                <option value="">Select Duration</option>
                                @if(isset($visaData) && $visaData->count() > 0)
                                    @foreach($visaData as $v)
                                        <option value="{{ $v->UAEVisaDuration }}" data-price="{{ $v->UAEVPrice }}">
                                            {{ $v->UAEVisaDuration }} — {{ number_format($v->UAEVPrice, 0) }} AED
                                        </option>
                                    @endforeach
                                @else
                                    <option value="30 DAYS VISA" data-price="300">30 DAYS VISA — 300 AED</option>
                                    <option value="60 DAYS VISA" data-price="600">60 DAYS VISA — 600 AED</option>
                                @endif
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

                    {{-- ROW 3: Email, WhatsApp --}}
                    <div class="form-grid" style="margin-top: 12px;">
                        <div class="form-field">
                            <label class="field-label">Email Address</label>
                            <input type="email" class="field-input" name="email" placeholder="name@example.com" required>
                        </div>
                        <div class="form-field">
                            <label class="field-label">WhatsApp Number</label>
                            <input type="tel" id="phoneInput" class="field-input" name="phone" placeholder="50 000 0000" required>
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
                            <span class="checkbox-text">I agree not to overstay. If so, I agree to pay the overstay charges per day and also the absconding fee.</span>
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
        fetch('https://restcountries.com/v3.1/all?fields=name')
            .then(res => res.json())
            .then(data => {
                data.sort((a, b) => a.name.common.localeCompare(b.name.common));
                data.forEach(country => {
                    const optionHtml = `<option value="${country.name.common}">${country.name.common}</option>`;
                    countrySelects.forEach(select => select.insertAdjacentHTML('beforeend', optionHtml));
                });
            })
            .catch(err => console.error(err));

        const visaCountSelect = document.getElementById('visaCount');
        const childrenCountInput = document.getElementById('visaChildren');
        const infantsCountInput = document.getElementById('visaInfants');
        const applicantsContainer = document.getElementById('applicantsContainer');
        const durationSelect = document.getElementById('visaDuration');
        const hotelCheckbox = document.getElementById('hotelCheckbox');
        const ticketCheckbox = document.getElementById('ticketCheckbox');

        function getAdults() { return parseInt(visaCountSelect.value, 10) || 1; }
        function getChildren() { return parseInt(childrenCountInput.value, 10) || 0; }
        function getInfants() { return parseInt(infantsCountInput.value, 10) || 0; }

        function generateApplicants(adults, children, infants) {
            applicantsContainer.innerHTML = '';
            const total = adults + children + infants;
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
                    <div class="applicant-box">
                        <div class="applicant-header">
                            <span><i class="bi bi-person-fill"></i> ${label}</span>
                        </div>
                        <div class="form-grid-3">
                            <div class="form-field">
                                <label class="field-label">Passport Copy</label>
                                <div class="file-input-wrapper">
                                    <input type="file" name="passport_copy[]" class="file-input-real" accept=".pdf,.jpg,.jpeg,.png" required onchange="updateFileName(this)">
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
                            <div class="form-field">
                                <label class="field-label">Additional Documents</label>
                                <div class="file-input-wrapper">
                                    <input type="file" name="supporting_document[]" class="file-input-real" accept=".pdf,.jpg,.jpeg,.png" onchange="updateFileName(this)">
                                    <div class="file-input-custom">
                                        <span class="file-name">Upload documents (if any)...</span>
                                        <i class="bi bi-paperclip file-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;
                applicantsContainer.insertAdjacentHTML('beforeend', html);
            }
        }

        const HOTEL_FEE = {{ $hotelFee ?? 25 }};
        const TICKET_FEE = {{ $ticketFee ?? 25 }};

        function updatePrice() {
            const adults = getAdults();
            const children = getChildren();
            const infants = getInfants();
            const totalPersons = adults + children + infants;
            const option = durationSelect.options[durationSelect.selectedIndex];
            const unitPrice = parseFloat(option.getAttribute('data-price')) || 0;

            const visaTotal = unitPrice * totalPersons;
            const hotelCost = hotelCheckbox.checked ? HOTEL_FEE : 0;
            const ticketCost = ticketCheckbox.checked ? TICKET_FEE : 0;
            const grandTotal = visaTotal + hotelCost + ticketCost;

            document.getElementById('hiddenPrice').value = grandTotal.toFixed(2);
            document.getElementById('countDisplay').textContent = totalPersons;
            document.getElementById('summaryBase').textContent = 'AED ' + visaTotal.toFixed(2);

            document.getElementById('hotelRow').style.display = hotelCheckbox.checked ? 'flex' : 'none';
            document.getElementById('ticketRow').style.display = ticketCheckbox.checked ? 'flex' : 'none';

            document.getElementById('summaryTotal').textContent = 'AED ' + grandTotal.toFixed(2);
        }

        function refreshForm() {
            generateApplicants(getAdults(), getChildren(), getInfants());
            updatePrice();
        }

        refreshForm();

        visaCountSelect.addEventListener('change', refreshForm);
        childrenCountInput.addEventListener('change', refreshForm);
        infantsCountInput.addEventListener('change', refreshForm);
        durationSelect.addEventListener('change', updatePrice);

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
        const opts = Array.from(sel.options);
        for (let raw of cands) {
            let c = String(raw).trim().toLowerCase();
            if (NAT_MAP[c]) c = NAT_MAP[c].toLowerCase();
            let opt = opts.find(o => o.value.toLowerCase() === c);
            if (!opt) opt = opts.find(o => o.value.toLowerCase().includes(c) || (c.length > 3 && c.includes(o.value.toLowerCase())));
            if (opt) { sel.value = opt.value; sel.dispatchEvent(new Event('change')); return opt.value; }
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

@include('footer')
