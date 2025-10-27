

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h3 class="mb-3 text-primary">Add Vendor</h3>
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

        
        <form action="<?php echo e(route('vendors.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
             
             <div class="col-md-4">
    <label class="form-label">PAN Number</label>
    <input type="text" id="pan_number" name="pan_number" class="form-control" 
           placeholder="Enter PAN Number">
           <!-- Button commented out (optional verification trigger) -->
           <!-- <button type="button" id="verifyPanBtn" class="btn btn-primary">Verify</button> -->
</div>
<!--  PAN status message area -->
  <small id="panStatus" class="text-muted mt-1 d-block"></small>

            
            <h5 class="text-secondary">Basic Details</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Vendor Name</label>
                    <input type="text" name="vendor_name" class="form-control" 
                           value="<?php echo e(old('vendor_name')); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Vendor Code</label>
                    <input type="text" class="form-control" value="Auto Generated" readonly>
                </div>
            </div>

            
            <div class="mb-3">
                <label class="form-label">Business Display Name</label>
                <input type="text" name="business_display_name" class="form-control"
                       value="<?php echo e(old('business_display_name')); ?>">
            </div>

            
            <h5 class="text-secondary mt-3">Address</h5>
            <input type="text" name="address1" class="form-control mb-2" placeholder="Address Line 1"
                   value="<?php echo e(old('address1')); ?>">
            <input type="text" name="address2" class="form-control mb-2" placeholder="Address Line 2"
                   value="<?php echo e(old('address2')); ?>">
            <input type="text" name="address3" class="form-control mb-2" placeholder="Address Line 3"
                   value="<?php echo e(old('address3')); ?>">
            
                   
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
                   value="<?php echo e(old('pincode')); ?>">

            
            <h5 class="text-secondary mt-3">Contact Person</h5>
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="contact_person_name" class="form-control mb-2" placeholder="Contact Person Name"
                           value="<?php echo e(old('contact_person_name')); ?>">
                </div>
                <div class="col-md-4">
                    <input type="text" name="contact_person_mobile" class="form-control mb-2" placeholder="Mobile Number"
                           value="<?php echo e(old('contact_person_mobile')); ?>">
                </div>
                <div class="col-md-4">
                    <input type="email" name="contact_person_email" class="form-control mb-2" placeholder="Email"
                           value="<?php echo e(old('contact_person_email')); ?>">
                </div>
            </div>

            
            <h5 class="text-secondary mt-3">Legal Details</h5>
            <div class="row">
                <div class="col-md-6">
                    <input type="text" name="gstin" class="form-control mb-2" placeholder="GSTIN"
                           value="<?php echo e(old('gstin')); ?>">
                </div>
                <div class="col-md-6">
                    <input type="text" name="pan_no" class="form-control mb-2" placeholder="PAN No"
                           value="<?php echo e(old('pan_no')); ?>">
                </div>
                <div class="col-md-6">
                    <input type="text" name="bank_account_no" class="form-control mb-2" placeholder="bank_account_no"
                           value="<?php echo e(old('bank_account_no')); ?>">
                </div>
                <div class="col-md-6">
                    <input type="text" name="ifsc_code" class="form-control mb-2" placeholder="ifsc_code"
                           value="<?php echo e(old('ifsc_code')); ?>">
                </div>
            </div>

            
            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="Active" <?php echo e(old('status') == 'Active' ? 'selected' : ''); ?>>Active</option>
                    <option value="Inactive" <?php echo e(old('status') == 'Inactive' ? 'selected' : ''); ?>>Inactive</option>
                </select>
            </div>

            
            <button type="submit" class="btn btn-success mt-3">Save Vendor</button>
            <a href="<?php echo e(route('vendors.index')); ?>" class="btn btn-secondary mt-3">Cancel</a>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {

    // ✅ GST Auto-Fill
    const gstInput = document.querySelector('[name="gstin"]');
    gstInput.addEventListener('blur', function() {
        let gstin = this.value.trim();
        if (gstin.length === 15) {
            fetch(`/gst/fetch/${gstin}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector('[name="business_display_name"]').value = data.data.tradeNam || '';
                        document.querySelector('[name="pan_no"]').value = data.pan || '';

                        if (data.data.pradr && data.data.pradr.addr) {
                            let addr = data.data.pradr.addr;
                            document.querySelector('[name="address1"]').value = (addr.bnm || '') + ' ' + (addr.st || '');
                            document.querySelector('[name="city"]').value = addr.loc || '';
                            document.querySelector('[name="state"]').value = addr.stcd || '';
                        }
                        alert("✅ Company details filled successfully!");
                    } else {
                        alert("❌ Invalid GST Number");
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("⚠️ Error fetching GST details");
                });
        }
    });

    // ✅ Verify PAN button click
    const verifyPanBtn = document.getElementById('verifyPanBtn');
    const panInput = document.getElementById('pan_number');
    const panStatus = document.getElementById('panStatus');

    verifyPanBtn.addEventListener('click', function() {
        let pan = panInput.value.trim().toUpperCase();
        // 🧩 Basic validation (PAN must be 10 chars)
        if (pan.length !== 10) {
            panStatus.innerHTML = '<span class="text-danger">⚠️ Enter a valid 10-character PAN number</span>';
            return;
        }

        // 🕐 Disable button while verifying
        verifyPanBtn.disabled = true;
        verifyPanBtn.textContent = "Verifying...";
        panStatus.textContent = "";

        // 🌐 Call Laravel route for PAN check
        fetch(`/company/fetch/${pan}`)
            .then(res => res.json())
            .then(data => {
                verifyPanBtn.disabled = false;
                verifyPanBtn.textContent = "Verify";

                if (data.success) {
                    // ✅ Fill data from company table
                    let c = data.data;
                    document.querySelector('[name="gstin"]').value = c.gst_no || '';
                    document.querySelector('[name="business_display_name"]').value = c.company_name || '';
                    document.querySelector('[name="contact_person_email"]').value = c.email_1 || '';
                    document.querySelector('[name="address1"]').value = c.address_line1 || '';

                    panStatus.innerHTML = '<span class="text-success">✅ PAN Verified & details filled!</span>';
                } else {
                    panStatus.innerHTML = '<span class="text-danger">❌ No company found for this PAN</span>';
                }
            })
            .catch(err => {
                verifyPanBtn.disabled = false;
                verifyPanBtn.textContent = "Verify";
                console.error(err);
                panStatus.innerHTML = '<span class="text-danger">⚠️ Error verifying PAN number</span>';
            });
    });

});
</script>


<script src="<?php echo e(asset('js/gst_pan_autofill.js')); ?>"></script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\new\multipleuserpage\resources\views/vendors/create.blade.php ENDPATH**/ ?>