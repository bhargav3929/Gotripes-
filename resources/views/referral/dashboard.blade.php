@extends('layouts.referral')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div>
                    <h1 class="h5 mb-1">Welcome back, {{ $agent->name }}!</h1>
                    <p class="text-muted mb-0 small">Here's an overview of your referral performance</p>
                </div>
                <span class="badge bg-{{ $agent->status === 'active' ? 'success' : 'secondary' }} px-3 py-2">
                    <i class="fas fa-circle me-1 small"></i>{{ ucfirst($agent->status) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Referral Link Box -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="referral-box">
                <div class="row align-items-center">
                    <div class="col-12 col-lg-8 mb-3 mb-lg-0">
                        <h6 class="mb-1" style="color: var(--primary-gold); font-size: 0.85rem;">
                            <i class="fas fa-link me-2"></i>Your Referral Link
                        </h6>
                        <p class="text-muted mb-2" style="font-size: 0.7rem;">Share this link to earn commissions on every purchase</p>
                        <div class="d-flex gap-2">
                            <div class="referral-url flex-grow-1" id="referralUrl">{{ $agent->referral_url }}</div>
                            <button class="btn btn-gold copy-btn" data-copy="{{ $agent->referral_url }}">
                                <i class="fas fa-copy me-1"></i><span class="d-none d-sm-inline">Copy</span>
                            </button>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 text-lg-end">
                        <div class="d-flex flex-column align-items-lg-end">
                            <span class="text-muted" style="font-size: 0.7rem;">Your Commission Rate</span>
                            <span class="h5 mb-0" style="color: var(--primary-gold);">
                                @if($agent->commission_type === 'percentage')
                                    {{ $agent->commission_value }}%
                                @else
                                    AED {{ number_format($agent->commission_value, 2) }}
                                @endif
                            </span>
                            <span class="text-muted" style="font-size: 0.7rem;">per order</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="stat-icon" style="background: rgba(255, 215, 0, 0.15); color: var(--primary-gold);">
                            <i class="fas fa-mouse-pointer"></i>
                        </span>
                    </div>
                    <h5 class="mb-0">{{ number_format($stats['total_clicks']) }}</h5>
                    <span class="text-muted" style="font-size: 0.7rem;">Total Clicks</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="stat-icon" style="background: rgba(59, 130, 246, 0.15); color: var(--info);">
                            <i class="fas fa-shopping-cart"></i>
                        </span>
                    </div>
                    <h5 class="mb-0">{{ number_format($stats['total_sales']) }}</h5>
                    <span class="text-muted" style="font-size: 0.7rem;">Total Sales</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="stat-icon" style="background: rgba(34, 197, 94, 0.15); color: var(--success);">
                            <i class="fas fa-coins"></i>
                        </span>
                    </div>
                    <h5 class="mb-0 text-success">AED {{ number_format($stats['total_commission'], 2) }}</h5>
                    <span class="text-muted" style="font-size: 0.7rem;">Total Earnings</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="stat-icon" style="background: rgba(245, 158, 11, 0.15); color: var(--warning);">
                            <i class="fas fa-exchange-alt"></i>
                        </span>
                    </div>
                    <h5 class="mb-0">{{ $stats['conversion_rate'] }}%</h5>
                    <span class="text-muted" style="font-size: 0.7rem;">Conversion Rate</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings Summary & Chart -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <span style="font-size: 0.8rem;"><i class="fas fa-wallet me-2" style="color: var(--primary-gold);"></i>Earnings Summary</span>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Pending</span>
                            <span class="text-warning fw-semibold">AED {{ number_format($stats['pending_commission'], 2) }}</span>
                        </div>
                        <div class="progress" style="height: 6px; background: var(--light-dark);">
                            @php
                                $total = $stats['total_commission'] + $stats['pending_commission'];
                                $pendingPercent = $total > 0 ? ($stats['pending_commission'] / $total) * 100 : 0;
                            @endphp
                            <div class="progress-bar bg-warning" style="width: {{ $pendingPercent }}%"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Approved</span>
                            <span class="text-info fw-semibold">AED {{ number_format($stats['total_commission'] - $stats['paid_commission'], 2) }}</span>
                        </div>
                        <div class="progress" style="height: 6px; background: var(--light-dark);">
                            @php
                                $approvedPercent = $total > 0 ? (($stats['total_commission'] - $stats['paid_commission']) / $total) * 100 : 0;
                            @endphp
                            <div class="progress-bar bg-info" style="width: {{ $approvedPercent }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Paid</span>
                            <span class="text-success fw-semibold">AED {{ number_format($stats['paid_commission'], 2) }}</span>
                        </div>
                        <div class="progress" style="height: 6px; background: var(--light-dark);">
                            @php
                                $paidPercent = $total > 0 ? ($stats['paid_commission'] / $total) * 100 : 0;
                            @endphp
                            <div class="progress-bar bg-success" style="width: {{ $paidPercent }}%"></div>
                        </div>
                    </div>

                    <hr class="my-4 border-dark">

                    <div class="text-center">
                        <span class="text-muted d-block" style="font-size: 0.7rem;">Total Earned</span>
                        <h5 class="mb-0" style="color: var(--primary-gold);">AED {{ number_format($stats['total_commission'], 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span style="font-size: 0.8rem;"><i class="fas fa-chart-bar me-2" style="color: var(--primary-gold);"></i>Performance Overview</span>
                    <span class="text-muted small">Last 6 months</span>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span style="font-size: 0.8rem;"><i class="fas fa-history me-2" style="color: var(--primary-gold);"></i>Recent Orders</span>
                    <a href="{{ route('referral.orders') }}" class="btn btn-sm btn-outline-gold">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th class="text-end">Order Amount</th>
                                    <th class="text-end">Commission</th>
                                    <th class="text-center">Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                <tr>
                                    <td><code>#{{ $order->order_id }}</code></td>
                                    <td>
                                        <div>{{ $order->customer_name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ Str::mask($order->customer_email ?? '', '*', 3, -10) }}</small>
                                    </td>
                                    <td class="text-end">AED {{ number_format($order->order_amount, 2) }}</td>
                                    <td class="text-end text-success fw-semibold">AED {{ number_format($order->commission_amount, 2) }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $order->status_badge }}">{{ ucfirst($order->status) }}</span>
                                    </td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-receipt fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted mb-2">No orders yet</p>
                                        <p class="text-muted small">Share your referral link to start earning!</p>
                                    </td>
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
    /* Compact Layout */
    .container { max-width: 1200px; }

    /* Text Visibility Fixes */
    p, span, div, li, small {
        color: #e2e8f0;
    }
    .text-muted {
        color: #a0aec0 !important;
    }
    h1, h2, h3, h4, h5, h6, strong {
        color: #ffffff !important;
    }
    .card-header h5, .card-header h6 {
        color: #ffffff !important;
    }

    /* Stat Icon */
    .stat-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
    }

    /* Stat Card */
    .stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 10px;
    }
    .stat-card .card-body {
        padding: 12px !important;
    }

    /* Referral Box */
    .referral-box {
        padding: 16px 20px !important;
    }
    .referral-url {
        font-size: 0.75rem !important;
        padding: 8px 12px !important;
    }
    .btn-gold {
        font-size: 0.8rem;
        padding: 6px 12px;
    }

    /* Cards */
    .card {
        border-radius: 10px;
    }
    .card-header {
        padding: 10px 14px !important;
    }
    .card-body {
        padding: 14px !important;
    }

    /* Progress bars */
    .progress {
        height: 4px !important;
    }

    /* Table */
    .table {
        font-size: 0.75rem;
    }
    .table th {
        font-size: 0.65rem;
        padding: 8px 12px;
    }
    .table td {
        padding: 8px 12px;
    }

    /* Badge */
    .badge {
        font-size: 0.6rem;
        padding: 3px 6px;
    }

    /* Chart */
    #performanceChart {
        max-height: 180px !important;
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Copy button
    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const text = this.dataset.copy;
            navigator.clipboard.writeText(text).then(() => {
                const originalHTML = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check me-1"></i><span class="d-none d-sm-inline">Copied!</span>';
                setTimeout(() => {
                    this.innerHTML = originalHTML;
                }, 2000);
            });
        });
    });

    // Performance Chart
    const ctx = document.getElementById('performanceChart').getContext('2d');
    const monthlyStats = @json($monthlyStats);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: monthlyStats.map(s => s.month),
            datasets: [
                {
                    label: 'Orders',
                    data: monthlyStats.map(s => s.orders),
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderRadius: 4,
                    yAxisID: 'y'
                },
                {
                    label: 'Commission (AED)',
                    data: monthlyStats.map(s => s.commission),
                    type: 'line',
                    borderColor: '#FFD700',
                    backgroundColor: 'rgba(255, 215, 0, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#FFD700',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#8a8f98',
                        usePointStyle: true,
                        padding: 20
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)'
                    },
                    ticks: {
                        color: '#8a8f98'
                    }
                },
                y: {
                    position: 'left',
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)'
                    },
                    ticks: {
                        color: '#8a8f98'
                    },
                    title: {
                        display: true,
                        text: 'Orders',
                        color: '#8a8f98'
                    }
                },
                y1: {
                    position: 'right',
                    grid: {
                        drawOnChartArea: false
                    },
                    ticks: {
                        color: '#FFD700'
                    },
                    title: {
                        display: true,
                        text: 'Commission (AED)',
                        color: '#FFD700'
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
