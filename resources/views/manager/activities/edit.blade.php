@extends('layouts.manager')

@section('title', 'Edit Activity')
@section('page-title', 'Activities Management')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Edit Activity</h1>
    <a href="{{ route('manager.activities.index') }}" class="wp-btn wp-btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Activities
    </a>
</div>

<form action="{{ route('manager.activities.update', $activity->activityID) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-lg-8">
            {{-- Basic Info --}}
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-info-circle text-secondary-wp"></i> Basic Information</div>
                <div class="wp-card-body">
                    <div class="wp-form-group">
                        <label class="wp-form-label">Activity Name <span class="required">*</span></label>
                        <input type="text" class="wp-input" name="activityName" value="{{ old('activityName', $activity->activityName) }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Emirate <span class="required">*</span></label>
                                <select class="wp-select" name="emiratesID" required>
                                    <option value="">Select Emirate</option>
                                    @foreach($emirates as $emirate)
                                        <option value="{{ $emirate->emiratesID }}"
                                            {{ old('emiratesID', $activity->emiratesID) == $emirate->emiratesID ? 'selected' : '' }}>
                                            {{ $emirate->emiratesName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Location <span class="required">*</span></label>
                                <input type="text" class="wp-input" name="activityLocation" value="{{ old('activityLocation', $activity->activityLocation) }}" required>
                            </div>
                        </div>
                    <div class="wp-form-group">
                        <label class="wp-form-label">Activity Category / Type</label>
                        <input type="text" class="wp-input" name="activityCategory" value="{{ old('activityCategory', $activity->activityCategory) }}" placeholder="e.g. Adventure, Water Sports, Desert Safari">
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
                                <input type="number" class="wp-input" name="activityPrice" step="0.01" min="0" value="{{ old('activityPrice', $activity->activityPrice) }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Child Price ($)</label>
                                <input type="number" class="wp-input" name="activityChildPrice" step="0.01" min="0" value="{{ old('activityChildPrice', $activity->activityChildPrice) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Transaction Charges ($)</label>
                                <input type="number" class="wp-input" name="activityTransactionCharges" step="0.01" min="0" value="{{ old('activityTransactionCharges', $activity->activityTransactionCharges) }}">
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
                                <input type="text" class="wp-input" name="supplierName" value="{{ old('supplierName', $activity->supplierName) }}" placeholder="e.g. Desert Safari Tours LLC">
                                <p class="wp-form-help">Optional — supplier will receive booking notifications</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Supplier Email</label>
                                <input type="email" class="wp-input" name="supplierEmail" value="{{ old('supplierEmail', $activity->supplierEmail) }}" placeholder="supplier@example.com">
                                <p class="wp-form-help">Optional — booking confirmations will be sent here</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Current Images --}}
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-images text-secondary-wp"></i> Activity Images</div>
                <div class="wp-card-body">
                    @if(count($existingImages) > 0)
                        <p style="font-size: 12px; color: var(--wp-text-muted); margin-bottom: 10px;">
                            <i class="fas fa-info-circle"></i> Current images ({{ count($existingImages) }}):
                        </p>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; margin-bottom: 16px;">
                            @foreach($existingImages as $index => $imagePath)
                                <div style="position: relative; border-radius: 6px; overflow: hidden; border: 1px solid var(--wp-border-light); background: #222;">
                                    <img src="{{ asset($imagePath) }}" alt="Image {{ $index + 1 }}"
                                         style="width: 100%; height: 90px; object-fit: cover;"
                                         onerror="this.src='data:image/svg+xml,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; viewBox=&quot;0 0 100 100&quot;><rect fill=&quot;%23333&quot; width=&quot;100&quot; height=&quot;100&quot;/><text x=&quot;50&quot; y=&quot;55&quot; text-anchor=&quot;middle&quot; fill=&quot;%23666&quot; font-size=&quot;12&quot;>No Image</text></svg>'">
                                    @if($index === 0)
                                        <div style="position: absolute; top: 4px; left: 4px; background: var(--wp-primary); color: #000; padding: 1px 6px; border-radius: 3px; font-size: 10px; font-weight: 600;">Main</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div style="border-top: 1px solid var(--wp-border-light); padding-top: 16px;">
                        <p style="font-size: 13px; font-weight: 500; color: var(--wp-text); margin-bottom: 8px;">Upload new images</p>

                        <div id="imageDropZone" style="border: 2px dashed var(--wp-border); border-radius: 6px; padding: 30px 20px; text-align: center; cursor: pointer; transition: all 0.2s; background: #1a1a1a;">
                            <div id="imageDropContent">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 28px; color: var(--wp-border); margin-bottom: 8px; display: block;"></i>
                                <p style="margin: 0; font-size: 13px; color: var(--wp-text-muted);">Click to select new images (optional)</p>
                            </div>
                        </div>
                        <input type="file" id="activityImageFiles" name="activityImageFiles[]" multiple accept="image/*" style="display: none;">
                        <div id="imagePreviewGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; margin-top: 12px;"></div>

                        <div style="margin-top: 10px;">
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--wp-text); cursor: pointer;">
                                <input type="checkbox" name="replace_images" value="1" style="accent-color: var(--wp-primary);">
                                Replace existing images (instead of adding to them)
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Overview --}}
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-align-left text-secondary-wp"></i> Overview Description <span class="required">*</span></div>
                <div class="wp-card-body">
                    <div class="wp-form-group" style="margin-bottom: 0;">
                        <textarea class="wp-textarea" name="detailsOverview" rows="6" required>{{ old('detailsOverview', $details->detailsOverview ?? '') }}</textarea>
                        <p class="wp-form-help">HTML is supported for rich formatting.</p>
                    </div>
                </div>
            </div>

            {{-- Important Information --}}
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-exclamation-circle text-secondary-wp"></i> Important Information <span class="required">*</span></div>
                <div class="wp-card-body">
                    <div id="iminfoContainer">
                        @if(count($detailsIminfo) > 0)
                            @foreach($detailsIminfo as $info)
                                <div class="iminfo-row" style="display: flex; gap: 8px; margin-bottom: 8px;">
                                    <input type="text" class="wp-input" name="detailsIminfo[]" value="{{ $info }}" required style="flex: 1;">
                                    <button type="button" class="wp-btn wp-btn-danger wp-btn-sm remove-row" style="flex-shrink: 0;" onclick="removeRow(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="iminfo-row" style="display: flex; gap: 8px; margin-bottom: 8px;">
                                <input type="text" class="wp-input" name="detailsIminfo[]" required placeholder="Enter important info..." style="flex: 1;">
                                <button type="button" class="wp-btn wp-btn-danger wp-btn-sm remove-row" style="flex-shrink: 0;" onclick="removeRow(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endif
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
                        @if(count($detailsHighlights) > 0)
                            @foreach($detailsHighlights as $highlight)
                                <div class="highlight-row" style="display: flex; gap: 8px; margin-bottom: 8px;">
                                    <input type="text" class="wp-input" name="detailsHighlights[]" value="{{ $highlight }}" required style="flex: 1;">
                                    <button type="button" class="wp-btn wp-btn-danger wp-btn-sm remove-row" style="flex-shrink: 0;" onclick="removeRow(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="highlight-row" style="display: flex; gap: 8px; margin-bottom: 8px;">
                                <input type="text" class="wp-input" name="detailsHighlights[]" required placeholder="Enter a highlight..." style="flex: 1;">
                                <button type="button" class="wp-btn wp-btn-danger wp-btn-sm remove-row" style="flex-shrink: 0;" onclick="removeRow(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                    <button type="button" class="wp-btn wp-btn-secondary wp-btn-sm" onclick="addHighlightRow()">
                        <i class="fas fa-plus"></i> Add More Highlights
                    </button>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Update Box --}}
            <div class="wp-card" style="position: sticky; top: 68px;">
                <div class="wp-card-header"><i class="fas fa-save text-secondary-wp"></i> Update</div>
                <div class="wp-card-body" style="font-size: 13px; color: var(--wp-text-secondary);">
                    <p style="margin-bottom: 8px;"><i class="fas fa-user" style="width: 16px; color: var(--wp-text-muted);"></i> Created by: <strong>{{ $activity->createdBy ?? 'Unknown' }}</strong></p>
                    @if($activity->createdDate)
                        <p style="margin-bottom: 8px;"><i class="fas fa-calendar" style="width: 16px; color: var(--wp-text-muted);"></i> Created: {{ \Carbon\Carbon::parse($activity->createdDate)->format('M d, Y') }}</p>
                    @endif
                    @if($activity->modifiedDate)
                        <p style="margin-bottom: 0;"><i class="fas fa-clock" style="width: 16px; color: var(--wp-text-muted);"></i> Modified: {{ \Carbon\Carbon::parse($activity->modifiedDate)->format('M d, Y') }}</p>
                    @endif
                </div>
                <div class="wp-card-footer" style="display: flex; flex-direction: column; gap: 8px;">
                    <button type="submit" class="wp-btn wp-btn-primary" style="width: 100%; justify-content: center;">
                        <i class="fas fa-check"></i> Update Activity
                    </button>
                    <form action="{{ route('manager.activities.destroy', $activity->activityID) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this activity?');" style="width: 100%;">
                        @csrf @method('DELETE')
                        <button type="submit" class="wp-btn wp-btn-danger" style="width: 100%; justify-content: center;">
                            <i class="fas fa-trash-alt"></i> Delete Activity
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
$(function() {
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
                position: 'relative', borderRadius: '6px', overflow: 'hidden',
                border: '1px solid var(--wp-border-light)', background: '#222'
            });
            var img = $('<img>').attr('src', url).css({ width: '100%', height: '90px', objectFit: 'cover' });
            var label = $('<div>').text(file.name).css({
                padding: '4px 6px', fontSize: '10px', color: 'var(--wp-text-muted)',
                whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis'
            });
            div.append(img, label);
            grid.append(div);
        });

        if (this.files.length > 0) {
            $('#imageDropContent').html('<i class="fas fa-check-circle" style="font-size: 24px; color: var(--wp-success); margin-bottom: 8px; display: block;"></i><p style="margin:0; font-size: 13px; color: var(--wp-text);">' + this.files.length + ' new image(s) selected.</p>');
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
