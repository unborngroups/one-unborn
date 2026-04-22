


<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h3 class="text-primary mb-3">💰 Tax & Invoice Settings</h3>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('tax.invoice.update')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Invoice Prefix</label>
                <input type="text" name="invoice_prefix" class="form-control"
                    value="<?php echo e(old('invoice_prefix', $setting->invoice_prefix ?? '')); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label>Starting Invoice Number</label>
                <input type="number" name="invoice_start_no" class="form-control"
                    value="<?php echo e(old('invoice_start_no', $setting->invoice_start_no ?? 1)); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label>Tax Percentage (%)</label>
                <input type="number" name="tax_percentage" step="0.01" class="form-control"
                    value="<?php echo e(old('tax_percentage', $setting->tax_percentage ?? '')); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label>Currency Symbol</label>
                <input type="text" name="currency_symbol" class="form-control"
                    value="<?php echo e(old('currency_symbol', $setting->currency_symbol ?? '₹')); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label>Currency Code</label>
                <input type="text" name="currency_code" class="form-control"
                    value="<?php echo e(old('currency_code', $setting->currency_code ?? 'INR')); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label>Billing Terms</label>
                <input type="text" name="billing_terms" class="form-control"
                    value="<?php echo e(old('billing_terms', $setting->billing_terms ?? 'Net 30 days')); ?>">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\settings\tax_invoice.blade.php ENDPATH**/ ?>