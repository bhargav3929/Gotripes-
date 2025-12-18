@extends('layouts.admin')

@section('title', 'Edit Carousel Image')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Carousel Image</h3>
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
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Image Requirements:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li><strong>Dimensions:</strong> Between 480x160 and 482x165 pixels</li>
                                        <li><strong>Formats:</strong> JPEG, PNG, JPG, GIF, WEBP</li>
                                        <li><strong>Maximum Size:</strong> 5MB</li>
                                    </ul>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="image" class="form-label h5">Replace Image (Optional)</label>
                                    <input type="file" 
                                           class="form-control @error('image') is-invalid @enderror" 
                                           id="image" 
                                           name="image" 
                                           accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Leave empty to keep current image. New image must be between 480x160 and 482x165 pixels</small>
                                </div>

                                @if($homepagead->imgPath)
                                <div class="form-group mb-4">
                                    <h6>Current Image:</h6>
                                    <div class="text-center">
                                        <img src="{{ asset($homepagead->imgPath) }}" alt="Current Image" 
                                             class="img-fluid rounded shadow" style="max-height: 300px;">
                                    </div>
                                </div>
                                @endif

                                <!-- New Image Preview -->
                                <div id="newImagePreview" class="text-center mt-4" style="display: none;">
                                    <h6>New Image Preview:</h6>
                                    <img id="newPreviewImg" src="#" alt="New Preview" class="img-fluid rounded shadow" style="max-height: 300px;">
                                    <div id="imageDimensions" class="mt-2 text-muted"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save"></i> Update Image
                        </button>
                        <a href="{{ route('admin.homepageads.index') }}" class="btn btn-secondary btn-lg ml-3">
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
    const imageInput = document.getElementById('image');
    const newImagePreview = document.getElementById('newImagePreview');
    const newPreviewImg = document.getElementById('newPreviewImg');
    const imageDimensions = document.getElementById('imageDimensions');

    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                newPreviewImg.src = e.target.result;
                
                // Check image dimensions
                newPreviewImg.onload = function() {
                    const width = this.naturalWidth;
                    const height = this.naturalHeight;
                    
                    imageDimensions.innerHTML = `<strong>New Image Dimensions:</strong> ${width} x ${height} pixels`;
                    
                    // Validate dimensions
                    if (width >= 480 && width <= 482 && height >= 160 && height <= 165) {
                        imageDimensions.innerHTML += ' <span class="badge bg-success">✓ Valid Dimensions</span>';
                    } else {
                        imageDimensions.innerHTML += ' <span class="badge bg-danger">✗ Invalid - Must be 480-482 x 160-165</span>';
                    }
                };
                
                newImagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            newImagePreview.style.display = 'none';
        }
    });
});
</script>
@endsection
