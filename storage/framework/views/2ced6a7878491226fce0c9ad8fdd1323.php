

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h3 class="mb-3 text-primary">Edit Asset Type</h3>
    <div class="card p-4 shadow">
        <form action="<?php echo e(route('assetmaster.asset_type.update', $assetType->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <!-- Select Asset type -->
             <div class="col-md-12">

                    <label class="form-label">Asset Type</label>

                    <select name="type_name" class="form-select select2-tags">

                        <option value="">Select or Type City</option>

                        <option value="Switch" <?php echo e((old('type_name', $assetType->type_name) == 'Switch') ? 'selected' : ''); ?>>Switch</option>

                        <option value="Router" <?php echo e((old('type_name', $assetType->type_name) == 'Router') ? 'selected' : ''); ?>>Router</option>

                        <option value="SD WAN" <?php echo e((old('type_name', $assetType->type_name) == 'SD WAN') ? 'selected' : ''); ?>>SD WAN</option>
                    </select>

                </div>

                <div class="mt-3">
            <button class="btn btn-primary float-start">Update Asset Type</button>
            </div>
            <a href="<?php echo e(route('assetmaster.asset_type.index')); ?>" class="btn btn-light ms-2 float-end">Cancel</a>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\assetmaster\asset_type\edit.blade.php ENDPATH**/ ?>