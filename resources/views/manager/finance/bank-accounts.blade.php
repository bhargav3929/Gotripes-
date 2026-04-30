@extends('layouts.manager')
@section('title', 'Bank Accounts')
@section('page-title', 'Bank Accounts')

@section('content')
<style>
    .ba-grid { display:grid; grid-template-columns: 1fr 380px; gap:24px; align-items:flex-start; }
    @media (max-width:900px) { .ba-grid { grid-template-columns:1fr; } }
    .ba-card { background:#1a1a1a; border:1px solid rgba(255,215,0,0.15); border-radius:12px; padding:20px 22px; margin-bottom:14px; }
    .ba-card.primary { border-color: #FFD700; box-shadow:0 0 0 1px rgba(255,215,0,0.15); }
    .ba-card .bank-name { font-size:16px; font-weight:700; color:#fff; margin-bottom:4px; display:flex; align-items:center; gap:8px; }
    .ba-card .holder { color:#aaa; font-size:13px; margin-bottom:10px; }
    .ba-row { display:flex; justify-content:space-between; gap:16px; font-size:13px; padding:6px 0; color:#ccc; border-top:1px solid rgba(255,255,255,0.04); }
    .ba-row:first-of-type { border-top:none; }
    .ba-row .lbl { color:#888; letter-spacing:0.4px; font-size:11px; text-transform:uppercase; }
    .ba-form { background:#1a1a1a; border:1px solid rgba(255,215,0,0.18); border-radius:12px; padding:24px; }
    .ba-form h3 { font-size:16px; color:#fff; margin-bottom:16px; }
    .ba-form label { display:block; font-size:11px; letter-spacing:0.4px; color:#888; text-transform:uppercase; margin-bottom:5px; margin-top:12px; }
    .ba-form input { width:100%; background:#111; border:1px solid #222; color:#fff; padding:11px 14px; border-radius:8px; font-size:14px; }
    .ba-form input:focus { outline:none; border-color:#FFD700; }
    .ba-form .submit { width:100%; background:linear-gradient(135deg, #FFD700, #FFA500); color:#000; border:none; padding:12px; border-radius:8px; font-weight:700; cursor:pointer; margin-top:18px; font-size:14px; letter-spacing:0.5px; }
    .ba-form .submit:hover { transform:translateY(-1px); }
    .pill-primary { background:#FFD700; color:#000; padding:2px 8px; border-radius:99px; font-size:10px; font-weight:700; letter-spacing:0.5px; }
    .delete-form button { background:transparent; border:1px solid rgba(239,68,68,0.3); color:#f87171; padding:6px 12px; border-radius:6px; cursor:pointer; font-size:12px; }
    .delete-form button:hover { background:rgba(239,68,68,0.08); }
</style>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
@if($errors->any())
    <div class="alert alert-danger">
        <ul style="margin:0; padding-left:18px;">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="ba-grid">
    <div>
        @forelse($accounts as $acc)
            <div class="ba-card {{ $acc->is_primary ? 'primary' : '' }}">
                <div class="bank-name">
                    <i class="fas fa-university" style="color:#FFD700;"></i>
                    {{ $acc->bank_name }}
                    @if($acc->is_primary)<span class="pill-primary">Primary</span>@endif
                </div>
                <div class="holder">Account holder: {{ $acc->account_holder_name }}</div>

                <div class="ba-row"><span class="lbl">Account #</span><span>{{ $acc->account_number }}</span></div>
                @if($acc->iban)<div class="ba-row"><span class="lbl">IBAN</span><span>{{ $acc->iban }}</span></div>@endif
                @if($acc->swift_code)<div class="ba-row"><span class="lbl">SWIFT</span><span>{{ $acc->swift_code }}</span></div>@endif
                <div class="ba-row"><span class="lbl">Country</span><span>{{ $acc->country }}</span></div>

                <form method="POST" action="{{ route('manager.finance.bank-accounts.destroy', $acc) }}" class="delete-form" style="margin-top:12px;" onsubmit="return confirm('Remove this bank account?');">
                    @csrf @method('DELETE')
                    <button type="submit"><i class="fas fa-trash" style="margin-right:4px;"></i>Remove</button>
                </form>
            </div>
        @empty
            <div class="ba-card" style="text-align:center; padding:40px 20px;">
                <i class="fas fa-university" style="color:#FFD700; font-size:32px; margin-bottom:14px;"></i>
                <p style="color:#aaa;">No bank accounts yet. Add one to receive your commission payouts.</p>
            </div>
        @endforelse
    </div>

    <div class="ba-form">
        <h3><i class="fas fa-plus" style="margin-right:6px; color:#FFD700;"></i>Add Bank Account</h3>
        <form method="POST" action="{{ route('manager.finance.bank-accounts.store') }}">
            @csrf
            <label>Bank Name *</label>
            <input type="text" name="bank_name" required placeholder="e.g. Emirates NBD">
            <label>Account Holder *</label>
            <input type="text" name="account_holder_name" required placeholder="As on bank record">
            <label>Account Number *</label>
            <input type="text" name="account_number" required placeholder="0123 4567 8901 2345">
            <label>IBAN <span style="text-transform:none; font-size:10px;">(optional)</span></label>
            <input type="text" name="iban" placeholder="AE07 0331 ...">
            <label>SWIFT / BIC <span style="text-transform:none; font-size:10px;">(optional)</span></label>
            <input type="text" name="swift_code" placeholder="EBILAEAD">
            <label>Country</label>
            <input type="text" name="country" value="UAE" placeholder="UAE">
            <label style="display:flex; align-items:center; gap:8px; text-transform:none; font-size:13px; color:#ccc;">
                <input type="checkbox" name="is_primary" value="1" style="width:auto;"> Set as primary account
            </label>
            <button type="submit" class="submit">Add Account</button>
        </form>
    </div>
</div>
@endsection
