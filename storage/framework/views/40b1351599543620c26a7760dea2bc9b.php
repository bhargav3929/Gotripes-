<?php $__env->startSection('content'); ?>
    <h2>Payment Successful</h2>
    <p>Thank you for your payment. Your booking/order ID is: <?php echo e($booking->id); ?></p>
    <p>Status: <?php echo e($booking->payment_status); ?></p>
    <!-- Add more booking/payment details here -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/payment_success.blade.php ENDPATH**/ ?>