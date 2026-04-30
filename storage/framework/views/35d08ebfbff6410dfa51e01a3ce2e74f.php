<?php $__env->startSection('content'); ?>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<div class="container-fluid">

    <!-- Page Heading -->
    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

<!-- Content Row -->
        <div class="card shadow">
            <div class="card-header">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800"><?php echo e(__('edit')); ?></h1>
                    <a href="<?php echo e(route('admin.tolvals.index')); ?>" class="btn btn-primary btn-sm shadow-sm"><?php echo e(__('Go Back')); ?></a>
                </div>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('admin.tolvals.update', $tolval->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('put'); ?>
                    
                    <div class="form-group row">
    <label for="name" class="col-sm-2 col-form-label"><strong><?php echo e(__('Validation Name')); ?> :</strong></label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="name"  name="name"  value="<?php echo e(old('name', $tolval->name)); ?>" />
    </div>
</div>
                 
                    <div class="form-group row">
                        <label for="comment" class="col-sm-2 col-form-label"><strong><?php echo e(__('Comment')); ?> :</strong></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="comment"  name="comment" value="<?php echo e(old('name', $tolval->comment)); ?>" />
                    </div>
                 </div>
    <div class="form-group row">
        <label for="comment" class="col-sm-2 col-form-label mt-3"><strong><?php echo e(__('Messages')); ?> :</strong></label>
        
        <!-- SMS Toggle -->
        <div class="form-check form-switch col-sm-3 mt-4">
            <input class="form-check-input" type="checkbox" id="smsSwitch" name="sms" value="1" <?php echo e(old('sms', $tolval->sms) == '1' ? 'checked' : ''); ?>>
            <label class="form-check-label" for="smsSwitch"><?php echo e(__('Send SMS')); ?></label>
        </div>
        
        <!-- Email Toggle -->
        <div class="form-check form-switch col-sm-6 mt-4">
            <input class="form-check-input" type="checkbox" id="emailSwitch" name="email" value="1" <?php echo e(old('email', $tolval->email) == '1' ? 'checked' : ''); ?>>
            <label class="form-check-label" for="emailSwitch"><?php echo e(__('Send Email')); ?></label>
        </div>
    </div>


               
                
                    <button type="submit" class="btn btn-primary btn-block"><?php echo e(__('Save')); ?></button>
                </form>
            </div>
        </div>
    

    <!-- Content Row -->

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/admin/transactions/edit.blade.php ENDPATH**/ ?>