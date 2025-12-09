
<?php $__env->startSection('content'); ?>
<div class="container">
<h4 class="mb-3">Add Asset</h4>

<form action="<?php echo e(route('asset.store')); ?>" method="POST">
<?php echo csrf_field(); ?>

<?php echo $__env->make('asset.form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<button type="submit" class="btn btn-primary mt-3">Save</button>
</form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\asset\create.blade.php ENDPATH**/ ?>