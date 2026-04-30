<?php $__env->startSection('title', 'Settings'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-cog me-2"></i>Settings</h1>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <form action="<?php echo e(route('client.settings.update')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <!-- Business Settings -->
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-briefcase me-2"></i>Business Settings</div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label mb-1" style="font-size: 0.75rem;">Currency</label>
                            <select name="currency" class="form-select form-select-sm <?php $__errorArgs = ['currency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="AED" <?php echo e($company->currency === 'AED' ? 'selected' : ''); ?>>AED - UAE Dirham</option>
                                <option value="USD" <?php echo e($company->currency === 'USD' ? 'selected' : ''); ?>>USD - US Dollar</option>
                                <option value="EUR" <?php echo e($company->currency === 'EUR' ? 'selected' : ''); ?>>EUR - Euro</option>
                                <option value="GBP" <?php echo e($company->currency === 'GBP' ? 'selected' : ''); ?>>GBP - British Pound</option>
                                <option value="INR" <?php echo e($company->currency === 'INR' ? 'selected' : ''); ?>>INR - Indian Rupee</option>
                            </select>
                            <?php $__errorArgs = ['currency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label mb-1" style="font-size: 0.75rem;">Timezone</label>
                            <select name="timezone" class="form-select form-select-sm <?php $__errorArgs = ['timezone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="Asia/Dubai" <?php echo e($company->timezone === 'Asia/Dubai' ? 'selected' : ''); ?>>Asia/Dubai (GMT+4)</option>
                                <option value="UTC" <?php echo e($company->timezone === 'UTC' ? 'selected' : ''); ?>>UTC</option>
                                <option value="America/New_York" <?php echo e($company->timezone === 'America/New_York' ? 'selected' : ''); ?>>America/New_York</option>
                                <option value="Europe/London" <?php echo e($company->timezone === 'Europe/London' ? 'selected' : ''); ?>>Europe/London</option>
                                <option value="Asia/Kolkata" <?php echo e($company->timezone === 'Asia/Kolkata' ? 'selected' : ''); ?>>Asia/Kolkata</option>
                            </select>
                            <?php $__errorArgs = ['timezone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label mb-1" style="font-size: 0.75rem;">Markup Percentage</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="markup_percentage" class="form-control form-control-sm <?php $__errorArgs = ['markup_percentage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('markup_percentage', $company->markup_percentage)); ?>" min="0" max="100" step="0.01">
                                <span class="input-group-text">%</span>
                            </div>
                            <?php $__errorArgs = ['markup_percentage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted">Added to base eSIM prices</small>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-save me-1"></i>Save Settings
            </button>
        </form>
    </div>

    <div class="col-lg-4">
        <!-- Subscription Info -->
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-credit-card me-2"></i>Subscription</div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Current Plan</span>
                    <span class="badge bg-<?php echo e($company->plan === 'enterprise' ? 'primary' : ($company->plan === 'pro' ? 'info' : ($company->plan === 'basic' ? 'success' : 'warning'))); ?> fs-6">
                        <?php echo e(ucfirst($company->plan)); ?>

                    </span>
                </div>

                <?php if($company->isOnTrial()): ?>
                <div class="alert alert-warning py-2 mb-3">
                    <i class="fas fa-clock me-2"></i>Trial ends <?php echo e($company->trial_ends_at->diffForHumans()); ?>

                </div>
                <?php elseif($company->subscription_ends_at): ?>
                <div class="alert alert-info py-2 mb-3">
                    <i class="fas fa-calendar me-2"></i>
                    Renews <?php echo e($company->subscription_ends_at->format('M d, Y')); ?>

                </div>
                <?php endif; ?>

                <a href="#" class="btn btn-outline-primary w-100">
                    <i class="fas fa-arrow-up me-2"></i>Upgrade Plan
                </a>
            </div>
        </div>

        <!-- Account Info -->
        <div class="card">
            <div class="card-header"><i class="fas fa-info-circle me-2"></i>Account Info</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Slug</td>
                        <td><code><?php echo e($company->slug); ?></code></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Created</td>
                        <td><?php echo e($company->created_at->format('M d, Y')); ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            <span class="badge bg-<?php echo e($company->is_active ? 'success' : 'danger'); ?>">
                                <?php echo e($company->is_active ? 'Active' : 'Inactive'); ?>

                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/client/settings.blade.php ENDPATH**/ ?>