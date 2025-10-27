

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h4 class="text-primary fw-bold mb-3">Feasibility Status - closed</h4>

    <div class="card shadow border-0 p-4">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Client Name</th>
                    <th>Type of Service</th>
                    <th>Vendor 1</th>
                    <th>Vendor 2</th>
                    <th>Delivery Timeline</th>
                    <th>Status</th>
                    <th>Update</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $closedFeasibilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($item->feasibility->client_name ?? '-'); ?></td>
                        <td><?php echo e($item->feasibility->type_of_service ?? '-'); ?></td>
                        <td><?php echo e($item->vendor1_name ?? '-'); ?></td>
                        <td><?php echo e($item->vendor2_name ?? '-'); ?></td>
                        <td><?php echo e($item->delivery_timeline ?? '-'); ?></td>
                        <td>
                            <span class="badge bg-warning text-dark"><?php echo e($item->status); ?></span>
                        </td>
                        <td>
                            <a href="<?php echo e(route('feasibility-status.edit', $item->id)); ?>" class="btn btn-sm btn-primary">Update</a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\new\multipleuserpage\resources\views/feasibility/feasibility_status/closed.blade.php ENDPATH**/ ?>