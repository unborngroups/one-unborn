

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <h4 class="text-primary fw-bold mb-3">Edit Feasibility</h4>

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

        
<div class="container-fluid py-4">
    <div class="card shadow border-0 p-4">
        <h5 class="mb-3">Import / Export Feasibility</h5>
        <div class="row g-3 align-items-center">
            <div class="col-md-6">
                <form action="<?php echo e(route('feasibility.import')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="input-group">
                        <input type="file" name="file" class="form-control" required>
                        <button type="submit" class="btn btn-primary">Import Excel</button>
                    </div>
                </form>
            </div>
            <!-- <div class="col-md-6 text-end">
                <a href="<?php echo e(route('feasibility.export')); ?>" class="btn btn-success">Download Excel</a>
            </div> -->
        </div>
    </div>
</div>

        <form action="<?php echo e(route('feasibility.update', $feasibility->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Feasibility Request ID</label>
                    <input type="text" class="form-control bg-light" value="<?php echo e($feasibility->feasibility_request_id); ?>" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Type of Service <span class="text-danger">*</span></label>
                    <?php $typeSelection = old('type_of_service', $feasibility->type_of_service); ?>
                    <select name="type_of_service" id="type_of_service" class="form-select" required>
                        <option value="" <?php echo e($typeSelection === '' ? 'selected' : ''); ?>>Select</option>
                        <option value="Broadband" <?php echo e($typeSelection === 'Broadband' ? 'selected' : ''); ?>>Broadband</option>
                        <option value="ILL" <?php echo e($typeSelection === 'ILL' ? 'selected' : ''); ?>>ILL</option>
                        <option value="P2P" <?php echo e($typeSelection === 'P2P' ? 'selected' : ''); ?>>P2P</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Company <span class="text-danger">*</span></label>
                    <select name="company_id" id="company_id" class="form-select" required>
                        <option value="">Select Company</option>
                        <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($company->id); ?>" <?php echo e((string) old('company_id', $feasibility->company_id) === (string) $company->id ? 'selected' : ''); ?>>
                                <?php echo e($company->company_name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Client Name <span class="text-danger">*</span></label>
                    <select name="client_id" id="client_id" class="form-select" required>
                        <option value="">Select Client</option>
                        <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($client->id); ?>" <?php echo e((string) old('client_id', $feasibility->client_id) === (string) $client->id ? 'selected' : ''); ?>>
                                <?php echo e($client->business_name ?: $client->client_name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Pincode <span class="text-danger">*</span></label>
                    <input type="text" name="pincode" id="pincode" maxlength="6" value="<?php echo e(old('pincode', $feasibility->pincode)); ?>" class="form-control" required>
                </div>


                <div class="col-md-4">
                    <label class="form-label fw-semibold">State <span class="text-danger">*</span></label>
                    <?php $stateValue = old('state', $feasibility->state); ?>
                    <select name="state" id="state" class="form-select select2-tags">
                        <option value="" <?php echo e($stateValue === '' ? 'selected' : ''); ?>>Select or Type State</option>
                        <?php if($stateValue): ?>
                            <option value="<?php echo e($stateValue); ?>" selected><?php echo e($stateValue); ?></option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">District <span class="text-danger">*</span></label>
                    <?php $districtValue = old('district', $feasibility->district); ?>
                    <select name="district" id="district" class="form-select select2-tags">
                        <option value="" <?php echo e($districtValue === '' ? 'selected' : ''); ?>>Select or Type District</option>
                        <?php if($districtValue): ?>
                            <option value="<?php echo e($districtValue); ?>" selected><?php echo e($districtValue); ?></option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Area <span class="text-danger">*</span></label>
                    <?php $areaValue = old('area', $feasibility->area); ?>
                    <select name="area" id="post_office" class="form-select select2-tags">
                        <option value="" <?php echo e($areaValue === '' ? 'selected' : ''); ?>>Select or Type Area</option>
                        <?php if($areaValue): ?>
                            <option value="<?php echo e($areaValue); ?>" selected><?php echo e($areaValue); ?></option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Address <span class="text-danger">*</span></label>
                    <textarea name="address" class="form-control" rows="2" required><?php echo e(old('address', $feasibility->address)); ?></textarea>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">SPOC Name <span class="text-danger">*</span></label>
                    <input type="text" name="spoc_name" value="<?php echo e(old('spoc_name', $feasibility->spoc_name)); ?>" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">SPOC Contact 1 <span class="text-danger">*</span></label>
                    <input type="text" name="spoc_contact1" value="<?php echo e(old('spoc_contact1', $feasibility->spoc_contact1)); ?>" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">SPOC Contact 2</label>
                    <input type="text" name="spoc_contact2" value="<?php echo e(old('spoc_contact2', $feasibility->spoc_contact2)); ?>" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">SPOC Email</label>
                    <input type="email" name="spoc_email" value="<?php echo e(old('spoc_email', $feasibility->spoc_email)); ?>" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">No. of Links <span class="text-danger">*</span></label>
                    <?php $linkValue = (string) old('no_of_links', (string) $feasibility->no_of_links); ?>
                    <select name="no_of_links" id="no_of_links" class="form-select" required>
                        <option value="" <?php echo e($linkValue === '' ? 'selected' : ''); ?>>Select</option>
                        <option value="1" <?php echo e($linkValue === '1' ? 'selected' : ''); ?>>1</option>
                        <option value="2" <?php echo e($linkValue === '2' ? 'selected' : ''); ?>>2</option>
                        <option value="3" <?php echo e($linkValue === '3' ? 'selected' : ''); ?>>3</option>
                        <option value="4" <?php echo e($linkValue === '4' ? 'selected' : ''); ?>>4</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Vendor Type <span class="text-danger">*</span></label>
                    <?php $vendorTypeValue = old('vendor_type', $feasibility->vendor_type); ?>
                    <select name="vendor_type" id="vendor_type" class="form-select" required>
                        <option value="" <?php echo e($vendorTypeValue === '' ? 'selected' : ''); ?>>Select</option>
                        <option value="Same Vendor" <?php echo e($vendorTypeValue === 'Same Vendor' ? 'selected' : ''); ?>>Same Vendor</option>
                        <option value="Different Vendor" <?php echo e($vendorTypeValue === 'Different Vendor' ? 'selected' : ''); ?>>Different Vendor</option>
                        <option value="UBN" <?php echo e($vendorTypeValue === 'UBN' ? 'selected' : ''); ?>>UBN</option>
                        <option value="UBS" <?php echo e($vendorTypeValue === 'UBS' ? 'selected' : ''); ?>>UBS</option>
                        <option value="UBL" <?php echo e($vendorTypeValue === 'UBL' ? 'selected' : ''); ?>>UBL</option>
                        <option value="INF" <?php echo e($vendorTypeValue === 'INF' ? 'selected' : ''); ?>>INF</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Speed <span class="text-danger">*</span></label>
                    <input type="text" name="speed" value="<?php echo e(old('speed', $feasibility->speed)); ?>" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Static IP <span class="text-danger">*</span></label>
                    <?php $staticIpValue = old('static_ip', $feasibility->static_ip); ?>
                    <select name="static_ip" id="static_ip" class="form-select" required>
                        <option value="" <?php echo e($staticIpValue === '' ? 'selected' : ''); ?>>Select</option>
                        <option value="Yes" <?php echo e($staticIpValue === 'Yes' ? 'selected' : ''); ?>>Yes</option>
                        <option value="No" <?php echo e($staticIpValue === 'No' ? 'selected' : ''); ?>>No</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Static IP Subnet</label>
                    <?php $staticIpSubnetValue = old('static_ip_subnet', $feasibility->static_ip_subnet); ?>
                    <select name="static_ip_subnet" id="static_ip_subnet" class="form-select" <?php echo e($staticIpValue === 'Yes' ? '' : 'disabled'); ?>>
                        <option value="" <?php echo e($staticIpSubnetValue === '' ? 'selected' : ''); ?>>Select Subnet</option>
                        <?php $__currentLoopData = ['/32','/31','/30','/29','/28','/27','/26','/25','/24']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($sub); ?>" <?php echo e($staticIpSubnetValue === $sub ? 'selected' : ''); ?>><?php echo e($sub); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Expected Delivery <span class="text-danger">*</span></label>
                    <input type="date" name="expected_delivery" value="<?php echo e(old('expected_delivery', $feasibility->expected_delivery)); ?>" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Expected Activation <span class="text-danger">*</span></label>
                    <input type="date" name="expected_activation" value="<?php echo e(old('expected_activation', $feasibility->expected_activation)); ?>" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Hardware Required <span class="text-danger">*</span></label>
                    <?php $hardwareRequiredValue = old('hardware_required', $feasibility->hardware_required); ?>
                    <select name="hardware_required" id="hardware_required" class="form-select" required>
                        <option value="" <?php echo e($hardwareRequiredValue === '' ? 'selected' : ''); ?>>Select</option>
                        <option value="1" <?php echo e((string) $hardwareRequiredValue === '1' ? 'selected' : ''); ?>>Yes</option>
                        <option value="0" <?php echo e((string) $hardwareRequiredValue === '0' ? 'selected' : ''); ?>>No</option>
                    </select>
                </div>

                <div class="col-md-3" id="hardware_name_div" >
                    <label class="form-label fw-semibold">Hardware Model Name</label>
                    <input type="text" name="hardware_model_name" value="<?php echo e(old('hardware_model_name', $feasibility->hardware_model_name)); ?>" class="form-control">
                </div>

                                <input type="hidden" name="status" value="<?php echo e($feasibility->status); ?>">
                        </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update</button>
                <a href="<?php echo e(route('feasibility.index')); ?>" class="btn btn-secondary">Cancel</a>
            </div>

        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.getElementById('hardware_required').addEventListener('change', function() {
        document.getElementById('hardware_name_div').style.display = this.value == '1' ? 'block' : 'none';
});

function setSelectValue(selectElement, value) {
    if (!value || value === '') {
        selectElement.value = '';
        if (typeof $ !== 'undefined' && typeof $(selectElement).select2 === 'function') {
            $(selectElement).val('').trigger('change');
        }
        return;
    }
    let optionExists = false;
    for (let option of selectElement.options) {
        if (option.value === value) {
            optionExists = true;
            break;
        }
    }
    if (!optionExists) {
        const newOption = document.createElement('option');
        newOption.value = value;
        newOption.text = value;
        selectElement.appendChild(newOption);
    }
    selectElement.value = value;
    if (typeof $ !== 'undefined') {
        try {
            const $element = $(selectElement);
            if (typeof $element.select2 === 'function' && $element.hasClass('select2-hidden-accessible')) {
                $element.val(value).trigger('change');
            }
        } catch (error) {
            console.log('Select2 not available or error:', error);
        }
    }
}

function lookupPincode() {
    const pincodeField = document.getElementById('pincode');
    const p = pincodeField.value.trim();
    if (!/^\d{6}$/.test(p)) return;
    const stateField = document.getElementById('state');
    const districtField = document.getElementById('district');
    const areaField = document.getElementById('post_office');
    const originalState = stateField.value;
    const originalDistrict = districtField.value;
    const originalArea = areaField.value;
    setSelectValue(stateField, 'Loading...');
    setSelectValue(districtField, 'Loading...');
    setSelectValue(areaField, 'Loading...');
    axios.post('/api/pincode/lookup', { pincode: p })
        .then(r => {
            const d = r.data;
            setSelectValue(stateField, d.state || '');
            setSelectValue(districtField, d.district || '');
            setSelectValue(areaField, d.post_office || '');
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
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 3000);
        })
        .catch(err => {
            setSelectValue(stateField, originalState);
            setSelectValue(districtField, originalDistrict);
            setSelectValue(areaField, originalArea);
            let errorMessage = 'Unable to fetch pincode details. Please try again or enter manually.';
            if (err.response && err.response.status === 404) {
                errorMessage = 'Pincode not found. Please check the pincode and try again.';
            } else if (err.response && err.response.status === 422) {
                errorMessage = 'Invalid pincode format. Please enter a 6-digit pincode.';
            }
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
            setTimeout(() => {
                if (errorNotification.parentNode) {
                    errorNotification.parentNode.removeChild(errorNotification);
                }
            }, 5000);
        });
}

const pincodeInput = document.getElementById('pincode');
pincodeInput.addEventListener('blur', lookupPincode);
pincodeInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        lookupPincode();
    }
});
let pincodeTimeout;
pincodeInput.addEventListener('input', function() {
    if (pincodeTimeout) {
        clearTimeout(pincodeTimeout);
    }
    pincodeTimeout = setTimeout(() => {
        const value = this.value.trim();
        if (/^\d{6}$/.test(value)) {
            lookupPincode();
        }
    }, 1000);
});

document.addEventListener('DOMContentLoaded', function() {
        const noOfLinksSelect = document.querySelector('select[name="no_of_links"]');
        const vendorTypeSelect = document.querySelector('select[name="vendor_type"]');
        if (noOfLinksSelect.value) {
                noOfLinksSelect.dispatchEvent(new Event('change'));
        }
        const staticIPSelect = document.getElementById('static_ip');
        const subnetSelect = document.getElementById('static_ip_subnet');
        const typeOfServiceSelect = document.getElementById('type_of_service');
        typeOfServiceSelect.addEventListener('change', function() {
                if (this.value === 'ILL') {
                        staticIPSelect.value = 'Yes';
                        staticIPSelect.required = true;
                        staticIPSelect.dispatchEvent(new Event('change'));
                } else {
                        staticIPSelect.required = true;
                }
        });
        const staticIp = document.getElementById('static_ip');
        function checkStaticIP() {
                if (typeOfServiceSelect.value === 'ILL' && staticIp.value === 'No') {
                        alert("For ILL service, Static IP is mandatory. Please select Yes.");
                        staticIp.value = "Yes";
                }
        }
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
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\wlcome\multipleuserpage\resources\views\feasibility\edit.blade.php ENDPATH**/ ?>