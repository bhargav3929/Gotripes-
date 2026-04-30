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
                    <h1 class="h3 mb-0 text-gray-800"><?php echo e(__('create Position')); ?></h1>
                    <a href="<?php echo e(route('admin.positions.index')); ?>" class="btn btn-primary btn-sm shadow-sm"><?php echo e(__('Go Back')); ?></a>
                </div>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('admin.positions.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                          <div class="form-group">
                          <label for="country"><?php echo e(__('Convention')); ?></label>
                          <select class="form-control" id="country-dropdown" name="Convention_Id">
                          <option value="">--Select Convention--</option>
 
                            <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
                             
                                <option value="<?php echo e($country->id); ?>"><?php echo e($country->name); ?></option>
                          
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                             
                          </select>
                        </div>
                        <div class="form-group">
                          <label for="state">Hall</label>
                          <select class="form-control" id="state-dropdown" name="hall_id">
                             <option>--First Select Convention--</option>
                          </select>
                        </div> 
                     
                   
                    <div class="form-group">
                        <b><label for="position"><?php echo e(__('Position')); ?></label></b>
                        <input type="text" class="form-control" id="name" placeholder="<?php echo e(__('position')); ?>" name="name"  />
                    </div>
                    
                    
                    <button type="submit" class="btn btn-primary btn-block"><?php echo e(__('Save')); ?></button>
                </form>
            </div>
        </div>
    

    <!-- Content Row -->

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script-alt'); ?>
<script>
 
$(document).ready(function() {
 
    $('#country-dropdown').on('change', function() {
            var Convention_Id = this.value;
            console.log(Convention_Id);
             $("#state-dropdown").html('');
            $.ajax({
                url:"<?php echo e(url('get-states-by-country')); ?>",
                type: "POST",
                data: {
                    Convention_Id: Convention_Id,
                     _token: '<?php echo e(csrf_token()); ?>' 
                },
                
                dataType : 'json',
                success: function(result){
                    $('#state-dropdown').html('<option value="">--Select Hall--</option>'); 
                    $.each(result.states ,function(key,value){
                    $("#state-dropdown").append('<option value="'+value.id+'">'+value.name+'</option>');
                    });
                    $('#city-dropdown').html('<option value="">Select Hall First</option>'); 
                }
            });
         
         
    });    
 
    $('#state-dropdown').on('change', function() {
            var hall_id = this.value;
             $("#city-dropdown").html('');
            $.ajax({
                url:"<?php echo e(url('get-cities-by-state')); ?>",
                type: "POST",
                data: {
                    hall_id: hall_id,
                     _token: '<?php echo e(csrf_token()); ?>' 
                },
                dataType : 'json',
                success: function(result){
                    $('#city-dropdown').html('<option value="">Select Position</option>'); 
                    $.each(result.cities,function(key,value){
                    $("#city-dropdown").append('<option value="'+value.id+'">'+value.name+'</option>');
                    });
 
                }
            });
         
         
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/admin/positions/create.blade.php ENDPATH**/ ?>