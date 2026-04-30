<?php $__env->startSection('title', 'Edit Media'); ?>

<?php $__env->startSection('page-title', 'Edit Media'); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card border-0 animate-fade-in">
            <div class="card-header">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                    <h3 class="card-title">
                        <i class="fas fa-edit me-2"></i>Edit Media in TV <?php echo e($homepagead->slotOrder); ?>

                    </h3>
                    <a href="<?php echo e(route('admin.homepageads.index')); ?>" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to TVs
                    </a>
                </div>
            </div>
            <form method="POST" action="<?php echo e(route('admin.homepageads.update', $homepagead->id)); ?>" enctype="multipart/form-data" id="mediaForm">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="card-body p-3 p-md-4">

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    
                    <div class="mb-4">
                        <h6 class="section-label">TV Slot</h6>
                        <label for="slotOrder" class="form-label">TV Window <span class="text-required">*</span></label>
                        <select class="form-control" id="slotOrder" name="slotOrder" required>
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <option value="<?php echo e($i); ?>" <?php echo e(($homepagead->slotOrder ?? 0) == $i ? 'selected' : ''); ?>>TV <?php echo e($i); ?></option>
                            <?php endfor; ?>
                        </select>
                        <div class="form-text">Move this media to a different TV if needed</div>
                    </div>

                    <hr class="section-divider">

                    
                    <div class="mb-4">
                        <h6 class="section-label">Media Type</h6>
                        <div class="d-flex gap-3">
                            <label class="media-type-option">
                                <input type="radio" name="mediaType" id="typeImage" value="image"
                                       <?php echo e(($homepagead->mediaType ?? 'image') === 'image' ? 'checked' : ''); ?> class="d-none">
                                <div class="media-type-inner">
                                    <i class="fas fa-image"></i>
                                    <span>Image</span>
                                </div>
                            </label>
                            <label class="media-type-option">
                                <input type="radio" name="mediaType" id="typeVideo" value="video"
                                       <?php echo e(($homepagead->mediaType ?? 'image') === 'video' ? 'checked' : ''); ?> class="d-none">
                                <div class="media-type-inner">
                                    <i class="fas fa-video"></i>
                                    <span>Video</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    
                    <div class="mb-4" id="durationGroup" style="<?php echo e(($homepagead->mediaType ?? 'image') === 'video' ? 'display:none;' : ''); ?>">
                        <label for="duration" class="form-label">Display Duration</label>
                        <div class="input-group" style="max-width: 200px;">
                            <input type="number" class="form-control" id="duration" name="duration"
                                   value="<?php echo e($homepagead->duration ?? 5); ?>" min="1" max="60">
                            <span class="input-group-text">seconds</span>
                        </div>
                        <div class="form-text">How long this image displays before the next one</div>
                    </div>

                    <hr class="section-divider">

                    
                    <?php if($homepagead->imgPath): ?>
                    <div class="mb-4">
                        <h6 class="section-label">Current Media</h6>
                        <div class="current-media-box text-center">
                            <?php if(($homepagead->mediaType ?? 'image') === 'video'): ?>
                                <video controls class="img-fluid rounded" style="max-height: 280px;">
                                    <source src="<?php echo e(asset($homepagead->imgPath)); ?>" type="video/mp4">
                                </video>
                            <?php else: ?>
                                <img src="<?php echo e(asset($homepagead->imgPath)); ?>" alt="Current" class="img-fluid rounded" style="max-height: 280px;">
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    
                    <div class="mb-4">
                        <label for="media" class="form-label" id="mediaLabel">Replace File (Optional)</label>
                        <input type="file"
                               class="form-control <?php $__errorArgs = ['media'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               id="media"
                               name="media"
                               accept="<?php echo e(($homepagead->mediaType ?? 'image') === 'video' ? 'video/mp4' : 'image/*'); ?>">
                        <?php $__errorArgs = ['media'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div class="form-text" id="mediaHint">Leave empty to keep current file</div>
                    </div>

                    
                    <div id="mediaPreview" class="text-center mt-4" style="display: none;">
                        <div class="preview-box">
                            <div class="form-text mb-2">New Preview</div>
                            <img id="previewImg" src="#" alt="Preview" class="img-fluid rounded" style="max-height: 280px;">
                            <video id="previewVideo" controls class="img-fluid rounded" style="max-height: 280px; display: none;"></video>
                        </div>
                    </div>
                </div>

                
                <div class="card-footer bg-transparent" style="border-top: 1px solid var(--border-color);">
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-end">
                        <a href="<?php echo e(route('admin.homepageads.index')); ?>" class="btn btn-outline-primary order-2 order-sm-1">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary order-1 order-sm-2" id="submitBtn">
                            <i class="fas fa-save me-2"></i>Update Media
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
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

    .current-media-box {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 1rem;
    }

    .preview-box {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 215, 0, 0.15);
        border-radius: var(--radius-md);
        padding: 1rem;
    }
</style>

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

    // Form submit loading state
    document.getElementById('mediaForm').addEventListener('submit', function() {
        var btn = document.getElementById('submitBtn');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
        btn.disabled = true;
        setTimeout(function() {
            btn.innerHTML = '<i class="fas fa-save me-2"></i>Update Media';
            btn.disabled = false;
        }, 15000);
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/admin/homepageads/edit.blade.php ENDPATH**/ ?>