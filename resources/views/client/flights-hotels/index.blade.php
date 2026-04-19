@extends('layouts.client')

@section('title', 'Flights & Hotels')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title"><i class="fas fa-plane me-2" style="color: var(--gold);"></i>Flights & Hotels</h1>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by order ID..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="flight" {{ request('type') == 'flight' ? 'selected' : '' }}>Flights</option>
                    <option value="hotel" {{ request('type') == 'hotel' ? 'selected' : '' }}>Hotels</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="fas fa-search"></i></button>
                <a href="{{ route('client.flights-hotels') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-redo"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Type</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr>
                    <td><code>{{ $booking->order_id }}</code></td>
                    <td>
                        @if($booking->booking_type == 'flight')
                        <span class="badge bg-info"><i class="fas fa-plane me-1"></i>Flight</span>
                        @elseif($booking->booking_type == 'hotel')
                        <span class="badge bg-success"><i class="fas fa-hotel me-1"></i>Hotel</span>
                        @else
                        <span class="badge bg-secondary">{{ ucfirst($booking->booking_type ?? 'N/A') }}</span>
                        @endif
                    </td>
                    <td>
                        @if($booking->customer)
                        {{ $booking->customer['name'] ?? '-' }}
                        <br><small class="text-muted">{{ $booking->customer['email'] ?? '' }}</small>
                        @else
                        -
                        @endif
                    </td>
                    <td>{{ $booking->currency ?? 'AED' }} {{ number_format($booking->amount ?? 0, 2) }}</td>
                    <td>
                        <span class="badge bg-{{ $booking->status == 'completed' ? 'success' : ($booking->status == 'failed' ? 'danger' : 'warning') }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                    <td>{{ $booking->created_at->format('M d, Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No flight or hotel bookings found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($bookings->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center py-2">
        <small class="text-muted">Showing {{ $bookings->firstItem() }}-{{ $bookings->lastItem() }} of {{ $bookings->total() }}</small>
        <div class="d-flex gap-2">
            @if($bookings->onFirstPage())
                <span class="btn btn-sm btn-outline-secondary disabled"><i class="fas fa-chevron-left"></i></span>
            @else
                <a href="{{ $bookings->previousPageUrl() }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-chevron-left"></i></a>
            @endif
            <span class="btn btn-sm btn-dark disabled">{{ $bookings->currentPage() }} / {{ $bookings->lastPage() }}</span>
            @if($bookings->hasMorePages())
                <a href="{{ $bookings->nextPageUrl() }}" class="btn btn-sm btn-primary"><i class="fas fa-chevron-right"></i></a>
            @else
                <span class="btn btn-sm btn-outline-secondary disabled"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
