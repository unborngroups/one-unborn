

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h3 class="mb-3 text-primary">User Login Report</h3>

    <div class="card shadow p-3 border-0">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>S.No</th>
                    <th>User Name</th>
                    <th>Login Time</th>
                    <th>Logout Time</th>
                    <th>Total Minutes</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($key + 1); ?></td>
                    <td><?php echo e($log->user->name ?? 'Unknown User'); ?></td>

                    <td><?php echo e($log->login_time); ?></td>
                    

                    <td><?php echo e($log->logout_time ?? 'Active Now'); ?></td>
                    <!-- <td><?php echo e($log->total_minutes ?? 'Calculating...'); ?></td> -->
                    <td><?php echo e($log->total_minutes); ?>

                        <?php if(!$log->logout_time): ?>
                        <small class="text-muted">(Live)</small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($log->status === 'Online'): ?>
                            <span class="badge bg-success">Online</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Offline</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/admin/index.blade.php ENDPATH**/ ?>