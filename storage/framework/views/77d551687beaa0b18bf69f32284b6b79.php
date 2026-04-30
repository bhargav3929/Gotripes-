<?php $__env->startSection('title', 'Referral Program Settings'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-3">
    <!-- Page Header -->
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="<?php echo e(route('admin.referrals.dashboard')); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h5 class="mb-0 text-gold"><i class="fas fa-cog me-2"></i>Referral Program Settings</h5>
            <small class="text-muted">Configure commission rates and program rules</small>
        </div>
    </div>

    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show py-2 mb-3" role="alert" style="background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.3); color: #86efac; font-size: 0.8rem;">
        <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

        <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row g-3">
        <!-- Card 1: Commission Structure -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-percentage me-2 text-gold"></i>Commission Structure
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('admin.referrals.settings.update')); ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="form_section" value="commission">

                        <!-- Commission Type Toggle -->
                        <div class="mb-3">
                            <label class="form-label">Commission Type <span class="text-danger">*</span></label>
                            <div class="type-toggle-group">
                                <label class="type-option <?php echo e($settings->commission_type === 'percentage' ? 'active' : ''); ?>" id="label-percentage">
                                    <input type="radio" name="commission_type" value="percentage"
                                           <?php echo e($settings->commission_type === 'percentage' ? 'checked' : ''); ?>

                                           onchange="switchCommissionType('percentage')">
                                    <i class="fas fa-percent me-1"></i>Percentage
                                </label>
                                <label class="type-option <?php echo e($settings->commission_type === 'flat' ? 'active' : ''); ?>" id="label-flat">
                                    <input type="radio" name="commission_type" value="flat"
                                           <?php echo e($settings->commission_type === 'flat' ? 'checked' : ''); ?>

                                           onchange="switchCommissionType('flat')">
                                    <i class="fas fa-coins me-1"></i>Flat Rate
                                </label>
                            </div>
                            <?php $__errorArgs = ['commission_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block" style="font-size:0.65rem; color: var(--danger);"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Percentage Input -->
                        <div id="input-percentage" class="<?php echo e($settings->commission_type !== 'percentage' ? 'd-none' : ''); ?> mb-3">
                            <label class="form-label">Percentage Value <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="commission_percentage" step="0.01" min="0" max="100"
                                       class="form-control <?php $__errorArgs = ['commission_percentage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('commission_percentage', $settings->commission_type === 'percentage' ? $settings->commission_value : '')); ?>"
                                       placeholder="e.g. 10">
                                <span class="input-group-text">%</span>
                            </div>
                            <?php $__errorArgs = ['commission_percentage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block" style="font-size:0.65rem; color: var(--danger);"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted">Agent earns this % of each completed order value.</small>
                        </div>

                        <!-- Flat Rate Input -->
                        <div id="input-flat" class="<?php echo e($settings->commission_type !== 'flat' ? 'd-none' : ''); ?> mb-3">
                            <label class="form-label">Flat Rate per Sale <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">AED</span>
                                <input type="number" name="commission_flat" step="0.01" min="0"
                                       class="form-control <?php $__errorArgs = ['commission_flat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('commission_flat', $settings->commission_type === 'flat' ? $settings->commission_value : '')); ?>"
                                       placeholder="e.g. 25.00">
                            </div>
                            <?php $__errorArgs = ['commission_flat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block" style="font-size:0.65rem; color: var(--danger);"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted">Agent earns this fixed amount per completed order.</small>
                        </div>

                        <!-- Info Box -->
                        <div class="alert alert-info py-2 px-3 mb-3" style="font-size: 0.72rem; background: rgba(59,130,246,0.08); border-color: rgba(59,130,246,0.25); color: #93c5fd;">
                            <i class="fas fa-info-circle me-1"></i>
                            This rate applies to <strong>all new agents</strong> who sign up. Existing agents keep their current individual rates unless manually updated on their profile.
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-gold btn-sm">
                                <i class="fas fa-save me-1"></i>Save Commission Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Card 2: Program Settings -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-sliders-h me-2 text-gold"></i>Program Settings
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('admin.referrals.settings.update')); ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="form_section" value="program">

                        <!-- Auto-approve Toggle -->
                        <div class="setting-row mb-3">
                            <div class="setting-info">
                                <div class="setting-label">Auto-approve Commissions</div>
                                <div class="setting-desc">Automatically approve commissions when an order is completed, without manual review.</div>
                            </div>
                            <div class="form-check form-switch ms-3 mt-1">
                                <input class="form-check-input" type="checkbox" name="auto_approve_commissions" id="autoApprove" value="1"
                                       <?php echo e($settings->auto_approve_commissions ? 'checked' : ''); ?>>
                            </div>
                        </div>

                        <div class="divider-line mb-3"></div>

                        <!-- Minimum Withdrawal -->
                        <div class="mb-3">
                            <label class="form-label">Minimum Withdrawal Amount</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">AED</span>
                                <input type="number" name="min_withdrawal_amount" step="0.01" min="0"
                                       class="form-control <?php $__errorArgs = ['min_withdrawal_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('min_withdrawal_amount', $settings->min_withdrawal_amount ?? 50)); ?>"
                                       placeholder="e.g. 50.00">
                            </div>
                            <?php $__errorArgs = ['min_withdrawal_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block" style="font-size:0.65rem; color: var(--danger);"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted">Agents must have at least this balance to request a withdrawal.</small>
                        </div>

                        <div class="divider-line mb-3"></div>

                        <!-- Public Signup Toggle -->
                        <div class="setting-row mb-2">
                            <div class="setting-info">
                                <div class="setting-label">Enable Public Agent Signup</div>
                                <div class="setting-desc">Allow new agents to self-register via the public partner registration page.</div>
                            </div>
                            <div class="form-check form-switch ms-3 mt-1">
                                <input class="form-check-input" type="checkbox" name="enable_public_signup" id="publicSignup" value="1"
                                       <?php echo e(($settings->enable_public_signup ?? false) ? 'checked' : ''); ?>

                                       onchange="toggleSignupUrl(this)">
                            </div>
                        </div>

                        <!-- Signup URL (visible when toggle is on) -->
                        <div id="signupUrlBox" class="<?php echo e(($settings->enable_public_signup ?? false) ? '' : 'd-none'); ?> mb-3">
                            <label class="form-label">Public Signup URL</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" id="signupUrl"
                                       value="https://gotrips.ai/partner/register" readonly
                                       style="font-size: 0.7rem; color: var(--primary-gold);">
                                <button type="button" class="btn btn-outline-gold btn-sm" onclick="copySignupUrl()" title="Copy URL">
                                    <i class="fas fa-copy" id="copyIcon"></i>
                                </button>
                            </div>
                            <small class="text-muted">Share this link with potential partners to let them register directly.</small>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-gold btn-sm">
                                <i class="fas fa-save me-1"></i>Save Program Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-gold { color: var(--primary-gold) !important; }

    /* Card */
    .card { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 8px; }
    .card-header { background: transparent; border-bottom: 1px solid var(--border-color); padding: 10px 14px; font-size: 0.8rem; font-weight: 500; color: #fff; }
    .card-body { padding: 16px; }

    /* Form */
    .form-label { font-size: 0.7rem; color: var(--text-muted); margin-bottom: 3px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.02em; }
    .form-control, .form-select { background: var(--light-dark); border: 1px solid var(--border-color); color: var(--text-main); font-size: 0.75rem; }
    .form-control:focus, .form-select:focus { background: var(--light-dark); border-color: var(--primary-gold); color: var(--text-main); box-shadow: none; }
    .form-control[readonly] { opacity: 0.8; cursor: default; }
    .form-control-sm, .form-select-sm { padding: 6px 10px; }
    .input-group-text { background: var(--dark-bg); border: 1px solid var(--border-color); color: var(--text-muted); font-size: 0.7rem; }

    /* Commission Type Toggle */
    .type-toggle-group { display: flex; gap: 0; border: 1px solid var(--border-color); border-radius: 6px; overflow: hidden; }
    .type-option {
        flex: 1;
        padding: 8px 12px;
        font-size: 0.75rem;
        font-weight: 500;
        text-align: center;
        color: var(--text-muted);
        background: var(--light-dark);
        cursor: pointer;
        transition: all 150ms ease;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        margin: 0;
    }
    .type-option + .type-option { border-left: 1px solid var(--border-color); }
    .type-option.active { background: rgba(255, 215, 0, 0.12); color: var(--primary-gold); }
    .type-option input[type="radio"] { display: none; }
    .type-option:hover:not(.active) { background: rgba(255,255,255,0.04); color: #e2e8f0; }

    /* Setting Row */
    .setting-row { display: flex; align-items: flex-start; justify-content: space-between; }
    .setting-info { flex: 1; }
    .setting-label { font-size: 0.8rem; font-weight: 600; color: #e2e8f0; margin-bottom: 2px; }
    .setting-desc { font-size: 0.7rem; color: var(--text-muted); line-height: 1.4; }

    /* Divider */
    .divider-line { border-top: 1px solid var(--border-color); }

    /* Form Switch */
    .form-check-input { width: 2em; height: 1.1em; background-color: var(--border-color); border-color: var(--border-color); cursor: pointer; }
    .form-check-input:checked { background-color: var(--primary-gold); border-color: var(--primary-gold); }
    .form-check-input:focus { box-shadow: none; }

    /* Buttons */
    .btn-gold { background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold)); border: none; color: #000; font-weight: 600; }
    .btn-gold:hover { background: linear-gradient(135deg, var(--secondary-gold), var(--primary-gold)); color: #000; }
    .btn-outline-gold { border: 1px solid var(--primary-gold); color: var(--primary-gold); background: transparent; }
    .btn-outline-gold:hover { background: var(--primary-gold); color: #000; }
    .btn-sm { padding: 6px 12px; font-size: 0.75rem; }

    /* Alert */
    .alert-info { background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #93c5fd; }

    /* Text */
    h5 { color: #fff !important; }
    small { font-size: 0.65rem; }
</style>

<?php $__env->startPush('scripts'); ?>
<script>
function switchCommissionType(type) {
    document.getElementById('input-percentage').classList.toggle('d-none', type !== 'percentage');
    document.getElementById('input-flat').classList.toggle('d-none', type !== 'flat');

    document.getElementById('label-percentage').classList.toggle('active', type === 'percentage');
    document.getElementById('label-flat').classList.toggle('active', type === 'flat');
}

function toggleSignupUrl(checkbox) {
    document.getElementById('signupUrlBox').classList.toggle('d-none', !checkbox.checked);
}

function copySignupUrl() {
    const url = document.getElementById('signupUrl').value;
    navigator.clipboard.writeText(url).then(function() {
        const icon = document.getElementById('copyIcon');
        icon.classList.remove('fa-copy');
        icon.classList.add('fa-check');
        setTimeout(() => {
            icon.classList.remove('fa-check');
            icon.classList.add('fa-copy');
        }, 1500);
    });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/admin/referrals/settings.blade.php ENDPATH**/ ?>