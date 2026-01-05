



<?php $__env->startSection('content'); ?>

<div class="container-fluid">

    <div class="row">

        <div class="col-12">

            <div class="card">

                

                <div class="card-header text-dark">

                    <h4 class="mb-0">

                        <i class="bi bi-pencil-square"></i> Edit Purchase Order

                    </h4>

                </div>



                <div class="card-body">

                    
                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Validation Error:</strong> <?php echo e(session('error')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    

                    <form action="<?php echo e(route('sm.purchaseorder.update', $purchaseOrder->id)); ?>" method="POST" enctype="multipart/form-data" id="purchaseOrderForm">

                        <?php echo csrf_field(); ?>

                        <?php echo method_field('PUT'); ?>

                        

                        <div class="row">

                            

                            <div class="col-md-6 mb-3">

                                <label for="feasibility_id" class="form-label">

                                    <strong>Feasibility Request ID <span class="text-danger">*</span></strong>

                                </label>

                                <input type="text" class="form-control" 

                                       value="<?php echo e($purchaseOrder->feasibility->feasibility_request_id); ?> - <?php echo e($purchaseOrder->feasibility->client->client_name ?? 'Unknown'); ?>" 

                                       disabled>

                                <input type="hidden" name="feasibility_id" value="<?php echo e($purchaseOrder->feasibility_id); ?>">

                                
                                <?php
                                    $feas = $purchaseOrder->feasibility;
                                    $addrParts = collect([
                                        $feas->address ?? null,
                                        $feas->area ?? null,
                                        $feas->district ?? null,
                                        $feas->state ?? null,
                                        $feas->pincode ?? null,
                                    ])->filter();
                                    $feasAddressText = $addrParts->isNotEmpty() ? $addrParts->implode(', ') : '';
                                ?>
                                <?php if($feasAddressText): ?>
                                    <div class="mt-2">
                                        <label class="form-label mb-1"><strong>Feasibility Address</strong></label>
                                        <textarea class="form-control bg-light" rows="2" readonly><?php echo e($feasAddressText); ?></textarea>
                                    </div>
                                <?php endif; ?>

                            </div>



                            

                            <div class="col-md-6 mb-3">

                                <label for="po_number" class="form-label">

                                    <strong>PO Number <span class="text-danger">*</span></strong>

                                </label>

                                <input type="text" class="form-control <?php $__errorArgs = ['po_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 

                                       id="po_number" name="po_number" 

                                       value="<?php echo e(old('po_number', $purchaseOrder->po_number)); ?>" required>

                                <?php $__errorArgs = ['po_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>

                                    <div class="invalid-feedback"><?php echo e($message); ?></div>

                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                            </div>



                            

                            <div class="col-md-6 mb-3">

                                <label for="po_date" class="form-label">

                                    <strong>PO Date <span class="text-danger">*</span></strong>

                                </label>

                                <input type="date" class="form-control <?php $__errorArgs = ['po_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 

                                       id="po_date" name="po_date" 

                                       value="<?php echo e(old('po_date', $purchaseOrder->po_date)); ?>" required>

                                <?php $__errorArgs = ['po_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>

                                    <div class="invalid-feedback"><?php echo e($message); ?></div>

                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                            </div>

                            
                            <!-- <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">
                                    <strong>PO Status <span class="text-danger">*</span></strong>
                                </label>
                                <select class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="status" name="status" required>
                                    <option value="Active" <?php echo e(old('status', $purchaseOrder->status) == 'Active' ? 'selected' : ''); ?>>Active</option>
                                    <option value="Inactive" <?php echo e(old('status', $purchaseOrder->status) == 'Inactive' ? 'selected' : ''); ?>>Inactive</option>
                                </select>
                                <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <div class="form-text">
                                    <i class="bi bi-info-circle"></i> Changing status to <strong>"Active"</strong> will automatically create a Deliverable record.
                                </div>
                            </div> -->

                        </div>



                        

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-semibold">No. of Links *</label>

                                <select name="no_of_links" id="no_of_links_dropdown" class="form-select" required onchange="showDynamicPricing()">

                                    <option value="">Select</option>

                                    <?php for($i = 1; $i <= 4; $i++): ?>

                                        <option value="<?php echo e($i); ?>" <?php echo e(old('no_of_links', $purchaseOrder->no_of_links) == $i ? 'selected' : ''); ?>><?php echo e($i); ?></option>

                                    <?php endfor; ?>

                                </select>

                            </div>

                        </div>



                        

                        <div id="dynamicPricingContainer" style="display: block;">

                            <div class="card border-info mb-3">

                                <div class="card-header bg-info text-white">

                                    <h6 class="mb-0">Pricing Details (Per Link)</h6>

                                </div>

                                <div class="card-body">

                                    <div id="pricingFieldsContainer">

                                        <?php for($i = 1; $i <= $purchaseOrder->no_of_links; $i++): ?>

                                            <div class="row mb-3">

                                                <div class="col-12 mb-2">

                                                    <h6 class="text-primary">Link <?php echo e($i); ?> Pricing</h6>

                                                </div>

                                                <div class="col-md-4">

                                                    <label class="form-label">ARC - Link <?php echo e($i); ?> (₹) *</label>

                                                    <input type="number" step="0.01" min="0" 

                                                        class="form-control" name="arc_link_<?php echo e($i); ?>" 

                                                        id="arc_link_<?php echo e($i); ?>" 

                                                        value="<?php echo e(old('arc_link_'.$i, $purchaseOrder['arc_link_'.$i])); ?>" 

                                                        required onchange="calculateTotal()">

                                                </div>

                                                <div class="col-md-4">

                                                    <label class="form-label">OTC - Link <?php echo e($i); ?> (₹) *</label>

                                                    <input type="number" step="0.01" min="0" 

                                                        class="form-control" name="otc_link_<?php echo e($i); ?>" 

                                                        id="otc_link_<?php echo e($i); ?>" 

                                                        value="<?php echo e(old('otc_link_'.$i, $purchaseOrder['otc_link_'.$i])); ?>" 

                                                        required onchange="calculateTotal()">

                                                </div>

                                                <div class="col-md-4">

                                                    <label class="form-label">STATIC IP - Link <?php echo e($i); ?> (₹) *</label>

                                                    <input type="number" step="0.01" min="0" 

                                                        class="form-control" name="static_ip_link_<?php echo e($i); ?>" 

                                                        id="static_ip_link_<?php echo e($i); ?>" 

                                                        value="<?php echo e(old('static_ip_link_'.$i, $purchaseOrder['static_ip_link_'.$i])); ?>" 

                                                        required onchange="calculateTotal()">

                                                </div>

                                            </div>

                                        <?php endfor; ?>

                                    </div>

                                </div>

                            </div>

                        </div>



                        

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label for="contract_period" class="form-label">

                                    <strong>Contract Period (Months) <span class="text-danger">*</span></strong>

                                </label>

                                <input type="number" min="1" class="form-control <?php $__errorArgs = ['contract_period'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 

                                       id="contract_period" name="contract_period" 

                                       value="<?php echo e(old('contract_period', $purchaseOrder->contract_period)); ?>" required>

                                <?php $__errorArgs = ['contract_period'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>

                                    <div class="invalid-feedback"><?php echo e($message); ?></div>

                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                            </div>



                            <div class="col-md-6 mb-3">

                                <label class="form-label"><strong>Total Cost (Auto-calculated)</strong></label>

                                <div class="form-control bg-light" id="totalCost">

                                    ₹<?php echo e(number_format($purchaseOrder->total_cost, 2)); ?>


                                </div>

                            </div>

                            <div class="row">
                            <!-- Import Document Upload -->
                            <div class="col-md-4 mb-3">
                                <label for="import_file">Import Document</label>
                                <input type="file" class="form-control" name="import_file" id="import_file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                                <?php if($purchaseOrder->import_file): ?>
                                <a href="<?php echo e(asset($purchaseOrder->import_file)); ?>" target="_blank">
                                    View existing file
                                </a>
                                <?php endif; ?>

                            </div>
                        </div>


                        </div>



                        

                        <div class="row">

                            <div class="col-12 text-end">

                                <a href="<?php echo e(route('sm.purchaseorder.index')); ?>" class="btn btn-secondary me-2">

                                    <i class="bi bi-arrow-left"></i> Cancel

                                </a>

                                <button type="submit" class="btn btn-success" onclick="return validateBeforeSubmit()">

                                    <i class="bi bi-save"></i> Update Purchase Order

                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>







<script>

let feasibilityAmounts = {}; // Store feasibility amounts for validation



function loadFeasibilityDetails() {

    const feasibilityId = document.getElementById('feasibility_id').value;

    

    if (!feasibilityId) {

        // Reset form when no feasibility selected

        document.getElementById('no_of_links_dropdown').value = '';

        document.getElementById('dynamicPricingContainer').style.display = 'none';

        feasibilityAmounts = {};

        return;

    }



    // ✅ AJAX request to get feasibility details including pricing

    fetch(`/sm/purchaseorder/feasibility/${feasibilityId}/details`)

        .then(response => response.json())

        .then(data => {

            console.log('Feasibility data received:', data); // Debug log

            

            // Store feasibility amounts for validation (both old and new format)

            feasibilityAmounts = {

                arc_per_link: data.arc_per_link || 0,

                otc_per_link: data.otc_per_link || 0,

                static_ip_cost_per_link: data.static_ip_cost_per_link || 0,

                vendor_pricing: data.vendor_pricing || {} // Store vendor pricing data

            };

            

            console.log('Stored feasibilityAmounts:', feasibilityAmounts); // Debug log

            

            // NO AUTO-FILL for number of links - user must select manually

            // Keep dropdown empty so user has to choose

            document.getElementById('no_of_links_dropdown').value = '';

            document.getElementById('dynamicPricingContainer').style.display = 'none';

        })

        .catch(error => {

            console.error('Error fetching feasibility details:', error);

            alert('Error loading feasibility details. Please try again.');

        });

}



function showDynamicPricing() {

    const linksCount = parseInt(document.getElementById('no_of_links_dropdown').value);

    const container = document.getElementById('pricingFieldsContainer');

    

    if (!linksCount) {

        document.getElementById('dynamicPricingContainer').style.display = 'none';

        return;

    }

    

    // Clear existing fields

    container.innerHTML = '';

    

    // Generate pricing fields for each link

    for (let i = 1; i <= linksCount; i++) {

        const linkRow = document.createElement('div');

        linkRow.className = 'row mb-3';

        // Generate expected amounts display for all vendors

        const vendorPricing = feasibilityAmounts.vendor_pricing || {};

        let arcExpected = [];

        let otcExpected = [];

        let staticIpExpected = [];

        

        Object.keys(vendorPricing).forEach(vendorKey => {

            const vendor = vendorPricing[vendorKey];

            if (vendor && vendor.name) {

                if (vendor.arc > 0) arcExpected.push(vendor.name + ': ₹' + vendor.arc);

                if (vendor.otc > 0) otcExpected.push(vendor.name + ': ₹' + vendor.otc);

                if (vendor.static_ip_cost > 0) staticIpExpected.push(vendor.name + ': ₹' + vendor.static_ip_cost);

            }

        });



        linkRow.innerHTML = `

            <div class="col-12 mb-2">

                <h6 class="text-primary">Link ${i} Pricing</h6>

            </div>

            <div class="col-md-4">

                <label class="form-label">ARC - Link ${i} (₹) *</label>

                <div class="input-group">

                    <input type="number" step="0.01" min="0" class="form-control" 

                           name="arc_link_${i}" id="arc_link_${i}" 

                           placeholder="Enter ARC amount"

                           required onchange="validateEnteredAmount('arc_link_${i}', 'arc_per_link')"

                           onblur="calculateTotal()">

                    <button type="button" class="btn btn-outline-info btn-sm" 

                            onclick="redirectToFeasibilityView()"

                            title="View feasibility details to verify vendor amounts">

                        <i class="bi bi-info-circle"></i>

                    </button>

                </div>

            </div>

            <div class="col-md-4">

                <label class="form-label">OTC - Link ${i} (₹) *</label>

                <div class="input-group">

                    <input type="number" step="0.01" min="0" class="form-control" 

                           name="otc_link_${i}" id="otc_link_${i}" 

                           placeholder="Enter OTC amount"

                           required onchange="validateEnteredAmount('otc_link_${i}', 'otc_per_link')"

                           onblur="calculateTotal()">

                    <button type="button" class="btn btn-outline-info btn-sm" 

                            onclick="redirectToFeasibilityView()"

                            title="View feasibility details to verify vendor amounts">

                        <i class="bi bi-info-circle"></i>

                    </button>

                </div>

            </div>

            <div class="col-md-4">

                <label class="form-label">STATIC IP - Link ${i} (₹) *</label>

                <div class="input-group">

                    <input type="number" step="0.01" min="0" class="form-control" 

                           name="static_ip_link_${i}" id="static_ip_link_${i}" 

                           placeholder="Enter Static IP amount"

                           required onchange="validateEnteredAmount('static_ip_link_${i}', 'static_ip_cost_per_link')"

                           onblur="calculateTotal()">

                    <button type="button" class="btn btn-outline-info btn-sm" 

                            onclick="redirectToFeasibilityView()"

                            title="View feasibility details to verify vendor amounts">

                        <i class="bi bi-info-circle"></i>

                    </button>

                </div>

            </div>

        `;

        container.appendChild(linkRow);

    }

    

    document.getElementById('dynamicPricingContainer').style.display = 'block';

    calculateTotal();

}



function validateEnteredAmount(fieldId, amountType) {

    const enteredAmount = parseFloat(document.getElementById(fieldId).value) || 0;

    

    console.log('Validation called for:', fieldId, 'Amount Type:', amountType, 'Entered:', enteredAmount);

    console.log('Current feasibilityAmounts:', feasibilityAmounts);

    

    // Skip validation for empty amounts

    if (enteredAmount === 0) {

        document.getElementById(fieldId).style.borderColor = '';

        document.getElementById(fieldId).classList.remove('is-invalid');

        return;

    }

    

    // Get vendor pricing from feasibility

    const vendorPricing = feasibilityAmounts.vendor_pricing || {};

    console.log('Vendor pricing:', vendorPricing);

    

    // Map field types to vendor pricing fields

    const fieldMapping = {

        'arc_per_link': 'arc',

        'otc_per_link': 'otc', 

        'static_ip_cost_per_link': 'static_ip_cost'

    };

    

    const pricingField = fieldMapping[amountType];

    if (!pricingField) {

        console.warn('Unknown amount type:', amountType);

        return;

    }

    

    // Collect all valid amounts from all vendors

    let validAmounts = [];

    Object.keys(vendorPricing).forEach(vendorKey => {

        const vendor = vendorPricing[vendorKey];

        if (vendor && vendor[pricingField] > 0) {

            validAmounts.push({

                vendor: vendor.name,

                amount: parseFloat(vendor[pricingField])

            });

        }

    });

    

    // Check if entered amount matches any vendor amount

    let isValidAmount = false;

    validAmounts.forEach(validAmount => {

        const vendorAmount = validAmount.amount;

        if (vendorAmount > 0) {

            // Use small tolerance for float comparison

            if (Math.abs(enteredAmount - vendorAmount) <= 0.01) {

                isValidAmount = true;

            }

        }

    });

    

    if (!isValidAmount && validAmounts.length > 0 && enteredAmount > 0) {

        // Change field border to red to indicate error

        document.getElementById(fieldId).style.borderColor = '#dc3545';

        document.getElementById(fieldId).classList.add('is-invalid');

        

        // Create message showing all valid vendor amounts

        const vendorAmountsList = validAmounts.map(v => 

            `${v.vendor}: ₹${v.amount.toLocaleString('en-IN')}`

        ).join('\n');

        

        alert(

            `⚠️ AMOUNT WRONG!\n\n` +

            `Valid amounts from feasibility` +

            `Please enter one of the valid amounts or check feasibility details.`

        );

    } else {

        // Reset field styling if amount is correct

        document.getElementById(fieldId).style.borderColor = '';

        document.getElementById(fieldId).classList.remove('is-invalid');

    }

}



// Function to show all vendor pricing information

function showVendorPricing() {

    if (!feasibilityAmounts.vendor_pricing) {

        alert('No vendor pricing information available. Please select a feasibility first.');

        return;

    }

    

    const vendorPricing = feasibilityAmounts.vendor_pricing;

    let pricingInfo = '💰 VENDOR PRICING INFORMATION\n\n';

    

    Object.keys(vendorPricing).forEach(vendorKey => {

        const vendor = vendorPricing[vendorKey];

        if (vendor && vendor.name) {

            pricingInfo += `🏢 ${vendor.name}:\n`;

            pricingInfo += `  • ARC: ₹${vendor.arc.toLocaleString('en-IN')}\n`;

            pricingInfo += `  • OTC: ₹${vendor.otc.toLocaleString('en-IN')}\n`;

            pricingInfo += `  • Static IP: ₹${vendor.static_ip_cost.toLocaleString('en-IN')}\n\n`;

        }

    });

    

    pricingInfo += '💡 Tip: You can enter any of the vendor amounts shown above.';

    alert(pricingInfo);

}



// Function to directly redirect to feasibility view page

function redirectToFeasibilityView() {

    const feasibilityId = document.getElementById('feasibility_id').value;

    

    if (!feasibilityId) {

        alert('⚠️ Please select a feasibility first!');

        return;

    }

    

    // Open feasibility details in new tab to verify vendor amounts

    window.open(`/sm/feasibility/${feasibilityId}/view`, '_blank');

}



function checkFeasibilityAmount(amountType, amount) {

    const feasibilityId = document.getElementById('feasibility_id').value;

    

    if (!feasibilityId) {

        alert('⚠️ Please select a feasibility first!');

        return;

    }

    

    const amountTypeDisplay = amountType.replace('_', ' ').toUpperCase();

    const confirmView = confirm(

        `💰 ${amountTypeDisplay}\n\n` +

        // `Correct Amount: ₹${amount.toLocaleString('en-IN')}\n\n` +

        `This is the correct amount from the selected feasibility.\n` +

        `Would you like to view the feasibility details for more information?`

    );

    

    if (confirmView) {

        // Open feasibility details in new tab

        window.open(`/sm/feasibility/${feasibilityId}/view`, '_blank');

    }

}



// 

function calculateTotal() {

    const linksCount = parseInt(document.getElementById('no_of_links_dropdown').value) || 0;

    let total = 0;

    

    if (linksCount > 0) {

        for (let i = 1; i <= linksCount; i++) {

            const arc = parseFloat(document.getElementById(`arc_link_${i}`)?.value) || 0;

            const otc = parseFloat(document.getElementById(`otc_link_${i}`)?.value) || 0;

            const staticIP = parseFloat(document.getElementById(`static_ip_link_${i}`)?.value) || 0;

            total += (arc + otc + staticIP);

        }

    }

    

    // ✅ Format for Indian currency display

    document.getElementById('totalCost').textContent = `₹${total.toLocaleString('en-IN', { minimumFractionDigits: 2 })}`;

}



function validateBeforeSubmit() {

    const feasibilityId = document.getElementById('feasibility_id').value;

    const linksCount = parseInt(document.getElementById('no_of_links_dropdown').value);

    

    // Check if feasibility is selected

    if (!feasibilityId) {

        alert('⚠️ Please select a Feasibility Request ID first!');

        return false;

    }

    

    // Check if number of links is selected

    if (!linksCount || linksCount < 1) {

        alert('⚠️ Please select the Number of Links!');

        return false;

    }

    

    // Check if all pricing fields have values and correct amounts

    let missingAmounts = [];

    let wrongAmounts = [];

    

    for (let i = 1; i <= linksCount; i++) {

        const arc = parseFloat(document.getElementById(`arc_link_${i}`)?.value) || 0;

        const otc = parseFloat(document.getElementById(`otc_link_${i}`)?.value) || 0;

        const staticIP = parseFloat(document.getElementById(`static_ip_link_${i}`)?.value) || 0;

        

        // Check for missing amounts

        if (arc <= 0) missingAmounts.push(`ARC - Link ${i}`);

        if (otc <= 0) missingAmounts.push(`OTC - Link ${i}`);

        if (staticIP <= 0) missingAmounts.push(`STATIC IP - Link ${i}`);

        

        // Check for wrong amounts against ALL vendors (multi-vendor validation)

        const tolerance = 0.01;

        const vendorPricing = feasibilityAmounts.vendor_pricing || {};



        // Validate ARC amount

        let arcValid = false;

        Object.keys(vendorPricing).forEach(vendorKey => {

            const vendor = vendorPricing[vendorKey];

            if (vendor && vendor.arc > 0) {

                if (Math.abs(arc - vendor.arc) <= tolerance) {

                    arcValid = true;

                }

            }

        });

        if (!arcValid && arc > 0 && Object.keys(vendorPricing).length > 0) {

            const validAmounts = Object.keys(vendorPricing).map(k => 

                vendorPricing[k].name + ': ₹' + vendorPricing[k].arc

            ).filter(a => a.includes('₹') && !a.includes('₹0')).join(', ');

            wrongAmounts.push(`ARC - Link ${i} (Valid amounts: ${validAmounts}, Entered: ₹${arc})`);

        }



        // Validate OTC amount

        let otcValid = false;

        Object.keys(vendorPricing).forEach(vendorKey => {

            const vendor = vendorPricing[vendorKey];

            if (vendor && vendor.otc > 0) {

                if (Math.abs(otc - vendor.otc) <= tolerance) {

                    otcValid = true;

                }

            }

        });

        if (!otcValid && otc > 0 && Object.keys(vendorPricing).length > 0) {

            const validAmounts = Object.keys(vendorPricing).map(k => 

                vendorPricing[k].name + ': ₹' + vendorPricing[k].otc

            ).filter(a => a.includes('₹') && !a.includes('₹0')).join(', ');

            wrongAmounts.push(`OTC - Link ${i} (Valid amounts: ${validAmounts}, Entered: ₹${otc})`);

        }



        // Validate Static IP amount

        let staticIPValid = false;

        Object.keys(vendorPricing).forEach(vendorKey => {

            const vendor = vendorPricing[vendorKey];

            if (vendor && vendor.static_ip_cost > 0) {

                if (Math.abs(staticIP - vendor.static_ip_cost) <= tolerance) {

                    staticIPValid = true;

                }

            }

        });

        if (!staticIPValid && staticIP > 0 && Object.keys(vendorPricing).length > 0) {

            const validAmounts = Object.keys(vendorPricing).map(k => 

                vendorPricing[k].name + ': ₹' + vendorPricing[k].static_ip_cost

            ).filter(a => a.includes('₹') && !a.includes('₹0')).join(', ');

            wrongAmounts.push(`STATIC IP - Link ${i} (Valid amounts: ${validAmounts}, Entered: ₹${staticIP})`);

        }



    }

    

    if (missingAmounts.length > 0) {

        alert(`⚠️ Missing or invalid amounts for:\n${missingAmounts.join('\n')}\n\nPlease enter valid amounts.`);

        return false;

    }

    

    if (wrongAmounts.length > 0) {

        const confirmSubmit = confirm(

            `⚠️ WRONG AMOUNTS DETECTED!\n\n${wrongAmounts.join('\n')}\n\n` +

            `The amounts you entered don't match the feasibility amounts.\n\n` +

            `Click OK to submit anyway, or Cancel to fix the amounts.`

        );

        return confirmSubmit;

    }

    

    // All validations passed

    return true;

}



// ✅ Calculate total when page loads

window.addEventListener('DOMContentLoaded', function() {

    calculateTotal();

});

</script>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\sm\purchaseorder\edit.blade.php ENDPATH**/ ?>