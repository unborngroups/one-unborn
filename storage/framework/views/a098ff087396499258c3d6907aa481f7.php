

<?php $__env->startSection('content'); ?>
    <h4>Bank Accounts</h4>

    
    

    <?php if(session('success')): ?>

        <div class="alert alert-success">

            <?php echo e(session('success')); ?>


        </div>

    <?php endif; ?>

<a href="<?php echo e(route('finance.banking.create')); ?>" class="btn btn-primary mb-2">+ Add Bank</a>

<table class="table table-bordered">
<tr>
<th>Bank</th><th>Account No</th><th>Balance</th><th>Action</th>
</tr>
<?php $__currentLoopData = $banks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<tr>
<td><?php echo e($b->bank_name); ?></td>
<td><?php echo e($b->account_number); ?></td>
<td><?php echo e($b->opening_balance); ?></td>
<td>
<a href="<?php echo e(route('finance.banking.transactions',$b->id)); ?>"
 class="btn btn-sm btn-info">Transactions</a>
</td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</table>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\banking\index.blade.php ENDPATH**/ ?>