@extends('layouts.client')

@section('title', 'Order #' . ($order->order_reference ?? $order->id))

@section('content')
<div class="page-header">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('client.orders') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="page-title mb-0">Order #{{ $order->order_reference ?? $order->id }}</h1>
            <small class="text-muted">{{ $order->created_at->format('M d, Y H:i') }}</small>
        </div>
        <span class="badge bg-{{ ($order->status ?? $order->payment_status) === 'completed' ? 'success' : (($order->status ?? $order->payment_status) === 'pending' ? 'warning' : 'danger') }} ms-2 fs-6">
            {{ ucfirst($order->status ?? $order->payment_status ?? 'pending') }}
        </span>
    </div>
</div>

<div class="row g-4">
    <!-- Order Details -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-sim-card me-2"></i>eSIM Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted d-block mb-1">Country</label>
                        <span class="fw-500">{{ $order->country_name ?? '-' }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted d-block mb-1">Bundle</label>
                        <span class="fw-500">{{ $order->bundle_name ?? '-' }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted d-block mb-1">Data</label>
                        <span class="fw-500">{{ $order->data_amount ?? '-' }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted d-block mb-1">Validity</label>
                        <span class="fw-500">{{ $order->validity_days ?? '-' }} days</span>
                    </div>
                    @if($order->monty_iccid)
                    <div class="col-12">
                        <label class="text-muted d-block mb-1">ICCID</label>
                        <code class="fs-6">{{ $order->monty_iccid }}</code>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="fas fa-user me-2"></i>Customer Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted d-block mb-1">Name</label>
                        <span class="fw-500">{{ $order->customer_name ?? $order->user->name ?? '-' }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted d-block mb-1">Email</label>
                        <span class="fw-500">{{ $order->customer_email ?? $order->user->email ?? '-' }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted d-block mb-1">Phone</label>
                        <span class="fw-500">{{ $order->customer_phone ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment & Referral -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-receipt me-2"></i>Payment Summary</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Cost Price</td>
                        <td class="text-end">{{ app('current_company')->currency ?? 'AED' }} {{ number_format($order->monty_cost_price ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Selling Price</td>
                        <td class="text-end">{{ app('current_company')->currency ?? 'AED' }} {{ number_format($order->selling_price ?? $order->total_amount ?? 0, 2) }}</td>
                    </tr>
                    <tr class="border-top">
                        <td class="text-muted"><strong>Profit</strong></td>
                        <td class="text-end text-success">
                            <strong>{{ app('current_company')->currency ?? 'AED' }} {{ number_format(($order->selling_price ?? $order->total_amount ?? 0) - ($order->monty_cost_price ?? 0), 2) }}</strong>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        @if($order->referralAgent)
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-user-friends me-2"></i>Referral Agent</div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: var(--client-primary); color: #000;">
                            {{ strtoupper(substr($order->referralAgent->name, 0, 1)) }}
                        </div>
                    </div>
                    <div>
                        <div class="fw-500">{{ $order->referralAgent->name }}</div>
                        <small class="text-muted">{{ $order->referralAgent->email }}</small>
                    </div>
                </div>
                <hr>
                <small class="text-muted">Referral Code:</small>
                <code>{{ $order->referralAgent->referral_code }}</code>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header"><i class="fas fa-clock me-2"></i>Timeline</div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Created</small>
                    <span>{{ $order->created_at->format('M d, Y H:i:s') }}</span>
                </div>
                <div>
                    <small class="text-muted d-block">Last Updated</small>
                    <span>{{ $order->updated_at->format('M d, Y H:i:s') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
