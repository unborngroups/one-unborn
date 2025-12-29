

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>Edit Expense</h4>

    <form method="POST" action="<?php echo e(route('finance.expenses.update', $expense->id)); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="card">
        <div class="mb-3">
            <label>Expense Type</label>
            <input type="text" name="expense_type" class="form-control" value="<?php echo e($expense->expense_type); ?>">
        </div>

        <div class="mb-3">
            <label>Date</label>
            <input type="date" name="expense_date" class="form-control" value="<?php echo e($expense->expense_date); ?>">
        </div>

        <div class="mb-3">
            <label>Amount</label>
            <input type="number" name="amount" class="form-control" value="<?php echo e($expense->amount); ?>">
        </div>

        <button class="btn btn-success">Update</button>
        <a href="<?php echo e(route('finance.expenses.index')); ?>" class="btn btn-secondary">Back</a>
    
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\purchases\expenses\edit.blade.php ENDPATH**/ ?>