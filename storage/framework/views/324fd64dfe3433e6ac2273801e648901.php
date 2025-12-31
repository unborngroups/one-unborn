



<?php $__env->startSection('content'); ?>



<?php if($errors->any()): ?>

    <div class="alert alert-danger">

        <ul class="mb-0">

            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                <li><?php echo e($error); ?></li>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </ul>

    </div>

<?php endif; ?>



<div class="container py-4">

    <h3 class="mb-3">Edit User</h3>

    <div class="card shadow border-0 p-4">

        <form action="<?php echo e(route('users.update',$user)); ?>" method="POST">

            <?= csrf_field() ?>

             <?php echo method_field('PUT'); ?>
             <div class="row">

            <div class="col-md-6 mb-3">

                <label>Name</label>

                <input type="text" name="name" value="<?php echo e(old('name', $user->name)); ?>" class="form-control" required>

            </div>



            

             <div class="col-md-6 mb-3">

                <label>User Type</label>

                <select name="user_type_id" class="form-control" required>

                <option value="">Select User Type</option>

                <?php $__currentLoopData = $userTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

               <option value="<?php echo e($type->id); ?>" 

                <?php echo e(old('user_type_id', $user->user_type_id ?? '') == $type->id ? 'selected' : ''); ?>>

                <?php echo e(ucfirst($type->name)); ?>


               </option>

               <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

               </select>

                <?php $__errorArgs = ['user_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>

                <span class="text-danger"><?php echo e($message); ?></span>

                 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            </div>



            <!-- <div class="mb-3">

                <label>Email</label>

                <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" class="form-control" required>

            </div> -->



            

            <div class="col-md-6 mb-3">

    <label>Official Email</label>

    <input type="email" name="official_email" value="<?php echo e(old('official_email', $user->official_email)); ?>" class="form-control" required>

</div>



<div class="form-check mb-3">

    <input type="checkbox" name="send_email" value="1" class="form-check-input" id="sendEmailCheckbox">

    <label for="sendEmailCheckbox" class="form-check-label">

        Send update notification email?

    </label>

</div>







 <div class="col-md-6 mb-3">

    <label>Personal Email</label>

    <input type="email" name="personal_email" value="<?php echo e(old('personal_email', $user->personal_email)); ?>" class="form-control">

</div>





             <div class="col-md-6 mb-3">

                <label>Mobile</label>

                <input type="text" name="mobile" value="<?php echo e(old('mobile', $user->mobile)); ?>" class="form-control">

            </div>



                     

 <div class="col-md-6 mb-3">

    <label class="form-label">Company</label>

    <select id="company_id" name="companies[]" class="form-select" multiple required>

        <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <option value="<?php echo e($company->id); ?>" 

                <?php echo e(in_array($company->id, old('companies', $selectedCompanies)) ? 'selected' : ''); ?>>

                <?php echo e($company->company_name); ?>


            </option>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </select>

    <small class="text-muted">Hold <b>Ctrl</b> (Windows) or <b>Cmd</b> (Mac) to select multiple.</small>

</div>





             <div class="col-md-6 mb-3">

                <label>Date of Birth</label>

                <input type="date" name="Date_of_Birth" value="<?php echo e(old('Date_of_Birth', $user->Date_of_Birth)); ?>" class="form-control" required>

            </div>

            

             <div class="col-md-6 mb-3">

                <label>Date of Joining</label>

                <input type="date" name="Date_of_Joining" value="<?php echo e(old('Date_of_Joining', $user->Date_of_Joining)); ?>" class="form-control" required>

            </div>
            </div>

            <!-- 

            <div class="mb-3">

                <label>Status</label>

                <select name="status" class="form-control">

                    <option <?php echo e($user->status=='Active'?'selected':''); ?>>Active</option>

                    <option <?php echo e($user->status=='Inactive'?'selected':''); ?>>Inactive</option>

                </select>

            </div> -->

             

            <input type="hidden" name="status" value="Active">



            

            <button class="btn btn-warning">Update</button>

            <a href="<?php echo e(route('users.index')); ?>" class="btn btn-secondary">Back</a>

        </form>

    </div>

</div>
<style>
    label{
        font-weight: 600;
    }
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/users/edit.blade.php ENDPATH**/ ?>