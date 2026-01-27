

<?php $__env->startSection('content'); ?>
<div class="container">
<h4>Edit Account</h4>

<form method="POST" action="<?php echo e(route('finance.accounts.update',$account->id)); ?>">
<?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

<select name="account_group_id" class="form-control mb-2">
<?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<option value="<?php echo e($g->id); ?>" <?php echo e($account->account_group_id==$g->id?'selected':''); ?>>
    <?php echo e($g->name); ?>

</option>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>

<input type="text" name="account_name" value="<?php echo e($account->account_name); ?>" class="form-control mb-2">

<input type="text" name="account_code" value="<?php echo e($account->account_code); ?>" class="form-control mb-2">

<input type="number" step="0.01" name="opening_balance"
       value="<?php echo e($account->opening_balance); ?>" class="form-control mb-2">

<select name="balance_type" class="form-control mb-2">
<option value="Debit" <?php echo e($account->balance_type=='Debit'?'selected':''); ?>>Debit</option>
<option value="Credit" <?php echo e($account->balance_type=='Credit'?'selected':''); ?>>Credit</option>
</select>

<button class="btn btn-primary">Update</button>
</form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\accounts\edit.blade.php ENDPATH**/ ?>