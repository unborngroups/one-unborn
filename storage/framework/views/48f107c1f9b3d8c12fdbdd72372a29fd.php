

<?php $__env->startSection('content'); ?>
    <h4>Add Bank</h4>
<form method="POST" action="<?php echo e(route('finance.banking.store')); ?>">
<?php echo csrf_field(); ?>
<input name="bank_name" class="form-control mb-2" placeholder="Bank Name">
<input name="account_name" class="form-control mb-2" placeholder="Account Name">
<input name="account_number" class="form-control mb-2" placeholder="Account Number">
<input name="ifsc_code" class="form-control mb-2" placeholder="IFSC">
<input name="opening_balance" class="form-control mb-2" placeholder="Opening Balance">
<button class="btn btn-success">Save</button>
</form>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\banking\create.blade.php ENDPATH**/ ?>