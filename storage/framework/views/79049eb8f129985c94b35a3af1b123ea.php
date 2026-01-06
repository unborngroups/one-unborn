t@extends('layouts.app')



<?php $__env->startSection('content'); ?>

<div class="container-fluid py-4">

    <h4 class="fw-bold text-info mb-4">Edit Feasibility Status</h4>



    <div class="card shadow border-0 p-4">

        

        <div class="row mb-4">

            

            <div class="col-md-6">

                <h6 class="fw-semibold text-muted">Feasibility Request ID:</h6>

                <p><span class="badge bg-info fs-6"><?php echo e($record->feasibility->feasibility_request_id ?? 'N/A'); ?></span></p>

            </div>



            

            <div class="col-md-6">

                <h6 class="fw-semibold text-muted">Client:</h6>

                <p><?php echo e($record->feasibility->client->client_name ?? 'N/A'); ?></p>

            </div>



            

            <div class="col-md-6">

                <h6 class="fw-semibold text-muted">Feasibility Type:</h6>

                <p><?php echo e($record->feasibility->type_of_service ?? 'N/A'); ?></p>

            </div>



            

            <div class="col-md-6">

                <h6 class="fw-semibold text-muted">No. of Links:</h6>

                <p><?php echo e($record->feasibility->no_of_links ?? 'N/A'); ?></p>

            </div>



            

            <div class="col-md-6">

                <h6 class="fw-semibold text-muted">Current Status:</h6>

                <p>

                    <span class="badge 

                        <?php if($record->status == 'Open'): ?> bg-primary

                        <?php elseif($record->status == 'InProgress'): ?> bg-warning text-dark

                        <?php elseif($record->status == 'Closed'): ?> bg-success

                        <?php endif; ?>">

                        <?php echo e($record->status); ?>


                    </span>

                </p>

            </div>

        </div>



        <hr>



                

        <form id="feasibilityForm" method="POST">

            <?php echo csrf_field(); ?>



            <?php

            // Number of links determines how many vendor sections are mandatory

                $noOfLinks = $record->feasibility->no_of_links ?? 1;



                // Always render 4 vendor sections

                $maxVendors = 4; // Always show all 4 vendor sections

            ?>



            

            <?php for($i = 1; $i <= $maxVendors; $i++): ?>

                <h5 class="fw-bold text-info mb-3">

                    Vendor <?php echo e($i); ?>


                    <?php if($i <= $noOfLinks): ?>

                        <?php if($noOfLinks == 1): ?>

                            <small class="text-success">(Required - Default Vendor)</small>

                        <?php else: ?>

                            <small class="text-success">(Required - Link <?php echo e($i); ?>)</small>

                        <?php endif; ?>

                    <?php else: ?>

                        <small class="text-muted">(Optional - Additional Vendor)</small>

                    <?php endif; ?>

                </h5>

                

                <div class="row g-3 mb-4" id="vendor<?php echo e($i); ?>_section">

                    

                    <div class="col-md-2">

                        <label class="form-label fw-semibold">Name 

                            <?php if($i <= $noOfLinks): ?>

                                <span class="text-danger">*</span>

                            <?php endif; ?>

                        </label>

                        
                        <!-- class="form-select vendor-dropdown" (add only"vendor-dropdown" for duplicate validation) -->

                        <select name="vendor<?php echo e($i); ?>_name" 

                            class="form-select vendor-dropdown" 

                                data-vendor-number="<?php echo e($i); ?>"

                                <?php if($i <= $noOfLinks): ?> required <?php endif; ?>>

                            <option value="">Select Vendor</option>

                             

                            <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <option value="<?php echo e($vendor->vendor_name); ?>" 

                                        <?php if($record->{'vendor' . $i . '_name'} == $vendor->vendor_name): ?> selected <?php endif; ?>>

                                    <?php echo e($vendor->vendor_name); ?>


                                </option>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </select>

                        <div class="invalid-feedback">

                            This vendor is already selected in another section.

                        </div>

                    </div>

                    

                    <div class="col-md-2">

                        <label class="form-label fw-semibold">ARC</label>

                        <input type="number" name="vendor<?php echo e($i); ?>_arc" class="form-control" value="<?php echo e($record->{'vendor' . $i . '_arc'}); ?>">

                    </div>

                     

                    <div class="col-md-2">

                        <label class="form-label fw-semibold">OTC</label>

                        <input type="number" name="vendor<?php echo e($i); ?>_otc" class="form-control" value="<?php echo e($record->{'vendor' . $i . '_otc'}); ?>">

                    </div>

                    

                    <div class="col-md-2">

                        <label class="form-label fw-semibold">Static IP Cost</label>

                        <input type="number" name="vendor<?php echo e($i); ?>_static_ip_cost" class="form-control" value="<?php echo e($record->{'vendor' . $i . '_static_ip_cost'}); ?>">

                    </div>

                    

                    <div class="col-md-2">

                        <label class="form-label fw-semibold">Delivery Timeline</label>

                        <input type="text" name="vendor<?php echo e($i); ?>_delivery_timeline" class="form-control" value="<?php echo e($record->{'vendor' . $i . '_delivery_timeline'}); ?>">

                    </div>
                    

                    <div class="col-md-2">

                        <label class="form-label fw-semibold">Remarks</label>
                        <input type="text" name="vendor<?php echo e($i); ?>_remarks" class="form-control" value="<?php echo e($record->{'vendor' . $i . '_remarks'}); ?>">

                    </div>

                </div>

            <?php endfor; ?>

            <hr>

            

            <div class="mt-4">

                <div class="row">

                    <div class="col-md-6">

                        

                        <!-- <button type="button" class="btn btn-info" onclick="moveToSM()">

                            <i class="bi bi-arrow-left"></i> Move to S&M

                        </button> -->

                    </div>

                    <div class="col-md-6 text-end">

                        <button type="button" class="btn btn-warning me-2" onclick="saveToInProgress()">

                            <i class="bi bi-save"></i> Save (Move to In Progress)

                        </button>

                        <button type="button" class="btn btn-primary me-2" onclick="sendExceptionEmail()">

                            <i class="bi bi-send"></i> Send Exception

                        </button>

                        <button type="button" class="btn btn-success me-2" onclick="submitToClosed()">

                            <i class="bi bi-check-circle"></i> Submit (Move to Closed)

                        </button>

                        <?php if($record->status == 'Open'): ?>

                            <a href="<?php echo e(route('sm.feasibility.open')); ?>" class="btn btn-secondary">Cancel</a>

                        <?php elseif($record->status == 'InProgress'): ?>

                            <a href="<?php echo e(route('sm.feasibility.inprogress')); ?>" class="btn btn-secondary">Cancel</a>

                        <?php else: ?>

                            <a href="<?php echo e(route('sm.feasibility.closed')); ?>" class="btn btn-secondary">Cancel</a>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>



<?php $__env->stopSection(); ?>



<?php $__env->startSection('scripts'); ?>

<script>

document.addEventListener('DOMContentLoaded', function() {

    // Vendor name validation for duplicates and required fields

    function validateVendorNames() {

        const vendorDropdowns = document.querySelectorAll('.vendor-dropdown');

        const names = [];

        let isValid = true;

        const noOfLinks = parseInt('<?php echo e($noOfLinks); ?>');



        vendorDropdowns.forEach(function(dropdown, index) {

            dropdown.classList.remove('is-invalid');

            const vendorNumber = index + 1;

            const name = dropdown.value.trim().toLowerCase();

            

            // Check if this vendor is required (based on number of links)

            if (vendorNumber <= noOfLinks && !name) {

                dropdown.classList.add('is-invalid');

                isValid = false;

                return;

            }

            

            // Duplicate vendor names are now allowed; only track non-empty names

            if (name) {

                names.push(name);

            }

        });



        return isValid;

    }



    // ✅ Vendor Dropdown Logic - Hide selected vendors from other dropdowns
    // NOTE: Disabled for now as per requirement; keeping code for future use.
    /*
    function updateVendorDropdowns() {
        const dropdowns = document.querySelectorAll('.vendor-dropdown');
        const selectedValues = [];

        // Collect all selected values
        dropdowns.forEach(dropdown => {
            if (dropdown.value) {
                selectedValues.push(dropdown.value);
            }
        });

        // Update each dropdown
        dropdowns.forEach(currentDropdown => {
            const currentValue = currentDropdown.value;
            const options = currentDropdown.querySelectorAll('option');

            options.forEach(option => {
                if (option.value === '') {
                    // Keep empty option always visible
                    option.style.display = '';
                } else if (selectedValues.includes(option.value) && option.value !== currentValue) {
                    // Hide if selected in another dropdown
                    option.style.display = 'none';
                } else {
                    // Show if not selected elsewhere
                    option.style.display = '';
                }
            });
        });
    }

    // Add event listeners to vendor dropdowns for hide-logic
    document.querySelectorAll('.vendor-dropdown').forEach(function(dropdown) {
        dropdown.addEventListener('change', function() {
            updateVendorDropdowns();
            validateVendorNames();
        });
    });

    // Initialize vendor dropdown hide-logic
    updateVendorDropdowns();
    */



    // Save to In Progress function

    window.saveToInProgress = function() {

        if (!validateVendorNames()) {

            alert('Please fill all required vendor names and ensure they are different.');

            return false;

        }



        const form = document.getElementById('feasibilityForm');

        form.action = "<?php echo e(route('sm.feasibility.save', $record->id)); ?>";

        form.submit();

    };



    // Submit to Closed function

    window.submitToClosed = function() {

        if (!validateVendorNames()) {

            alert('Please fill all required vendor names and ensure they are different.');

            return false;

        }



        if (confirm('Are you sure you want to submit this feasibility? This will move it to Closed status.')) {

            const form = document.getElementById('feasibilityForm');

            form.action = "<?php echo e(route('sm.feasibility.submit', $record->id)); ?>";

            form.submit();

        }

    };


    // Send Exception Email function
    window.sendExceptionEmail = function() {
        if (!validateVendorNames()) {
            alert('Please fill all required vendor names before sending exception.');
            return false;
        }

        // At least one vendor must be selected; all selected names must be same
        const vendorDropdowns = document.querySelectorAll('.vendor-dropdown');
        const selectedNames = [];

        vendorDropdowns.forEach(function(dropdown) {
            const val = dropdown.value.trim();
            if (val !== '') {
                selectedNames.push(val.toLowerCase());
            }
        });

        if (selectedNames.length === 0) {
            alert('Please select at least one vendor before sending exception email.');
            return false;
        }

        const first = selectedNames[0];
        const allSame = selectedNames.every(n => n === first);

        if (!allSame) {
            alert('For exception, all selected vendor names must be same.');
            return false;
        }

        if (confirm('Send exception email for the selected vendor?')) {
            const form = document.getElementById('feasibilityForm');
            form.action = "<?php echo e(route('sm.feasibility.exception', $record->id)); ?>";
            form.submit();
        }
    };



    // ✅ Menu Swap Function - Move to S&M (Currently commented out as route is not defined)

    /*

    window.moveToSM = function() {

        if (confirm('Are you sure you want to move this feasibility to S&M section?')) {

            const form = document.createElement('form');

            form.method = 'POST';

            form.action = "<?php echo e(route('operations.feasibility.moveToSM', $record->id)); ?>";

            

            const csrfToken = document.createElement('input');

            csrfToken.type = 'hidden';

            csrfToken.name = '_token';

            csrfToken.value = "<?php echo e(csrf_token()); ?>";

            form.appendChild(csrfToken);

            

            document.body.appendChild(form);

            form.submit();

        }

    };

    */

});

</script>

<?php $__env->stopSection(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\sm\feasibility\edit.blade.php ENDPATH**/ ?>