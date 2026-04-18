@extends('layouts.client')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-chart-pie me-2"></i>Dashboard</h1>
    <span class="text-muted">Welcome back, {{ auth()->user()->name }}</span>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_orders']) }}</div>
            <div class="stat-label">Total Orders</div>
            <small class="text-success">+{{ $stats['today_orders'] }} today</small>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_revenue'], 0) }}</div>
            <div class="stat-label">Total Revenue ({{ $company->currency }})</div>
            <small class="text-success">+{{ number_format($stats['today_revenue'], 0) }} today</small>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_customers']) }}</div>
            <div class="stat-label">Customers</div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-user-friends"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_agents']) }}</div>
            <div class="stat-label">Referral Agents</div>
            <small class="text-warning">{{ $company->currency }} {{ number_format($stats['pending_commissions'], 0) }} pending</small>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Orders -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-clock me-2"></i>Recent Orders</span>
                <a href="{{ route('client.orders') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Country</th>
                            <th class="text-end">Amount</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                        <tr>
                            <td>
                                <div class="fw-500">#{{ $order->order_reference ?? $order->id }}</div>
                                <small class="text-muted">{{ $order->created_at->format('M d, H:i') }}</small>
                            </td>
                            <td>
                                <div>{{ $order->customer_name ?? $order->user->name ?? '-' }}</div>
                                <small class="text-muted">{{ $order->customer_email ?? $order->user->email ?? '' }}</small>
                            </td>
                            <td>{{ $order->country_name ?? '-' }}</td>
                            <td class="text-end text-success">
                                {{ $company->currency }} {{ number_format($order->selling_price ?? $order->total_amount ?? 0, 2) }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ ($order->status ?? $order->payment_status) === 'completed' ? 'success' : (($order->status ?? $order->payment_status) === 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($order->status ?? $order->payment_status ?? 'pending') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No orders yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Agents -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-trophy me-2" style="color: var(--client-primary);"></i>Top Referral Agents
            </div>
            <div class="card-body p-0">
                @forelse($topAgents as $index => $agent)
                <div class="d-flex align-items-center p-3 border-bottom" style="border-color: var(--client-border) !important;">
                    <div class="me-3">
                        <span class="badge bg-{{ $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'dark') }}">
                            #{{ $index + 1 }}
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-500">{{ $agent->name }}</div>
                        <small class="text-muted">{{ $agent->total_sales }} sales</small>
                    </div>
                    <div class="text-end">
                        <div class="text-success fw-600">{{ $company->currency }} {{ number_format($agent->total_earnings, 2) }}</div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">No agents yet</div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-4">
            <div class="card-header"><i class="fas fa-bolt me-2"></i>Quick Actions</div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.referrals.agents.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-user-plus me-2"></i>Add Referral Agent
                    </a>
                    <a href="{{ route('client.branding') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-palette me-2"></i>Update Branding
                    </a>
                    <a href="{{ route('client.analytics') }}" class="btn btn-outline-info">
                        <i class="fas fa-chart-bar me-2"></i>View Analytics
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
