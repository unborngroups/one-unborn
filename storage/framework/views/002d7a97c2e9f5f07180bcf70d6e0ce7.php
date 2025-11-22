



<?php $__env->startSection('content'); ?>

<div class="container-fluid">

    <div class="row">

        <div class="col-12">

            <div class="card">

                

                <div class="card-header text-dark">

                    <h4 class="mb-0">

                        <i class="bi bi-plus-circle"></i> Create Purchase Order

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

                     

                    <form action="<?php echo e(route('sm.purchaseorder.store')); ?>" method="POST" id="purchaseOrderForm">

                        <?php echo csrf_field(); ?>

                        

                        <div class="row">

                            

                            <div class="col-md-6 mb-3">

                                <label for="feasibility_id" class="form-label">

                                    <strong>Feasibility Request ID <span class="text-danger">*</span></strong>

                                    <small class="text-muted">(Only unused feasibilities shown)</small>

                                </label>

                                <select class="form-select <?php $__errorArgs = ['feasibility_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 

                                        id="feasibility_id" name="feasibility_id" required onchange="loadFeasibilityDetails()">

                                    <option value="">Select Available Feasibility</option>

                                     

                                    <?php $__empty_1 = true; $__currentLoopData = $closedFeasibilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feasibilityStatus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                                        <option value="<?php echo e($feasibilityStatus->feasibility->id); ?>" 

                                                <?php echo e(old('feasibility_id') == $feasibilityStatus->feasibility->id ? 'selected' : ''); ?>>

                                            <?php echo e($feasibilityStatus->feasibility->feasibility_request_id); ?> - <?php echo e($feasibilityStatus->feasibility->client->client_name ?? 'Unknown'); ?>


                                        </option>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                                        <option value="" disabled>No unused closed feasibilities available</option>

                                    <?php endif; ?>

                                </select>



                                

                                <?php if($closedFeasibilities->isEmpty()): ?>

                                    <div class="form-text text-warning">

                                        <i class="bi bi-info-circle"></i> All closed feasibilities already have purchase orders.

                                    </div>

                                <?php endif; ?>

                                <?php $__errorArgs = ['feasibility_id'];
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

                                        id="po_number" name="po_number" value="<?php echo e(old('po_number')); ?>" 

                                        placeholder="Enter PO Number" required>

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

                                       id="po_date" name="po_date" value="<?php echo e(old('po_date', date('Y-m-d'))); ?>" required>

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

                        </div>



                        

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-semibold">No. of Links *</label>

                                <select name="no_of_links" id="no_of_links_dropdown" class="form-select" required onchange="showDynamicPricing()">

                                    <option value="">Select</option>

                                    <option value="1">1</option>

                                    <option value="2">2</option>

                                    <option value="3">3</option>

                                    <option value="4">4</option>

                                </select>

                            </div>

                        </div>

                        



                        

                        <div id="dynamicPricingContainer" style="display: none;">

                            <div class="card border-info mb-3">

                                <div class="card-header bg-info text-white">

                                    <h6 class="mb-0">Pricing Details (Per Link)</h6>

                                </div>

                                <div class="card-body">

                                    <div id="pricingFieldsContainer">

                                        <!-- Dynamic pricing fields will be generated here -->

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

                                       id="contract_period" name="contract_period" value="<?php echo e(old('contract_period', 12)); ?>" required>

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

                                <label class="form-label">

                                    <strong>Total Cost (Auto-calculated)</strong>

                                </label>

                                <div class="form-control bg-light" id="totalCost">₹0.00</div>

                            </div>

                        </div>



                        

                        <div class="row">

                            <div class="col-12 text-end">

                                <a href="<?php echo e(route('sm.purchaseorder.index')); ?>" class="btn btn-secondary me-2">

                                    <i class="bi bi-arrow-left"></i> Cancel

                                </a>

                                <button type="submit" class="btn btn-success" onclick="return validateBeforeSubmit()">

                                    <i class="bi bi-save"></i> Create Purchase Order

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
    let feasibilityAmounts = {};
let validationTimeouts = {};

function loadFeasibilityDetails() {
    const feasibilityId = document.getElementById('feasibility_id').value;

    if (!feasibilityId) {
        document.getElementById('no_of_links_dropdown').value = '';
        document.getElementById('dynamicPricingContainer').style.display = 'none';
        feasibilityAmounts = {};
        return;
    }

    fetch(`/sm/purchaseorder/feasibility/${feasibilityId}/details`)
        .then(response => response.json())
        .then(data => {
            feasibilityAmounts = {
                arc_per_link: data.arc_per_link || 0,
                otc_per_link: data.otc_per_link || 0,
                static_ip_cost_per_link: data.static_ip_cost_per_link || 0,
                vendor_pricing: data.vendor_pricing || {},
                vendor_type: data.vendor_type || ""   // SELF / EXTERNAL
            };

            document.getElementById('no_of_links_dropdown').value = '';
            document.getElementById('dynamicPricingContainer').style.display = 'none';
        })
        .catch(error => {
            console.error('Error fetching feasibility details:', error);
            alert('Error loading feasibility details. Please try again.');
        });
}

// --------------------------------------------------------------------
//  SHOW DYNAMIC FIELDS PER LINK
// --------------------------------------------------------------------

function showDynamicPricing() {

    const links = parseInt(document.getElementById('no_of_links_dropdown').value);
    const container = document.getElementById('pricingFieldsContainer');

    if (!links) {
        document.getElementById('dynamicPricingContainer').style.display = 'none';
        return;
    }

    container.innerHTML = '';

    for (let i = 1; i <= links; i++) {

        const row = document.createElement('div');
        row.className = "row mb-3";

        row.innerHTML = `

            <div class="col-12 mb-2">
                <h6 class="text-primary">Link ${i} Pricing</h6>
            </div>

            <!-- ARC -->
            <div class="col-md-4">
                <label class="form-label">ARC - Link ${i} (₹) *</label>
                <div class="input-group">
                    <input type="number" step="0.01" min="0" class="form-control"
                           name="arc_link_${i}" id="arc_link_${i}"
                           placeholder="Enter ARC amount"
                           required
                           oninput="validateEnteredAmount('arc_link_${i}', 'arc_per_link')"
                           onchange="performValidation('arc_link_${i}', 'arc_per_link')"
                           onblur="calculateTotal()">

                    <button type="button" class="btn btn-outline-info btn-sm"
                            onclick="redirectToFeasibilityView()">
                        <i class="bi bi-info-circle"></i>
                    </button>
                </div>
            </div>

            <!-- OTC -->
            <div class="col-md-4">
                <label class="form-label">OTC - Link ${i} (₹) *</label>
                <div class="input-group">
                    <input type="number" step="0.01" min="0" class="form-control"
                           name="otc_link_${i}" id="otc_link_${i}"
                           placeholder="Enter OTC amount"
                           required
                           oninput="validateEnteredAmount('otc_link_${i}', 'otc_per_link')"
                           onchange="performValidation('otc_link_${i}', 'otc_per_link')"
                           onblur="calculateTotal()">

                    <button type="button" class="btn btn-outline-info btn-sm"
                            onclick="redirectToFeasibilityView()">
                        <i class="bi bi-info-circle"></i>
                    </button>
                </div>
            </div>

            <!-- STATIC IP -->
            <div class="col-md-4">
                <label class="form-label">STATIC IP - Link ${i} (₹) *</label>
                <div class="input-group">
                    <input type="number" step="0.01" min="0" class="form-control"
                           name="static_ip_link_${i}" id="static_ip_link_${i}"
                           placeholder="Enter Static IP amount"
                           required
                           oninput="validateEnteredAmount('static_ip_link_${i}', 'static_ip_cost_per_link')"
                           onchange="performValidation('static_ip_link_${i}', 'static_ip_cost_per_link')"
                           onblur="calculateTotal()">

                    <button type="button" class="btn btn-outline-info btn-sm"
                            onclick="redirectToFeasibilityView()">
                        <i class="bi bi-info-circle"></i>
                    </button>
                </div>
            </div>

        `;

        container.appendChild(row);
    }

    document.getElementById('dynamicPricingContainer').style.display = 'block';
    calculateTotal();
}

function isSelfVendor() {

    const vendorType = document.getElementById("vendor_type")?.value?.toUpperCase() || "";
    const feasibilityVendorName = document.getElementById("feasibility_vendor_name")?.value?.toLowerCase() || "";
    const feasibilityType = feasibilityAmounts.vendor_type?.toUpperCase() || "";

    // Self vendor rules
    return (
        feasibilityVendorName === "self" ||
        feasibilityType === "SELF" ||
        ["UBN", "UBS", "UBL", "INF"].includes(vendorType)
    );
}


// --------------------------------------------------------------------
// DELAYED VALIDATION (1s)
// --------------------------------------------------------------------
function validateEnteredAmount(inputId, feasibilityField) {

    const enteredValue = parseFloat(document.getElementById(inputId).value);
    const feasibilityValue = parseFloat(feasibilityAmounts[feasibilityField]) || 0;

    if (isNaN(enteredValue)) return;

    if (isSelfVendor()) {
        if (enteredValue > feasibilityValue) {
            alert(`INVALID PRICE - For Self Vendor, value cannot be higher than feasibility`);
            document.getElementById(inputId).value = "";
            return false;
        }
    } else {
        if (enteredValue <= feasibilityValue) {
            alert(`INVALID PRICE - For External Vendor, value must be higher than feasibility`);
            document.getElementById(inputId).value = "";
            return false;
        }
    }

    return true;
}

// --------------------------------------------------------------------
// VALIDATION RULES (SELF vs VENDOR)
// --------------------------------------------------------------------

function performValidation(fieldId, feasibilityField) {

    const entered = parseFloat(document.getElementById(fieldId).value) || 0;
    const feasibility = feasibilityAmounts[feasibilityField] || 0;

    if (!entered) return;

    if (isSelfVendor()) {
        if (entered > feasibility) {
            alert("INVALID PRICE - Self vendor cannot exceed feasibility");
            document.getElementById(fieldId).style.borderColor = "red";
            return;
        }
    }
    else {
        if (entered <= feasibility) {
            alert("INVALID PRICE - External vendor must be higher than feasibility");
            document.getElementById(fieldId).style.borderColor = "red";
            return;
        }
    }

    document.getElementById(fieldId).style.borderColor = "";
}

// --------------------------------------------------------------------
// TOTAL CALCULATIONS
// --------------------------------------------------------------------

function calculateTotal() {
    let totalArc = 0, totalOtc = 0, totalStatic = 0;

    const links = parseInt(document.getElementById('no_of_links_dropdown').value);

    for (let i = 1; i <= links; i++) {

        totalArc += parseFloat(document.getElementById(`arc_link_${i}`)?.value) || 0;
        totalOtc += parseFloat(document.getElementById(`otc_link_${i}`)?.value) || 0;
        totalStatic += parseFloat(document.getElementById(`static_ip_link_${i}`)?.value) || 0;
    }

    document.getElementById("total_arc").value = totalArc.toFixed(2);
    document.getElementById("total_otc").value = totalOtc.toFixed(2);
    document.getElementById("total_static_ip").value = totalStatic.toFixed(2);
}

// --------------------------------------------------------------------
// OPEN FEASIBILITY PAGE
// --------------------------------------------------------------------

function redirectToFeasibilityView() {
    const feasibilityId = document.getElementById("feasibility_id").value;
    if (feasibilityId) {
        window.open(`/sm/feasibility/${feasibilityId}/view`, "_blank");
    }
}

</script>

<script>

// let feasibilityAmounts = {};
// let validationTimeouts = {};
// const SELF_VENDORS = ["UBN", "UNS", "UBL", "INF", "SELF"];

// /* ============================================================
//    1. LOAD FEASIBILITY DETAILS (ON FEASIBILITY CHANGE)
//    ============================================================ */
// function loadFeasibilityDetails() {

//     const feasibilityId = document.getElementById('feasibility_id').value;

//     if (!feasibilityId) {
//         document.getElementById('no_of_links_dropdown').value = '';
//         document.getElementById('dynamicPricingContainer').style.display = 'none';
//         feasibilityAmounts = {};
//         return;
//     }

//     fetch(`/sm/purchaseorder/feasibility/${feasibilityId}/details`)
//         .then(res => res.json())
//         .then(data => {

//             feasibilityAmounts = {
//                 arc_per_link: data.arc_per_link || 0,
//                 otc_per_link: data.otc_per_link || 0,
//                 static_ip_cost_per_link: data.static_ip_cost_per_link || 0,
//                 vendor_pricing: data.vendor_pricing || {}
//             };

//             document.getElementById('no_of_links_dropdown').value = '';
//             document.getElementById('dynamicPricingContainer').style.display = 'none';
//         })
//         .catch(err => {
//             console.error('Error:', err);
//             alert('Error loading feasibility details');
//         });
// }

// /* ============================================================
//    2. GENERATE PRICING FIELDS BASED ON NUMBER OF LINKS
//    ============================================================ */
// function showDynamicPricing() {

//     const links = parseInt(document.getElementById('no_of_links_dropdown').value);
//     const container = document.getElementById('pricingFieldsContainer');

//     if (!links) {
//         document.getElementById('dynamicPricingContainer').style.display = 'none';
//         return;
//     }

//     container.innerHTML = '';

//     for (let i = 1; i <= links; i++) {

//         const row = document.createElement('div');
//         row.className = "row mb-3";

//         row.innerHTML = `

//             <div class="col-12 mb-2">

//                 <h6 class="text-primary">Link ${i} Pricing</h6>

//             </div>

//             <div class="col-md-4">

//                 <label class="form-label">ARC - Link ${i} (₹) *</label>

//                 <div class="input-group">

//                     <input type="number" step="0.01" min="0" class="form-control" 

//                            name="arc_link_${i}" id="arc_link_${i}" 

//                            placeholder="Enter ARC amount"

//                            required oninput="validateEnteredAmount('arc_link_${i}', 'arc_per_link')"

//                            onchange="performValidation('arc_link_${i}', 'arc_per_link')"

//                            onblur="calculateTotal()">

//                     <button type="button" class="btn btn-outline-info btn-sm" 

//                             onclick="redirectToFeasibilityView()"

//                             title="View feasibility details to verify vendor amounts">

//                         <i class="bi bi-info-circle"></i>

//                     </button>

//                 </div>

//             </div>

//             <div class="col-md-4">

//                 <label class="form-label">OTC - Link ${i} (₹) *</label>

//                 <div class="input-group">

//                     <input type="number" step="0.01" min="0" class="form-control" 

//                            name="otc_link_${i}" id="otc_link_${i}" 

//                            placeholder="Enter OTC amount"

//                            required oninput="validateEnteredAmount('otc_link_${i}', 'otc_per_link')"

//                            onchange="performValidation('otc_link_${i}', 'otc_per_link')"

//                            onblur="calculateTotal()">

//                     <button type="button" class="btn btn-outline-info btn-sm" 

//                             onclick="redirectToFeasibilityView()"

//                             title="View feasibility details to verify vendor amounts">

//                         <i class="bi bi-info-circle"></i>

//                     </button>

//                 </div>

//             </div>

//             <div class="col-md-4">

//                 <label class="form-label">STATIC IP - Link ${i} (₹) *</label>

//                 <div class="input-group">

//                     <input type="number" step="0.01" min="0" class="form-control" 

//                            name="static_ip_link_${i}" id="static_ip_link_${i}" 

//                            placeholder="Enter Static IP amount"

//                            required onchange="validateEnteredAmount('static_ip_link_${i}', 'static_ip_cost_per_link')"

//                            onblur="calculateTotal()">

//                     <button type="button" class="btn btn-outline-info btn-sm" 

//                             onclick="redirectToFeasibilityView()"

//                             title="View feasibility details to verify vendor amounts">

//                         <i class="bi bi-info-circle"></i>

//                     </button>

//                 </div>

//             </div>

//         `;
//         container.appendChild(row);
//     }

//     document.getElementById('dynamicPricingContainer').style.display = 'block';
//     calculateTotal();
// }

// /* ============================================================
//    3. DELAY VALIDATION WHILE TYPING
//    ============================================================ */
// function validateEnteredAmount(fieldId, amountType) {
//     if (validationTimeouts[fieldId]) {
//         clearTimeout(validationTimeouts[fieldId]);
//     }

//     validationTimeouts[fieldId] = setTimeout(() => {
//         performValidation(fieldId, amountType);
//     }, 800);
// }

// /* ============================================================
//    4. ACTUAL VALIDATION LOGIC (SELF & VENDOR)
//    ============================================================ */
// function performValidation(fieldId, amountType) {

//     const entered = parseFloat(document.getElementById(fieldId).value) || 0;
//     const selectedVendor = document.getElementById('vendor_name').value;

//     if (entered === 0) {
//         document.getElementById(fieldId).classList.remove('is-invalid');
//         return;
//     }

//     // Mapping
//     const mapType = {
//         arc_per_link: "arc",
//         otc_per_link: "otc",
//         static_ip_cost_per_link: "static_ip_cost"
//     };

//     const key = mapType[amountType];

//     /* ------------------ SELF VENDOR RULE ------------------ */
//     if (SELF_VENDORS.includes(selectedVendor)) {

//         const feasibilityValue = feasibilityAmounts[amountType] || 0;

//         if (entered > feasibilityValue) {
//             document.getElementById(fieldId).classList.add("is-invalid");
//             alert(
//                 `SELF Vendor - NOT ALLOWED\n\n` +
//                 `Entered: ₹${entered}\nFeasibility: ₹${feasibilityValue}\n\n` +
//                 `PO amount cannot be HIGHER than feasibility.`
//             );
//             return;
//         }

//         document.getElementById(fieldId).classList.remove("is-invalid");
//         return;
//     }

//     /* ------------------ EXTERNAL VENDOR RULE ------------------ */
//     const vendorPricing = feasibilityAmounts.vendor_pricing || {};
//     let vendorAmountList = [];

//     Object.keys(vendorPricing).forEach(v => {
//         if (vendorPricing[v] && vendorPricing[v][key] > 0) {
//             vendorAmountList.push(parseFloat(vendorPricing[v][key]));
//         }
//     });

//     if (vendorAmountList.length === 0) return;

//     const minVendorAmount = Math.min(...vendorAmountList);

//     // PO amount must be STRICTLY HIGHER than vendor price
//     if (entered <= minVendorAmount) {

//         document.getElementById(fieldId).classList.add("is-invalid");

//         alert(
//             `EXTERNAL Vendor - NOT ALLOWED\n\n` +
//             `Entered: ₹${entered}\nLowest Vendor Price: ₹${minVendorAmount}\n\n` +
//             `PO amount must be HIGHER than vendor amount (not equal or lower).`
//         );
//         return;
//     }

//     document.getElementById(fieldId).classList.remove('is-invalid');
// }

// /* ============================================================
//    5. TOTAL CALCULATION
//    ============================================================ */
// function calculateTotal() {
//     // optional, use your existing total logic
// }

// /* ============================================================
//    6. FINAL SUBMIT VALIDATION
//    ============================================================ */
// function validateBeforeSubmit() {

//     const links = parseInt(document.getElementById('no_of_links_dropdown').value) || 0;

//     for (let i = 1; i <= links; i++) {

//         let fields = [
//             { id: `arc_link_${i}`, type: "arc_per_link" },
//             { id: `otc_link_${i}`, type: "otc_per_link" },
//             { id: `static_ip_link_${i}`, type: "static_ip_cost_per_link" }
//         ];

//         for (let f of fields) {

//             performValidation(f.id, f.type);

//             if (document.getElementById(f.id).classList.contains('is-invalid')) {
//                 alert(`Please fix Link ${i} pricing before submitting.`);
//                 return false;
//             }
//         }
//     }

//     return true;
// }

// </script>

















// 

// <script>

// // let feasibilityAmounts = {}; // Store feasibility amounts for validation

// function validateLink(linkIndex) {

//     let arcFeas = parseFloat(document.getElementById(`feas_arc_${linkIndex}`).value);
//     let arcPo   = parseFloat(document.getElementById(`arc_${linkIndex}`).value);

//     let minRequiredARC = arcFeas * 1.20;

//     // REMOVE old error
//     document.getElementById(`arc_${linkIndex}`).classList.remove("is-invalid");

//     // 1. LOWER than feasibility
//     if (arcPo < arcFeas) {
//         showError(linkIndex, "ARC cannot be lower than feasibility amount.");
//         return;
//     }

//     // 2. MATCHED with feasibility
//     if (arcPo == arcFeas) {
//         showError(linkIndex, "ARC cannot match feasibility amount. Minimum 20% higher required.");
//         return;
//     }

//     // 3. HIGHER but still <20%
//     if (arcPo < minRequiredARC) {
//         showError(linkIndex, "ARC must be at least 20% higher than feasibility.");
//         return;
//     }
// }

// function showError(linkIndex, msg) {
//     document.getElementById(`arc_${linkIndex}`).classList.add("is-invalid");
//     alert(msg);
// }


// function loadFeasibilityDetails() {

//     const feasibilityId = document.getElementById('feasibility_id').value;

    

//     if (!feasibilityId) {

//         // Reset form when no feasibility selected

//         document.getElementById('no_of_links_dropdown').value = '';

//         document.getElementById('dynamicPricingContainer').style.display = 'none';

//         feasibilityAmounts = {};

//         return;

//     }



//     // ✅ AJAX request to get feasibility details including pricing

//     fetch(`/sm/purchaseorder/feasibility/${feasibilityId}/details`)

//         .then(response => response.json())

//         .then(data => {

//             console.log('Feasibility data received:', data); // Debug log

            

//             // Store feasibility amounts for validation (both old and new format)

//             feasibilityAmounts = {

//                 arc_per_link: data.arc_per_link || 0,

//                 otc_per_link: data.otc_per_link || 0,

//                 static_ip_cost_per_link: data.static_ip_cost_per_link || 0,

//                 vendor_pricing: data.vendor_pricing || {} // Store vendor pricing data

//             };

            

//             console.log('Stored feasibilityAmounts:', feasibilityAmounts); // Debug log

            

//             // NO AUTO-FILL for number of links - user must select manually

//             // Keep dropdown empty so user has to choose

//             document.getElementById('no_of_links_dropdown').value = '';

//             document.getElementById('dynamicPricingContainer').style.display = 'none';

//         })

//         .catch(error => {

//             console.error('Error fetching feasibility details:', error);

//             alert('Error loading feasibility details. Please try again.');

//         });

// }



// function showDynamicPricing() {

//     const linksCount = parseInt(document.getElementById('no_of_links_dropdown').value);

//     const container = document.getElementById('pricingFieldsContainer');

    

//     if (!linksCount) {

//         document.getElementById('dynamicPricingContainer').style.display = 'none';

//         return;

//     }

    

//     // Clear existing fields

//     container.innerHTML = '';

    

//     // Generate pricing fields for each link

//     for (let i = 1; i <= linksCount; i++) {

//         const linkRow = document.createElement('div');

//         linkRow.className = 'row mb-3';

//         // Generate expected amounts display for all vendors

//         const vendorPricing = feasibilityAmounts.vendor_pricing || {};

//         let arcExpected = [];

//         let otcExpected = [];

//         let staticIpExpected = [];

        

//         Object.keys(vendorPricing).forEach(vendorKey => {

//             const vendor = vendorPricing[vendorKey];

//             if (vendor && vendor.name) {

//                 if (vendor.arc > 0) arcExpected.push(vendor.name + ': ₹' + vendor.arc);

//                 if (vendor.otc > 0) otcExpected.push(vendor.name + ': ₹' + vendor.otc);

//                 if (vendor.static_ip_cost > 0) staticIpExpected.push(vendor.name + ': ₹' + vendor.static_ip_cost);

//             }

//         });



//         linkRow.innerHTML = `

//             <div class="col-12 mb-2">

//                 <h6 class="text-primary">Link ${i} Pricing</h6>

//             </div>

//             <div class="col-md-4">

//                 <label class="form-label">ARC - Link ${i} (₹) *</label>

//                 <div class="input-group">

//                     <input type="number" step="0.01" min="0" class="form-control" 

//                            name="arc_link_${i}" id="arc_link_${i}" 

//                            placeholder="Enter ARC amount"

//                            required oninput="validateEnteredAmount('arc_link_${i}', 'arc_per_link')"

//                            onchange="performValidation('arc_link_${i}', 'arc_per_link')"

//                            onblur="calculateTotal()">

//                     <button type="button" class="btn btn-outline-info btn-sm" 

//                             onclick="redirectToFeasibilityView()"

//                             title="View feasibility details to verify vendor amounts">

//                         <i class="bi bi-info-circle"></i>

//                     </button>

//                 </div>

//             </div>

//             <div class="col-md-4">

//                 <label class="form-label">OTC - Link ${i} (₹) *</label>

//                 <div class="input-group">

//                     <input type="number" step="0.01" min="0" class="form-control" 

//                            name="otc_link_${i}" id="otc_link_${i}" 

//                            placeholder="Enter OTC amount"

//                            required oninput="validateEnteredAmount('otc_link_${i}', 'otc_per_link')"

//                            onchange="performValidation('otc_link_${i}', 'otc_per_link')"

//                            onblur="calculateTotal()">

//                     <button type="button" class="btn btn-outline-info btn-sm" 

//                             onclick="redirectToFeasibilityView()"

//                             title="View feasibility details to verify vendor amounts">

//                         <i class="bi bi-info-circle"></i>

//                     </button>

//                 </div>

//             </div>

//             <div class="col-md-4">

//                 <label class="form-label">STATIC IP - Link ${i} (₹) *</label>

//                 <div class="input-group">

//                     <input type="number" step="0.01" min="0" class="form-control" 

//                            name="static_ip_link_${i}" id="static_ip_link_${i}" 

//                            placeholder="Enter Static IP amount"

//                            required onchange="validateEnteredAmount('static_ip_link_${i}', 'static_ip_cost_per_link')"

//                            onblur="calculateTotal()">

//                     <button type="button" class="btn btn-outline-info btn-sm" 

//                             onclick="redirectToFeasibilityView()"

//                             title="View feasibility details to verify vendor amounts">

//                         <i class="bi bi-info-circle"></i>

//                     </button>

//                 </div>

//             </div>

//         `;

//         container.appendChild(linkRow);

//     }
//     document.getElementById('dynamicPricingContainer').style.display = 'block';

//     calculateTotal();

// }
// // Store timeout IDs for each field to prevent multiple rapid validations
// // let validationTimeouts = {};

// function validateEnteredAmount(fieldId, amountType) {

//     console.log('🔍 validateEnteredAmount called:', fieldId, amountType);

//     // Clear any existing timeout for this field
//     if (validationTimeouts[fieldId]) {
//         clearTimeout(validationTimeouts[fieldId]);
//     }

//     // Set a delay of 1 second before validating
//     validationTimeouts[fieldId] = setTimeout(() => {
//         performValidation(fieldId, amountType);
//     }, 1000);
// }

// function performValidation(fieldId, amountType) {

//     const enteredAmount = parseFloat(document.getElementById(fieldId).value) || 0;

//     console.log('📊 Entered amount:', enteredAmount);
//     console.log('Current feasibilityAmounts:', feasibilityAmounts);

//     // Skip validation for empty amounts

//     if (enteredAmount === 0) {

//         document.getElementById(fieldId).style.borderColor = '';

//         document.getElementById(fieldId).classList.remove('is-invalid');

//         return;

//     }
//     // Get vendor pricing from feasibility

//     const vendorPricing = feasibilityAmounts.vendor_pricing || {};

//     console.log('Vendor pricing:', vendorPricing);

    

//     // Map field types to vendor pricing fields

//     const fieldMapping = {

//         'arc_per_link': 'arc',

//         'otc_per_link': 'otc', 

//         'static_ip_cost_per_link': 'static_ip_cost'

//     };
//     const pricingField = fieldMapping[amountType];

//     if (!pricingField) {

//         console.warn('Unknown amount type:', amountType);

//         return;

//     }
//     // Collect all valid amounts from all vendors

//     let validAmounts = [];

//     Object.keys(vendorPricing).forEach(vendorKey => {

//         const vendor = vendorPricing[vendorKey];

//         if (vendor && vendor[pricingField] > 0) {

//             validAmounts.push({

//                 vendor: vendor.name,

//                 amount: parseFloat(vendor[pricingField])

//             });

//         }

//     });

//     // Check if entered amount matches any vendor amount (exact match - NOT ALLOWED)
//     let isExactMatch = false;
//     let matchedVendor = '';
    
//     validAmounts.forEach(validAmount => {
//         if (Math.abs(enteredAmount - validAmount.amount) <= 0.01) {
//             isExactMatch = true;
//             matchedVendor = validAmount.vendor;
//         }
//     });

//     // Check if amount is lower than minimum vendor amount
//     const minVendorAmount = validAmounts.length > 0 ? Math.min(...validAmounts.map(v => v.amount)) : 0;
//     const isLowAmount = enteredAmount < minVendorAmount && enteredAmount > 0;

//     // Show error if exact match OR low amount
//     if ((isExactMatch || isLowAmount) && validAmounts.length > 0 && enteredAmount > 0) {

//         // Change field border to red to indicate error
//         document.getElementById(fieldId).style.borderColor = '#dc3545';
//         document.getElementById(fieldId).classList.add('is-invalid');

//         // Create message showing all valid vendor amounts
//         const vendorAmountsList = validAmounts.map(v => 
//             `${v.vendor}: ₹${v.amount.toLocaleString('en-IN')}`
//         ).join('\n');

//         if (isExactMatch) {
//             alert(
//                 `MATCHED PRICE (NOT ALLOWED)\n\n` +
//                 `Amount ₹${enteredAmount} matches ${matchedVendor} vendor amount.\n\n` +
//                 `Please enter a higher amount.`
//             );
//         } else if (isLowAmount) {
//             alert(
//                 `LOW PRICE (NOT ALLOWED)\n\n` +
//                 `Amount ₹${enteredAmount} is lower than vendor amounts.\n\n` +
//                 `Valid amounts from feasibility:\n${vendorAmountsList}\n\n` +
//                 `Please enter a higher amount.`
//             );
//         }

//     } else {

//         // Reset field styling if amount is correct (higher than all vendor amounts)
//         document.getElementById(fieldId).style.borderColor = '';
//         document.getElementById(fieldId).classList.remove('is-invalid');

//     }

// }
// // Function to show all vendor pricing information

// function showVendorPricing() {

//     if (!feasibilityAmounts.vendor_pricing) {

//         alert('No vendor pricing information available. Please select a feasibility first.');

//         return;

//     }

//     const vendorPricing = feasibilityAmounts.vendor_pricing;

//     let pricingInfo = '💰 VENDOR PRICING INFORMATION\n\n';

//     Object.keys(vendorPricing).forEach(vendorKey => {

//         const vendor = vendorPricing[vendorKey];

//         if (vendor && vendor.name) {

//             pricingInfo += `🏢 ${vendor.name}:\n`;

//             pricingInfo += `  • ARC: ₹${vendor.arc.toLocaleString('en-IN')}\n`;

//             pricingInfo += `  • OTC: ₹${vendor.otc.toLocaleString('en-IN')}\n`;

//             pricingInfo += `  • Static IP: ₹${vendor.static_ip_cost.toLocaleString('en-IN')}\n\n`;

//         }

//     });

//     pricingInfo += '💡 Tip: You can enter any of the vendor amounts shown above.';

//     alert(pricingInfo);

// }
// // Function to directly redirect to feasibility view page

// function redirectToFeasibilityView() {

//     const feasibilityId = document.getElementById('feasibility_id').value;

//     if (!feasibilityId) {

//         alert('⚠️ Please select a feasibility first!');

//         return;

//     }

//     // Open feasibility details in new tab to verify vendor amounts

//     window.open(`/sm/feasibility/${feasibilityId}/view`, '_blank');

// }
// function checkFeasibilityAmount(amountType, amount) {

//     const feasibilityId = document.getElementById('feasibility_id').value;

//     if (!feasibilityId) {

//         alert('⚠️ Please select a feasibility first!');

//         return;

//     }
//     const amountTypeDisplay = amountType.replace('_', ' ').toUpperCase();

//     const confirmView = confirm(

//         `💰 ${amountTypeDisplay}\n\n` +

//         // `Correct Amount: ₹${amount.toLocaleString('en-IN')}\n\n` +

//         `This is the correct amount from the selected feasibility.\n` +

//         `Would you like to view the feasibility details for more information?`

//     );
//     if (confirmView) {

//         // Open feasibility details in new tab

//         window.open(`/sm/feasibility/${feasibilityId}/view`, '_blank');

//     }

// }

// // 

// function calculateTotal() {

//     const linksCount = parseInt(document.getElementById('no_of_links_dropdown').value) || 0;

//     let total = 0;
//     if (linksCount > 0) {

//         for (let i = 1; i <= linksCount; i++) {

//             const arc = parseFloat(document.getElementById(`arc_link_${i}`)?.value) || 0;

//             const otc = parseFloat(document.getElementById(`otc_link_${i}`)?.value) || 0;

//             const staticIP = parseFloat(document.getElementById(`static_ip_link_${i}`)?.value) || 0;

//             total += (arc + otc + staticIP);

//         }

//     }
//     // ✅ Format for Indian currency display
//     document.getElementById('totalCost').textContent = `₹${total.toLocaleString('en-IN', { minimumFractionDigits: 2 })}`;

// }
// function validateBeforeSubmit() {
//     const feasibilityId = document.getElementById('feasibility_id').value;

//     const linksCount = parseInt(document.getElementById('no_of_links_dropdown').value);

//     // Check if feasibility is selected

//     if (!feasibilityId) {

//         alert('⚠️ Please select a Feasibility Request ID first!');

//         return false;

//     }

//     // Check if number of links is selected

//     if (!linksCount || linksCount < 1) {

//         alert('⚠️ Please select the Number of Links!');

//         return false;

//     }

//     // Check if all pricing fields have values

//     let missingAmounts = [];
//     // ✅ wrongAmounts array removed - now using only 20% validation

//     for (let i = 1; i <= linksCount; i++) {

//         const arc = parseFloat(document.getElementById(`arc_link_${i}`)?.value) || 0;

//         const otc = parseFloat(document.getElementById(`otc_link_${i}`)?.value) || 0;

//         const staticIP = parseFloat(document.getElementById(`static_ip_link_${i}`)?.value) || 0;

//         // Check for missing amounts

//         if (arc <= 0) missingAmounts.push(`ARC - Link ${i}`);

//         if (otc <= 0) missingAmounts.push(`OTC - Link ${i}`);

//         if (staticIP <= 0) missingAmounts.push(`STATIC IP - Link ${i}`);

//         // 🗑️ OLD EXACT MATCH VALIDATION REMOVED
//         // Now only using 20% minimum validation below

//     }

//     if (missingAmounts.length > 0) {

//         alert(`⚠️ Missing or invalid amounts for:\n${missingAmounts.join('\n')}\n\nPlease enter valid amounts.`);

//         return false;

//     }

//     // 🔥 EXACT MATCH VALIDATION - Check if amounts match exactly with feasibility
//     let exactMatchErrors = [];
    
//     // Calculate total amounts entered by user
//     let totalARC = 0, totalOTC = 0, totalStaticIP = 0;
//     for (let i = 1; i <= linksCount; i++) {
//         totalARC += parseFloat(document.getElementById(`arc_link_${i}`)?.value) || 0;
//         totalOTC += parseFloat(document.getElementById(`otc_link_${i}`)?.value) || 0;
//         totalStaticIP += parseFloat(document.getElementById(`static_ip_link_${i}`)?.value) || 0;
//     }
    
//     // Get minimum feasibility amounts from all vendors and calculate total feasibility amounts
//     const vendorPricing = feasibilityAmounts.vendor_pricing || {};
//     let minARC = Number.MAX_VALUE, minOTC = Number.MAX_VALUE, minStaticIP = Number.MAX_VALUE;
    
//     Object.keys(vendorPricing).forEach(vendorKey => {
//         const vendor = vendorPricing[vendorKey];
//         if (vendor) {
//             if (vendor.arc > 0) minARC = Math.min(minARC, vendor.arc);
//             if (vendor.otc > 0) minOTC = Math.min(minOTC, vendor.otc);
//             if (vendor.static_ip_cost > 0) minStaticIP = Math.min(minStaticIP, vendor.static_ip_cost);
//         }
//     });

//     // Calculate total feasibility amounts (per link × number of links)
//     const totalFeasibilityARC = minARC !== Number.MAX_VALUE ? minARC * linksCount : 0;
//     const totalFeasibilityOTC = minOTC !== Number.MAX_VALUE ? minOTC * linksCount : 0;
//     const totalFeasibilityStaticIP = minStaticIP !== Number.MAX_VALUE ? minStaticIP * linksCount : 0;

//     console.log('Debug - Purchase Order Amounts:', {totalARC, totalOTC, totalStaticIP});
//     console.log('Debug - Feasibility Amounts:', {totalFeasibilityARC, totalFeasibilityOTC, totalFeasibilityStaticIP});

//     // 🚫 CHECK FOR EXACT MATCHES - Not allowed!
//     if (totalFeasibilityARC > 0 && Math.abs(totalARC - totalFeasibilityARC) < 0.01) {
//         exactMatchErrors.push(`ARC amount (₹${totalARC.toFixed(2)}) cannot be exactly the same as feasibility amount (₹${totalFeasibilityARC.toFixed(2)})!`);
//     }
    
//     if (totalFeasibilityOTC > 0 && Math.abs(totalOTC - totalFeasibilityOTC) < 0.01) {
//         exactMatchErrors.push(`OTC amount (₹${totalOTC.toFixed(2)}) cannot be exactly the same as feasibility amount (₹${totalFeasibilityOTC.toFixed(2)})!`);
//     }
    
//     if (totalFeasibilityStaticIP > 0 && Math.abs(totalStaticIP - totalFeasibilityStaticIP) < 0.01) {
//         exactMatchErrors.push(`Static IP cost (₹${totalStaticIP.toFixed(2)}) cannot be exactly the same as feasibility amount (₹${totalFeasibilityStaticIP.toFixed(2)})!`);
//     }

//     // Show exact match error first
//     if (exactMatchErrors.length > 0) {
//         alert(`MATCHED PRICE (NOT ALLOWED)\n\n${exactMatchErrors.join('\n\n')}\n\nPurchase Order amounts cannot exactly match feasibility amounts.`);
//         return false;
//     }

//     // 🔥 20% MINIMUM VALIDATION  
//     let lowAmountErrors = [];
    
//     // Check if amounts are at least 20% higher than total feasibility amounts
//     if (totalFeasibilityARC > 0) {
//         const requiredARC = totalFeasibilityARC * 1.20;
//         if (totalARC < requiredARC) {
//             lowAmountErrors.push(`ARC amount (₹${totalARC.toFixed(2)}) must be at least 20% higher than feasibility total (₹${totalFeasibilityARC.toFixed(2)}). Required: ₹${requiredARC.toFixed(2)}`);
//         }
//     }
    
//     if (totalFeasibilityOTC > 0) {
//         const requiredOTC = totalFeasibilityOTC * 1.20;
//         if (totalOTC < requiredOTC) {
//             lowAmountErrors.push(`OTC amount (₹${totalOTC.toFixed(2)}) must be at least 20% higher than feasibility total (₹${totalFeasibilityOTC.toFixed(2)}). Required: ₹${requiredOTC.toFixed(2)}`);
//         }
//     }
    
//     if (totalFeasibilityStaticIP > 0) {
//         const requiredStaticIP = totalFeasibilityStaticIP * 1.20;
//         if (totalStaticIP < requiredStaticIP) {
//             lowAmountErrors.push(`Static IP cost (₹${totalStaticIP.toFixed(2)}) must be at least 20% higher than feasibility total (₹${totalFeasibilityStaticIP.toFixed(2)}). Required: ₹${requiredStaticIP.toFixed(2)}`);
//         }
//     }
    
//     // � RE-ENABLED: 20% minimum validation
//     if (lowAmountErrors.length > 0) {
//         alert(`LOW PRICE (NOT ALLOWED)\n\n${lowAmountErrors.join('\n\n')}\n\nPurchase Order amounts must be at least 20% higher than feasibility amounts.`);
//         return false;
//     }
    
//     // All validations passed
//     return true;    
// }

// </script>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\wlcome\multipleuserpage\resources\views/sm/purchaseorder/create.blade.php ENDPATH**/ ?>