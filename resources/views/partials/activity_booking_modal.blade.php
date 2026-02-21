<div class="modal fade" id="activityBookingModal" tabindex="-1" aria-labelledby="activityBookingModalLabel" aria-hidden="true" style="z-index: 10001;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <button type="button" class="btn-close-luxury" data-bs-dismiss="modal">
                <i class="bi bi-x-lg"></i>
            </button>
            <div class="modal-body pt-5 px-4">
                {{-- Step Indicator --}}
                <div class="modal-step-indicator">
                    <div class="step-item active">
                        <div class="step-dot">1</div>
                        <span class="step-label">Details</span>
                    </div>
                    <div class="step-item">
                        <div class="step-dot">2</div>
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
                                    <input type="number" name="childrens" class="form-control-premium" placeholder="Children (6-17)" min="0" max="50">
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
    </div>
</div>

<!-- Modal Pricing Overlay (Secondary Modal for Confirmation before payment) -->
<div id="modalChargePreview" style="display:none; position:fixed; left:0; top:0; width:100vw; height:100vh; z-index:10002; justify-content:center; align-items:center; background:rgba(0,0,0,0.85);">
    <div style="background:#181818; color:#fff; padding:30px; border-radius:14px; min-width:320px; max-width:90vw; border: 1px solid #FFD23F;">
        <h3 style="margin-bottom:20px; font-size:1.4rem; text-align:center; color: #FFD23F;">Confirm Payment</h3>
        <table style="width:100%; border-collapse:collapse; font-size:16px; margin-bottom:20px;">
            <tbody>
                <tr><td style="padding:8px 0;">Adults Total</td><td style="text-align:right;" id="prevAdultsTotal">0.00</td></tr>
                <tr><td style="padding:8px 0;">Children Total</td><td style="text-align:right;" id="prevChildrenTotal">0.00</td></tr>
                <tr id="prevTransportRow" style="display:none;"><td style="padding:8px 0;">Transport</td><td style="text-align:right;" id="prevTransportTotal">0.00</td></tr>
                <tr><td style="padding:8px 0;">Transaction Fee</td><td style="text-align:right;" id="prevTxnFee">0.00</td></tr>
                <tr><td style="padding:8px 0;">VAT (5%)</td><td style="text-align:right;" id="prevVatTotal">0.00</td></tr>
                <tr style="border-top:1px solid #FFD23F; font-weight:bold; color: #FFD23F; font-size: 1.2rem;">
                    <td style="padding:12px 0;">Final Total</td>
                    <td style="text-align:right;"><span id="prevFinalTotal">0.00</span> AED</td>
                </tr>
            </tbody>
        </table>
        <div class="d-flex gap-2">
            <button type="button" id="closePrevModal" style="flex: 1; background:#444; color:#fff; padding:10px; border:none; border-radius:5px;">Back</button>
            <button type="button" id="modalFinalPayBtn" style="flex: 1; background:#FFD23F; color:#000; padding:10px; border:none; border-radius:5px; font-weight:bold;">PAY NOW</button>
        </div>
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

    /* Modal Step Indicator */
    .modal-step-indicator {
        display: flex;
        justify-content: space-between;
        padding: 0 100px;
        margin-bottom: 45px;
        position: relative;
    }
    .modal-step-indicator::before {
        content: '';
        position: absolute;
        top: 16px;
        left: 130px;
        right: 130px;
        height: 2px;
        background: rgba(255,255,255,0.08);
        z-index: 1;
    }
    .step-item {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }
    .step-dot {
        width: 32px;
        height: 32px;
        background: #1a1a1a;
        border: 2px solid rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 800;
        color: rgba(255,255,255,0.4);
        transition: all 0.4s ease;
    }
    .step-item.active .step-dot {
        background: #FFD23F;
        border-color: #FFD23F;
        color: #000;
        box-shadow: 0 0 15px rgba(255, 210, 63, 0.4);
    }
    .step-label {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 700;
        color: rgba(255,255,255,0.4);
    }
    .step-item.active .step-label { color: #FFD23F; }

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
            padding: 0 40px;
            margin-bottom: 30px;
        }
        .modal-step-indicator::before {
            left: 70px;
            right: 70px;
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
        .modal-step-indicator {
            padding: 0 20px;
        }
        .modal-step-indicator::before {
            left: 50px;
            right: 50px;
        }
        #modalChargePreview > div {
            padding: 20px 15px;
            min-width: unset;
            width: calc(100vw - 30px);
        }
        #modalChargePreview table {
            font-size: 14px;
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
            const response = await fetch(`/activity/prices/${id}`);
            const data = await response.json();
            
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
            document.getElementById('prevTransportRow').style.display = 'table-row';
        } else {
            document.getElementById('prevTransportRow').style.display = 'none';
        }

        document.getElementById('modalChargePreview').style.display = 'flex';
    });


    document.getElementById('closePrevModal').addEventListener('click', () => {
        document.getElementById('modalChargePreview').style.display = 'none';
    });

    document.getElementById('modalFinalPayBtn').addEventListener('click', async function() {
        const payBtn = this;
        const originalText = payBtn.textContent;
        payBtn.disabled = true;
        payBtn.innerHTML = '<span class="btn-loader"></span>Processing...';

        try {
            const formData = new FormData(bookingForm);
            formData.append('action_type', 'book_and_pay');
            formData.append('final_payment_amount', calculateTotal());
            formData.append('currency', 'AED');

            const response = await fetch('{{ route("activity.book") }}', {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });

            const data = await response.json();
            
            if (data.success && data.booking_id) {
                // Initiate Payment
                const payResponse = await fetch('{{ route("activity.payment.initiate") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ booking_id: data.booking_id, amount: calculateTotal() })
                });
                
                const payData = await payResponse.json();
                if (payData.success && payData.checkout_url) {
                    window.location.href = payData.checkout_url;
                    return;
                }
            }
            throw new Error(data.message || 'Payment failed to initiate.');
        } catch (error) {
            console.error('Submission error:', error);
            showModalSnackbar(error.message || 'An error occurred. Please try again.', 'failed');
            payBtn.disabled = false;
            payBtn.textContent = originalText;
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
