

<?php $__env->startSection('content'); ?>
<?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>
<div class="container py-4">
    <h3 class="mb-3 text-primary">Edit Client</h3>
    <div class="card shadow border-0 p-4">
        <form action="<?php echo e(route('clients.update', $client->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            
            <h5 class="text-secondary">Basic Details</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Client Name</label>
                    <input type="text" name="client_name" class="form-control" 
                           value="<?php echo e(old('client_name', $client->client_name)); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Client Code</label>
                    <input type="text" class="form-control" 
                           value="<?php echo e($client->client_code); ?>" readonly>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Business Display Name</label>
                <input type="text" name="business_display_name" class="form-control"
                       value="<?php echo e(old('business_display_name', $client->business_display_name)); ?>">
            </div>

            
            <h5 class="text-secondary mt-3">Address</h5>
            <input type="text" name="address1" class="form-control mb-2" placeholder="Address Line 1"
                   value="<?php echo e(old('address1', $client->address1)); ?>">
            <input type="text" name="address2" class="form-control mb-2" placeholder="Address Line 2"
                   value="<?php echo e(old('address2', $client->address2)); ?>">
            <input type="text" name="address3" class="form-control mb-2" placeholder="Address Line 3"
                   value="<?php echo e(old('address3', $client->address3)); ?>">
            
            <!-- <div class="row">
                <div class="col-md-4">
                    <input type="text" name="city" class="form-control mb-2" placeholder="City"
                           value="<?php echo e(old('city', $client->city)); ?>">
                </div>
                <div class="col-md-4">
                    <input type="text" name="state" class="form-control mb-2" placeholder="State"
                           value="<?php echo e(old('state', $client->state)); ?>">
                </div>
                <div class="col-md-4">
                    <input type="text" name="country" class="form-control mb-2" placeholder="Country"
                           value="<?php echo e(old('country', $client->country)); ?>">
                </div>
            </div> -->
            <div class="row">
    <div class="col-md-4">
        <label class="form-label">City</label>
        <select name="city" id="city" class="form-select select2-tags">
            <option value="">Select or Type City</option>
            <option value="Bangalore">Bangalore</option>
            <option value="Chennai">Chennai</option>
            <option value="Hyderabad">Hyderabad</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">State</label>
        <select name="state" id="state" class="form-select select2-tags">
            <option value="">Select or Type State</option>
            <option value="Karnataka">Karnataka</option>
            <option value="Tamil Nadu">Tamil Nadu</option>
            <option value="Telangana">Telangana</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">Country</label>
        <select name="country" id="country" class="form-select select2-tags">
            <option value="">Select or Type Country</option>
            <option value="India">India</option>
            <option value="USA">USA</option>
            <option value="UK">UK</option>
        </select>
    </div>
</div>
<br>


            <input type="text" name="pincode" class="form-control mb-3" placeholder="Pincode"
                   value="<?php echo e(old('pincode', $client->pincode)); ?>">

            
            <h5 class="text-secondary mt-3">Business Contact Details</h5>
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="billing_spoc_name" class="form-control mb-2" placeholder="Billing SPOC Name"
                           value="<?php echo e(old('billing_spoc_name', $client->billing_spoc_name)); ?>">
                </div>
                <div class="col-md-4">
                    <input type="text" name="billing_spoc_contact" class="form-control mb-2" placeholder="Contact Number"
                           value="<?php echo e(old('billing_spoc_contact', $client->billing_spoc_contact)); ?>">
                </div>
                <div class="col-md-4">
                    <input type="email" name="billing_spoc_email" class="form-control mb-2" placeholder="Email"
                           value="<?php echo e(old('billing_spoc_email', $client->billing_spoc_email)); ?>">
                </div>
            </div>
            <input type="text" name="gstin" class="form-control mb-3" placeholder="GSTIN"
                   value="<?php echo e(old('gstin', $client->gstin)); ?>">

                   
<h5 class="text-secondary mt-3">Invoice Details</h5>
<div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label">Invoice Email</label>
        <input type="email" name="invoice_email" class="form-control"
               value="<?php echo e(old('invoice_email', $client->invoice_email)); ?>" placeholder="Enter invoice email">
    </div>
    <div class="col-md-6">
        <label class="form-label">Invoice CC</label>
        <input type="email" name="invoice_cc" class="form-control"
               value="<?php echo e(old('invoice_cc', $client->invoice_cc)); ?>" placeholder="Enter invoice CC email">
    </div>
</div>


            
            <h5 class="text-secondary mt-3">Technical Support</h5>
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="support_spoc_name" class="form-control mb-2" placeholder="SPOC Name"
                           value="<?php echo e(old('support_spoc_name', $client->support_spoc_name)); ?>">
                </div>
                <div class="col-md-4">
                    <input type="text" name="support_spoc_mobile" class="form-control mb-2" placeholder="Mobile Number"
                           value="<?php echo e(old('support_spoc_mobile', $client->support_spoc_mobile)); ?>">
                </div>
                <div class="col-md-4">
                    <input type="email" name="support_spoc_email" class="form-control mb-2" placeholder="Email"
                           value="<?php echo e(old('support_spoc_email', $client->support_spoc_email)); ?>">
                </div>
            </div>

            
            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option <?php echo e($client->status=='Active'?'selected':''); ?>>Active</option>
                    <option <?php echo e($client->status=='Inactive'?'selected':''); ?>>Inactive</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Update Client</button>
            <a href="<?php echo e(route('clients.index')); ?>" class="btn btn-secondary mt-3">Cancel</a>
        </form>
    </div>
</div>
<script>
document.getElementById('gstin').addEventListener('blur', function() {
    let gstin = this.value.trim();
    if (gstin.length === 15) {
        fetch(`/gst/fetch/${gstin}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("Company Name: " + data.data.tradeNam + "\nPAN: " + data.pan);
                // You can also auto-fill form fields here
                // document.querySelector('[name="business_display_name"]').value = data.data.tradeNam;
            } else {
                alert("Invalid GST Number");
            }
        })
        .catch(err => console.error(err));
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\new\multipleuserpage\resources\views/clients/edit.blade.php ENDPATH**/ ?>