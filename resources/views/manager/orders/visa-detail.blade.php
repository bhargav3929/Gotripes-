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
        <ul class="orders-detail-list">
            <li><span class="label">Holder Name</span>    <span class="value">{{ $application->UAEV_bank_account_holder ?: '—' }}</span></li>
            <li><span class="label">Bank Name</span>      <span class="value">{{ $application->UAEV_bank_name ?: '—' }}</span></li>
            <li><span class="label">Account / IBAN</span> <span class="value">{{ $application->UAEV_bank_account_number ?: '—' }}</span></li>
            <li><span class="label">SWIFT Code</span>     <span class="value">{{ $application->UAEV_bank_swift_code ?: '—' }}</span></li>
        </ul>
    </div>
    @endif
</div>
@endsection
