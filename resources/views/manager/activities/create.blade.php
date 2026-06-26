@extends('layouts.manager')

@section('title', 'Add New Activity')
@section('page-title', 'Activities Management')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Add New Activity</h1>
    <a href="{{ route('manager.activities.index') }}" class="wp-btn wp-btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Activities
    </a>
</div>

<form action="{{ route('manager.activities.store') }}" method="POST" enctype="multipart/form-data" id="activityForm">
    @csrf

    <div class="row">
        <div class="col-lg-8">
            {{-- Basic Info --}}
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-info-circle text-secondary-wp"></i> Basic Information</div>
                <div class="wp-card-body">
                    <div class="wp-form-group">
                        <label class="wp-form-label">Activity Name <span class="required">*</span></label>
                        <input type="text" class="wp-input" name="activityName"
                               value="{{ old('activityName') }}" required placeholder="e.g. Desert Safari Adventure">
                    </div>

                    @include('partials.activity_location_fields')

                    <div class="wp-form-group">
                        <label class="wp-form-label">Activity Category / Type</label>
                        <input type="text" class="wp-input" name="activityCategory"
                               value="{{ old('activityCategory') }}"
                               placeholder="e.g. Adventure, Water Sports, Desert Safari">
                        <p class="wp-form-help">Optional — used for future filtering</p>
                    </div>
                </div>
            </div>

            {{-- Pricing --}}
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-dollar-sign text-secondary-wp"></i> Pricing</div>
                <div class="wp-card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Currency <span class="required">*</span></label>
                                <input type="text" class="wp-input" name="activityCurrency" id="activityCurrency"
                                       value="{{ old('activityCurrency', 'AED') }}" required maxlength="3"
                                       placeholder="AED" style="text-transform:uppercase"
                                       oninput="this.dataset.manuallyChanged='1';this.value=this.value.toUpperCase()">
                                <p class="wp-form-help">ISO code (auto-fills)</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Adult Price <span class="required">*</span></label>
                                <input type="number" class="wp-input" name="activityPrice"
                                       step="0.01" min="0" value="{{ old('activityPrice') }}" required placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Child Price</label>
                                <input type="number" class="wp-input" name="activityChildPrice"
                                       step="0.01" min="0" value="{{ old('activityChildPrice') }}" placeholder="0.00">
                                <p class="wp-form-help">Leave empty for adult price</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Transaction Charges</label>
                                <input type="number" class="wp-input" name="activityTransactionCharges"
                                       step="0.01" min="0" value="{{ old('activityTransactionCharges') }}" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Supplier Information --}}
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-truck text-secondary-wp"></i> Supplier Information</div>
                <div class="wp-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Supplier / Tour Operator Name</label>
                                <input type="text" class="wp-input" name="supplierName"
                                       value="{{ old('supplierName') }}" placeholder="e.g. Desert Safari Tours LLC">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Supplier Email</label>
                                <input type="email" class="wp-input" name="supplierEmail"
                                       value="{{ old('supplierEmail') }}" placeholder="supplier@example.com">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="wp-form-group">
                                <label class="wp-form-label"><i class="fas fa-bell"></i> Booking Notification Emails</label>
                                <textarea class="wp-input @error('notification_emails') is-invalid @enderror" name="notification_emails"
                                          rows="2" placeholder="ops@yourbusiness.com, owner@yourbusiness.com">{{ old('notification_emails') }}</textarea>
                                <small class="text-muted">Optional — comma-separated. Notified when this activity is booked. Leave blank to use your company email.</small>
                                @error('notification_emails')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Images --}}
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-images text-secondary-wp"></i> Activity Images <span class="required">*</span></div>
                <div class="wp-card-body">
                    <div id="imageDropZone" style="border: 2px dashed var(--wp-border); border-radius: 6px; padding: 40px 20px; text-align: center; cursor: pointer; transition: all 0.2s; background: #1a1a1a;">
                        <div id="imageDropContent">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 36px; color: var(--wp-border); margin-bottom: 12px; display: block;"></i>
                            <p style="margin: 0 0 8px; font-size: 14px; font-weight: 500; color: var(--wp-text);">Drop images here or click to upload</p>
                            <p style="margin: 0; font-size: 12px; color: var(--wp-text-muted);">JPEG, PNG, GIF, WebP — Max 5MB each — Multiple files allowed</p>
                        </div>
                    </div>
                    <input type="file" id="activityImageFiles" name="activityImageFiles[]" multiple accept="image/*" style="display: none;">
                    <p id="imageError" style="display:none; margin: 10px 0 0; font-size: 13px; color: var(--wp-danger, #e53e3e);"><i class="fas fa-exclamation-circle"></i> Please upload at least one image.</p>
                    <div id="imagePreviewGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; margin-top: 12px;"></div>
                </div>
            </div>

            {{-- Overview --}}
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-align-left text-secondary-wp"></i> Overview Description <span class="required">*</span></div>
                <div class="wp-card-body">
                    <div class="wp-form-group" style="margin-bottom: 0;">
                        <textarea class="wp-textarea" name="detailsOverview" rows="6" required
                                  placeholder="Write a detailed description of this activity...">{{ old('detailsOverview') }}</textarea>
                        <p class="wp-form-help">HTML is supported for rich formatting.</p>
                    </div>
                </div>
            </div>

            {{-- Important Information --}}
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-exclamation-circle text-secondary-wp"></i> Important Information <span class="required">*</span></div>
                <div class="wp-card-body">
                    <div id="iminfoContainer">
                        <div class="iminfo-row" style="display: flex; gap: 8px; margin-bottom: 8px;">
                            <input type="text" class="wp-input" name="detailsIminfo[]" required
                                   placeholder="e.g. Pickup available from all hotels" style="flex: 1;">
                            <button type="button" class="wp-btn wp-btn-danger wp-btn-sm" style="flex-shrink: 0;" onclick="removeRow(this)">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <button type="button" class="wp-btn wp-btn-secondary wp-btn-sm" onclick="addIminfoRow()">
                        <i class="fas fa-plus"></i> Add More Info
                    </button>
                </div>
            </div>

            {{-- Highlights --}}
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-star text-secondary-wp"></i> Highlights <span class="required">*</span></div>
                <div class="wp-card-body">
                    <div id="highlightsContainer">
                        <div class="highlight-row" style="display: flex; gap: 8px; margin-bottom: 8px;">
                            <input type="text" class="wp-input" name="detailsHighlights[]" required
                                   placeholder="e.g. Stunning sunset views" style="flex: 1;">
                            <button type="button" class="wp-btn wp-btn-danger wp-btn-sm" style="flex-shrink: 0;" onclick="removeRow(this)">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <button type="button" class="wp-btn wp-btn-secondary wp-btn-sm" onclick="addHighlightRow()">
                        <i class="fas fa-plus"></i> Add More Highlights
                    </button>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="wp-card" style="position: sticky; top: 68px;">
                <div class="wp-card-header"><i class="fas fa-paper-plane text-secondary-wp"></i> Publish</div>
                <div class="wp-card-body" style="font-size: 13px; color: var(--wp-text-secondary);">
                    <p style="margin-bottom: 8px;"><i class="fas fa-eye" style="width: 16px; color: var(--wp-text-muted);"></i> Status: <strong>Active</strong></p>
                    <p style="margin-bottom: 0;"><i class="fas fa-calendar" style="width: 16px; color: var(--wp-text-muted);"></i> Appears on Activities page immediately</p>
                </div>
                <div class="wp-card-footer">
                    <button type="submit" class="wp-btn wp-btn-primary" style="width: 100%; justify-content: center;">
                        <i class="fas fa-check"></i> Create Activity
                    </button>
                </div>
            </div>

            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-lightbulb text-secondary-wp"></i> Tips</div>
                <div class="wp-card-body" style="font-size: 12px; color: var(--wp-text-secondary); line-height: 1.7;">
                    <p><strong>Country</strong> — Choose the country where this activity takes place. UAE activities require an Emirate; all others just need a city/location.</p>
                    <p><strong>Currency</strong> — Auto-fills based on country, but you can override it.</p>
                    <p><strong>Images</strong> — Upload multiple images. The first becomes the main thumbnail.</p>
                    <p style="margin-bottom: 0;"><strong>Highlights</strong> — List the best features that make this activity special.</p>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
$(function() {
    var dropZone  = $('#imageDropZone');
    var fileInput = $('#activityImageFiles');

    dropZone.click(function() { fileInput.click(); });
    dropZone.on('dragover', function(e) {
        e.preventDefault();
        $(this).css({ borderColor: 'var(--wp-primary)', background: 'rgba(255,215,0,0.08)' });
    }).on('dragleave drop', function(e) {
        e.preventDefault();
        $(this).css({ borderColor: 'var(--wp-border)', background: '#1a1a1a' });
    }).on('drop', function(e) {
        e.preventDefault();
        var files = e.originalEvent.dataTransfer.files;
        if (files.length) { fileInput[0].files = files; fileInput.trigger('change'); }
    });
    fileInput.change(function() {
        var grid = $('#imagePreviewGrid');
        grid.empty();
        Array.from(this.files).forEach(function(file, i) {
            var url = URL.createObjectURL(file);
            var div = $('<div>').css({ position: 'relative', borderRadius: '6px', overflow: 'hidden', border: '1px solid var(--wp-border-light)', background: '#222' });
            var img = $('<img>').attr('src', url).css({ width: '100%', height: '90px', objectFit: 'cover' });
            var label = $('<div>').text(file.name).css({ padding: '4px 6px', fontSize: '10px', color: 'var(--wp-text-muted)', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' });
            if (i === 0) {
                div.append($('<div>').text('Main').css({ position: 'absolute', top: '4px', left: '4px', background: 'var(--wp-primary)', color: '#000', padding: '1px 6px', borderRadius: '3px', fontSize: '10px', fontWeight: '600' }));
            }
            div.append(img, label);
            grid.append(div);
        });
        if (this.files.length > 0) {
            $('#imageDropContent').html('<i class="fas fa-check-circle" style="font-size: 24px; color: var(--wp-success); margin-bottom: 8px; display: block;"></i><p style="margin:0; font-size: 13px; color: var(--wp-text);">' + this.files.length + ' image(s) selected. Click to change.</p>');
            $('#imageError').hide();
        }
    });

    // Enforce the image requirement visibly (the input is hidden, so HTML5 `required`
    // would abort the submit silently — validate on submit instead).
    $('#activityForm').on('submit', function(e) {
        if (!fileInput[0].files || fileInput[0].files.length === 0) {
            e.preventDefault();
            $('#imageError').show();
            $('html, body').animate({ scrollTop: dropZone.offset().top - 100 }, 300);
        }
    });
});

function addIminfoRow() {
    $('#iminfoContainer').append('<div class="iminfo-row" style="display: flex; gap: 8px; margin-bottom: 8px;"><input type="text" class="wp-input" name="detailsIminfo[]" required placeholder="Enter important info..." style="flex: 1;"><button type="button" class="wp-btn wp-btn-danger wp-btn-sm" style="flex-shrink: 0;" onclick="removeRow(this)"><i class="fas fa-times"></i></button></div>');
}
function addHighlightRow() {
    $('#highlightsContainer').append('<div class="highlight-row" style="display: flex; gap: 8px; margin-bottom: 8px;"><input type="text" class="wp-input" name="detailsHighlights[]" required placeholder="Enter a highlight..." style="flex: 1;"><button type="button" class="wp-btn wp-btn-danger wp-btn-sm" style="flex-shrink: 0;" onclick="removeRow(this)"><i class="fas fa-times"></i></button></div>');
}
function removeRow(btn) {
    var container = $(btn).closest('.iminfo-row, .highlight-row').parent();
    if (container.children().length > 1) {
        $(btn).closest('.iminfo-row, .highlight-row').remove();
    }
}
</script>
@endpush
