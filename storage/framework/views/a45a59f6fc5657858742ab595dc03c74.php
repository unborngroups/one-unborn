


<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h3 class="mb-3 text-primary">System Settings</h3>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow border-0 p-4">
        <form action="<?php echo e(route('system.settings.update')); ?>" method="POST">
            <?php echo csrf_field(); ?>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Timezone</label>
                    <input type="text" name="timezone" value="<?php echo e($settings->timezone ?? ''); ?>" class="form-control">
                </div>
                <div class="col-md-6">
                    <label>Date Format</label>
                    <input type="text" name="date_format" value="<?php echo e($settings->date_format ?? ''); ?>" class="form-control">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Language</label>
                    <input type="text" name="language" value="<?php echo e($settings->language ?? ''); ?>" class="form-control">
                </div>
                <div class="col-md-6">
                    <label>Currency Symbol</label>
                    <input type="text" name="currency_symbol" value="<?php echo e($settings->currency_symbol ?? ''); ?>" class="form-control">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Fiscal Year Start Month</label>
                    <input type="text" name="fiscal_start_month" value="<?php echo e($settings->fiscal_start_month ?? ''); ?>" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\wlcome\multipleuserpage\resources\views\settings\system.blade.php ENDPATH**/ ?>