@extends('layouts.admin')

@section('title', 'Add Media to TV')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-plus-circle me-2"></i>Add Media to TV Slot</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.homepageads.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to TVs
                        </a>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.homepageads.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="alert alert-info">
                                    <i class="fas fa-tv me-2"></i>
                                    <strong>TV Slot System:</strong> Each TV slot (1-5) is a dedicated display window on the homepage.
                                    You can add multiple images and videos to each TV. They will cycle automatically like an airport display.
                                </div>

                                <!-- TV Slot Selection -->
                                <div class="form-group mb-4">
                                    <label for="slotOrder" class="form-label h5">TV Slot <span class="text-danger">*</span></label>
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
                                    <small class="text-muted">Choose which TV window this media will appear in</small>
                                </div>

                                <!-- Media Type -->
                                <div class="form-group mb-4">
                                    <label class="form-label h5">Media Type <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="mediaType" id="typeImage" value="image" checked>
                                            <label class="form-check-label" for="typeImage">
                                                <i class="fas fa-image me-1"></i> Image
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="mediaType" id="typeVideo" value="video">
                                            <label class="form-check-label" for="typeVideo">
                                                <i class="fas fa-video me-1"></i> Video
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Duration (for images) -->
                                <div class="form-group mb-4" id="durationGroup">
                                    <label for="duration" class="form-label h5">Display Duration</label>
                                    <div class="input-group" style="max-width: 200px;">
                                        <input type="number" class="form-control" id="duration" name="duration"
                                               value="{{ old('duration', 5) }}" min="1" max="60">
                                        <span class="input-group-text">seconds</span>
                                    </div>
                                    <small class="text-muted">How long this image shows before the next one (videos play in full)</small>
                                </div>

                                <!-- File Upload -->
                                <div class="form-group mb-4">
                                    <label for="media" class="form-label h5" id="mediaLabel">Upload Image <span class="text-danger">*</span></label>
                                    <input type="file"
                                           class="form-control @error('media') is-invalid @enderror"
                                           id="media"
                                           name="media"
                                           accept="image/*"
                                           required>
                                    @error('media')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted" id="mediaHint">Accepted: JPEG, PNG, JPG, GIF, WEBP (max 5MB)</small>
                                </div>

                                <!-- Preview -->
                                <div id="mediaPreview" class="text-center mt-4" style="display: none;">
                                    <h6>Preview:</h6>
                                    <img id="previewImg" src="#" alt="Preview" class="img-fluid rounded shadow" style="max-height: 300px;">
                                    <video id="previewVideo" controls class="img-fluid rounded shadow" style="max-height: 300px; display: none;"></video>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-upload"></i> Add to TV
                        </button>
                        <a href="{{ route('admin.homepageads.index') }}" class="btn btn-secondary btn-lg ms-3">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
            mediaLabel.innerHTML = 'Upload Video <span class="text-danger">*</span>';
            mediaInput.accept = 'video/mp4';
            mediaHint.textContent = 'Accepted: MP4 (max 50MB)';
            durationGroup.style.display = 'none';
        } else {
            mediaLabel.innerHTML = 'Upload Image <span class="text-danger">*</span>';
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
});
</script>
@endsection
