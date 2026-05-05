@extends('layouts.manager')

@section('title', 'Booking #' . $booking->id)
@section('page-title', 'Activity Booking #' . $booking->id)

@section('content')
<div class="orders-toolbar">
    <a href="{{ route('manager.orders.activities') }}" class="orders-btn orders-btn-ghost">
        <i class="fas fa-arrow-left"></i> Back to bookings
    </a>
</div>

<div class="orders-detail-grid">
    <div class="orders-card orders-detail-card">
        <h3>Customer</h3>
        <ul class="orders-detail-list">
            <li><span class="label">Name</span>      <span class="value">{{ $booking->name ?: '—' }}</span></li>
            <li><span class="label">Email</span>     <span class="value">{{ $booking->email ?: '—' }}</span></li>
            <li><span class="label">Phone</span>     <span class="value">{{ $booking->phone ?: '—' }}</span></li>
            <li><span class="label">Nationality</span><span class="value">{{ $booking->nationality ?: '—' }}</span></li>
            <li><span class="label">Address</span>   <span class="value">{{ $booking->address ?: '—' }}</span></li>
        </ul>
    </div>

    <div class="orders-card orders-detail-card">
        <h3>Booking</h3>
        <ul class="orders-detail-list">
            <li><span class="label">Activity</span>  <span class="value">{{ $activity?->activityName ?: 'ID '.$booking->activityId }}</span></li>
            <li><span class="label">Date</span>      <span class="value">{{ optional($booking->date)->format('d M Y') ?: '—' }}</span></li>
            <li><span class="label">Adults</span>    <span class="value">{{ $booking->adults ?? 0 }}</span></li>
            <li><span class="label">Children</span>  <span class="value">{{ $booking->childrens ?? 0 }}</span></li>
            <li><span class="label">Transfer</span>  <span class="value">{{ $booking->transfer ?: '—' }}</span></li>
            <li><span class="label">Remarks</span>   <span class="value">{{ $booking->remarks ?: '—' }}</span></li>
        </ul>
    </div>

    <div class="orders-card orders-detail-card">
        <h3>Payment</h3>
        <ul class="orders-detail-list">
            <li><span class="label">Amount</span>             <span class="value">{{ number_format((float) $booking->amount, 2) }} {{ $booking->currency ?: 'AED' }}</span></li>
            <li><span class="label">Transport charges</span>  <span class="value">{{ number_format((float) ($booking->transportCharges ?? 0), 2) }} {{ $booking->currency ?: 'AED' }}</span></li>
            <li><span class="label">Method</span>             <span class="value">{{ $booking->paymentOption ?: '—' }}</span></li>
            <li><span class="label">Status</span>             <span class="value">{{ $booking->status ?: $booking->paymentOption ?: '—' }}</span></li>
            <li><span class="label">Booked on</span>          <span class="value">{{ optional($booking->createDate)->format('d M Y H:i') ?: '—' }}</span></li>
        </ul>
    </div>

    @if($activity)
    <div class="orders-card orders-detail-card">
        <h3>Supplier</h3>
        <ul class="orders-detail-list">
            <li><span class="label">Name</span>     <span class="value">{{ $activity->supplierName ?: '—' }}</span></li>
            <li><span class="label">Email</span>    <span class="value">{{ $activity->supplierEmail ?: '—' }}</span></li>
            <li><span class="label">Location</span> <span class="value">{{ $activity->activityLocation ?: '—' }}</span></li>
            <li><span class="label">Category</span> <span class="value">{{ $activity->activityCategory ?: '—' }}</span></li>
        </ul>
    </div>
    @endif
</div>
@endsection
