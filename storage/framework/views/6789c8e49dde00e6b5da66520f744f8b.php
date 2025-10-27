

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h3 class="mb-3 text-primary">View Client Details</h3>

    <div class="card shadow border-0 p-4">
        
        <h5 class="text-secondary">Basic Details</h5>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Client Name:</label>
                <p class="form-control-plaintext"><?php echo e($client->client_name); ?></p>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Client Code:</label>
                <p class="form-control-plaintext"><?php echo e($client->client_code); ?></p>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Business Display Name:</label>
            <p class="form-control-plaintext"><?php echo e($client->business_display_name); ?></p>
        </div>

        
        <h5 class="text-secondary mt-3">Address</h5>
        <p class="form-control-plaintext mb-0"><?php echo e($client->address1); ?></p>
        <p class="form-control-plaintext mb-0"><?php echo e($client->address2); ?></p>
        <p class="form-control-plaintext mb-2"><?php echo e($client->address3); ?></p>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">City:</label>
                <p class="form-control-plaintext"><?php echo e($client->city); ?></p>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">State:</label>
                <p class="form-control-plaintext"><?php echo e($client->state); ?></p>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Country:</label>
                <p class="form-control-plaintext"><?php echo e($client->country); ?></p>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Pincode:</label>
            <p class="form-control-plaintext"><?php echo e($client->pincode); ?></p>
        </div>

        
        <h5 class="text-secondary mt-3">Business Contact Details</h5>
        <div class="row">
            <div class="col-md-4">
                <label class="form-label fw-bold">Billing SPOC Name:</label>
                <p class="form-control-plaintext"><?php echo e($client->billing_spoc_name); ?></p>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Contact Number:</label>
                <p class="form-control-plaintext"><?php echo e($client->billing_spoc_contact); ?></p>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Email:</label>
                <p class="form-control-plaintext"><?php echo e($client->billing_spoc_email); ?></p>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">GSTIN:</label>
            <p class="form-control-plaintext"><?php echo e($client->gstin); ?></p>
        </div>

        
        <h5 class="text-secondary mt-3">Invoice Details</h5>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Invoice Email:</label>
                <p class="form-control-plaintext"><?php echo e($client->invoice_email); ?></p>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Invoice CC:</label>
                <p class="form-control-plaintext"><?php echo e($client->invoice_cc); ?></p>
            </div>
        </div>

        
        <h5 class="text-secondary mt-3">Technical Support</h5>
        <div class="row">
            <div class="col-md-4">
                <label class="form-label fw-bold">SPOC Name:</label>
                <p class="form-control-plaintext"><?php echo e($client->support_spoc_name); ?></p>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Mobile:</label>
                <p class="form-control-plaintext"><?php echo e($client->support_spoc_mobile); ?></p>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Email:</label>
                <p class="form-control-plaintext"><?php echo e($client->support_spoc_email); ?></p>
            </div>
        </div>

        <div class="mt-3">
            <label class="form-label fw-bold">Status:</label>
            <span class="badge bg-<?php echo e($client->status == 'Active' ? 'success' : 'secondary'); ?>">
                <?php echo e($client->status); ?>

            </span>
        </div>

        <div class="mt-4">
            <a href="<?php echo e(route('clients.index')); ?>" class="btn btn-secondary">Back</a>
            <a href="<?php echo e(route('clients.edit', $client->id)); ?>" class="btn btn-primary">Edit Client</a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\new\multipleuserpage\resources\views/clients/view.blade.php ENDPATH**/ ?>