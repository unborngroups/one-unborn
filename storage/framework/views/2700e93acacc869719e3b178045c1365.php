

<?php $__env->startSection('content'); ?>
<div class="container">
<h4>Create Account</h4>


        <?php if($errors->any()): ?>

            <div class="alert alert-danger">

                <ul class="mb-0">

                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <li><?php echo e($error); ?></li>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </ul>

            </div>

        <?php endif; ?>


<form method="POST" action="<?php echo e(route('finance.accounts.store')); ?>">
<?php echo csrf_field(); ?>


<select name="account_group_id" class="form-control mb-2" required>
    <option value="">Select Account Group</option>
    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($g->id); ?>"><?php echo e($g->name); ?></option>
        <option value="">Asset</option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>

<input type="text" name="account_name" class="form-control mb-2" placeholder="Account Name">

<input type="text" name="account_code" class="form-control mb-2" placeholder="Account Code">

<input type="number" step="0.01" name="opening_balance" class="form-control mb-2" placeholder="Opening Balance">

<select name="balance_type" class="form-control mb-2">
    <option value="Debit">Debit</option>
    <option value="Credit">Credit</option>
</select>

<button class="btn btn-success">Save</button>
</form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\accounts\create.blade.php ENDPATH**/ ?>