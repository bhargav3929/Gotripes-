<form method="post" action="<?php echo e($ccavenue_url ?? config('services.ccavenue.url')); ?>" name="redirect">
    <input type="hidden" name="encRequest" value="<?php echo e($encRequest); ?>">
    <input type="hidden" name="access_code" value="<?php echo e($accessCode); ?>">
    <noscript>
        <input type="submit" value="Click here if you are not redirected"/>
    </noscript>
</form>
<script>document.redirect.submit();</script>
<?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/ccavenue_redirect.blade.php ENDPATH**/ ?>