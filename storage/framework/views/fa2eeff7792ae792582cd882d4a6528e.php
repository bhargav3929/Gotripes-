<?php $__env->startSection('content'); ?>
    <h2>Payment Failed or Aborted</h2>
    <p>Your payment was not successful. Please try again or contact support.</p>
    <p>Booking/Order ID: <?php echo e($booking->id); ?></p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/payment_failed.blade.php ENDPATH**/ ?>