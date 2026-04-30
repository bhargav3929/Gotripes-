@extends('layouts.superadmin')
@section('title', 'Withdrawals')

@section('content')
<style>
    .stat-grid { display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; margin-bottom:24px; }
    .stat-c { background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:18px 20px; }
    .stat-c .lbl { font-size:11px; letter-spacing:1px; text-transform:uppercase; color:#888; margin-bottom:8px; }
    .stat-c .val { font-size:22px; font-weight:700; color:#111; }
    .stat-c .val small { font-size:13px; color:#666; }
    .stat-c.pending .val  { color:#d97706; }
    .stat-c.approved .val { color:#2563eb; }
    .stat-c.paid .val     { color:#16a34a; }
    .pill { display:inline-block; padding:3px 9px; border-radius:99px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.4px; }
    .pill.pending  { background:rgba(217,119,6,0.12); color:#d97706; }
    .pill.approved { background:rgba(37,99,235,0.12); color:#2563eb; }
    .pill.paid     { background:rgba(22,163,74,0.12); color:#16a34a; }
    .pill.rejected { background:rgba(239,68,68,0.12); color:#dc2626; }
    @media (max-width:900px) { .stat-grid { grid-template-columns:repeat(2, 1fr); } }
</style>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-money-bill-transfer"></i>Withdrawals</h1>
    <form method="POST" action="{{ route('superadmin.withdrawals.release') }}" onsubmit="return confirm('Release all pending commissions older than 24h to available status?');" style="margin:0;">
        @csrf
        <input type="hidden" name="hours" value="24">
        <button class="btn btn-outline-success btn-sm"><i class="fas fa-unlock me-1"></i>Release pending commissions (>24h)</button>
    </form>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

<div class="stat-grid">
    <div class="stat-c pending"><div class="lbl">Pending Requests</div><div class="val">{{ number_format($stats['pending'], 2) }} <small>AED</small></div></div>
    <div class="stat-c approved"><div class="lbl">Approved (unpaid)</div><div class="val">{{ number_format($stats['approved'], 2) }} <small>AED</small></div></div>
    <div class="stat-c paid"><div class="lbl">Paid Out</div><div class="val">{{ number_format($stats['paid'], 2) }} <small>AED</small></div></div>
    <div class="stat-c"><div class="lbl">All-Time Commission</div><div class="val">{{ number_format($stats['all_time_commission'], 2) }} <small>AED</small></div></div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Tenant</th>
                    <th>Bank</th>
                    <th class="text-end">Amount</th>
                    <th>Status</th>
                    <th class="text-end" style="min-width:280px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($withdrawals as $w)
                    <tr>
                        <td><small>{{ $w->created_at->format('Y-m-d H:i') }}</small></td>
                        <td>
                            <strong>{{ $w->company?->name ?? '—' }}</strong><br>
                            <small class="text-muted">{{ $w->company?->subdomain }}.gotrips.ai</small>
                        </td>
                        <td>
                            @if($w->bankAccount)
                                {{ $w->bankAccount->bank_name }}<br>
                                <small class="text-muted">****{{ substr($w->bankAccount->account_number, -4) }} · {{ $w->bankAccount->account_holder_name }}</small>
                            @else
                                <em class="text-muted">deleted</em>
                            @endif
                        </td>
                        <td class="text-end"><strong>{{ $w->currency }} {{ number_format($w->amount, 2) }}</strong></td>
                        <td>
                            <span class="pill {{ $w->status }}">{{ $w->status }}</span>
                            @if($w->payment_reference)<br><small class="text-muted">ref: {{ $w->payment_reference }}</small>@endif
                        </td>
                        <td class="text-end">
                            @if($w->status === 'pending')
                                <form method="POST" action="{{ route('superadmin.withdrawals.approve', $w) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-outline-primary btn-sm">Approve</button>
                                </form>
                                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#reject-{{ $w->id }}">Reject</button>
                            @endif
                            @if(in_array($w->status, ['pending', 'approved']))
                                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#paid-{{ $w->id }}"><i class="fas fa-check"></i> Mark Paid</button>
                            @endif

                            <!-- Mark Paid modal -->
                            <div class="modal fade" id="paid-{{ $w->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form method="POST" action="{{ route('superadmin.withdrawals.paid', $w) }}" class="modal-content">
                                        @csrf
                                        <div class="modal-header"><h5 class="modal-title">Mark as Paid</h5></div>
                                        <div class="modal-body text-start">
                                            <p>Confirm transferring <strong>{{ $w->currency }} {{ number_format($w->amount, 2) }}</strong> to {{ $w->company?->name }}.</p>
                                            <label class="form-label">Bank Reference / Transaction ID *</label>
                                            <input type="text" name="payment_reference" class="form-control" required placeholder="e.g. TXN-2026-04-30-998877">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success">Confirm Paid</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Reject modal -->
                            <div class="modal fade" id="reject-{{ $w->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form method="POST" action="{{ route('superadmin.withdrawals.reject', $w) }}" class="modal-content">
                                        @csrf
                                        <div class="modal-header"><h5 class="modal-title">Reject withdrawal</h5></div>
                                        <div class="modal-body text-start">
                                            <label class="form-label">Reason *</label>
                                            <textarea name="admin_notes" class="form-control" rows="3" required></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Reject</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted p-4">No withdrawal requests yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($withdrawals->hasPages())
        <div class="card-footer">{{ $withdrawals->links() }}</div>
    @endif
</div>
@endsection
