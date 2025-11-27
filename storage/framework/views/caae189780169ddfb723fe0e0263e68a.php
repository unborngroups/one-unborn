



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

            

            <h5 class="text-secondary">Basic Details</h5>

            <div class="row mb-3">

                <div class="col-md-6">

                    <label class="form-label">Vendor Name</label>

                    <input type="text" name="vendor_name" id="vendor_name" class="form-control" 

                           value="<?php echo e(old('vendor_name')); ?>" required>

                </div>

                <div class="col-md-6">

                    <label class="form-label">Vendor Code</label>

                    <input type="text" class="form-control" value="Auto Generated" readonly>

                </div>

                <div class="col-md-6">

                    <label class="form-label">User Name</label>

                    <input type="text" name="user_name" class="form-control" required>

                </div>

            </div>



            

            <div class="mb-3">

                <label class="form-label">Business Display Name</label>

                <input type="text" name="business_display_name" id="business_display_name" class="form-control"

                       value="<?php echo e(old('business_display_name')); ?>">

            </div>



            

            <h5 class="text-secondary mt-3">Address</h5>

            <input type="text" name="address1" id="address1" class="form-control mb-2" placeholder="Address Line 1"

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

                    <input type="text" name="contact_person_mobile" id="contact_person_mobile" class="form-control mb-2" placeholder="Mobile Number"

                           value="<?php echo e(old('contact_person_mobile')); ?>">

                </div>

                <div class="col-md-4">

                    <input type="email" name="contact_person_email" id="contact_person_email" class="form-control mb-2" placeholder="Email"

                           value="<?php echo e(old('contact_person_email')); ?>">

                </div>

            </div>



            

            <h5 class="text-secondary mt-3">Legal Details</h5>

            <div class="row">

                <div class="col-md-6">

                    <input type="text" name="gstin" id="gstin" class="form-control mb-2" placeholder="GSTIN"

                           value="<?php echo e(old('gstin')); ?>">

                </div>

                <div class="col-md-6">

                    <input type="text" name="pan_no" class="form-control mb-2" placeholder="PAN No"

                           value="<?php echo e(old('pan_no')); ?>">

                </div>

                <div class="col-md-6">

                    <input type="text" name="branch_name" class="form-control mb-2" placeholder="branch_name"

                           value="<?php echo e(old('branch_name')); ?>">

                </div>

                <div class="col-md-6">

                    <input type="text" name="bank_name" class="form-control mb-2" placeholder="bank_name"

                           value="<?php echo e(old('bank_name')); ?>">

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



            

            <!-- <div class="mb-3">

                <label>Status</label>

                <select name="status" class="form-control">

                    <option value="Active" <?php echo e(old('status') == 'Active' ? 'selected' : ''); ?>>Active</option>

                    <option value="Inactive" <?php echo e(old('status') == 'Inactive' ? 'selected' : ''); ?>>Inactive</option>

                </select>

                

            </div> -->

            <input type="hidden" name="status" value="Active">

    



            

            <button type="submit" class="btn btn-success mt-3">Save Vendor</button>

            <a href="<?php echo e(route('vendors.index')); ?>" class="btn btn-secondary mt-3">Cancel</a>

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


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\wlcome\multipleuserpage\resources\views\vendors\create.blade.php ENDPATH**/ ?>