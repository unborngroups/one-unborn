

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4 class="mb-4">
        <i class="bi bi-percent"></i> TDS Settings
    </h4>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('finance.settings.tds.update')); ?>">
        <?php echo csrf_field(); ?>

        
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" name="tds_enabled"
                   <?php echo e($tds->tds_enabled ? 'checked' : ''); ?>>
            <label class="form-check-label fw-bold">Enable TDS</label>
        </div>

        
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">TDS Section</label>
                <input type="text" name="section" class="form-control"
                       placeholder="194C / 194J"
                       value="<?php echo e($tds->section); ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">TDS Rate (%)</label>
                <input type="number" step="0.01" name="tds_rate"
                       class="form-control"
                       value="<?php echo e($tds->tds_rate); ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Threshold Amount</label>
                <input type="number" step="0.01" name="threshold_amount"
                       class="form-control"
                       value="<?php echo e($tds->threshold_amount); ?>">
            </div>
        </div>

        
        <div class="mb-4">
            <label class="form-label fw-bold">Deduct TDS On</label>
            <select name="deduction_on" class="form-select">
                <option value="payment" <?php echo e($tds->deduction_on == 'payment' ? 'selected' : ''); ?>>
                    Payment
                </option>
                <option value="invoice" <?php echo e($tds->deduction_on == 'invoice' ? 'selected' : ''); ?>>
                    Invoice
                </option>
            </select>
        </div>

        <button class="btn btn-primary">
            <i class="bi bi-save"></i> Save TDS Settings
        </button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\settings\tds.blade.php ENDPATH**/ ?>