<div class="modal fade" id="activityBookingModal" tabindex="-1" aria-labelledby="activityBookingModalLabel" aria-hidden="true" style="z-index: 10001;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <button type="button" class="btn-close-luxury" data-bs-dismiss="modal">
                <i class="bi bi-x-lg"></i>
            </button>
            <div class="modal-body pt-5 px-4">
                {{-- Step Indicator --}}
                <div class="modal-step-indicator">
                    <div class="step-item active" data-step="1">
                        <div class="step-circle">
                            <span class="step-number">1</span>
                        </div>
                        <span class="step-label">Details</span>
                    </div>
                    <div class="step-connector">
                        <div class="connector-fill"></div>
                    </div>
                    <div class="step-item" data-step="2">
                        <div class="step-circle">
                            <span class="step-number">2</span>
                        </div>
                        <span class="step-label">Review</span>
                    </div>
                </div>

                <h5 class="text-center mb-4" id="activityBookingModalLabel" style="color: #fff; font-size: 28px; font-weight: 900; text-transform: uppercase; letter-spacing: -0.5px;">
                    Book <span style="color: #FFD23F;" id="modalActivityName">Activity</span>
                </h5>

                <form method="POST" id="activityBookingForm" autocomplete="off">
                    @csrf
                    <input type="hidden" name="activityId" id="modalActivityId">
                    <input type="hidden" name="currency" id="formCurrency" value="AED">

                    <!-- Section 1: Primary Contact Information -->
                    <div class="booking-section-group mb-4">
                        <div class="section-header mb-3">
                            <h6 class="text-uppercase fw-bold m-0" style="color: #FFD23F; letter-spacing: 2px; font-size: 11px;">Primary Contact Information</h6>
                        </div>
                        <div class="row g-3">
                            <!-- Name -->
                            <div class="col-12">
                                <div class="input-group-premium">
                                    <i class="bi bi-person text-warning"></i>
                                    <input type="text" name="name" class="form-control-premium" placeholder="Full Name*" required>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <div class="input-group-premium">
                                    <i class="bi bi-envelope text-warning"></i>
                                    <input type="email" name="email" class="form-control-premium" placeholder="Email Address*" required>
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="col-md-6">
                                <div class="input-group-premium">
                                    <i class="bi bi-telephone text-warning"></i>
                                    <input type="text" name="phone" class="form-control-premium" placeholder="Phone Number*" required>
                                </div>
                            </div>

                            <!-- Date -->
                            <div class="col-md-6">
                                <div class="input-group-premium">
                                    <i class="bi bi-calendar-event text-warning"></i>
                                    <input type="text" name="date" class="form-control-premium" placeholder="Booking Date*" onfocus="(this.type='date')" onblur="if(this.value==''){this.type='text'}" required>
                                </div>
                            </div>

                            <!-- Adults -->
                            <div class="col-md-6">
                                <div class="input-group-premium">
                                    <i class="bi bi-people text-warning"></i>
                                    <input type="number" name="adults" class="form-control-premium" placeholder="Adults (18+)*" min="1" max="50" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Additional Details -->
                    <div class="booking-section-group mb-4">
                        <div class="section-header mb-3">
                            <h6 class="text-uppercase fw-bold m-0" style="color: #FFD23F; letter-spacing: 2px; font-size: 11px;">Trip Customization</h6>
                        </div>
                        <div class="row g-3">
                            <!-- Children -->
                            <div class="col-md-6">
                                <div class="input-group-premium">
                                    <i class="bi bi-person text-warning"></i>
                                    <input type="number" name="childrens" class="form-control-premium" placeholder="Children (6-17)" min="0" max="50" value="0">
                                </div>
                            </div>

                            <!-- Nationality -->
                            <div class="col-md-6">
                                <div class="input-group-premium">
                                    <i class="bi bi-globe text-warning"></i>
                                    <input type="text" name="nationality" class="form-control-premium" placeholder="Nationality">
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="col-12">
                                <div class="input-group-premium">
                                    <i class="bi bi-geo-alt text-warning"></i>
                                    <input type="text" name="address" class="form-control-premium" placeholder="Pick-up Address / Hotel Name">
                                </div>
                            </div>

                            <!-- Transport Selection -->
                            <div class="col-12">
                                <div class="input-group-premium">
                                    <i class="bi bi-truck text-warning"></i>
                                    <select class="form-control-premium" name="transfer" id="modalTransferSelect" required>
                                        <option value="">-- Select Transport Option --</option>
                                        <option value="abu_dhabi">Transport within Abu Dhabi</option>
                                        <option value="dubai">Transport within Dubai</option>
                                        <option value="abu_dhabi_to_dubai">Abu Dhabi ⇄ Dubai</option>
                                        <option value="any_emirates">Any Emirates</option>
                                        <option value="without_transfer">Without Transport</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Remarks -->
                            <div class="col-12">
                                <div class="input-group-premium align-items-start py-2">
                                    <i class="bi bi-chat-left-text text-warning mt-1"></i>
                                    <textarea name="remarks" class="form-control-premium border-0 p-0 ms-2" rows="2" placeholder="Any special requests or notes..." style="background: transparent; min-height: 50px;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="payment_option" value="book_and_pay_yourself">

                    <!-- Price Breakdown Area -->
                    <div id="modalPriceSummary" class="price-summary-card mb-4" style="display: none;">
                        <h6 class="text-center mb-3 text-white-50" style="font-size: 11px; text-transform: uppercase; letter-spacing: 2px;">Estimated Summary</h6>
                        <div class="summary-row">
                            <span>Adult Tickets</span>
                            <span id="modalDisplayAdultPrice">0.00 AED</span>
                        </div>
                        <div class="summary-row">
                            <span>Child Tickets</span>
                            <span id="modalDisplayChildPrice">0.00 AED</span>
                        </div>
                        <div class="summary-row" id="modalDisplayTransportRow" style="display: none !important;">
                            <span>Premium Transport</span>
                            <span id="modalDisplayTransportPrice">0.00 AED</span>
                        </div>
                        <div class="summary-row fw-normal opacity-75" id="modalDisplayVatRow" style="display: none !important;">
                            <span>VAT (5%)</span>
                            <span id="modalDisplayVatPrice">0.00 AED</span>
                        </div>
                        <div class="summary-total mt-3 pt-3">
                            <span>Total Amount</span>
                            <span id="modalDisplayTotalPrice">0.00 AED</span>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-0 mb-3">
                        <button type="button" class="btn-premium-book" id="modalBookNowBtn">
                            <span id="modalBookNowText">PROCEED TO BOOKING</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pricing Overlay (Secondary Modal for Confirmation before payment) -->
<div id="modalChargePreview" class="payment-overlay">
    <div class="payment-card">
        {{-- Close button --}}
        <button type="button" id="closePrevModal" class="payment-close-btn">
            <i class="bi bi-arrow-left"></i>
        </button>

        {{-- Header with icon --}}
        <div class="payment-header">
            <div class="payment-icon">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h3 class="payment-title">Order Summary</h3>
            <p class="payment-subtitle">Review your booking details</p>
        </div>

        {{-- Price breakdown --}}
        <div class="payment-breakdown">
            <div class="payment-row">
                <div class="payment-row-left">
                    <i class="bi bi-person-fill"></i>
                    <span>Adults</span>
                </div>
                <span class="payment-row-value" id="prevAdultsTotal">0.00</span>
            </div>
            <div class="payment-row">
                <div class="payment-row-left">
                    <i class="bi bi-people-fill"></i>
                    <span>Children</span>
                </div>
                <span class="payment-row-value" id="prevChildrenTotal">0.00</span>
            </div>
            <div class="payment-row" id="prevTransportRow" style="display:none;">
                <div class="payment-row-left">
                    <i class="bi bi-truck"></i>
                    <span>Transport</span>
                </div>
                <span class="payment-row-value" id="prevTransportTotal">0.00</span>
            </div>
            <div class="payment-row">
                <div class="payment-row-left">
                    <i class="bi bi-receipt"></i>
                    <span>Transaction Fee</span>
                </div>
                <span class="payment-row-value" id="prevTxnFee">0.00</span>
            </div>
            <div class="payment-row">
                <div class="payment-row-left">
                    <i class="bi bi-percent"></i>
                    <span>VAT (5%)</span>
                </div>
                <span class="payment-row-value" id="prevVatTotal">0.00</span>
            </div>
        </div>

        {{-- Total --}}
        <div class="payment-total">
            <span class="payment-total-label">Total Amount</span>
            <div class="payment-total-value">
                <span id="prevFinalTotal">0.00</span>
                <small>AED</small>
            </div>
        </div>

        {{-- Pay button --}}
        <button type="button" id="modalFinalPayBtn" class="payment-pay-btn">
            <i class="bi bi-lock-fill"></i>
            <span>PAY NOW SECURELY</span>
        </button>

        <p class="payment-secure-note">
            <i class="bi bi-shield-check"></i> Secured by 256-bit SSL encryption
        </p>
    </div>
</div>

<style>
    /* Premium Modal Styling */
    #activityBookingModal .modal-content {
        background: #0a0a0a !important; /* Solid background to prevent transparency issues */
        border: 1px solid rgba(255, 210, 63, 0.4);
        border-radius: 28px;
        box-shadow: 0 0 80px rgba(0, 0, 0, 0.9), 0 0 30px rgba(255, 210, 63, 0.1);
        overflow: hidden;
        position: relative;
    }

    /* ─── Redesigned Step Indicator ─────────────────── */
    .modal-step-indicator {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 35px;
        gap: 0;
    }

    .step-item {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: default;
        position: relative;
    }

    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.04);
        border: 2px solid rgba(255, 255, 255, 0.12);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        flex-shrink: 0;
    }

    .step-number {
        font-size: 15px;
        font-weight: 800;
        color: rgba(255, 255, 255, 0.3);
        transition: all 0.4s ease;
        line-height: 1;
    }

    .step-label {
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.3);
        transition: all 0.4s ease;
        white-space: nowrap;
    }

    /* Connector line between steps */
    .step-connector {
        width: 80px;
        height: 3px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 3px;
        margin: 0 20px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .connector-fill {
        width: 0%;
        height: 100%;
        background: linear-gradient(90deg, #FFD23F, #FFB800);
        border-radius: 3px;
        transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Active step */
    .step-item.active .step-circle {
        background: linear-gradient(135deg, #FFD23F 0%, #FFB800 100%);
        border-color: transparent;
        box-shadow: 0 0 20px rgba(255, 210, 63, 0.35), 0 4px 15px rgba(255, 184, 0, 0.25);
    }

    .step-item.active .step-number {
        color: #000;
        font-weight: 900;
    }

    .step-item.active .step-label {
        color: #FFD23F;
        font-weight: 800;
    }

    /* Completed step (when moving to step 2) */
    .step-item.completed .step-circle {
        background: linear-gradient(135deg, #FFD23F 0%, #FFB800 100%);
        border-color: transparent;
        box-shadow: 0 0 12px rgba(255, 210, 63, 0.2);
    }

    .step-item.completed .step-number {
        color: #000;
    }

    .step-item.completed .step-label {
        color: rgba(255, 210, 63, 0.7);
    }

    /* Filled connector when step 2 is active */
    .step-connector.filled .connector-fill {
        width: 100%;
    }

    .input-group-premium {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 14px;
        padding: 0 20px;
        display: flex;
        align-items: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        width: 100%;
        min-height: 58px; /* High-end standard height */
    }

    .input-group-premium i {
        font-size: 18px;
        margin-right: 12px;
        opacity: 0.8;
    }

    .input-group-premium:focus-within {
        border-color: #FFD23F;
        background: rgba(255, 210, 63, 0.08);
        box-shadow: 0 0 25px rgba(255, 210, 63, 0.1);
    }

    .form-control-premium {
        background: transparent !important;
        border: none !important;
        color: #fff !important;
        font-size: 15px;
        padding: 6px 0 !important;
        width: 100%;
        outline: none;
        font-family: inherit;
    }

    .form-control-premium::placeholder {
        color: rgba(255, 255, 255, 0.3);
    }

    /* Remove Number Spinners */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }

    select.form-control-premium option {
        background: #111;
        color: #fff;
        padding: 15px;
    }

    /* Section Groups */
    .booking-section-group {
        background: rgba(255,255,255,0.02);
        padding: 25px;
        border-radius: 18px;
        border: 1px solid rgba(255,255,255,0.05);
    }

    .section-header h6 {
        font-size: 12px !important;
        letter-spacing: 2px !important;
        color: rgba(255,255,255,0.6) !important;
        margin-bottom: 20px !important;
    }

    /* Price Summary Card */
    .price-summary-card {
        background: linear-gradient(135deg, rgba(20, 20, 20, 0.95) 0%, rgba(40, 40, 40, 0.95) 100%);
        border: 1px solid rgba(255, 210, 63, 0.3);
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 14px;
        color: rgba(255, 255, 255, 0.6);
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        border-top: 1px solid rgba(255, 210, 63, 0.2);
        margin-top: 20px;
        padding-top: 20px;
        font-size: 22px;
        font-weight: 800;
        color: #FFD23F;
    }

    /* Close Button */
    .btn-close-luxury {
        position: absolute;
        top: 25px;
        right: 25px;
        background: rgba(255,255,255,0.1);
        border: none;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        cursor: pointer;
        transition: all 0.3s;
        z-index: 10;
    }
    .btn-close-luxury:hover {
        background: #FFD23F;
        color: #000;
        transform: rotate(90deg);
    }

    /* Premium Button */
    .btn-premium-book {
        width: 100%;
        background: linear-gradient(135deg, #FFD700 0%, #FFB800 100%);
        color: #000;
        border: none;
        padding: 18px;
        border-radius: 12px;
        font-weight: 800;
        font-size: 16px;
        text-transform: uppercase;
        letter-spacing: 1.8px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
    }

    .btn-premium-book:hover {
        transform: translateY(-3px) scale(1.01);
        box-shadow: 0 12px 30px rgba(255, 215, 0, 0.35);
        filter: brightness(1.1);
    }

    .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }
    
    .btn-loader {
        display: inline-block; width: 18px; height: 18px;
        border: 2px solid rgba(0,0,0,0.3); border-radius: 50%;
        border-top-color: #000; animation: modal-spin 1s linear infinite;
        margin-right: 8px; vertical-align: middle;
    }
    @keyframes modal-spin { to { transform: rotate(360deg); } }

    /* ─── Payment Confirmation Overlay ─────────────── */
    .payment-overlay {
        display: none;
        position: fixed;
        left: 0;
        top: 0;
        width: 100vw;
        height: 100vh;
        z-index: 10002;
        justify-content: center;
        align-items: center;
        background: rgba(0, 0, 0, 0.9);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        animation: overlayFadeIn 0.3s ease;
    }

    @keyframes overlayFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes cardSlideUp {
        from { opacity: 0; transform: translateY(30px) scale(0.96); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .payment-card {
        background: linear-gradient(165deg, #141414 0%, #0a0a0a 50%, #111 100%);
        color: #fff;
        padding: 35px 30px 30px;
        border-radius: 24px;
        min-width: 380px;
        max-width: 440px;
        width: 90vw;
        border: 1px solid rgba(255, 210, 63, 0.15);
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.7), 0 0 40px rgba(255, 210, 63, 0.06);
        position: relative;
        animation: cardSlideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .payment-close-btn {
        position: absolute;
        top: 16px;
        left: 16px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255, 255, 255, 0.5);
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 16px;
    }

    .payment-close-btn:hover {
        background: rgba(255, 210, 63, 0.1);
        border-color: rgba(255, 210, 63, 0.3);
        color: #FFD23F;
    }

    .payment-header {
        text-align: center;
        margin-bottom: 28px;
    }

    .payment-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: linear-gradient(135deg, rgba(255, 210, 63, 0.15) 0%, rgba(255, 184, 0, 0.08) 100%);
        border: 1px solid rgba(255, 210, 63, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 22px;
        color: #FFD23F;
    }

    .payment-title {
        font-size: 22px;
        font-weight: 800;
        color: #fff;
        margin: 0 0 6px;
        letter-spacing: -0.3px;
    }

    .payment-subtitle {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.35);
        margin: 0;
        letter-spacing: 0.3px;
    }

    .payment-breakdown {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        padding: 6px 0;
        margin-bottom: 20px;
    }

    .payment-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 13px 20px;
        transition: background 0.2s ease;
    }

    .payment-row:not(:last-child) {
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    }

    .payment-row-left {
        display: flex;
        align-items: center;
        gap: 10px;
        color: rgba(255, 255, 255, 0.55);
        font-size: 14px;
    }

    .payment-row-left i {
        font-size: 14px;
        color: rgba(255, 210, 63, 0.5);
        width: 18px;
        text-align: center;
    }

    .payment-row-value {
        font-size: 14px;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.8);
        font-variant-numeric: tabular-nums;
    }

    .payment-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, rgba(255, 210, 63, 0.1) 0%, rgba(255, 184, 0, 0.05) 100%);
        border: 1px solid rgba(255, 210, 63, 0.2);
        border-radius: 14px;
        padding: 18px 20px;
        margin-bottom: 24px;
    }

    .payment-total-label {
        font-size: 14px;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.6);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .payment-total-value {
        display: flex;
        align-items: baseline;
        gap: 6px;
    }

    .payment-total-value span {
        font-size: 28px;
        font-weight: 900;
        color: #FFD23F;
        letter-spacing: -0.5px;
        line-height: 1;
    }

    .payment-total-value small {
        font-size: 14px;
        font-weight: 700;
        color: rgba(255, 210, 63, 0.6);
    }

    .payment-pay-btn {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background: linear-gradient(135deg, #FFD700 0%, #FFB800 100%);
        color: #000;
        border: none;
        padding: 16px 24px;
        border-radius: 14px;
        font-weight: 800;
        font-size: 15px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 8px 25px rgba(255, 215, 0, 0.2);
    }

    .payment-pay-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(255, 215, 0, 0.3);
        filter: brightness(1.08);
    }

    .payment-pay-btn:active {
        transform: translateY(0);
    }

    .payment-pay-btn i {
        font-size: 16px;
    }

    .payment-secure-note {
        text-align: center;
        font-size: 11px;
        color: rgba(255, 255, 255, 0.25);
        margin: 14px 0 0;
        letter-spacing: 0.3px;
    }

    .payment-secure-note i {
        color: rgba(74, 222, 128, 0.5);
        margin-right: 4px;
    }

    /* ─── MOBILE RESPONSIVE ─────────────────────────── */
    @media (max-width: 767px) {
        #activityBookingModal .modal-dialog {
            margin: 10px;
            max-height: calc(100vh - 20px);
        }
        #activityBookingModal .modal-content {
            border-radius: 18px;
            max-height: calc(100vh - 20px);
            overflow-y: auto;
        }
        #activityBookingModal .modal-body {
            padding-top: 60px !important;
            padding-left: 16px !important;
            padding-right: 16px !important;
        }
        .modal-step-indicator {
            margin-bottom: 25px;
        }
        .step-connector {
            width: 50px;
            margin: 0 12px;
        }
        .step-circle {
            width: 36px;
            height: 36px;
        }
        .step-label {
            font-size: 11px;
            letter-spacing: 1px;
        }
        .booking-section-group {
            padding: 16px;
        }
        .input-group-premium {
            min-height: 50px;
            padding: 0 14px;
        }
        .input-group-premium i {
            font-size: 16px;
            margin-right: 8px;
        }
        .form-control-premium {
            font-size: 14px;
        }
        .price-summary-card {
            padding: 18px;
        }
        .summary-total {
            font-size: 18px;
        }
        .btn-premium-book {
            padding: 14px;
            font-size: 14px;
            letter-spacing: 1px;
        }
        #activityBookingModal h5 {
            font-size: 22px !important;
        }
    }

    @media (max-width: 575px) {
        .step-connector {
            width: 35px;
            margin: 0 8px;
        }
        .step-circle {
            width: 32px;
            height: 32px;
        }
        .step-number {
            font-size: 13px;
        }
        .step-label {
            font-size: 10px;
        }
        .payment-card {
            min-width: unset;
            width: calc(100vw - 30px);
            padding: 28px 18px 24px;
        }
        .payment-total-value span {
            font-size: 22px;
        }
        .payment-row-left, .payment-row-value {
            font-size: 13px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // State
    let currentPrices = {
        adult: 0,
        child: 0,
        dubai: 0,
        abuDhabi: 0,
        abuDhabiDubai: 0,
        emirates: 0,
        txn: 0,
        taxPercent: 5.0
    };

    const bookingModalElem = document.getElementById('activityBookingModal');
    if(!bookingModalElem) return;

    const modal = new bootstrap.Modal(bookingModalElem);
    const bookingForm = document.getElementById('activityBookingForm');
    const transferSelect = document.getElementById('modalTransferSelect');
    const bookBtn = document.getElementById('modalBookNowBtn');
    
    // Open modal and fetch data
    window.openActivityBookingModal = function(id, name) {
        // Set initial UI
        document.getElementById('modalActivityId').value = id;
        document.getElementById('modalActivityName').textContent = name;
        document.getElementById('modalPriceSummary').style.display = 'none';
        transferSelect.value = '';
        
        // Reset step indicator to step 1
        updateStepIndicator(1);

        // Show modal
        modal.show();
        
        // Fetch pricing details
        fetchPricing(id);
    };

    // Card-based triggers
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.open-booking-modal');
        if(btn) {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            window.openActivityBookingModal(id, name);
        }
    });

    async function fetchPricing(id) {
        try {
            const response = await fetch(`/activity/prices/${id}`, {
                headers: { 'Accept': 'application/json' }
            });
            const text = await response.text();
            let data;
            try { data = JSON.parse(text); } catch(e) {
                throw new Error('Server returned invalid response.');
            }
            
            currentPrices = {
                adult: parseFloat(data.adultPrice) || 0,
                child: parseFloat(data.childPrice) || 0,
                dubai: parseFloat(data.dubaiPrice) || 0,
                abuDhabi: parseFloat(data.abuDhabiPrice) || 0,
                abuDhabiDubai: parseFloat(data.fromAbuDhabiToDubai) || 0,
                emirates: parseFloat(data.emirates) || 0,
                txn: parseFloat(data.txnCharges) || 0,
                taxPercent: parseFloat(data.taxPercent) || 5.0
            };
            
            calculateTotal();
            document.getElementById('modalPriceSummary').style.display = 'block';
        } catch (error) {
            console.error('Error fetching prices:', error);
            showModalSnackbar('Error loading activity prices.', 'failed');
        }
    }

    function calculateTotal() {
        const adults = parseInt(bookingForm.adults.value) || 0;
        const children = parseInt(bookingForm.childrens.value) || 0;
        const transfer = transferSelect.value;
        
        let transportPrice = 0;
        if(transfer === 'dubai') transportPrice = currentPrices.dubai;
        if(transfer === 'abu_dhabi') transportPrice = currentPrices.abuDhabi;
        if(transfer === 'abu_dhabi_to_dubai') transportPrice = currentPrices.abuDhabiDubai;
        if(transfer === 'any_emirates') transportPrice = currentPrices.emirates;

        const subtotal = (adults * currentPrices.adult) + (children * currentPrices.child) + transportPrice + currentPrices.txn;
        const vat = subtotal * (currentPrices.taxPercent / 100);
        const total = subtotal + vat;
        
        // Update Modal Display
        document.getElementById('modalDisplayAdultPrice').textContent = `${currentPrices.adult.toFixed(2)} x ${adults} = ${(currentPrices.adult * adults).toFixed(2)} AED`;
        document.getElementById('modalDisplayChildPrice').textContent = `${currentPrices.child.toFixed(2)} x ${children} = ${(currentPrices.child * children).toFixed(2)} AED`;
        
        const transRow = document.getElementById('modalDisplayTransportRow');
        if(transportPrice > 0) {
            transRow.style.setProperty('display', 'flex', 'important');
            document.getElementById('modalDisplayTransportPrice').textContent = `${transportPrice.toFixed(2)} AED`;
        } else {
            transRow.style.setProperty('display', 'none', 'important');
        }
        
        const vatRow = document.getElementById('modalDisplayVatRow');
        if(vatRow) {
            vatRow.style.setProperty('display', 'flex', 'important');
            document.getElementById('modalDisplayVatPrice').textContent = `${vat.toFixed(2)} AED`;
        }
        
        document.getElementById('modalDisplayTotalPrice').textContent = `${total.toFixed(2)} AED`;
        
        return total;
    }

    // Recalculate on input
    ['adults', 'childrens'].forEach(name => {
        bookingForm[name].addEventListener('input', calculateTotal);
    });
    transferSelect.addEventListener('change', calculateTotal);

    // Form Submission
    bookBtn.addEventListener('click', async function() {
        if(!bookingForm.checkValidity()) {
            bookingForm.reportValidity();
            return;
        }

        const total = calculateTotal();
        const adults = parseInt(bookingForm.adults.value) || 0;
        const children = parseInt(bookingForm.childrens.value) || 0;
        
        const subtotal = (adults * currentPrices.adult) + (children * currentPrices.child) + currentPrices.txn;
        const transfer = transferSelect.value;
        let transportPrice = 0;
        if(transfer === 'dubai') transportPrice = currentPrices.dubai;
        if(transfer === 'abu_dhabi') transportPrice = currentPrices.abuDhabi;
        if(transfer === 'abu_dhabi_to_dubai') transportPrice = currentPrices.abuDhabiDubai;
        if(transfer === 'any_emirates') transportPrice = currentPrices.emirates;
        
        const vat = (subtotal + transportPrice) * (currentPrices.taxPercent / 100);

        // Show Confirmation Preview
        document.getElementById('prevAdultsTotal').textContent = (adults * currentPrices.adult).toFixed(2);
        document.getElementById('prevChildrenTotal').textContent = (children * currentPrices.child).toFixed(2);
        document.getElementById('prevTxnFee').textContent = currentPrices.txn.toFixed(2);
        
        const prevVatElem = document.getElementById('prevVatTotal');
        if(prevVatElem) prevVatElem.textContent = vat.toFixed(2);
        
        document.getElementById('prevFinalTotal').textContent = total.toFixed(2);
        
        if(transportPrice > 0) {
            document.getElementById('prevTransportTotal').textContent = transportPrice.toFixed(2);
            document.getElementById('prevTransportRow').style.display = 'flex';
        } else {
            document.getElementById('prevTransportRow').style.display = 'none';
        }

        document.getElementById('modalChargePreview').style.display = 'flex';
        updateStepIndicator(2);
    });


    document.getElementById('closePrevModal').addEventListener('click', () => {
        document.getElementById('modalChargePreview').style.display = 'none';
        updateStepIndicator(1);
    });

    // Step indicator visual update
    function updateStepIndicator(step) {
        const steps = document.querySelectorAll('.modal-step-indicator .step-item');
        const connector = document.querySelector('.step-connector');
        if (step === 2) {
            steps[0].classList.remove('active');
            steps[0].classList.add('completed');
            steps[1].classList.add('active');
            if (connector) connector.classList.add('filled');
        } else {
            steps[0].classList.add('active');
            steps[0].classList.remove('completed');
            steps[1].classList.remove('active');
            if (connector) connector.classList.remove('filled');
        }
    }

    document.getElementById('modalFinalPayBtn').addEventListener('click', async function() {
        const payBtn = this;
        const originalHTML = payBtn.innerHTML;
        payBtn.disabled = true;
        payBtn.innerHTML = '<span class="btn-loader"></span> Creating Booking...';

        try {
            // Step 1: Create the booking
            const formData = new FormData(bookingForm);
            formData.append('action_type', 'book_and_pay');
            formData.append('final_payment_amount', calculateTotal());
            formData.append('currency', 'AED');

            const response = await fetch('{{ route("activity.book") }}', {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });

            const bookText = await response.text();
            let data;
            try { data = JSON.parse(bookText); } catch(e) {
                throw new Error('Server error. Please refresh the page and try again.');
            }

            if (!response.ok || !data.success) {
                const errorMsg = data.errors
                    ? Object.values(data.errors).flat().join(', ')
                    : (data.message || 'Failed to create booking.');
                throw new Error(errorMsg);
            }

            if (!data.booking_id) {
                throw new Error('Booking created but no booking ID returned.');
            }

            // Step 2: Initiate payment
            payBtn.innerHTML = '<span class="btn-loader"></span> Connecting to Payment...';

            const payResponse = await fetch('{{ route("activity.payment.initiate") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ booking_id: data.booking_id, amount: calculateTotal() })
            });

            const payText = await payResponse.text();
            let payData;
            try { payData = JSON.parse(payText); } catch(e) {
                throw new Error('Payment service error. Please try again.');
            }

            if (!payResponse.ok || !payData.success || !payData.checkout_url) {
                throw new Error(payData.error || 'Payment gateway unavailable. Please try again.');
            }

            // Step 3: Redirect to payment gateway
            payBtn.innerHTML = '<span class="btn-loader"></span> Redirecting to Payment...';
            window.location.href = payData.checkout_url;

        } catch (error) {
            console.error('Payment error:', error);
            showModalSnackbar(error.message || 'An error occurred. Please try again.', 'failed');
            payBtn.disabled = false;
            payBtn.innerHTML = originalHTML;
        }
    });

    function showModalSnackbar(message, type = 'success') {
        const bar = document.getElementById('mainSnackbar');
        if(!bar) { alert(message); return; }
        bar.textContent = message;
        bar.className = `classic-snackbar ${type} show`;
        setTimeout(() => bar.classList.remove('show'), 4000);
    }
});
</script>
