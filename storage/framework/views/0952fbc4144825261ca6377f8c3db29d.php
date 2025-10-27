

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <h4 class="text-primary fw-bold mb-3">View Feasibility</h4>

    <div class="card shadow border-0 p-4">
        
        <div class="row g-3">

            
            <div class="col-md-4">
                <label class="form-label fw-semibold">Type of Service</label>
                <p class="form-control-plaintext"><?php echo e($feasibility->type_of_service); ?></p>
            </div>

            
            <div class="col-md-4">
                <label class="form-label fw-semibold">Client Name</label>
                <p class="form-control-plaintext"><?php echo e($feasibility->client->client_name ?? 'N/A'); ?></p>
            </div>

            
            <div class="col-md-4">
                <label class="form-label fw-semibold">Pincode</label>
                <p class="form-control-plaintext"><?php echo e($feasibility->pincode); ?></p>
            </div>

            
            <div class="col-md-4">
                <label class="form-label fw-semibold">State</label>
                <p class="form-control-plaintext"><?php echo e($feasibility->state); ?></p>
            </div>

            
            <div class="col-md-4">
                <label class="form-label fw-semibold">District</label>
                <p class="form-control-plaintext"><?php echo e($feasibility->district); ?></p>
            </div>

            
            <div class="col-md-4">
                <label class="form-label fw-semibold">Area</label>
                <p class="form-control-plaintext"><?php echo e($feasibility->area); ?></p>
            </div>

            
            <div class="col-md-6">
                <label class="form-label fw-semibold">Address</label>
                <p class="form-control-plaintext"><?php echo e($feasibility->address); ?></p>
            </div>

            
            <div class="col-md-3">
                <label class="form-label fw-semibold">SPOC Name</label>
                <p class="form-control-plaintext"><?php echo e($feasibility->spoc_name); ?></p>
            </div>

            
            <div class="col-md-3">
                <label class="form-label fw-semibold">SPOC Contact 1</label>
                <p class="form-control-plaintext"><?php echo e($feasibility->spoc_contact1); ?></p>
            </div>

            
            <div class="col-md-3">
                <label class="form-label fw-semibold">SPOC Contact 2</label>
                <p class="form-control-plaintext"><?php echo e($feasibility->spoc_contact2); ?></p>
            </div>

            
            <div class="col-md-3">
                <label class="form-label fw-semibold">SPOC Email</label>
                <p class="form-control-plaintext"><?php echo e($feasibility->spoc_email); ?></p>
            </div>

            
            <div class="col-md-3">
                <label class="form-label fw-semibold">No. of Links</label>
                <p class="form-control-plaintext"><?php echo e($feasibility->no_of_links); ?></p>
            </div>

            
            <div class="col-md-3">
                <label class="form-label fw-semibold">Vendor Type</label>
                <p class="form-control-plaintext"><?php echo e($feasibility->vendor_type); ?></p>
            </div>

            
            <div class="col-md-3">
                <label class="form-label fw-semibold">Speed</label>
                <p class="form-control-plaintext"><?php echo e($feasibility->speed); ?></p>
            </div>

            
            <div class="col-md-3">
                <label class="form-label fw-semibold">Static IP</label>
                <p class="form-control-plaintext"><?php echo e($feasibility->static_ip); ?></p>
            </div>

            
            <div class="col-md-3">
                <label class="form-label fw-semibold">Expected Delivery</label>
                <p class="form-control-plaintext"><?php echo e($feasibility->expected_delivery); ?></p>
            </div>

            
            <div class="col-md-3">
                <label class="form-label fw-semibold">Expected Activation</label>
                <p class="form-control-plaintext"><?php echo e($feasibility->expected_activation); ?></p>
            </div>

            
            <div class="col-md-3">
                <label class="form-label fw-semibold">Hardware Required</label>
                <p class="form-control-plaintext"><?php echo e($feasibility->hardware_required ? 'Yes' : 'No'); ?></p>
            </div>

            
            <?php if($feasibility->hardware_required): ?>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Hardware Model Name</label>
                <p class="form-control-plaintext"><?php echo e($feasibility->hardware_model_name); ?></p>
            </div>
            <?php endif; ?>

            
            <div class="col-md-3">
                <label class="form-label fw-semibold">Status</label>
                <p class="form-control-plaintext"><?php echo e($feasibility->status); ?></p>
            </div>

        </div>

        
        <div class="mt-4 text-end">
            <a href="<?php echo e(route('feasibility.index')); ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\new\multipleuserpage\resources\views/feasibility/view.blade.php ENDPATH**/ ?>