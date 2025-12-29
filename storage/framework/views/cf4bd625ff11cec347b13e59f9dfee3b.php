

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>Expenses</h4>
        <a href="<?php echo e(route('finance.expenses.create')); ?>" class="btn btn-primary">+ Add Expense</a>
    </div>

    <table class="table table-bordered ">
        <thead class="table-dark-primary">
            <tr>
                <th>S.No</th>
                <th>Action</th>
                <th>Expense Type</th>
                <th>Date</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($loop->iteration); ?></td>
                <td>
                    <?php if($permissions->can_edit): ?>

                               <a href="<?php echo e(route('finance.expenses.edit', $expense)); ?>" class="btn btn-sm btn-primary">

                                    <i class="bi bi-pencil"></i>

                                </a>

                                <?php endif; ?>
                                 

                                 <?php if($permissions->can_delete): ?>

                                 <form action="<?php echo e(route('finance.expenses.destroy',$expense)); ?>" method="POST" class="d-inline">

                                    <?php echo csrf_field(); ?>

                                    <?php echo method_field('DELETE'); ?> 

                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this Client?')">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </form>

                                   <?php endif; ?>
                </td>
                <td><?php echo e($expense->expense_type); ?></td>
                <td><?php echo e($expense->expense_date); ?></td>
                <td>₹ <?php echo e($expense->amount); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\purchases\expenses\index.blade.php ENDPATH**/ ?>