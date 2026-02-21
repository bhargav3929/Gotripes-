@extends('layouts.manager')

@section('title', 'Edit Media')
@section('page-title', 'Hero Ad TVs')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Edit Media in TV {{ $homepagead->slotOrder }}</h1>
    <a href="{{ route('manager.adslots.index') }}" class="wp-btn wp-btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to All TVs
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('manager.adslots.update', $homepagead->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Current Media -->
            <div class="wp-card">
                <div class="wp-card-header">Current Media</div>
                <div class="wp-card-body" style="text-align: center; background: #1a1a1a;">
                    @if($homepagead->mediaType === 'video')
                        <video controls style="max-width: 100%; max-height: 280px; border-radius: 4px;">
                            <source src="{{ asset($homepagead->imgPath) }}" type="video/mp4">
                        </video>
                    @else
                        <img src="{{ asset($homepagead->imgPath) }}" alt="Current" style="max-width: 100%; max-height: 280px; border-radius: 4px;">
                    @endif
                </div>
            </div>

            <!-- Replace Media -->
            <div class="wp-card">
                <div class="wp-card-header">Replace Media <span style="font-weight: 400; color: var(--wp-text-muted);">(optional)</span></div>
                <div class="wp-card-body">
                    <div id="dropZone" style="border: 2px dashed var(--wp-border); border-radius: 6px; padding: 30px 20px; text-align: center; cursor: pointer; transition: all 0.2s; background: #1a1a1a;">
                        <div id="dropZoneContent">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 28px; color: var(--wp-border); margin-bottom: 8px; display: block;"></i>
                            <p style="margin: 0 0 4px; font-size: 13px; color: var(--wp-text);">Drop file or click to replace</p>
                            <p style="margin: 0; font-size: 12px; color: var(--wp-text-muted);">Leave empty to keep current file</p>
                        </div>
                        <div id="previewArea" style="display: none;">
                            <img id="imagePreview" src="" alt="Preview" style="max-width: 100%; max-height: 200px; border-radius: 4px; display: none;">
                            <video id="videoPreview" controls style="max-width: 100%; max-height: 200px; border-radius: 4px; display: none;">
                                <source id="videoSource" src="" type="video/mp4">
                            </video>
                            <p id="fileName" style="margin: 8px 0 0; font-size: 12px; color: var(--wp-text-muted);"></p>
                        </div>
                    </div>
                    <input type="file" id="media" name="media" style="display: none;">
                </div>
            </div>

            <!-- Settings -->
            <div class="wp-card">
                <div class="wp-card-header">Settings</div>
                <div class="wp-card-body">
                    <div class="wp-form-group">
                        <label class="wp-form-label">TV Slot</label>
                        <select class="wp-select" id="slotOrder" name="slotOrder" required style="max-width: 200px;">
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ $homepagead->slotOrder == $i ? 'selected' : '' }}>TV {{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="wp-form-group">
                        <label class="wp-form-label">Media Type</label>
                        <div style="display: flex; gap: 16px;">
                            <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px;">
                                <input type="radio" name="mediaType" value="image" {{ ($homepagead->mediaType ?? 'image') === 'image' ? 'checked' : '' }} style="accent-color: var(--wp-primary);">
                                <i class="fas fa-image" style="color: var(--wp-text-muted);"></i> Image
                            </label>
                            <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px;">
                                <input type="radio" name="mediaType" value="video" {{ ($homepagead->mediaType ?? 'image') === 'video' ? 'checked' : '' }} style="accent-color: var(--wp-primary);">
                                <i class="fas fa-video" style="color: var(--wp-text-muted);"></i> Video
                            </label>
                        </div>
                    </div>

                    <div class="wp-form-group" id="durationGroup" style="{{ ($homepagead->mediaType ?? 'image') === 'video' ? 'display:none;' : '' }}">
                        <label for="duration" class="wp-form-label">Display Duration</label>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <input type="number" class="wp-input" id="duration" name="duration" value="{{ $homepagead->duration ?? 5 }}" min="1" max="60" style="max-width: 100px;">
                            <span style="font-size: 13px; color: var(--wp-text-muted);">seconds</span>
                        </div>
                    </div>
                </div>
                <div class="wp-card-footer">
                    <button type="submit" class="wp-btn wp-btn-primary">
                        <i class="fas fa-save"></i> Update Media
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="wp-card">
            <div class="wp-card-header">Media Details</div>
            <div class="wp-card-body" style="font-size: 12px; color: var(--wp-text-secondary); line-height: 1.8;">
                <p style="margin-bottom: 6px;"><strong>Type:</strong>
                    @if($homepagead->mediaType === 'video')
                        <span class="wp-badge wp-badge-amber">Video</span>
                    @else
                        <span class="wp-badge wp-badge-green">Image</span>
                    @endif
                </p>
                <p style="margin-bottom: 6px;"><strong>TV Slot:</strong> {{ $homepagead->slotOrder }}</p>
                <p style="margin-bottom: 6px;"><strong>Display Order:</strong> #{{ $homepagead->displayOrder }}</p>
                <p style="margin-bottom: 6px;"><strong>Duration:</strong> {{ $homepagead->duration ?? 5 }}s</p>
                <p style="margin-bottom: 6px;"><strong>Created by:</strong> {{ $homepagead->createdby ?? 'Unknown' }}</p>
                @if($homepagead->modifiedby)
                    <p style="margin-bottom: 0;"><strong>Last modified by:</strong> {{ $homepagead->modifiedby }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    var dropZone = $('#dropZone');
    var fileInput = $('#media');
    var durationGroup = $('#durationGroup');

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

    $('input[name="mediaType"]').change(function() {
        var isVideo = $(this).val() === 'video';
        fileInput.attr('accept', isVideo ? 'video/mp4' : 'image/jpeg,image/png,image/jpg,image/gif,image/webp');
        durationGroup.toggle(!isVideo);
    });

    fileInput.change(function() {
        var file = this.files[0];
        if (!file) return;
        var isVideo = $('input[name="mediaType"]:checked').val() === 'video';
        var url = URL.createObjectURL(file);

        $('#dropZoneContent').hide();
        $('#previewArea').show();

        var sizeMB = (file.size / 1048576).toFixed(1);
        $('#fileName').text(file.name + ' (' + sizeMB + ' MB)');

        if (isVideo) {
            $('#imagePreview').hide();
            $('#videoSource').attr('src', url);
            $('#videoPreview').show()[0].load();
        } else {
            $('#videoPreview').hide();
            $('#imagePreview').attr('src', url).show();
        }
    });
});
</script>
@endpush
