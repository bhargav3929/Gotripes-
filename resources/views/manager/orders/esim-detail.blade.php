@extends('layouts.manager')

@section('title', 'eSIM Order ' . ($order->order_reference ?: '#'.$order->id))
@section('page-title', 'eSIM Order ' . ($order->order_reference ?: '#'.$order->id))

@section('content')
@php
    // "Needs provisioning" is a per-eSIM question, not a per-order one. Judging
    // it by the parent's monty_order_id — which mirrors the first unit — hid the
    // Retry button on exactly the orders that needed it: a group booking where
    // some travellers got an eSIM and some did not.
    $orderQty     = $order->unitCount();
    $issuedUnits  = $order->units()->whereNotNull('monty_order_id')->count();
    $outstanding  = $orderQty - ($issuedUnits ?: ($order->monty_order_id ? $orderQty : 0));

    $needsProvisioning = $order->payment_status === 'paid' && $outstanding > 0;

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
            @if($orderQty > 1 && $issuedUnits > 0)
                <strong>This customer paid for {{ $orderQty }} eSIMs but only {{ $issuedUnits }} were issued.</strong>
                {{ $outstanding }} {{ Str::plural('traveller', $outstanding) }} {{ $outstanding === 1 ? 'has' : 'have' }} nothing.
                <strong>Retry provisioning</strong> above issues only the missing ones — the
                {{ $issuedUnits }} already bought are not charged again.
            @else
                <strong>This customer has paid but has no eSIM.</strong>
                Provisioning {{ $order->reservation_status === 'pending' ? 'has not run' : 'failed ('.e($order->reservation_status).')' }},
                so no QR code was sent. Use <strong>Retry provisioning</strong> above.
            @endif
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
        @php
            // selling_price is the ORDER TOTAL, monty_cost_price is PER UNIT, so
            // the cost has to be multiplied out before they can be subtracted.
            // Comparing them directly overstated the margin on a group order by
            // the cost of every eSIM after the first.
            $qty       = $order->unitCount();
            $unitSell  = (float) ($order->unit_selling_price ?: $order->selling_price);
            $unitCost  = (float) $order->monty_cost_price;
            $totalSell = (float) $order->selling_price;
            $totalCost = $unitCost * $qty;
            $cur       = $order->currency ?: 'AED';
        @endphp
        <ul class="orders-detail-list">
            @if($qty > 1)
                <li><span class="label">Quantity</span>       <span class="value">{{ $qty }} eSIMs</span></li>
                <li><span class="label">Unit price</span>     <span class="value">{{ number_format($unitSell, 2) }} {{ $cur }} each</span></li>
            @endif
            <li><span class="label">Selling price</span>  <span class="value">{{ number_format($totalSell, 2) }} {{ $cur }}{{ $qty > 1 ? ' total' : '' }}</span></li>
            <li><span class="label">Cost price</span>     <span class="value">{{ number_format($totalCost, 2) }} {{ $cur }}{{ $qty > 1 ? ' ('.number_format($unitCost, 2).' × '.$qty.')' : '' }}</span></li>
            <li><span class="label">Margin</span>         <span class="value">{{ number_format($totalSell - $totalCost, 2) }} {{ $cur }}</span></li>
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
            <li><span class="label">QR emailed</span>          <span class="value">{{ $order->qr_sent_at?->format('d M Y H:i') ?: 'Not sent by us' }}</span></li>
            <li><span class="label">Created</span>             <span class="value">{{ $order->created_at?->format('d M Y H:i') ?: '—' }}</span></li>
        </ul>
    </div>

    @php
        // The two fields above mirror the FIRST eSIM only. On a group order that
        // is misleading on its own, so every unit is listed out — including any
        // that failed, with the provider's reason.
        $units = $order->units()->get();
    @endphp
    @if($units->count() > 1)
        <div class="orders-card orders-detail-card" style="grid-column: 1 / -1;">
            <h3>
                Individual eSIMs
                <span style="font-weight:400; opacity:.7;">
                    — {{ $units->whereNotNull('monty_order_id')->count() }} of {{ $order->unitCount() }} issued
                </span>
            </h3>
            <div class="table-responsive">
                <table class="wp-table">
                    <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>Monty order ID</th>
                            <th>ICCID</th>
                            <th style="width:130px;">Status</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($units as $u)
                            <tr>
                                <td>{{ $u->unit_number }}</td>
                                <td>{{ $u->monty_order_id ?: '—' }}</td>
                                <td>{{ $u->monty_iccid ?: '—' }}</td>
                                <td>
                                    @if($u->reservation_status === 'completed')
                                        <span class="wp-badge wp-badge-green">Issued</span>
                                    @elseif($u->monty_order_id)
                                        <span class="wp-badge wp-badge-amber">{{ $u->reservation_status ?: 'unknown' }}</span>
                                    @else
                                        <span class="wp-badge wp-badge-red">{{ $u->reservation_status ?: 'pending' }}</span>
                                    @endif
                                </td>
                                <td style="font-size:12px; opacity:.75;">
                                    {{ data_get($u->monty_response, 'error') ?: '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
