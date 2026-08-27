@extends('layouts.manager')

@section('title', 'UAE Visa Services')
@section('page-title', 'UAE Visa Packages & Pricing')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">UAE Visa Packages &amp; Pricing</h1>
</div>

<style>
    /* Deposit block: revealed only for emirates that take a refundable deposit. */
    .deposit-block {
        display: none;
        border: 1px solid var(--wp-border);
        border-left: 3px solid var(--wp-primary);
        border-radius: 4px;
        padding: 14px;
        margin: 4px 0 16px;
        background: rgba(255, 215, 0, 0.04);
    }
    .deposit-block.show {
        display: block;
    }
    .deposit-block-title {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--wp-primary);
        margin-bottom: 10px;
    }

    .field-group-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--wp-text-muted);
        margin: 4px 0 10px;
        padding-bottom: 6px;
        border-bottom: 1px solid var(--wp-border-light);
    }

    /* Two-column field layout used inside the Create Package modal. */
    .field-cols { display: flex; gap: 20px; flex-wrap: wrap; }
    .field-cols > div { flex: 1 1 260px; min-width: 0; }

    /* ── Existing Packages: full-width wide table ───────────────── */
    .wp-table-wide th, .wp-table-wide td { vertical-align: top; }
    .wp-table-wide col.col-emirate  { width: 160px; }
    .wp-table-wide col.col-name     { width: 230px; }
    .wp-table-wide col.col-type     { width: 150px; }
    .wp-table-wide col.col-supplier { width: 210px; }
    .wp-table-wide col.col-company  { width: 210px; }
    .wp-table-wide col.col-status   { width: 140px; }
    .wp-table-wide col.col-pricing  { width: 150px; }
    .wp-table-wide col.col-actions  { width: 90px; }

    .pricing-toggle-btn { width: 100%; display: flex; align-items: center; justify-content: center; gap: 6px; }
    .pricing-toggle-btn .fa-chevron-down { transition: transform 0.15s ease; font-size: 10px; }
    .pricing-toggle-btn[aria-expanded="true"] .fa-chevron-down { transform: rotate(180deg); }

    .pkg-expand-row > td { padding: 0 !important; border-bottom: 1px solid var(--wp-border-light); }
    .pkg-expand-body {
        padding: 16px 20px;
        background: rgba(255, 215, 0, 0.02);
        border-top: 1px dashed var(--wp-border);
    }
    .pkg-pricing-section .wp-table td { padding: 6px 8px; }
    .pkg-pricing-section .wp-table th { padding: 8px; }
    .pkg-nationality-override summary {
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
        color: var(--wp-primary);
        margin-top: 10px;
    }

    .wp-card-header-actions { display: flex; align-items: center; gap: 8px; }

    /* ── Modal polish: give Create Package / Add-On Fees real depth and a
       gold accent instead of blending flat into the page behind them. ── */
    .gt-modal {
        border: 1px solid rgba(255, 215, 0, 0.35);
        border-radius: 10px;
        box-shadow: 0 0 0 1px rgba(255, 215, 0, 0.08), 0 0 48px rgba(255, 215, 0, 0.10), 0 24px 64px rgba(0, 0, 0, 0.6);
        overflow: hidden;
    }
    .gt-modal .modal-header {
        position: relative;
        padding: 18px 24px;
        background: linear-gradient(180deg, rgba(255, 215, 0, 0.06), transparent);
        border-bottom: 1px solid var(--wp-border);
    }
    .gt-modal .modal-header::after {
        content: '';
        position: absolute; left: 0; right: 0; bottom: -1px; height: 2px;
        background: linear-gradient(90deg, var(--wp-primary), rgba(255, 215, 0, 0));
    }
    .gt-modal .modal-title {
        display: flex; align-items: center; gap: 10px;
        font-size: 17px; font-weight: 700; color: var(--wp-text);
    }
    .gt-modal .modal-title-icon {
        display: inline-flex; align-items: center; justify-content: center;
        width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
        background: rgba(255, 215, 0, 0.12); color: var(--wp-primary); font-size: 14px;
    }
    .gt-modal .btn-close {
        opacity: 0.55; transition: opacity 0.15s ease, transform 0.15s ease;
    }
    .gt-modal .btn-close:hover { opacity: 1; transform: rotate(90deg); }
    .gt-modal .modal-body { padding: 24px; }
    .gt-modal .modal-footer {
        padding: 16px 24px;
        background: rgba(0, 0, 0, 0.15);
        border-top: 1px solid var(--wp-border-light);
    }
    .gt-modal .field-group-label {
        color: var(--wp-primary);
        opacity: 0.9;
    }
    /* The one price row a new package launches with — boxed like the
       deposit block so it reads as "this matters", not another bare field. */
    .price-row-box {
        border: 1px solid var(--wp-border);
        border-left: 3px solid var(--wp-primary);
        border-radius: 4px;
        padding: 14px 14px 4px;
        margin-bottom: 8px;
        background: rgba(255, 215, 0, 0.03);
    }
</style>

<div class="wp-card">
    <div class="wp-card-header" style="display:flex; align-items:center; justify-content:space-between;">
        <span><i class="fas fa-box-open text-secondary-wp"></i> Existing Packages</span>
        <span class="wp-card-header-actions">
            <button type="button" class="wp-btn wp-btn-secondary wp-btn-sm" data-bs-toggle="modal" data-bs-target="#addOnFeesModal">
                <i class="fas fa-cart-plus"></i> Add-On Fees
            </button>
            <button type="button" class="wp-btn wp-btn-primary wp-btn-sm" data-bs-toggle="modal" data-bs-target="#createPackageModal">
                <i class="fas fa-plus"></i> Add Package
            </button>
        </span>
    </div>
    <div class="wp-card-body">
        @if($packages->isEmpty())
            <p class="text-center py-4 mb-0">
                No visa packages yet.
                <button type="button" class="wp-btn wp-btn-primary wp-btn-sm" data-bs-toggle="modal" data-bs-target="#createPackageModal">
                    <i class="fas fa-plus"></i> Add your first package
                </button>
            </p>
        @else
            <div class="table-responsive">
                <table class="wp-table wp-table-wide">
                    <colgroup>
                        <col class="col-emirate"><col class="col-name"><col class="col-type">
                        <col class="col-supplier"><col class="col-company"><col class="col-status">
                        <col class="col-pricing"><col class="col-actions">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Emirate</th>
                            <th>Package Name</th>
                            <th>Type</th>
                            <th>Supplier Email</th>
                            <th>Company Email</th>
                            <th>Status</th>
                            <th>Pricing</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($packages as $p)
                            @php $activeCount = $p->prices->where('isActive', 1)->count(); @endphp
                            <tr>
                                <td>
                                    <select class="wp-input pkg-emirate" name="emirates_id" data-package-id="{{ $p->id }}" form="pkg-update-{{ $p->id }}" required>
                                        @foreach($emirates as $e)
                                            <option value="{{ $e->emiratesID }}" {{ $p->emirates_id == $e->emiratesID ? 'selected' : '' }}>{{ $e->emiratesName }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="wp-input" name="name" value="{{ $p->name }}" form="pkg-update-{{ $p->id }}" required maxlength="100">
                                </td>
                                <td>
                                    <select class="wp-input" name="package_type" form="pkg-update-{{ $p->id }}" required>
                                        <option value="Standard" {{ $p->package_type === 'Standard' || !$p->package_type ? 'selected' : '' }}>Standard</option>
                                        <option value="Urgent" {{ $p->package_type === 'Urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="wp-input" name="supplier_email" value="{{ $p->supplier_email }}" form="pkg-update-{{ $p->id }}" placeholder="supplier@example.com">
                                </td>
                                <td>
                                    <input type="text" class="wp-input" name="company_email" value="{{ $p->company_email }}" form="pkg-update-{{ $p->id }}" placeholder="visas@gotrips.ai">
                                </td>
                                <td>
                                    <select class="wp-input" name="isActive" form="pkg-update-{{ $p->id }}">
                                        <option value="1" {{ $p->isActive ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ !$p->isActive ? 'selected' : '' }}>Disabled</option>
                                    </select>
                                </td>
                                <td>
                                    <button type="button" class="wp-btn wp-btn-secondary wp-btn-sm pricing-toggle-btn" data-bs-toggle="collapse" data-bs-target="#pricing-row-{{ $p->id }}" aria-expanded="false" aria-controls="pricing-row-{{ $p->id }}">
                                        <span class="wp-badge {{ $activeCount ? 'wp-badge-green' : 'wp-badge-red' }}">{{ $activeCount }} active</span>
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                </td>
                                <td>
                                    <form id="pkg-update-{{ $p->id }}" action="{{ route('manager.visa-packages.update', $p->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <button type="submit" class="wp-btn wp-btn-secondary wp-btn-sm" title="Save"><i class="fas fa-save"></i></button>
                                    </form>
                                    <form action="{{ route('manager.visa-packages.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Remove this package and all associated prices?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="wp-btn wp-btn-danger wp-btn-sm" title="Delete"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="pkg-expand-row">
                                <td colspan="8">
                                    <div class="collapse" id="pricing-row-{{ $p->id }}">
                                        <div class="pkg-expand-body">

                                            <div class="field-cols">
                                                <div>
                                                    <div class="wp-form-group mb-0">
                                                        <label class="wp-form-label">Description</label>
                                                        <textarea class="wp-input" name="description" form="pkg-update-{{ $p->id }}" rows="2" maxlength="1000" placeholder="Processing time, requirements, anything the customer should know...">{{ $p->description }}</textarea>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="deposit-block" id="pkgDepositBlock-{{ $p->id }}" style="margin:0;">
                                                        <div class="deposit-block-title">
                                                            <i class="fas fa-shield-halved"></i> Refundable security deposit
                                                        </div>
                                                        <div class="field-cols">
                                                            <div class="wp-form-group mb-0">
                                                                <label class="wp-form-label">Default Security Deposit (AED)</label>
                                                                <input type="number" class="wp-input" name="security_deposit" value="{{ $p->security_deposit }}" form="pkg-update-{{ $p->id }}" step="0.01" min="0" placeholder="Deposit (AED)">
                                                            </div>
                                                            <div class="wp-form-group mb-0">
                                                                <label class="wp-form-label">Default Processing Fee (AED)</label>
                                                                <input type="number" class="wp-input" name="deposit_admin_fee" value="{{ $p->deposit_admin_fee }}" form="pkg-update-{{ $p->id }}" step="0.01" min="0" placeholder="Processing fee">
                                                            </div>
                                                        </div>
                                                        <p class="wp-form-help" style="margin-top:6px;">Used for every nationality with no override below (e.g. India/Pakistan/Nepal &rarr; AED 1,040).</p>

                                                        @if($p->deposits->isNotEmpty())
                                                            <table class="wp-table" style="margin-top:10px;">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Nationality</th>
                                                                        <th style="width:110px;">Deposit</th>
                                                                        <th style="width:110px;">Fee</th>
                                                                        <th style="width:110px;">Status</th>
                                                                        <th style="width:40px;"></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($p->deposits as $d)
                                                                        <tr>
                                                                            <td>
                                                                                <form id="deposit-update-{{ $d->id }}" action="{{ route('manager.visa-deposits.update', $d->id) }}" method="POST" style="display:none;">
                                                                                    @csrf @method('PUT')
                                                                                </form>
                                                                                <form id="delete-deposit-{{ $d->id }}" action="{{ route('manager.visa-deposits.destroy', $d->id) }}" method="POST" style="display:none;">
                                                                                    @csrf @method('DELETE')
                                                                                </form>
                                                                                <input type="text" class="wp-input" name="nationality" value="{{ $d->nationality }}" form="deposit-update-{{ $d->id }}" placeholder="All (default)" maxlength="100">
                                                                            </td>
                                                                            <td>
                                                                                <input type="number" class="wp-input" name="security_deposit" value="{{ $d->security_deposit }}" form="deposit-update-{{ $d->id }}" step="0.01" min="0" required>
                                                                            </td>
                                                                            <td>
                                                                                <input type="number" class="wp-input" name="deposit_admin_fee" value="{{ $d->deposit_admin_fee }}" form="deposit-update-{{ $d->id }}" step="0.01" min="0">
                                                                            </td>
                                                                            <td>
                                                                                <select class="wp-input" name="isActive" form="deposit-update-{{ $d->id }}">
                                                                                    <option value="1" {{ $d->isActive ? 'selected' : '' }}>Active</option>
                                                                                    <option value="0" {{ !$d->isActive ? 'selected' : '' }}>Disabled</option>
                                                                                </select>
                                                                            </td>
                                                                            <td>
                                                                                <button type="submit" class="wp-btn wp-btn-secondary wp-btn-sm" form="deposit-update-{{ $d->id }}" title="Save"><i class="fas fa-save"></i></button>
                                                                                <button type="button" class="wp-btn wp-btn-danger wp-btn-sm" title="Delete"
                                                                                        onclick="if(confirm('Remove this deposit row?')) document.getElementById('delete-deposit-{{ $d->id }}').submit();">
                                                                                    <i class="fas fa-trash"></i>
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        @endif

                                                        <details class="pkg-nationality-override">
                                                            <summary>+ Add nationality-specific deposit</summary>
                                                            <form action="{{ route('manager.visa-deposits.store') }}" method="POST" class="row g-2 mt-2">
                                                                @csrf
                                                                <input type="hidden" name="visa_package_id" value="{{ $p->id }}">
                                                                <div class="col-6 col-md-4">
                                                                    <input type="text" class="wp-input" name="nationality" placeholder="Nationality (e.g. India)" maxlength="100" required>
                                                                </div>
                                                                <div class="col-6 col-md-3">
                                                                    <input type="number" class="wp-input" name="security_deposit" step="0.01" min="0" placeholder="Deposit (AED)" required>
                                                                </div>
                                                                <div class="col-6 col-md-3">
                                                                    <input type="number" class="wp-input" name="deposit_admin_fee" step="0.01" min="0" placeholder="Processing fee (AED)">
                                                                </div>
                                                                <div class="col-6 col-md-2">
                                                                    <button type="submit" class="wp-btn wp-btn-secondary wp-btn-sm w-100" title="Add deposit"><i class="fas fa-plus"></i></button>
                                                                </div>
                                                            </form>
                                                        </details>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="pkg-pricing-section">
                                                <div class="field-group-label">Pricing — every combination for this package</div>

                                                <form action="{{ route('manager.visa-prices.bulk-update') }}" method="POST">
                                                    @csrf
                                                    <div class="table-responsive">
                                                        <table class="wp-table">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width: 130px;">Entry Type</th>
                                                                    <th style="width: 100px;">Duration</th>
                                                                    <th style="width: 100px;">Visa For</th>
                                                                    <th style="width: 110px;">Nationality</th>
                                                                    <th style="width: 110px;">Price (AED)</th>
                                                                    <th style="width: 110px;">Status</th>
                                                                    <th style="width: 50px;">Del</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($p->prices as $pr)
                                                                    <tr>
                                                                        <td>
                                                                            <select class="wp-input" name="prices[{{ $pr->id }}][entry_type]" required>
                                                                                <option value="Single Entry" {{ $pr->entry_type === 'Single Entry' ? 'selected' : '' }}>Single Entry</option>
                                                                                <option value="Multiple Entry" {{ $pr->entry_type === 'Multiple Entry' ? 'selected' : '' }}>Multiple Entry</option>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <select class="wp-input" name="prices[{{ $pr->id }}][duration]" required>
                                                                                <option value="30 Days" {{ $pr->duration === '30 Days' ? 'selected' : '' }}>30 Days</option>
                                                                                <option value="60 Days" {{ $pr->duration === '60 Days' ? 'selected' : '' }}>60 Days</option>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <select class="wp-input" name="prices[{{ $pr->id }}][traveller_type]" required>
                                                                                <option value="Adult" {{ $pr->traveller_type === 'Adult' ? 'selected' : '' }}>Adult</option>
                                                                                <option value="Child" {{ $pr->traveller_type === 'Child' ? 'selected' : '' }}>Child</option>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" class="wp-input" name="prices[{{ $pr->id }}][nationality]" value="{{ $pr->nationality }}" placeholder="All" maxlength="100">
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" class="wp-input" name="prices[{{ $pr->id }}][price]" value="{{ $pr->price }}" step="0.01" min="0" required>
                                                                        </td>
                                                                        <td>
                                                                            <select class="wp-input" name="prices[{{ $pr->id }}][isActive]">
                                                                                <option value="1" {{ $pr->isActive ? 'selected' : '' }}>Active</option>
                                                                                <option value="0" {{ !$pr->isActive ? 'selected' : '' }}>Disabled</option>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <button type="button" class="wp-btn wp-btn-danger wp-btn-sm" title="Delete this row"
                                                                                    onclick="if(confirm('Remove this price row?')) document.getElementById('delete-pr-{{ $pr->id }}').submit();">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="7" class="text-center py-3">No pricing rows yet.</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    @if($p->prices->isNotEmpty())
                                                        <div class="text-end mt-2">
                                                            <button type="submit" class="wp-btn wp-btn-secondary wp-btn-sm">
                                                                <i class="fas fa-save"></i> Save Pricing
                                                            </button>
                                                        </div>
                                                    @endif
                                                </form>

                                                <details class="pkg-nationality-override">
                                                    <summary>+ Add nationality-specific override</summary>
                                                    <form action="{{ route('manager.visa-prices.store') }}" method="POST" class="row g-2 mt-2">
                                                        @csrf
                                                        <input type="hidden" name="visa_package_id" value="{{ $p->id }}">
                                                        <div class="col-6 col-md-2">
                                                            <select class="wp-input" name="entry_type" required>
                                                                <option value="Single Entry">Single Entry</option>
                                                                <option value="Multiple Entry">Multiple Entry</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-6 col-md-2">
                                                            <select class="wp-input" name="duration" required>
                                                                <option value="30 Days">30 Days</option>
                                                                <option value="60 Days">60 Days</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-6 col-md-2">
                                                            <select class="wp-input" name="traveller_type" required>
                                                                <option value="Adult">Adult</option>
                                                                <option value="Child">Child</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-6 col-md-3">
                                                            <input type="text" class="wp-input" name="nationality" placeholder="Nationality (e.g. India)" maxlength="100" required>
                                                        </div>
                                                        <div class="col-6 col-md-2">
                                                            <input type="number" class="wp-input" name="price" step="0.01" min="0" placeholder="Price (AED)" required>
                                                        </div>
                                                        <div class="col-6 col-md-1">
                                                            <button type="submit" class="wp-btn wp-btn-secondary wp-btn-sm w-100" title="Add override"><i class="fas fa-plus"></i></button>
                                                        </div>
                                                    </form>
                                                </details>
                                            </div>

                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@foreach($prices as $pr)
    <form id="delete-pr-{{ $pr->id }}" action="{{ route('manager.visa-prices.destroy', $pr->id) }}" method="POST" style="display:none;">
        @csrf @method('DELETE')
    </form>
@endforeach

{{-- ==================== Add Package modal ==================== --}}
<div class="modal fade" id="createPackageModal" tabindex="-1" aria-labelledby="createPackageModalLabel" aria-hidden="true" data-bs-theme="dark">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content gt-modal" style="background: var(--wp-white); color: var(--wp-text);">
            <div class="modal-header">
                <h5 class="modal-title" id="createPackageModalLabel">
                    <span class="modal-title-icon"><i class="fas fa-plus-circle"></i></span>
                    Create a Visa Package
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('manager.visa-packages.store') }}" method="POST" id="createPackageForm">
                @csrf
                <div class="modal-body">
                    <div class="field-cols">
                        <div>
                            <div class="wp-form-group">
                                <label class="wp-form-label">Emirate <span class="required">*</span></label>
                                <select class="wp-input" name="emirates_id" id="createEmirate" required>
                                    <option value="">Select Emirate...</option>
                                    @foreach($emirates as $e)
                                        <option value="{{ $e->emiratesID }}" {{ old('emirates_id') == $e->emiratesID ? 'selected' : '' }}>{{ $e->emiratesName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="wp-form-group">
                                <label class="wp-form-label">Package Name <span class="required">*</span></label>
                                <input type="text" class="wp-input" name="name" value="{{ old('name') }}" required placeholder="e.g. Tourist Visa" maxlength="100">
                            </div>
                            <div class="wp-form-group">
                                <label class="wp-form-label">Processing Type <span class="required">*</span></label>
                                <select class="wp-input" name="package_type" required>
                                    <option value="Standard" {{ old('package_type') === 'Standard' ? 'selected' : '' }}>Standard</option>
                                    <option value="Urgent" {{ old('package_type') === 'Urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <div class="wp-form-group">
                                <label class="wp-form-label">Description</label>
                                <textarea class="wp-input" name="description" placeholder="Processing time, requirements, anything the customer should know..." rows="2" maxlength="1000">{{ old('description') }}</textarea>
                            </div>
                            <div class="wp-form-group">
                                <label class="wp-form-label">Supplier Email</label>
                                <input type="text" class="wp-input" name="supplier_email" value="{{ old('supplier_email') }}" placeholder="supplier@example.com, second@example.com">
                                <p class="wp-form-help">Comma-separate two suppliers. Leave blank to use the company-wide supplier.</p>
                            </div>
                            <div class="wp-form-group">
                                <label class="wp-form-label">Our Company Email</label>
                                <input type="text" class="wp-input" name="company_email" value="{{ old('company_email') }}" placeholder="visas@gotrips.ai">
                            </div>
                        </div>
                    </div>

                    {{-- Only Sharjah takes a deposit today; the block is driven by a
                         data attribute so another emirate can be added server-side. --}}
                    <div class="deposit-block" id="createDepositBlock">
                        <div class="deposit-block-title">
                            <i class="fas fa-shield-halved"></i> Refundable security deposit
                        </div>
                        <div class="field-cols">
                            <div class="wp-form-group mb-0">
                                <label class="wp-form-label">Security Deposit — per applicant (AED)</label>
                                <input type="number" class="wp-input" name="security_deposit" value="{{ old('security_deposit') }}" step="0.01" min="0" placeholder="e.g. 5000">
                                <p class="wp-form-help">Charged on top of the visa price, refunded after the visit. Enter 0 for no deposit.</p>
                            </div>
                            <div class="wp-form-group mb-0">
                                <label class="wp-form-label">Processing Fee — per applicant (AED)</label>
                                <input type="number" class="wp-input" name="deposit_admin_fee" value="{{ old('deposit_admin_fee') }}" step="0.01" min="0" placeholder="e.g. 150">
                                <p class="wp-form-help">Held back when the deposit is returned. Cannot exceed the deposit.</p>
                            </div>
                        </div>
                    </div>

                    <div class="field-group-label">First price row</div>
                    <div class="price-row-box">
                        <div class="row">
                            <div class="col-6">
                                <div class="wp-form-group">
                                    <label class="wp-form-label">Visa For <span class="required">*</span></label>
                                    <select class="wp-input" name="traveller_type" required>
                                        <option value="Adult">Adult</option>
                                        <option value="Child">Child (2-12 yrs)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="wp-form-group">
                                    <label class="wp-form-label">Duration <span class="required">*</span></label>
                                    <select class="wp-input" name="duration" required>
                                        <option value="30 Days">30 Days</option>
                                        <option value="60 Days">60 Days</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="wp-form-group">
                                    <label class="wp-form-label">Entry Type <span class="required">*</span></label>
                                    <select class="wp-input" name="entry_type" required>
                                        <option value="Single Entry">Single Entry</option>
                                        <option value="Multiple Entry">Multiple Entry</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="wp-form-group">
                                    <label class="wp-form-label">Price (AED) <span class="required">*</span></label>
                                    <input type="number" class="wp-input" name="price" value="{{ old('price') }}" required step="0.01" min="0" placeholder="e.g. 350.00">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <div class="wp-form-group mb-0">
                                    <label class="wp-form-label">Nationality (optional)</label>
                                    <input type="text" class="wp-input" name="nationality" value="{{ old('nationality') }}" placeholder="e.g. India">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="wp-form-group mb-0">
                                    <label class="wp-form-label">Nationality Deposit (AED)</label>
                                    <input type="number" class="wp-input" name="nationality_security_deposit" value="{{ old('nationality_security_deposit') }}" step="0.01" min="0" placeholder="Overrides default">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="wp-form-group mb-0">
                                    <label class="wp-form-label">Nationality Processing Fee (AED)</label>
                                    <input type="number" class="wp-input" name="nationality_deposit_admin_fee" value="{{ old('nationality_deposit_admin_fee') }}" step="0.01" min="0" placeholder="Overrides default">
                                </div>
                            </div>
                        </div>
                        <p class="wp-form-help" style="margin-top:6px;">Leave Nationality blank to apply this price to everyone, or type one to make this price/deposit specific to that nationality only.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="wp-btn wp-btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="wp-btn wp-btn-primary">
                        <i class="fas fa-plus"></i> Create Package
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ==================== Add-On Fees modal ==================== --}}
<div class="modal fade" id="addOnFeesModal" tabindex="-1" aria-labelledby="addOnFeesModalLabel" aria-hidden="true" data-bs-theme="dark">
    <div class="modal-dialog">
        <div class="modal-content gt-modal" style="background: var(--wp-white); color: var(--wp-text);">
            <div class="modal-header">
                <h5 class="modal-title" id="addOnFeesModalLabel">
                    <span class="modal-title-icon"><i class="fas fa-cart-plus"></i></span>
                    Optional Add-On Fees
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('manager.visa-pricing.service-fees.update') }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="wp-form-group">
                        <label class="wp-form-label">Hotel Booking Assistance (AED)</label>
                        <input type="number" class="wp-input" name="visa_hotel_booking_fee" value="{{ old('visa_hotel_booking_fee', $hotelFee) }}" step="0.01" min="0" required>
                    </div>
                    <div class="wp-form-group mb-0">
                        <label class="wp-form-label">Flight Booking Assistance (AED)</label>
                        <input type="number" class="wp-input" name="visa_ticket_booking_fee" value="{{ old('visa_ticket_booking_fee', $ticketFee) }}" step="0.01" min="0" required>
                    </div>
                    <p class="wp-form-help">Charged only when the customer ticks the matching box at checkout. These two apply to every package.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="wp-btn wp-btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="wp-btn wp-btn-primary">
                        <i class="fas fa-save"></i> Save Add-On Fees
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Emirates that take a refundable security deposit, from the server.
        const depositEmirates = @json($depositEmirateIds);
        const isDepositEmirate = (v) => depositEmirates.map(String).includes(String(v));

        // Create modal: reveal the deposit block only for a deposit emirate.
        const createEmirate = document.getElementById('createEmirate');
        const createBlock   = document.getElementById('createDepositBlock');
        if (createEmirate && createBlock) {
            const sync = () => createBlock.classList.toggle('show', isDepositEmirate(createEmirate.value));
            createEmirate.addEventListener('change', sync);
            sync();
        }

        // Existing-packages table: same rule per package, tied to that row's
        // own emirate dropdown so switching a package to Sharjah reveals the
        // deposit fields inside its expandable pricing section.
        document.querySelectorAll('.pkg-emirate').forEach(function (selector) {
            const block = document.getElementById('pkgDepositBlock-' + selector.dataset.packageId);
            if (!block) return;
            const sync = () => block.classList.toggle('show', isDepositEmirate(selector.value));
            selector.addEventListener('change', sync);
            sync();
        });

        // Auto-expand a package's pricing row after a redirect back here,
        // e.g. ?package=12 after saving that package or its pricing.
        const packageId = new URLSearchParams(window.location.search).get('package');
        if (packageId) {
            const collapseEl = document.getElementById('pricing-row-' + packageId);
            if (collapseEl) {
                new bootstrap.Collapse(collapseEl, { show: true });
                collapseEl.closest('tr')?.previousElementSibling?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        @if($errors->any() && old('name') !== null)
            // A Create Package submission failed validation — reopen the modal
            // so the errors and the manager's input aren't hidden from view.
            new bootstrap.Modal(document.getElementById('createPackageModal')).show();
        @endif
    });
</script>
@endsection
