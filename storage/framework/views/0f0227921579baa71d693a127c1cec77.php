

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h3 class="mb-3 text-primary">View User Details</h3>

    <div class="card shadow border-0 p-4">
         
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="fw-bold">Name:</label>
                <div><?php echo e($user->name); ?></div>
            </div>

            <div class="col-md-6">
                <label class="fw-bold">User Type:</label>
                <div><?php echo e($user->userType->name ?? '-'); ?></div>
            </div>
        </div>

        
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="fw-bold">Official Email:</label>
                <div><?php echo e($user->official_email); ?></div>
            </div>
            <div class="col-md-6">
                <label class="fw-bold">Personal Email:</label>
                <div><?php echo e($user->personal_email ?? '-'); ?></div>
            </div>
        </div>

        
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="fw-bold">Mobile:</label>
                <div><?php echo e($user->mobile ?? '-'); ?></div>
            </div>
            <div class="col-md-6">
                <label class="fw-bold">Companies:</label>
                <div>
                    <?php if($user->companies && $user->companies->count() > 0): ?>
                        <?php $__currentLoopData = $user->companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="badge bg-primary"><?php echo e($company->company_name); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        - 
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="fw-bold">Date of Birth:</label>
                <div><?php echo e($user->Date_of_Birth ? \Carbon\Carbon::parse($user->Date_of_Birth)->format('d-M-Y') : '-'); ?></div>
            </div>
            <div class="col-md-6">
                <label class="fw-bold">Date of Joining:</label>
                <div><?php echo e($user->Date_of_Joining ? \Carbon\Carbon::parse($user->Date_of_Joining)->format('d-M-Y') : '-'); ?></div>
            </div>
        </div>

        
        <div class="mb-3">
            <label class="fw-bold">Status:</label>
            <div>
                <?php if($user->status === 'Active'): ?>
                    <span class="badge bg-success">Active</span>
                <?php else: ?>
                    <span class="badge bg-danger">Inactive</span>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="mt-3">
            <a href="<?php echo e(route('users.index')); ?>" class="btn btn-secondary">Back</a>
            <a href="<?php echo e(route('users.edit', $user)); ?>" class="btn btn-warning">Edit</a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\new\multipleuserpage\resources\views/users/view.blade.php ENDPATH**/ ?>