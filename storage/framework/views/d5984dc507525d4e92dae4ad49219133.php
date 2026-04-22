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

        <form action="<?php echo e(route('settings.system.update')); ?>" method="POST">

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

            <hr class="my-4">

            <h5 class="mb-3 text-primary"><i class="bi bi-shield-lock"></i> GSTIN to PAN Settings</h5>

            <div class="row mb-3">

                <div class="col-md-12">

                    <label class="form-label"><i class="bi bi-key"></i> API Bearer Token (GSTIN Verification)</label>

                    <textarea name="surepass_api_token" class="form-control" rows="3" placeholder="Enter API Bearer Token for GSTIN verification"><?php echo e($settings->surepass_api_token ?? ''); ?></textarea>

                    <small class="text-muted">Configure API token to enable automatic GSTIN fetching from PAN number</small>

                </div>

            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">GSTIN API Environment</label>
                    <select name="surepass_api_environment" class="form-select">
                        <option value="production" <?php echo e(($settings->surepass_api_environment ?? 'production')==='production' ? 'selected' : ''); ?>>Production</option>
                        <option value="sandbox" <?php echo e(($settings->surepass_api_environment ?? '')==='sandbox' ? 'selected' : ''); ?>>Sandbox</option>
                    </select>
                    <small class="text-muted">Choose sandbox for testing with mock data, production for live PAN → GSTIN lookups.</small>
                </div>
            </div>

            <hr class="my-4">
            <h5 class="mb-3 text-primary"><i class="bi bi-whatsapp"></i> WhatsApp Settings</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Default WhatsApp Number</label>
                    <input type="text" name="whatsapp_default_number" value="<?php echo e($settings->whatsapp_default_number ?? ''); ?>" class="form-control" placeholder="e.g. 919876543210">
                    <small class="text-muted">Used as fallback sender / reference number across the system.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Enable WhatsApp Features</label>
                    <select name="whatsapp_enabled" class="form-select">
                        <option value="0" <?php echo e(!($settings->whatsapp_enabled ?? false) ? 'selected' : ''); ?>>Disabled</option>
                        <option value="1" <?php echo e(($settings->whatsapp_enabled ?? false) ? 'selected' : ''); ?>>Enabled</option>
                    </select>
                    <small class="text-muted">Toggle system-wide WhatsApp notifications & integrations.</small>
                </div>
            </div>



            <button type="submit" class="btn btn-primary">Save Settings</button>

        </form>

    </div>

</div>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\settings\system.blade.php ENDPATH**/ ?>