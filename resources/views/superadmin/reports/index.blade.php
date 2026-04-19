@extends('layouts.superadmin')

@section('title', 'Reports')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title mb-0" style="font-size: 1.25rem;"><i class="fas fa-chart-bar me-2"></i>Reports</h1>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary btn-sm" onclick="window.print()"><i class="fas fa-print"></i></button>
        <a href="{{ route('superadmin.reports.export') }}" class="btn btn-primary btn-sm"><i class="fas fa-download me-1"></i>Export</a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from', now()->subMonth()->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to', now()->format('Y-m-d')) }}">
            </div>
            <div class="col-md-4">
                <select name="company_id" class="form-select form-select-sm">
                    <option value="">All Companies</option>
                    @foreach($companies ?? [] as $company)
                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="fas fa-filter"></i></button>
                <a href="{{ route('superadmin.reports.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-redo"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-2 mb-3">
    <div class="col-6 col-md-3">
        <div class="card">
            <div class="card-body py-2 px-3 text-center">
                <div class="text-success fw-bold" style="font-size: 1.1rem;">AED {{ number_format($stats['total_revenue'] ?? 0, 2) }}</div>
                <small class="text-muted">Revenue</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card">
            <div class="card-body py-2 px-3 text-center">
                <div class="fw-bold" style="font-size: 1.1rem;">{{ number_format($stats['total_orders'] ?? 0) }}</div>
                <small class="text-muted">Orders</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card">
            <div class="card-body py-2 px-3 text-center">
                <div class="fw-bold" style="font-size: 1.1rem;">{{ number_format($stats['new_users'] ?? 0) }}</div>
                <small class="text-muted">Users</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card">
            <div class="card-body py-2 px-3 text-center">
                <div class="fw-bold" style="font-size: 1.1rem;">{{ number_format($stats['new_companies'] ?? 0) }}</div>
                <small class="text-muted">Companies</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Revenue by Company -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header py-2" style="font-size: 0.85rem;"><i class="fas fa-building me-2"></i>Revenue by Company</div>
            <div class="card-body p-0" style="max-height: 200px; overflow-y: auto;">
                @forelse($revenueByCompany ?? [] as $company)
                <div class="d-flex align-items-center px-3 py-2 border-bottom border-dark">
                    <div class="flex-grow-1">
                        <div style="font-size: 0.85rem;">{{ $company->name }}</div>
                        <small class="text-muted" style="font-size: 0.7rem;">{{ $company->orders_count }} orders</small>
                    </div>
                    <div class="text-success" style="font-size: 0.85rem;">AED {{ number_format($company->revenue, 2) }}</div>
                </div>
                @empty
                <div class="text-center py-3 text-muted" style="font-size: 0.8rem;">No data</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Orders by Status -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header py-2" style="font-size: 0.85rem;"><i class="fas fa-shopping-cart me-2"></i>Orders by Status</div>
            <div class="card-body p-0">
                @forelse($ordersByStatus ?? [] as $status => $count)
                <div class="d-flex align-items-center px-3 py-2 border-bottom border-dark">
                    <div class="flex-grow-1">
                        <span class="badge bg-{{ $status === 'completed' || $status === 'paid' ? 'success' : ($status === 'pending' || $status === 'unpaid' ? 'warning' : 'danger') }}" style="font-size: 0.7rem;">
                            {{ ucfirst($status) }}
                        </span>
                    </div>
                    <div class="fw-bold" style="font-size: 0.9rem;">{{ number_format($count) }}</div>
                </div>
                @empty
                <div class="text-center py-3 text-muted" style="font-size: 0.8rem;">No data</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Monthly Trend -->
<div class="card mt-3">
    <div class="card-header py-2" style="font-size: 0.85rem;"><i class="fas fa-chart-line me-2"></i>Monthly Trend</div>
    <div class="table-responsive">
        <table class="table table-sm mb-0" style="font-size: 0.8rem;">
            <thead>
                <tr>
                    <th>Month</th>
                    <th class="text-end">Revenue</th>
                    <th class="text-end">Orders</th>
                    <th class="text-end">Users</th>
                </tr>
            </thead>
            <tbody>
                @forelse($monthlyTrend ?? [] as $month)
                <tr>
                    <td>{{ $month->month }}</td>
                    <td class="text-end text-success">AED {{ number_format($month->revenue, 2) }}</td>
                    <td class="text-end">{{ number_format($month->orders) }}</td>
                    <td class="text-end">{{ number_format($month->users) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-3 text-muted">No data available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
