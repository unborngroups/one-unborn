

<?php $__env->startSection('content'); ?>
<div class="container mt-4">
    <h2 class="mb-3">HR - User Profiles</h2>

    <?php if($users->isEmpty()): ?>
        <div class="alert alert-info">No users found.</div>
    <?php else: ?>
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped align-middle mb-0">
                    <thead class="table-dark-primary">
                        <tr>
                            <th>S.No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Designation</th>
                            <th>Profile Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($user->id); ?></td>
                                <td><?php echo e($user->name ?? ($user->profile->fname.' '.$user->profile->lname ?? '-')); ?></td>
                                <td><?php echo e($user->email); ?></td>
                                <td><?php echo e(optional($user->profile)->designation ?? '-'); ?></td>
                                <td>
                                    <?php if($user->profile_created && $user->profile): ?>
                                        <span class="badge bg-success">Completed</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($user->profile): ?>
                                        <a href="<?php echo e(route('hr.view', $user->id)); ?>" class="btn btn-sm btn-warning">View</a>
                                        <a href="<?php echo e(route('hr.edit', $user->id)); ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <?php else: ?>
                                        <span class="text-muted">No profile</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\hr\index.blade.php ENDPATH**/ ?>