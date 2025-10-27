 

<?php $__env->startSection('content'); ?>
<div class="container py-4"> 
    <h3 class="mb-3 text-primary">Add Client</h3>
    
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

        
        <form action="<?php echo e(route('clients.store')); ?>" method="POST">
            <?php echo csrf_field(); ?> 
            <div class="col-md-4">
    <label class="form-label">PAN Number</label>
    <input type="text" id="pan_number" name="pan_number" class="form-control" 
           placeholder="Enter PAN Number">
           <!-- <button type="button" id="verifyPanBtn" class="btn btn-primary">Verify</button> -->
</div>
<!-- 👇 ADD THIS LINE (important!) -->
  <small id="panStatus" class="text-muted mt-1 d-block"></small>


            
            <h5 class="text-secondary">Basic Details</h5>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Client Name</label>
                    <input type="text" name="client_name" class="form-control" 
                           value="<?php echo e(old('client_name')); ?>" required> 
                </div>
                <div class="col-md-6">
                    <label class="form-label">Client Code</label>
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

            
            <h5 class="text-secondary mt-3">Business Contact Details</h5>
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="billing_spoc_name" class="form-control mb-2" placeholder="Billing SPOC Name"
                           value="<?php echo e(old('billing_spoc_name')); ?>">
                </div>
                <div class="col-md-4">
                    <input type="text" name="billing_spoc_contact" class="form-control mb-2" placeholder="Contact Number"
                           value="<?php echo e(old('billing_spoc_contact')); ?>">
                </div>
                <div class="col-md-4">
                    <input type="email" name="billing_spoc_email" class="form-control mb-2" placeholder="Email"
                           value="<?php echo e(old('billing_spoc_email')); ?>">
                </div>
            </div>

            <input type="text" name="gstin" id="gstin" class="form-control mb-3" placeholder="GSTIN"
                   value="<?php echo e(old('gstin')); ?>">

            
            <h5 class="text-secondary mt-3">Invoice Details</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Invoice Email</label>
                    <input type="email" name="invoice_email" class="form-control"
                           value="<?php echo e(old('invoice_email')); ?>" placeholder="Enter invoice email">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Invoice CC</label>
                    <input type="email" name="invoice_cc" class="form-control"
                           value="<?php echo e(old('invoice_cc')); ?>" placeholder="Enter invoice CC email">
                </div>
            </div>

            
            <h5 class="text-secondary mt-3">Technical Support</h5>
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="support_spoc_name" class="form-control mb-2" placeholder="SPOC Name"
                           value="<?php echo e(old('support_spoc_name')); ?>">
                </div>
                <div class="col-md-4">
                    <input type="text" name="support_spoc_mobile" class="form-control mb-2" placeholder="Mobile Number"
                           value="<?php echo e(old('support_spoc_mobile')); ?>">
                </div>
                <div class="col-md-4">
                    <input type="email" name="support_spoc_email" class="form-control mb-2" placeholder="Email"
                           value="<?php echo e(old('support_spoc_email')); ?>">
                </div>
            </div>

            
            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>

            
            <button type="submit" class="btn btn-success mt-3">Save Client</button> 
            <a href="<?php echo e(route('clients.index')); ?>" class="btn btn-secondary mt-3">Cancel</a> 
        </form>
    </div>
</div>
<!-- GST API -->
<script>
document.addEventListener("DOMContentLoaded", () => {

    const gstInput = document.querySelector('[name="gstin"]');
    const panInput = document.querySelector('[name="pan_number"]');
    const verifyPanBtn = document.getElementById("verifyPanBtn");
    const panStatus = document.getElementById("panStatus");

    /**
     * 🔹 GST Fetch & Auto-fill
     */
    gstInput?.addEventListener('blur', function() {
        let gstin = this.value.trim();
        if (gstin.length === 15) {
            fetch(`/gst/fetch/${gstin}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Auto-fill company info
                        document.querySelector('[name="business_display_name"]').value = data.data.tradeNam || '';
                        document.querySelector('[name="pan_number"]').value = data.pan || '';

                        if (data.data.pradr && data.data.pradr.addr) {
                            const addr = data.data.pradr.addr;
                            document.querySelector('[name="address1"]').value = `${addr.bnm || ''} ${addr.st || ''}`.trim();
                            document.querySelector('[name="city"]').value = addr.loc || '';
                            document.querySelector('[name="state"]').value = addr.stcd || '';
                        }

                        alert("✅ GST details fetched successfully!");
                    } else {
                        alert("❌ Invalid GST Number");
                    }
                })
                .catch(err => {
                    console.error("GST Fetch Error:", err);
                    alert("⚠️ Error fetching GST details");
                });
        }
    });

    /**
     * 🔹 PAN Fetch & Auto-fill
     */
    const verifyPan = () => {
        const pan = panInput.value.trim();
        if (pan.length === 10) {
            panStatus.innerHTML = "⏳ Verifying PAN...";
            fetch(`/company/fetch/${pan}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const c = data.data;
                        document.querySelector('[name="gstin"]').value = c.gst_no || '';
                        document.querySelector('[name="business_display_name"]').value = c.company_name || '';
                        document.querySelector('[name="billing_spoc_email"]').value = c.email_1 || '';
                        document.querySelector('[name="address1"]').value = c.address || '';

                        panStatus.innerHTML = "✅ Company details auto-filled!";
                        panStatus.classList.add("text-success");
                        panStatus.classList.remove("text-danger");
                    } else {
                        panStatus.innerHTML = "❌ No company found for this PAN";
                        panStatus.classList.add("text-danger");
                        panStatus.classList.remove("text-success");
                    }
                })
                .catch(err => {
                    console.error("PAN Fetch Error:", err);
                    panStatus.innerHTML = "⚠️ Error verifying PAN";
                    panStatus.classList.add("text-danger");
                    panStatus.classList.remove("text-success");
                });
        } else {
            panStatus.innerHTML = "⚠️ Enter valid 10-character PAN";
            panStatus.classList.add("text-danger");
        }
    };

    // 🔘 Click Verify button
    verifyPanBtn?.addEventListener('click', verifyPan);

    // 🧠 Also verify automatically on blur
    panInput?.addEventListener('blur', verifyPan);

});
</script>
<!-- GST & PAN Autofill Script -->
<script src="<?php echo e(asset('js/gst_pan_autofill.js')); ?>"></script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\new\multipleuserpage\resources\views/clients/create.blade.php ENDPATH**/ ?>