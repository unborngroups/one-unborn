



<?php $__env->startSection('content'); ?>

<div class="container-fluid py-4">

    <h4 class="text-primary fw-bold mb-3 ">Add Feasibility</h4>
    
        <?php if(session('import_errors')): ?>

            <div class="alert alert-warning">
                <strong>Import could not process some rows:</strong>
                <ul class="mb-0">
                    <?php $__currentLoopData = session('import_errors'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $importError): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($importError); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>

        <?php endif; ?>

        <?php
            $importRow = session('imported_row', []);
        ?>
     
    <!-- <h5 class="mb-3 ">Import Feasibility</h5> -->
        <div class="row g-3 mb-3">
            <div class="col-md-12">
                <button class="btn btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#importCard" aria-expanded="false" aria-controls="importCard">
                    Import Feasibility
                </button>
                <div class="collapse mt-3" id="importCard">
                    <div class="card border-info">
                        <div class="card-body">
                            <p class="mb-3 small text-muted">Download the sample format, populate it with feasibility data, and then upload it via Import Excel.</p>
                            <form action="<?php echo e(route('feasibility.import')); ?>" method="POST" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                <div class="input-group">
                                    <input type="file" name="file" class="form-control" required>
                                    <a href="<?php echo e(asset('images/feasibilityimport/feasibility_import_69255ee5caa5f.xlsx')); ?>" target="_blank" class="btn btn-outline-secondary" title="Download import template">Download Format</a>
                                    <button type="submit" class="btn btn-primary">Import Excel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>



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

        <?php if(session('success')): ?>

            <div class="alert alert-success">
                <?php echo e(session('success')); ?>

            </div>

        <?php endif; ?>


<!--         

        <h5 class="mb-3">Import Feasibility</h5>
        <div class="row g-3 align-items-center ml-2 mb-4">
            <div class="col-md-4">
                <form action="<?php echo e(route('feasibility.import')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="input-group">
                        <input type="file" name="file" class="form-control" required>
                        <button type="submit" class="btn btn-primary">Import Excel</button>
                    </div>
                </form>
            </div>
    </div>
 -->



         

        <form action="<?php echo e(route('feasibility.store')); ?>" method="POST">

            <?php echo csrf_field(); ?>

            <div class="row g-3">



                <div class="col-md-3">

                    <label class="form-label fw-semibold">Feasibility Request ID</label>

                    <input type="text" class="form-control bg-light" value="Auto-generated" readonly>

                    <!-- <small class="text-muted">ID will be generated automatically when saved</small> -->

                </div>



                <div class="col-md-3">

                    <label class="form-label fw-semibold">Type of Service <span class="text-danger">*</span></label>

                    <?php $typeSelection = old('type_of_service', $importRow['type_of_service'] ?? ''); ?>
                    <select name="type_of_service" id="type_of_service" class="form-select" required>

                        <option value="" <?php echo e($typeSelection === '' ? 'selected' : ''); ?>>Select</option>

                        <option value="Broadband" <?php echo e($typeSelection === 'Broadband' ? 'selected' : ''); ?>>Broadband</option>

                        <option value="ILL" <?php echo e($typeSelection === 'ILL' ? 'selected' : ''); ?>>ILL</option>

                        <option value="P2P" <?php echo e($typeSelection === 'P2P' ? 'selected' : ''); ?>>P2P</option>

                    </select>

                </div>



                <div class="col-md-3">

                    <label class="form-label fw-semibold">Company <span class="text-danger">*</span></label>

                    <select name="company_id" id="company_id" class="form-select" required>

                        <option value="">Select Company</option>

                        <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <option value="<?php echo e($company->id); ?>" <?php echo e((string) old('company_id', $importRow['company_id'] ?? '') === (string) $company->id ? 'selected' : ''); ?>><?php echo e($company->company_name); ?></option>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </select>

                </div>



                <div class="col-md-3">

                    <label class="form-label fw-semibold">Client Name <span class="text-danger">*</span></label>

                    <select name="client_id" id="client_id" class="form-select" required>

                        <option value="">Select Client</option>

                        <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <option value="<?php echo e($client->id); ?>" <?php echo e((string) old('client_id', $importRow['client_id'] ?? '') === (string) $client->id ? 'selected' : ''); ?>><?php echo e($client->business_name ?: $client->client_name); ?></option>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </select>

                </div>





                <div class="col-md-3">

                    <label class="form-label fw-semibold">Pincode <span class="text-danger">*</span></label>

                    <input type="text" name="pincode" id="pincode" maxlength="6" class="form-control" required value="<?php echo e(old('pincode', $importRow['pincode'] ?? '')); ?>">

           <!-- <button type="button" id="pincodeVerifyBtn" class="btn btn-primary">Verify</button> -->

                </div>



                <div class="col-md-3">

                    <label class="form-label fw-semibold">State <span class="text-danger">*</span></label>

                    <?php $stateValue = old('state', $importRow['state'] ?? ''); ?>
                    <select name="state" id="state" class="form-select select2-tags">

                        <option value="" <?php echo e($stateValue === '' ? 'selected' : ''); ?>>Select or Type State</option>

                        <option value="Karnataka" <?php echo e($stateValue === 'Karnataka' ? 'selected' : ''); ?>>Karnataka</option>

                        <option value="Tamil Nadu" <?php echo e($stateValue === 'Tamil Nadu' ? 'selected' : ''); ?>>Tamil Nadu</option>

                        <option value="Telangana" <?php echo e($stateValue === 'Telangana' ? 'selected' : ''); ?>>Telangana</option>

                    </select>

                </div>



                <div class="col-md-3">

                    <label class="form-label fw-semibold">District <span class="text-danger">*</span></label>

                   <?php $districtValue = old('district', $importRow['district'] ?? ''); ?>
                   <select name="district" id="district" class="form-select select2-tags">

                        <option value="" <?php echo e($districtValue === '' ? 'selected' : ''); ?>>Select or Type District</option>

                        <option value="Salem" <?php echo e($districtValue === 'Salem' ? 'selected' : ''); ?>>Salem</option>

                        <option value="Dharmapuri" <?php echo e($districtValue === 'Dharmapuri' ? 'selected' : ''); ?>>Dharmapuri</option>

                        <option value="Erode" <?php echo e($districtValue === 'Erode' ? 'selected' : ''); ?>>Erode</option>

                    </select>

                </div>



                <div class="col-md-3">

                    <label class="form-label fw-semibold">Area <span class="text-danger">*</span></label>
                    <?php $areaValue = old('area', $importRow['area'] ?? ''); ?>
                      
                    <select name="area" id="post_office" class="form-select select2-tags">

                        <option value="">Select or Type Area</option>

                        <option value="Uthagarai" <?php echo e($areaValue === 'Uthagarai' ? 'selected' : ''); ?>>Uthagarai</option>

                        <option value="Harur" <?php echo e($areaValue === 'Harur' ? 'selected' : ''); ?>>Harur</option>

                        <option value="Kottaiyur" <?php echo e($areaValue === 'Kottaiyur' ? 'selected' : ''); ?>>Kottaiyur</option>
                    </select>

                </div>



                <div class="col-md-3">

                    <label class="form-label fw-semibold">Address <span class="text-danger">*</span></label>

                    <textarea name="address" class="form-control" rows="1" required><?php echo e(old('address', $importRow['address'] ?? '')); ?></textarea>

                </div>



                <div class="col-md-3">

                    <label class="form-label fw-semibold">SPOC Name <span class="text-danger">*</span></label>

                    <input type="text" name="spoc_name" class="form-control" value="<?php echo e(old('spoc_name', $importRow['spoc_name'] ?? '')); ?>" required>

                </div>



                <div class="col-md-3">

                    <label class="form-label fw-semibold">SPOC Contact 1 <span class="text-danger">*</span></label>

                    <input type="text" name="spoc_contact1" class="form-control" value="<?php echo e(old('spoc_contact1', $importRow['spoc_contact1'] ?? '')); ?>" required>

                </div>



                <div class="col-md-3">

                    <label class="form-label fw-semibold">SPOC Contact 2</label>

                    <input type="text" name="spoc_contact2" class="form-control" value="<?php echo e(old('spoc_contact2', $importRow['spoc_contact2'] ?? '')); ?>">

                </div>



                <div class="col-md-3">

                    <label class="form-label fw-semibold">SPOC Email</label>

                    <input type="email" name="spoc_email" class="form-control" value="<?php echo e(old('spoc_email', $importRow['spoc_email'] ?? '')); ?>" >

                </div>



                <div class="col-md-3">

                    <label class="form-label fw-semibold">No. of Links <span class="text-danger">*</span></label>
                    <?php $linkValue = old('no_of_links', $importRow['no_of_links'] ?? ''); ?>
                      
                    <select name="no_of_links" class="form-select" required>

                        <option value="" <?php echo e($linkValue === '' ? 'selected' : ''); ?>>Select</option>

                        <option value="1" <?php echo e($linkValue === '1' ? 'selected' : ''); ?>>1</option>

                        <option value="2" <?php echo e($linkValue === '2' ? 'selected' : ''); ?>>2</option>
                        <option value="3" <?php echo e($linkValue === '3' ? 'selected' : ''); ?>>3</option>

                        <option value="4" <?php echo e($linkValue === '4' ? 'selected' : ''); ?>>4</option>

                    </select>

                </div>

                <div class="col-md-3">

                    <label class="form-label fw-semibold">Vendor Type <span class="text-danger">*</span></label>
                    <?php $vendorTypeValue = old('vendor_type', $importRow['vendor_type'] ?? ''); ?>
                      
                    <select name="vendor_type" class="form-select" required>

                        <option value="" <?php echo e($vendorTypeValue === '' ? 'selected' : ''); ?>>Select</option>

                        <option <?php echo e($vendorTypeValue === 'Same Vendor' ? 'selected' : ''); ?>>Same Vendor</option>

                        <option <?php echo e($vendorTypeValue === 'Different Vendor' ? 'selected' : ''); ?>>Different Vendor</option>
                        <option <?php echo e($vendorTypeValue === 'UBN' ? 'selected' : ''); ?>>UBN</option>

                        <option <?php echo e($vendorTypeValue === 'UBS' ? 'selected' : ''); ?>>UBS</option>

                        <option <?php echo e($vendorTypeValue === 'UBL' ? 'selected' : ''); ?>>UBL</option>

                        <option <?php echo e($vendorTypeValue === 'INF' ? 'selected' : ''); ?>>INF</option>

                    </select>

                </div>

                <div class="col-md-3">

                    <label class="form-label fw-semibold">Speed <span class="text-danger">*</span></label>

                    <input type="text" name="speed" placeholder="Mbps or Gbps" class="form-control" value="<?php echo e(old('speed', $importRow['speed'] ?? '')); ?>" required>

                </div>



                <div class="col-md-3">

                    <label class="form-label fw-semibold">Static IP <span class="text-danger">*</span></label>
                    <?php $staticIpValue = old('static_ip', $importRow['static_ip'] ?? ''); ?>
                        
                    <select name="static_ip" id="static_ip" class="form-select" required>

                        <option value="" <?php echo e($staticIpValue === '' ? 'selected' : ''); ?>>Select</option>

                        <option value="Yes" <?php echo e($staticIpValue === 'Yes' ? 'selected' : ''); ?>>Yes</option>

                        <option value="No" <?php echo e($staticIpValue === 'No' ? 'selected' : ''); ?>>No</option>

                    </select>

                </div>

                <div class="col-md-3">

                    <label class="form-label fw-semibold">Static IP Subnet</label>
                    <?php $staticIpSubnetValue = old('static_ip_subnet', $importRow['static_ip_subnet'] ?? ''); ?>
                    <select name="static_ip_subnet" id="static_ip_subnet" class="form-select" disabled>

                        <option value="" <?php echo e($staticIpSubnetValue === '' ? 'selected' : ''); ?>>Select Subnet</option>

                        <option value="/32" <?php echo e($staticIpSubnetValue === '/32' ? 'selected' : ''); ?>>/32</option>

                        <option value="/31" <?php echo e($staticIpSubnetValue === '/31' ? 'selected' : ''); ?>>/31</option>

                        <option value="/30" <?php echo e($staticIpSubnetValue === '/30' ? 'selected' : ''); ?>>/30</option>
                        <option value="/29" <?php echo e($staticIpSubnetValue === '/29' ? 'selected' : ''); ?>>/29</option>

                        <option value="/28" <?php echo e($staticIpSubnetValue === '/28' ? 'selected' : ''); ?>>/28</option>

                        <option value="/27" <?php echo e($staticIpSubnetValue === '/27' ? 'selected' : ''); ?>>/27</option>

                        <option value="/26" <?php echo e($staticIpSubnetValue === '/26' ? 'selected' : ''); ?>>/26</option>

                        <option value="/25" <?php echo e($staticIpSubnetValue === '/25' ? 'selected' : ''); ?>>/25</option>
                        <option value="/24" <?php echo e($staticIpSubnetValue === '/24' ? 'selected' : ''); ?>>/24</option>

                    </select>

                    <small class="text-muted">Select subnet only if Static IP is Yes</small>

                </div>

                






                <div class="col-md-3">

                    <label class="form-label fw-semibold">Expected Delivery <span class="text-danger">*</span></label>

                    <input type="date" name="expected_delivery" class="form-control" value="<?php echo e(old('expected_delivery', $importRow['expected_delivery'] ?? '')); ?>" required>

                </div>



                <div class="col-md-3">

                    <label class="form-label fw-semibold">Expected Activation <span class="text-danger">*</span></label>

                    <input type="date" name="expected_activation" class="form-control" value="<?php echo e(old('expected_activation', $importRow['expected_activation'] ?? '')); ?>" required>

                </div>



                <div class="col-md-3">

                    <label class="form-label fw-semibold">Hardware Required <span class="text-danger">*</span></label>
                    <?php $hardwareRequiredValue = old('hardware_required', $importRow['hardware_required'] ?? ''); ?>
                    <select name="hardware_required" id="hardware_required" class="form-select" required>

                        <option value="" <?php echo e($hardwareRequiredValue === '' ? 'selected' : ''); ?>>Select</option>

                        <option value="1" <?php echo e($hardwareRequiredValue === '1' ? 'selected' : ''); ?>>Yes</option>

                        <option value="0" <?php echo e($hardwareRequiredValue === '0' ? 'selected' : ''); ?>>No</option>

                    </select>

                </div>
                
<!-- container for all hardware rows -->
<div id="hardware_container">
    <div class="row hardware_row" style="display:none;">
        <div class="col-md-3">
            <label class="form-label fw-semibold">Hardware Make</label>
            <input type="text" name="hardware_make[]" class="form-control" placeholder="Enter Make">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Hardware Model Name</label>
            <input type="text" name="hardware_model_name[]" class="form-control">
        </div>
    </div>
</div>

<div class="col-md-3 mt-3" id="add_btn_div" style="display:none;">
    <button type="button" id="add_hardware_btn" class="btn btn-primary btn-sm">Add</button>
</div>
                <!-- <div class="col-md-3" id="hardware_make_div" style="display:none;">
                        <label class="form-label fw-semibold">Hardware Make</label>
                        <input type="text" name="hardware_make" class="form-control" placeholder="Enter Make">
                </div>

                <div class="col-md-3" id="hardware_name_div" style="display:none;">

                    <label class="form-label fw-semibold">Hardware Model Name</label>

                    <input type="text" name="hardware_model_name" class="form-control" value="<?php echo e(old('hardware_model_name', $importRow['hardware_model_name'] ?? '')); ?>">
 <button >Add</button>
                </div> -->
               
                <!-- <div class="col-md-3">
    <label class="fw-semibold">Hardware Make</label>
    <select name="hardware_make" class="form-control">
        <option value="">Select</option>
        <option>Cisco</option>
        <option>Fortinet</option>
        <option>Aruba</option>
        <option>MikroTik</option>
    </select>
</div> -->
<!-- 
<div class="col-md-3 fw-semibold">
    <label>Hardware Model</label>
    <input type="text" name="hardware_model" class="form-control" placeholder="Enter Model Number">
</div> -->


                    

            <input type="hidden" name="status" value="Active">





            </div>



            <div class="mt-4 text-end">

                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>

                <!-- <a href="<?php echo e(route('feasibility.index')); ?>" class="btn btn-secondary">Cancel</a> -->

            </div>

        </form>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>



<script>

// document.getElementById('hardware_required').addEventListener('change', function() {

//     document.getElementById('hardware_name_div').style.display = this.value == '1' ? 'block' : 'none';
//     document.getElementById('hardware_make_div').style.display = this.value == '1' ? 'block' : 'none';

// });
document.getElementById('hardware_required').addEventListener('change', function () {

    let value = this.value;

    // Show when "Yes"
    if (value == '1') {
        document.querySelector('.hardware_row').style.display = 'flex';
        document.getElementById('add_btn_div').style.display = 'block';
    } else {
        document.querySelector('.hardware_row').style.display = 'none';
        document.getElementById('add_btn_div').style.display = 'none';
    }
});
document.getElementById('add_hardware_btn').addEventListener('click', function () {

    // Clone the row
    let originalRow = document.querySelector('.hardware_row');
    let newRow = originalRow.cloneNode(true);

    // Reset values in the new row
    newRow.querySelectorAll('input').forEach(function (input) {
        input.value = "";
    });

    newRow.style.display = 'flex';

    // Append to container
    document.getElementById('hardware_container').appendChild(newRow);
});



// Helper function to set value in select dropdown, creating option if needed

function setSelectValue(selectElement, value) {

  console.log('setSelectValue called with:', selectElement.id, 'value:', value);

  

  if (!value || value === '') {

    selectElement.value = '';

    // If it's a Select2 element, trigger change

    if (typeof $ !== 'undefined' && typeof $(selectElement).select2 === 'function') {

      $(selectElement).val('').trigger('change');

    }

    return;

  }

  

  // Check if option already exists

  let optionExists = false;

  for (let option of selectElement.options) {

    if (option.value === value) {

      optionExists = true;

      break;

    }

  }

  // If option doesn't exist, create it

  if (!optionExists) {

    const newOption = document.createElement('option');

    newOption.value = value;

    newOption.text = value;

    selectElement.appendChild(newOption);

    console.log('Created new option:', value, 'for', selectElement.id);

  }

  

  // Set the value

  selectElement.value = value;

  console.log('Set native value for', selectElement.id, 'to:', value);

  

  // Handle Select2 if available

  if (typeof $ !== 'undefined') {

    try {

      const $element = $(selectElement);

      // Check if Select2 is initialized

      if (typeof $element.select2 === 'function' && $element.hasClass('select2-hidden-accessible')) {

        $element.val(value).trigger('change');

        console.log('Triggered Select2 change for', selectElement.id);

      }

    } catch (error) {

      console.log('Select2 not available or error:', error);

    }

  }

  

  console.log('Final value for', selectElement.id, ':', selectElement.value);

}



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

// Trigger on Enter key press

pincodeInput.addEventListener('keypress', function(e) {

  if (e.key === 'Enter') {

    e.preventDefault(); // Prevent form submission

    lookupPincode();

  }

});

// Trigger on input with debouncing (wait for user to stop typing)

let pincodeTimeout;

pincodeInput.addEventListener('input', function() {

  // Clear previous timeout

  if (pincodeTimeout) {

    clearTimeout(pincodeTimeout);

  }

  // Set new timeout to trigger after 1 second of no typing

  pincodeTimeout = setTimeout(() => {

    const value = this.value.trim();

    if (/^\d{6}$/.test(value)) {

      lookupPincode();

    }

  }, 1000);

});

// Initialize the vendor type field state on page load

document.addEventListener('DOMContentLoaded', function() {

    const noOfLinksSelect = document.querySelector('select[name="no_of_links"]');

    const vendorTypeSelect = document.querySelector('select[name="vendor_type"]');

    // If there's already a value selected, trigger the change event

    if (noOfLinksSelect.value) {

        noOfLinksSelect.dispatchEvent(new Event('change'));

    }
    
    // ✅ Static IP Subnet dropdown logic
    const staticIPSelect = document.getElementById('static_ip');
    const subnetSelect = document.getElementById('static_ip_subnet');
    
    // =======================
// 🔐 Static IP Validation
// =======================

const typeOfService = document.getElementById('type_of_service');
const staticIp = document.getElementById('static_ip');

function checkStaticIP() {
    if (typeOfService.value === 'ILL' && staticIp.value === 'No') {
        alert("For ILL service, Static IP is mandatory. Please select Yes.");
        staticIp.value = "Yes";   // Auto-correct to Yes
    }
}

// When Type of Service changes → if ILL, force Static IP to Yes
typeOfService.addEventListener('change', function () {
    if (this.value === 'ILL') {
        staticIp.value = 'Yes';
    }
});

// When Static IP dropdown changes → validate
staticIp.addEventListener('change', checkStaticIP);

    staticIPSelect.addEventListener('change', function() {
        if (this.value === 'Yes') {
            subnetSelect.disabled = false;
            subnetSelect.required = true;
        } else {
            subnetSelect.disabled = true;
            subnetSelect.required = false;
            subnetSelect.value = '';
        }
    });

    // ✅ Auto-select Static IP = "Yes" when Type of Service = "ILL"
    const typeOfServiceSelect = document.getElementById('type_of_service');
    
typeOfServiceSelect.addEventListener('change', function() {
    if (this.value === 'ILL') {
        staticIPSelect.value = 'Yes';
        staticIPSelect.required = true; // <-- Make Static IP mandatory
        staticIPSelect.dispatchEvent(new Event('change'));
    } else {
        staticIPSelect.required = true; // For other services, user selects manually
    }
});

});

// document.getElementById('importexcel').

function excelImport() {
    
    if (!links) {
        c.innerHTML = '';
        document.getElementById('excelImport').style.display = 'none';
        return;
    }

    c.innerHTML = '';
    {
        c.innerHTML += `

        <div class=" card-body row mb-3">
            <div class="col-md-4">
                <label class="form-label"></label>
                <div class="input-group">
                    <input type="number" step="0.01" min="0" id="arc_link_${i}" name="arc_link_${i}" class="form-control" onblur="validateEnteredAmount('arc_link_${i}', 'arc_per_link')" oninput="calculateTotal()">
                    <button type="button" class="btn btn-outline-info btn-sm" onclick="redirectToFeasibilityView()"><i class="bi bi-info-circle"></i></button>
                </div>
            </div>


        </div>`;
    }

    document.getElementById('dynamicPricingContainer').style.display = 'block';
}

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\feasibility\create.blade.php ENDPATH**/ ?>