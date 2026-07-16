@extends('layouts.admin')

@section('title', 'Edit Hajj & Umrah Package')

@section('page-title', 'Edit Hajj & Umrah Package')

@section('content')
<div class="container-fluid px-2 px-md-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card shadow-lg border-0 animate-fade-in">
                <div class="card-header">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <h3 class="card-title">
                            <i class="fas fa-kaaba me-2"></i>Edit Hajj & Umrah Package
                        </h3>
                        <a href="{{ route('admin.umrah-packages.index') }}"
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.umrah-packages.update', $package->id) }}" enctype="multipart/form-data" id="packageForm">
                    @csrf
                    @method('PUT')
                    <div class="card-body p-2 p-sm-3 p-md-4">
                        <div class="row g-2 g-sm-3 g-md-4">

                            <!-- Left Column: Main Fields -->
                            <div class="col-12 col-lg-8">
                                <!-- Title -->
                                <div class="mb-3 mb-md-4">
                                    <label for="title" class="form-label fw-semibold text-gold d-flex align-items-center">
                                        <i class="fas fa-heading me-2 d-none d-sm-inline"></i>
                                        <span>Package Title</span>
                                        <span class="text-danger ms-1">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control form-control-mobile @error('title') is-invalid @enderror"
                                           id="title" name="title"
                                           value="{{ old('title', $package->title) }}"
                                           required maxlength="255">
                                    @error('title')
                                        <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                 <!-- Price, Currency, Category & Duration Row -->
                                 <div class="row g-2 g-sm-3">
                                     <div class="col-6 col-sm-3">
                                         <div class="mb-3 mb-md-4">
                                             <label for="price" class="form-label fw-semibold text-gold d-flex align-items-center">
                                                 <i class="fas fa-tag me-2 d-none d-sm-inline"></i>
                                                 <span>Price</span>
                                                 <span class="text-danger ms-1">*</span>
                                             </label>
                                             <input type="number"
                                                    class="form-control form-control-mobile @error('price') is-invalid @enderror"
                                                    id="price" name="price"
                                                    value="{{ old('price', $package->price) }}"
                                                    step="0.01" min="0" required>
                                             @error('price')
                                                 <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                             @enderror
                                         </div>
                                     </div>
                                     <div class="col-6 col-sm-2">
                                         <div class="mb-3 mb-md-4">
                                             <label for="currency" class="form-label fw-semibold text-gold d-flex align-items-center">
                                                 <i class="fas fa-dollar-sign me-2 d-none d-sm-inline"></i>
                                                 <span>Currency</span>
                                             </label>
                                             <select class="form-control form-control-mobile" id="currency" name="currency">
                                                 <option value="USD" {{ old('currency', $package->currency) == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                                 <option value="AED" {{ old('currency', $package->currency) == 'AED' ? 'selected' : '' }}>AED</option>
                                                 <option value="SAR" {{ old('currency', $package->currency) == 'SAR' ? 'selected' : '' }}>SAR</option>
                                                 <option value="GBP" {{ old('currency', $package->currency) == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                                 <option value="EUR" {{ old('currency', $package->currency) == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                             </select>
                                         </div>
                                     </div>
                                     <div class="col-6 col-sm-3">
                                         <div class="mb-3 mb-md-4">
                                             <label for="category" class="form-label fw-semibold text-gold d-flex align-items-center">
                                                 <i class="fas fa-list me-2 d-none d-sm-inline"></i>
                                                 <span>Category</span>
                                                 <span class="text-danger ms-1">*</span>
                                             </label>
                                             <select class="form-control form-control-mobile" id="category" name="category" required>
                                                 <option value="economy" {{ old('category', $package->category) == 'economy' ? 'selected' : '' }}>Economy</option>
                                                 <option value="standard" {{ old('category', $package->category) == 'standard' ? 'selected' : '' }}>Standard</option>
                                                 <option value="premium" {{ old('category', $package->category) == 'premium' ? 'selected' : '' }}>Premium</option>
                                                 <option value="vip" {{ old('category', $package->category) == 'vip' ? 'selected' : '' }}>VIP</option>
                                             </select>
                                         </div>
                                     </div>
                                     <div class="col-6 col-sm-4">
                                         <div class="mb-3 mb-md-4">
                                             <label for="duration" class="form-label fw-semibold text-gold d-flex align-items-center">
                                                 <i class="fas fa-clock me-2 d-none d-sm-inline"></i>
                                                 <span>Duration</span>
                                                 <span class="text-danger ms-1">*</span>
                                             </label>
                                             <input type="text"
                                                    class="form-control form-control-mobile @error('duration') is-invalid @enderror"
                                                    id="duration" name="duration"
                                                    value="{{ old('duration', $package->duration) }}"
                                                    required maxlength="255">
                                             @error('duration')
                                                 <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                             @enderror
                                         </div>
                                     </div>
                                 </div>

                                <!-- Tag & Sort Order Row -->
                                <div class="row g-2 g-sm-3">
                                    <div class="col-8 col-sm-6">
                                        <div class="mb-3 mb-md-4">
                                            <label for="tag" class="form-label fw-semibold text-gold d-flex align-items-center">
                                                <i class="fas fa-bookmark me-2 d-none d-sm-inline"></i>
                                                <span>Tag / Badge</span>
                                            </label>
                                            <select class="form-control form-control-mobile" id="tag" name="tag">
                                                <option value="">No Tag</option>
                                                <option value="POPULAR" {{ old('tag', $package->tag) == 'POPULAR' ? 'selected' : '' }}>POPULAR</option>
                                                <option value="BEST VALUE" {{ old('tag', $package->tag) == 'BEST VALUE' ? 'selected' : '' }}>BEST VALUE</option>
                                                <option value="LUXURY" {{ old('tag', $package->tag) == 'LUXURY' ? 'selected' : '' }}>LUXURY</option>
                                                <option value="NEW" {{ old('tag', $package->tag) == 'NEW' ? 'selected' : '' }}>NEW</option>
                                                <option value="ECONOMY" {{ old('tag', $package->tag) == 'ECONOMY' ? 'selected' : '' }}>ECONOMY</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4 col-sm-3">
                                        <div class="mb-3 mb-md-4">
                                            <label for="sortOrder" class="form-label fw-semibold text-gold d-flex align-items-center">
                                                <i class="fas fa-sort me-2 d-none d-sm-inline"></i>
                                                <span>Order</span>
                                            </label>
                                            <input type="number" class="form-control form-control-mobile"
                                                   id="sortOrder" name="sortOrder"
                                                   value="{{ old('sortOrder', $package->sortOrder) }}" min="0">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-3 d-flex align-items-center">
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                   id="isFeatured" name="isFeatured" value="1"
                                                   {{ old('isFeatured', $package->isFeatured) ? 'checked' : '' }}
                                                   style="min-width: 3rem; min-height: 1.5rem; background-color: rgba(255,255,255,0.06); border: 1px solid var(--border-color);">
                                            <label class="form-check-label text-white ms-2" for="isFeatured">
                                                <i class="fas fa-star text-gold me-1"></i>Featured
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="mb-3 mb-md-4">
                                    <label for="description" class="form-label fw-semibold text-gold d-flex align-items-center">
                                        <i class="fas fa-align-left me-2 d-none d-sm-inline"></i>
                                        <span>Description</span>
                                        <span class="text-danger ms-1">*</span>
                                    </label>
                                    <textarea class="form-control form-control-mobile @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="3"
                                              required>{{ old('description', $package->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>

                                 <!-- Features -->
                                 <div class="mb-3 mb-md-4">
                                     <label for="features" class="form-label fw-semibold text-gold d-flex align-items-center">
                                         <i class="fas fa-list-check me-2 d-none d-sm-inline"></i>
                                         <span>Package Features</span>
                                     </label>
                                     <textarea class="form-control form-control-mobile" id="features" name="features" rows="4"
                                               placeholder="One feature per line...">{{ old('features', is_array($package->features) ? implode("\n", $package->features) : '') }}</textarea>
                                 </div>

                                 <!-- Transport & Hotels -->
                                 <div class="row g-2 g-sm-3">
                                     <div class="col-sm-6">
                                         <div class="mb-3 mb-md-4">
                                             <label for="transport" class="form-label fw-semibold text-gold d-flex align-items-center">
                                                 <i class="fas fa-bus me-2 d-none d-sm-inline"></i>
                                                 <span>Transport Details</span>
                                             </label>
                                             <input type="text" class="form-control form-control-mobile" id="transport" name="transport" value="{{ old('transport', $package->transport) }}" placeholder="e.g., Luxury AC Coach Service">
                                         </div>
                                     </div>
                                     <div class="col-sm-6">
                                         <div class="mb-3 mb-md-4">
                                             <label for="hotels" class="form-label fw-semibold text-gold d-flex align-items-center">
                                                 <i class="fas fa-building me-2 d-none d-sm-inline"></i>
                                                 <span>Hotels Description</span>
                                             </label>
                                             <input type="text" class="form-control form-control-mobile" id="hotels" name="hotels" value="{{ old('hotels', $package->hotels) }}" placeholder="e.g., 3 Nights Makkah + 4 Nights Medina">
                                         </div>
                                     </div>
                                 </div>

                                 <!-- Inclusions & Exclusions -->
                                 <div class="row g-2 g-sm-3">
                                     <div class="col-sm-6">
                                         <div class="mb-3 mb-md-4">
                                             <label for="inclusions" class="form-label fw-semibold text-gold d-flex align-items-center">
                                                 <i class="fas fa-plus-circle me-2 d-none d-sm-inline"></i>
                                                 <span>Inclusions (one per line)</span>
                                             </label>
                                             <textarea class="form-control form-control-mobile" id="inclusions" name="inclusions" rows="4" placeholder="Umrah Visa included&#10;Complimentary Zamzam water">{{ old('inclusions', is_array($package->inclusions) ? implode("\n", $package->inclusions) : '') }}</textarea>
                                         </div>
                                     </div>
                                     <div class="col-sm-6">
                                         <div class="mb-3 mb-md-4">
                                             <label for="exclusions" class="form-label fw-semibold text-gold d-flex align-items-center">
                                                 <i class="fas fa-minus-circle me-2 d-none d-sm-inline"></i>
                                                 <span>Exclusions (one per line)</span>
                                             </label>
                                             <textarea class="form-control form-control-mobile" id="exclusions" name="exclusions" rows="4" placeholder="Food not included&#10;Personal items">{{ old('exclusions', is_array($package->exclusions) ? implode("\n", $package->exclusions) : '') }}</textarea>
                                         </div>
                                     </div>
                                 </div>

                                 <!-- Itinerary -->
                                 <div class="mb-3 mb-md-4">
                                     <label for="itinerary" class="form-label fw-semibold text-gold d-flex align-items-center">
                                         <i class="fas fa-route me-2 d-none d-sm-inline"></i>
                                         <span>Itinerary (one day per line)</span>
                                     </label>
                                     <textarea class="form-control form-control-mobile" id="itinerary" name="itinerary" rows="5" placeholder="Day 1: Departure from Dubai&#10;Day 2: Rest and Medina Ziyarat">{{ old('itinerary', is_array($package->itinerary) ? implode("\n", $package->itinerary) : '') }}</textarea>
                                 </div>
                            </div>

                            <!-- Right Column: Image Upload -->
                            <div class="col-12 col-lg-4">
                                <div class="card bg-light-dark border-gold">
                                    <div class="card-body p-2 p-sm-3">
                                        <h6 class="card-title text-gold fw-semibold mb-2 mb-sm-3 fs-6">
                                            <i class="fas fa-image me-2"></i>Package Image
                                        </h6>

                                        @if($package->image)
                                        <div class="mb-3">
                                            <label class="form-label fw-medium text-white" style="font-size: 0.85rem;">Current Image</label>
                                            <div class="border border-gold rounded p-2 bg-dark text-center">
                                                <img src="{{ asset($package->image) }}" alt="{{ $package->title }}"
                                                     class="rounded" style="max-width: 100%; max-height: 180px; object-fit: cover;">
                                            </div>
                                        </div>
                                        @endif

                                        <label class="form-label fw-medium text-muted" style="font-size: 0.8rem;">
                                            {{ $package->image ? 'Replace Image (optional)' : 'Upload Image' }}
                                        </label>

                                        <div class="image-upload-area" id="dropZone" onclick="document.getElementById('image').click()">
                                            <i class="fas fa-cloud-upload-alt text-gold mb-2" style="font-size: 1.5rem;"></i>
                                            <p class="text-muted mb-1" style="font-size: 0.8rem;">
                                                <span class="d-none d-sm-inline">Drag & drop or click to upload</span>
                                                <span class="d-sm-none">Tap to upload</span>
                                            </p>
                                            <small class="text-muted">JPEG, PNG, GIF, WebP | Max 5MB</small>
                                        </div>

                                        <input type="file" class="d-none @error('image') is-invalid @enderror"
                                               id="image" name="image"
                                               accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                               onchange="previewImage(this)">
                                        @error('image')
                                            <div class="text-danger mt-2" style="font-size: 0.8rem;">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror

                                         <div class="mt-4 mb-2">
                                             <h6 class="card-title text-gold fw-semibold mb-2 fs-6">
                                                 <i class="fas fa-images me-2"></i>Gallery Images (optional)
                                             </h6>
                                             <input type="file" name="gallery_images[]" class="form-control" multiple accept="image/*" style="background-color: rgba(255,255,255,0.06); border: 1px solid var(--border-color); color: #fff;">
                                             <div class="form-text text-light-muted">
                                                 <small><i class="fas fa-info-circle me-1"></i>This will overwrite existing gallery images.</small>
                                             </div>
                                         </div>

                                        <div id="imagePreview" class="mt-3 d-none">
                                            <div class="border border-gold rounded p-2 bg-dark text-center">
                                                <img id="previewImg" src="" alt="Preview" class="rounded" style="max-width: 100%; max-height: 200px; object-fit: cover;">
                                                <div class="mt-2 d-flex justify-content-between align-items-center">
                                                    <small class="text-gold">New Image Preview</small>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeImage()"><i class="fas fa-times"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="card-footer bg-transparent border-top-gold p-2 p-sm-3 p-md-4">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2">
                            <div class="text-light-muted order-2 order-sm-1 text-center text-sm-start">
                                <small><i class="fas fa-info-circle me-1"></i>Last modified: {{ $package->modifiedDate ? \Carbon\Carbon::parse($package->modifiedDate)->format('M d, Y H:i') : 'Never' }}</small>
                            </div>
                            <div class="d-flex gap-2 order-1 order-sm-2">
                                <a href="{{ route('admin.umrah-packages.index') }}"
                                   class="btn btn-outline-primary flex-fill flex-sm-grow-0 animate-scale">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary flex-fill flex-sm-grow-0 animate-scale" id="submitBtn">
                                    <i class="fas fa-save me-1"></i>
                                    <span class="d-none d-sm-inline">Update Package</span>
                                    <span class="d-sm-none">Update</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .border-top-gold { border-top: 1px solid var(--border-gold) !important; }
    .image-upload-area {
        border: 2px dashed var(--border-gold); border-radius: var(--radius-sm);
        padding: 1.25rem; text-align: center; background: rgba(255, 215, 0, 0.03);
        transition: all 0.15s; cursor: pointer;
    }
    .image-upload-area:hover, .image-upload-area.dragover {
        background: rgba(255, 215, 0, 0.08); border-color: var(--primary-gold);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('packageForm');
    const submitBtn = document.getElementById('submitBtn');
    const dropZone = document.getElementById('dropZone');

    form.addEventListener('submit', function() {
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Updating...';
        submitBtn.disabled = true;
        setTimeout(() => { submitBtn.innerHTML = '<i class="fas fa-save me-1"></i> Update Package'; submitBtn.disabled = false; }, 10000);
    });

    ['dragenter', 'dragover'].forEach(evt => {
        dropZone.addEventListener(evt, function(e) { e.preventDefault(); dropZone.classList.add('dragover'); });
    });
    ['dragleave', 'drop'].forEach(evt => {
        dropZone.addEventListener(evt, function(e) { e.preventDefault(); dropZone.classList.remove('dragover'); });
    });
    dropZone.addEventListener('drop', function(e) {
        if (e.dataTransfer.files.length > 0) {
            document.getElementById('image').files = e.dataTransfer.files;
            previewImage(document.getElementById('image'));
        }
    });
});

function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (!['image/jpeg','image/png','image/jpg','image/gif','image/webp'].includes(file.type)) {
            alert('Please select a valid image file.'); input.value = ''; preview.classList.add('d-none'); return;
        }
        if (file.size / 1024 / 1024 > 5) {
            alert('Image must be less than 5MB.'); input.value = ''; preview.classList.add('d-none'); return;
        }
        const reader = new FileReader();
        reader.onload = function(e) { previewImg.src = e.target.result; preview.classList.remove('d-none'); };
        reader.readAsDataURL(file);
    }
}

function removeImage() {
    document.getElementById('image').value = '';
    document.getElementById('imagePreview').classList.add('d-none');
}
</script>
@endsection
