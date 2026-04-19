<?php $__env->startSection('title', 'Create Referral Agent'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?php echo e(route('admin.referrals.agents.index')); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 mb-1" style="color: var(--primary-gold);">
                <i class="fas fa-user-plus me-2"></i>Create Referral Agent
            </h1>
            <p class="text-muted mb-0">Add a new referral partner to your program</p>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-8">
            <form method="POST" action="<?php echo e(route('admin.referrals.agents.store')); ?>">
                <?php echo csrf_field(); ?>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user me-2 text-gold"></i>Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('name')); ?>" required placeholder="John Doe">
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
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('email')); ?>" required placeholder="agent@example.com">
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
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('phone')); ?>" placeholder="+971 50 123 4567">
                                <?php $__errorArgs = ['phone'];
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
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="active" <?php echo e(old('status') == 'active' ? 'selected' : ''); ?>>Active</option>
                                    <option value="inactive" <?php echo e(old('status') == 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                                    <option value="suspended" <?php echo e(old('status') == 'suspended' ? 'selected' : ''); ?>>Suspended</option>
                                </select>
                                <?php $__errorArgs = ['status'];
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
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-lock me-2 text-gold"></i>Login Credentials</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       required placeholder="Min 8 characters">
                                <?php $__errorArgs = ['password'];
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
                                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control"
                                       required placeholder="Confirm password">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-link me-2 text-gold"></i>Referral Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Referral Code</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-dark text-muted"><?php echo e(url('/')); ?>/?ref=</span>
                                    <input type="text" name="referral_code" id="referralCode"
                                           class="form-control <?php $__errorArgs = ['referral_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           value="<?php echo e(old('referral_code')); ?>" placeholder="Leave blank to auto-generate">
                                    <button type="button" class="btn btn-outline-gold" id="generateCode">
                                        <i class="fas fa-magic me-1"></i>Generate
                                    </button>
                                </div>
                                <small class="text-muted">Only letters, numbers, dashes and underscores allowed</small>
                                <?php $__errorArgs = ['referral_code'];
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
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-percentage me-2 text-gold"></i>Commission Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Commission Type <span class="text-danger">*</span></label>
                                <select name="commission_type" id="commissionType" class="form-select <?php $__errorArgs = ['commission_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="percentage" <?php echo e(old('commission_type') == 'percentage' ? 'selected' : ''); ?>>Percentage (%)</option>
                                    <option value="fixed" <?php echo e(old('commission_type') == 'fixed' ? 'selected' : ''); ?>>Fixed Amount (AED)</option>
                                </select>
                                <?php $__errorArgs = ['commission_type'];
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
                                <label class="form-label">Commission Value <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="commission_value" step="0.01" min="0"
                                           class="form-control <?php $__errorArgs = ['commission_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           value="<?php echo e(old('commission_value', 10)); ?>" required>
                                    <span class="input-group-text bg-dark border-dark" id="commissionSuffix">%</span>
                                </div>
                                <?php $__errorArgs = ['commission_value'];
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
                            <div class="col-12">
                                <div class="alert alert-info bg-dark border-info mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <span id="commissionInfo">Agent will earn 10% of each order amount</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-sticky-note me-2 text-gold"></i>Notes</h5>
                    </div>
                    <div class="card-body">
                        <textarea name="notes" class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                  rows="3" placeholder="Internal notes about this agent (optional)"><?php echo e(old('notes')); ?></textarea>
                        <?php $__errorArgs = ['notes'];
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
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?php echo e(route('admin.referrals.agents.index')); ?>" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-gold">
                        <i class="fas fa-save me-2"></i>Create Agent
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar Info -->
        <div class="col-12 col-lg-4">
            <div class="card sticky-top" style="top: 100px;">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-lightbulb me-2 text-warning"></i>Quick Tips</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0 tips-list">
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <strong class="text-white">Referral Code:</strong> <span class="tip-text">Keep it short and memorable. If left blank, it will be auto-generated from the agent's name.</span>
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <strong class="text-white">Commission Type:</strong> <span class="tip-text">Use percentage for varying order sizes, or fixed amount for consistent payouts.</span>
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <strong class="text-white">Status:</strong> <span class="tip-text">Only active agents can generate referrals. Inactive agents cannot log in.</span>
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <strong class="text-white">Credentials:</strong> <span class="tip-text">The agent will use their email and password to access their dashboard.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
    }
    .card-header {
        background: transparent;
        border-bottom: 1px solid var(--border-color);
        padding: 1rem 1.25rem;
    }
    .card-header h5, .card-header h6 {
        color: #ffffff !important;
    }
    .card-body {
        padding: 1.25rem;
    }
    .text-gold {
        color: var(--primary-gold) !important;
    }
    .form-label {
        color: var(--text-main);
        font-weight: 500;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }
    .form-control, .form-select {
        background: var(--light-dark);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        padding: 0.625rem 1rem;
    }
    .form-control:focus, .form-select:focus {
        background: var(--light-dark);
        border-color: var(--primary-gold);
        color: var(--text-main);
        box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.15);
    }
    .form-control::placeholder {
        color: var(--text-muted);
    }
    .input-group-text {
        background: var(--dark-bg);
        border: 1px solid var(--border-color);
        color: var(--text-muted);
    }
    .btn-gold {
        background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
        border: none;
        color: #000;
        font-weight: 600;
    }
    .btn-gold:hover {
        background: linear-gradient(135deg, var(--secondary-gold), var(--primary-gold));
        color: #000;
    }
    .btn-outline-gold {
        border: 1px solid var(--primary-gold);
        color: var(--primary-gold);
    }
    .btn-outline-gold:hover {
        background: var(--primary-gold);
        color: #000;
    }
    .alert-info {
        color: var(--info);
    }
    .border-info {
        border-color: rgba(59, 130, 246, 0.3) !important;
    }
    .is-invalid {
        border-color: var(--danger) !important;
    }
    .invalid-feedback {
        color: var(--danger);
    }
    .tips-list li {
        color: #e2e8f0;
        line-height: 1.6;
    }
    .tip-text {
        color: #a0aec0;
    }
    .text-white {
        color: #ffffff !important;
    }
</style>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const commissionType = document.getElementById('commissionType');
    const commissionSuffix = document.getElementById('commissionSuffix');
    const commissionInfo = document.getElementById('commissionInfo');
    const commissionValue = document.querySelector('input[name="commission_value"]');
    const generateCodeBtn = document.getElementById('generateCode');
    const referralCodeInput = document.getElementById('referralCode');
    const nameInput = document.querySelector('input[name="name"]');

    function updateCommissionInfo() {
        const type = commissionType.value;
        const value = commissionValue.value || 0;

        if (type === 'percentage') {
            commissionSuffix.textContent = '%';
            commissionInfo.textContent = `Agent will earn ${value}% of each order amount`;
        } else {
            commissionSuffix.textContent = 'AED';
            commissionInfo.textContent = `Agent will earn AED ${value} per order`;
        }
    }

    commissionType.addEventListener('change', updateCommissionInfo);
    commissionValue.addEventListener('input', updateCommissionInfo);

    // Generate referral code from name
    generateCodeBtn.addEventListener('click', function() {
        const name = nameInput.value || 'agent';
        const baseCode = name.toLowerCase().replace(/[^a-z0-9]/g, '').substring(0, 10);
        const randomSuffix = Math.random().toString(36).substring(2, 5);
        referralCodeInput.value = baseCode + randomSuffix;
    });

    updateCommissionInfo();
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Pragathi\Desktop\GoTrips-Complete\resources\views/admin/referrals/agents/create.blade.php ENDPATH**/ ?>