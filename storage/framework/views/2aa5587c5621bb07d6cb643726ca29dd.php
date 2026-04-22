
<?php use Milon\Barcode\Facades\DNS1DFacade as DNS1D; ?>
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


<form action="<?php echo e(route('operations.asset.update', $asset->id)); ?>" method="POST">
<?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

<?php echo $__env->make('operations.asset.form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<button type="submit" class="btn btn-success mt-3">Update</button>
<div class="text-end">

            <a href="<?php echo e(route('operations.asset.index')); ?>" class="btn btn-secondary">Back</a>

        </div>
</form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\operations\asset\edit.blade.php ENDPATH**/ ?>