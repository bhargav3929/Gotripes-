@extends('layouts.admin')

@section('title', 'Referral Dashboard')

@section('content')
<div class="container-fluid py-3">
    <!-- Welcome Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h4 class="fw-bold text-white mb-1">Referral Dashboard</h4>
            <p class="text-muted mb-0">Overview of your referral program performance</p>
        </div>
        <a href="{{ route('admin.referrals.agents.create') }}" class="btn btn-gold">
            <i class="fas fa-plus me-2"></i>Add Agent
        </a>
    </div>

    <!-- Quick Stats Box -->
    <div class="info-box mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h6 class="text-white mb-1"><i class="fas fa-chart-pie me-2"></i>Program Summary</h6>
                <p class="text-muted small mb-3">Total commissions and revenue from referral program</p>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.referrals.agents.index') }}" class="btn btn-sm btn-outline-light">View All Agents</a>
                    <a href="{{ route('admin.referrals.commissions.index') }}" class="btn btn-sm btn-outline-warning">Manage Commissions</a>
                    <a href="{{ route('admin.referrals.withdrawals.index') }}" class="btn btn-sm btn-outline-info">
                        Manage Withdrawals
                        @if(($stats['pending_withdrawals'] ?? 0) > 0)
                        <span class="badge bg-warning ms-1" style="font-size: 0.55rem; padding: 2px 5px;">{{ $stats['pending_withdrawals'] }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.referrals.settings') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-cog me-1"></i>Settings
                    </a>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <small class="text-muted d-block">Total Revenue Generated</small>
                <span class="display-value">AED {{ number_format($stats['this_month_revenue'], 2) }}</span>
                <small class="text-muted d-block">this month</small>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon gold"><i class="fas fa-user-tie"></i></div>
                <div class="stat-number">{{ $stats['total_agents'] }}</div>
                <div class="stat-label">Total Agents</div>
                <small class="text-success">{{ $stats['active_agents'] }} active</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fas fa-shopping-cart"></i></div>
                <div class="stat-number">{{ $stats['total_orders'] }}</div>
                <div class="stat-label">Total Orders</div>
                <small class="text-info">{{ $stats['today_orders'] }} today</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-coins"></i></div>
                <div class="stat-number text-success">AED {{ number_format($stats['paid_commissions'], 2) }}</div>
                <div class="stat-label">Paid Commissions</div>
                <small class="text-muted">all time</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
                <div class="stat-number text-warning">AED {{ number_format($stats['pending_commissions'], 2) }}</div>
                <div class="stat-label">Pending Approval</div>
                <small class="text-warning">awaiting review</small>
            </div>
        </div>
    </div>

    <!-- Second Row Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fas fa-mouse-pointer"></i></div>
                <div class="stat-number">{{ number_format($stats['total_clicks']) }}</div>
                <div class="stat-label">Total Clicks</div>
                <small class="text-muted">{{ $stats['today_clicks'] }} today</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon cyan"><i class="fas fa-exchange-alt"></i></div>
                @php
                    $convRate = $stats['total_clicks'] > 0 ? round(($stats['total_conversions'] / $stats['total_clicks']) * 100, 2) : 0;
                @endphp
                <div class="stat-number">{{ $convRate }}%</div>
                <div class="stat-label">Conversion Rate</div>
                <small class="text-muted">{{ $stats['total_conversions'] }} conversions</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card clickable" onclick="window.location='{{ route('admin.referrals.commissions.index', ['status' => 'pending']) }}'">
                <div class="stat-icon orange"><i class="fas fa-tasks"></i></div>
                <div class="stat-number">{{ $stats['total_orders'] > 0 ? $stats['total_orders'] : 0 }}</div>
                <div class="stat-label">Review Pending</div>
                <small class="text-warning">click to review</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card clickable" onclick="window.location='{{ route('admin.referrals.withdrawals.index', ['status' => 'pending']) }}'">
                <div class="stat-icon red"><i class="fas fa-money-bill-wave"></i></div>
                <div class="stat-number text-danger">{{ $stats['pending_withdrawals'] ?? 0 }}</div>
                <div class="stat-label">Pending Withdrawals</div>
                <small class="text-danger">AED {{ number_format($stats['pending_withdrawal_amount'] ?? 0, 2) }}</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card clickable" onclick="window.location='{{ route('admin.referrals.agents.create') }}'">
                <div class="stat-icon gold"><i class="fas fa-user-plus"></i></div>
                <div class="stat-number"><i class="fas fa-plus"></i></div>
                <div class="stat-label">Add Agent</div>
                <small class="text-muted">click to add</small>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row g-3">
        <!-- Top Agents -->
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-trophy text-warning me-2"></i>Top Performing Agents
                    <a href="{{ route('admin.referrals.agents.index') }}" class="btn btn-xs btn-outline-gold float-end">View All</a>
                </div>
                <div class="card-body p-0">
                    @forelse($topAgents as $item)
                    <div class="agent-row">
                        <div class="agent-avatar">{{ strtoupper(substr($item['agent']->name ?? 'N', 0, 1)) }}</div>
                        <div class="agent-info">
                            <div class="agent-name">{{ $item['agent']->name ?? 'N/A' }}</div>
                            <div class="agent-code">{{ $item['agent']->referral_code ?? '' }}</div>
                        </div>
                        <div class="agent-orders">
                            <span class="badge bg-info">{{ $item['total_orders'] }}</span>
                        </div>
                        <div class="agent-commission">AED {{ number_format($item['total_commission'], 2) }}</div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">No agents yet</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-history text-info me-2"></i>Recent Referral Orders
                    <a href="{{ route('admin.referrals.commissions.index') }}" class="btn btn-xs btn-outline-gold float-end">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Agent</th>
                                    <th>Status</th>
                                    <th class="text-end">Commission</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                <tr>
                                    <td>
                                        <span class="order-id">#{{ $order->order_id }}</span>
                                        <small class="d-block text-muted">{{ $order->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>{{ $order->referralAgent->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'approved' ? 'success' : 'info') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end commission-amount">AED {{ number_format($order->commission_amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No orders yet</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Info Box */
    .info-box {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(255, 165, 0, 0.05));
        border: 1px solid rgba(255, 215, 0, 0.2);
        border-radius: 12px;
        padding: 20px 24px;
    }
    .display-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--primary-gold);
    }

    /* Stat Cards */
    .stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 16px;
        height: 100%;
    }
    .stat-card:hover {
        border-color: rgba(255, 215, 0, 0.3);
    }
    .stat-card.clickable {
        cursor: pointer;
    }
    .stat-card.clickable:hover {
        background: rgba(255, 215, 0, 0.05);
    }
    .stat-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        margin-bottom: 12px;
    }
    .stat-icon.gold { background: rgba(255, 215, 0, 0.15); color: var(--primary-gold); }
    .stat-icon.blue { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
    .stat-icon.green { background: rgba(34, 197, 94, 0.15); color: #22c55e; }
    .stat-icon.orange { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .stat-icon.purple { background: rgba(139, 92, 246, 0.15); color: #8b5cf6; }
    .stat-icon.cyan { background: rgba(6, 182, 212, 0.15); color: #06b6d4; }
    .stat-icon.red { background: rgba(239, 68, 68, 0.15); color: #ef4444; }

    .stat-number {
        font-size: 1.25rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 2px;
    }
    .stat-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-bottom: 4px;
    }

    /* Card */
    .card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
    }
    .card-header {
        background: transparent;
        border-bottom: 1px solid var(--border-color);
        padding: 12px 16px;
        font-size: 0.9rem;
        font-weight: 600;
        color: #fff;
    }
    .card-body {
        padding: 16px;
    }

    /* Agent Row */
    .agent-row {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        border-bottom: 1px solid var(--border-color);
    }
    .agent-row:last-child {
        border-bottom: none;
    }
    .agent-row:hover {
        background: rgba(255, 215, 0, 0.03);
    }
    .agent-avatar {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #000;
        font-size: 0.85rem;
        margin-right: 12px;
    }
    .agent-info {
        flex: 1;
    }
    .agent-name {
        font-weight: 500;
        color: #fff;
        font-size: 0.85rem;
    }
    .agent-code {
        font-size: 0.7rem;
        color: var(--text-muted);
    }
    .agent-orders {
        margin-right: 16px;
    }
    .agent-commission {
        font-weight: 600;
        color: #22c55e;
        font-size: 0.85rem;
    }

    /* Table */
    .table {
        margin: 0;
        background: transparent !important;
    }
    .table thead th {
        background: rgba(0, 0, 0, 0.2) !important;
        color: var(--text-muted);
        font-size: 0.7rem;
        font-weight: 500;
        text-transform: uppercase;
        padding: 10px 16px;
        border-bottom: 1px solid var(--border-color);
    }
    .table tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border-color);
        background: transparent !important;
        color: #e2e8f0;
        font-size: 0.85rem;
        vertical-align: middle;
    }
    .table tbody tr:hover {
        background: rgba(255, 215, 0, 0.03) !important;
    }
    .order-id {
        font-weight: 600;
        color: var(--primary-gold);
    }
    .commission-amount {
        font-weight: 600;
        color: var(--primary-gold);
    }

    /* Buttons */
    .btn-gold {
        background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
        border: none;
        color: #000;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 8px 16px;
        border-radius: 8px;
    }
    .btn-gold:hover {
        background: linear-gradient(135deg, var(--secondary-gold), var(--primary-gold));
        color: #000;
    }
    .btn-outline-gold {
        border: 1px solid var(--primary-gold);
        color: var(--primary-gold);
        background: transparent;
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 6px;
    }
    .btn-outline-gold:hover {
        background: var(--primary-gold);
        color: #000;
    }
    .btn-xs {
        padding: 3px 8px;
        font-size: 0.7rem;
    }

    /* Badge */
    .badge {
        font-size: 0.65rem;
        font-weight: 500;
        padding: 4px 8px;
        border-radius: 4px;
    }
</style>
@endsection
