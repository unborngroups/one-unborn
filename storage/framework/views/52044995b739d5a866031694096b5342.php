

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Finance Settings</h4>
        <p class="text-muted mb-0">Configure GST and TDS settings for finance module</p>
    </div>

    <div class="row row-cols-1 row-cols-md-2 g-3">
        <div class="col">
            <a href="<?php echo e(route('finance.settings.gst')); ?>" class="card h-100 text-decoration-none text-dark border-primary shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-percent fs-4 text-primary me-2"></i>
                        <h5 class="card-title mb-0">GST Settings</h5>
                    </div>
                    <p class="card-text text-muted">Manage GST rates and configuration for all finance transactions.</p>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="<?php echo e(route('finance.settings.tds')); ?>" class="card h-100 text-decoration-none text-dark border-success shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-scissors fs-4 text-success me-2"></i>
                        <h5 class="card-title mb-0">TDS Settings</h5>
                    </div>
                    <p class="card-text text-muted">Configure TDS rates and rules for vendor payments and compliance.</p>
                </div>
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\settings\index.blade.php ENDPATH**/ ?>