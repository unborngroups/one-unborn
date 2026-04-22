

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4 class="mb-4">
        <i class="bi bi-receipt"></i> GST Settings
    </h4>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('finance.settings.gst.update')); ?>">
        <?php echo csrf_field(); ?>

        
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" name="gst_enabled"
                   <?php echo e($gst->gst_enabled ? 'checked' : ''); ?>>
            <label class="form-check-label fw-bold">Enable GST</label>
        </div>

        
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">GST Number</label>
                <input type="text" name="gst_number" class="form-control"
                       value="<?php echo e($gst->gst_number); ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">State Code</label>
                <input type="text" name="state_code" class="form-control"
                       value="<?php echo e($gst->state_code); ?>">
            </div>
        </div>

        
        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label">CGST %</label>
                <input type="number" step="0.01" name="cgst_rate"
                       class="form-control" value="<?php echo e($gst->cgst_rate); ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label">SGST %</label>
                <input type="number" step="0.01" name="sgst_rate"
                       class="form-control" value="<?php echo e($gst->sgst_rate); ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label">IGST %</label>
                <input type="number" step="0.01" name="igst_rate"
                       class="form-control" value="<?php echo e($gst->igst_rate); ?>">
            </div>
        </div>

        
        <div class="mb-4">
            <label class="form-label fw-bold">GST Calculation Type</label>
            <select name="calculation_type" class="form-select">
                <option value="exclusive" <?php echo e($gst->calculation_type == 'exclusive' ? 'selected' : ''); ?>>
                    Exclusive
                </option>
                <option value="inclusive" <?php echo e($gst->calculation_type == 'inclusive' ? 'selected' : ''); ?>>
                    Inclusive
                </option>
            </select>
        </div>

        <button class="btn btn-primary">
            <i class="bi bi-save"></i> Save GST Settings
        </button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\settings\gst.blade.php ENDPATH**/ ?>