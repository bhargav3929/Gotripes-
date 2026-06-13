@extends('layouts.manager')

@section('title', 'Visa Pricing')
@section('page-title', 'Visa Pricing')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Visa Pricing</h1>
</div>

<div class="row">
    <div class="col-lg-5">
        <div class="wp-card">
            <div class="wp-card-header"><i class="fas fa-plus-circle text-secondary-wp"></i> Add New Price</div>
            <div class="wp-card-body">
                <form action="{{ route('manager.visa-pricing.store') }}" method="POST">
                    @csrf
                    <div class="wp-form-group">
                        <label class="wp-form-label">Visa Type / Duration <span class="required">*</span></label>
                        <input type="text" class="wp-input" name="UAEVisaDuration" value="{{ old('UAEVisaDuration') }}" required maxlength="100" placeholder="e.g. 30 Days Tourist, 60 Days, Multi-entry">
                        <p class="wp-form-help">This label appears on your public visa page.</p>
                    </div>
                    <div class="wp-form-group">
                        <label class="wp-form-label">Price (AED) <span class="required">*</span></label>
                        <input type="number" class="wp-input" name="UAEVPrice" value="{{ old('UAEVPrice') }}" step="0.01" min="0" required placeholder="e.g. 350.00">
                    </div>
                    <button type="submit" class="wp-btn wp-btn-primary" style="width:100%;">
                        <i class="fas fa-plus"></i> Add Visa Price
                    </button>
                </form>
            </div>
        </div>

        <div class="wp-card" style="margin-top:16px;">
            <div class="wp-card-header"><i class="fas fa-concierge-bell text-secondary-wp"></i> Add-On Service Fees</div>
            <div class="wp-card-body">
                <form action="{{ route('manager.visa-pricing.service-fees.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="wp-form-group">
                        <label class="wp-form-label">Hotel Booking Service Fee (AED)</label>
                        <input type="number" class="wp-input" name="visa_hotel_booking_fee" value="{{ old('visa_hotel_booking_fee', $hotelFee) }}" step="0.01" min="0" required placeholder="e.g. 25.00">
                        <p class="wp-form-help">Charged when a customer selects hotel booking assistance.</p>
                    </div>
                    <div class="wp-form-group">
                        <label class="wp-form-label">Ticket Booking Service Fee (AED)</label>
                        <input type="number" class="wp-input" name="visa_ticket_booking_fee" value="{{ old('visa_ticket_booking_fee', $ticketFee) }}" step="0.01" min="0" required placeholder="e.g. 25.00">
                        <p class="wp-form-help">Charged when a customer selects ticket booking assistance.</p>
                    </div>
                    <button type="submit" class="wp-btn wp-btn-primary" style="width:100%;">
                        <i class="fas fa-save"></i> Save Service Fees
                    </button>
                </form>
            </div>
        </div>

        <div class="wp-card" style="margin-top:16px;">
            <div class="wp-card-header"><i class="fas fa-globe text-secondary-wp"></i> e-Visa Markup (80+ Countries)</div>
            <div class="wp-card-body">
                <form action="{{ route('manager.visa-pricing.evisa-markup.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="wp-form-group">
                        <label class="wp-form-label">Profit Margin (%)</label>
                        <input type="number" class="wp-input" name="markup_percent" value="{{ old('markup_percent', $evisaMarkup ?? 15) }}" step="0.01" min="0" max="1000" required placeholder="e.g. 15">
                        <p class="wp-form-help">Added on top of the supplier's net fee for every e-Visa on the <code>/e-visa</code> storefront. Example: a $100 net visa at 15% sells for $115.</p>
                    </div>
                    <button type="submit" class="wp-btn wp-btn-primary" style="width:100%;">
                        <i class="fas fa-save"></i> Save e-Visa Markup
                    </button>
                </form>
            </div>
        </div>

        <div class="wp-card" style="margin-top:16px;">
            <div class="wp-card-body" style="font-size:13px; color: var(--wp-text-secondary);">
                <i class="fas fa-info-circle" style="color: var(--wp-primary); margin-right:6px;"></i>
                These prices appear on your public visa page at <code>/uaevisa</code>.
                Customers picking a duration see the matching price at checkout.
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        @foreach($visas as $visa)
            <form id="visa-update-{{ $visa->vID }}" action="{{ route('manager.visa-pricing.update', $visa->vID) }}" method="POST" style="display:none;">
                @csrf @method('PUT')
            </form>
            <form id="visa-delete-{{ $visa->vID }}" action="{{ route('manager.visa-pricing.destroy', $visa->vID) }}" method="POST" onsubmit="return confirm('Remove this visa price?');" style="display:none;">
                @csrf @method('DELETE')
            </form>
        @endforeach

        <div class="wp-card">
            <div class="wp-card-header"><i class="fas fa-list text-secondary-wp"></i> Current Visa Prices</div>
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
                                    <input type="text" class="wp-input" name="UAEVisaDuration" value="{{ $visa->UAEVisaDuration }}"
                                           form="visa-update-{{ $visa->vID }}" required>
                                </td>
                                <td>
                                    <input type="number" class="wp-input" name="UAEVPrice" value="{{ $visa->UAEVPrice }}"
                                           form="visa-update-{{ $visa->vID }}" step="0.01" min="0" required>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 6px;">
                                        <button type="submit" form="visa-update-{{ $visa->vID }}" class="wp-btn wp-btn-secondary wp-btn-sm">
                                            <i class="fas fa-save"></i> Save
                                        </button>
                                        <button type="submit" form="visa-delete-{{ $visa->vID }}" class="wp-btn wp-btn-danger wp-btn-sm">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="4">
                                    <div style="padding: 24px 0; text-align: center;">
                                        <i class="fas fa-passport" style="font-size: 32px; color: var(--wp-border); margin-bottom: 8px; display: block;"></i>
                                        No visa prices yet. Use the form on the left to add one.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
