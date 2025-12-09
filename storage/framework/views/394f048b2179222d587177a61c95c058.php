

<?php $__env->startSection('content'); ?>

<div class="container py-4">
    <h3 class="mb-3 text-dark-primary float-start">Make Type List</h3>

    <a href="<?php echo e(route('assetmaster.make_type.create')); ?>" class="btn btn-success mb-3 float-end">+ Add Make Type</a>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
        <thead class="table-dark-primary">
            <tr>
                <th>S.No</th>
                <th width="140">Actions</th>
                <th>Company</th>
                <th>Make Name</th>
                <th>Created Date</th>
                
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $makeTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $mk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($key + 1); ?></td>
                    <td>
                        <a href="<?php echo e(route('assetmaster.make_type.edit', $mk->id)); ?>" class="btn btn-primary btn-sm">Edit</a>
                        <form action="<?php echo e(route('assetmaster.make_type.destroy', $mk->id)); ?>" method="POST" style="display:inline-block;">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                    <td><?php echo e($mk->company->company_name); ?></td>
                    <td><?php echo e($mk->make_name); ?></td>
                    <td><?php echo e($mk->created_at->format('d-m-Y')); ?></td>
                    
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="text-center">No records found</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/assetmaster/make_type/index.blade.php ENDPATH**/ ?>