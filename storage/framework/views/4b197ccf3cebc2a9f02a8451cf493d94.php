

<?php $__env->startSection('title', ucfirst($type) . ' Contact Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><?php echo e(ucfirst($type)); ?> Contact Details</h4>
        <a href="<?php echo e(route('contacts.' . $type . '.index')); ?>" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-muted">Name</label>
                    <div class="fw-semibold"><?php echo e($contact->name); ?></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted">Area</label>
                    <div class="fw-semibold"><?php echo e($contact->area ?: '-'); ?></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted">State</label>
                    <div class="fw-semibold"><?php echo e($contact->state ?: '-'); ?></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted">Contact1</label>
                    <div class="fw-semibold"><?php echo e($contact->contact1); ?></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted">Contact2</label>
                    <div class="fw-semibold"><?php echo e($contact->contact2 ?: '-'); ?></div>
                </div>
                <div class="col-md-6">
                        <label for="form-label fw-semibold">Remarks</label>
                        <div class="fw-semibold"><?php echo e($contact->remarks ?: '-'); ?></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted">Status</label>
                    <div>
                        <span class="badge <?php echo e(strtolower($contact->status) === 'active' ? 'bg-success' : 'bg-secondary'); ?>">
                            <?php echo e(ucfirst(strtolower($contact->status))); ?>

                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\contact\show.blade.php ENDPATH**/ ?>