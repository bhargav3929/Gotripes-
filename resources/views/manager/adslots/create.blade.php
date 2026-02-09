@extends('layouts.manager')

@section('title', 'Add New Ad Slot')
@section('page-title', 'Hero Ad Slots')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Add New Ad Slot</h1>
    <a href="{{ route('manager.adslots.index') }}" class="wp-btn wp-btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to All Slots
    </a>
</div>

@if($activeCount >= 6)
    <div class="wp-notice wp-notice-error">
        <span><i class="fas fa-exclamation-triangle me-2"></i>Maximum 6 active slots reached. Remove one before adding a new slot.</span>
    </div>
@endif

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('manager.adslots.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Upload Area -->
            <div class="wp-card">
                <div class="wp-card-header">Upload Media</div>
                <div class="wp-card-body">
                    <div id="dropZone" style="border: 2px dashed var(--wp-border); border-radius: 6px; padding: 40px 20px; text-align: center; cursor: pointer; transition: all 0.2s; background: #fafafa;">
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

                    <div class="wp-form-group" style="margin-bottom: 0;">
                        <label for="slotOrder" class="wp-form-label">Slot Position <span class="required">*</span></label>
                        <select class="wp-select" id="slotOrder" name="slotOrder" required style="max-width: 200px;">
                            @for($i = 1; $i <= 6; $i++)
                                <option value="{{ $i }}">Position {{ $i }}</option>
                            @endfor
                        </select>
                        <p class="wp-form-help">Choose where this slot appears in the carousel (1 = first).</p>
                    </div>
                </div>
                <div class="wp-card-footer">
                    <button type="submit" class="wp-btn wp-btn-primary" {{ $activeCount >= 6 ? 'disabled' : '' }}>
                        <i class="fas fa-upload"></i> Publish Slot
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Sidebar Help -->
    <div class="col-lg-4">
        <div class="wp-card">
            <div class="wp-card-header">Tips</div>
            <div class="wp-card-body" style="font-size: 12px; color: var(--wp-text-secondary); line-height: 1.7;">
                <p><strong>Images</strong> look best at 16:9 aspect ratio (e.g., 1920x1080). Use high-quality JPEG or WebP for faster loading.</p>
                <p><strong>Videos</strong> should be short (5-15 seconds), optimized for web, and in MP4 format. They will autoplay muted.</p>
                <p style="margin-bottom: 0;"><strong>Slot position</strong> determines the order in the carousel. Position 1 appears first.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    const dropZone = $('#dropZone');
    const fileInput = $('#media');

    dropZone.click(function() { fileInput.click(); });
    $('#replaceHint').click(function() { fileInput.click(); });

    dropZone.on('dragover', function(e) {
        e.preventDefault();
        $(this).css({ borderColor: 'var(--wp-primary)', background: '#f0f6fc' });
    }).on('dragleave drop', function(e) {
        e.preventDefault();
        $(this).css({ borderColor: 'var(--wp-border)', background: '#fafafa' });
    }).on('drop', function(e) {
        e.preventDefault();
        const files = e.originalEvent.dataTransfer.files;
        if (files.length) { fileInput[0].files = files; fileInput.trigger('change'); }
    });

    $('input[name="mediaType"]').change(function() {
        const isVideo = $(this).val() === 'video';
        fileInput.attr('accept', isVideo ? 'video/mp4' : 'image/jpeg,image/png,image/jpg,image/gif,image/webp');
        resetPreview();
    });

    fileInput.change(function() {
        const file = this.files[0];
        if (!file) return;
        const isVideo = $('input[name="mediaType"]:checked').val() === 'video';
        const url = URL.createObjectURL(file);

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
