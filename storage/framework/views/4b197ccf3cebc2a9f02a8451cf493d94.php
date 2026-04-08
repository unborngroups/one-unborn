

<?php $__env->startSection('title', ucfirst($type) . ' Contact Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold"><?php echo e(ucfirst($type)); ?> Contact Details</h4>
        <a href="<?php echo e(route('contacts.' . $type . '.index')); ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-semibold text-secondary mb-2">Name</label>
                        <div class="p-3 bg-light rounded"><?php echo e($contact->name); ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-semibold text-secondary mb-2">Area</label>
                        <div class="p-3 bg-light rounded"><?php echo e($contact->area ?: '-'); ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-semibold text-secondary mb-2">State</label>
                        <div class="p-3 bg-light rounded"><?php echo e($contact->state ?: '-'); ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-semibold text-secondary mb-2">Contact1</label>
                        <div class="p-3 bg-light rounded fw-medium"><?php echo e($contact->contact1); ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-semibold text-secondary mb-2">Contact2</label>
                        <div class="p-3 bg-light rounded"><?php echo e($contact->contact2 ?: '-'); ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-semibold text-secondary mb-2">Status</label>
                        <div>
                            <span class="badge <?php echo e(strtolower($contact->status) === 'active' ? 'bg-success' : 'bg-secondary'); ?> p-2">
                                <?php echo e(ucfirst(strtolower($contact->status))); ?>

                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\contact\show.blade.php ENDPATH**/ ?>