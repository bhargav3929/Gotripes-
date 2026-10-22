@extends('layouts.manager')

@section('title', 'Booking ' . $booking->order_id)
@section('page-title', 'Tour Booking')

@section('content')
@php
    $sel = $booking->selections ?? [];
    $breakdown = $booking->price_breakdown ?? [];
    $hotel = $breakdown['hotel'] ?? null;
@endphp
<div class="wp-page-header">
    <div>
        <h1 class="wp-page-title">{{ $booking->order_id }}</h1>
        <span class="wp-badge {{ ['paid' => 'wp-badge-green', 'pending' => 'wp-badge-amber'][$booking->payment_status] ?? 'wp-badge-red' }}">Payment: {{ ucfirst($booking->payment_status) }}</span>
        <span class="wp-badge {{ ['confirmed' => 'wp-badge-green', 'new' => 'wp-badge-blue'][$booking->status] ?? 'wp-badge-red' }}">{{ ucfirst($booking->status) }}</span>
    </div>
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
        <a href="{{ route('manager.tour-bookings.index') }}" class="wp-btn wp-btn-secondary"><i class="fas fa-arrow-left"></i> All bookings</a>
        @foreach(['confirmed' => 'Mark confirmed', 'cancelled' => 'Cancel booking', 'new' => 'Back to new'] as $st => $label)
            @if($booking->status !== $st)
                <form method="POST" action="{{ route('manager.tour-bookings.status', $booking->id) }}" @if($st === 'cancelled') onsubmit="return confirm('Cancel this booking? Refunds are handled separately in Nomod.')" @endif>
                    @csrf <input type="hidden" name="status" value="{{ $st }}">
                    <button class="wp-btn {{ $st === 'confirmed' ? 'wp-btn-primary' : ($st === 'cancelled' ? 'wp-btn-danger' : 'wp-btn-secondary') }}">{{ $label }}</button>
                </form>
            @endif
        @endforeach
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="wp-card"><div class="wp-card-header"><i class="fas fa-user"></i> Customer</div><div class="wp-card-body">
            <p><strong>{{ $booking->customer_name }}</strong><br>
            <a href="mailto:{{ $booking->customer_email }}">{{ $booking->customer_email }}</a><br>
            <a href="tel:{{ preg_replace('/[^\d+]/', '', $booking->customer_phone) }}">{{ $booking->customer_phone }}</a><br>
            Passport: {{ $booking->nationality }}</p>
            <p>Visa: <strong>{{ \App\Models\DestinationVisaRule::REQUIREMENTS[$booking->visa_requirement] ?? 'Not on file — confirm with the customer' }}</strong></p>
            @if($booking->special_requests)<p>Special requests: {{ $booking->special_requests }}</p>@endif
        </div></div>
        <div class="wp-card"><div class="wp-card-header"><i class="fas fa-suitcase"></i> Trip</div><div class="wp-card-body">
            <p><strong>{{ $booking->package?->title }}</strong> ({{ $booking->package?->country }})<br>
            {{ $booking->start_date->format('D d M Y') }} to {{ $booking->end_date->format('D d M Y') }} · {{ $booking->days }} days / {{ $booking->days - 1 }} nights<br>
            {{ ucfirst($booking->traveler_type) }}: {{ $booking->travellerSummary() }}@if($booking->child_ages) (children's ages {{ implode(', ', $booking->child_ages) }})@endif</p>
            <p>{{ $booking->hotel_stars }}★
            @if($hotel)
                {{ $hotel['name'] }}@if($hotel['location']), {{ $hotel['location'] }}@endif · check-in {{ $hotel['check_in'] }}, check-out {{ $hotel['check_out'] }}
            @else
                hotel
            @endif
            <br>
            Meals: {{ $sel['meal_plan_label'] ?? '—' }} · Guide: {{ $sel['guide_label'] ?? '—' }} · Transport: {{ $sel['transport_label'] ?? '—' }}<br>
            Airport pickup: {{ !empty($sel['airport_transfer']) ? 'Yes' : 'No' }} · Insurance: {{ !empty($sel['insurance']) ? 'Yes' : 'No' }}</p>
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="wp-card"><div class="wp-card-header"><i class="fas fa-receipt"></i> Price (as charged)</div><div class="wp-card-body">
            <table class="wp-table">
                <thead><tr><th>Line</th><th style="text-align:right">Net</th><th style="text-align:right">Customer paid</th></tr></thead>
                <tbody>
                @foreach($breakdown['lines'] ?? [] as $l)
                    <tr><td>{{ $l['label'] }}<br><small style="color:var(--wp-text-muted)">{{ $l['detail'] }}</small></td>
                        <td style="text-align:right; font-variant-numeric:tabular-nums">{{ number_format($l['net'], 2) }}</td>
                        <td style="text-align:right; font-variant-numeric:tabular-nums">{{ number_format($l['amount'], 2) }}</td></tr>
                @endforeach
                <tr><th>Total ({{ $booking->currency }})</th><th style="text-align:right">{{ number_format($booking->net_total, 2) }}</th><th style="text-align:right">{{ number_format($booking->total, 2) }}</th></tr>
                <tr><td colspan="2">Your margin ({{ rtrim(rtrim(number_format($breakdown['margin_percent'] ?? 0, 2), '0'), '.') }}%)</td><td style="text-align:right">{{ number_format($booking->total - $booking->net_total, 2) }}</td></tr>
                </tbody>
            </table>
            @if(!empty($breakdown['season']))<p class="wp-form-help">Peak season applied: {{ $breakdown['season'] }}.</p>@endif
        </div></div>
    </div>
</div>
@endsection
