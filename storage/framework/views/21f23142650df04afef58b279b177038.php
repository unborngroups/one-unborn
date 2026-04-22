

<?php $__env->startSection('content'); ?>
    <h4><?php echo e($bank->bank_name); ?> - Transactions</h4>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('finance.banking.transaction.store')); ?>">
<?php echo csrf_field(); ?>
<input type="hidden" name="bank_account_id" value="<?php echo e($bank->id); ?>">
<input type="date" name="transaction_date" class="form-control mb-2" value="<?php echo e(old('transaction_date')); ?>">
<select name="type" class="form-control mb-2">
<option value="Receipt" <?php echo e(old('type') === 'Receipt' ? 'selected' : ''); ?>>Receipt</option>
<option value="Payment" <?php echo e(old('type') === 'Payment' ? 'selected' : ''); ?>>Payment</option>
</select>
<input name="amount" class="form-control mb-2" placeholder="Amount" value="<?php echo e(old('amount')); ?>">
<input name="reference" class="form-control mb-2" placeholder="Reference" value="<?php echo e(old('reference')); ?>">
<button class="btn btn-primary">Add</button>
</form>

<hr>

<table class="table table-bordered">
<tr>
<th>Date</th><th>Type</th><th>Amount</th><th>Status</th><th></th>
</tr>
<?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<tr>
<td><?php echo e($t->transaction_date); ?></td>
<td><?php echo e($t->type); ?></td>
<td><?php echo e($t->amount); ?></td>
<td><?php echo e($t->is_reconciled?'Reconciled':'Pending'); ?></td>
<td>
<?php if(!$t->is_reconciled): ?>
<a href="<?php echo e(route('finance.banking.reconcile',$t->id)); ?>"
 class="btn btn-sm btn-success">Reconcile</a>
<?php endif; ?>
</td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</table>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\banking\transactions.blade.php ENDPATH**/ ?>