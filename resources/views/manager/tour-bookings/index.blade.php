@extends('layouts.manager')

@section('title', 'Tour Bookings')
@section('page-title', 'Tour Bookings')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Tour Bookings</h1>
</div>

<div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:12px;">
    @foreach(['' => 'All', 'paid' => 'Paid', 'pending' => 'Awaiting payment', 'failed' => 'Failed', 'cancelled' => 'Cancelled'] as $key => $label)
        <a href="{{ route('manager.tour-bookings.index', $key ? ['payment' => $key] : []) }}"
           class="wp-btn wp-btn-sm {{ request('payment', '') === $key ? 'wp-btn-primary' : 'wp-btn-secondary' }}">
            {{ $label }}@if($key && ($counts[$key] ?? 0)) ({{ $counts[$key] }})@endif
        </a>
    @endforeach
</div>

<div class="wp-card">
    <div class="table-responsive">
        <table class="wp-table">
            <thead><tr><th>Order</th><th>Customer</th><th>Package</th><th>Travel dates</th><th>Travellers</th><th style="text-align:right">Total</th><th>Payment</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($bookings as $b)
                <tr>
                    <td><strong>{{ $b->order_id }}</strong><br><small style="color:var(--wp-text-muted)">{{ $b->created_at->format('d M Y H:i') }}</small></td>
                    <td>{{ $b->customer_name }}<br><small style="color:var(--wp-text-muted)">{{ $b->nationality }}</small></td>
                    <td>{{ $b->package?->title ?? 'Deleted package' }}<br><small style="color:var(--wp-text-muted)">{{ $b->days }} days · {{ $b->hotel_stars }}★</small></td>
                    <td style="white-space:nowrap">{{ $b->start_date->format('d M') }} – {{ $b->end_date->format('d M Y') }}</td>
                    <td>{{ $b->travellerSummary() }}</td>
                    <td style="text-align:right; white-space:nowrap; font-variant-numeric:tabular-nums">{{ $b->currency }} {{ number_format($b->total, 2) }}</td>
                    <td><span class="wp-badge {{ ['paid' => 'wp-badge-green', 'pending' => 'wp-badge-amber', 'failed' => 'wp-badge-red', 'cancelled' => 'wp-badge-red'][$b->payment_status] ?? '' }}">{{ ucfirst($b->payment_status) }}</span></td>
                    <td><span class="wp-badge {{ ['confirmed' => 'wp-badge-green', 'new' => 'wp-badge-blue', 'cancelled' => 'wp-badge-red'][$b->status] ?? '' }}">{{ ucfirst($b->status) }}</span></td>
                    <td><a href="{{ route('manager.tour-bookings.show', $b->id) }}" class="wp-btn wp-btn-secondary wp-btn-sm">Open</a></td>
                </tr>
            @empty
                <tr><td colspan="9" style="text-align:center; padding:48px 16px; color:var(--wp-text-muted);">
                    <i class="fas fa-calendar-check" style="font-size:28px; display:block; margin-bottom:10px;"></i>
                    No tour bookings yet. They appear here as soon as a customer completes the booking wizard on a package.
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($bookings->hasPages())<div class="wp-card-footer">{{ $bookings->links() }}</div>@endif
</div>
@endsection
