@include('header')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

    * {
        box-sizing: border-box;
    }

    .checkout-page {
        background: linear-gradient(180deg, #000 0%, #080808 100%);
        min-height: 100vh;
        padding-top: 85px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Outfit', sans-serif;
    }

    .checkout-wrapper {
        width: 100%;
        max-width: 1200px;
        padding: 0 40px 30px;
    }

    .checkout-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .checkout-title {
        color: #FFD700;
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        font-size: 32px;
        text-transform: uppercase;
        letter-spacing: 8px;
        margin: 0 0 12px 0;
        text-shadow: 0 0 60px rgba(255, 215, 0, 0.25);
    }

    .checkout-subtitle {
        color: #666;
        font-size: 14px;
        font-weight: 400;
        letter-spacing: 3px;
    }

    .checkout-main {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 40px;
        align-items: stretch;
    }

    /* Premium Card Style */
    .card {
        background: linear-gradient(165deg, #0e0e0e 0%, #080808 100%);
        border: 1px solid rgba(255, 215, 0, 0.08);
        border-radius: 20px;
        padding: 35px 40px;
        position: relative;
        box-shadow:
            0 25px 50px -12px rgba(0, 0, 0, 0.5),
            inset 0 1px 0 rgba(255, 255, 255, 0.03);
    }

    .card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60%;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 215, 0, 0.5), transparent);
    }

    .card-title {
        color: #FFD700;
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 3px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-title i {
        font-size: 18px;
        opacity: 0.9;
    }

    /* Form Grid */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    .form-field {
        display: flex;
        flex-direction: column;
    }

    .form-field.full {
        grid-column: 1 / -1;
    }

    .field-label {
        color: #888;
        font-family: 'Outfit', sans-serif;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 10px;
    }

    .field-input {
        width: 100%;
        height: 54px;
        background: #111;
        border: 1px solid #2a2a2a;
        border-radius: 12px;
        padding: 0 20px;
        color: #fff;
        font-family: 'Outfit', sans-serif;
        font-size: 15px;
        font-weight: 500;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }

    .field-input:hover {
        border-color: #3a3a3a;
        background: #141414;
    }

    .field-input:focus {
        outline: none;
        border-color: #FFD700;
        background: #151515;
        box-shadow: 0 0 0 4px rgba(255, 215, 0, 0.08);
    }

    .field-input::placeholder {
        color: #444;
        font-weight: 400;
    }

    /* Custom Select Styling */
    select.field-input {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23FFD700' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: calc(100% - 18px) center;
        background-size: 12px;
        padding-right: 50px;
        cursor: pointer;
    }

    select.field-input option {
        background: #111;
        color: #fff;
        padding: 12px;
    }

    /* Phone Input Group */
    .phone-row {
        display: grid;
        grid-template-columns: 130px 1fr;
        gap: 16px;
    }

    .phone-code {
        padding: 0 12px;
        padding-right: 36px;
        background-position: calc(100% - 12px) center;
    }

    /* Summary Card */
    .summary-card {
        display: flex;
        flex-direction: column;
    }

    .summary-body {
        flex: 1;
    }

    .summary-placeholder {
        text-align: center;
        padding: 40px 20px;
        color: #3a3a3a;
        font-size: 14px;
        font-weight: 500;
    }

    .summary-placeholder i {
        font-size: 32px;
        display: block;
        margin-bottom: 14px;
        color: #2a2a2a;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    }

    .summary-row:last-of-type {
        border-bottom: none;
    }

    .summary-label {
        color: #777;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .summary-label i {
        color: #FFD700;
        font-size: 12px;
        opacity: 0.8;
    }

    .summary-value {
        color: #ccc;
        font-size: 15px;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .summary-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 215, 0, 0.4), transparent);
        margin: 20px 0;
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .total-label {
        color: #999;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .total-value {
        color: #FFD700;
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        font-size: 30px;
        letter-spacing: 1px;
        text-shadow: 0 0 40px rgba(255, 215, 0, 0.3);
    }

    /* Pay Button */
    .pay-btn {
        width: 100%;
        height: 56px;
        background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%);
        border: none;
        border-radius: 14px;
        margin-top: 28px;
        color: #000;
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 3px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 20px rgba(255, 215, 0, 0.2);
    }

    .pay-btn:hover:not(:disabled) {
        background: linear-gradient(135deg, #fff 0%, #f0f0f0 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(255, 255, 255, 0.15);
    }

    .pay-btn:active:not(:disabled) {
        transform: translateY(0);
    }

    .pay-btn:disabled {
        background: #1a1a1a;
        color: #444;
        cursor: not-allowed;
        box-shadow: none;
    }

    .pay-btn i {
        font-size: 16px;
    }

    .secure-badge {
        text-align: center;
        margin-top: 18px;
        color: #444;
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .secure-badge i {
        color: #4CAF50;
        font-size: 12px;
    }

    /* Responsive */
    @media (max-width: 1000px) {
        .checkout-main {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .phone-row {
            grid-template-columns: 1fr;
        }

        .checkout-title {
            font-size: 24px;
            letter-spacing: 5px;
        }

        .card {
            padding: 25px;
        }
    }
</style>

<div class="checkout-page">
    <div class="checkout-wrapper">
        <div class="checkout-header">
            <h1 class="checkout-title">Secure Payment</h1>
            <p class="checkout-subtitle">Complete your payment securely</p>
        </div>

        <div class="checkout-main">
            <!-- Form Card -->
            <div class="card">
                <div class="card-title">
                    <i class="bi bi-person-fill"></i> Client Information
                </div>

                <form id="agentPayForm">
                    @csrf

                    <div class="form-grid">
                        <div class="form-field">
                            <label class="field-label">Full Name</label>
                            <input type="text" class="field-input" id="client_name" name="client_name"
                                placeholder="John Doe" required>
                        </div>

                        <div class="form-field">
                            <label class="field-label">Email Address</label>
                            <input type="email" class="field-input" id="client_email" name="client_email"
                                placeholder="john@example.com" required>
                        </div>

                        <div class="form-field full">
                            <label class="field-label">Phone Number</label>
                            <div class="phone-row">
                                <select class="field-input phone-code" id="country_code" name="country_code">
                                    <option value="+971">🇦🇪 +971</option>
                                    <option value="+1">🇺🇸 +1</option>
                                    <option value="+44">🇬🇧 +44</option>
                                    <option value="+91">🇮🇳 +91</option>
                                    <option value="+92">🇵🇰 +92</option>
                                    <option value="+966">🇸🇦 +966</option>
                                    <option value="+974">🇶🇦 +974</option>
                                </select>
                                <input type="tel" class="field-input" id="client_phone_number" placeholder="50 123 4567"
                                    required>
                            </div>
                            <input type="hidden" id="client_phone" name="client_phone">
                        </div>

                        <div class="form-field">
                            <label class="field-label">Service Type</label>
                            <select class="field-input" id="service" name="service" required>
                                <option value="">SELECT SERVICE</option>
                                <option value="AIRPORT PICK & DROP">AIRPORT PICK & DROP</option>
                                <option value="INDUSTRY CONSULTATION">INDUSTRY CONSULTATION</option>
                                <option value="JOB SUPPORT SERVICES">JOB SUPPORT SERVICES</option>
                                <option value="TICKETS SERVICES">TICKETS SERVICES</option>
                                <option value="TOUR ACTIVITIES">TOUR ACTIVITIES</option>
                                <option value="VISA SERVICES">VISA SERVICES</option>
                                <option value="HOTEL BOOKING">HOTEL BOOKING</option>
                                <option value="OTHER SERVICES">OTHER SERVICES</option>
                            </select>
                        </div>

                        <div class="form-field">
                            <label class="field-label">Amount (AED)</label>
                            <input type="number" class="field-input" id="amount" name="amount"
                                placeholder="Enter amount" min="1" step="0.01" required>
                        </div>
                    </div>

                    <input type="hidden" id="total_amount" name="total_amount">
                </form>
            </div>

            <!-- Summary Card -->
            <div class="card summary-card">
                <div class="card-title">
                    <i class="bi bi-receipt"></i> Payment Summary
                </div>

                <div class="summary-body">
                    <div id="summaryPlaceholder">
                        <div class="summary-placeholder">
                            <i class="bi bi-calculator"></i>
                            Enter amount to see breakdown
                        </div>
                    </div>

                    <div id="summaryBreakdown" style="display: none;">
                        <div class="summary-row">
                            <span class="summary-label"><i class="bi bi-tag-fill"></i> Service Amount</span>
                            <span class="summary-value" id="showBase">AED 0.00</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label"><i class="bi bi-plus-circle-fill"></i> Transaction Fee</span>
                            <span class="summary-value" id="showFee">AED 1.00</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label"><i class="bi bi-credit-card-fill"></i> Gateway (3%)</span>
                            <span class="summary-value" id="showGateway">AED 0.00</span>
                        </div>
                        <div class="summary-divider"></div>
                        <div class="summary-total">
                            <span class="total-label">Total</span>
                            <span class="total-value" id="showTotal">AED 0.00</span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="pay-btn" id="submitBtn" form="agentPayForm" disabled>
                    <i class="bi bi-lock-fill"></i> Proceed to Payment
                </button>

                <div class="secure-badge">
                    <i class="bi bi-shield-check"></i> Secured Payment • 256-bit SSL
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calc(base) {
    const b = parseFloat(base) || 0;
    const fee = 1;
    const gw = (b + fee) * 0.03;
    return { base: b, fee, gw, total: b + fee + gw };
}

function fmt(n) { return 'AED ' + n.toFixed(2); }

function update() {
    const b = parseFloat(document.getElementById('amount').value) || 0;
    const ph = document.getElementById('summaryPlaceholder');
    const bd = document.getElementById('summaryBreakdown');
    const btn = document.getElementById('submitBtn');

    if (b > 0) {
        const c = calc(b);
        document.getElementById('showBase').textContent = fmt(c.base);
        document.getElementById('showFee').textContent = fmt(c.fee);
        document.getElementById('showGateway').textContent = fmt(c.gw);
        document.getElementById('showTotal').textContent = fmt(c.total);
        document.getElementById('total_amount').value = c.total.toFixed(2);
        ph.style.display = 'none';
        bd.style.display = 'block';
        btn.disabled = false;
    } else {
        ph.style.display = 'block';
        bd.style.display = 'none';
        btn.disabled = true;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const amt = document.getElementById('amount');
    amt.addEventListener('input', update);
    amt.addEventListener('change', update);

    const form = document.getElementById('agentPayForm');
    const btn = document.getElementById('submitBtn');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass"></i> Processing...';

        const csrf = document.querySelector('input[name="_token"]').value;
        const cc = document.getElementById('country_code').value;
        const pn = document.getElementById('client_phone_number').value;
        document.getElementById('client_phone').value = cc + pn;

        const fd = new FormData(form);
        fd.set('amount', document.getElementById('total_amount').value);

        fetch("{{ route('agent.pay') }}", {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: fd
        })
        .then(r => r.json())
        .then(d => {
            if (d.success && d.checkout_url) {
                window.location.href = d.checkout_url;
            } else {
                throw new Error(d.message || 'Failed');
            }
        })
        .catch(e => {
            alert(e.message || 'Error occurred');
            btn.disabled = false;
            btn.innerHTML = orig;
        });
    });
});
</script>

@include('footer')