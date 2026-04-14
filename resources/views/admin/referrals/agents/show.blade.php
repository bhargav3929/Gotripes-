@extends('layouts.admin')

@section('title', 'Agent Details - ' . $agent->name)

@section('content')
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.referrals.agents.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h5 class="mb-0 text-gold">{{ $agent->name }}</h5>
                <small class="text-muted">{{ $agent->email }}</small>
            </div>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <a href="{{ route('admin.referrals.agents.edit', $agent) }}" class="btn btn-outline-warning btn-sm">
                <i class="fas fa-edit me-1"></i>Edit
            </a>
            <span class="badge bg-{{ $agent->status === 'active' ? 'success' : ($agent->status === 'suspended' ? 'danger' : 'secondary') }}">
                {{ ucfirst($agent->status) }}
            </span>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-2 mb-3">
        <div class="col-6 col-lg-3">
            <div class="stat-box">
                <i class="fas fa-mouse-pointer text-gold"></i>
                <div class="stat-value">{{ number_format($stats['total_clicks']) }}</div>
                <small class="text-muted">Clicks</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-box">
                <i class="fas fa-shopping-cart text-info"></i>
                <div class="stat-value">{{ number_format($stats['total_sales']) }}</div>
                <small class="text-muted">Sales</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-box">
                <i class="fas fa-coins text-success"></i>
                <div class="stat-value">AED {{ number_format($stats['total_commission'], 2) }}</div>
                <small class="text-muted">Commission</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-box">
                <i class="fas fa-exchange-alt text-warning"></i>
                <div class="stat-value">{{ $stats['conversion_rate'] }}%</div>
                <small class="text-muted">Conversion</small>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Agent Info -->
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header"><i class="fas fa-user me-2 text-gold"></i>Agent Info</div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Referral Code</small>
                        <div class="d-flex align-items-center gap-2">
                            <code class="ref-code flex-grow-1">{{ $agent->referral_code }}</code>
                            <button class="btn btn-xs btn-outline-gold copy-btn" data-copy="{{ $agent->referral_url }}">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Full URL</small>
                        <input type="text" class="form-control form-control-sm" value="{{ $agent->referral_url }}" readonly>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Commission</small>
                        <div>
                            @if($agent->commission_type === 'percentage')
                                <span class="badge bg-info">{{ $agent->commission_value }}% per order</span>
                            @else
                                <span class="badge bg-success">AED {{ number_format($agent->commission_value, 2) }} per order</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-row"><span>Phone</span><span>{{ $agent->phone ?? 'N/A' }}</span></div>
                    <div class="info-row"><span>Created</span><span>{{ $agent->created_at->format('M d, Y') }}</span></div>
                    <div class="info-row"><span>Last Login</span><span>{{ $agent->last_login_at ? $agent->last_login_at->format('M d, Y H:i') : 'Never' }}</span></div>
                </div>
            </div>

            <!-- Earnings -->
            <div class="card">
                <div class="card-header"><i class="fas fa-chart-pie me-2 text-gold"></i>Earnings</div>
                <div class="card-body">
                    <div class="row g-2 mb-2">
                        <div class="col-4">
                            <div class="earnings-box pending">
                                <small>Pending</small>
                                <div class="earnings-value">{{ number_format($stats['pending_commission'], 0) }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="earnings-box approved">
                                <small>Approved</small>
                                <div class="earnings-value">{{ number_format($stats['total_commission'] - $stats['paid_commission'], 0) }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="earnings-box paid">
                                <small>Paid</small>
                                <div class="earnings-value">{{ number_format($stats['paid_commission'], 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="info-row"><span>Total Orders</span><span>{{ number_format($stats['total_orders']) }}</span></div>
                    <div class="info-row"><span>Revenue</span><span>AED {{ number_format($stats['total_revenue'], 2) }}</span></div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-receipt me-2 text-gold"></i>Recent Orders</span>
                    <a href="{{ route('admin.referrals.commissions.index', ['agent_id' => $agent->id]) }}" class="btn btn-xs btn-outline-gold">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th class="text-end">Amount</th>
                                <th class="text-end">Commission</th>
                                <th class="text-center">Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td><code class="order-id">#{{ $order->order_id }}</code></td>
                                <td>
                                    <div>{{ $order->customer_name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $order->customer_email }}</small>
                                </td>
                                <td class="text-end">AED {{ number_format($order->order_amount, 2) }}</td>
                                <td class="text-end commission-amt">AED {{ number_format($order->commission_amount, 2) }}</td>
                                <td class="text-center"><span class="badge bg-{{ $order->status_badge }}">{{ ucfirst($order->status) }}</span></td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-3 text-muted">No orders yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Clicks -->
            <div class="card">
                <div class="card-header"><i class="fas fa-mouse-pointer me-2 text-gold"></i>Recent Clicks</div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentClicks as $click)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <i class="fas fa-{{ $click->device_type === 'mobile' ? 'mobile-alt' : ($click->device_type === 'tablet' ? 'tablet-alt' : 'desktop') }} text-muted me-1"></i>
                                    <span>{{ $click->browser }} / {{ $click->os }}</span>
                                    <small class="text-muted ms-2">{{ $click->ip_address }}</small>
                                </div>
                                <div class="text-end">
                                    @if($click->converted)<span class="badge bg-success">Converted</span>@endif
                                    <small class="text-muted d-block">{{ $click->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item text-center text-muted py-3">No clicks recorded</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-gold { color: var(--primary-gold) !important; }

    /* Card */
    .card { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 8px; }
    .card-header { background: transparent; border-bottom: 1px solid var(--border-color); padding: 10px 14px; font-size: 0.8rem; font-weight: 500; color: #fff; }
    .card-body { padding: 14px; }

    /* Stat Box */
    .stat-box { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 8px; padding: 12px; text-align: center; }
    .stat-box i { font-size: 1rem; margin-bottom: 4px; }
    .stat-value { font-size: 1rem; font-weight: 700; color: #fff; }

    /* Form */
    .form-control { background: var(--light-dark); border: 1px solid var(--border-color); color: var(--text-main); font-size: 0.7rem; }
    .form-control-sm { padding: 6px 10px; }

    /* Ref Code */
    .ref-code { background: rgba(255, 215, 0, 0.1); color: var(--primary-gold); padding: 6px 10px; border-radius: 4px; font-size: 0.8rem; display: block; text-align: center; }

    /* Info Row */
    .info-row { display: flex; justify-content: space-between; font-size: 0.75rem; padding: 4px 0; border-bottom: 1px solid var(--border-color); }
    .info-row:last-child { border-bottom: none; }
    .info-row span:first-child { color: var(--text-muted); }

    /* Earnings Box */
    .earnings-box { text-align: center; padding: 8px; border-radius: 6px; }
    .earnings-box.pending { background: rgba(245, 158, 11, 0.1); }
    .earnings-box.pending .earnings-value { color: var(--warning); }
    .earnings-box.approved { background: rgba(59, 130, 246, 0.1); }
    .earnings-box.approved .earnings-value { color: var(--info); }
    .earnings-box.paid { background: rgba(34, 197, 94, 0.1); }
    .earnings-box.paid .earnings-value { color: var(--success); }
    .earnings-box small { font-size: 0.6rem; color: var(--text-muted); }
    .earnings-box .earnings-value { font-size: 0.85rem; font-weight: 700; }

    /* Table */
    .table { font-size: 0.75rem; background: transparent !important; }
    .table thead th { background: rgba(0,0,0,0.2) !important; color: var(--text-muted); font-size: 0.65rem; font-weight: 500; text-transform: uppercase; padding: 8px 10px; border-bottom: 1px solid var(--border-color); }
    .table tbody td { padding: 8px 10px; border-bottom: 1px solid var(--border-color); background: transparent !important; color: #e2e8f0; vertical-align: middle; }
    .table tbody tr:hover { background: rgba(255,215,0,0.03) !important; }
    .order-id { color: var(--primary-gold); }
    .commission-amt { color: #22c55e; font-weight: 600; }

    /* List Group */
    .list-group-item { background: transparent; border-color: var(--border-color); padding: 10px 14px; font-size: 0.75rem; color: #e2e8f0; }

    /* Buttons */
    .btn-outline-gold { border: 1px solid var(--primary-gold); color: var(--primary-gold); }
    .btn-outline-gold:hover { background: var(--primary-gold); color: #000; }
    .btn-sm { padding: 6px 12px; font-size: 0.75rem; }
    .btn-xs { padding: 3px 8px; font-size: 0.65rem; }

    /* Badge */
    .badge { font-size: 0.6rem; padding: 3px 6px; }

    /* Text */
    h5 { color: #fff !important; }
    small { font-size: 0.65rem; }
</style>

@push('scripts')
<script>
document.querySelectorAll('.copy-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        navigator.clipboard.writeText(this.dataset.copy).then(() => {
            const icon = this.querySelector('i');
            icon.className = 'fas fa-check';
            setTimeout(() => icon.className = 'fas fa-copy', 2000);
        });
    });
});
</script>
@endpush
@endsection
