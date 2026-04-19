@extends('layouts.client')

@section('title', 'Activity Bookings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title"><i class="fas fa-ticket-alt me-2" style="color: var(--client-primary);"></i>Activity Bookings</h1>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name or email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="fas fa-search"></i></button>
                <a href="{{ route('client.activities') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-redo"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Customer</th>
                    <th>Activity</th>
                    <th>Date</th>
                    <th>Guests</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr>
                    <td>#{{ $booking->id }}</td>
                    <td>
                        <strong>{{ $booking->name }}</strong>
                        <br><small class="text-muted">{{ $booking->email }}</small>
                    </td>
                    <td>{{ $booking->activityId }}</td>
                    <td>{{ $booking->date ? \Carbon\Carbon::parse($booking->date)->format('M d, Y') : '-' }}</td>
                    <td>{{ $booking->adults ?? 0 }} Adults, {{ $booking->childrens ?? 0 }} Children</td>
                    <td>{{ $booking->currency ?? 'AED' }} {{ number_format($booking->amount ?? 0, 2) }}</td>
                    <td>
                        <span class="badge bg-{{ ($booking->status ?? 'pending') == 'confirmed' ? 'success' : (($booking->status ?? 'pending') == 'cancelled' ? 'danger' : 'warning') }}">
                            {{ ucfirst($booking->status ?? 'pending') }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">No activity bookings found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($bookings->hasPages())
    <div class="card-footer d-flex justify-content-center">
        {{ $bookings->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
