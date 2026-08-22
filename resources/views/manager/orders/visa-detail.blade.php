@extends('layouts.manager')

@section('title', 'Visa Application #' . $application->id)
@section('page-title', 'Visa Application #' . $application->id)

@section('content')
<div class="orders-toolbar">
    <a href="{{ route('manager.orders.visa') }}" class="orders-btn orders-btn-ghost">
        <i class="fas fa-arrow-left"></i> Back to applications
    </a>
</div>

<div class="orders-detail-grid">
    <div class="orders-card orders-detail-card">
        <h3>Applicant</h3>
        <ul class="orders-detail-list">
            <li><span class="label">First name</span>     <span class="value">{{ $application->UAEV_first_name ?: '—' }}</span></li>
            <li><span class="label">Last name</span>      <span class="value">{{ $application->UAEV_last_name ?: '—' }}</span></li>
            <li><span class="label">Passport number</span> <span class="value">{{ $application->UAEV_passport_number ?: '—' }}</span></li>
            <li><span class="label">Gender</span>         <span class="value">{{ $application->UAEV_gender ?: '—' }}</span></li>
            <li><span class="label">Date of birth</span>  <span class="value">{{ optional($application->UAEV_dob)?->format('d M Y') ?: '—' }}</span></li>
            <li><span class="label">Marital status</span> <span class="value">{{ $application->UAEV_marital_status ?: '—' }}</span></li>
            <li><span class="label">Profession</span>     <span class="value">{{ $application->UAEV_profession ?: '—' }}</span></li>
        </ul>
    </div>

    <div class="orders-card orders-detail-card">
        <h3>Contact</h3>
        <ul class="orders-detail-list">
            <li><span class="label">Email</span>       <span class="value">{{ $application->UAEV_email ?: '—' }}</span></li>
            <li><span class="label">Phone</span>       <span class="value">{{ $application->UAEV_phone ?: '—' }}</span></li>
            <li><span class="label">Nationality</span> <span class="value">{{ $application->UAEV_nationality ?: '—' }}</span></li>
        </ul>
    </div>

    <div class="orders-card orders-detail-card">
        <h3>Travel</h3>
        <ul class="orders-detail-list">
            @if($application->UAEV_emirate)
                <li><span class="label">Selected Emirate</span>  <span class="value">{{ $application->UAEV_emirate }}</span></li>
            @endif
            @if($application->UAEV_package_name)
                <li><span class="label">Visa Package</span>      <span class="value">{{ $application->UAEV_package_name }}</span></li>
            @endif
            @if($application->UAEV_visa_type)
                <li><span class="label">Entry Type</span>        <span class="value">{{ $application->UAEV_visa_type }}</span></li>
            @endif
            @if($application->UAEV_traveller_type)
                <li><span class="label">Traveller Type</span>    <span class="value">{{ $application->UAEV_traveller_type }}</span></li>
            @endif
            <li><span class="label">Visa duration</span>     <span class="value">{{ $application->UAEV_visaDuration ?: '—' }}</span></li>
            <li><span class="label">Arrival date</span>      <span class="value">{{ optional($application->UAEV_arrival_date)?->format('d M Y') ?: '—' }}</span></li>
            <li><span class="label">Departure date</span>    <span class="value">{{ optional($application->UAEV_departure_date)?->format('d M Y') ?: '—' }}</span></li>
            <li><span class="label">Passport valid 6m+</span><span class="value">{{ $application->UAEV_passport_valid ? 'Yes' : 'No' }}</span></li>
            <li><span class="label">Will not overstay</span> <span class="value">{{ $application->UAEV_not_stay_long ? 'Yes' : 'No' }}</span></li>
            @if($application->UAEV_addons)
                <li><span class="label">Selected Add-ons</span>
                    <span class="value">
                        @php
                            $addons = json_decode($application->UAEV_addons, true) ?: [];
                        @endphp
                        @if(!empty($addons))
                            {{ implode(', ', array_map('ucfirst', $addons)) }}
                        @else
                            None
                        @endif
                    </span>
                </li>
            @endif
        </ul>
    </div>

    <div class="orders-card orders-detail-card">
        <h3>Documents & Status</h3>
        <ul class="orders-detail-list">
            <li><span class="label">Passport copy</span>
                <span class="value">
                    @if($application->UAEV_passport_copy)
                        <a href="{{ asset('storage/' . $application->UAEV_passport_copy) }}" target="_blank" style="color:#FFD700;">View file</a>
                    @else — @endif
                </span>
            </li>
            <li><span class="label">Passport photo</span>
                <span class="value">
                    @if($application->UAEV_passport_photo)
                        <a href="{{ asset('storage/' . $application->UAEV_passport_photo) }}" target="_blank" style="color:#FFD700;">View file</a>
                    @else — @endif
                </span>
            </li>
            <li><span class="label">Airline ticket</span>
                <span class="value">
                    @if($application->UAEV_airline_ticket)
                        <a href="{{ asset('storage/' . $application->UAEV_airline_ticket) }}" target="_blank" style="color:#FFD700;">View file</a>
                    @else — @endif
                </span>
            </li>
            <li><span class="label">Price</span>          <span class="value">AED {{ number_format($application->UAEV_price ?: 0, 2) }}</span></li>
            @if($application->UAEV_deposit_amount > 0)
                <li><span class="label" style="color: #FFD700;">Security Deposit</span> <span class="value" style="color: #FFD700;">AED {{ number_format($application->UAEV_deposit_amount, 2) }}</span></li>
                <li><span class="label" style="color: #22c55e;">Refund Amount</span>   <span class="value" style="color: #22c55e;">AED {{ number_format($application->UAEV_refund_amount, 2) }}</span></li>
            @endif
            <li><span class="label">Status</span>         <span class="value">{{ $application->UAEV_status ?: '—' }}</span></li>
            <li><span class="label">Submitted</span>      <span class="value">{{ optional($application->UAEV_created_date)?->format('d M Y H:i') ?: '—' }}</span></li>
        </ul>
    </div>

    @if(strtolower($application->UAEV_emirate) === 'sharjah' || $application->UAEV_bank_account_holder)
    <div class="orders-card orders-detail-card">
        <h3>Refund Bank Details</h3>

        {{-- One selectable block rather than a bulleted list. Staff issuing a
             refund copy the whole lot in a single go into WhatsApp or the bank
             portal; with <li> rows they were picking it up a line at a time,
             which is exactly what the client asked us to stop.

             Only the account details live in here. Refund status and the
             mark-as-paid action stay outside it — they are ours to track, and
             pasting "Refund Status: Pending" into a bank transfer would be
             noise at best. --}}
        @php
            $bankText = collect([
                    'Holder Name'    => $application->UAEV_bank_account_holder,
                    'Bank Name'      => $application->UAEV_bank_name,
                    'Account / IBAN' => $application->UAEV_bank_account_number,
                    'SWIFT Code'     => $application->UAEV_bank_swift_code,
                ])
                ->filter(fn ($value) => filled($value))
                ->map(fn ($value, $label) => $label . ': ' . $value)
                ->implode("\n");
        @endphp

        @if($bankText !== '')
            <p id="refundBankText"
               style="white-space:pre-line; margin:0 0 12px; padding:14px 16px;
                      background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.10);
                      border-radius:10px; color:#e6e6e6; font-size:14px; line-height:1.7;">{{ $bankText }}</p>
            <button type="button" class="orders-btn orders-btn-ghost" onclick="copyRefundBankDetails(this)">
                <i class="fas fa-copy"></i> Copy bank details
            </button>
        @else
            <p style="margin:0; color:#999;">The customer did not provide bank details.</p>
        @endif

        @if($application->UAEV_refund_amount > 0)
            <ul class="orders-detail-list" style="margin-top:16px;">
                <li>
                    <span class="label">Refund Status</span>
                    <span class="value">
                        @if($application->UAEV_refund_status === 'refunded')
                            <span style="color:#22c55e;">Refunded {{ optional($application->UAEV_refunded_at)->format('d M Y H:i') }}</span>
                        @else
                            <span style="color:#FFD700;">Pending</span>
                        @endif
                    </span>
                </li>
                @if($application->UAEV_refund_status !== 'refunded')
                    <li>
                        <form action="{{ route('manager.orders.visa.refund', $application->id) }}" method="POST" onsubmit="return confirm('Mark this AED {{ number_format($application->UAEV_refund_amount, 2) }} refund as paid to the bank details above?');">
                            @csrf
                            <button type="submit" class="orders-btn orders-btn-primary">Mark Refund as Paid</button>
                        </form>
                    </li>
                @endif
            </ul>
        @endif
    </div>
    @endif
</div>

<script>
    // Copies the refund account in one click. navigator.clipboard needs a secure
    // context, which production has but a plain-HTTP staging box would not, so
    // there is a execCommand fallback rather than a button that silently does
    // nothing. Either way the text stays selectable by hand.
    function copyRefundBankDetails(button) {
        var block = document.getElementById('refundBankText');
        if (!block) return;

        var text = block.innerText;
        var done = function () {
            var original = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i> Copied';
            setTimeout(function () { button.innerHTML = original; }, 1800);
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(done, function () { fallback(text, done); });
        } else {
            fallback(text, done);
        }

        function fallback(value, onDone) {
            var area = document.createElement('textarea');
            area.value = value;
            area.setAttribute('readonly', '');
            area.style.position = 'fixed';
            area.style.opacity = '0';
            document.body.appendChild(area);
            area.select();
            try { document.execCommand('copy'); onDone(); } catch (e) { /* leave it to manual selection */ }
            document.body.removeChild(area);
        }
    }
</script>
@endsection
