@extends('layouts.manager')

@section('title', 'Umrah Bookings')
@section('page-title', 'Umrah Booking Management')

@section('content')

<div class="wp-page-header">
    <h1 class="wp-page-title">
        <i class="fas fa-book-open" style="color:var(--wp-primary); margin-right:8px;"></i>
        Umrah Bookings
    </h1>
    <a href="{{ route('manager.umrah-bookings.export', request()->query()) }}" class="wp-btn wp-btn-secondary wp-btn-sm">
        <i class="fas fa-download"></i> Export CSV
    </a>
</div>

{{-- Flash --}}
@if(session('success'))
<div style="padding:12px 16px; background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.3); border-radius:8px; color:#22c55e; margin-bottom:18px; display:flex; gap:10px;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

{{-- KPI Bar --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:12px; margin-bottom:20px;">
    <div class="wp-card" style="padding:16px; text-align:center;">
        <div style="font-size:24px; font-weight:800; color:var(--wp-text);">{{ number_format($totalBookings) }}</div>
        <div style="font-size:11px; color:var(--wp-text-muted); margin-top:3px;">Total Bookings</div>
    </div>
    <div class="wp-card" style="padding:16px; text-align:center;">
        <div style="font-size:24px; font-weight:800; color:#eab308;">{{ number_format($pendingBookings) }}</div>
        <div style="font-size:11px; color:var(--wp-text-muted); margin-top:3px;">Pending</div>
    </div>
    <div class="wp-card" style="padding:16px; text-align:center;">
        <div style="font-size:24px; font-weight:800; color:#22c55e;">{{ number_format($confirmedBookings) }}</div>
        <div style="font-size:11px; color:var(--wp-text-muted); margin-top:3px;">Confirmed</div>
    </div>
    <div class="wp-card" style="padding:16px; text-align:center;">
        <div style="font-size:22px; font-weight:800; color:var(--wp-primary);">AED {{ number_format($totalRevenue, 0) }}</div>
        <div style="font-size:11px; color:var(--wp-text-muted); margin-top:3px;">Total Revenue</div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="wp-card" style="padding:16px; margin-bottom:16px;">
    <form method="GET" action="{{ route('manager.umrah-bookings.index') }}" style="display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end;">
        <div style="flex:1; min-width:180px;">
            <label style="font-size:11px; font-weight:600; color:var(--wp-text-muted); text-transform:uppercase; display:block; margin-bottom:5px;">Search</label>
            <input type="text" name="search" class="wp-input" placeholder="Name, email, phone, order ID..."
                   value="{{ request('search') }}" style="width:100%;">
        </div>
        <div>
            <label style="font-size:11px; font-weight:600; color:var(--wp-text-muted); text-transform:uppercase; display:block; margin-bottom:5px;">Package</label>
            <select name="package_id" class="wp-select" style="min-width:150px;">
                <option value="">All Packages</option>
                @foreach($packages as $pkg)
                    <option value="{{ $pkg->id }}" {{ request('package_id') == $pkg->id ? 'selected' : '' }}>{{ Str::limit($pkg->title, 30) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="font-size:11px; font-weight:600; color:var(--wp-text-muted); text-transform:uppercase; display:block; margin-bottom:5px;">Status</label>
            <select name="payment_status" class="wp-select" style="min-width:120px;">
                <option value="">All Statuses</option>
                <option value="pending"   {{ request('payment_status')=='pending'   ? 'selected' : '' }}>Pending</option>
                <option value="paid"      {{ request('payment_status')=='paid'      ? 'selected' : '' }}>Paid</option>
                <option value="cancelled" {{ request('payment_status')=='cancelled' ? 'selected' : '' }}>Cancelled</option>
                <option value="refunded"  {{ request('payment_status')=='refunded'  ? 'selected' : '' }}>Refunded</option>
            </select>
        </div>
        <div>
            <label style="font-size:11px; font-weight:600; color:var(--wp-text-muted); text-transform:uppercase; display:block; margin-bottom:5px;">From</label>
            <input type="date" name="from" class="wp-input" value="{{ request('from') }}">
        </div>
        <div>
            <label style="font-size:11px; font-weight:600; color:var(--wp-text-muted); text-transform:uppercase; display:block; margin-bottom:5px;">To</label>
            <input type="date" name="to" class="wp-input" value="{{ request('to') }}">
        </div>
        <div style="display:flex; gap:8px;">
            <button type="submit" class="wp-btn wp-btn-primary wp-btn-sm">
                <i class="fas fa-search"></i> Search
            </button>
            @if(request()->hasAny(['search','package_id','payment_status','from','to']))
            <a href="{{ route('manager.umrah-bookings.index') }}" class="wp-btn wp-btn-secondary wp-btn-sm">
                <i class="fas fa-times"></i> Clear
            </a>
            @endif
        </div>
    </form>
</div>

{{-- Bookings Table --}}
<div class="wp-card">
    <div class="table-responsive">
        <table class="wp-table">
            <thead>
                <tr>
                    <th style="width:130px;">Order ID</th>
                    <th>Package</th>
                    <th style="width:110px;">Departure</th>
                    <th style="width:160px;">Customer</th>
                    <th style="width:80px; text-align:center;">Pax</th>
                    <th style="width:110px; text-align:right;">Total</th>
                    <th style="width:100px;">Payment</th>
                    <th style="width:120px;">Status</th>
                    <th style="width:70px; text-align:center;">View</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr>
                    <td>
                        <code style="font-size:11px; color:var(--wp-primary);">{{ $booking->order_id ?? '—' }}</code>
                        <br><span style="font-size:10px; color:var(--wp-text-muted);">{{ $booking->created_at?->format('d M Y') }}</span>
                    </td>
                    <td>
                        <strong style="color:var(--wp-text); font-size:13px;">{{ Str::limit($booking->package?->title ?? '—', 40) }}</strong>
                    </td>
                    <td>
                        <span style="font-size:13px; color:var(--wp-text-secondary);">
                            {{ $booking->departure_date?->format('d M Y') ?? '—' }}
                        </span>
                    </td>
                    <td>
                        <strong style="font-size:13px; color:var(--wp-text);">{{ $booking->customer_name }}</strong>
                        <br><span style="font-size:11px; color:var(--wp-text-muted);">{{ $booking->customer_email }}</span>
                        <br><span style="font-size:11px; color:var(--wp-text-muted);">{{ $booking->customer_phone }}</span>
                    </td>
                    <td style="text-align:center;">
                        <div style="font-size:11px; line-height:1.7; color:var(--wp-text-secondary);">
                            <span title="Adults">👤 {{ $booking->adults }}</span>
                            @if($booking->children) <span title="Children">🧒 {{ $booking->children }}</span> @endif
                            @if($booking->infants)  <span title="Infants">👶 {{ $booking->infants }}</span>  @endif
                        </div>
                    </td>
                    <td style="text-align:right; font-weight:800; color:var(--wp-primary);">
                        AED {{ number_format($booking->total_price, 0) }}
                        @if($booking->installment_months && $booking->installment_months > 1)
                            <br><span style="font-size:10px; color:var(--wp-text-muted); font-weight:400;">{{ $booking->installment_months }}× instalment</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-size:11px; color:var(--wp-text-muted);">{{ $booking->payment_gateway ?? '—' }}</span>
                    </td>
                    <td>
                        {{-- Inline status updater --}}
                        <form action="{{ route('manager.umrah-bookings.status', $booking->id) }}" method="POST">
                            @csrf
                            <select name="payment_status" class="wp-select"
                                    style="font-size:11px; padding:4px 6px; border-radius:6px;"
                                    onchange="this.form.submit()">
                                @foreach(['pending'=>'⏳ Pending','paid'=>'✅ Paid','cancelled'=>'❌ Cancelled','refunded'=>'↩ Refunded'] as $val => $label)
                                    <option value="{{ $val }}" {{ $booking->payment_status == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                    <td style="text-align:center;">
                        <a href="{{ route('manager.umrah-bookings.show', $booking->id) }}"
                           class="wp-btn wp-btn-secondary wp-btn-sm" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="padding:32px; text-align:center; color:var(--wp-text-muted);">
                        <i class="fas fa-inbox" style="font-size:28px; display:block; margin-bottom:10px; color:var(--wp-border);"></i>
                        No bookings found matching your filters.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($bookings->hasPages())
    <div class="wp-pagination" style="padding:16px 20px;">
        {{ $bookings->links() }}
    </div>
    @endif
</div>

@endsection
