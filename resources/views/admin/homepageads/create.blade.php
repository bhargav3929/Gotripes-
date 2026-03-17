@extends('layouts.admin')

@section('title', 'Add Media to TV')

@section('page-title', 'Add Media to TV')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card border-0 animate-fade-in">
            <div class="card-header">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                    <h3 class="card-title">
                        <i class="fas fa-plus-circle me-2"></i>Add Media to TV Slot
                    </h3>
                    <a href="{{ route('admin.homepageads.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to TVs
                    </a>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.homepageads.store') }}" enctype="multipart/form-data" id="mediaForm">
                @csrf
                <div class="card-body p-3 p-md-4">

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="alert-hint mb-4">
                        <i class="fas fa-tv me-2"></i>
                        Each TV slot (1-5) is a dedicated display window on the homepage. Add multiple images/videos to each TV -- they cycle automatically.
                    </div>

                    {{-- TV Slot Selection --}}
                    <div class="mb-4">
                        <h6 class="section-label">TV Slot</h6>
                        <label for="slotOrder" class="form-label">Select TV Window <span class="text-required">*</span></label>
                        <select class="form-control" id="slotOrder" name="slotOrder" required>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}"
                                    {{ (request('tv') == $i || old('slotOrder') == $i) ? 'selected' : '' }}>
                                    TV {{ $i }}
                                    @if(in_array($i, $usedSlots ?? []))
                                        (has media)
                                    @else
                                        (empty)
                                    @endif
                                </option>
                            @endfor
                        </select>
                        <div class="form-text">Choose which TV window this media will appear in</div>
                    </div>

                    <hr class="section-divider">

                    {{-- Media Type --}}
                    <div class="mb-4">
                        <h6 class="section-label">Media Type</h6>
                        <div class="d-flex gap-3">
                            <label class="media-type-option">
                                <input type="radio" name="mediaType" id="typeImage" value="image" checked class="d-none">
                                <div class="media-type-inner">
                                    <i class="fas fa-image"></i>
                                    <span>Image</span>
                                </div>
                            </label>
                            <label class="media-type-option">
                                <input type="radio" name="mediaType" id="typeVideo" value="video" class="d-none">
                                <div class="media-type-inner">
                                    <i class="fas fa-video"></i>
                                    <span>Video</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Duration (images only) --}}
                    <div class="mb-4" id="durationGroup">
                        <label for="duration" class="form-label">Display Duration</label>
                        <div class="input-group" style="max-width: 200px;">
                            <input type="number" class="form-control" id="duration" name="duration"
                                   value="{{ old('duration', 5) }}" min="1" max="60">
                            <span class="input-group-text">seconds</span>
                        </div>
                        <div class="form-text">How long this image shows before the next one (videos play in full)</div>
                    </div>

                    <hr class="section-divider">

                    {{-- File Upload --}}
                    <div class="mb-4">
                        <h6 class="section-label">Upload</h6>
                        <label for="media" class="form-label" id="mediaLabel">Upload Image <span class="text-required">*</span></label>
                        <input type="file"
                               class="form-control @error('media') is-invalid @enderror"
                               id="media"
                               name="media"
                               accept="image/*"
                               required>
                        @error('media')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text" id="mediaHint">Accepted: JPEG, PNG, JPG, GIF, WEBP (max 5MB)</div>
                    </div>

                    {{-- Preview --}}
                    <div id="mediaPreview" class="text-center mt-4" style="display: none;">
                        <div class="preview-box">
                            <div class="form-text mb-2">Preview</div>
                            <img id="previewImg" src="#" alt="Preview" class="img-fluid rounded" style="max-height: 280px;">
                            <video id="previewVideo" controls class="img-fluid rounded" style="max-height: 280px; display: none;"></video>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="card-footer bg-transparent" style="border-top: 1px solid var(--border-color);">
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-end">
                        <a href="{{ route('admin.homepageads.index') }}" class="btn btn-outline-primary order-2 order-sm-1">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary order-1 order-sm-2" id="submitBtn">
                            <i class="fas fa-upload me-2"></i>Add to TV
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .alert-hint {
        background: rgba(255, 215, 0, 0.04);
        border: 1px solid var(--border-gold);
        border-radius: var(--radius-sm);
        padding: 0.75rem 1rem;
        color: var(--primary-gold);
        font-size: 0.8125rem;
    }

    .media-type-option { cursor: pointer; margin: 0; }
    .media-type-option input { position: absolute; opacity: 0; pointer-events: none; }
    .media-type-inner {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 0.625rem 1.25rem;
        border: 2px solid var(--border-color);
        border-radius: var(--radius-sm);
        color: var(--text-muted);
        transition: all 0.15s;
        font-weight: 600;
        font-size: 0.8125rem;
    }
    .media-type-option input:checked + .media-type-inner {
        border-color: var(--primary-gold);
        background: rgba(255, 215, 0, 0.08);
        color: var(--primary-gold);
    }
    .media-type-inner:hover {
        border-color: rgba(255, 215, 0, 0.3);
        color: var(--text-main);
    }

    .preview-box {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 1rem;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var mediaInput = document.getElementById('media');
    var mediaLabel = document.getElementById('mediaLabel');
    var mediaHint = document.getElementById('mediaHint');
    var previewImg = document.getElementById('previewImg');
    var previewVideo = document.getElementById('previewVideo');
    var mediaPreview = document.getElementById('mediaPreview');
    var typeImage = document.getElementById('typeImage');
    var typeVideo = document.getElementById('typeVideo');
    var durationGroup = document.getElementById('durationGroup');

    function updateMediaType() {
        var isVideo = typeVideo.checked;
        if (isVideo) {
            mediaLabel.innerHTML = 'Upload Video <span class="text-required">*</span>';
            mediaInput.accept = 'video/mp4';
            mediaHint.textContent = 'Accepted: MP4 (max 50MB)';
            durationGroup.style.display = 'none';
        } else {
            mediaLabel.innerHTML = 'Upload Image <span class="text-required">*</span>';
            mediaInput.accept = 'image/*';
            mediaHint.textContent = 'Accepted: JPEG, PNG, JPG, GIF, WEBP (max 5MB)';
            durationGroup.style.display = 'block';
        }
        mediaInput.value = '';
        mediaPreview.style.display = 'none';
    }

    typeImage.addEventListener('change', updateMediaType);
    typeVideo.addEventListener('change', updateMediaType);

    mediaInput.addEventListener('change', function() {
        var file = this.files[0];
        if (!file) { mediaPreview.style.display = 'none'; return; }

        var isVideo = typeVideo.checked;
        if (isVideo) {
            previewImg.style.display = 'none';
            previewVideo.style.display = 'block';
            previewVideo.src = URL.createObjectURL(file);
        } else {
            previewVideo.style.display = 'none';
            previewImg.style.display = 'block';
            var reader = new FileReader();
            reader.onload = function(e) { previewImg.src = e.target.result; };
            reader.readAsDataURL(file);
        }
        mediaPreview.style.display = 'block';
    });

    // Form submit loading state
    document.getElementById('mediaForm').addEventListener('submit', function() {
        var btn = document.getElementById('submitBtn');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';
        btn.disabled = true;
        setTimeout(function() {
            btn.innerHTML = '<i class="fas fa-upload me-2"></i>Add to TV';
            btn.disabled = false;
        }, 15000);
    });
});
</script>
@endsection
