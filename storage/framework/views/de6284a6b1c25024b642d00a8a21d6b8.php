

<?php $__env->startSection('title', 'Dashboard'); ?> 

<?php $__env->startSection('content'); ?>
<div class="container mt-4">
    <h4 class="mb-3">Welcome, <?php echo e($client->business_name); ?></h4>

    <div class="card shadow">
        <div class="card-header fw-bold">Your Active Links</div>
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0">
                <thead>
                <tr>
                    <th>Service ID</th>
                    <th>Link Type</th>
                    <th>Router</th>
                    <th>Bandwidth</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($link->service_id); ?></td>
                        <td><?php echo e($link->link_type); ?></td>
                        <td><?php echo e($link->router->router_name ?? '-'); ?></td>
                        <td><?php echo e($link->bandwidth); ?> Mbps</td>
                        <td>
                            <a href="<?php echo e(route('client.link.details', $link->id)); ?>" class="btn btn-sm btn-primary">
                                View
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php if(count($links) == 0): ?>
                    <tr><td colspan="6" class="text-center text-muted">No links found</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('client_portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/client_portal/dashboard.blade.php ENDPATH**/ ?>