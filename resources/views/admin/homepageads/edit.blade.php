@extends('layouts.admin')

@section('title', 'Edit Ad Slot')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Ad Slot #{{ $homepagead->slotOrder }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.homepageads.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.homepageads.update', $homepagead->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <!-- Media Type Selection -->
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

                                <!-- Slot Order -->
                                <div class="form-group mb-4">
                                    <label for="slotOrder" class="form-label h5">Slot Order <span class="text-danger">*</span></label>
                                    <select class="form-control" id="slotOrder" name="slotOrder" required>
                                        @for($i = 1; $i <= 6; $i++)
                                            <option value="{{ $i }}" {{ ($homepagead->slotOrder ?? 0) == $i ? 'selected' : '' }}>Slot {{ $i }}</option>
                                        @endfor
                                    </select>
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

                                <!-- File Upload -->
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
                            <i class="fas fa-save"></i> Update Ad Slot
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
    const mediaInput = document.getElementById('media');
    const mediaLabel = document.getElementById('mediaLabel');
    const mediaHint = document.getElementById('mediaHint');
    const previewImg = document.getElementById('previewImg');
    const previewVideo = document.getElementById('previewVideo');
    const mediaPreview = document.getElementById('mediaPreview');
    const typeImage = document.getElementById('typeImage');
    const typeVideo = document.getElementById('typeVideo');

    function updateMediaType() {
        const isVideo = typeVideo.checked;
        mediaInput.accept = isVideo ? 'video/mp4' : 'image/*';
        mediaInput.value = '';
        mediaPreview.style.display = 'none';
    }

    typeImage.addEventListener('change', updateMediaType);
    typeVideo.addEventListener('change', updateMediaType);

    mediaInput.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) {
            mediaPreview.style.display = 'none';
            return;
        }

        const isVideo = typeVideo.checked;
        if (isVideo) {
            previewImg.style.display = 'none';
            previewVideo.style.display = 'block';
            previewVideo.src = URL.createObjectURL(file);
        } else {
            previewVideo.style.display = 'none';
            previewImg.style.display = 'block';
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
        mediaPreview.style.display = 'block';
    });
});
</script>
@endsection
