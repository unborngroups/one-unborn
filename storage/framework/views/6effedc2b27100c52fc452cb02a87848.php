

<?php $__env->startSection('content'); ?>
<div class="container">
    <h1>Create Invoice</h1>
    <form method="POST" action="<?php echo e(route('finance.invoices.store')); ?>">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="deliverable_id" value="<?php echo e($deliverable->id); ?>">
        <div class="mb-3">
            <label>Client Name</label>
            <input type="text" class="form-control" value="<?php echo e($deliverable->feasibility->client->client_name ?? ''); ?>" readonly>
        </div>
        <div class="mb-3">
            <label>Company Name</label>
            <input type="text" class="form-control" value="<?php echo e($deliverable->feasibility->company->company_name ?? ''); ?>" readonly>
        </div>
        <div class="mb-3">
            <label>Invoice Date</label>
            <input type="date" class="form-control" name="invoice_date" required>
        </div>
        <div class="mb-3">
            <label>Due Date</label>
            <input type="date" class="form-control" name="due_date">
        </div>
        <div class="mb-3">
            <label>Amount</label>
            <input type="number" step="0.01" class="form-control" name="amount" required>
        </div>
        <!-- Add more fields as needed -->
        <button type="submit" class="btn btn-primary">Create Invoice</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\invoices\create.blade.php ENDPATH**/ ?>