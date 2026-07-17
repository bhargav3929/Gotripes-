@extends('layouts.manager')

@section('title', 'Visa Services')
@section('page-title', 'Visa Services & Pricing')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Visa Services & Pricing</h1>
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
</style>

<ul class="nav nav-tabs wp-tabs mb-4" id="visaTabs" role="tablist" style="border-bottom: 2px solid var(--wp-border); padding: 0;">
    <li class="nav-item" role="presentation">
        <button class="wp-tab-btn active" id="packages-tab" data-bs-toggle="tab" data-bs-target="#packages" type="button" role="tab" aria-controls="packages" aria-selected="true">
            <i class="fas fa-box-open me-1"></i> Visa Packages
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="wp-tab-btn" id="pricing-tab" data-bs-toggle="tab" data-bs-target="#pricing" type="button" role="tab" aria-controls="pricing" aria-selected="false">
            <i class="fas fa-th me-1"></i> Pricing Matrix Grid
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="wp-tab-btn" id="emirates-tab" data-bs-toggle="tab" data-bs-target="#emirates" type="button" role="tab" aria-controls="emirates" aria-selected="false">
            <i class="fas fa-map-marked-alt me-1"></i> Emirates
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="wp-tab-btn" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab" aria-controls="settings" aria-selected="false">
            <i class="fas fa-cog me-1"></i> Add-On & Settings
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="wp-tab-btn" id="legacy-tab" data-bs-toggle="tab" data-bs-target="#legacy" type="button" role="tab" aria-controls="legacy" aria-selected="false">
            <i class="fas fa-history me-1"></i> Legacy Prices
        </button>
    </li>
</ul>

<div class="tab-content" id="visaTabsContent">
    
    {{-- ==================== TAB 1: VISA PACKAGES ==================== --}}
    <div class="tab-pane fade show active" id="packages" role="tabpanel" aria-labelledby="packages-tab">
        <div class="row">
            <div class="col-lg-4">
                <div class="wp-card">
                    <div class="wp-card-header"><i class="fas fa-plus-circle text-secondary-wp"></i> Add Visa Package</div>
                    <div class="wp-card-body">
                        <form action="{{ route('manager.visa-packages.store') }}" method="POST">
                            @csrf
                            <div class="wp-form-group">
                                <label class="wp-form-label">Emirate <span class="required">*</span></label>
                                <select class="wp-input" name="emirates_id" required>
                                    <option value="">Select Emirate...</option>
                                    @foreach($emirates as $e)
                                        <option value="{{ $e->emiratesID }}">{{ $e->emiratesName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="wp-form-group">
                                <label class="wp-form-label">Package Name <span class="required">*</span></label>
                                <input type="text" class="wp-input" name="name" required placeholder="e.g. Standard Tourist Visa" maxlength="100">
                            </div>
                            <div class="wp-form-group">
                                <label class="wp-form-label">Description</label>
                                <textarea class="wp-input" name="description" placeholder="Short details about processing speed or requirements..." rows="3"></textarea>
                            </div>
                            <button type="submit" class="wp-btn wp-btn-primary w-100">
                                <i class="fas fa-plus"></i> Create Package
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="wp-card">
                    <div class="wp-card-header"><i class="fas fa-box-open text-secondary-wp"></i> Existing Packages</div>
                    <div class="table-responsive">
                        <table class="wp-table">
                            <thead>
                                <tr>
                                    <th>Emirate</th>
                                    <th>Package Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th style="width: 200px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($packages as $p)
                                    <tr>
                                        <td>
                                            <select class="wp-input" name="emirates_id" form="pkg-update-{{ $p->id }}" required>
                                                @foreach($emirates as $e)
                                                    <option value="{{ $e->emiratesID }}" {{ $p->emirates_id == $e->emiratesID ? 'selected' : '' }}>{{ $e->emiratesName }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="wp-input" name="name" value="{{ $p->name }}" form="pkg-update-{{ $p->id }}" required>
                                        </td>
                                        <td>
                                            <textarea class="wp-input" name="description" form="pkg-update-{{ $p->id }}" rows="2" maxlength="1000" placeholder="Short details about processing speed or requirements...">{{ $p->description }}</textarea>
                                        </td>
                                        <td>
                                            <select class="wp-input" name="isActive" form="pkg-update-{{ $p->id }}">
                                                <option value="1" {{ $p->isActive ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ !$p->isActive ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </td>
                                        <td>
                                            <form id="pkg-update-{{ $p->id }}" action="{{ route('manager.visa-packages.update', $p->id) }}" method="POST" style="display:inline;">
                                                @csrf @method('PUT')
                                                <button type="submit" class="wp-btn wp-btn-secondary wp-btn-sm"><i class="fas fa-save"></i></button>
                                            </form>
                                            <form action="{{ route('manager.visa-packages.destroy', $p->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Remove this package and all associated prices?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="wp-btn wp-btn-danger wp-btn-sm"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">No visa packages configured yet. Use the form on the left.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== TAB 2: PRICING GRID ==================== --}}
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
                    <div class="wp-card-header"><i class="fas fa-list text-secondary-wp"></i> Active Price Grid Matrix</div>
                    <form action="{{ route('manager.visa-prices.bulk-update') }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="wp-table">
                                <thead>
                                    <tr>
                                        <th>Visa Package</th>
                                        <th>Entry Type</th>
                                        <th>Duration</th>
                                        <th>Traveller</th>
                                        <th>Price (AED)</th>
                                        <th>Status</th>
                                        <th style="width: 100px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($prices as $pr)
                                        <tr>
                                            <td>
                                                <span style="font-weight:600;">{{ $pr->package->emirate->emiratesName }}</span><br>
                                                <span class="text-secondary-wp" style="font-size:12px;">{{ $pr->package->name }}</span>
                                                @if(!$pr->package->isActive)
                                                    <span class="wp-badge wp-badge-red ms-1" style="font-size: 10px;">Inactive Pkg</span>
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
                                                <input type="number" class="wp-input" name="prices[{{ $pr->id }}][price]" value="{{ $pr->price }}" step="0.01" min="0" required style="width:100px;">
                                            </td>
                                            <td>
                                                <select class="wp-input" name="prices[{{ $pr->id }}][isActive]" style="width:100px;">
                                                    <option value="1" {{ $pr->isActive ? 'selected' : '' }}>Active</option>
                                                    <option value="0" {{ !$pr->isActive ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </td>
                                            <td>
                                                <button type="button" class="wp-btn wp-btn-danger wp-btn-sm" onclick="if(confirm('Remove this price configuration row?')) document.getElementById('delete-pr-{{ $pr->id }}').submit();">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">No pricing grid configuration entries found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($prices->isNotEmpty())
                            <div class="wp-card-footer text-end">
                                <button type="submit" class="wp-btn wp-btn-primary">
                                    <i class="fas fa-save me-1"></i> Save Pricing Matrix
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
        @foreach($prices as $pr)
            <form id="delete-pr-{{ $pr->id }}" action="{{ route('manager.visa-prices.destroy', $pr->id) }}" method="POST" style="display:none;">
                @csrf @method('DELETE')
            </form>
        @endforeach
    </div>

    {{-- ==================== TAB 3: EMIRATES ==================== --}}
    <div class="tab-pane fade" id="emirates" role="tabpanel" aria-labelledby="emirates-tab">
        <div class="row">
            <div class="col-lg-4">
                <div class="wp-card">
                    <div class="wp-card-header"><i class="fas fa-plus-circle text-secondary-wp"></i> Add Custom Emirate</div>
                    <div class="wp-card-body">
                        <form action="{{ route('manager.visa-emirates.store') }}" method="POST">
                            @csrf
                            <div class="wp-form-group">
                                <label class="wp-form-label">Emirate Name <span class="required">*</span></label>
                                <input type="text" class="wp-input" name="emiratesName" required placeholder="e.g. Abu Dhabi" maxlength="100">
                            </div>
                            <div class="wp-form-group">
                                <label class="wp-form-label">Description</label>
                                <textarea class="wp-input" name="emiratesDescription" placeholder="Description of the emirate..." rows="3"></textarea>
                            </div>
                            <div class="wp-form-group">
                                <label class="wp-form-label">Flag Image Asset Path</label>
                                <input type="text" class="wp-input" name="emiratesImage" placeholder="e.g. assets/emirates/abudhabi.png">
                            </div>
                            <button type="submit" class="wp-btn wp-btn-primary w-100">
                                <i class="fas fa-plus"></i> Create Emirate
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="wp-card">
                    <div class="wp-card-header"><i class="fas fa-map-marked-alt text-secondary-wp"></i> Registered Emirates</div>
                    <div class="table-responsive">
                        <table class="wp-table">
                            <thead>
                                <tr>
                                    <th>Flag</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th style="width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($emirates as $em)
                                    <tr>
                                        <td>
                                            @if($em->emiratesImage)
                                                <img src="{{ asset($em->emiratesImage) }}" alt="{{ $em->emiratesName }}" style="width:40px; height:auto; border-radius:3px; border:1px solid #333;">
                                            @else
                                                <i class="fas fa-flag fa-2x text-muted"></i>
                                            @endif
                                            <input type="hidden" name="emiratesImage" value="{{ $em->emiratesImage }}" form="em-update-{{ $em->emiratesID }}">
                                        </td>
                                        <td>
                                            <input type="text" class="wp-input" name="emiratesName" value="{{ $em->emiratesName }}" form="em-update-{{ $em->emiratesID }}" required>
                                        </td>
                                        <td>
                                            <textarea class="wp-input" name="emiratesDescription" form="em-update-{{ $em->emiratesID }}" rows="1">{{ $em->emiratesDescription }}</textarea>
                                        </td>
                                        <td>
                                            <select class="wp-input" name="isActive" form="em-update-{{ $em->emiratesID }}">
                                                <option value="1" {{ $em->isActive ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ !$em->isActive ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </td>
                                        <td>
                                            <form id="em-update-{{ $em->emiratesID }}" action="{{ route('manager.visa-emirates.update', $em->emiratesID) }}" method="POST" style="display:inline;">
                                                @csrf @method('PUT')
                                                <button type="submit" class="wp-btn wp-btn-secondary wp-btn-sm"><i class="fas fa-save"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== TAB 4: ADD-ON & SETTINGS ==================== --}}
    <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
        <div class="row">
            <div class="col-lg-6">
                <div class="wp-card">
                    <div class="wp-card-header"><i class="fas fa-cog text-secondary-wp"></i> UAE Visa Settings & Service Fees</div>
                    <div class="wp-card-body">
                        <form action="{{ route('manager.visa-pricing.service-fees.update') }}" method="POST">
                            @csrf @method('PUT')
                            <div class="wp-form-group">
                                <label class="wp-form-label">Hotel Booking Service Fee (AED)</label>
                                <input type="number" class="wp-input" name="visa_hotel_booking_fee" value="{{ old('visa_hotel_booking_fee', $hotelFee) }}" step="0.01" min="0" required>
                                <p class="wp-form-help">Charged when a customer selects hotel booking assistance.</p>
                            </div>
                            <div class="wp-form-group">
                                <label class="wp-form-label">Flight Booking Service Fee (AED)</label>
                                <input type="number" class="wp-input" name="visa_ticket_booking_fee" value="{{ old('visa_ticket_booking_fee', $ticketFee) }}" step="0.01" min="0" required>
                                <p class="wp-form-help">Charged when a customer selects ticket booking assistance.</p>
                            </div>
                            <div class="wp-form-group">
                                <label class="wp-form-label">Supplier Email Address</label>
                                <input type="email" class="wp-input" name="visa_supplier_email" value="{{ old('visa_supplier_email', $supplierEmail) }}" placeholder="e.g. supplier@example.com">
                                <p class="wp-form-help">Emails for guest UAE visa applications will be copied to this supplier address.</p>
                            </div>
                            <div class="wp-form-group">
                                <label class="wp-form-label">Sharjah Security Deposit — per applicant (AED)</label>
                                <input type="number" class="wp-input" name="visa_sharjah_deposit" value="{{ old('visa_sharjah_deposit', $sharjahDeposit ?: '') }}" step="0.01" min="0" placeholder="e.g. 5000">
                                <p class="wp-form-help">Refundable deposit shown and charged per applicant for Sharjah visas. Leave blank for no deposit — the storefront then shows a generic message with no amount and charges nothing.</p>
                            </div>
                            <button type="submit" class="wp-btn wp-btn-primary w-100">
                                <i class="fas fa-save"></i> Save UAE Visa Settings
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="wp-card">
                    <div class="wp-card-header"><i class="fas fa-globe text-secondary-wp"></i> Global e-Visa Markup (80+ Countries)</div>
                    <div class="wp-card-body">
                        <form action="{{ route('manager.visa-pricing.evisa-markup.update') }}" method="POST">
                            @csrf @method('PUT')
                            <div class="wp-form-group">
                                <label class="wp-form-label">Profit Margin (%)</label>
                                <input type="number" class="wp-input" name="markup_percent" value="{{ old('markup_percent', $evisaMarkup ?? 15) }}" step="0.01" min="0" max="1000" required>
                                <p class="wp-form-help">Added on top of the supplier's net fee for every e-Visa on the <code>/e-visa</code> storefront. Example: a $100 net visa at 15% sells for $115.</p>
                            </div>
                            <button type="submit" class="wp-btn wp-btn-primary w-100">
                                <i class="fas fa-save"></i> Save e-Visa Markup
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== TAB 5: LEGACY PRICES ==================== --}}
    <div class="tab-pane fade" id="legacy" role="tabpanel" aria-labelledby="legacy-tab">
        <div class="row">
            <div class="col-lg-4">
                <div class="wp-card">
                    <div class="wp-card-header"><i class="fas fa-plus-circle text-secondary-wp"></i> Add Legacy Visa Price</div>
                    <div class="wp-card-body">
                        <form action="{{ route('manager.visa-pricing.store') }}" method="POST">
                            @csrf
                            <div class="wp-form-group">
                                <label class="wp-form-label">Visa Type / Duration <span class="required">*</span></label>
                                <input type="text" class="wp-input" name="UAEVisaDuration" value="{{ old('UAEVisaDuration') }}" required maxlength="100" placeholder="e.g. 30 Days Tourist, 60 Days, Multi-entry">
                                <p class="wp-form-help">This flat structure list is preserved for backward compatibility.</p>
                            </div>
                            <div class="wp-form-group">
                                <label class="wp-form-label">Price (AED) <span class="required">*</span></label>
                                <input type="number" class="wp-input" name="UAEVPrice" value="{{ old('UAEVPrice') }}" step="0.01" min="0" required placeholder="e.g. 350.00">
                            </div>
                            <button type="submit" class="wp-btn wp-btn-primary w-100">
                                <i class="fas fa-plus"></i> Add Visa Price
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8">
                <div class="wp-card">
                    <div class="wp-card-header"><i class="fas fa-list text-secondary-wp"></i> Legacy Visa Prices</div>
                    <div class="table-responsive">
                        <table class="wp-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Visa Type / Duration</th>
                                    <th style="width: 150px;">Price (AED)</th>
                                    <th style="width: 170px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($visas as $i => $visa)
                                    <tr>
                                        <td style="color: var(--wp-text-muted);">{{ $i + 1 }}</td>
                                        <td>
                                            <input type="text" class="wp-input" name="UAEVisaDuration" value="{{ $visa->UAEVisaDuration }}" form="visa-update-{{ $visa->vID }}" required>
                                        </td>
                                        <td>
                                            <input type="number" class="wp-input" name="UAEVPrice" value="{{ $visa->UAEVPrice }}" form="visa-update-{{ $visa->vID }}" step="0.01" min="0" required>
                                        </td>
                                        <td>
                                            <form id="visa-update-{{ $visa->vID }}" action="{{ route('manager.visa-pricing.update', $visa->vID) }}" method="POST" style="display:inline;">
                                                @csrf @method('PUT')
                                                <button type="submit" class="wp-btn wp-btn-secondary wp-btn-sm"><i class="fas fa-save"></i></button>
                                            </form>
                                            <form action="{{ route('manager.visa-pricing.destroy', $visa->vID) }}" method="POST" style="display:inline;" onsubmit="return confirm('Remove this visa price?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="wp-btn wp-btn-danger wp-btn-sm"><i class="fas fa-trash-alt"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">No legacy visa prices configured.</td>
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Activate tab based on URL query parameter ?tab=xxx
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab) {
            const tabBtn = document.getElementById(tab + '-tab');
            if (tabBtn) {
                const bsTab = new bootstrap.Tab(tabBtn);
                bsTab.show();
            }
        }
    });
</script>
@endsection
