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
                    <a href="<?php echo e(route('admin.banners.index')); ?>" class="btn btn-primary btn-sm shadow-sm"><?php echo e(__('Go Back')); ?></a>
                </div>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('admin.banners.update', $banner->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('put'); ?>
                    
                    <div class="form-group row">
    <label for="bannerHC" class="col-sm-2 col-form-label"><strong><?php echo e(__('bannerHC')); ?> :</strong></label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="bannerHC"  name="bannerHC"  value="<?php echo e(old('name', $banner->bannerHC)); ?>" />
    </div>
</div>
                 
                    <div class="form-group row">
                        <label for="bannerBC" class="col-sm-2 col-form-label"><strong><?php echo e(__('Body Content')); ?> :</strong></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="bannerBC"  name="bannerBC" value="<?php echo e(old('name', $banner->bannerBC)); ?>" />
                    </div>
                 </div>
               


               
                
                    <button type="submit" class="btn btn-primary btn-block"><?php echo e(__('Save')); ?></button>
                </form>
            </div>
        </div>
    

    <!-- Content Row -->

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/admin/banners/edit.blade.php ENDPATH**/ ?>