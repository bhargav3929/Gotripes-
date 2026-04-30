@include('header')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

    * {
        box-sizing: border-box;
    }

    /* --- VARIABLES --- */
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

    body {
        margin: 0;
        background-color: var(--c-dark-bg);
    }

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

    /* --- HEADER --- */
    .visa-header {
        display: flex;
        align-items: baseline;
        gap: 16px;
        margin-bottom: 10px;
    }

    .visa-title {
        color: var(--c-gold);
        font-weight: 700;
        font-size: 22px;
        text-transform: uppercase;
        letter-spacing: 4px;
        margin: 0;
        text-shadow: 0 0 30px rgba(255, 215, 0, 0.2);
    }

    .visa-subtitle {
        color: var(--c-text-muted);
        font-size: 11px;
        font-weight: 400;
        letter-spacing: 3px;
        text-transform: uppercase;
        margin: 0;
    }

    /* --- GRID LAYOUT --- */
    .visa-main {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 18px;
        align-items: start;
    }

    /* --- CARDS --- */
    .card {
        background: var(--c-card-bg);
        border: 1px solid rgba(255, 215, 0, 0.1);
        border-radius: 14px;
        padding: 16px 20px;
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
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 3px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        opacity: 0.9;
    }

    /* --- FORM ELEMENTS --- */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px 16px;
    }

    .form-grid-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 8px 16px;
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
        letter-spacing: 1.6px;
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

    /* Custom Select */
    select.field-input {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='16' height='16' fill='%23FFD700'%3E%3Cpath d='M11.9997 13.1714L16.9495 8.22168L18.3637 9.63589L11.9997 15.9999L5.63574 9.63589L7.04996 8.22168L11.9997 13.1714Z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: calc(100% - 20px) center;
        cursor: pointer;
    }

    /* --- APPLICANT BOX --- */
    .applicant-box {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 215, 0, 0.05);
        border-radius: 8px;
        padding: 8px 14px;
        margin-bottom: 6px;
    }

    .applicant-header {
        color: #eee;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
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
        height: 36px;
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
        flex-direction: row;
        flex-wrap: wrap;
        gap: 6px 24px;
        margin-top: 4px;
        padding-top: 6px;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
    }

    .custom-checkbox-label {
        display: flex;
        align-items: center;
        gap: 15px;
        cursor: pointer;
        user-select: none;
    }

    .custom-checkbox-input {
        display: none;
    }

    .custom-checkbox-box {
        width: 18px;
        height: 18px;
        min-width: 18px;
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
        color: #999;
        font-size: 12px;
        letter-spacing: 0.3px;
    }

    /* --- SUMMARY --- */
    .summary-card-wrapper {
        position: sticky;
        top: 190px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 14px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .summary-label {
        color: #777;
        font-size: 12px;
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
        transition: all 0.3s cubic;
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

                    <div class="form-grid-3">
                        <div class="form-field">
                            <label class="field-label">Nationality</label>
                            <select id="nationality" name="nationality" class="field-input" required>
                                <option value="">Select Nationality</option>
                            </select>
                        </div>
                        <div class="form-field">
                            <label class="field-label">Current Residence</label>
                            <select id="residence" name="residence" class="field-input" required>
                                <option value="">Select Country</option>
                            </select>
                        </div>
                        <div class="form-field">
                            <label class="field-label">Visa Duration</label>
                            <select class="field-input" id="visaDuration" name="visaDuration" required>
                                <option value="">Select Duration</option>
                                <option value="30" data-price="300">30 DAYS VISA - 300 AED</option>
                                <option value="60" data-price="600">60 DAYS VISA - 600 AED</option>
                            </select>
                            <input type="hidden" name="price" id="hiddenPrice" value="0">
                        </div>
                        <div class="form-field">
                            <label class="field-label">Number of Persons (Adults)</label>
                            <select class="field-input" id="visaCount" name="visa_count" required>
                                @for ($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ $i === 1 ? 'selected' : '' }}>{{ $i }} {{ $i === 1 ? 'Adult' : 'Adults' }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-field">
                            <label class="field-label">Number of Children</label>
                            <select class="field-input" id="visaChildren" name="children_count">
                                @for ($i = 0; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ $i === 0 ? 'selected' : '' }}>{{ $i }} {{ $i === 1 ? 'Child' : 'Children' }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-field">
                            <label class="field-label">Arrival Date</label>
                            <input type="date" class="field-input" name="arrival_date" required>
                        </div>
                        <div class="form-field">
                            <label class="field-label">Departure Date</label>
                            <input type="date" class="field-input" name="departure_date" required>
                        </div>
                        <div class="form-field">
                            <label class="field-label">Email Address</label>
                            <input type="email" class="field-input" name="email" placeholder="name@example.com" required>
                        </div>
                        <div class="form-field">
                            <label class="field-label">Phone Number</label>
                            <input type="tel" class="field-input" name="phone" placeholder="+971 50 000 0000" required>
                        </div>
                        <div class="form-field" style="display:flex; align-items:flex-end;">
                            <div class="checkbox-group" style="border:none; margin:0; padding:0;">
                                <label class="custom-checkbox-label">
                                    <input type="checkbox" class="custom-checkbox-input" name="passport_valid" value="1" required>
                                    <span class="custom-checkbox-box"><i class="bi bi-check-lg"></i></span>
                                    <span class="checkbox-text">Passports valid 6+ months</span>
                                </label>
                                <label class="custom-checkbox-label">
                                    <input type="checkbox" class="custom-checkbox-input" name="not_stay_long" value="1" required>
                                    <span class="custom-checkbox-box"><i class="bi bi-check-lg"></i></span>
                                    <span class="checkbox-text">Will not overstay</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div style="margin: 10px 0; border-top: 1px solid rgba(255,255,255,0.05);"></div>

                    <!-- APPLICANTS CONTAINER -->
                    <div id="applicantsContainer">
                        <!-- Dynamic fields will be injected here -->
                    </div>

                </form>
            </div>

            <!-- RIGHT: SUMMARY -->
            <div class="summary-card-wrapper">
                <div class="card">
                    <div class="card-title">
                        <i class="bi bi-receipt-cutoff"></i> Payment Summary
                    </div>

                    <div class="summary-row">
                        <span class="summary-label">Visa Fee (<span id="countDisplay">1</span>x)</span>
                        <span class="summary-value" id="summaryBase">AED 0.00</span>
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
            // Derive default text from name structure name="passport_copy[]"
            const name = input.getAttribute('name');
            if (name.includes('passport_copy')) fileNameSpan.textContent = 'Click to upload (PDF/Image)...';
            else if (name.includes('passport_photo')) fileNameSpan.textContent = 'Click to upload (ID Photo)...';
            else fileNameSpan.textContent = 'Other documents (if any)...';
            fileNameSpan.classList.remove('selected');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // --- Populate Country Dropdowns ---
        const countrySelects = [document.getElementById('nationality'), document.getElementById('residence')];
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
        const applicantsContainer = document.getElementById('applicantsContainer');
        const durationSelect = document.getElementById('visaDuration');

        // Numeric-only enforcement (block letters/symbols, allow nav keys)
        function enforceNumericOnly(input) {
            input.addEventListener('keydown', function (e) {
                const allowed = ['Backspace', 'Tab', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Delete', 'Home', 'End', 'Enter'];
                if (allowed.includes(e.key) || e.ctrlKey || e.metaKey) return;
                if (!/^[0-9]$/.test(e.key)) e.preventDefault();
            });
            input.addEventListener('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
            input.addEventListener('paste', function (e) {
                const text = (e.clipboardData || window.clipboardData).getData('text');
                if (!/^[0-9]+$/.test(text)) e.preventDefault();
            });
        }
        // Numeric enforcement no longer needed for native select dropdowns
        // (kept enforceNumericOnly defined in case other inputs reuse it)

        function readCount(el, min, max, fallback) {
            let v = parseInt(el.value, 10);
            if (isNaN(v)) v = fallback;
            if (v < min) v = min;
            if (v > max) v = max;
            return v;
        }

        function getAdults() { return readCount(visaCountSelect, 1, 10, 1); }
        function getChildren() { return readCount(childrenCountInput, 0, 10, 0); }

        // --- Dynamic Form Generation ---
        function generateApplicants(adults, children) {
            applicantsContainer.innerHTML = ''; // Clear existing
            const total = adults + children;
            for (let i = 0; i < total; i++) {
                const isChild = i >= adults;
                const label = isChild
                    ? `Child #${i - adults + 1}`
                    : `Applicant #${i + 1}`;
                const html = `
                    <div class="applicant-box">
                        <div class="applicant-header">
                            <span><i class="bi bi-person-fill"></i> ${label}</span>
                            <small class="text-muted" style="font-size:9px;">DOCUMENTS REQUIRED</small>
                        </div>
                        <div class="form-grid-3">
                            <div class="form-field">
                                <label class="field-label">Passport Copy</label>
                                <div class="file-input-wrapper">
                                    <input type="file" name="passport_copy[]" class="file-input-real" accept=".pdf,.jpg,.jpeg,.png" required onchange="updateFileName(this)">
                                    <div class="file-input-custom">
                                        <span class="file-name">Upload (PDF/Image)...</span>
                                        <i class="bi bi-cloud-arrow-up-fill file-icon"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="form-field">
                                <label class="field-label">Passport Photo</label>
                                <div class="file-input-wrapper">
                                    <input type="file" name="passport_photo[]" class="file-input-real" accept="image/*" required onchange="updateFileName(this)">
                                    <div class="file-input-custom">
                                        <span class="file-name">Upload (ID Photo)...</span>
                                        <i class="bi bi-person-bounding-box file-icon"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="form-field">
                                <label class="field-label">Supporting Doc (Optional)</label>
                                <div class="file-input-wrapper">
                                    <input type="file" name="supporting_document[]" class="file-input-real" accept=".pdf,.jpg,.jpeg,.png" onchange="updateFileName(this)">
                                    <div class="file-input-custom">
                                        <span class="file-name">Other docs (if any)...</span>
                                        <i class="bi bi-paperclip file-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                 `;
                applicantsContainer.insertAdjacentHTML('beforeend', html);
            }
        }

        function refreshForm() {
            generateApplicants(getAdults(), getChildren());
            updatePrice();
        }

        // Initialize
        refreshForm();

        visaCountSelect.addEventListener('change', refreshForm);
        childrenCountInput.addEventListener('change', refreshForm);

        // --- Pricing Logic ---
        function updatePrice() {
            const adults = getAdults();
            const children = getChildren();
            const total = adults + children;
            const option = durationSelect.options[durationSelect.selectedIndex];
            const unitPrice = parseFloat(option.getAttribute('data-price')) || 0;

            const totalBase = unitPrice * total;

            document.getElementById('hiddenPrice').value = totalBase.toFixed(2);
            document.getElementById('countDisplay').textContent = total;

            document.getElementById('summaryBase').textContent = 'AED ' + totalBase.toFixed(2);
            document.getElementById('summaryTotal').textContent = 'AED ' + totalBase.toFixed(2);
        }

        durationSelect.addEventListener('change', updatePrice);

        // --- Form Submit ---
        const form = document.getElementById('visaForm');
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            const originalText = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> PROCESSING...';

            const formData = new FormData(this);
            // Append counts explicitly just in case
            formData.set('visa_count', String(getAdults()));
            formData.set('children_count', String(getChildren()));

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

@include('footer')