

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h3 class="mb-3 text-primary">View Vendor Details</h3>

    <div class="card shadow border-0 p-4">
        
        <h5 class="text-secondary">Basic Details</h5>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Vendor Name:</label>
                <p class="form-control-plaintext"><?php echo e($vendor->vendor_name); ?></p>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Vendor Code:</label>
                <p class="form-control-plaintext"><?php echo e($vendor->vendor_code); ?></p>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Business Display Name:</label>
            <p class="form-control-plaintext"><?php echo e($vendor->business_display_name); ?></p>
        </div>

        
        <h5 class="text-secondary mt-3">Address</h5>
        <p class="form-control-plaintext mb-0"><?php echo e($vendor->address1); ?></p>
        <p class="form-control-plaintext mb-0"><?php echo e($vendor->address2); ?></p>
        <p class="form-control-plaintext mb-2"><?php echo e($vendor->address3); ?></p>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">City:</label>
                <p class="form-control-plaintext"><?php echo e($vendor->city); ?></p>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">State:</label>
                <p class="form-control-plaintext"><?php echo e($vendor->state); ?></p>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Country:</label>
                <p class="form-control-plaintext"><?php echo e($vendor->country); ?></p>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Pincode:</label>
            <p class="form-control-plaintext"><?php echo e($vendor->pincode); ?></p>
        </div>

        
        <h5 class="text-secondary mt-3">Contact Person</h5>
        <div class="row">
            <div class="col-md-4">
                <label class="form-label fw-bold">Name:</label>
                <p class="form-control-plaintext"><?php echo e($vendor->contact_person_name); ?></p>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Mobile:</label>
                <p class="form-control-plaintext"><?php echo e($vendor->contact_person_mobile); ?></p>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Email:</label>
                <p class="form-control-plaintext"><?php echo e($vendor->contact_person_email); ?></p>
            </div>
        </div>

        
        <h5 class="text-secondary mt-3">Legal Details</h5>
        <div class="row">
            <div class="col-md-6">
                <label class="form-label fw-bold">GSTIN:</label>
                <p class="form-control-plaintext"><?php echo e($vendor->gstin); ?></p>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">PAN No:</label>
                <p class="form-control-plaintext"><?php echo e($vendor->pan_no); ?></p>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Bank Account No:</label>
                <p class="form-control-plaintext"><?php echo e($vendor->bank_account_no); ?></p>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">IFSC Code:</label>
                <p class="form-control-plaintext"><?php echo e($vendor->ifsc_code); ?></p>
            </div>
        </div>

        
        <div class="mt-3">
            <label class="form-label fw-bold">Status:</label>
            <span class="badge bg-<?php echo e($vendor->status == 'Active' ? 'success' : 'secondary'); ?>">
                <?php echo e($vendor->status); ?>
            </span>
        </div>

        
        <div class="mt-4">
            <a href="<?php echo e(route('vendors.index')); ?>" class="btn btn-secondary">Back</a>
            <!-- <a href="<?php echo e(route('vendors.edit', $vendor->id)); ?>" class="btn btn-primary">Edit Vendor</a> -->
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\vendors\view.blade.php ENDPATH**/ ?>