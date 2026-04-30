<?php $__env->startSection('content'); ?>
    <h2>Payment Cancelled</h2>
    <p>You have cancelled the payment process. If this was a mistake, please try booking again.</p>
    <a href="<?php echo e(url('/')); ?>">Return to Home</a>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/payment_cancel.blade.php ENDPATH**/ ?>