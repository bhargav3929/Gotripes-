<?php $__env->startSection('title', 'Withdrawal Requests'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-3">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo e(route('admin.referrals.dashboard')); ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h5 class="mb-0 text-gold">
                    <i class="fas fa-money-bill-wave me-2"></i>Withdrawal Requests
                    <?php if(($stats['pending_count'] ?? 0) > 0): ?>
                    <span class="badge bg-warning ms-1" style="font-size: 0.65rem; padding: 3px 7px; vertical-align: middle;">
                        <?php echo e($stats['pending_count']); ?> pending
                    </span>
                    <?php endif; ?>
                </h5>
                <small class="text-muted">Review and process agent withdrawal requests</small>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show py-2 mb-3" role="alert" style="background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.3); color: #86efac; font-size: 0.8rem;">
        <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

        <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show py-2 mb-3" role="alert" style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; font-size: 0.8rem;">
        <i class="fas fa-exclamation-circle me-2"></i><?php echo e(session('error')); ?>

        <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Stats Row -->
    <div class="row g-2 mb-3">
        <div class="col-6 col-lg-3">
            <div class="stat-box stat-box-warning">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="stat-dot warning"></span>
                    <small class="text-muted">Pending</small>
                </div>
                <div class="stat-value text-warning"><?php echo e($stats['pending_count'] ?? 0); ?></div>
                <div class="stat-sub">AED <?php echo e(number_format($pendingTotal ?? 0, 2)); ?></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-box stat-box-info">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="stat-dot info"></span>
                    <small class="text-muted">Processing</small>
                </div>
                <div class="stat-value text-info"><?php echo e($stats['processing_count'] ?? 0); ?></div>
                <div class="stat-sub">AED <?php echo e(number_format($processingTotal ?? 0, 2)); ?></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-box stat-box-success">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="stat-dot success"></span>
                    <small class="text-muted">Completed</small>
                </div>
                <div class="stat-value text-success"><?php echo e($stats['completed_count'] ?? 0); ?></div>
                <div class="stat-sub">AED <?php echo e(number_format($completedTotal ?? 0, 2)); ?></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-box stat-box-danger">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="stat-dot danger"></span>
                    <small class="text-muted">Rejected</small>
                </div>
                <div class="stat-value text-danger"><?php echo e($stats['rejected_count'] ?? 0); ?></div>
                <div class="stat-sub">all time</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="<?php echo e(route('admin.referrals.withdrawals.index')); ?>" class="row g-2 align-items-end">
                <div class="col-6 col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                        <option value="processing" <?php echo e(request('status') == 'processing' ? 'selected' : ''); ?>>Processing</option>
                        <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>Completed</option>
                        <option value="rejected" <?php echo e(request('status') == 'rejected' ? 'selected' : ''); ?>>Rejected</option>
                    </select>
                </div>
                <div class="col-6 col-md-5">
                    <label class="form-label">Search Agent</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                           value="<?php echo e(request('search')); ?>" placeholder="Agent name or email...">
                </div>
                <div class="col-12 col-md-4 d-flex gap-1">
                    <button type="submit" class="btn btn-gold btn-sm flex-grow-1">
                        <i class="fas fa-search me-1"></i>Filter
                    </button>
                    <a href="<?php echo e(route('admin.referrals.withdrawals.index')); ?>" class="btn btn-outline-secondary btn-sm" title="Clear filters">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Withdrawals Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Agent</th>
                        <th class="text-end">Amount</th>
                        <th>Bank Details</th>
                        <th class="text-center">Status</th>
                        <th>Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $withdrawals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $withdrawal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $bank = is_array($withdrawal->bank_snapshot) ? $withdrawal->bank_snapshot : json_decode($withdrawal->bank_snapshot, true);
                        $acctNum = $bank['account_number'] ?? '';
                        $maskedAcct = strlen($acctNum) > 4 ? '•••• ' . substr($acctNum, -4) : ($acctNum ?: '—');
                    ?>
                    <tr>
                        <td>
                            <div class="agent-name-text"><?php echo e($withdrawal->referralAgent->name ?? 'N/A'); ?></div>
                            <small class="text-muted"><?php echo e($withdrawal->referralAgent->email ?? ''); ?></small>
                        </td>
                        <td class="text-end">
                            <span class="withdrawal-amount">AED <?php echo e(number_format($withdrawal->amount, 2)); ?></span>
                        </td>
                        <td>
                            <div class="bank-name"><?php echo e($bank['bank_name'] ?? '—'); ?></div>
                            <small class="text-muted">
                                <?php echo e($bank['account_holder_name'] ?? ''); ?>

                                <?php if($maskedAcct !== '—'): ?>
                                · <?php echo e($maskedAcct); ?>

                                <?php endif; ?>
                            </small>
                        </td>
                        <td class="text-center">
                            <?php
                                $statusBadge = match($withdrawal->status) {
                                    'pending'    => 'warning',
                                    'processing' => 'info',
                                    'completed'  => 'success',
                                    'rejected'   => 'danger',
                                    default      => 'secondary',
                                };
                            ?>
                            <span class="badge bg-<?php echo e($statusBadge); ?>"><?php echo e(ucfirst($withdrawal->status)); ?></span>
                        </td>
                        <td>
                            <div><?php echo e($withdrawal->created_at->format('M d, Y')); ?></div>
                            <small class="text-muted"><?php echo e($withdrawal->created_at->format('H:i')); ?></small>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-1">
                                <?php if($withdrawal->status === 'pending'): ?>
                                    <!-- Mark Processing -->
                                    <form method="POST" action="<?php echo e(route('admin.referrals.withdrawals.approve', $withdrawal)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-xs btn-info" title="Mark as Processing">
                                            <i class="fas fa-spinner me-1"></i>Processing
                                        </button>
                                    </form>
                                    <!-- Reject -->
                                    <button type="button" class="btn btn-xs btn-danger reject-trigger"
                                            data-withdrawal-id="<?php echo e($withdrawal->id); ?>"
                                            title="Reject">
                                        <i class="fas fa-times"></i>
                                    </button>

                                <?php elseif($withdrawal->status === 'processing'): ?>
                                    <!-- Mark Complete -->
                                    <form method="POST" action="<?php echo e(route('admin.referrals.withdrawals.complete', $withdrawal)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-xs btn-success" title="Mark as Complete">
                                            <i class="fas fa-check me-1"></i>Complete
                                        </button>
                                    </form>
                                    <!-- Reject -->
                                    <button type="button" class="btn btn-xs btn-danger reject-trigger"
                                            data-withdrawal-id="<?php echo e($withdrawal->id); ?>"
                                            title="Reject">
                                        <i class="fas fa-times"></i>
                                    </button>

                                <?php elseif($withdrawal->status === 'completed'): ?>
                                    <span class="text-success" title="Completed"><i class="fas fa-check-circle"></i></span>

                                <?php elseif($withdrawal->status === 'rejected'): ?>
                                    <span class="text-danger" title="<?php echo e($withdrawal->admin_notes ?? 'Rejected'); ?>">
                                        <i class="fas fa-times-circle"></i>
                                    </span>
                                    <?php if($withdrawal->admin_notes): ?>
                                    <small class="text-muted" style="max-width: 120px; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo e($withdrawal->admin_notes); ?>"><?php echo e($withdrawal->admin_notes); ?></small>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6">
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-2x text-muted mb-2 d-block"></i>
                                <div class="text-muted" style="font-size: 0.8rem;">No withdrawal requests found</div>
                                <?php if(request()->hasAny(['status','search'])): ?>
                                <a href="<?php echo e(route('admin.referrals.withdrawals.index')); ?>" class="btn btn-sm btn-outline-secondary mt-2" style="font-size: 0.7rem;">Clear filters</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($withdrawals->hasPages()): ?>
        <div class="card-footer border-top py-2">
            <?php echo e($withdrawals->withQueryString()->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="modal-header py-2" style="border-bottom: 1px solid var(--border-color);">
                <h6 class="modal-title text-danger mb-0">
                    <i class="fas fa-times-circle me-2"></i>Reject Withdrawal
                </h6>
                <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="rejectForm" action="">
                <?php echo csrf_field(); ?>
                <div class="modal-body py-3">
                    <label class="form-label">Reason / Admin Notes <span class="text-danger">*</span></label>
                    <textarea name="admin_notes" class="form-control form-control-sm" rows="3"
                              required placeholder="Briefly explain why this request is being rejected..."></textarea>
                    <small class="text-muted mt-1 d-block">The agent will see this note in their dashboard.</small>
                </div>
                <div class="modal-footer py-2" style="border-top: 1px solid var(--border-color);">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-times me-1"></i>Reject Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .text-gold { color: var(--primary-gold) !important; }

    /* Stat Box */
    .stat-box {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 12px 14px;
    }
    .stat-box.stat-box-warning { border-left: 3px solid var(--warning); }
    .stat-box.stat-box-info    { border-left: 3px solid var(--info); }
    .stat-box.stat-box-success { border-left: 3px solid var(--success); }
    .stat-box.stat-box-danger  { border-left: 3px solid var(--danger); }
    .stat-value { font-size: 1.2rem; font-weight: 700; line-height: 1; margin-bottom: 2px; }
    .stat-sub   { font-size: 0.65rem; color: var(--text-muted); }
    .stat-dot   { width: 6px; height: 6px; border-radius: 50%; display: inline-block; flex-shrink: 0; }
    .stat-dot.warning { background: var(--warning); }
    .stat-dot.info    { background: var(--info); }
    .stat-dot.success { background: var(--success); }
    .stat-dot.danger  { background: var(--danger); }

    /* Card */
    .card { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 8px; }
    .card-body { padding: 12px; }
    .card-footer { background: transparent; }

    /* Form */
    .form-label { font-size: 0.65rem; color: var(--text-muted); margin-bottom: 2px; }
    .form-control, .form-select { background: var(--light-dark); border: 1px solid var(--border-color); color: var(--text-main); font-size: 0.75rem; }
    .form-control:focus, .form-select:focus { background: var(--light-dark); border-color: var(--primary-gold); color: var(--text-main); box-shadow: none; }
    .form-control-sm, .form-select-sm { padding: 4px 8px; }

    /* Table */
    .table { font-size: 0.75rem; margin: 0; background: transparent !important; }
    .table thead th {
        background: rgba(0,0,0,0.2) !important;
        color: var(--text-muted);
        font-size: 0.65rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 8px 12px;
        border-bottom: 1px solid var(--border-color);
    }
    .table tbody td {
        padding: 10px 12px;
        border-bottom: 1px solid var(--border-color);
        background: transparent !important;
        color: #e2e8f0;
        vertical-align: middle;
    }
    .table tbody tr:last-child td { border-bottom: none; }
    .table tbody tr:hover { background: rgba(255,215,0,0.03) !important; }

    .agent-name-text { font-weight: 500; color: #fff; }
    .bank-name { font-weight: 500; color: #e2e8f0; }
    .withdrawal-amount { font-weight: 700; color: var(--primary-gold); font-size: 0.85rem; }

    /* Buttons */
    .btn-gold { background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold)); border: none; color: #000; font-weight: 600; }
    .btn-gold:hover { background: linear-gradient(135deg, var(--secondary-gold), var(--primary-gold)); color: #000; }
    .btn-sm { padding: 4px 10px; font-size: 0.75rem; }
    .btn-xs { padding: 3px 7px; font-size: 0.65rem; border-radius: 4px; }

    /* Badge */
    .badge { font-size: 0.6rem; padding: 3px 7px; }

    /* Modal */
    .modal-title { font-size: 0.85rem; }

    /* Text */
    h5 { color: #fff !important; }
    small { font-size: 0.65rem; }
</style>

<?php $__env->startPush('scripts'); ?>
<script>
document.querySelectorAll('.reject-trigger').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const withdrawalId = this.dataset.withdrawalId;
        const form = document.getElementById('rejectForm');
        form.action = `/admin/referrals/withdrawals/${withdrawalId}/reject`;
        // Clear previous notes
        form.querySelector('textarea[name="admin_notes"]').value = '';
        new bootstrap.Modal(document.getElementById('rejectModal')).show();
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/admin/referrals/withdrawals/index.blade.php ENDPATH**/ ?>