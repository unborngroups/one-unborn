

<?php $__env->startSection('content'); ?>

<div class="container py-4">
    <h3 class="mb-4 text-primary"><i class="bi bi-whatsapp"></i> Test WhatsApp Message</h3>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>



    <div class="card shadow border-0 p-4">
        <form action="<?php echo e(route('settings.whatsapp.test.send')); ?>" method="POST">
            <?php echo csrf_field(); ?>

            <div class="mb-3">
                <label class="form-label fw-bold">Mobile Number <span class="text-danger">*</span></label>
                <input type="text" 
                       name="mobile" 
                       class="form-control" 
                       placeholder="919876543210" 
                       required
                       pattern="[0-9]{10,12}"
                       title="Enter 10-12 digit mobile number with country code">
                <small class="text-muted">Enter with country code (e.g., 919876543210)</small>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Message <span class="text-danger">*</span></label>
                <textarea name="message" class="form-control" rows="5" required placeholder="Enter your test message here..."></textarea>
                <!-- <small class="text-muted">Message will be URL encoded automatically</small> -->
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success px-4">
                    <i class="bi bi-send"></i> Send Test Message
                </button>
                <a href="<?php echo e(route('settings.whatsapp')); ?>" class="btn btn-secondary px-4">
                    <i class="bi bi-arrow-left"></i> Back to Settings
                </a>
            </div>
        </form>
    </div>

    
    <?php if(session('api_response')): ?>
        <div class="card shadow border-0 p-4 mt-4">
            <h5 class="text-info"><i class="bi bi-code-square"></i> API Response</h5>
            <pre class="bg-light p-3 rounded"><code><?php echo e(session('api_response')); ?></code></pre>
        </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\wlcome\multipleuserpage\resources\views\settings\whatsapp-test.blade.php ENDPATH**/ ?>