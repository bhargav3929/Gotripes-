<?php $__env->startSection('title', 'Feature Settings'); ?>
<?php $__env->startSection('page-title', 'Feature Settings'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .features-card {
        background: #1a1a1a;
        border: 1px solid rgba(255, 215, 0, 0.18);
        border-radius: 12px;
        padding: 28px 32px;
        max-width: 880px;
    }
    .features-card h2 {
        font-size: 18px; font-weight: 600;
        color: #fff;
        margin: 0 0 6px;
    }
    .features-card .lede {
        color: #c0c0c0; font-size: 13px; margin: 0 0 20px;
        line-height: 1.55;
    }
    .features-tenant {
        display: inline-flex; align-items: center; gap: 8px;
        background: rgba(255, 215, 0, 0.08);
        border: 1px solid rgba(255, 215, 0, 0.25);
        color: #FFD700;
        padding: 6px 12px;
        border-radius: 99px;
        font-size: 12px; font-weight: 600;
        margin-bottom: 20px;
    }
    .features-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin: 20px 0 24px;
    }
    .feature-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 16px;
        background: #232323;
        border: 1px solid rgba(255, 215, 0, 0.10);
        border-radius: 10px;
        transition: all 0.15s;
    }
    .feature-row:hover { border-color: rgba(255, 215, 0, 0.28); background: #262626; }
    .feature-row label {
        margin: 0; flex: 1; cursor: pointer;
        color: #f0f0f0; font-size: 14px; font-weight: 500;
    }
    .feature-row .feat-key { display:block; color:#888; font-size:11px; font-weight:400; margin-top:2px; letter-spacing:0.5px; }
    .toggle {
        position: relative; width: 42px; height: 24px;
        background: #444; border-radius: 99px; cursor: pointer;
        transition: background 0.2s;
    }
    .toggle::before {
        content: ''; position: absolute; top: 2px; left: 2px;
        width: 20px; height: 20px; border-radius: 50%;
        background: #fff; transition: left 0.2s;
    }
    input[type="checkbox"]:checked + .toggle { background: #FFD700; }
    input[type="checkbox"]:checked + .toggle::before { left: 20px; }
    .feature-row input[type="checkbox"] { display: none; }
    .save-btn {
        background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        color: #000; border: none;
        font-weight: 600; font-size: 14px;
        padding: 11px 28px;
        border-radius: 8px; cursor: pointer;
        transition: all 0.2s;
    }
    .save-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(255, 215, 0, 0.25); }
    .alert-ok { background: rgba(0, 163, 42, 0.15); border: 1px solid rgba(0, 163, 42, 0.4); color: #4ade80; padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
    .alert-err { background: rgba(214, 54, 56, 0.15); border: 1px solid rgba(214, 54, 56, 0.4); color: #f87171; padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
    @media (max-width: 640px) { .features-grid { grid-template-columns: 1fr; } }
</style>

<div class="features-card">
    <?php if(session('success')): ?><div class="alert-ok"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if(session('error')): ?><div class="alert-err"><?php echo e(session('error')); ?></div><?php endif; ?>

    <?php if($company): ?>
        <div class="features-tenant">
            <i class="fas fa-building"></i>
            <?php echo e($company->name); ?> <span style="opacity:0.6;">·</span> <?php echo e($company->subdomain); ?>.gotrips.ai
        </div>
    <?php else: ?>
        <div class="alert-err">
            No tenant company is bound to this session. Feature toggles can only be edited from a tenant subdomain (e.g. <code>bhargav.gotrips.ai/manager</code>).
        </div>
    <?php endif; ?>

    <h2>Enabled Services</h2>
    <p class="lede">Toggle which services this partner can sell from their site. Disabled services return a 404 to visitors and are hidden from menus.</p>

    <form method="POST" action="<?php echo e(route('manager.settings.features.update')); ?>">
        <?php echo csrf_field(); ?>
        <div class="features-grid">
            <?php $__currentLoopData = $allFeatures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="feature-row">
                    <label for="feat-<?php echo e($key); ?>">
                        <?php echo e($label); ?>

                        <span class="feat-key"><?php echo e($key); ?></span>
                    </label>
                    <input type="checkbox" id="feat-<?php echo e($key); ?>" name="features[]" value="<?php echo e($key); ?>" <?php echo e(in_array($key, $enabled, true) ? 'checked' : ''); ?>>
                    <label for="feat-<?php echo e($key); ?>" class="toggle"></label>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <button type="submit" class="save-btn" <?php echo e($company ? '' : 'disabled'); ?>>
            <i class="fas fa-check" style="margin-right:6px;"></i>Save Settings
        </button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.manager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/manager/settings/features.blade.php ENDPATH**/ ?>