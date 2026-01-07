

<?php $__env->startSection('content'); ?>

<div class="container py-4 d-flex flex-column align-items-center justify-content-center" style="min-height: 80vh;">

    <h3 class="mb-3 text-center">View Renewal</h3>

    <div class="card shadow border-0 p-4 w-100" style="max-width: 800px;">

        
        <div class="mb-3">
            <label class="form-label">Deliverable</label>
            <input type="text"
                   class="form-control"
                   value="<?php echo e($renewal->deliverable->circuit_id ?? $renewal->deliverable->po_number ?? 'Deliverable #'.$renewal->deliverable_id); ?>"
                   readonly>
        </div>

        
        <div class="mb-3">
            <label class="form-label">Date of Renewal</label>
            <input type="date"
                   class="form-control"
                   value="<?php echo e($renewal->date_of_renewal); ?>"
                   readonly>
        </div>

        
        <div class="mb-3">
            <label class="form-label">Renewal Months</label>
            <input type="number"
                   class="form-control"
                   value="<?php echo e($renewal->renewal_months); ?>"
                   readonly>
        </div>

        
        <div class="mb-3">
            <label class="form-label">New Expiry Date</label>
            <input type="text"
                   class="form-control"
                   value="<?php echo e($renewal->new_expiry_date); ?>"
                   readonly>
        </div>

        
        <div class="d-flex justify-content-center gap-2">
            <a href="<?php echo e(route('operations.renewals.edit', $renewal->id)); ?>" class="btn btn-primary">
                Edit
            </a>
            <a href="<?php echo e(route('operations.renewals.index')); ?>" class="btn btn-secondary">
                Back
            </a>
        </div>

    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\operations\renewals\view.blade.php ENDPATH**/ ?>