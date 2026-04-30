<?php $__env->startSection('title', 'Settings'); ?>

<?php $__env->startSection('content'); ?>
<h1 class="page-title mb-3" style="font-size: 1.25rem;"><i class="fas fa-cog me-2"></i>Settings</h1>

<div class="row g-3">
    <div class="col-lg-8">
        <!-- General Settings -->
        <div class="card mb-3">
            <div class="card-header py-2" style="font-size: 0.85rem;"><i class="fas fa-globe me-2"></i>General</div>
            <div class="card-body py-2">
                <form action="<?php echo e(route('superadmin.settings.update')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label mb-1" style="font-size: 0.75rem;">Platform Name</label>
                            <input type="text" name="platform_name" class="form-control form-control-sm" value="<?php echo e($settings['platform_name'] ?? 'GoTrips SaaS'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label mb-1" style="font-size: 0.75rem;">Support Email</label>
                            <input type="email" name="support_email" class="form-control form-control-sm" value="<?php echo e($settings['support_email'] ?? 'support@gotrips.ai'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label mb-1" style="font-size: 0.75rem;">Currency</label>
                            <select name="default_currency" class="form-select form-select-sm">
                                <option value="AED" <?php echo e(($settings['default_currency'] ?? 'AED') === 'AED' ? 'selected' : ''); ?>>AED</option>
                                <option value="USD" <?php echo e(($settings['default_currency'] ?? '') === 'USD' ? 'selected' : ''); ?>>USD</option>
                                <option value="EUR" <?php echo e(($settings['default_currency'] ?? '') === 'EUR' ? 'selected' : ''); ?>>EUR</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label mb-1" style="font-size: 0.75rem;">Timezone</label>
                            <select name="default_timezone" class="form-select form-select-sm">
                                <option value="Asia/Dubai" <?php echo e(($settings['default_timezone'] ?? 'Asia/Dubai') === 'Asia/Dubai' ? 'selected' : ''); ?>>Asia/Dubai</option>
                                <option value="UTC" <?php echo e(($settings['default_timezone'] ?? '') === 'UTC' ? 'selected' : ''); ?>>UTC</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm mt-2"><i class="fas fa-save me-1"></i>Save</button>
                </form>
            </div>
        </div>

        <!-- Trial & Email in one row -->
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header py-2" style="font-size: 0.85rem;"><i class="fas fa-clock me-2"></i>Trial</div>
                    <div class="card-body py-2">
                        <form action="<?php echo e(route('superadmin.settings.update')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <div class="mb-2">
                                <label class="form-label mb-1" style="font-size: 0.75rem;">Duration (days)</label>
                                <input type="number" name="trial_days" class="form-control form-control-sm" value="<?php echo e($settings['trial_days'] ?? 14); ?>" min="1" max="90">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i>Save</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header py-2" style="font-size: 0.85rem;"><i class="fas fa-envelope me-2"></i>SMTP</div>
                    <div class="card-body py-2">
                        <form action="<?php echo e(route('superadmin.settings.update')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <div class="row g-2">
                                <div class="col-8">
                                    <input type="text" name="smtp_host" class="form-control form-control-sm" placeholder="Host" value="<?php echo e($settings['smtp_host'] ?? ''); ?>">
                                </div>
                                <div class="col-4">
                                    <input type="number" name="smtp_port" class="form-control form-control-sm" placeholder="Port" value="<?php echo e($settings['smtp_port'] ?? 587); ?>">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm mt-2"><i class="fas fa-save me-1"></i>Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card mb-3">
            <div class="card-header py-2" style="font-size: 0.85rem;"><i class="fas fa-bolt me-2"></i>Actions</div>
            <div class="card-body py-2">
                <div class="d-grid gap-2">
                    <form action="<?php echo e(route('superadmin.settings.clear-cache')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-outline-warning btn-sm w-100"><i class="fas fa-broom me-1"></i>Clear Cache</button>
                    </form>
                    <a href="<?php echo e(route('superadmin.companies.create')); ?>" class="btn btn-outline-primary btn-sm"><i class="fas fa-plus me-1"></i>New Company</a>
                    <a href="<?php echo e(route('superadmin.reports.index')); ?>" class="btn btn-outline-info btn-sm"><i class="fas fa-chart-bar me-1"></i>Reports</a>
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="card">
            <div class="card-header py-2" style="font-size: 0.85rem;"><i class="fas fa-server me-2"></i>System</div>
            <div class="card-body py-2">
                <table class="table table-sm table-borderless mb-0" style="font-size: 0.8rem;">
                    <tr><td class="text-muted py-1">PHP</td><td class="py-1"><?php echo e(PHP_VERSION); ?></td></tr>
                    <tr><td class="text-muted py-1">Laravel</td><td class="py-1"><?php echo e(app()->version()); ?></td></tr>
                    <tr><td class="text-muted py-1">Env</td><td class="py-1"><span class="badge bg-<?php echo e(app()->environment('production') ? 'success' : 'warning'); ?>" style="font-size: 0.65rem;"><?php echo e(app()->environment()); ?></span></td></tr>
                    <tr><td class="text-muted py-1">Debug</td><td class="py-1"><span class="badge bg-<?php echo e(config('app.debug') ? 'danger' : 'success'); ?>" style="font-size: 0.65rem;"><?php echo e(config('app.debug') ? 'ON' : 'OFF'); ?></span></td></tr>
                    <tr><td class="text-muted py-1">Cache</td><td class="py-1"><?php echo e(config('cache.default')); ?></td></tr>
                    <tr><td class="text-muted py-1">Queue</td><td class="py-1"><?php echo e(config('queue.default')); ?></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.superadmin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/superadmin/settings/index.blade.php ENDPATH**/ ?>