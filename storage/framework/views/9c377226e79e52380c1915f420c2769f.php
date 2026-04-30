<?php $__env->startSection('title', 'Edit Referral Agent'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="<?php echo e(route('admin.referrals.agents.index')); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h5 class="mb-0 text-gold"><i class="fas fa-user-edit me-2"></i>Edit Agent</h5>
            <small class="text-muted"><?php echo e($agent->name); ?></small>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <form method="POST" action="<?php echo e(route('admin.referrals.agents.update', $agent)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <!-- Basic Info -->
                <div class="card mb-3">
                    <div class="card-header"><i class="fas fa-user me-2 text-gold"></i>Basic Information</div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-sm <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('name', $agent->name)); ?>" required>
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
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control form-control-sm <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('email', $agent->email)); ?>" required>
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
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control form-control-sm <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('phone', $agent->phone)); ?>">
                                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Country</label>
                                <input type="text" name="country" list="agent-country-options"
                                       class="form-control form-control-sm <?php $__errorArgs = ['country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('country', $agent->country)); ?>" placeholder="Start typing">
                                <datalist id="agent-country-options"></datalist>
                                <?php $__errorArgs = ['country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select form-select-sm <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="active" <?php echo e(old('status', $agent->status) == 'active' ? 'selected' : ''); ?>>Active</option>
                                    <option value="inactive" <?php echo e(old('status', $agent->status) == 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                                    <option value="suspended" <?php echo e(old('status', $agent->status) == 'suspended' ? 'selected' : ''); ?>>Suspended</option>
                                </select>
                                <?php $__errorArgs = ['status'];
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

                <!-- Password -->
                <div class="card mb-3">
                    <div class="card-header"><i class="fas fa-lock me-2 text-gold"></i>Change Password</div>
                    <div class="card-body">
                        <div class="alert alert-warning py-2 mb-2">
                            <small><i class="fas fa-info-circle me-1"></i>Leave blank to keep current password</small>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">New Password</label>
                                <input type="password" name="password" class="form-control form-control-sm <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       placeholder="Min 8 characters">
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control form-control-sm"
                                       placeholder="Confirm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Referral Settings -->
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-link me-2 text-gold"></i>Referral Settings</span>
                        <button type="button" class="btn btn-xs btn-outline-gold" id="regenerateCode">
                            <i class="fas fa-sync-alt me-1"></i>Regenerate
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label">Referral Code</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"><?php echo e(url('/')); ?>/?ref=</span>
                                    <input type="text" name="referral_code" id="referralCode"
                                           class="form-control <?php $__errorArgs = ['referral_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           value="<?php echo e(old('referral_code', $agent->referral_code)); ?>">
                                    <button type="button" class="btn btn-outline-info copy-btn" data-copy="<?php echo e($agent->referral_url); ?>">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                <?php $__errorArgs = ['referral_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Full URL</label>
                                <input type="text" class="form-control form-control-sm" value="<?php echo e($agent->referral_url); ?>" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Commission -->
                <div class="card mb-3">
                    <div class="card-header"><i class="fas fa-percentage me-2 text-gold"></i>Commission Settings</div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Type <span class="text-danger">*</span></label>
                                <select name="commission_type" id="commissionType" class="form-select form-select-sm <?php $__errorArgs = ['commission_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="percentage" <?php echo e(old('commission_type', $agent->commission_type) == 'percentage' ? 'selected' : ''); ?>>Percentage (%)</option>
                                    <option value="fixed" <?php echo e(old('commission_type', $agent->commission_type) == 'fixed' ? 'selected' : ''); ?>>Fixed (AED)</option>
                                </select>
                                <?php $__errorArgs = ['commission_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Value <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="commission_value" step="0.01" min="0"
                                           class="form-control <?php $__errorArgs = ['commission_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           value="<?php echo e(old('commission_value', $agent->commission_value)); ?>" required>
                                    <span class="input-group-text" id="commissionSuffix"><?php echo e($agent->commission_type === 'percentage' ? '%' : 'AED'); ?></span>
                                </div>
                                <?php $__errorArgs = ['commission_value'];
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

                <!-- Notes -->
                <div class="card mb-3">
                    <div class="card-header"><i class="fas fa-sticky-note me-2 text-gold"></i>Notes</div>
                    <div class="card-body">
                        <textarea name="notes" class="form-control form-control-sm <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                  rows="2"><?php echo e(old('notes', $agent->notes)); ?></textarea>
                        <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                    <div class="d-flex gap-2">
                        <a href="<?php echo e(route('admin.referrals.agents.index')); ?>" class="btn btn-outline-secondary btn-sm">Cancel</a>
                        <button type="submit" class="btn btn-gold btn-sm"><i class="fas fa-save me-1"></i>Update</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Stats Sidebar -->
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header"><i class="fas fa-chart-bar me-2 text-gold"></i>Statistics</div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="stat-box gold">
                                <div class="stat-value"><?php echo e(number_format($agent->total_clicks)); ?></div>
                                <small class="text-muted">Clicks</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box info">
                                <div class="stat-value"><?php echo e(number_format($agent->total_sales)); ?></div>
                                <small class="text-muted">Sales</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box success">
                                <div class="stat-value"><?php echo e(number_format($agent->total_earnings, 0)); ?></div>
                                <small class="text-muted">Earned (AED)</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box warning">
                                <div class="stat-value"><?php echo e(number_format($agent->pending_earnings, 0)); ?></div>
                                <small class="text-muted">Pending (AED)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><i class="fas fa-info-circle me-2 text-gold"></i>Account Info</div>
                <div class="card-body">
                    <div class="info-row"><span>Created</span><span><?php echo e($agent->created_at->format('M d, Y')); ?></span></div>
                    <div class="info-row"><span>Last Login</span><span><?php echo e($agent->last_login_at ? $agent->last_login_at->format('M d, Y H:i') : 'Never'); ?></span></div>
                    <div class="info-row"><span>Conversion</span><span class="text-success"><?php echo e($agent->conversion_rate); ?>%</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content bg-dark">
            <div class="modal-header border-dark py-2">
                <h6 class="modal-title text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Delete Agent</h6>
                <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2">
                <p class="small mb-0">Delete <strong><?php echo e($agent->name); ?></strong>? All referral data will be lost.</p>
            </div>
            <div class="modal-footer border-dark py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="<?php echo e(route('admin.referrals.agents.destroy', $agent)); ?>">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .text-gold { color: var(--primary-gold) !important; }

    /* Card */
    .card { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 8px; }
    .card-header { background: transparent; border-bottom: 1px solid var(--border-color); padding: 10px 14px; font-size: 0.8rem; font-weight: 500; color: #fff; }
    .card-body { padding: 14px; }

    /* Form */
    .form-label { font-size: 0.7rem; color: var(--text-muted); margin-bottom: 2px; font-weight: 500; }
    .form-control, .form-select { background: var(--light-dark); border: 1px solid var(--border-color); color: var(--text-main); font-size: 0.75rem; }
    .form-control:focus, .form-select:focus { background: var(--light-dark); border-color: var(--primary-gold); color: var(--text-main); box-shadow: none; }
    .form-control-sm, .form-select-sm { padding: 6px 10px; }
    .form-control[readonly] { opacity: 0.7; }
    .input-group-text { background: var(--dark-bg); border: 1px solid var(--border-color); color: var(--text-muted); font-size: 0.7rem; }

    /* Buttons */
    .btn-gold { background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold)); border: none; color: #000; font-weight: 600; }
    .btn-outline-gold { border: 1px solid var(--primary-gold); color: var(--primary-gold); }
    .btn-outline-gold:hover { background: var(--primary-gold); color: #000; }
    .btn-sm { padding: 6px 12px; font-size: 0.75rem; }
    .btn-xs { padding: 3px 8px; font-size: 0.65rem; }

    /* Stat Box */
    .stat-box { text-align: center; padding: 10px; border-radius: 6px; }
    .stat-box.gold { background: rgba(255, 215, 0, 0.1); }
    .stat-box.gold .stat-value { color: var(--primary-gold); }
    .stat-box.info { background: rgba(59, 130, 246, 0.1); }
    .stat-box.info .stat-value { color: var(--info); }
    .stat-box.success { background: rgba(34, 197, 94, 0.1); }
    .stat-box.success .stat-value { color: var(--success); }
    .stat-box.warning { background: rgba(245, 158, 11, 0.1); }
    .stat-box.warning .stat-value { color: var(--warning); }
    .stat-value { font-size: 1rem; font-weight: 700; }

    /* Info Row */
    .info-row { display: flex; justify-content: space-between; font-size: 0.75rem; padding: 4px 0; }
    .info-row span:first-child { color: var(--text-muted); }

    /* Alert */
    .alert-warning { background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); color: var(--warning); }

    /* Modal */
    .modal-content { border: 1px solid var(--border-color); }

    /* Text */
    h5, h6 { color: #fff !important; }
    small { font-size: 0.65rem; }
    .is-invalid { border-color: var(--danger) !important; }
    .invalid-feedback { color: var(--danger); font-size: 0.65rem; }
</style>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const commissionType = document.getElementById('commissionType');
    const commissionSuffix = document.getElementById('commissionSuffix');

    commissionType.addEventListener('change', function() {
        commissionSuffix.textContent = this.value === 'percentage' ? '%' : 'AED';
    });

    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            navigator.clipboard.writeText(this.dataset.copy).then(() => {
                const icon = this.querySelector('i');
                icon.className = 'fas fa-check';
                setTimeout(() => icon.className = 'fas fa-copy', 2000);
            });
        });
    });

    const countryDatalist = document.getElementById('agent-country-options');
    if (countryDatalist) {
        fetch('https://restcountries.com/v3.1/all?fields=name')
            .then(r => r.ok ? r.json() : Promise.reject())
            .then(data => {
                data.map(c => c.name?.common).filter(Boolean)
                    .sort((a, b) => a.localeCompare(b))
                    .forEach(n => {
                        const o = document.createElement('option');
                        o.value = n;
                        countryDatalist.appendChild(o);
                    });
            })
            .catch(() => {});
    }

    document.getElementById('regenerateCode').addEventListener('click', function() {
        if (!confirm('Regenerate code? Old link will stop working.')) return;
        fetch('<?php echo e(route("admin.referrals.agents.regenerate-code", $agent)); ?>', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('referralCode').value = data.referral_code;
                document.querySelector('.copy-btn').dataset.copy = data.referral_url;
                document.querySelector('input[readonly]').value = data.referral_url;
            }
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/admin/referrals/agents/edit.blade.php ENDPATH**/ ?>