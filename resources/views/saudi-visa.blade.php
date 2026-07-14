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
        --c-card-bg: #0b0b0b;
        --c-input-bg: #111;
        --c-input-border: #222;
        --c-text-muted: #888;
        --c-text-light: #eee;
        
        --gt-surface: #0a0a0a;
        --gt-text: #fff;
        --gt-text-secondary: #ccc;
        --gt-text-muted: #888;
        --gt-border-strong: rgba(255, 215, 0, 0.25);
        --gt-border-soft: rgba(255, 215, 0, 0.12);
        --gt-gold: #FFD700;
        --gt-gold-2: #FFA500;
        --gt-gold-soft: rgba(255, 215, 0, 0.06);
    }

    html[data-theme="light"] {
        --c-dark-bg: #f8f9fa;
        --c-card-bg: #ffffff;
        --c-input-bg: #ffffff;
        --c-input-border: #dcdcdc;
        --c-text-muted: #666;
        --c-text-light: #222;
        
        --gt-surface: #ffffff;
        --gt-text: #1a1a1a;
        --gt-text-secondary: #444;
        --gt-text-muted: #666;
        --gt-border-strong: rgba(212, 175, 55, 0.4);
        --gt-border-soft: rgba(212, 175, 55, 0.2);
        --gt-gold: #D4AF37;
        --gt-gold-2: #C5A028;
        --gt-gold-soft: rgba(212, 175, 55, 0.08);
    }

    body { margin: 0; background-color: var(--c-dark-bg); color: var(--gt-text); }

    .visa-page {
        background: linear-gradient(180deg, var(--c-dark-bg) 0%, var(--c-dark-bg) 100%);
        min-height: 100vh;
        padding-top: 170px;
        padding-bottom: 50px;
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
        margin-bottom: 20px;
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
        grid-template-columns: 1fr 360px;
        gap: 24px;
        align-items: start;
    }

    @media (max-width: 991.98px) {
        .visa-main {
            grid-template-columns: 1fr;
        }
        .visa-page {
            padding-top: 120px;
        }
    }

    .card {
        background: var(--c-card-bg);
        border: 1px solid var(--gt-border-soft);
        border-radius: 14px;
        padding: 24px;
        position: relative;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
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
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 3px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        opacity: 0.9;
        border-bottom: 1px solid var(--gt-border-soft);
        padding-bottom: 10px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px 20px;
    }

    @media (max-width: 575.98px) {
        .form-grid {
            grid-template-columns: 1fr;
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
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        margin-bottom: 8px;
    }

    .field-input {
        width: 100%;
        height: 48px;
        background: var(--c-input-bg);
        border: 1px solid var(--c-input-border);
        border-radius: 10px;
        padding: 0 16px;
        color: var(--gt-text);
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .field-input:hover {
        border-color: var(--gt-border-strong);
    }

    .field-input:focus {
        outline: none;
        border-color: var(--c-gold);
        background: var(--c-input-bg);
        box-shadow: 0 0 15px rgba(255, 215, 0, 0.08);
    }

    select.field-input {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='16' height='16' fill='%23FFD700'%3E%3Cpath d='M11.9997 13.1714L16.9495 8.22168L18.3637 9.63589L11.9997 15.9999L5.63574 9.63589L7.04996 8.22168L11.9997 13.1714Z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: calc(100% - 20px) center;
        cursor: pointer;
    }

    /* intl-tel-input overrides */
    .iti { width: 100% !important; }
    .iti__selected-flag {
        background: var(--c-input-bg) !important;
        border-right: 1px solid var(--c-input-border) !important;
        border-radius: 10px 0 0 10px !important;
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

    /* Document upload styling */
    .file-upload-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-top: 15px;
    }

    @media (max-width: 575.98px) {
        .file-upload-container {
            grid-template-columns: 1fr;
        }
    }

    .file-box {
        position: relative;
        border: 2px dashed var(--gt-border-strong);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.25s ease;
        background: var(--gt-gold-soft);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 120px;
    }

    .file-box:hover {
        border-color: var(--c-gold);
        background: rgba(255, 215, 0, 0.1);
    }

    .file-box input[type="file"] {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 10;
    }

    .file-icon {
        font-size: 24px;
        color: var(--c-gold);
        margin-bottom: 8px;
    }

    .file-text {
        font-size: 13px;
        font-weight: 600;
        color: var(--gt-text);
    }

    .file-sub {
        font-size: 11px;
        color: var(--c-text-muted);
        margin-top: 3px;
    }

    .file-name-span {
        display: block;
        margin-top: 8px;
        font-size: 12px;
        font-weight: 500;
        color: var(--c-gold);
        word-break: break-all;
    }

    /* Checkout summary card */
    .checkout-card {
        background: var(--c-card-bg);
        border: 1px solid var(--gt-border-strong);
        border-radius: 14px;
        padding: 24px;
        position: sticky;
        top: 170px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .sum-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        padding: 10px 0;
        border-bottom: 1px solid var(--gt-border-soft);
        font-size: 14px;
    }

    .sum-row.total {
        border-bottom: none;
        padding-top: 15px;
        margin-top: 10px;
    }

    .sum-label {
        color: var(--c-text-muted);
        font-weight: 500;
    }

    .sum-val {
        color: var(--gt-text);
        font-weight: 700;
    }

    .sum-total-val {
        color: var(--c-gold);
        font-size: 24px;
        font-weight: 800;
    }

    .btn-submit {
        width: 100%;
        height: 52px;
        background: var(--c-gold-gradient);
        border: none;
        border-radius: 12px;
        color: #000;
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        cursor: pointer;
        transition: all 0.25s ease;
        margin-top: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 8px 25px rgba(255, 215, 0, 0.2);
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(255, 215, 0, 0.35);
    }

    .btn-submit:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* TomSelect Theme overrides */
    .ts-wrapper.field-input {
        padding: 0 !important;
        background: var(--c-input-bg) !important;
        border: 1px solid var(--c-input-border) !important;
        border-radius: 10px !important;
    }
    .ts-control {
        background: transparent !important;
        border: none !important;
        color: var(--gt-text) !important;
        padding: 0 16px !important;
        height: 46px !important;
        display: flex !important;
        align-items: center !important;
        font-family: 'Outfit', sans-serif !important;
        font-size: 15px !important;
    }
    .ts-dropdown {
        background: var(--c-card-bg) !important;
        border: 1px solid var(--c-input-border) !important;
        color: var(--gt-text) !important;
        border-radius: 10px !important;
        box-shadow: 0 20px 50px rgba(0,0,0,0.6) !important;
    }
    .ts-dropdown .option {
        padding: 10px 16px !important;
        color: var(--gt-text-secondary) !important;
    }
    .ts-dropdown .active {
        background: var(--gt-gold-soft) !important;
        color: var(--c-gold) !important;
    }

    /* Scan Result Card */
    .scan-result-card {
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 13px;
        margin-top: 15px;
        display: none;
        line-height: 1.5;
    }
    .scan-result-card.success {
        background: rgba(34, 197, 94, 0.08);
        border: 1px solid rgba(34, 197, 94, 0.25);
        color: #2cde64;
    }
    .scan-result-card.scanning {
        background: rgba(255, 215, 0, 0.08);
        border: 1px solid rgba(255, 215, 0, 0.25);
        color: #FFD700;
    }
    .scan-result-card.error {
        background: rgba(239, 68, 68, 0.08);
        border: 1px solid rgba(239, 68, 68, 0.25);
        color: #ff4a4a;
    }

    .visa-type-meta {
        background: var(--gt-gold-soft);
        border: 1px dashed var(--gt-border-soft);
        border-radius: 10px;
        padding: 14px;
        margin-top: 15px;
        font-size: 13px;
        line-height: 1.6;
        display: none;
    }
    .visa-type-meta h4 {
        margin: 0 0 8px;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--c-gold);
    }
    .visa-type-meta ul {
        margin: 8px 0 0;
        padding-left: 20px;
    }
</style>

<div class="visa-page">
    <div class="visa-wrapper">
        <div class="visa-header">
            <h1 class="visa-title">Saudi Visa</h1>
            <span class="visa-subtitle">Standalone Application</span>
        </div>

        <div class="visa-main">
            {{-- Form container --}}
            <form id="saudiVisaForm" action="{{ route('saudi-visa.submit') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="card mb-4">
                    <div class="card-title">
                        <span><i class="bi bi-person-fill text-warning me-2"></i>Applicant Details</span>
                        <button type="button" class="btn btn-sm btn-outline-warning" id="triggerScanBtn" style="font-size: 11px; padding: 2px 10px; border-radius: 6px;">
                            <i class="bi bi-camera me-1"></i> Scan Passport
                        </button>
                    </div>

                    <input type="file" id="ocrScannerInput" accept="image/*,application/pdf" style="display: none;">

                    <div class="scan-result-card" id="scanStatusCard"></div>

                    <div class="form-grid">
                        <div class="form-field">
                            <label class="field-label">First Name (Given Names) *</label>
                            <input type="text" name="first_name" id="first_name" class="field-input app-first-name" required placeholder="John">
                        </div>
                        <div class="form-field">
                            <label class="field-label">Last Name (Surname) *</label>
                            <input type="text" name="last_name" id="last_name" class="field-input app-last-name" required placeholder="Doe">
                        </div>

                        <div class="form-field">
                            <label class="field-label">Email Address *</label>
                            <input type="email" name="email" id="email" class="field-input" required placeholder="john.doe@example.com">
                        </div>
                        <div class="form-field">
                            <label class="field-label">WhatsApp Number *</label>
                            <input type="tel" name="phone" id="phone" class="field-input" required>
                        </div>

                        <div class="form-field">
                            <label class="field-label">Nationality *</label>
                            <select name="nationality" id="nationality" class="field-input" required>
                                <option value="">Select Nationality</option>
                                @foreach(\App\Support\CountryCodes::all() as $c)
                                    <option value="{{ $c['name'] }}">{{ $c['name'] }}</option>
                                @endforeach
                            </select>
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
                        <div id="metaVisaDocsContainer" style="margin-top: 10px;">
                            <strong>Required Documents:</strong>
                            <ul id="metaVisaDocsList"></ul>
                        </div>
                    </div>

                    <div class="form-grid mt-3">
                        <div class="form-field">
                            <label class="field-label">Passport Number *</label>
                            <input type="text" name="passport_number" id="passport_number" class="field-input app-passport-number" required placeholder="L1234567">
                        </div>
                        <div class="form-field">
                            <label class="field-label">Passport Expiry *</label>
                            <input type="date" name="passport_expiry" id="passport_expiry" class="field-input" required>
                        </div>

                        <div class="form-field">
                            <label class="field-label">Date of Birth *</label>
                            <input type="date" name="dob" id="dob" class="field-input app-dob" required>
                        </div>
                        <div class="form-field">
                            <label class="field-label">Gender *</label>
                            <select name="gender" id="gender" class="field-input app-gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>

                    {{-- Upload boxes --}}
                    <div class="file-upload-container">
                        <div class="form-field">
                            <label class="field-label">Passport Copy *</label>
                            <div class="file-box" id="fileBoxPassport">
                                <i class="bi bi-file-earmark-pdf-fill file-icon"></i>
                                <span class="file-text">Passport Copy</span>
                                <span class="file-sub">PDF or Image, max 4MB</span>
                                <input type="file" name="passport_copy" id="passport_copy" required accept="image/*,application/pdf" onchange="updateFileName(this, 'fileBoxPassport')">
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
                    </div>

                    <div class="form-field full mt-3">
                        <label class="field-label">Additional Documents (Optional)</label>
                        <div class="file-box" id="fileBoxAdditional">
                            <i class="bi bi-folder-fill file-icon"></i>
                            <span class="file-text">Upload any supporting docs</span>
                            <span class="file-sub">PDF or Image, max 4MB</span>
                            <input type="file" name="additional_document" id="additional_document" accept="image/*,application/pdf" onchange="updateFileName(this, 'fileBoxAdditional')">
                            <span class="file-name-span" id="span_additional_document"></span>
                        </div>
                    </div>

                    {{-- Checkboxes --}}
                    <div class="form-field full mt-4">
                        <label class="d-flex align-items-start gap-2 fs-6" style="cursor: pointer; color: var(--gt-text-secondary);">
                            <input type="checkbox" name="passport_valid" value="1" required style="width:16px; height:16px; margin-top: 3px; accent-color: var(--c-gold);">
                            <span>I confirm that the applicant's passport is valid for 6+ months from the date of travel.</span>
                        </label>
                    </div>

                    <div class="form-field full mt-3">
                        <label class="d-flex align-items-start gap-2 fs-6" style="cursor: pointer; color: var(--gt-text-secondary);">
                            <input type="checkbox" name="not_stay_long" value="1" required style="width:16px; height:16px; margin-top: 3px; accent-color: var(--c-gold);">
                            <span>I agree not to overstay in Saudi Arabia. If I overstay, I agree to pay the overstay charges per day and also the absconding fee.</span>
                        </label>
                    </div>
                </div>
            </form>

            {{-- Checkout sidebar --}}
            <div class="checkout-card">
                <h3 style="margin: 0 0 20px; font-size: 15px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: var(--c-gold);">
                    Booking Summary
                </h3>

                <div class="sum-row">
                    <span class="sum-label">Visa Type</span>
                    <span class="sum-val" id="summaryVisaType">—</span>
                </div>
                <div class="sum-row">
                    <span class="sum-label">Nationality</span>
                    <span class="sum-val" id="summaryNationality">—</span>
                </div>
                <div class="sum-row">
                    <span class="sum-label">Processing Time</span>
                    <span class="sum-val" id="summaryProcessing">—</span>
                </div>
                <div class="sum-row">
                    <span class="sum-label">Visa Fee</span>
                    <span class="sum-val" id="summaryVisaFee">AED 0.00</span>
                </div>

                <div class="sum-row total">
                    <span class="sum-label" style="font-size: 15px; font-weight: 700;">Total Payable</span>
                    <span class="sum-total-val" id="summaryTotal">AED 0.00</span>
                </div>

                <button type="submit" form="saudiVisaForm" class="btn-submit" id="btnSubmitForm">
                    <span>Proceed to Pay</span> <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
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

        // Nationality TomSelect
        const natEl = document.getElementById('nationality');
        let nationalitySelect = null;
        if (natEl && typeof TomSelect !== 'undefined') {
            nationalitySelect = new TomSelect(natEl, {
                create: false,
                placeholder: 'Select Nationality',
                controlInput: '<input>',
                render: {
                    no_results: function(data, escape) {
                        return '<div class="no-results" style="padding: 8px 14px; color: #888;">No nationality found for "' + escape(data.input) + '"</div>';
                    }
                }
            });
            window.nationalityTomSelect = nationalitySelect;
        }

        // Pricing Summary fields
        const visaTypeSelect = document.getElementById('saudi_visa_type_id');
        const summaryVisaType = document.getElementById('summaryVisaType');
        const summaryNationality = document.getElementById('summaryNationality');
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
            
            // Update Nationality
            const natVal = natEl.value;
            summaryNationality.textContent = natVal || '—';

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
                metaContainer.style.display = 'none';
            }
        }

        if (visaTypeSelect) visaTypeSelect.addEventListener('change', updateSummary);
        if (natEl) {
            if (nationalitySelect) {
                nationalitySelect.on('change', updateSummary);
            } else {
                natEl.addEventListener('change', updateSummary);
            }
        }

        // OCR Scanning logic
        const triggerScanBtn = document.getElementById('triggerScanBtn');
        const ocrScannerInput = document.getElementById('ocrScannerInput');
        const scanStatusCard = document.getElementById('scanStatusCard');

        if (triggerScanBtn && ocrScannerInput) {
            triggerScanBtn.addEventListener('click', function () {
                ocrScannerInput.click();
            });

            ocrScannerInput.addEventListener('change', function () {
                if (!ocrScannerInput.files || ocrScannerInput.files.length === 0) return;
                const file = ocrScannerInput.files[0];

                // Show scanning status
                scanStatusCard.className = 'scan-result-card scanning';
                scanStatusCard.style.display = 'block';
                scanStatusCard.innerHTML = '<span><i class="bi bi-hourglass-split"></i> Scanning passport image...</span>';

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
                    if (data.success && data.fields) {
                        const f = data.fields;
                        
                        // Fill name
                        const first_name = document.getElementById('first_name');
                        const last_name = document.getElementById('last_name');
                        if (first_name && (f.given_names || f.full_name)) {
                            first_name.value = f.given_names || f.full_name;
                        }
                        if (last_name && f.surname) {
                            last_name.value = f.surname;
                        }

                        // Passport number
                        const passport_number = document.getElementById('passport_number');
                        if (passport_number && f.passport_number) {
                            passport_number.value = f.passport_number;
                        }

                        // Date of Birth
                        const dob = document.getElementById('dob');
                        if (dob && f.date_of_birth) {
                            dob.value = f.date_of_birth;
                        }

                        // Expiry Date
                        const passport_expiry = document.getElementById('passport_expiry');
                        if (passport_expiry && f.date_of_expiry) {
                            passport_expiry.value = f.date_of_expiry;
                        }

                        // Gender
                        const gender = document.getElementById('gender');
                        if (gender && f.sex) {
                            const sex = String(f.sex).toLowerCase();
                            if (sex.startsWith('m')) gender.value = 'Male';
                            else if (sex.startsWith('f')) gender.value = 'Female';
                        }

                        // Nationality
                        if (f.nationality) {
                            const cands = [f.issuing_country, f.nationality].filter(Boolean);
                            const opts = Array.from(natEl.options);
                            let foundNat = null;
                            for (let raw of cands) {
                                let c = String(raw).trim().toLowerCase();
                                if (NAT_MAP[c]) c = NAT_MAP[c].toLowerCase();
                                let opt = opts.find(o => o.value.toLowerCase() === c);
                                if (!opt) opt = opts.find(o => o.value.toLowerCase().includes(c));
                                if (opt) {
                                    natEl.value = opt.value;
                                    if (nationalitySelect) {
                                        nationalitySelect.setValue(opt.value);
                                    }
                                    foundNat = opt.value;
                                    break;
                                }
                            }
                        }

                        scanStatusCard.className = 'scan-result-card success';
                        scanStatusCard.innerHTML = '<span><i class="bi bi-check-circle-fill"></i> Passport scanned successfully! Fields auto-filled.</span>';
                        updateSummary();
                    } else {
                        scanStatusCard.className = 'scan-result-card error';
                        scanStatusCard.innerHTML = '<span><i class="bi bi-exclamation-triangle-fill"></i> OCR scan failed. Please type details manually.</span>';
                    }
                })
                .catch(err => {
                    scanStatusCard.className = 'scan-result-card error';
                    scanStatusCard.innerHTML = '<span><i class="bi bi-exclamation-triangle-fill"></i> Scanning error. Please enter details manually.</span>';
                    console.error('OCR Error:', err);
                });
            });
        }

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
