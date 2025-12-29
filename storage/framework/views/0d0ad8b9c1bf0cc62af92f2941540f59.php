

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>Chart of Accounts</h4>

    <a href="<?php echo e(route('finance.accounts.create')); ?>" class="btn btn-primary mb-3">
        + Add Account
    </a>


    
    

    <?php if(session('success')): ?>

        <div class="alert alert-success">

            <?php echo e(session('success')); ?>


        </div>

    <?php endif; ?>

        <?php $currentUserId = auth()->id(); ?>
        <table class="table table-bordered">
        <thead>
            <tr>
                <th>Account</th>
                <th>Group</th>
                <th>Opening Balance</th>
                <th>Status</th>
                <th width="260">Workflow</th>
                <th width="200">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($acc->account_name); ?></td>
                <td><?php echo e($acc->group->name); ?></td>
                <td><?php echo e($acc->opening_balance); ?> (<?php echo e($acc->balance_type); ?>)</td>
                <td>
                    <span class="badge bg-<?php echo e($acc->status === 'approved' || $acc->status === 'locked' ? 'success' : ($acc->status === 'pending_checker' ? 'warning' : 'secondary')); ?> text-uppercase">
                        <?php echo e(str_replace('_', ' ', $acc->status)); ?>

                    </span>
                    <div class="small text-muted mt-1">
                        Maker: <?php echo e(optional($acc->maker)->name ?? '—'); ?><br>
                        Checker: <?php echo e(optional($acc->checker)->name ?? '—'); ?>

                    </div>
                </td>
                <td>
                    <?php if($acc->status === 'draft'): ?>
                        <form method="POST" action="<?php echo e(route('finance.accounts.submit', $acc)); ?>">
                            <?php echo csrf_field(); ?>
                            <button class="btn btn-sm btn-outline-primary w-100">Submit for Approval</button>
                        </form>
                    <?php elseif($acc->status === 'pending_checker'): ?>
                        <?php if($currentUserId && $currentUserId !== $acc->maker_id): ?>
                            <form method="POST" action="<?php echo e(route('finance.accounts.approve', $acc)); ?>" class="mb-1">
                                <?php echo csrf_field(); ?>
                                <button class="btn btn-sm btn-success w-100">Approve</button>
                            </form>
                            <form method="POST" action="<?php echo e(route('finance.accounts.reject', $acc)); ?>">
                                <?php echo csrf_field(); ?>
                                <div class="input-group input-group-sm mb-1">
                                    <input type="text" name="remarks" class="form-control" placeholder="Rejection reason" required>
                                    <button class="btn btn-sm btn-danger" type="submit">Reject</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <span class="text-muted">Awaiting checker approval</span>
                        <?php endif; ?>
                    <?php elseif($acc->status === 'rejected'): ?>
                        <span class="text-danger">Rejected · edit & resubmit</span>
                    <?php else: ?>
                        <span class="text-muted">Locked</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="<?php echo e(route('finance.accounts.edit',$acc->id)); ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="<?php echo e(route('finance.accounts.toggle',$acc->id)); ?>" class="btn btn-sm btn-secondary">
                        Toggle
                    </a>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/finance/accounts/index.blade.php ENDPATH**/ ?>