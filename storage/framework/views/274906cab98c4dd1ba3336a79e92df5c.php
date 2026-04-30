<?php $__env->startSection('title', 'Edit Umrah Package'); ?>

<?php $__env->startSection('page-title', 'Edit Umrah Package'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2 px-md-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card shadow-lg border-0 animate-fade-in">
                <div class="card-header">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <h3 class="card-title">
                            <i class="fas fa-kaaba me-2"></i>Edit Umrah Package
                        </h3>
                        <a href="<?php echo e(route('admin.umrah-packages.index')); ?>"
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>

                <form method="POST" action="<?php echo e(route('admin.umrah-packages.update', $package->id)); ?>" enctype="multipart/form-data" id="packageForm">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
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
                                           class="form-control form-control-mobile <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           id="title" name="title"
                                           value="<?php echo e(old('title', $package->title)); ?>"
                                           required maxlength="255">
                                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <!-- Price, Currency & Duration Row -->
                                <div class="row g-2 g-sm-3">
                                    <div class="col-6 col-sm-4">
                                        <div class="mb-3 mb-md-4">
                                            <label for="price" class="form-label fw-semibold text-gold d-flex align-items-center">
                                                <i class="fas fa-tag me-2 d-none d-sm-inline"></i>
                                                <span>Price</span>
                                                <span class="text-danger ms-1">*</span>
                                            </label>
                                            <input type="number"
                                                   class="form-control form-control-mobile <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   id="price" name="price"
                                                   value="<?php echo e(old('price', $package->price)); ?>"
                                                   step="0.01" min="0" required>
                                            <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="mb-3 mb-md-4">
                                            <label for="currency" class="form-label fw-semibold text-gold d-flex align-items-center">
                                                <i class="fas fa-dollar-sign me-2 d-none d-sm-inline"></i>
                                                <span>Currency</span>
                                            </label>
                                            <select class="form-control form-control-mobile" id="currency" name="currency">
                                                <option value="USD" <?php echo e(old('currency', $package->currency) == 'USD' ? 'selected' : ''); ?>>USD ($)</option>
                                                <option value="AED" <?php echo e(old('currency', $package->currency) == 'AED' ? 'selected' : ''); ?>>AED</option>
                                                <option value="SAR" <?php echo e(old('currency', $package->currency) == 'SAR' ? 'selected' : ''); ?>>SAR</option>
                                                <option value="GBP" <?php echo e(old('currency', $package->currency) == 'GBP' ? 'selected' : ''); ?>>GBP (£)</option>
                                                <option value="EUR" <?php echo e(old('currency', $package->currency) == 'EUR' ? 'selected' : ''); ?>>EUR (€)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-5">
                                        <div class="mb-3 mb-md-4">
                                            <label for="duration" class="form-label fw-semibold text-gold d-flex align-items-center">
                                                <i class="fas fa-clock me-2 d-none d-sm-inline"></i>
                                                <span>Duration</span>
                                                <span class="text-danger ms-1">*</span>
                                            </label>
                                            <input type="text"
                                                   class="form-control form-control-mobile <?php $__errorArgs = ['duration'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   id="duration" name="duration"
                                                   value="<?php echo e(old('duration', $package->duration)); ?>"
                                                   required maxlength="255">
                                            <?php $__errorArgs = ['duration'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                                                <option value="POPULAR" <?php echo e(old('tag', $package->tag) == 'POPULAR' ? 'selected' : ''); ?>>POPULAR</option>
                                                <option value="BEST VALUE" <?php echo e(old('tag', $package->tag) == 'BEST VALUE' ? 'selected' : ''); ?>>BEST VALUE</option>
                                                <option value="LUXURY" <?php echo e(old('tag', $package->tag) == 'LUXURY' ? 'selected' : ''); ?>>LUXURY</option>
                                                <option value="NEW" <?php echo e(old('tag', $package->tag) == 'NEW' ? 'selected' : ''); ?>>NEW</option>
                                                <option value="ECONOMY" <?php echo e(old('tag', $package->tag) == 'ECONOMY' ? 'selected' : ''); ?>>ECONOMY</option>
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
                                                   value="<?php echo e(old('sortOrder', $package->sortOrder)); ?>" min="0">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-3 d-flex align-items-center">
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                   id="isFeatured" name="isFeatured" value="1"
                                                   <?php echo e(old('isFeatured', $package->isFeatured) ? 'checked' : ''); ?>

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
                                    <textarea class="form-control form-control-mobile <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                              id="description" name="description" rows="3"
                                              required><?php echo e(old('description', $package->description)); ?></textarea>
                                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <!-- Features -->
                                <div class="mb-3 mb-md-4">
                                    <label for="features" class="form-label fw-semibold text-gold d-flex align-items-center">
                                        <i class="fas fa-list-check me-2 d-none d-sm-inline"></i>
                                        <span>Package Features</span>
                                    </label>
                                    <textarea class="form-control form-control-mobile <?php $__errorArgs = ['features'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                              id="features" name="features" rows="5"
                                              placeholder="One feature per line..."><?php echo e(old('features', is_array($package->features) ? implode("\n", $package->features) : '')); ?></textarea>
                                    <?php $__errorArgs = ['features'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-1"></i><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <div class="form-text text-light-muted">
                                        <small><i class="fas fa-info-circle me-1"></i>One feature per line. Each line becomes a bullet point.</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Image Upload -->
                            <div class="col-12 col-lg-4">
                                <div class="card bg-light-dark border-gold">
                                    <div class="card-body p-2 p-sm-3">
                                        <h6 class="card-title text-gold fw-semibold mb-2 mb-sm-3 fs-6">
                                            <i class="fas fa-image me-2"></i>Package Image
                                        </h6>

                                        <?php if($package->image): ?>
                                        <div class="mb-3">
                                            <label class="form-label fw-medium text-white" style="font-size: 0.85rem;">Current Image</label>
                                            <div class="border border-gold rounded p-2 bg-dark text-center">
                                                <img src="<?php echo e(asset($package->image)); ?>" alt="<?php echo e($package->title); ?>"
                                                     class="rounded" style="max-width: 100%; max-height: 180px; object-fit: cover;">
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <label class="form-label fw-medium text-muted" style="font-size: 0.8rem;">
                                            <?php echo e($package->image ? 'Replace Image (optional)' : 'Upload Image'); ?>

                                        </label>

                                        <div class="image-upload-area" id="dropZone" onclick="document.getElementById('image').click()">
                                            <i class="fas fa-cloud-upload-alt text-gold mb-2" style="font-size: 1.5rem;"></i>
                                            <p class="text-muted mb-1" style="font-size: 0.8rem;">
                                                <span class="d-none d-sm-inline">Drag & drop or click to upload</span>
                                                <span class="d-sm-none">Tap to upload</span>
                                            </p>
                                            <small class="text-muted">JPEG, PNG, GIF, WebP | Max 5MB</small>
                                        </div>

                                        <input type="file" class="d-none <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               id="image" name="image"
                                               accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                               onchange="previewImage(this)">
                                        <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="text-danger mt-2" style="font-size: 0.8rem;">
                                                <i class="fas fa-exclamation-circle me-1"></i><?php echo e($message); ?>

                                            </div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

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
                                <small><i class="fas fa-info-circle me-1"></i>Last modified: <?php echo e($package->modifiedDate ? \Carbon\Carbon::parse($package->modifiedDate)->format('M d, Y H:i') : 'Never'); ?></small>
                            </div>
                            <div class="d-flex gap-2 order-1 order-sm-2">
                                <a href="<?php echo e(route('admin.umrah-packages.index')); ?>"
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/admin/umrah-packages/edit.blade.php ENDPATH**/ ?>