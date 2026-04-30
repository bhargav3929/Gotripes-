<?php $__env->startSection('title', 'Edit User'); ?>

<?php $__env->startSection('page-title', 'Edit User'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card shadow-lg border-0 animate-fade-in">
                <div class="card-header">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <h3 class="card-title">
                            <i class="fas fa-user-edit me-2"></i>Edit User: <?php echo e($user->name); ?>

                        </h3>
                        <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Users
                        </a>
                    </div>
                </div>
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

                    <?php
                        $currentAccessType = $user->access_type ?? ($user->isAdmin() ? 'full' : 'specific');
                    ?>

                    <form action="<?php echo e(route('admin.users.update', $user->id)); ?>" method="POST" id="editUserForm">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        
                        <div class="mb-4">
                            <h6 class="section-label">
                                Account Details
                            </h6>
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label for="name" class="form-label">Username <span class="text-required">*</span></label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           id="name" name="name" value="<?php echo e(old('name', $user->name)); ?>"
                                           placeholder="Enter username" required>
                                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="email" class="form-label">Email <span class="text-required">*</span></label>
                                    <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           id="email" name="email" value="<?php echo e(old('email', $user->email)); ?>"
                                           placeholder="Enter email address" required>
                                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="password" class="form-label">Change Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               id="password" name="password" placeholder="Leave blank to keep current">
                                        <button class="btn btn-outline-primary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Only fill this if you want to change the password</small>
                                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <hr class="section-divider">

                        
                        <div class="mb-4">
                            <h6 class="section-label">
                                Current Roles
                            </h6>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <?php $__empty_1 = true; $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <span class="badge rounded-pill px-3 py-1 badge-gold">
                                        <i class="fas fa-tag me-1"></i><?php echo e($role->title); ?>

                                    </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <span class="text-muted fs-7">No roles assigned</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <hr class="section-divider">

                        
                        <div class="mb-4">
                            <h6 class="section-label">
                                Access Control
                            </h6>

                            <label class="form-label mb-3">Access Type <span class="text-required">*</span></label>

                            <div class="row g-3 mb-3">
                                <div class="col-12 col-sm-6">
                                    <div class="access-type-card" id="fullAccessCard" onclick="selectAccessType('full')">
                                        <input type="radio" name="access_type" value="full" id="accessFull"
                                               <?php echo e(old('access_type', $currentAccessType) === 'full' ? 'checked' : ''); ?> class="d-none">
                                        <div class="access-card-inner">
                                            <div class="access-card-icon">
                                                <i class="fas fa-shield-alt"></i>
                                            </div>
                                            <div>
                                                <strong class="d-block" class="text-light">Full Access</strong>
                                                <small class="text-muted">Complete admin privileges to all modules</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="access-type-card" id="specificAccessCard" onclick="selectAccessType('specific')">
                                        <input type="radio" name="access_type" value="specific" id="accessSpecific"
                                               <?php echo e(old('access_type', $currentAccessType) === 'specific' ? 'checked' : ''); ?> class="d-none">
                                        <div class="access-card-inner">
                                            <div class="access-card-icon">
                                                <i class="fas fa-key"></i>
                                            </div>
                                            <div>
                                                <strong class="d-block" class="text-light">Specific Access</strong>
                                                <small class="text-muted">Limited to selected modules only</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                            <div id="moduleSection" style="display: none;">
                                <label class="form-label mb-3">Select Modules <span class="text-required">*</span></label>
                                <div class="row g-3">
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <label class="module-checkbox-card">
                                            <input type="checkbox" name="modules[]" value="uaeactivities"
                                                   <?php echo e(old('modules') ? (in_array('uaeactivities', old('modules', [])) ? 'checked' : '') : ($user->hasRole('Activities Manager') ? 'checked' : '')); ?>>
                                            <div class="module-card-inner">
                                                <i class="fas fa-map-marked-alt module-icon"></i>
                                                <span>UAE Activities</span>
                                                <i class="fas fa-check check-icon"></i>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <label class="module-checkbox-card">
                                            <input type="checkbox" name="modules[]" value="announcements"
                                                   <?php echo e(old('modules') ? (in_array('announcements', old('modules', [])) ? 'checked' : '') : ($user->hasRole('Announcements Manager') ? 'checked' : '')); ?>>
                                            <div class="module-card-inner">
                                                <i class="fas fa-bullhorn module-icon"></i>
                                                <span>Announcements</span>
                                                <i class="fas fa-check check-icon"></i>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <label class="module-checkbox-card">
                                            <input type="checkbox" name="modules[]" value="homepageads"
                                                   <?php echo e(old('modules') ? (in_array('homepageads', old('modules', [])) ? 'checked' : '') : ($user->hasRole('Carousel Manager') ? 'checked' : '')); ?>>
                                            <div class="module-card-inner">
                                                <i class="fas fa-images module-icon"></i>
                                                <span>Travel Ads</span>
                                                <i class="fas fa-check check-icon"></i>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="section-divider">

                        
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-end">
                            <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-outline-primary order-2 order-sm-1">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary order-1 order-sm-2">
                                <i class="fas fa-save me-2"></i>Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>



<?php $__env->startPush('scripts'); ?>
<script>
    function selectAccessType(type) {
        document.querySelectorAll('.access-type-card').forEach(function(card) {
            card.classList.remove('selected');
        });

        if (type === 'full') {
            document.getElementById('accessFull').checked = true;
            document.getElementById('fullAccessCard').classList.add('selected');
            document.getElementById('moduleSection').style.display = 'none';
            document.querySelectorAll('#moduleSection input[type="checkbox"]').forEach(function(cb) {
                cb.checked = false;
            });
        } else {
            document.getElementById('accessSpecific').checked = true;
            document.getElementById('specificAccessCard').classList.add('selected');
            document.getElementById('moduleSection').style.display = 'block';
        }
    }

    // Password toggle
    document.getElementById('togglePassword').addEventListener('click', function() {
        var pwd = document.getElementById('password');
        var icon = this.querySelector('i');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            pwd.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });

    // Initialize state on page load
    document.addEventListener('DOMContentLoaded', function() {
        var checked = document.querySelector('input[name="access_type"]:checked');
        if (checked) {
            selectAccessType(checked.value);
        } else {
            selectAccessType('full');
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/admin/users/edit.blade.php ENDPATH**/ ?>