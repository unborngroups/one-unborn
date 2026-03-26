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

              

            <!-- <div class="col-md-4">

                <label class="form-label">PAN Number</label>

                <input type="text" id="pan_number" name="pan_number" class="form-control" placeholder="Enter PAN Number">

            </div>



            <small id="panStatus" class="text-muted mt-1 d-block"></small> -->



            



            <small id="gstStatus" class="mt-2 d-block text-muted"></small>



            <hr>

            

            <h5 class="text-secondary">Basic Details</h5>

            <div class="row mb-3">

                <div class="col-md-3">

                    <label class="form-label">Vendor Name</label>

                    <input type="text" name="vendor_name" id="vendor_name" class="form-control" 

                           value="<?php echo e(old('vendor_name')); ?>" required>

                </div>

                <div class="col-md-3">

                    <label class="form-label">Vendor Code</label>

                    <input type="text" class="form-control" value="Auto Generated" readonly>

                </div>

                <div class="col-md-3">

                    <label class="form-label">User Name</label>

                    <input type="text" name="user_name" class="form-control" required>

                </div>
                 

            <div class=" col-md-3 mb-3">

                <label class="form-label">Business Display Name</label>

                <input type="text" name="business_display_name" id="business_display_name" class="form-control"

                       value="<?php echo e(old('business_display_name')); ?>">

            </div>

          
            </div>      



            

            <h5 class="text-secondary mt-3">Address</h5>

            <input type="text" name="address1" id="address1" class="form-control mb-2" placeholder="Address Line 1"

                   value="<?php echo e(old('address1')); ?>">

            <input type="text" name="address2" class="form-control mb-2" placeholder="Address Line 2"

                   value="<?php echo e(old('address2')); ?>">

            <input type="text" name="address3" class="form-control mb-2" placeholder="Address Line 3"

                   value="<?php echo e(old('address3')); ?>">

            

                   

             <div class="row">

    <div class="col-md-3">

        <label class="form-label">City</label>

        <select name="city"  class="form-select select2-tags">

            <option value="">Select or Type City</option>

            <option value="Bangalore">Bangalore</option>

            <option value="Chennai">Chennai</option>

            <option value="Hyderabad">Hyderabad</option>

        </select>

    </div>



    <div class="col-md-3">

        <label class="form-label">State</label>

        <select name="state"  class="form-select select2-tags">

            <option value="">Select or Type State</option>

            <option value="Karnataka">Karnataka</option>

            <option value="Tamil Nadu">Tamil Nadu</option>

            <option value="Telangana">Telangana</option>

        </select>

    </div>



    <div class="col-md-3">

        <label class="form-label">Country</label>

        <select name="country" id="" class="form-select select2-tags">

            <option value="">Select or Type Country</option>

            <option value="India">India</option>

            <option value="USA">USA</option>

            <option value="UK">UK</option>

        </select>

    </div>
    <div class="col-md-3">
        <label for="pincode">Pincode</label>
          <input type="text" name="pincode" id="pincode" class="form-control mb-3" placeholder="Pincode"
                   value="<?php echo e(old('pincode')); ?>">
    </div>
</div>
                <!-- product Category -->
                 <!-- resources/views/vendors/form.blade.php (or create.blade.php) -->
            

            <h5 class="text-dark-primary mt-3">Contact Person</h5>

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

                <div class="col-md-4">

                    <input type="text" name="gstin" id="gstin" class="form-control mb-2" placeholder="GSTIN"

                           value="<?php echo e(old('gstin')); ?>">

                </div>

                <div class="col-md-4">

                    <input type="text" name="pan_no" class="form-control mb-2" placeholder="PAN No"

                           value="<?php echo e(old('pan_no')); ?>">

                </div>

                <div class="col-md-4">

                    <input type="text" name="branch_name" class="form-control mb-2" placeholder="branch_name"

                           value="<?php echo e(old('branch_name')); ?>">

                </div>

                <div class="col-md-4">

                    <input type="text" name="bank_name" class="form-control mb-2" placeholder="bank_name"

                           value="<?php echo e(old('bank_name')); ?>">

                </div>

                <div class="col-md-4">

                    <input type="text" name="bank_account_no" class="form-control mb-2" placeholder="bank_account_no"

                           value="<?php echo e(old('bank_account_no')); ?>">

                </div>

                <div class="col-md-4">

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



<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>



<script>
document.getElementById('make_id').addEventListener('change', function () {
    let selected = this.options[this.selectedIndex];

    document.getElementById('company_name').value = selected.getAttribute('data-company') || '';
    document.getElementById('make_contact_no').value = selected.getAttribute('data-contact') || '';
    document.getElementById('make_email').value = selected.getAttribute('data-email') || '';
});


function fetchGST() {

    let pan = document.getElementById("pan_number").value.trim();

    let state = document.getElementById("gst_state").value;

    let gstStatus = document.getElementById("gstStatus");



    if (pan.length !== 10 || state === "") {

        gstStatus.innerHTML = "⚠ Enter valid PAN + Select State";

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

            gstStatus.innerHTML = "⚠ Server Error";

        });

}



document.getElementById("pan_number").addEventListener("blur", fetchGST);

document.getElementById("gst_state").addEventListener("change", fetchGST);

<!-- include jQuery -->

document.addEventListener('DOMContentLoaded', function () {
    const makeSelect = document.getElementById('make_id');
    const companyInput = document.getElementById('company_name');
    const contactInput = document.getElementById('make_contact_no');
    const emailInput = document.getElementById('make_email');
    const assetInput = document.getElementById('asset_id');
    const barcodePreview = document.getElementById('barcode_preview');

    makeSelect?.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        if (!selected || !selected.value) {
            companyInput.value = '';
            contactInput.value = '';
            emailInput.value = '';
            return;
        }

        // Use preloaded data attributes to auto-fill immediately
        companyInput.value = selected.dataset.company || '';
        contactInput.value = selected.dataset.contact || '';
        emailInput.value = selected.dataset.email || '';

        // Optionally request server to compute AssetID immediately (AJAX)
        fetch("<?php echo e(url('vendor-makes')); ?>/" + selected.value)
            .then(res => res.json())
            .then(data => {
                // server returned make + company name — request generated asset id from backend (optional)
                // We'll generate possible asset preview by calling dedicated endpoint or leave blank and let server generate on save.
            });
    });

    // Generate barcode image on click for the shown asset id
    document.getElementById('generate_barcode')?.addEventListener('click', function () {
        const asset = assetInput.value.trim();
        if (!asset) {
            alert('Asset ID not yet generated. Save vendor to auto-generate Asset ID, then click Generate Barcode.');
            return;
        }
        barcodePreview.innerHTML = "<img src=\"<?php echo e(url('vendors')); ?>/" + asset + "/barcode.png\" alt=\"Barcode\">";
    });
});

$('#make_id').change(function () {
    var makeId = $(this).val();
    if(makeId){
        $.ajax({
            url: "/get-make-details/" + makeId,
            type: "GET",
            success: function (data) {
                $('#company_name').val(data.company_name);
                $('#make_contact_no').val(data.contact_no);
                $('#make_email').val(data.email_id);
            }
        });
    }
});


// start pincode lookup--------------------------------------------------
// Pincode lookup function

function lookupPincode() {

  const pincodeField = document.getElementById('pincode');

  const p = pincodeField.value.trim();

  

  // Only proceed if we have exactly 6 digits

  if (!/^\d{6}$/.test(p)) return;

  

  // Get field references

  const stateField = document.getElementById('state');

  const districtField = document.getElementById('district');

  const areaField = document.getElementById('post_office');

  

  // Store original values in case of error

  const originalState = stateField.value;

  const originalDistrict = districtField.value;

  const originalArea = areaField.value;

  

  // Show loading state

  setSelectValue(stateField, 'Loading...');

  setSelectValue(districtField, 'Loading...');

  setSelectValue(areaField, 'Loading...');

  

  console.log('🔍 Looking up pincode:', p);

  

  // Make API call

  axios.post('/api/pincode/lookup', { pincode: p })

    .then(r => {

      const d = r.data;

      console.log('✅ Pincode lookup successful:', d);

      console.log('State field element:', stateField);

      console.log('District field element:', districtField);

      console.log('Area field element:', areaField);

      

      // Update fields with fetched data

      console.log('Setting state to:', d.state);

      setSelectValue(stateField, d.state || '');

      

      console.log('Setting district to:', d.district);

      setSelectValue(districtField, d.district || '');

      

      console.log('Setting area to:', d.post_office);

      setSelectValue(areaField, d.post_office || '');

      

      // Show success message briefly

      const notification = document.createElement('div');

      notification.style.cssText = `

        position: fixed; top: 20px; right: 20px; 

        background: #d4edda; color: #155724; 

        padding: 10px 15px; border-radius: 5px; 

        border: 1px solid #c3e6cb; z-index: 9999;

        font-size: 14px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);

      `;

      notification.innerHTML = `✅ Location found: ${d.state}, ${d.district}`;

      document.body.appendChild(notification);

      

      // Remove notification after 3 seconds

      setTimeout(() => {

        if (notification.parentNode) {

          notification.parentNode.removeChild(notification);

        }

      }, 3000);

    })

    .catch(err => {

      console.error('❌ Pincode lookup failed:', err);

      

      // Restore original values

      setSelectValue(stateField, originalState);

      setSelectValue(districtField, originalDistrict);

      setSelectValue(areaField, originalArea);

      

      // Show error message

      let errorMessage = 'Unable to fetch pincode details. Please try again or enter manually.';

      if (err.response && err.response.status === 404) {

        errorMessage = 'Pincode not found. Please check the pincode and try again.';

      } else if (err.response && err.response.status === 422) {

        errorMessage = 'Invalid pincode format. Please enter a 6-digit pincode.';

      }

      

      // Show error notification

      const errorNotification = document.createElement('div');

      errorNotification.style.cssText = `

        position: fixed; top: 20px; right: 20px; 

        background: #f8d7da; color: #721c24; 

        padding: 10px 15px; border-radius: 5px; 

        border: 1px solid #f5c6cb; z-index: 9999;

        font-size: 14px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);

      `;

      errorNotification.innerHTML = `❌ ${errorMessage}`;

      document.body.appendChild(errorNotification);

      

      // Remove error notification after 5 seconds

      setTimeout(() => {

        if (errorNotification.parentNode) {

          errorNotification.parentNode.removeChild(errorNotification);

        }

      }, 5000);

    });

}
// Add multiple event listeners for better responsiveness

const pincodeInput = document.getElementById('pincode');

// Trigger on blur (when user clicks outside the field)

pincodeInput.addEventListener('blur', lookupPincode);

function setSelectValue(select, value) {

    if (!value) return;

    let option = Array.from(select.options).find(o => o.value === value);

    if (!option) {
        let newOption = new Option(value, value, true, true);
        select.add(newOption);
    }

    select.value = value;

// 🔥 IMPORTANT for select2 refresh
    if ($(select).hasClass("select2-hidden-accessible")) {
        $(select).trigger('change');
    }
}

//end pincode lookup-----------------------------------


</script>



<script src="<?php echo e(asset('js/gstin-fetch-vendor.js')); ?>"></script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/vendors/create.blade.php ENDPATH**/ ?>