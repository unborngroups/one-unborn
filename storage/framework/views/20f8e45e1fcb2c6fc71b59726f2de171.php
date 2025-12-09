
<?php $__env->startSection('content'); ?>
<div class="container">
<h4 class="mb-3">Edit Asset</h4>



    <?php if($errors->any()): ?>

        <div class="alert alert-danger">

            <ul class="mb-0">

                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <li><?php echo e($error); ?></li>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </ul>

        </div>

    <?php endif; ?>

<div class="text-center mb-3">
    <?php echo DNS1D::getBarcodeHTML($asset->asset_id, 'C128', 1.4, 40); ?>

    <p class="fw-bold"><?php echo e($asset->asset_id); ?></p>
</div>

<form action="<?php echo e(route('asset.update', $asset->id)); ?>" method="POST">
<?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

<?php echo $__env->make('asset.form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<button type="submit" class="btn btn-success mt-3">Update</button>
<div class="text-end">

            <a href="<?php echo e(route('asset.index')); ?>" class="btn btn-secondary">Back</a>

        </div>
</form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/asset/edit.blade.php ENDPATH**/ ?>