
<?php $__env->startSection('content'); ?>
<div class="container">
<h4 class="mb-3">Add Asset</h4>



    <?php if($errors->any()): ?>

        <div class="alert alert-danger">

            <ul class="mb-0">

                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <li><?php echo e($error); ?></li>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </ul>

        </div>

    <?php endif; ?>

<form action="<?php echo e(route('asset.store')); ?>" method="POST">
<?php echo csrf_field(); ?>

<?php echo $__env->make('asset.form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<button type="submit" class="btn btn-primary mt-3 float-start">Save</button>


            <a href="<?php echo e(route('asset.index')); ?>" class="btn btn-secondary mt-3 float-end"><--Back</a>

    
</form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/asset/create.blade.php ENDPATH**/ ?>