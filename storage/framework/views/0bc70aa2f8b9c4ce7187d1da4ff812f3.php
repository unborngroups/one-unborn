<?php $__env->startSection('content'); ?>

<div class="container py-4">
    <h3 class="mb-3 text-primary">Edit Client</h3>

```
<div class="card shadow border-0 p-4">
    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('clients.update', $client->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <small id="gstStatus" class="mt-2 d-block text-muted"></small>
        <hr>

        <h5 class="text-secondary mt-2">Basic Details</h5>

        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label">Client Name</label>
                <input type="text" name="client_name" class="form-control"
                       value="<?php echo e(old('client_name', $client->client_name)); ?>" required>
            </div>

            <div class="col-md-3">
                    <label for="form-label">Short Name</label>
                    <input type="text" name="short_name" class="form-control"
                           value="<?php echo e(old('short_name', $client->short_name)); ?>">
                </div>

            <div class="col-md-3" id="clientCodeDiv">
                <label class="form-label">Client Code</label>
                <input type="text" class="form-control" value="<?php echo e($client->client_code); ?>" readonly>
            </div>

            <div class="col-md-3">
                <label class="form-label">User Name</label>
                <div class="input-group">
                    <input type="text" name="user_name" id="user_name" class="form-control"
                           value="<?php echo e(old('user_name', $client->user_name)); ?>" required>
                    <button type="button" id="sendPwdBtn" class="btn btn-outline-primary">PWD</button>
                </div>
                <small id="pwdStatus" class="text-muted d-block mt-1"></small>
            </div>

            <div class="col-md-3">
                <label class="form-label">Business Display Name</label>
                <input type="text" name="business_display_name" id="business_display_name" class="form-control"
                       value="<?php echo e(old('business_display_name', $client->business_display_name)); ?>">
            </div>

            <div class="col-md-3 mt-3">
                <label class="form-label">Type of Office</label>
                <select name="office_type" id="office_type" class="form-select" required>
                    <option value="">Select</option>
                    <option value="head" <?php echo e($client->office_type === 'head' ? 'selected' : ''); ?>>Head Office</option>
                    <option value="branch" <?php echo e($client->office_type === 'branch' ? 'selected' : ''); ?>>Branch Office</option>
                </select>
            </div>

            <div class="col-md-3 mt-3" id="panDiv">
                <label class="form-label">PAN Number</label>
                <input type="text" name="pan_number" id="pan_number" class="form-control"
                       value="<?php echo e(old('pan_number', $client->pan_number)); ?>">
            </div>

            <div class="col-md-3 mt-3 d-none" id="headOfficeDiv">
                <label class="form-label">Head Office</label>
                <select name="head_office_id" class="form-select">
                    <option value="">Select Head Office</option>
                    <?php $__currentLoopData = $headOffices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ho): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($ho->id); ?>" <?php echo e($client->head_office_id == $ho->id ? 'selected' : ''); ?>>
                            <?php echo e($ho->client_name); ?> (<?php echo e($ho->client_code); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>

        <h5 class="text-secondary mt-3">Address</h5>
        <input type="text" name="address1" id="address1" class="form-control mb-2"
               value="<?php echo e(old('address1', $client->address1)); ?>" placeholder="Address Line 1">
        <input type="text" name="address2" class="form-control mb-2"
               value="<?php echo e(old('address2', $client->address2)); ?>" placeholder="Address Line 2">
        <input type="text" name="address3" class="form-control mb-2"
               value="<?php echo e(old('address3', $client->address3)); ?>" placeholder="Address Line 3">

        <div class="row">
            <div class="col-md-3">
                <label class="form-label">City</label>
                <select name="city" class="form-select select2-tags">
                    <option value="">Select City</option>
                    <option value="Bangalore" <?php echo e($client->city=='Bangalore'?'selected':''); ?>>Bangalore</option>
                    <option value="Chennai" <?php echo e($client->city=='Chennai'?'selected':''); ?>>Chennai</option>
                    <option value="Hyderabad" <?php echo e($client->city=='Hyderabad'?'selected':''); ?>>Hyderabad</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">State</label>
                <select name="state" class="form-select select2-tags">
                    <option value="">Select State</option>
                    <option value="Karnataka" <?php echo e($client->state=='Karnataka'?'selected':''); ?>>Karnataka</option>
                    <option value="Tamil Nadu" <?php echo e($client->state=='Tamil Nadu'?'selected':''); ?>>Tamil Nadu</option>
                    <option value="Telangana" <?php echo e($client->state=='Telangana'?'selected':''); ?>>Telangana</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Country</label>
                <select name="country" class="form-select select2-tags">
                    <option value="">Select Country</option>
                    <option value="India" <?php echo e($client->country=='India'?'selected':''); ?>>India</option>
                    <option value="USA" <?php echo e($client->country=='USA'?'selected':''); ?>>USA</option>
                    <option value="UK" <?php echo e($client->country=='UK'?'selected':''); ?>>UK</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Pincode</label>
                <input type="text" name="pincode" class="form-control"
                       value="<?php echo e(old('pincode', $client->pincode)); ?>">
            </div>
        </div>

        <h5 class="text-secondary mt-3">Business Contact Details</h5>
        <div class="row">
            <div class="col-md-3">
                <input type="text" name="billing_spoc_name" class="form-control mb-2"
                       value="<?php echo e(old('billing_spoc_name', $client->billing_spoc_name)); ?>"
                       placeholder="Billing SPOC Name">
            </div>
            <div class="col-md-3">
                <input type="text" name="billing_spoc_contact" id="billing_spoc_contact" class="form-control mb-2"
                       value="<?php echo e(old('billing_spoc_contact', $client->billing_spoc_contact)); ?>"
                       placeholder="Contact Number">
            </div>
            <div class="col-md-3">
                <input type="email" name="billing_spoc_email" id="billing_spoc_email" class="form-control mb-2"
                       value="<?php echo e(old('billing_spoc_email', $client->billing_spoc_email)); ?>"
                       placeholder="Email">
            </div>
            <div class="col-md-3">
                <input type="text" name="gstin" id="gstin" class="form-control mb-2"
                       value="<?php echo e(old('gstin', $client->gstin)); ?>" placeholder="GSTIN">
            </div>

            <div class="col-md-3">
                    <!-- <label for="form-label">Billing Sequence</label> -->
                    <select name="billing_sequence" class="form-select">
                        <option value="">select billing sequence</option>
                        <option value="monthly" <?php echo e($client->billing_sequence=='monthly'?'selected':''); ?>>Monthly</option>
                        <option value="quarterly" <?php echo e($client->billing_sequence=='quarterly'?'selected':''); ?>>Quarterly</option>
                        <option value="half-yearly" <?php echo e($client->billing_sequence=='half-yearly'?'selected':''); ?>>Half-Yearly</option>
                        <option value="yearly" <?php echo e($client->billing_sequence=='yearly'?'selected':''); ?>>Yearly</option>
                    </select>
            </div>
        </div>

        <h5 class="text-secondary mt-3">Invoice Details</h5>
        <div class="row mb-3">
            
            <div class="col-md-6">
                <label class="form-label">Invoice Email</label>
                <input type="email" name="invoice_email" class="form-control"
                       value="<?php echo e(old('invoice_email', $client->invoice_email)); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Invoice CC</label>
                <input type="text" name="invoice_cc" class="form-control"
                       value="<?php echo e(old('invoice_cc', $client->invoice_cc)); ?>"
                       placeholder="email1@example.com; email2@example.com">
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Delivered Email</label>
                <input type="email" name="delivered_email" class="form-control"
                       value="<?php echo e(old('delivered_email', $client->delivered_email)); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Delivered CC</label>
                <input type="text" name="delivered_cc" class="form-control"
                       value="<?php echo e(old('delivered_cc', $client->delivered_cc)); ?>"
                       placeholder="email1@example.com; email2@example.com">
            </div>
        </div>

        <h5 class="text-secondary mt-3">Technical Support</h5>
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="support_spoc_name" class="form-control mb-2"
                       value="<?php echo e(old('support_spoc_name', $client->support_spoc_name)); ?>"
                       placeholder="SPOC Name">
            </div>
            <div class="col-md-4">
                <input type="text" name="support_spoc_mobile" class="form-control mb-2"
                       value="<?php echo e(old('support_spoc_mobile', $client->support_spoc_mobile)); ?>"
                       placeholder="Mobile Number">
            </div>
            <div class="col-md-4">
                <input type="email" name="support_spoc_email" id="support_spoc_email" class="form-control mb-2"
                       value="<?php echo e(old('support_spoc_email', $client->support_spoc_email)); ?>"
                       placeholder="Email">
            </div>
        </div>

        <input type="hidden" name="status" value="Active">

        <button type="submit" class="btn btn-warning mt-3">Update Client</button>
        <a href="<?php echo e(route('clients.index')); ?>" class="btn btn-secondary mt-3">Cancel</a>
    </form>
</div>
```

</div>

<script>
// Toggle office fields (EDIT-safe)
function toggleOfficeFields() {
    let type = document.getElementById('office_type').value;
    let panDiv = document.getElementById('panDiv');
    let headOfficeDiv = document.getElementById('headOfficeDiv');
    let clientCodeDiv = document.getElementById('clientCodeDiv');
    let userName = document.getElementById('user_name');
    let displayName = document.getElementById('business_display_name');
    let panNumber = document.getElementById('pan_number');

    if (type === 'head') {
        panDiv.classList.remove('d-none');
        headOfficeDiv.classList.add('d-none');
        clientCodeDiv.classList.remove('d-none');

        userName.required = true;
        displayName.required = true;
        panNumber.required = true;
        panNumber.disabled = false;

    } else if (type === 'branch') {
        panDiv.classList.add('d-none');
        headOfficeDiv.classList.remove('d-none');
        clientCodeDiv.classList.add('d-none');

        userName.required = false;
        displayName.required = false;
        panNumber.required = false;
        panNumber.disabled = true;
    }
}

// Run on page load + change
document.addEventListener('DOMContentLoaded', toggleOfficeFields);
document.getElementById('office_type').addEventListener('change', toggleOfficeFields);
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\clients\edit.blade.php ENDPATH**/ ?>