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
                        <input type="text" class="wp-input" name="activityName" value="{{ old('activityName') }}" required placeholder="e.g. Desert Safari Adventure">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Emirate <span class="required">*</span></label>
                                <select class="wp-select" name="emiratesID" required>
                                    <option value="">Select Emirate</option>
                                    @foreach($emirates as $emirate)
                                        <option value="{{ $emirate->emiratesID }}" {{ old('emiratesID') == $emirate->emiratesID ? 'selected' : '' }}>
                                            {{ $emirate->emiratesName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Location <span class="required">*</span></label>
                                <input type="text" class="wp-input" name="activityLocation" value="{{ old('activityLocation') }}" required placeholder="e.g. Dubai Marina">
                            </div>
                        </div>
                    <div class="wp-form-group">
                        <label class="wp-form-label">Activity Category / Type</label>
                        <input type="text" class="wp-input" name="activityCategory" value="{{ old('activityCategory') }}" placeholder="e.g. Adventure, Water Sports, Desert Safari">
                        <p class="wp-form-help">Optional — used for future filtering</p>
                    </div>
                </div>
            </div>

            {{-- Pricing --}}
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-dollar-sign text-secondary-wp"></i> Pricing</div>
                <div class="wp-card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Adult Price ($) <span class="required">*</span></label>
                                <input type="number" class="wp-input" name="activityPrice" step="0.01" min="0" value="{{ old('activityPrice') }}" required placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Child Price ($)</label>
                                <input type="number" class="wp-input" name="activityChildPrice" step="0.01" min="0" value="{{ old('activityChildPrice') }}" placeholder="0.00">
                                <p class="wp-form-help">Leave empty to use adult price</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Transaction Charges ($)</label>
                                <input type="number" class="wp-input" name="activityTransactionCharges" step="0.01" min="0" value="{{ old('activityTransactionCharges') }}" placeholder="0.00">
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
                                <input type="text" class="wp-input" name="supplierName" value="{{ old('supplierName') }}" placeholder="e.g. Desert Safari Tours LLC">
                                <p class="wp-form-help">Optional — supplier will receive booking notifications</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Supplier Email</label>
                                <input type="email" class="wp-input" name="supplierEmail" value="{{ old('supplierEmail') }}" placeholder="supplier@example.com">
                                <p class="wp-form-help">Optional — booking confirmations will be sent here</p>
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
                    <input type="file" id="activityImageFiles" name="activityImageFiles[]" multiple accept="image/*" style="display: none;" required>
                    <div id="imagePreviewGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; margin-top: 12px;"></div>
                </div>
            </div>

            {{-- Overview (Rich Text) --}}
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-align-left text-secondary-wp"></i> Overview Description <span class="required">*</span></div>
                <div class="wp-card-body">
                    <div class="wp-form-group" style="margin-bottom: 0;">
                        <textarea class="wp-textarea" name="detailsOverview" rows="6" required placeholder="Write a detailed description of this activity...">{{ old('detailsOverview') }}</textarea>
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
                            <input type="text" class="wp-input" name="detailsIminfo[]" required placeholder="e.g. Pickup available from all hotels" style="flex: 1;">
                            <button type="button" class="wp-btn wp-btn-danger wp-btn-sm remove-row" style="flex-shrink: 0;" onclick="removeRow(this)">
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
                            <input type="text" class="wp-input" name="detailsHighlights[]" required placeholder="e.g. Stunning sunset views" style="flex: 1;">
                            <button type="button" class="wp-btn wp-btn-danger wp-btn-sm remove-row" style="flex-shrink: 0;" onclick="removeRow(this)">
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
            {{-- Publish Box --}}
            <div class="wp-card" style="position: sticky; top: 68px;">
                <div class="wp-card-header"><i class="fas fa-paper-plane text-secondary-wp"></i> Publish</div>
                <div class="wp-card-body" style="font-size: 13px; color: var(--wp-text-secondary);">
                    <p style="margin-bottom: 8px;"><i class="fas fa-eye" style="width: 16px; color: var(--wp-text-muted);"></i> Status: <strong>Active</strong></p>
                    <p style="margin-bottom: 0;"><i class="fas fa-calendar" style="width: 16px; color: var(--wp-text-muted);"></i> Will appear on Activities page immediately</p>
                </div>
                <div class="wp-card-footer">
                    <button type="submit" class="wp-btn wp-btn-primary" style="width: 100%; justify-content: center;">
                        <i class="fas fa-check"></i> Create Activity
                    </button>
                </div>
            </div>

            {{-- Tips --}}
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-lightbulb text-secondary-wp"></i> Tips</div>
                <div class="wp-card-body" style="font-size: 12px; color: var(--wp-text-secondary); line-height: 1.7;">
                    <p><strong>Images</strong> — Upload multiple high-quality images. The first image becomes the main thumbnail on the Activities page.</p>
                    <p><strong>Overview</strong> — Describe what the activity includes, duration, and what to expect.</p>
                    <p><strong>Important Info</strong> — Add practical details like pickup times, what to bring, cancellation policy, etc.</p>
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
    // Image upload handling
    var dropZone = $('#imageDropZone');
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
        if (files.length) {
            fileInput[0].files = files;
            fileInput.trigger('change');
        }
    });

    fileInput.change(function() {
        var grid = $('#imagePreviewGrid');
        grid.empty();

        Array.from(this.files).forEach(function(file, i) {
            var url = URL.createObjectURL(file);
            var div = $('<div>').css({
                position: 'relative',
                borderRadius: '6px',
                overflow: 'hidden',
                border: '1px solid var(--wp-border-light)',
                background: '#222'
            });
            var img = $('<img>').attr('src', url).css({ width: '100%', height: '90px', objectFit: 'cover' });
            var label = $('<div>').text(file.name).css({
                padding: '4px 6px', fontSize: '10px', color: 'var(--wp-text-muted)',
                whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis'
            });
            if (i === 0) {
                var badge = $('<div>').text('Main').css({
                    position: 'absolute', top: '4px', left: '4px',
                    background: 'var(--wp-primary)', color: '#000',
                    padding: '1px 6px', borderRadius: '3px', fontSize: '10px', fontWeight: '600'
                });
                div.append(badge);
            }
            div.append(img, label);
            grid.append(div);
        });

        if (this.files.length > 0) {
            $('#imageDropContent').html('<i class="fas fa-check-circle" style="font-size: 24px; color: var(--wp-success); margin-bottom: 8px; display: block;"></i><p style="margin:0; font-size: 13px; color: var(--wp-text);">' + this.files.length + ' image(s) selected. Click to change.</p>');
        }
    });
});

function addIminfoRow() {
    var row = '<div class="iminfo-row" style="display: flex; gap: 8px; margin-bottom: 8px;">' +
        '<input type="text" class="wp-input" name="detailsIminfo[]" required placeholder="Enter important info..." style="flex: 1;">' +
        '<button type="button" class="wp-btn wp-btn-danger wp-btn-sm remove-row" style="flex-shrink: 0;" onclick="removeRow(this)"><i class="fas fa-times"></i></button>' +
        '</div>';
    $('#iminfoContainer').append(row);
}

function addHighlightRow() {
    var row = '<div class="highlight-row" style="display: flex; gap: 8px; margin-bottom: 8px;">' +
        '<input type="text" class="wp-input" name="detailsHighlights[]" required placeholder="Enter a highlight..." style="flex: 1;">' +
        '<button type="button" class="wp-btn wp-btn-danger wp-btn-sm remove-row" style="flex-shrink: 0;" onclick="removeRow(this)"><i class="fas fa-times"></i></button>' +
        '</div>';
    $('#highlightsContainer').append(row);
}

function removeRow(btn) {
    var container = $(btn).closest('.iminfo-row, .highlight-row').parent();
    if (container.children().length > 1) {
        $(btn).closest('.iminfo-row, .highlight-row').remove();
    }
}
</script>
@endpush
