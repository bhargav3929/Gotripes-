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
                    <a href="<?php echo e(route('admin.tols.index')); ?>" class="btn btn-primary btn-sm shadow-sm"><?php echo e(__('Go Back')); ?></a>
                </div>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('admin.tols.update', $tol->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('put'); ?>
                    
                    <div class="form-group row">
    <label for="tolName" class="col-sm-2 col-form-label"><strong><?php echo e(__('Loan Name')); ?> :</strong></label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="tolName"  name="tolName"  value="<?php echo e(old('name', $tol->tolName)); ?>" />
    </div>
</div>
                 
                    <div class="form-group row">
                        <label for="comment" class="col-sm-2 col-form-label"><strong><?php echo e(__('Comment')); ?> :</strong></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="comment"  name="comment" value="<?php echo e(old('name', $tol->comment)); ?>" />
                    </div>
                 </div>
               


               
                
                    <button type="submit" class="btn btn-primary btn-block"><?php echo e(__('Save')); ?></button>
                </form>
            </div>
        </div>
    

    <!-- Content Row -->

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/admin/typeofloans/edit.blade.php ENDPATH**/ ?>