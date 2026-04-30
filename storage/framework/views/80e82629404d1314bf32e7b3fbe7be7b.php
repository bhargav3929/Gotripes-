<?php $__env->startSection('content'); ?>
    <h2>Payment Status Unknown</h2>
    <p>The payment status could not be confirmed at this time. Please check back later.</p>
    <p>Booking/Order ID: <?php echo e($booking->id); ?></p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/payment_unknown.blade.php ENDPATH**/ ?>