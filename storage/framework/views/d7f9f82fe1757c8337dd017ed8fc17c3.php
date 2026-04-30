<?php $__env->startSection('content'); ?>
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
                    <a href="<?php echo e(route('admin.referrers.index')); ?>" class="btn btn-primary btn-sm shadow-sm"><?php echo e(__('Go Back')); ?></a>
                </div>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('admin.referrers.update', $referrer->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('put'); ?>
                    
                    <div class="form-group row">
    <label for="referrerName" class="col-sm-2 col-form-label"><strong><?php echo e(__('Name')); ?> :</strong></label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="referrerName"  name="referrerName"  value="<?php echo e(old('name', $referrer->referrerName)); ?>" />
    </div>
</div>
                 
                <div class="form-group row">
                        <label for="referrerEmail" class="col-sm-2 col-form-label"><strong><?php echo e(__('Email')); ?> :</strong></label>
                        <div class="col-sm-10">
                        <input type="email" class="form-control" id="referrerEmail"  name="referrerEmail" value="<?php echo e(old('name', $referrer->referrerEmail)); ?>" />
                    </div>
                 </div>
                  <div class="form-group row">
                        <label for="referrerMobile" class="col-sm-2 col-form-label"><strong><?php echo e(__('Mobile')); ?> :</strong></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="referrerMobile"  name="referrerMobile" value="<?php echo e(old('name', $referrer->referrerMobile)); ?>" />
                    </div>
                 </div>
               


               
                
                    <button type="submit" class="btn btn-primary btn-block"><?php echo e(__('Save')); ?></button>
                </form>
            </div>
        </div>
    

    <!-- Content Row -->

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/admin/referrers/edit.blade.php ENDPATH**/ ?>