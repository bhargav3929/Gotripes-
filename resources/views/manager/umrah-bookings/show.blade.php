@extends('layouts.manager')

@section('title', 'Booking #' . ($booking->order_id ?? $booking->id))
@section('page-title', 'Booking Details')

@section('content')

<div class="wp-page-header">
    <div>
        <a href="{{ route('manager.umrah-bookings.index') }}" class="wp-btn wp-btn-secondary wp-btn-sm" style="margin-bottom:10px;">
            <i class="fas fa-arrow-left"></i> All Bookings
        </a>
        <h2 style="margin:0; font-size:18px; color:var(--wp-text);">
            Order <code style="color:var(--wp-primary);">{{ $booking->order_id ?? '#' . $booking->id }}</code>
        </h2>
        <span style="font-size:12px; color:var(--wp-text-muted);">Booked {{ $booking->created_at?->format('d M Y, H:i') ?? '—' }}</span>
    </div>
    <div>
        @php
            $statusColors = [
                'pending'   => '#eab308',
                'paid'      => '#22c55e',
                'cancelled' => '#ef4444',
                'refunded'  => '#9ca3af',
            ];
            $sc = $statusColors[$booking->payment_status] ?? '#9ca3af';
        @endphp
        <span style="display:inline-block; padding:8px 20px; border-radius:50px; font-size:13px; font-weight:800; letter-spacing:0.5px; background:{{ $sc }}22; color:{{ $sc }}; border:1px solid {{ $sc }}55;">
            {{ strtoupper($booking->payment_status) }}
        </span>
    </div>
</div>

@if(session('success'))
<div style="padding:12px 16px; background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.3); border-radius:8px; color:#22c55e; margin-bottom:18px;">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
</div>
@endif

<div class="row g-4">

    {{-- Left: Booking Summary --}}
    <div class="col-lg-8">
        {{-- Package + Trip Info --}}
        <div class="wp-card" style="margin-bottom:16px; padding:20px;">
            <h3 style="font-size:14px; font-weight:700; color:var(--wp-text-muted); text-transform:uppercase; letter-spacing:0.4px; margin-bottom:14px;">
                <i class="fas fa-kaaba" style="color:var(--wp-primary); margin-right:7px;"></i>Trip Details
            </h3>
            <div class="row g-3">
                <div class="col-sm-6">
                    <span style="font-size:11px; color:var(--wp-text-muted);">Package</span>
                    <div style="font-weight:700; color:var(--wp-text); margin-top:3px;">{{ $booking->package?->title ?? '—' }}</div>
                </div>
                <div class="col-sm-3">
                    <span style="font-size:11px; color:var(--wp-text-muted);">Departure</span>
                    <div style="font-weight:700; color:var(--wp-text); margin-top:3px;">{{ $booking->departure_date?->format('d M Y') ?? '—' }}</div>
                </div>
                <div class="col-sm-3">
                    <span style="font-size:11px; color:var(--wp-text-muted);">Duration</span>
                    <div style="font-weight:700; color:var(--wp-text); margin-top:3px;">{{ $booking->package?->duration ?? '—' }}</div>
                </div>
                <div class="col-sm-3">
                    <span style="font-size:11px; color:var(--wp-text-muted);">Adults</span>
                    <div style="font-weight:700; color:var(--wp-text); margin-top:3px;">{{ $booking->adults }}</div>
                </div>
                <div class="col-sm-3">
                    <span style="font-size:11px; color:var(--wp-text-muted);">Children</span>
                    <div style="font-weight:700; color:var(--wp-text); margin-top:3px;">{{ $booking->children ?? 0 }}</div>
                </div>
                <div class="col-sm-3">
                    <span style="font-size:11px; color:var(--wp-text-muted);">Infants</span>
                    <div style="font-weight:700; color:var(--wp-text); margin-top:3px;">{{ $booking->infants ?? 0 }}</div>
                </div>
                <div class="col-sm-3">
                    <span style="font-size:11px; color:var(--wp-text-muted);">Total Pax</span>
                    <div style="font-weight:700; color:var(--wp-primary); margin-top:3px;">{{ ($booking->adults ?? 0) + ($booking->children ?? 0) + ($booking->infants ?? 0) }}</div>
                </div>
            </div>
        </div>

        {{-- Customer Info --}}
        <div class="wp-card" style="margin-bottom:16px; padding:20px;">
            <h3 style="font-size:14px; font-weight:700; color:var(--wp-text-muted); text-transform:uppercase; letter-spacing:0.4px; margin-bottom:14px;">
                <i class="fas fa-user" style="color:var(--wp-primary); margin-right:7px;"></i>Customer Details
            </h3>
            <div class="row g-3">
                <div class="col-sm-4">
                    <span style="font-size:11px; color:var(--wp-text-muted);">Name</span>
                    <div style="font-weight:700; color:var(--wp-text); margin-top:3px;">{{ $booking->customer_name }}</div>
                </div>
                <div class="col-sm-4">
                    <span style="font-size:11px; color:var(--wp-text-muted);">Email</span>
                    <div style="font-weight:600; color:var(--wp-text); margin-top:3px;">
                        <a href="mailto:{{ $booking->customer_email }}" style="color:var(--wp-primary);">{{ $booking->customer_email }}</a>
                    </div>
                </div>
                <div class="col-sm-4">
                    <span style="font-size:11px; color:var(--wp-text-muted);">Phone</span>
                    <div style="font-weight:700; color:var(--wp-text); margin-top:3px;">{{ $booking->customer_phone }}</div>
                </div>
            </div>
        </div>

        {{-- Passenger Details --}}
        @if($booking->passenger_details && count($booking->passenger_details))
        <div class="wp-card" style="padding:20px;">
            <h3 style="font-size:14px; font-weight:700; color:var(--wp-text-muted); text-transform:uppercase; letter-spacing:0.4px; margin-bottom:14px;">
                <i class="fas fa-users" style="color:var(--wp-primary); margin-right:7px;"></i>Passenger Details
            </h3>
            <div class="table-responsive">
                <table class="wp-table" style="font-size:13px;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Nationality</th>
                            <th>Passport No.</th>
                            <th>Date of Birth</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($booking->passenger_details as $i => $pax)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $pax['name'] ?? '—' }}</td>
                            <td>{{ $pax['nationality'] ?? '—' }}</td>
                            <td><code style="font-size:11px;">{{ $pax['passport_number'] ?? '—' }}</code></td>
                            <td>{{ $pax['dob'] ?? '—' }}</td>
                            <td>
                                <span class="wp-badge" style="font-size:10px; background:rgba(255,215,0,0.1); color:var(--wp-primary);">
                                    {{ ucfirst($pax['type'] ?? 'adult') }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    {{-- Right: Payment & Actions --}}
    <div class="col-lg-4">
        {{-- Payment Summary --}}
        <div class="wp-card" style="padding:20px; margin-bottom:16px;">
            <h3 style="font-size:14px; font-weight:700; color:var(--wp-text-muted); text-transform:uppercase; letter-spacing:0.4px; margin-bottom:14px;">
                <i class="fas fa-credit-card" style="color:var(--wp-primary); margin-right:7px;"></i>Payment
            </h3>
            <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                <span style="color:var(--wp-text-muted);">Total Amount</span>
                <strong style="color:var(--wp-text);">AED {{ number_format($booking->total_price, 2) }}</strong>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                <span style="color:var(--wp-text-muted);">Gateway</span>
                <span style="color:var(--wp-text-secondary);">{{ $booking->payment_gateway ?? '—' }}</span>
            </div>
            @if($booking->installment_months && $booking->installment_months > 1)
            <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                <span style="color:var(--wp-text-muted);">Instalments</span>
                <span style="color:var(--wp-text-secondary);">{{ $booking->installment_months }}× of AED {{ number_format($booking->total_price / $booking->installment_months, 2) }}</span>
            </div>
            @endif
            <div style="height:1px; background:var(--wp-border-light); margin:14px 0;"></div>
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <span style="font-weight:700; color:var(--wp-text);">Status</span>
                <span style="padding:5px 14px; border-radius:50px; font-size:12px; font-weight:800; background:{{ $sc }}22; color:{{ $sc }};">
                    {{ ucfirst($booking->payment_status) }}
                </span>
            </div>
        </div>

        {{-- Update Status --}}
        <div class="wp-card" style="padding:20px;">
            <h3 style="font-size:14px; font-weight:700; color:var(--wp-text-muted); text-transform:uppercase; letter-spacing:0.4px; margin-bottom:14px;">
                <i class="fas fa-pen" style="color:var(--wp-primary); margin-right:7px;"></i>Update Status
            </h3>
            <form action="{{ route('manager.umrah-bookings.status', $booking->id) }}" method="POST">
                @csrf
                <div style="margin-bottom:12px;">
                    <label style="font-size:11px; font-weight:600; color:var(--wp-text-muted); text-transform:uppercase; display:block; margin-bottom:6px;">New Status</label>
                    <select name="payment_status" class="wp-select">
                        @foreach(['pending'=>'Pending','paid'=>'Paid','cancelled'=>'Cancelled','refunded'=>'Refunded'] as $val => $label)
                            <option value="{{ $val }}" {{ $booking->payment_status == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="wp-btn wp-btn-primary w-100">
                    <i class="fas fa-save me-2"></i> Update Status
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
