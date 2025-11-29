

<?php $__env->startSection('content'); ?>

<div class="container py-4">

    <h3 class="mb-4 text-primary"><i class="bi bi-whatsapp"></i> WhatsApp API Settings</h3>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    
    <ul class="nav nav-tabs mb-4" id="whatsappTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="official-tab" data-bs-toggle="tab" data-bs-target="#official" type="button" role="tab">
                <i class="bi bi-shield-check"></i> Official WhatsApp API
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="unofficial-tab" data-bs-toggle="tab" data-bs-target="#unofficial" type="button" role="tab">
                <i class="bi bi-gear"></i> Unofficial WhatsApp API
            </button>
        </li>
    </ul>

    
    <div class="tab-content" id="whatsappTabsContent">
        
        
        <div class="tab-pane fade show active" id="official" role="tabpanel">
            <div class="card shadow border-0 p-4">
              
                <form action="<?php echo e(route('settings.whatsapp.update')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="api_type" value="official">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">WhatsApp Business Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="official_phone" value="<?php echo e(old('official_phone', $settings->official_phone ?? '')); ?>" 
                                   class="form-control" placeholder="+91XXXXXXXXXX" required>
                            <!-- <small class="text-muted">Enter with country code (e.g., +919876543210)</small> -->
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">WhatsApp Business Account ID <span class="text-danger">*</span></label>
                            <input type="text" name="official_account_id" value="<?php echo e(old('official_account_id', $settings->official_account_id ?? '')); ?>" 
                                   class="form-control" placeholder="Business Account ID" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-save"></i> Save Official API Settings
                    </button>
                </form>
            </div>
        </div>

        
        <div class="tab-pane fade" id="unofficial" role="tabpanel">
            <div class="card shadow border-0 p-4">
               
                <form action="<?php echo e(route('settings.whatsapp.update')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="api_type" value="unofficial">

                    <div class="row mb-3">
                        <div class="col-md-6 p-4">
                            <label class="form-label fw-bold">API URL <span class="text-danger">*</span></label>
                            <input type="url"
                                   name="unofficial_api_url"
                                   value="<?php echo e(old('unofficial_api_url', $settings->unofficial_api_url ?? 'https://wahub.pro/api/send')); ?>"
                                   class="form-control"
                                   required>
                           
                        </div>
                  
                        <div class="col-md-6 p-4">
                            <label class="form-label fw-bold">Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" name="unofficial_mobile" value="<?php echo e(old('unofficial_mobile', $settings->unofficial_mobile ?? '')); ?>" 
                                   class="form-control" placeholder="919876543210" required>
                        </div>

                        <div class="col-md-6 p-4">
                            <label class="form-label fw-bold">Instance ID <span class="text-danger">*</span></label>
                            <input type="text" name="unofficial_instance_id" value="<?php echo e(old('unofficial_instance_id', $settings->unofficial_instance_id ?? '691AEEF33256E')); ?>" 
                                   class="form-control" placeholder="691AEEF33256E" required>
                        </div>

                        <div class="col-md-6 p-4">
                            <label class="form-label fw-bold">Access Token <span class="text-danger">*</span></label>
                            <input type="text" name="unofficial_access_token" value="<?php echo e(old('unofficial_access_token', $settings->unofficial_access_token ?? '68f9df1ac354c')); ?>" 
                                   class="form-control" placeholder="68f9df1ac354c" required>
                        </div>
                    </div>

<!--                   
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="unofficial_enabled" id="unofficialEnabled" 
                               <?php echo e(($settings->unofficial_enabled ?? false) ? 'checked' : ''); ?> value="1">
                        <label class="form-check-label" for="unofficialEnabled">
                            <strong>Enable Unofficial WhatsApp API</strong>
                        </label>
                    </div> -->

                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save"></i> Save Unofficial API Settings
                    </button>

                    <a href="<?php echo e(route('settings.whatsapp.test')); ?>" class="btn btn-info px-4 ms-2" target="_blank">
                        <i class="bi bi-send"></i> Test WhatsApp Message
                    </a>

                </form>
            </div>
        </div>

    </div>

</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\settings\whatsapp.blade.php ENDPATH**/ ?>