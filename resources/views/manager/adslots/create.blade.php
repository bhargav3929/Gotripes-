@extends('layouts.manager')

@section('title', 'Add Media to TV')
@section('page-title', 'Hero Ad TVs')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Add Media to TV</h1>
    <a href="{{ route('manager.adslots.index') }}" class="wp-btn wp-btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to All TVs
    </a>
</div>

<div class="wp-notice wp-notice-info" style="border-left-color: var(--wp-primary);">
    <span><i class="fas fa-tv me-1"></i> Each TV cycles through its media items like an airport display. Add photos and videos to any TV slot.</span>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('manager.adslots.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Upload Area -->
            <div class="wp-card">
                <div class="wp-card-header">Upload Media</div>
                <div class="wp-card-body">
                    <div id="dropZone" style="border: 2px dashed var(--wp-border); border-radius: 6px; padding: 40px 20px; text-align: center; cursor: pointer; transition: all 0.2s; background: #1a1a1a;">
                        <div id="dropZoneContent">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 36px; color: var(--wp-border); margin-bottom: 12px; display: block;"></i>
                            <p style="margin: 0 0 8px; font-size: 14px; font-weight: 500; color: var(--wp-text);">Drop file here or click to upload</p>
                            <p style="margin: 0; font-size: 12px; color: var(--wp-text-muted);">
                                Images: JPEG, PNG, GIF, WebP (max 5MB) &bull; Videos: MP4 (max 50MB)
                            </p>
                        </div>
                        <div id="previewArea" style="display: none;">
                            <img id="imagePreview" src="" alt="Preview" style="max-width: 100%; max-height: 280px; border-radius: 4px; display: none;">
                            <video id="videoPreview" controls style="max-width: 100%; max-height: 280px; border-radius: 4px; display: none;">
                                <source id="videoSource" src="" type="video/mp4">
                            </video>
                            <p id="fileName" style="margin: 10px 0 0; font-size: 12px; color: var(--wp-text-muted);"></p>
                        </div>
                    </div>
                    <input type="file" id="media" name="media" style="display: none;" required>
                    <p id="replaceHint" style="display: none; margin-top: 8px; font-size: 12px; color: var(--wp-primary); cursor: pointer;">
                        <i class="fas fa-sync-alt me-1"></i>Click to replace file
                    </p>
                </div>
            </div>

            <!-- Settings -->
            <div class="wp-card">
                <div class="wp-card-header">Settings</div>
                <div class="wp-card-body">
                    <div class="wp-form-group">
                        <label class="wp-form-label">TV Slot <span class="required">*</span></label>
                        <select class="wp-select" id="slotOrder" name="slotOrder" required style="max-width: 250px;">
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ request('tv') == $i ? 'selected' : '' }}>
                                    TV {{ $i }}
                                    @if(in_array($i, $usedSlots ?? []))
                                        (has media)
                                    @else
                                        (empty)
                                    @endif
                                </option>
                            @endfor
                        </select>
                        <p class="wp-form-help">Choose which TV window this media will appear in.</p>
                    </div>

                    <div class="wp-form-group">
                        <label class="wp-form-label">Media Type <span class="required">*</span></label>
                        <div style="display: flex; gap: 16px;">
                            <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; color: var(--wp-text);">
                                <input type="radio" name="mediaType" value="image" checked style="accent-color: var(--wp-primary);">
                                <i class="fas fa-image" style="color: var(--wp-text-muted);"></i> Image
                            </label>
                            <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; color: var(--wp-text);">
                                <input type="radio" name="mediaType" value="video" style="accent-color: var(--wp-primary);">
                                <i class="fas fa-video" style="color: var(--wp-text-muted);"></i> Video
                            </label>
                        </div>
                    </div>

                    <div class="wp-form-group" id="durationGroup">
                        <label for="duration" class="wp-form-label">Display Duration</label>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <input type="number" class="wp-input" id="duration" name="duration" value="5" min="1" max="60" style="max-width: 100px;">
                            <span style="font-size: 13px; color: var(--wp-text-muted);">seconds</span>
                        </div>
                        <p class="wp-form-help">How long this image shows before the next one (videos play in full).</p>
                    </div>
                </div>
                <div class="wp-card-footer">
                    <button type="submit" class="wp-btn wp-btn-primary">
                        <i class="fas fa-upload"></i> Add to TV
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="wp-card">
            <div class="wp-card-header">Tips</div>
            <div class="wp-card-body" style="font-size: 12px; color: var(--wp-text-secondary); line-height: 1.7;">
                <p><strong>Images</strong> look best at 16:9 aspect ratio (e.g., 1920x1080). Use high-quality JPEG or WebP for faster loading.</p>
                <p><strong>Videos</strong> should be short (5-15 seconds), optimized for web, and in MP4 format. They will autoplay muted.</p>
                <p style="margin-bottom: 0;"><strong>Multiple media</strong> per TV creates an automatic slideshow. Images rotate based on their duration, videos play in full.</p>
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
    $('#replaceHint').click(function() { fileInput.click(); });

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
        resetPreview();
    });

    fileInput.change(function() {
        var file = this.files[0];
        if (!file) return;
        var isVideo = $('input[name="mediaType"]:checked').val() === 'video';
        var url = URL.createObjectURL(file);

        $('#dropZoneContent').hide();
        $('#previewArea').show();
        $('#replaceHint').show();
        $('#fileName').text(file.name + ' (' + formatSize(file.size) + ')');

        if (isVideo) {
            $('#imagePreview').hide();
            $('#videoSource').attr('src', url);
            $('#videoPreview').show()[0].load();
        } else {
            $('#videoPreview').hide();
            $('#imagePreview').attr('src', url).show();
        }
    });

    function resetPreview() {
        $('#previewArea').hide();
        $('#dropZoneContent').show();
        $('#replaceHint').hide();
        fileInput.val('');
    }

    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }
});
</script>
@endpush
