<form method="POST" action="<?php echo e(route('payment.initiate')); ?>">
    <?php echo csrf_field(); ?>
    <label>Amount (AED)</label>
    <input type="text" name="amount" required>
    <button type="submit">Pay Now</button>
</form>
<?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/payment_form.blade.php ENDPATH**/ ?>