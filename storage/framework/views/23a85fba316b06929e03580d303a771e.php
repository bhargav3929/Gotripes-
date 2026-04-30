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
                    <a href="<?php echo e(route('admin.conventions.index')); ?>" class="btn btn-primary btn-sm shadow-sm"><?php echo e(__('Go Back')); ?></a>
                </div>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('admin.conventions.update', $convention->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('put'); ?>
                    
                    <div class="form-group row">
    <label for="title" class="col-sm-2 col-form-label"><strong><?php echo e(__('Title')); ?> :</strong></label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="title"  name="title"  value="<?php echo e(old('name', $convention->title)); ?>" />
    </div>
</div>
                 
                    <div class="form-group row">
                        <label for="content" class="col-sm-2 col-form-label"><strong><?php echo e(__('Content')); ?> :</strong></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="content"  name="content" value="<?php echo e(old('name', $convention->content)); ?>" />
                    </div>
                 </div>
               

<div class="form-group row">
    <label for="experience" class="col-sm-2 col-form-label"><strong><?php echo e(__('Experience')); ?> :</strong></label>
     <label for="experience_to" class="col-form-label"> From </label>
    <div class="col-sm-4">
        <input type="number" class="form-control" id="experience" name="experience" value="<?php echo e(old('name', $convention->experience)); ?>" />
    </div>
    <label for="experience_to" class="col-form-label"> To </label>
    <div class="col-sm-4">
        <input type="number" class="form-control" id="experience_to" name="experience_to" value="<?php echo e(old('name', $convention->experience_to)); ?>" />
    </div>
    <label for="experience_to" class="col-form-label"> Years</label>
</div>
               
                    <div class="form-group row">
                        <label for="description" class="col-sm-2 col-form-label"><strong><?php echo e(__('Description :')); ?></strong></label>
                        <div class="col-sm-10">
                        <textarea  class="form-control" style="height:150px" id="description" placeholder="<?php echo e(__('Description')); ?>" name="description" value="<?php echo e(old('name', $convention->description)); ?>" >
                         <?php echo e(old('name', $convention->description)); ?>

                        </textarea >  
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="location" class="col-sm-2 col-form-label"><strong><?php echo e(__('Location')); ?> :</strong></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="location"  name="location"  value="<?php echo e(old('name', $convention->location)); ?>"/>
                        </div>
                    </div>
                 <div class="form-group row">
                        <label for="workShift" class="col-sm-2 col-form-label"><strong><?php echo e(__('Work Shift')); ?> :</strong></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="workShift"  name="workShift" value="<?php echo e(old('name', $convention->workShift)); ?>" />
                        </div>
                    </div>
                 <div class="form-group row">
                        <label for="essentialJobFunctions" class="col-sm-2 col-form-label"><strong><?php echo e(__('Essential Job Functions')); ?> :</strong></label>
                        <div class="col-sm-10">
                        <textarea class="form-control"style="height:150px" id="essentialJobFunctions"  name="essentialJobFunctions" value=" <?php echo e(old('name', $convention->essentialJobFunctions)); ?>" > <?php echo e(old('name', $convention->essentialJobFunctions)); ?></textarea > 
                        </div>
                    </div>
                 <div class="form-group row">
                        <label for="technicalSkillsAndKnowledge" class="col-sm-2 col-form-label"><strong><?php echo e(__('Technical Skills And Knowledge')); ?> :</strong></label>
                        <div class="col-sm-10">
                        <textarea class="form-control" style="height:150px" id="technicalSkillsAndKnowledge"  name="technicalSkillsAndKnowledge" value=" <?php echo e(old('name', $convention->technicalSkillsAndKnowledge)); ?>" > <?php echo e(old('name', $convention->technicalSkillsAndKnowledge)); ?></textarea > 
                        </div>
                    </div>
                 <div class="form-group row">
                        <label for="otherSkillsAndAbilities" class="col-sm-2 col-form-label"><strong><?php echo e(__('Other Skills And Abilities')); ?> :</strong></label>
                        <div class="col-sm-10">
                        <textarea class="form-control" style="height:150px" id="otherSkillsAndAbilities"  name="otherSkillsAndAbilities" value=" <?php echo e(old('name', $convention->otherSkillsAndAbilities)); ?>" > <?php echo e(old('name', $convention->otherSkillsAndAbilities)); ?></textarea > 
                        </div>
                    </div>
                 <div class="form-group row">
                        <label for="experienceRequired" class="col-sm-2 col-form-label"><strong><?php echo e(__('Desired Candidate Profile')); ?>:</strong></label>
                        <div class="col-sm-10">
                        <textarea class="form-control" style="height:150px" id="experienceRequired"  name="experienceRequired"  value=" <?php echo e(old('name', $convention->experienceRequired)); ?>"> <?php echo e(old('name', $convention->experienceRequired)); ?></textarea > 
                        </div>
                    </div>
                 <div class="form-group row">
                        <label for="perksAndBenefits" class="col-sm-2 col-form-label"><strong><?php echo e(__('Perks And Benefits')); ?>:</strong></label>
                        <div class="col-sm-10">
                        <textarea class="form-control" style="height:150px" id="perksAndBenefits"  name="perksAndBenefits" value=" <?php echo e(old('name', $convention->perksAndBenefits)); ?>" > <?php echo e(old('name', $convention->perksAndBenefits)); ?></textarea > 
                        </div>
                    </div>
                 <div class="form-group row">
                        <label for="education" class="col-sm-2 col-form-label"><strong><?php echo e(__('Education')); ?>:</strong></label>
                        <div class="col-sm-10">
                        <input class="form-control" id="education"  name="education"  value=" <?php echo e(old('name', $convention->education)); ?>"/>
                        </div>
                    </div>
                 
                    <button type="submit" class="btn btn-primary btn-block"><?php echo e(__('Save')); ?></button>
                </form>
            </div>
        </div>
    

    <!-- Content Row -->

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/admin/conventions/edit.blade.php ENDPATH**/ ?>