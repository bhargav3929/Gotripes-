@extends('layouts.superadmin')

@section('title', 'Reports')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-chart-bar me-2"></i>Reports</h1>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" onclick="window.print()">
            <i class="fas fa-print me-2"></i>Print
        </button>
        <a href="{{ route('superadmin.reports.export') }}" class="btn btn-primary">
            <i class="fas fa-download me-2"></i>Export CSV
        </a>
    </div>
</div>

<!-- Date Range Filter -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('superadmin.reports.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">From Date</label>
                <input type="date" name="from" class="form-control" value="{{ request('from', now()->subMonth()->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">To Date</label>
                <input type="date" name="to" class="form-control" value="{{ request('to', now()->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Company</label>
                <select name="company_id" class="form-select">
                    <option value="">All Companies</option>
                    @foreach($companies ?? [] as $company)
                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                        {{ $company->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Apply</button>
                <a href="{{ route('superadmin.reports.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Summary Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="stat-value text-success">AED {{ number_format($stats['total_revenue'] ?? 0, 2) }}</div>
            <div class="stat-label">Total Revenue</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="stat-value">{{ number_format($stats['total_orders'] ?? 0) }}</div>
            <div class="stat-label">Total Orders</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="stat-value">{{ number_format($stats['new_users'] ?? 0) }}</div>
            <div class="stat-label">New Users</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="stat-value">{{ number_format($stats['new_companies'] ?? 0) }}</div>
            <div class="stat-label">New Companies</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Revenue by Company -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="fas fa-building me-2"></i>Revenue by Company</div>
            <div class="card-body p-0">
                @forelse($revenueByCompany ?? [] as $company)
                <div class="d-flex align-items-center p-3 border-bottom border-dark">
                    <div class="flex-grow-1">
                        <div class="fw-500">{{ $company->name }}</div>
                        <small class="text-muted">{{ $company->orders_count }} orders</small>
                    </div>
                    <div class="text-success fw-600">AED {{ number_format($company->revenue, 2) }}</div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">No data available</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Orders by Status -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="fas fa-shopping-cart me-2"></i>Orders by Status</div>
            <div class="card-body p-0">
                @forelse($ordersByStatus ?? [] as $status => $count)
                <div class="d-flex align-items-center p-3 border-bottom border-dark">
                    <div class="flex-grow-1">
                        <span class="badge bg-{{ $status === 'completed' ? 'success' : ($status === 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($status) }}
                        </span>
                    </div>
                    <div class="fw-600">{{ number_format($count) }}</div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">No data available</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Monthly Trend -->
<div class="card mt-4">
    <div class="card-header"><i class="fas fa-chart-line me-2"></i>Monthly Revenue Trend</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th class="text-end">Revenue</th>
                        <th class="text-end">Orders</th>
                        <th class="text-end">New Users</th>
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
                        <td colspan="4" class="text-center py-4 text-muted">No data available</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
