 



<?php $__env->startSection('content'); ?>

<div class="container py-4"> 

    <h3 class="mb-3 text-primary">Edit Client</h3>

    

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



            

            <div class="col-md-4">

                <label class="form-label">PAN Number</label>

                <input type="text" id="pan_number" name="pan_number" class="form-control" placeholder="Enter PAN Number">

            </div>



            <small id="panStatus" class="text-muted mt-1 d-block"></small>



            

            <div class="col-md-4 mt-3">

                <label class="form-label">Select GST State</label>

                <select id="gst_state" class="form-select select2-tags">

                    <option value="">-- Select State --</option>

                    <option value="29">Karnataka</option>

                    <option value="33">Tamil Nadu</option>

                    <option value="36">Telangana</option>

                    <option value="27">Maharashtra</option>

                    <option value="07">Delhi</option>

                </select>

            </div>



            <small id="gstStatus" class="mt-2 d-block text-muted"></small>



            <hr>



            

            <h5 class="text-secondary mt-4">Basic Details</h5>



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

                <div class="col-md-6">

                    <label class="form-label">Client Code</label>

                    <input type="text" class="form-control" 

                           value="<?php echo e($client->client_code); ?>" readonly> 

                </div>

                <div class="col-md-6">

                    <label class="form-label">User Name</label>

                    <input type="text" name="user_name" class="form-control" 

                           value="<?php echo e(old('user_name', $client->user_name)); ?>" required>

                </div>

            </div>



            

            <div class="mb-3">

                <label class="form-label">Business Display Name</label>

                <input type="text" name="business_display_name" id="business_display_name" class="form-control"

                       value="<?php echo e(old('business_display_name', $client->business_display_name)); ?>">

            </div>



            

            <h5 class="text-secondary mt-3">Address</h5>



            <input type="text" name="address1" id="address1" class="form-control mb-2"

                   value="<?php echo e(old('address1', $client->address1)); ?>" placeholder="Address Line 1">



            <input type="text" name="address2" class="form-control mb-2"

                   value="<?php echo e(old('address2', $client->address2)); ?>" placeholder="Address Line 2">



            <input type="text" name="address3" class="form-control mb-2"

                   value="<?php echo e(old('address3', $client->address3)); ?>" placeholder="Address Line 3">



            

            <div class="row">

                <div class="col-md-4">

                    <label class="form-label">City</label>

                    <select name="city" id="city" class="form-select select2-tags">

                        <option value="">Select or Type City</option>

                        <option value="Bangalore" <?php echo e($client->city == 'Bangalore' ? 'selected' : ''); ?>>Bangalore</option>

                        <option value="Chennai" <?php echo e($client->city == 'Chennai' ? 'selected' : ''); ?>>Chennai</option>

                        <option value="Hyderabad" <?php echo e($client->city == 'Hyderabad' ? 'selected' : ''); ?>>Hyderabad</option>

                    </select>

                </div>



                <div class="col-md-4">

                    <label class="form-label">State</label>

                    <select name="state" id="state" class="form-select select2-tags">

                        <option value="">Select or Type State</option>

                        <option value="Karnataka" <?php echo e($client->state == 'Karnataka' ? 'selected' : ''); ?>>Karnataka</option>

                        <option value="Tamil Nadu" <?php echo e($client->state == 'Tamil Nadu' ? 'selected' : ''); ?>>Tamil Nadu</option>

                        <option value="Telangana" <?php echo e($client->state == 'Telangana' ? 'selected' : ''); ?>>Telangana</option>

                    </select>

                </div>



                <div class="col-md-4">

                    <label class="form-label">Country</label>

                    <select name="country" id="country" class="form-select select2-tags">

                        <option value="">Select Country</option>

                        <option value="India" <?php echo e($client->country == 'India' ? 'selected' : ''); ?>>India</option>

                        <option value="USA" <?php echo e($client->country == 'USA' ? 'selected' : ''); ?>>USA</option>

                        <option value="UK" <?php echo e($client->country == 'UK' ? 'selected' : ''); ?>>UK</option>

                    </select>

                </div>

            </div>



            <br>



            <input type="text" name="pincode" class="form-control mb-3" placeholder="Pincode"

                   value="<?php echo e(old('pincode', $client->pincode)); ?>">



            

            <h5 class="text-secondary mt-3">Business Contact Details</h5>



            <div class="row">

                <div class="col-md-4">

                    <input type="text" name="billing_spoc_name" class="form-control mb-2"

                           value="<?php echo e(old('billing_spoc_name', $client->billing_spoc_name)); ?>"

                           placeholder="Billing SPOC Name">

                </div>



                <div class="col-md-4">

                    <input type="text" name="billing_spoc_contact" id="billing_spoc_contact" class="form-control mb-2"

                           value="<?php echo e(old('billing_spoc_contact', $client->billing_spoc_contact)); ?>"

                           placeholder="Contact Number">

                </div>



                <div class="col-md-4">

                    <input type="email" name="billing_spoc_email" id="billing_spoc_email" class="form-control mb-2"

                           value="<?php echo e(old('billing_spoc_email', $client->billing_spoc_email)); ?>"

                           placeholder="Email">

                </div>

            </div>



            <input type="text" name="gstin" id="gstin" class="form-control mb-3" placeholder="GSTIN"

                   value="<?php echo e(old('gstin', $client->gstin)); ?>">



            

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

                    <small class="text-muted">Use semicolon (;) to separate multiple emails</small>

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

                    <input type="email" name="support_spoc_email" class="form-control mb-2"

                           value="<?php echo e(old('support_spoc_email', $client->support_spoc_email)); ?>"

                           placeholder="Email">

                </div>

            </div>



            

            <input type="hidden" name="status" value="Active">



            

            <button type="submit" class="btn btn-warning mt-3">Update Client</button>

            <a href="<?php echo e(route('clients.index')); ?>" class="btn btn-secondary mt-3">Cancel</a>



        </form>

    </div>

</div>







<script>

function fetchGST() {

    let pan = document.getElementById("pan_number").value.trim();

    let state = document.getElementById("gst_state").value;

    let gstStatus = document.getElementById("gstStatus");



    if (pan.length !== 10 || state === "") {

        gstStatus.innerHTML = "⚠️ Enter valid PAN + Select State";

        return;

    }



    gstStatus.innerHTML = "⏳ Fetching GST details...";



    fetch(`/api/gst/fetch/${pan}/${state}`)

        .then(res => res.json())

        .then(data => {

            if (data.success) {

                document.getElementById("gstin").value = data.data.gstin;

                document.getElementById("business_display_name").value = data.data.trade_name;

                document.getElementById("address1").value = data.data.address;

                document.getElementById("billing_spoc_email").value = data.data.company_email;

                document.getElementById("billing_spoc_contact").value = data.data.company_phone;



                gstStatus.innerHTML = "✅ GST Details Auto-filled!";

            } else {

                gstStatus.innerHTML = "❌ GST Not Found for this PAN + State";

            }

        })

        .catch(() => {

            gstStatus.innerHTML = "⚠️ Server Error";

        });

}



document.getElementById("pan_number").addEventListener("blur", fetchGST);

document.getElementById("gst_state").addEventListener("change", fetchGST);

</script>



<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\wlcome\multipleuserpage\resources\views\clients\edit.blade.php ENDPATH**/ ?>