

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h3 class="mb-3 text-primary">Add Make Type</h3>
    <div class="card p-4 shadow">
        <form action="<?php echo e(route('assetmaster.make_type.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>

            <div class="mb-3">
                <label class="form-label">Make Name</label>
                <input type="text" name="make_name" class="form-control" value="<?php echo e(old('make_name')); ?>">
                <?php $__errorArgs = ['make_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <button class="btn btn-primary">Save Make Type</button>
            <a href="<?php echo e(route('assetmaster.make_type.index')); ?>" class="btn btn-secondary mt-3 float-end">Cancel</a>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\assetmaster\make_type\create.blade.php ENDPATH**/ ?>