@extends('layouts.manager')

@section('title', 'eSIM Order ' . ($order->order_reference ?: '#'.$order->id))
@section('page-title', 'eSIM Order ' . ($order->order_reference ?: '#'.$order->id))

@section('content')
@php
    $needsProvisioning = $order->payment_status === 'paid' && ! $order->monty_order_id;

    // Reason the provider gave for refusing to issue the eSIM. Without this an
    // agent sees "assign_failed" and has to go digging through laravel.log to
    // find out whether it was a funding problem, a dead bundle, or an outage.
    $failure = is_array($order->monty_response ?? null) && ($order->monty_response['failed'] ?? false)
        ? $order->monty_response
        : null;
@endphp

<div class="orders-toolbar">
    <a href="{{ route('manager.orders.esim') }}" class="orders-btn orders-btn-ghost">
        <i class="fas fa-arrow-left"></i> Back to eSIM orders
    </a>

    @if($order->monty_order_id)
        <form method="POST" action="{{ route('manager.orders.esim.resend-qr', $order->id) }}" class="d-inline">
            @csrf
            <button type="submit" class="orders-btn">
                <i class="fas fa-envelope"></i> Resend QR code email
            </button>
        </form>
    @endif

    @if($needsProvisioning)
        <form method="POST" action="{{ route('manager.orders.esim.retry', $order->id) }}" class="d-inline"
              onsubmit="return confirm('Retry provisioning for {{ $order->customer_email }}? This charges the reseller wallet and emails the customer their QR code.');">
            @csrf
            <button type="submit" class="orders-btn">
                <i class="fas fa-rotate-right"></i> Retry provisioning
            </button>
        </form>
    @endif
</div>

@if($needsProvisioning)
    <div class="wp-notice wp-notice-error">
        <span>
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>This customer has paid but has no eSIM.</strong>
            Provisioning {{ $order->reservation_status === 'pending' ? 'has not run' : 'failed ('.e($order->reservation_status).')' }},
            so no QR code was sent. Use <strong>Retry provisioning</strong> above.
        </span>
    </div>

    @if($failure)
        <div class="wp-notice wp-notice-error">
            <span>
                <i class="fas fa-circle-info me-2"></i>
                <strong>MontyeSIM said:</strong> {{ $failure['error'] }}
                @if(!empty($failure['failed_at']))
                    <span style="opacity:.75;">({{ \Carbon\Carbon::parse($failure['failed_at'])->diffForHumans() }})</span>
                @endif
                @if(str_contains(strtolower($failure['error']), 'insufficient balance'))
                    <br><em>Top up the reseller wallet in the MontyeSIM portal, then retry.</em>
                @endif
            </span>
        </div>
    @endif
@endif

<div class="orders-detail-grid">
    <div class="orders-card orders-detail-card">
        <h3>Customer</h3>
        <ul class="orders-detail-list">
            <li><span class="label">Name</span>  <span class="value">{{ $order->customer_name ?: '—' }}</span></li>
            <li><span class="label">Email</span> <span class="value">{{ $order->customer_email ?: '—' }}</span></li>
            <li><span class="label">Phone</span> <span class="value">{{ $order->customer_phone ?: '—' }}</span></li>
        </ul>
    </div>

    <div class="orders-card orders-detail-card">
        <h3>Bundle</h3>
        <ul class="orders-detail-list">
            <li><span class="label">Country</span>      <span class="value">{{ $order->country_name ?: '—' }} ({{ $order->country_code ?: '—' }})</span></li>
            <li><span class="label">Bundle</span>       <span class="value">{{ $order->bundle_name ?: '—' }}</span></li>
            <li><span class="label">Bundle code</span>  <span class="value">{{ $order->bundle_code ?: '—' }}</span></li>
            <li><span class="label">Data</span>         <span class="value">{{ $order->data_amount ?: '—' }}</span></li>
            <li><span class="label">Validity</span>     <span class="value">{{ $order->validity_days ? $order->validity_days.' days' : '—' }}</span></li>
        </ul>
    </div>

    <div class="orders-card orders-detail-card">
        <h3>Payment</h3>
        <ul class="orders-detail-list">
            <li><span class="label">Selling price</span>  <span class="value">{{ number_format((float) $order->selling_price, 2) }} {{ $order->currency ?: 'AED' }}</span></li>
            <li><span class="label">Cost price</span>     <span class="value">{{ number_format((float) $order->monty_cost_price, 2) }} {{ $order->currency ?: 'AED' }}</span></li>
            <li><span class="label">Margin</span>         <span class="value">{{ number_format((float) $order->selling_price - (float) $order->monty_cost_price, 2) }} {{ $order->currency ?: 'AED' }}</span></li>
            <li><span class="label">Payment status</span> <span class="value">{{ ucfirst($order->payment_status ?: 'pending') }}</span></li>
        </ul>
    </div>

    <div class="orders-card orders-detail-card">
        <h3>Provisioning</h3>
        <ul class="orders-detail-list">
            <li><span class="label">Order reference</span>     <span class="value">{{ $order->order_reference ?: '—' }}</span></li>
            <li><span class="label">Reservation status</span>  <span class="value">{{ $order->reservation_status ?: '—' }}</span></li>
            <li><span class="label">Monty order ID</span>      <span class="value">{{ $order->monty_order_id ?: '—' }}</span></li>
            <li><span class="label">ICCID</span>               <span class="value">{{ $order->monty_iccid ?: '—' }}</span></li>
            <li><span class="label">Created</span>             <span class="value">{{ $order->created_at?->format('d M Y H:i') ?: '—' }}</span></li>
        </ul>
    </div>
</div>
@endsection
