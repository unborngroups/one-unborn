

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <h4 class="fw-bold text-primary mb-3">Update Feasibility Status</h4>

    <div class="card shadow border-0 p-4">
        <form action="<?php echo e(route('feasibility.status.update', $record->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Vendor Name *</label>
                    <input type="text" name="vendor_name" class="form-control" value="<?php echo e($record->vendor_name); ?>" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">ARC</label>
                    <input type="text" name="arc" class="form-control" value="<?php echo e($record->arc); ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">OTC</label>
                    <input type="text" name="otc" class="form-control" value="<?php echo e($record->otc); ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Static IP Cost</label>
                    <input type="text" name="static_ip_cost" class="form-control" value="<?php echo e($record->static_ip_cost); ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Delivery Timeline</label>
                    <input type="text" name="delivery_timeline" class="form-control" value="<?php echo e($record->delivery_timeline); ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="Open" <?php echo e($record->status == 'Open' ? 'selected' : ''); ?>>Open</option>
                        <option value="InProgress" <?php echo e($record->status == 'InProgress' ? 'selected' : ''); ?>>In Progress</option>
                        <option value="Closed" <?php echo e($record->status == 'Closed' ? 'selected' : ''); ?>>Closed</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Changes</button>
                <a href="<?php echo e(route('feasibility.status.index', 'Open')); ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\new\multipleuserpage\resources\views/feasibility/feasibility_status/view.blade.php ENDPATH**/ ?>