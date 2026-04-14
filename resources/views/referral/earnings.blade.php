@extends('layouts.referral')

@section('title', 'My Earnings')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div>
                    <h5 class="mb-0">My Earnings</h5>
                    <small class="text-muted">Track your commission earnings and payouts</small>
                </div>
                <div class="btn-group" role="group">
                    <a href="{{ route('referral.earnings', ['period' => 'week']) }}"
                       class="btn btn-xs {{ $period == 'week' ? 'btn-gold' : 'btn-outline-gold' }}">Week</a>
                    <a href="{{ route('referral.earnings', ['period' => 'month']) }}"
                       class="btn btn-xs {{ $period == 'month' ? 'btn-gold' : 'btn-outline-gold' }}">Month</a>
                    <a href="{{ route('referral.earnings', ['period' => 'year']) }}"
                       class="btn btn-xs {{ $period == 'year' ? 'btn-gold' : 'btn-outline-gold' }}">Year</a>
                    <a href="{{ route('referral.earnings', ['period' => 'all']) }}"
                       class="btn btn-xs {{ $period == 'all' ? 'btn-gold' : 'btn-outline-gold' }}">All Time</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings Cards -->
    <div class="row g-3 mb-3">
        <div class="col-4">
            <div class="card earnings-card pending">
                <div class="card-body text-center py-3">
                    <div class="earnings-icon mb-2">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="earnings-label">Pending</div>
                    <div class="earnings-value warning">AED {{ number_format($earningsByStatus['pending'], 2) }}</div>
                    <small class="text-muted">Awaiting approval</small>
                </div>
            </div>
        </div>

        <div class="col-4">
            <div class="card earnings-card approved">
                <div class="card-body text-center py-3">
                    <div class="earnings-icon mb-2">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="earnings-label">Approved</div>
                    <div class="earnings-value info">AED {{ number_format($earningsByStatus['approved'], 2) }}</div>
                    <small class="text-muted">Ready for payout</small>
                </div>
            </div>
        </div>

        <div class="col-4">
            <div class="card earnings-card paid">
                <div class="card-body text-center py-3">
                    <div class="earnings-icon mb-2">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="earnings-label">Paid</div>
                    <div class="earnings-value success">AED {{ number_format($earningsByStatus['paid'], 2) }}</div>
                    <small class="text-muted">Successfully paid out</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Earnings Banner -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card total-earnings-card">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-md-5 text-center text-md-start mb-2 mb-md-0">
                            <small class="text-muted">Total Lifetime Earnings</small>
                            <h4 class="mb-0" style="color: var(--primary-gold);">
                                AED {{ number_format($earningsByStatus['pending'] + $earningsByStatus['approved'] + $earningsByStatus['paid'], 2) }}
                            </h4>
                        </div>
                        <div class="col-md-7">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="stat-num">{{ number_format($stats['total_orders']) }}</div>
                                    <small class="text-muted">Orders</small>
                                </div>
                                <div class="col-4">
                                    <div class="stat-num">{{ number_format($stats['total_clicks']) }}</div>
                                    <small class="text-muted">Clicks</small>
                                </div>
                                <div class="col-4">
                                    <div class="stat-num">{{ $stats['conversion_rate'] }}%</div>
                                    <small class="text-muted">Conversion</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Earnings Chart -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="card-title"><i class="fas fa-chart-line me-2" style="color: var(--primary-gold);"></i>Monthly Earnings</span>
                    <small class="text-muted">Last 12 months</small>
                </div>
                <div class="card-body">
                    <canvas id="earningsChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .container { max-width: 1100px; }

    /* Buttons */
    .btn-xs {
        padding: 4px 10px;
        font-size: 0.7rem;
    }
    .btn-gold {
        background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
        border: none;
        color: #000;
        font-weight: 600;
    }
    .btn-outline-gold {
        border: 1px solid var(--primary-gold);
        color: var(--primary-gold);
        background: transparent;
    }
    .btn-outline-gold:hover {
        background: var(--primary-gold);
        color: #000;
    }

    /* Earnings Cards */
    .earnings-card {
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    .earnings-card:hover {
        transform: translateY(-2px);
    }
    .earnings-card.pending {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), transparent);
        border: 1px solid rgba(245, 158, 11, 0.2);
    }
    .earnings-card.approved {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), transparent);
        border: 1px solid rgba(59, 130, 246, 0.2);
    }
    .earnings-card.paid {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), transparent);
        border: 1px solid rgba(34, 197, 94, 0.2);
    }
    .earnings-icon {
        font-size: 1.1rem;
    }
    .earnings-card.pending .earnings-icon { color: var(--warning); }
    .earnings-card.approved .earnings-icon { color: var(--info); }
    .earnings-card.paid .earnings-icon { color: var(--success); }
    .earnings-label {
        font-size: 0.7rem;
        color: var(--text-muted);
        margin-bottom: 4px;
    }
    .earnings-value {
        font-size: 1rem;
        font-weight: 700;
    }
    .earnings-value.warning { color: var(--warning); }
    .earnings-value.info { color: var(--info); }
    .earnings-value.success { color: var(--success); }

    /* Total Card */
    .total-earnings-card {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(255, 165, 0, 0.05));
        border: 1px solid var(--border-gold);
        border-radius: 10px;
    }
    .stat-num {
        font-size: 1rem;
        font-weight: 700;
        color: #fff;
    }

    /* Card */
    .card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 10px;
    }
    .card-header {
        background: transparent;
        border-bottom: 1px solid var(--border-color);
        padding: 10px 14px;
    }
    .card-title {
        font-size: 0.8rem;
        font-weight: 500;
        color: #fff;
    }
    .card-body {
        padding: 14px;
    }

    /* Text */
    h1, h2, h3, h4, h5, h6, strong { color: #fff !important; }
    p, span, div, small { color: #e2e8f0; }
    .text-muted { color: #a0aec0 !important; }
    small { font-size: 0.65rem; }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('earningsChart').getContext('2d');
    const monthlyEarnings = @json($monthlyEarnings);

    const gradient = ctx.createLinearGradient(0, 0, 0, 200);
    gradient.addColorStop(0, 'rgba(255, 215, 0, 0.3)');
    gradient.addColorStop(1, 'rgba(255, 215, 0, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyEarnings.map(e => e.month),
            datasets: [{
                label: 'Earnings (AED)',
                data: monthlyEarnings.map(e => e.amount),
                borderColor: '#FFD700',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#FFD700',
                pointBorderColor: '#FFD700',
                pointRadius: 3,
                pointHoverRadius: 5,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#16161a',
                    borderColor: 'rgba(255, 215, 0, 0.2)',
                    borderWidth: 1,
                    titleColor: '#e2e8f0',
                    titleFont: { size: 11 },
                    bodyColor: '#FFD700',
                    bodyFont: { size: 11 },
                    padding: 8,
                    callbacks: {
                        label: function(context) {
                            return 'AED ' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { color: '#8a8f98', font: { size: 10 } }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: {
                        color: '#8a8f98',
                        font: { size: 10 },
                        callback: function(value) { return 'AED ' + value; }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
