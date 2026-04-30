<?php $__env->startSection('title', $company->name); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="d-flex align-items-center gap-3">
        <a href="<?php echo e(route('superadmin.companies.index')); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="page-title mb-0"><?php echo e($company->name); ?></h1>
            <small class="text-muted"><?php echo e($company->subdomain); ?>.gotrips.ai</small>
        </div>
        <span class="badge bg-<?php echo e($company->is_active ? 'success' : 'danger'); ?> ms-2">
            <?php echo e($company->is_active ? 'Active' : 'Inactive'); ?>

        </span>
    </div>
    <div class="d-flex gap-2">
        <form action="<?php echo e(route('superadmin.companies.provision-subdomain', $company)); ?>" method="POST"
              onsubmit="return confirm('Provision <?php echo e($company->subdomain); ?>.gotrips.ai on Hostinger and link to the main Laravel app?');">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-outline-success btn-sm">
                <i class="fas fa-globe me-1"></i>Provision Subdomain
            </button>
        </form>
        <form action="<?php echo e(route('superadmin.companies.impersonate', $company)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-user-secret me-1"></i>Impersonate
            </button>
        </form>
        <a href="<?php echo e(route('superadmin.companies.edit', $company)); ?>" class="btn btn-warning btn-sm">
            <i class="fas fa-edit me-1"></i>Edit
        </a>
    </div>
</div>

<?php if(session('success')): ?><div class="alert alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
<?php if(session('error')): ?><div class="alert alert-danger"><?php echo e(session('error')); ?></div><?php endif; ?>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card text-center">
            <div class="stat-value"><?php echo e(number_format($stats['total_users'])); ?></div>
            <div class="stat-label">Users</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card text-center">
            <div class="stat-value"><?php echo e(number_format($stats['total_orders'])); ?></div>
            <div class="stat-label">Orders</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card text-center">
            <div class="stat-value text-success">AED <?php echo e(number_format($stats['total_revenue'], 2)); ?></div>
            <div class="stat-label">Revenue</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card text-center">
            <div class="stat-value"><?php echo e(number_format($stats['referral_agents'])); ?></div>
            <div class="stat-label">Referral Agents</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Company Info -->
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-info-circle me-2"></i>Company Information</div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr><td class="text-muted" width="150">Name</td><td><?php echo e($company->name); ?></td></tr>
                    <tr><td class="text-muted">Slug</td><td><code><?php echo e($company->slug); ?></code></td></tr>
                    <tr><td class="text-muted">Subdomain</td><td><?php echo e($company->subdomain); ?>.gotrips.ai</td></tr>
                    <tr><td class="text-muted">Custom Domain</td><td><?php echo e($company->domain ?: 'Not set'); ?></td></tr>
                    <tr><td class="text-muted">Email</td><td><?php echo e($company->email); ?></td></tr>
                    <tr><td class="text-muted">Phone</td><td><?php echo e($company->phone ?: 'Not set'); ?></td></tr>
                    <tr><td class="text-muted">Currency</td><td><?php echo e($company->currency); ?></td></tr>
                    <tr><td class="text-muted">Markup</td><td><?php echo e($company->markup_percentage); ?>%</td></tr>
                    <tr><td class="text-muted">Created</td><td><?php echo e($company->created_at->format('M d, Y H:i')); ?></td></tr>
                </table>
            </div>
        </div>

        <!-- Branding -->
        <div class="card">
            <div class="card-header"><i class="fas fa-palette me-2"></i>Branding</div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-4 mb-3">
                    <?php if($company->logo): ?>
                    <img src="<?php echo e($company->logo_url); ?>" alt="Logo" style="max-height: 60px;">
                    <?php else: ?>
                    <div class="text-muted">No logo uploaded</div>
                    <?php endif; ?>
                </div>
                <div class="d-flex gap-3">
                    <div class="text-center">
                        <div style="width: 40px; height: 40px; background: <?php echo e($company->primary_color); ?>; border-radius: 8px;"></div>
                        <small class="text-muted">Primary</small>
                    </div>
                    <div class="text-center">
                        <div style="width: 40px; height: 40px; background: <?php echo e($company->secondary_color); ?>; border-radius: 8px;"></div>
                        <small class="text-muted">Secondary</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscription & Users -->
    <div class="col-lg-6">
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
                <div class="alert alert-<?php echo e($company->subscription_ends_at->isFuture() ? 'info' : 'danger'); ?> py-2 mb-3">
                    <i class="fas fa-calendar me-2"></i>Subscription <?php echo e($company->subscription_ends_at->isFuture() ? 'ends' : 'ended'); ?> <?php echo e($company->subscription_ends_at->format('M d, Y')); ?>

                </div>
                <?php endif; ?>

                <hr>

                <!-- Change Plan -->
                <form action="<?php echo e(route('superadmin.companies.change-plan', $company)); ?>" method="POST" class="mb-3">
                    <?php echo csrf_field(); ?>
                    <div class="input-group">
                        <select name="plan" class="form-select form-select-sm">
                            <option value="trial" <?php echo e($company->plan === 'trial' ? 'selected' : ''); ?>>Trial</option>
                            <option value="basic" <?php echo e($company->plan === 'basic' ? 'selected' : ''); ?>>Basic</option>
                            <option value="pro" <?php echo e($company->plan === 'pro' ? 'selected' : ''); ?>>Pro</option>
                            <option value="enterprise" <?php echo e($company->plan === 'enterprise' ? 'selected' : ''); ?>>Enterprise</option>
                        </select>
                        <button type="submit" class="btn btn-outline-primary btn-sm">Change</button>
                    </div>
                </form>

                <!-- Extend Subscription -->
                <form action="<?php echo e(route('superadmin.companies.extend-subscription', $company)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="input-group">
                        <input type="number" name="days" class="form-control form-control-sm" placeholder="Days" min="1" max="365" value="30">
                        <button type="submit" class="btn btn-outline-success btn-sm">Extend</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Admin Users -->
        <div class="card">
            <div class="card-header"><i class="fas fa-users me-2"></i>Admin Users</div>
            <div class="card-body p-0">
                <?php $__empty_1 = true; $__currentLoopData = $company->admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="d-flex align-items-center p-3 border-bottom border-dark">
                    <div class="flex-grow-1">
                        <div class="fw-500"><?php echo e($admin->name); ?></div>
                        <small class="text-muted"><?php echo e($admin->email); ?></small>
                    </div>
                    <span class="badge bg-<?php echo e($admin->role === 'company_owner' ? 'primary' : 'info'); ?>">
                        <?php echo e(str_replace('_', ' ', ucfirst($admin->role))); ?>

                    </span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-4 text-muted">No admin users</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Danger Zone -->
<div class="card mt-4 border-danger">
    <div class="card-header text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Danger Zone</div>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong><?php echo e($company->is_active ? 'Deactivate' : 'Activate'); ?> Company</strong>
                <p class="text-muted mb-0 small"><?php echo e($company->is_active ? 'Suspend this company and prevent access' : 'Reactivate this company'); ?></p>
            </div>
            <form action="<?php echo e(route('superadmin.companies.toggle-status', $company)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-<?php echo e($company->is_active ? 'danger' : 'success'); ?> btn-sm">
                    <?php echo e($company->is_active ? 'Deactivate' : 'Activate'); ?>

                </button>
            </form>
        </div>

        <hr>

        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong class="text-danger">Delete Company</strong>
                <p class="text-muted mb-0 small">Permanently delete this company and all its data</p>
            </div>
            <form action="<?php echo e(route('superadmin.companies.destroy', $company)); ?>" method="POST"
                  onsubmit="return confirm('Are you sure? This action cannot be undone.')">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.superadmin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/superadmin/companies/show.blade.php ENDPATH**/ ?>