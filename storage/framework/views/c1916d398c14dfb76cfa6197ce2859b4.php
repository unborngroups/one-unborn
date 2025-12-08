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

                <input type="text" id="pan_number" name="pan_number" class="form-control" placeholder="Enter PAN Number">

            </div>



            <small id="panStatus" class="text-muted mt-1 d-block"></small>



            



            <small id="gstStatus" class="mt-2 d-block text-muted"></small>



            <hr>



            

            <h5 class="text-secondary">Basic Details</h5>


            
            <div class="row mb-3">

                <div class="col-md-3">

                    <label class="form-label">Client Name</label>

                    <input type="text" name="client_name" class="form-control" required>

                </div>

               <!--  -->

                <div class="col-md-3">

                    <label class="form-label">Client Code</label>

                    <input type="text" class="form-control" value="Auto Generated" readonly>

                </div>
                <!--  -->
                <div class="col-md-3">

                    <label class="form-label">User Name</label>
<div class="input-group">
    <input type="text" name="user_name" id="user_name" class="form-control" required>
    <button type="button" name="portal_password" id="sendPwdBtn" class="btn btn-outline-primary">PWD</button>
</div>
<small id="pwdStatus" class="text-muted d-block mt-1"></small>

                </div>
                

            <div class="col-md-3 mb-3">

                <label class="form-label">Business Display Name</label>

                <input type="text" name="business_display_name" id="business_display_name" class="form-control">

            </div>

            </div>

            

            <h5 class="text-secondary mt-3">Address</h5>



            <input type="text" name="address1" id="address1" class="form-control mb-2" placeholder="Address Line 1">

            <input type="text" name="address2" class="form-control mb-2" placeholder="Address Line 2">

            <input type="text" name="address3" class="form-control mb-2" placeholder="Address Line 3">



            <div class="row p-2">

                <div class="col-md-3">

                    <label class="form-label">City</label>

                    <select name="city" class="form-select select2-tags">

                        <option value="">Select or Type City</option>

                        <option>Bangalore</option>

                        <option>Chennai</option>

                        <option>Hyderabad</option>

                    </select>

                </div>



                <div class="col-md-3">

                    <label class="form-label">State</label>

                    <select name="state" class="form-select select2-tags">

                        <option value="">Select or Type State</option>

                        <option>Karnataka</option>

                        <option>Tamil Nadu</option>

                        <option>Telangana</option>

                    </select>

                </div>



                <div class="col-md-3">

                    <label class="form-label">Country</label>

                    <select name="country" class="form-select select2-tags">

                        <option value="">Select or Type Country</option>

                        <option>India</option>

                        <option>USA</option>

                        <option>UK</option>

                    </select>

                </div>
                <div class="col-md-3">
                    <label for="">Pincode</label>
            <input type="text" name="pincode" class="form-control mb-3 mt-1" placeholder="Pincode">
                    
                </div>

            </div>

            

            <h5 class="text-secondary mt-3">Business Contact Details</h5>



            <div class="row">

                <div class="col-md-3">

                    <input type="text" name="billing_spoc_name" class="form-control mb-2" placeholder="Billing SPOC Name">

                </div>



                <div class="col-md-3">

                    <input type="text" name="billing_spoc_contact" id="billing_spoc_contact" class="form-control mb-2" placeholder="Contact Number">

                </div>



                <div class="col-md-3">

                    <input type="email" name="billing_spoc_email" id="billing_spoc_email" class="form-control mb-2" placeholder="Email">

                </div>

                <div class="col-md-3">
            <input type="text" name="gstin" id="gstin" class="form-control mb-3" placeholder="GSTIN">
                    
                </div>

            </div>



            




            

            <h5 class="text-secondary mt-3">Invoice Details</h5>



            <div class="row mb-3">

                <div class="col-md-6">

                    <label class="form-label">Invoice Email</label>

                    <input type="email" name="invoice_email" class="form-control">

                </div>



                <div class="col-md-6">

                    <label class="form-label">Invoice CC</label>

                    <input type="text" name="invoice_cc" class="form-control" 

                           placeholder="email1@example.com; email2@example.com">

                    <small class="text-muted">Use semicolon (;) to separate multiple emails</small>

                </div>

            </div>



            

            <h5 class="text-secondary mt-3">Technical Support</h5>



            <div class="row">

                <div class="col-md-4">

                    <input type="text" name="support_spoc_name" class="form-control mb-2" placeholder="SPOC Name">

                </div>



                <div class="col-md-4">

                    <input type="text" name="support_spoc_mobile" class="form-control mb-2" placeholder="Mobile Number">

                </div>



                <div class="col-md-4">

                    <input type="email" name="support_spoc_email" class="form-control mb-2" placeholder="Email">

                </div>
                
            </div>



            <input type="hidden" name="status" value="Active">



            <button type="submit" class="btn btn-success mt-3">Save Client</button>

            <a href="<?php echo e(route('clients.index')); ?>" class="btn btn-secondary mt-3">Cancel</a>

        </form>

    </div>

</div>





<script>

// GST Fetch
function fetchGST() {
    let pan = document.getElementById("pan_number").value.trim();
    let gstStatus = document.getElementById("gstStatus");

    if (pan.length !== 10) {
        gstStatus.innerHTML = "⚠️ Enter valid PAN";
        return;
    }

    gstStatus.innerHTML = "⏳ Fetching GST details...";

    fetch(`/api/gst/fetch/${pan}`)
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
                gstStatus.innerHTML = "❌ GST Not Found for this PAN";
            }
        })
        .catch(() => gstStatus.innerHTML = "⚠ Server Error");
}

document.getElementById("pan_number").addEventListener("blur", fetchGST);


// ⭐ Password Send Btn
document.getElementById("sendPwdBtn").addEventListener("click", function () {
    let email = document.getElementById("billing_spoc_email").value;
    let userName = document.getElementById("user_name").value;
    let pwdStatus = document.getElementById("pwdStatus");

    if (!email) {
        pwdStatus.innerHTML = "⚠️ Enter Billing Email first!";
        setTimeout(() => { pwdStatus.innerHTML = ""; }, 4000);
        return;
    }

    if (!userName) {
        pwdStatus.innerHTML = "⚠️ Enter Username!";
        setTimeout(() => { pwdStatus.innerHTML = ""; }, 4000);
        return;
    }

    pwdStatus.innerHTML = "⏳ Sending password...";

    fetch("/api/client/send-password", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify({ email: email, user_name: userName })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            pwdStatus.innerHTML = "✔ Password sent!";
        } else {
            pwdStatus.innerHTML = "❌ " + data.message;
        }
        setTimeout(() => { pwdStatus.innerHTML = ""; }, 4000);
    })
    .catch(() => {
        pwdStatus.innerHTML = "⚠ Server error";
        setTimeout(() => { pwdStatus.innerHTML = ""; }, 4000);
    });
});

</script>



<script src="<?php echo e(asset('js/gstin-fetch.js')); ?>"></script>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\clients\create.blade.php ENDPATH**/ ?>