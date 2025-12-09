

<?php $__env->startSection('content'); ?>

<div class="container py-4">
    <h3 class="mb-3 text-primary">Asset Type List</h3>

    <a href="<?php echo e(route('assetmaster.asset_type.create')); ?>" class="btn btn-success mb-3">+ Add Asset Type</a>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>S.No</th>
                <th>Company</th>
                <th>Asset Type</th>
                <th>Created Date</th>
                <th width="140">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $assetTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $at): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($key + 1); ?></td>
                    <td><?php echo e($at->company->company_name); ?></td>
                    <td><?php echo e($at->type_name); ?></td>
                    <td><?php echo e($at->created_at->format('d-m-Y')); ?></td>
                    <td>
                        <a href="<?php echo e(route('assetmaster.asset_type.edit', $at->id)); ?>" class="btn btn-primary btn-sm">Edit</a>
                        <form action="<?php echo e(route('assetmaster.asset_type.destroy', $at->id)); ?>" method="POST" style="display:inline-block;">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="text-center">No records found</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\assetmaster\asset_type\index.blade.php ENDPATH**/ ?>