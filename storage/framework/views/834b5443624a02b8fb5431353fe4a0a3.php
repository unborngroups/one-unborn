

<?php $__env->startSection('content'); ?>
<div class="container mt-3">
    <h4 class="fw-bold">Notification Logs</h4>

    <table class="table table-bordered table-striped mt-3">
        <thead>
        <tr>
            <th>Sent At</th>
            <th>Alert Type</th>
            <th>Email</th>
            <th>Status</th>
            <th>Message</th>
        </tr>
        </thead>
        <tbody>
        <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($log->sent_at); ?></td>
                <td><?php echo e($log->alert_type); ?></td>
                <td><?php echo e($log->sent_to_email); ?></td>
                <td>
                    <?php if($log->status): ?>
                        <span class="badge bg-success">Sent</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Failed</span>
                    <?php endif; ?>
                </td>
                <td><?php echo e($log->message); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <?php echo e($logs->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('client_portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\client_portal\notifications\logs.blade.php ENDPATH**/ ?>