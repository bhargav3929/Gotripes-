@extends('layouts.manager')

@section('title', 'UAE Visa Services')
@section('page-title', 'UAE Visa Packages & Pricing')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">UAE Visa Packages &amp; Pricing</h1>
</div>

<style>
    .wp-tab-btn {
        background: transparent;
        border: none;
        border-bottom: 3px solid transparent;
        color: var(--wp-text-secondary);
        padding: 12px 20px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .wp-tab-btn:hover {
        color: var(--wp-primary);
    }
    .wp-tab-btn.active {
        color: var(--wp-primary);
        border-bottom-color: var(--wp-primary);
    }
    .tab-content {
        margin-top: 16px;
    }

    /* Deposit block: revealed only for emirates that take a refundable deposit. */
    .deposit-block {
        display: none;
        border: 1px solid var(--wp-border);
        border-left: 3px solid var(--wp-primary);
        border-radius: 4px;
        padding: 14px;
        margin-bottom: 16px;
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
        margin: 20px 0 10px;
        padding-bottom: 6px;
        border-bottom: 1px solid var(--wp-border-light);
    }
    .field-group-label:first-of-type {
        margin-top: 0;
    }

    /* Deposit inputs on an existing package row. Shown only for emirates that
       take a deposit; the JS flips this between 'flex' and 'none'. */
    .pkg-deposit-row {
        display: none;
        gap: 4px;
        margin-bottom: 4px;
    }

    .pkg-meta {
        font-size: 11px;
        color: var(--wp-text-muted);
        line-height: 1.5;
    }
    .pkg-meta strong {
        color: var(--wp-text-secondary);
        font-weight: 600;
    }
</style>

<ul class="nav nav-tabs wp-tabs mb-4" id="visaTabs" role="tablist" style="border-bottom: 2px solid var(--wp-border); padding: 0;">
    <li class="nav-item" role="presentation">
        <button class="wp-tab-btn active" id="packages-tab" data-bs-toggle="tab" data-bs-target="#packages" type="button" role="tab" aria-controls="packages" aria-selected="true">
            <i class="fas fa-box-open me-1"></i> Create Visa Packages
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="wp-tab-btn" id="pricing-tab" data-bs-toggle="tab" data-bs-target="#pricing" type="button" role="tab" aria-controls="pricing" aria-selected="false">
            <i class="fas fa-th me-1"></i> Pricing Matrix
        </button>
    </li>
</ul>

<div class="tab-content" id="visaTabsContent">

    {{-- ==================== TAB 1: CREATE VISA PACKAGES ==================== --}}
    <div class="tab-pane fade show active" id="packages" role="tabpanel" aria-labelledby="packages-tab">
        <div class="row">
            <div class="col-lg-5">
                <div class="wp-card">
                    <div class="wp-card-header"><i class="fas fa-plus-circle text-secondary-wp"></i> Create a Visa Package</div>
                    <div class="wp-card-body">
                        <form action="{{ route('manager.visa-packages.store') }}" method="POST" id="createPackageForm">
                            @csrf

                            <div class="field-group-label">1 &middot; Where</div>

                            <div class="wp-form-group">
                                <label class="wp-form-label">Emirate <span class="required">*</span></label>
                                <select class="wp-input" name="emirates_id" id="createEmirate" required>
                                    <option value="">Select Emirate...</option>
                                    @foreach($emirates as $e)
                                        <option value="{{ $e->emiratesID }}" {{ old('emirates_id') == $e->emiratesID ? 'selected' : '' }}>{{ $e->emiratesName }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Only Sharjah takes a deposit today; the block is driven by a
                                 data attribute so another emirate can be added server-side. --}}
                            <div class="deposit-block" id="createDepositBlock">
                                <div class="deposit-block-title">
                                    <i class="fas fa-shield-halved"></i> Refundable security deposit
                                </div>
                                <div class="wp-form-group">
                                    <label class="wp-form-label">Security Deposit — per applicant (AED)</label>
                                    <input type="number" class="wp-input" name="security_deposit" value="{{ old('security_deposit') }}" step="0.01" min="0" placeholder="e.g. 5000">
                                    <p class="wp-form-help">Charged on top of the visa price and refunded after the visit. Leave blank to use the company-wide default; enter 0 for no deposit.</p>
                                </div>
                                <div class="wp-form-group mb-0">
                                    <label class="wp-form-label">Processing Fee — per applicant (AED)</label>
                                    <input type="number" class="wp-input" name="deposit_admin_fee" value="{{ old('deposit_admin_fee') }}" step="0.01" min="0" placeholder="e.g. 150">
                                    <p class="wp-form-help">Held back when the deposit is returned, so the applicant gets <strong>deposit &minus; fee</strong>. Cannot exceed the deposit.</p>
                                </div>
                            </div>

                            <div class="field-group-label">2 &middot; What</div>

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
                            <div class="wp-form-group">
                                <label class="wp-form-label">Description</label>
                                <textarea class="wp-input" name="description" placeholder="Processing time, requirements, anything the customer should know..." rows="2" maxlength="1000">{{ old('description') }}</textarea>
                            </div>

                            <div class="field-group-label">3 &middot; Who &amp; how long</div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="wp-form-group">
                                        <label class="wp-form-label">Visa For <span class="required">*</span></label>
                                        <select class="wp-input" name="traveller_type" required>
                                            <option value="Adult">Adult</option>
                                            <option value="Child">Child</option>
                                            <option value="Infant">Infant</option>
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
                            <p class="wp-form-help" style="margin-top:-6px;">This is the price shown on the website. Add the other traveller types and durations from the Pricing Matrix tab.</p>

                            <div class="field-group-label">4 &middot; Who gets notified</div>

                            <div class="wp-form-group">
                                <label class="wp-form-label">Supplier Email</label>
                                <input type="text" class="wp-input" name="supplier_email" value="{{ old('supplier_email') }}" placeholder="supplier@example.com, second@example.com">
                                <p class="wp-form-help">Whoever fulfils this visa type. Separate two suppliers with a comma. Leave blank to use the company-wide supplier.</p>
                            </div>
                            <div class="wp-form-group">
                                <label class="wp-form-label">Our Company Email</label>
                                <input type="text" class="wp-input" name="company_email" value="{{ old('company_email') }}" placeholder="visas@gotrips.ai">
                                <p class="wp-form-help">Whoever on our side handles this visa. Change it here when the job moves to someone else — no code change needed.</p>
                            </div>

                            <button type="submit" class="wp-btn wp-btn-primary w-100">
                                <i class="fas fa-plus"></i> Create Package
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="wp-card">
                    <div class="wp-card-header"><i class="fas fa-box-open text-secondary-wp"></i> Existing Packages</div>
                    <div class="table-responsive">
                        <table class="wp-table">
                            <thead>
                                <tr>
                                    <th style="width: 150px;">Emirate</th>
                                    <th>Package</th>
                                    <th style="width: 110px;">Type</th>
                                    <th style="width: 100px;">Status</th>
                                    <th style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($packages as $p)
                                    <tr>
                                        <td>
                                            <select class="wp-input pkg-emirate" name="emirates_id" form="pkg-update-{{ $p->id }}" required>
                                                @foreach($emirates as $e)
                                                    <option value="{{ $e->emiratesID }}" {{ $p->emirates_id == $e->emiratesID ? 'selected' : '' }}>{{ $e->emiratesName }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="wp-input mb-1" name="name" value="{{ $p->name }}" form="pkg-update-{{ $p->id }}" required>
                                            <textarea class="wp-input mb-1" name="description" form="pkg-update-{{ $p->id }}" rows="1" maxlength="1000" placeholder="Description...">{{ $p->description }}</textarea>
                                            <input type="text" class="wp-input mb-1" name="supplier_email" value="{{ $p->supplier_email }}" form="pkg-update-{{ $p->id }}" placeholder="Supplier email(s)">
                                            <input type="text" class="wp-input mb-1" name="company_email" value="{{ $p->company_email }}" form="pkg-update-{{ $p->id }}" placeholder="Our company email">
                                            {{-- No .d-flex here: Bootstrap declares it !important, which
                                                 beats the inline display:none the visibility toggle sets.
                                                 The row lays itself out via .pkg-deposit-row instead. --}}
                                            <div class="pkg-deposit-row" data-emirate="{{ $p->emirates_id }}">
                                                <input type="number" class="wp-input" name="security_deposit" value="{{ $p->security_deposit }}" form="pkg-update-{{ $p->id }}" step="0.01" min="0" placeholder="Deposit (AED)">
                                                <input type="number" class="wp-input" name="deposit_admin_fee" value="{{ $p->deposit_admin_fee }}" form="pkg-update-{{ $p->id }}" step="0.01" min="0" placeholder="Processing fee">
                                            </div>
                                            <div class="pkg-meta mt-1">
                                                <strong>{{ $p->prices->where('isActive', 1)->count() }}</strong> active price row(s)
                                            </div>
                                        </td>
                                        <td>
                                            <select class="wp-input" name="package_type" form="pkg-update-{{ $p->id }}" required>
                                                <option value="Standard" {{ $p->package_type === 'Standard' || !$p->package_type ? 'selected' : '' }}>Standard</option>
                                                <option value="Urgent" {{ $p->package_type === 'Urgent' ? 'selected' : '' }}>Urgent</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="wp-input" name="isActive" form="pkg-update-{{ $p->id }}">
                                                <option value="1" {{ $p->isActive ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ !$p->isActive ? 'selected' : '' }}>Disabled</option>
                                            </select>
                                        </td>
                                        <td>
                                            <form id="pkg-update-{{ $p->id }}" action="{{ route('manager.visa-packages.update', $p->id) }}" method="POST" style="display:inline;">
                                                @csrf @method('PUT')
                                                <button type="submit" class="wp-btn wp-btn-secondary wp-btn-sm" title="Save"><i class="fas fa-save"></i></button>
                                            </form>
                                            <form action="{{ route('manager.visa-packages.destroy', $p->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Remove this package and all associated prices?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="wp-btn wp-btn-danger wp-btn-sm" title="Delete"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">No visa packages yet. Create one with the form on the left.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== TAB 2: PRICING MATRIX ==================== --}}
    <div class="tab-pane fade" id="pricing" role="tabpanel" aria-labelledby="pricing-tab">
        <div class="row">
            <div class="col-lg-4">
                <div class="wp-card">
                    <div class="wp-card-header"><i class="fas fa-plus-circle text-secondary-wp"></i> Add Price Grid Row</div>
                    <div class="wp-card-body">
                        <form action="{{ route('manager.visa-prices.store') }}" method="POST">
                            @csrf
                            <div class="wp-form-group">
                                <label class="wp-form-label">Visa Package <span class="required">*</span></label>
                                <select class="wp-input" name="visa_package_id" required>
                                    <option value="">Select Package...</option>
                                    @foreach($packages->where('isActive', 1) as $p)
                                        <option value="{{ $p->id }}">{{ $p->emirate->emiratesName }} — {{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="wp-form-group">
                                <label class="wp-form-label">Entry Type <span class="required">*</span></label>
                                <select class="wp-input" name="entry_type" required>
                                    <option value="Single Entry">Single Entry</option>
                                    <option value="Multiple Entry">Multiple Entry</option>
                                </select>
                            </div>
                            <div class="wp-form-group">
                                <label class="wp-form-label">Duration <span class="required">*</span></label>
                                <select class="wp-input" name="duration" required>
                                    <option value="30 Days">30 Days</option>
                                    <option value="60 Days">60 Days</option>
                                </select>
                            </div>
                            <div class="wp-form-group">
                                <label class="wp-form-label">Traveller Type <span class="required">*</span></label>
                                <select class="wp-input" name="traveller_type" required>
                                    <option value="Adult">Adult</option>
                                    <option value="Child">Child</option>
                                    <option value="Infant">Infant</option>
                                </select>
                            </div>
                            <div class="wp-form-group">
                                <label class="wp-form-label">Nationality (optional)</label>
                                <input type="text" class="wp-input" name="nationality" placeholder="Leave blank to apply to all nationalities" maxlength="100">
                                <p class="wp-form-help">Only needed for a nationality-specific override. Every package already has a full set of rows with no nationality set (applies to everyone) — edit those in the matrix instead of adding new ones.</p>
                            </div>
                            <div class="wp-form-group">
                                <label class="wp-form-label">Price (AED) <span class="required">*</span></label>
                                <input type="number" class="wp-input" name="price" required step="0.01" min="0" placeholder="e.g. 350.00">
                            </div>
                            <button type="submit" class="wp-btn wp-btn-primary w-100">
                                <i class="fas fa-plus"></i> Add Price Row
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="wp-card">
                    <div class="wp-card-header"><i class="fas fa-list text-secondary-wp"></i> Active Price Grid</div>
                    <form action="{{ route('manager.visa-prices.bulk-update') }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="wp-table">
                                <thead>
                                    <tr>
                                        <th>Package</th>
                                        <th style="width: 130px;">Entry Type</th>
                                        <th style="width: 100px;">Duration</th>
                                        <th style="width: 100px;">Visa For</th>
                                        <th style="width: 110px;">Nationality</th>
                                        <th style="width: 110px;">Price (AED)</th>
                                        <th style="width: 110px;">Status</th>
                                        <th style="width: 60px;">Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($prices as $pr)
                                        <tr>
                                            <td>
                                                <span style="font-weight:600;">{{ $pr->package?->emirate?->emiratesName ?? '—' }}</span><br>
                                                <span class="text-secondary-wp" style="font-size:12px;">{{ $pr->package?->name }}</span>
                                                @if($pr->package && $pr->package->package_type)
                                                    <span class="wp-badge wp-badge-amber ms-1" style="font-size: 10px;">{{ $pr->package->package_type }}</span>
                                                @endif
                                                @if($pr->package && !$pr->package->isActive)
                                                    <span class="wp-badge wp-badge-red ms-1" style="font-size: 10px;">Pkg disabled</span>
                                                @endif
                                            </td>
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
                                                    <option value="Infant" {{ $pr->traveller_type === 'Infant' ? 'selected' : '' }}>Infant</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="wp-input" name="prices[{{ $pr->id }}][nationality]" value="{{ $pr->nationality }}" placeholder="All" maxlength="100" style="width:110px;">
                                            </td>
                                            <td>
                                                <input type="number" class="wp-input" name="prices[{{ $pr->id }}][price]" value="{{ $pr->price }}" step="0.01" min="0" required style="width:100px;">
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
                                            <td colspan="8" class="text-center py-4">No prices yet. Create a package first — its price row appears here.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($prices->isNotEmpty())
                            <div class="wp-card-footer text-end">
                                <button type="submit" class="wp-btn wp-btn-primary">
                                    <i class="fas fa-save me-1"></i> Save All Changes
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="wp-card mb-4">
                    <div class="wp-card-header"><i class="fas fa-plus-circle text-secondary-wp"></i> Add Another Price Row</div>
                    <div class="wp-card-body">
                        <form action="{{ route('manager.visa-prices.store') }}" method="POST">
                            @csrf
                            <div class="wp-form-group">
                                <label class="wp-form-label">Package <span class="required">*</span></label>
                                <select class="wp-input" name="visa_package_id" required>
                                    <option value="">Select Package...</option>
                                    @foreach($packages->where('isActive', 1) as $p)
                                        <option value="{{ $p->id }}">{{ $p->emirate?->emiratesName }} — {{ $p->name }}{{ $p->package_type ? ' (' . $p->package_type . ')' : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="wp-form-group">
                                <label class="wp-form-label">Entry Type <span class="required">*</span></label>
                                <select class="wp-input" name="entry_type" required>
                                    <option value="Single Entry">Single Entry</option>
                                    <option value="Multiple Entry">Multiple Entry</option>
                                </select>
                            </div>
                            <div class="wp-form-group">
                                <label class="wp-form-label">Duration <span class="required">*</span></label>
                                <select class="wp-input" name="duration" required>
                                    <option value="30 Days">30 Days</option>
                                    <option value="60 Days">60 Days</option>
                                </select>
                            </div>
                            <div class="wp-form-group">
                                <label class="wp-form-label">Visa For <span class="required">*</span></label>
                                <select class="wp-input" name="traveller_type" required>
                                    <option value="Adult">Adult</option>
                                    <option value="Child">Child</option>
                                    <option value="Infant">Infant</option>
                                </select>
                            </div>
                            <div class="wp-form-group">
                                <label class="wp-form-label">Price (AED) <span class="required">*</span></label>
                                <input type="number" class="wp-input" name="price" required step="0.01" min="0" placeholder="e.g. 350.00">
                            </div>
                            <button type="submit" class="wp-btn wp-btn-primary w-100">
                                <i class="fas fa-plus"></i> Add Price Row
                            </button>
                        </form>
                    </div>
                </div>

                <div class="wp-card mb-4">
                    <div class="wp-card-header"><i class="fas fa-cart-plus text-secondary-wp"></i> Optional Add-On Fees</div>
                    <div class="wp-card-body">
                        <form action="{{ route('manager.visa-pricing.service-fees.update') }}" method="POST">
                            @csrf @method('PUT')
                            <div class="wp-form-group">
                                <label class="wp-form-label">Hotel Booking Assistance (AED)</label>
                                <input type="number" class="wp-input" name="visa_hotel_booking_fee" value="{{ old('visa_hotel_booking_fee', $hotelFee) }}" step="0.01" min="0" required>
                            </div>
                            <div class="wp-form-group">
                                <label class="wp-form-label">Flight Booking Assistance (AED)</label>
                                <input type="number" class="wp-input" name="visa_ticket_booking_fee" value="{{ old('visa_ticket_booking_fee', $ticketFee) }}" step="0.01" min="0" required>
                            </div>
                            <p class="wp-form-help">Charged only when the customer ticks the matching box at checkout. These two apply to every package.</p>
                            <button type="submit" class="wp-btn wp-btn-secondary w-100">
                                <i class="fas fa-save"></i> Save Add-On Fees
                            </button>
                        </form>
                    </div>
                </div>

                <div class="wp-card">
                    <div class="wp-card-header"><i class="fas fa-globe text-secondary-wp"></i> Global e-Visa Markup</div>
                    <div class="wp-card-body">
                        <form action="{{ route('manager.visa-pricing.evisa-markup.update') }}" method="POST">
                            @csrf @method('PUT')
                            <div class="wp-form-group">
                                <label class="wp-form-label">Profit Margin (%)</label>
                                <input type="number" class="wp-input" name="markup_percent" value="{{ old('markup_percent', $evisaMarkup ?? 15) }}" step="0.01" min="0" max="1000" required>
                                <p class="wp-form-help">Added on top of the supplier's net fee for every e-Visa on the <code>/e-visa</code> storefront. A $100 net visa at 15% sells for $115.</p>
                                <p class="wp-form-help" style="color:var(--wp-warning,#b8860b);">
                                    <i class="fas fa-triangle-exclamation"></i>
                                    <strong>Platform-wide.</strong> Unlike the prices above, this applies to every site on GoTrips, not just yours.
                                </p>
                            </div>
                            <button type="submit" class="wp-btn wp-btn-secondary w-100">
                                <i class="fas fa-save"></i> Save e-Visa Markup
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @foreach($prices as $pr)
            <form id="delete-pr-{{ $pr->id }}" action="{{ route('manager.visa-prices.destroy', $pr->id) }}" method="POST" style="display:none;">
                @csrf @method('DELETE')
            </form>
        @endforeach
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Emirates that take a refundable security deposit, from the server.
        const depositEmirates = @json($depositEmirateIds);
        const isDepositEmirate = (v) => depositEmirates.map(String).includes(String(v));

        // Create form: reveal the deposit block only for a deposit emirate.
        const createEmirate = document.getElementById('createEmirate');
        const createBlock   = document.getElementById('createDepositBlock');
        if (createEmirate && createBlock) {
            const sync = () => createBlock.classList.toggle('show', isDepositEmirate(createEmirate.value));
            createEmirate.addEventListener('change', sync);
            sync();
        }

        // Existing-packages table: same rule per row, and it follows the row's
        // emirate dropdown so switching a package to Sharjah reveals the fields.
        document.querySelectorAll('.pkg-deposit-row').forEach(function (row) {
            const cell     = row.closest('tr');
            const selector = cell ? cell.querySelector('.pkg-emirate') : null;
            const sync = () => {
                const value = selector ? selector.value : row.dataset.emirate;
                row.style.display = isDepositEmirate(value) ? 'flex' : 'none';
            };
            if (selector) selector.addEventListener('change', sync);
            sync();
        });

        // Activate a tab from ?tab=xxx so redirects land where the work happened.
        const tab = new URLSearchParams(window.location.search).get('tab');
        if (tab) {
            const tabBtn = document.getElementById(tab + '-tab');
            if (tabBtn) new bootstrap.Tab(tabBtn).show();
        }
    });
</script>
@endsection
