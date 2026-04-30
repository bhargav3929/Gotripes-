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
                <div class="d-sm-flex align-items-center justify-content-between ">
                    <h1 class="h3 mb-0 text-gray-800"><?php echo e(__('Create Referrer')); ?></h1>
                    <a href="<?php echo e(route('admin.referrers.index')); ?>" class="btn btn-primary btn-sm shadow-sm"><?php echo e(__('Go Back')); ?></a>
                </div>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('admin.referrers.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
             <div class="form-group row">
    <label for="referrerName" class="col-sm-2 col-form-label"><strong><?php echo e(__('Name')); ?> :</strong></label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="referrerName"  name="referrerName" />
    </div>
</div> 

                 <div class="form-group row">
                        <label for="referrerEmail" class="col-sm-2 col-form-label"><strong><?php echo e(__('Email')); ?> :</strong></label>
                        <div class="col-sm-10">
                        <input type="email" class="form-control" id="referrerEmail"  name="referrerEmail"  />
                    </div>
                 </div>
                  <div class="form-group row">
                        <label for="referrerMobile" class="col-sm-2 col-form-label"><strong><?php echo e(__('Mobile')); ?> :</strong></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="referrerMobile"  name="referrerMobile"  />
                    </div>
                 </div>
                 
                 
                    <button type="submit" class="btn btn-primary btn-block"><?php echo e(__('Save')); ?></button>
                </form>
            </div>
        </div>
    

    <!-- Content Row -->

</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('style-alt'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script-alt'); ?>
<script src="https://cdn.datatables.net/select/1.2.0/js/dataTables.select.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script>
        $('.datetimepicker').datetimepicker({
             format: 'YYYY-MM-DD HH:mm',
            //format: 'YYYY-MM-DD',
            locale: 'en',
            sideBySide: true,
            icons: {
            up: 'fas fa-chevron-up',
            down: 'fas fa-chevron-down',
            previous: 'fas fa-chevron-left',
            next: 'fas fa-chevron-right'
            },
            stepping: 10
        });
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/admin/referrers/create.blade.php ENDPATH**/ ?>