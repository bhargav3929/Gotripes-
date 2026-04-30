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
                    <h1 class="h3 mb-0 text-gray-800"><?php echo e(__('Create Referee')); ?></h1>
                    <a href="<?php echo e(route('admin.referees.index')); ?>" class="btn btn-primary btn-sm shadow-sm"><?php echo e(__('Go Back')); ?></a>
                </div>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('admin.referees.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                <div class="form-group row">
                    <label for="refereeName" class="col-sm-2 col-form-label"><strong><?php echo e(__('Referee Name')); ?> :</strong></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="refereeName"  name="refereeName" />
                    </div>
                </div> 
                <div class="form-group row">
                        <label class="col-sm-2 col-form-label"><strong><?php echo e(__('Referrer Name')); ?> :</strong><span style="text-color:red">*</span> </label>
                           <div class="col-sm-10">
                        <select class="form-control" name="referrerID" id="referrerID" required>
                             <option value="" selected disabled>Select Referrer Name</option>
                            <?php $__currentLoopData = $referrers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $referrer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($referrer->id); ?>"><?php echo e($referrer->referrerName); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    </div>
                 <div class="form-group row">
                        <label for="refereeEmail" class="col-sm-2 col-form-label"><strong><?php echo e(__('Referrer Email')); ?> :</strong></label>
                        <div class="col-sm-10">
                        <input type="email" class="form-control" id="refreeEmail"  name="refereeEmail"  />
                    </div>
                 </div>
                  <div class="form-group row">
                        <label for="refereeMobile" class="col-sm-2 col-form-label"><strong><?php echo e(__('Referrer Mobile')); ?> :</strong></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="refreeMobile"  name="refereeMobile"  />
                    </div>
                 </div>
                  <div class="form-group row">
                        <label for="refereeAddress" class="col-sm-2 col-form-label"><strong><?php echo e(__('Referrer Address')); ?> :</strong></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="refereeAddress"  name="refereeAddress"  />
                    </div>
                 </div> 
                 
                 <div class="form-group row">
                        <label class="col-sm-2 col-form-label"><strong><?php echo e(__('Loan Type')); ?> :</strong><span style="text-color:red">*</span> </label>
                           <div class="col-sm-10">
                        <select class="form-control" name="tolID" id="tolID" required>
                            <option value="" selected disabled>Select Loan Type</option>
                            <?php $__currentLoopData = $loantypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loantype): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($loantype->id); ?>"><?php echo e($loantype->tolName); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    </div>
                 <div class="form-group row">
        <label for="comment" class="col-sm-2 col-form-label mt-3"><strong><?php echo e(__('Messages')); ?> :</strong></label>
        <!-- SMS Toggle -->
        <div class="form-check form-switch col-sm-3 mt-4 ">
            
            <input class="form-check-input" type="checkbox" id="smsSwitch" name="sms" value="1" >
            <label class="form-check-label" for="smsSwitch"><?php echo e(__('Send SMS')); ?></label>
        </div>
        
        <!-- Email Toggle -->
        <div class="form-check form-switch col-sm-6 mt-4">
         
            <input class="form-check-input" type="checkbox" id="emailSwitch" name="email" value="1" checked>
            <label class="form-check-label" for="emailSwitch"><?php echo e(__('Send Email')); ?></label>
        </div>
    </div>
     <div class="form-group row">
                <label class="col-sm-2 col-form-label"><strong><?php echo e(__('previous State')); ?> :</strong><span style="text-color:red">*</span> </label>
                    <div class="col-sm-10">
                <select class="form-control" name="previousState" id="previousState" required>
                    <option value="" selected disabled>Select previous State</option>
                    <?php $__currentLoopData = $tolvals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $previousState): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($previousState->id); ?>"><?php echo e($previousState->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
             </div>
            </div>
          <div class="form-group row">
                <label class="col-sm-2 col-form-label"><strong><?php echo e(__('current State')); ?> :</strong><span style="text-color:red">*</span> </label>
                    <div class="col-sm-10">
                <select class="form-control" name="currentState" id="currentState" required>
                    <option value="" selected disabled>Select current state</option>
                    <?php $__currentLoopData = $tolvals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currentState): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($currentState->id); ?>"><?php echo e($currentState->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/admin/referees/create.blade.php ENDPATH**/ ?>