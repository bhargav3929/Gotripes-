@extends('layouts.client')

@section('title', 'Analytics')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-chart-line me-2"></i>Analytics</h1>
</div>

<!-- Date Filter -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('client.analytics') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">From Date</label>
                <input type="date" name="from" class="form-control" value="{{ request('from', now()->subMonth()->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">To Date</label>
                <input type="date" name="to" class="form-control" value="{{ request('to', now()->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Apply</button>
                <a href="{{ route('client.analytics') }}" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="stat-value text-success">{{ app('current_company')->currency ?? 'AED' }} {{ number_format($stats['esim_revenue'] ?? 0, 0) }}</div>
            <div class="stat-label">eSIM Revenue</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="stat-value">{{ number_format($stats['esim_orders'] ?? 0) }}</div>
            <div class="stat-label">eSIM Orders</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="stat-value">{{ number_format($stats['visa_applications'] ?? 0) }}</div>
            <div class="stat-label">Visa Applications</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="stat-value text-success">{{ app('current_company')->currency ?? 'AED' }} {{ number_format($stats['flight_hotel_revenue'] ?? 0, 0) }}</div>
            <div class="stat-label">Flights & Hotels</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Daily Revenue Chart -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="fas fa-chart-area me-2"></i>Daily Revenue</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th class="text-end">Revenue</th>
                                <th class="text-end">Orders</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dailyRevenue as $day)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</td>
                                <td class="text-end text-success">{{ app('current_company')->currency ?? 'AED' }} {{ number_format($day->revenue, 2) }}</td>
                                <td class="text-end">{{ $day->orders }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">No data for selected period</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Countries -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="fas fa-globe me-2"></i>Top Countries</div>
            <div class="card-body p-0">
                @forelse($topCountries as $country)
                <div class="d-flex align-items-center p-3 border-bottom" style="border-color: var(--border) !important;">
                    <div class="flex-grow-1">
                        <div class="fw-500">{{ $country->country_name }}</div>
                        <small class="text-muted">{{ $country->count }} orders</small>
                    </div>
                    <div class="text-end">
                        <div class="text-success">{{ app('current_company')->currency ?? 'AED' }} {{ number_format($country->revenue, 0) }}</div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">No data available</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
