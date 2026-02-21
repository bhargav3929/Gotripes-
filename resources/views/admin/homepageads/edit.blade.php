@extends('layouts.admin')

@section('title', 'Edit Media')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-edit me-2"></i>Edit Media in TV {{ $homepagead->slotOrder }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.homepageads.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to TVs
                        </a>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.homepageads.update', $homepagead->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <!-- TV Slot -->
                                <div class="form-group mb-4">
                                    <label for="slotOrder" class="form-label h5">TV Slot <span class="text-danger">*</span></label>
                                    <select class="form-control" id="slotOrder" name="slotOrder" required>
                                        @for($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}" {{ ($homepagead->slotOrder ?? 0) == $i ? 'selected' : '' }}>TV {{ $i }}</option>
                                        @endfor
                                    </select>
                                    <small class="text-muted">Move this media to a different TV if needed</small>
                                </div>

                                <!-- Media Type -->
                                <div class="form-group mb-4">
                                    <label class="form-label h5">Media Type <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="mediaType" id="typeImage" value="image"
                                                   {{ ($homepagead->mediaType ?? 'image') === 'image' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="typeImage">
                                                <i class="fas fa-image me-1"></i> Image
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="mediaType" id="typeVideo" value="video"
                                                   {{ ($homepagead->mediaType ?? 'image') === 'video' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="typeVideo">
                                                <i class="fas fa-video me-1"></i> Video
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Duration -->
                                <div class="form-group mb-4" id="durationGroup" style="{{ ($homepagead->mediaType ?? 'image') === 'video' ? 'display:none;' : '' }}">
                                    <label for="duration" class="form-label h5">Display Duration</label>
                                    <div class="input-group" style="max-width: 200px;">
                                        <input type="number" class="form-control" id="duration" name="duration"
                                               value="{{ $homepagead->duration ?? 5 }}" min="1" max="60">
                                        <span class="input-group-text">seconds</span>
                                    </div>
                                    <small class="text-muted">How long this image displays before the next one</small>
                                </div>

                                <!-- Current Media -->
                                @if($homepagead->imgPath)
                                <div class="form-group mb-4">
                                    <h6>Current Media:</h6>
                                    <div class="text-center">
                                        @if(($homepagead->mediaType ?? 'image') === 'video')
                                            <video controls class="img-fluid rounded shadow" style="max-height: 300px;">
                                                <source src="{{ asset($homepagead->imgPath) }}" type="video/mp4">
                                            </video>
                                        @else
                                            <img src="{{ asset($homepagead->imgPath) }}" alt="Current" class="img-fluid rounded shadow" style="max-height: 300px;">
                                        @endif
                                    </div>
                                </div>
                                @endif

                                <!-- Replace File -->
                                <div class="form-group mb-4">
                                    <label for="media" class="form-label h5" id="mediaLabel">Replace File (Optional)</label>
                                    <input type="file"
                                           class="form-control @error('media') is-invalid @enderror"
                                           id="media"
                                           name="media"
                                           accept="{{ ($homepagead->mediaType ?? 'image') === 'video' ? 'video/mp4' : 'image/*' }}">
                                    @error('media')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted" id="mediaHint">Leave empty to keep current file</small>
                                </div>

                                <!-- Preview -->
                                <div id="mediaPreview" class="text-center mt-4" style="display: none;">
                                    <h6>New Preview:</h6>
                                    <img id="previewImg" src="#" alt="Preview" class="img-fluid rounded shadow" style="max-height: 300px;">
                                    <video id="previewVideo" controls class="img-fluid rounded shadow" style="max-height: 300px; display: none;"></video>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save"></i> Update Media
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
    var previewImg = document.getElementById('previewImg');
    var previewVideo = document.getElementById('previewVideo');
    var mediaPreview = document.getElementById('mediaPreview');
    var typeImage = document.getElementById('typeImage');
    var typeVideo = document.getElementById('typeVideo');
    var durationGroup = document.getElementById('durationGroup');

    function updateMediaType() {
        var isVideo = typeVideo.checked;
        mediaInput.accept = isVideo ? 'video/mp4' : 'image/*';
        durationGroup.style.display = isVideo ? 'none' : 'block';
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
