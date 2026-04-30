<?php $__env->startSection('title', 'Branding'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-palette me-2"></i>Branding</h1>
</div>

<form action="<?php echo e(route('client.branding.update')); ?>" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Company Info -->
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-building me-2"></i>Company Information</div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label mb-1" style="font-size: 0.75rem;">Company Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-sm <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('name', $company->name)); ?>" required>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label mb-1" style="font-size: 0.75rem;">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control form-control-sm <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('email', $company->email)); ?>" required>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label mb-1" style="font-size: 0.75rem;">Phone</label>
                            <input type="text" name="phone" class="form-control form-control-sm <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('phone', $company->phone)); ?>">
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visual Branding -->
            <div class="card">
                <div class="card-header"><i class="fas fa-paint-brush me-2"></i>Visual Branding</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Logo</label>
                            <?php if($company->logo): ?>
                            <div class="mb-3 p-3 rounded" style="background: rgba(0,0,0,0.3);">
                                <img src="<?php echo e($company->logo_url); ?>" alt="Current Logo" style="max-height: 60px;">
                                <small class="d-block text-muted mt-2">Current logo</small>
                            </div>
                            <?php endif; ?>
                            <input type="file" name="logo" class="form-control form-control-sm <?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   accept="image/*">
                            <?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted">Recommended: PNG or SVG, max 2MB</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Primary Color</label>
                            <input type="color" name="primary_color" class="form-control form-control-color w-100"
                                   value="<?php echo e(old('primary_color', $company->primary_color)); ?>" style="height: 50px;">
                            <small class="text-muted">Main accent color</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Secondary Color</label>
                            <input type="color" name="secondary_color" class="form-control form-control-color w-100"
                                   value="<?php echo e(old('secondary_color', $company->secondary_color)); ?>" style="height: 50px;">
                            <small class="text-muted">Gradient/hover color</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Preview -->
            <div class="card mb-3">
                <div class="card-header py-2"><i class="fas fa-eye me-2"></i>Live Preview</div>
                <div class="card-body py-3 text-center">
                    <div class="p-3 rounded mb-2" style="background: linear-gradient(135deg, <?php echo e($company->primary_color); ?> 0%, <?php echo e($company->secondary_color); ?> 100%);">
                        <?php if($company->logo): ?>
                        <img src="<?php echo e($company->logo_url); ?>" alt="Logo" style="max-height: 30px;">
                        <?php else: ?>
                        <span class="text-dark fw-600"><?php echo e($company->name); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm flex-grow-1" style="background: <?php echo e($company->primary_color); ?>; color: #000; font-size: 0.75rem;">
                            Primary
                        </button>
                        <button type="button" class="btn btn-sm flex-grow-1" style="background: <?php echo e($company->secondary_color); ?>; color: #000; font-size: 0.75rem;">
                            Secondary
                        </button>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <button type="submit" class="btn btn-primary btn-sm w-100 mb-3">
                <i class="fas fa-save me-1"></i>Save Branding
            </button>

            <!-- Domain Info -->
            <div class="card mt-4">
                <div class="card-header"><i class="fas fa-globe me-2"></i>Your Domain</div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Subdomain:</strong><br>
                        <code><?php echo e($company->subdomain); ?>.gotrips.ai</code>
                    </p>
                    <?php if($company->domain): ?>
                    <p class="mb-0">
                        <strong>Custom Domain:</strong><br>
                        <code><?php echo e($company->domain); ?></code>
                    </p>
                    <?php else: ?>
                    <p class="text-muted mb-0">
                        <small>Contact support to set up a custom domain.</small>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/client/branding.blade.php ENDPATH**/ ?>