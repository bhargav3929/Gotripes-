@extends('layouts.manager')
@section('title', 'Withdrawals')
@section('page-title', 'Withdrawals')

@section('content')
<style>
    .w-top { display:grid; grid-template-columns: 1fr 360px; gap:24px; margin-bottom:24px; align-items:flex-start; }
    @media (max-width:900px) { .w-top { grid-template-columns:1fr; } }
    .w-bal { background:linear-gradient(135deg, rgba(255,215,0,0.10), rgba(212,175,55,0.04)); border:1px solid rgba(255,215,0,0.3); border-radius:14px; padding:32px; text-align:center; }
    .w-bal .lbl { font-size:12px; letter-spacing:1.2px; text-transform:uppercase; color:#FFD700; margin-bottom:10px; }
    .w-bal .val { font-size:42px; font-weight:800; color:#fff; letter-spacing:-0.02em; }
    .w-bal .val small { font-size:18px; color:#888; margin-left:6px; }
    .w-form { background:#1a1a1a; border:1px solid rgba(255,215,0,0.18); border-radius:12px; padding:22px; }
    .w-form h3 { font-size:15px; color:#fff; margin-bottom:14px; }
    .w-form label { display:block; font-size:11px; letter-spacing:0.4px; color:#888; text-transform:uppercase; margin-bottom:5px; margin-top:10px; }
    .w-form input, .w-form select, .w-form textarea { width:100%; background:#111; border:1px solid #222; color:#fff; padding:10px 12px; border-radius:8px; font-size:14px; font-family:inherit; }
    .w-form input:focus, .w-form select:focus, .w-form textarea:focus { outline:none; border-color:#FFD700; }
    .w-form .submit { width:100%; background:linear-gradient(135deg, #FFD700, #FFA500); color:#000; border:none; padding:11px; border-radius:8px; font-weight:700; cursor:pointer; margin-top:14px; font-size:13px; letter-spacing:0.5px; }
    .w-form .submit:disabled { background:#333; color:#666; cursor:not-allowed; }
    .w-table { background:#1a1a1a; border:1px solid rgba(255,215,0,0.12); border-radius:12px; overflow:hidden; }
    .w-table table { width:100%; }
    .w-table th { background:#0d0d0d; padding:12px 16px; font-size:11px; letter-spacing:1.2px; text-transform:uppercase; color:#888; text-align:left; }
    .w-table td { padding:12px 16px; border-top:1px solid rgba(255,215,0,0.06); font-size:13px; color:#eee; }
    .pill { display:inline-block; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600; text-transform:uppercase; }
    .pill.pending { background:rgba(255,215,0,0.12); color:#FFD23F; }
    .pill.approved { background:rgba(96,165,250,0.12); color:#60a5fa; }
    .pill.paid { background:rgba(34,197,94,0.12); color:#22c55e; }
    .pill.rejected { background:rgba(239,68,68,0.12); color:#f87171; }
    .alert-warn { background:rgba(255,215,0,0.06); border-left:3px solid #FFD700; padding:14px 18px; border-radius:8px; color:#ddd; font-size:13px; }
</style>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

<div class="w-top">
    <div class="w-bal">
        <div class="lbl">Available to Withdraw</div>
        <div class="val">{{ number_format($available, 2) }}<small>{{ $company->currency ?? 'AED' }}</small></div>
    </div>

    <div class="w-form">
        <h3><i class="fas fa-money-bill-transfer" style="margin-right:6px; color:#FFD700;"></i>Request Withdrawal</h3>
        @if($accounts->isEmpty())
            <div class="alert-warn">
                Add a bank account first.
                <a href="{{ route('manager.finance.bank-accounts') }}" style="color:#FFD700; font-weight:600;">Go to Bank Accounts →</a>
            </div>
        @elseif($available < 50)
            <div class="alert-warn">
                Minimum withdrawal is <strong>AED 50</strong>. Your available balance is <strong>{{ $company->currency ?? 'AED' }} {{ number_format($available, 2) }}</strong>.
            </div>
        @else
            <form method="POST" action="{{ route('manager.finance.withdrawals.request') }}">
                @csrf
                <label>Amount ({{ $company->currency ?? 'AED' }})</label>
                <input type="number" step="0.01" min="50" max="{{ $available }}" name="amount" value="{{ $available }}" required>
                <label>To Bank Account</label>
                <select name="bank_account_id" required>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ $acc->is_primary ? 'selected' : '' }}>
                            {{ $acc->bank_name }} · {{ substr($acc->account_number, -4) }} {{ $acc->is_primary ? '(primary)' : '' }}
                        </option>
                    @endforeach
                </select>
                <label>Notes <span style="text-transform:none; font-size:10px;">(optional)</span></label>
                <textarea name="notes" rows="2" placeholder="e.g. invoice ref, urgency"></textarea>
                <button type="submit" class="submit">Request Withdrawal</button>
            </form>
        @endif
    </div>
</div>

<div class="w-table">
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Amount</th>
                <th>To Bank</th>
                <th>Status</th>
                <th>Reference</th>
            </tr>
        </thead>
        <tbody>
            @forelse($withdrawals as $w)
                <tr>
                    <td>{{ $w->created_at->format('Y-m-d H:i') }}</td>
                    <td><strong style="color:#FFD700;">{{ $w->currency }} {{ number_format($w->amount, 2) }}</strong></td>
                    <td>{{ $w->bankAccount?->bank_name ?? '—' }} {{ $w->bankAccount ? '· ****'.substr($w->bankAccount->account_number, -4) : '' }}</td>
                    <td><span class="pill {{ $w->status }}">{{ $w->status }}</span></td>
                    <td>{{ $w->payment_reference ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center; padding:36px; color:#666;">No withdrawal requests yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($withdrawals->hasPages())
        <div style="padding:14px 18px; background:#0d0d0d;">{{ $withdrawals->links() }}</div>
    @endif
</div>
@endsection
