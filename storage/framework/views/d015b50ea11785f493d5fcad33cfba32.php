

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-dark">
                    <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Create Purchase Order</h4>
                </div>
                <div class="card-body position-relative">
                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Validation Error:</strong> <?php echo e(session('error')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('sm.purchaseorder.store')); ?>" method="POST" id="purchaseOrderForm">
                    
                    <form action="<?php echo e(route('sm.purchaseorder.store')); ?>" method="POST" id="purchaseOrderForm">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="feasibility_id" class="form-label"><strong>Feasibility Request ID <span class="text-danger">*</span></strong></label>
                                <select class="form-select" id="feasibility_id" name="feasibility_id" required onchange="loadFeasibilityDetails()">
                                    <option value="">Select Available Feasibility</option>
                                    <?php $__currentLoopData = $closedFeasibilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($feas->feasibility->id); ?>"
                                                data-status-id="<?php echo e($feas->id); ?>">
                                            <?php echo e($feas->feasibility->feasibility_request_id); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                               
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>PO Number *</strong></label>
                                <input type="text" class="form-control" id="po_number" name="po_number" onblur="checkPoNumber()" required>
                                <input type="hidden" id="allow_reuse" name="allow_reuse" value="0">
                                <input type="hidden" id="forceSubmit" name="forceSubmit" value="0">
                                <div class="border rounded-2 border-danger bg-danger bg-opacity-10 text-danger small d-none mt-2" id="poTakenAlert" role="alert">
                                    <div class="d-flex align-items-center justify-content-between gap-2 p-2">
                                        <div class="d-flex align-items-center gap-1">
                                            <i class="bi bi-exclamation-circle-fill"></i>
                                            That PO number already exists. Would you like to reuse it?
                                        </div>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-danger" id="reuseNo">Change</button>
                                            <button type="button" class="btn btn-sm btn-danger" id="reuseYes">Reuse</button>
                                        </div>
                                    </div>
                                </div>
                                <?php $__errorArgs = ['po_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>PO Date *</strong></label>
                                <input type="date" class="form-control" id="po_date" name="po_date" required value="<?php echo e(date('Y-m-d')); ?>">
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

                        <div id="dynamicPricingContainer" style="display:none;">
                            <div class="card border-info mb-3">
                                <div class="card-header bg-info text-white"><h6 class="mb-0">Pricing Details (Per Link)</h6></div>
                                <div class="card-body"><div id="pricingFieldsContainer"></div></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>Contract Period (Months) *</strong></label>
                                <input type="number" min="1" class="form-control" id="contract_period" name="contract_period" value="12" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>Total Cost</strong></label>
                                <input type="text" id="totalCost" class="form-control bg-light" readonly value="₹0.00">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 text-end">
                                <a href="<?php echo e(route('sm.purchaseorder.index')); ?>" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-success">Create Purchase Order</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function checkPoNumber() {
    let poNumber = document.getElementById("po_number").value.trim();
    if (poNumber === "") {
        togglePoTakenAlert(false);
        return;
    }

    fetch("/check-po-number?po_number=" + encodeURIComponent(poNumber))
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                document.getElementById('allow_reuse').value = 0;
                togglePoTakenAlert(true);
            } else {
                document.getElementById('allow_reuse').value = 0;
                togglePoTakenAlert(false);
            }
        })
        .catch(error => console.error("Error checking PO number:", error));
}

function togglePoTakenAlert(show) {
    const alertBox = document.getElementById('poTakenAlert');
    if (!alertBox) return;
    alertBox.classList.toggle('d-none', !show);
}

document.addEventListener('DOMContentLoaded', function () {
    const reuseYes = document.getElementById('reuseYes');
    const reuseNo = document.getElementById('reuseNo');

    if (reuseYes) {
        reuseYes.addEventListener('click', function () {
            document.getElementById('allow_reuse').value = 1;
            togglePoTakenAlert(false);
        });
    }

    if (reuseNo) {
        reuseNo.addEventListener('click', function () {
            document.getElementById('allow_reuse').value = 0;
            document.getElementById('po_number').value = '';
            togglePoTakenAlert(false);
            document.getElementById('po_number').focus();
        });
    }
});

let feasibilityAmounts = {};

function loadFeasibilityDetails() {
    const id = document.getElementById('feasibility_id').value;
    if (!id) {
        feasibilityAmounts = {};
        document.getElementById('dynamicPricingContainer').style.display = 'none';
        return;
    }

    fetch(`/sm/purchaseorder/feasibility/${id}/details`)
        .then(r => r.json())
        .then(d => {
            feasibilityAmounts = {
                arc_per_link: parseFloat(d.arc_per_link) || 0,
                otc_per_link: parseFloat(d.otc_per_link) || 0,
                static_ip_cost_per_link: parseFloat(d.static_ip_cost_per_link) || 0,
                vendor_type: (d.vendor_type || '').toUpperCase()
            };
        });
}

function showDynamicPricing() {
    const links = parseInt(document.getElementById('no_of_links_dropdown').value);
    const c = document.getElementById('pricingFieldsContainer');

    if (!links) {
        c.innerHTML = '';
        document.getElementById('dynamicPricingContainer').style.display = 'none';
        return;
    }

    c.innerHTML = '';

    for (let i = 1; i <= links; i++) {
        c.innerHTML += `
        <div class="col-12 mb-2"><h6 class="text-primary">Link ${i} Pricing</h6></div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">ARC - Link ${i} *</label>
                <div class="input-group">
                    <input type="number" step="0.01" min="0" id="arc_link_${i}" name="arc_link_${i}" class="form-control" onblur="validateEnteredAmount('arc_link_${i}', 'arc_per_link')" oninput="calculateTotal()">
                    <button type="button" class="btn btn-outline-info btn-sm" onclick="redirectToFeasibilityView()"><i class="bi bi-info-circle"></i></button>
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label">OTC - Link ${i} *</label>
                <div class="input-group">
                    <input type="number" step="0.01" min="0" id="otc_link_${i}" name="otc_link_${i}" class="form-control" onblur="validateEnteredAmount('otc_link_${i}', 'otc_per_link')" oninput="calculateTotal()">
                    <button type="button" class="btn btn-outline-info btn-sm" onclick="redirectToFeasibilityView()"><i class="bi bi-info-circle"></i></button>
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label">Static IP - Link ${i} *</label>
                <div class="input-group">
                    <input type="number" step="0.01" min="0" id="static_ip_link_${i}" name="static_ip_link_${i}" class="form-control" onblur="validateEnteredAmount('static_ip_link_${i}', 'static_ip_cost_per_link')" oninput="calculateTotal()">
                    <button type="button" class="btn btn-outline-info btn-sm" onclick="redirectToFeasibilityView()"><i class="bi bi-info-circle"></i></button>
                </div>
            </div>
        </div>`;
    }

    document.getElementById('dynamicPricingContainer').style.display = 'block';
}

function validateEnteredAmount(inputId, key) {
    const v = parseFloat(document.getElementById(inputId).value) || 0;
    const f = feasibilityAmounts[key] || 0;
    const selfTypes = ['SELF', 'INF', 'UBN', 'UBS', 'UBL'];
    const vendorType = (feasibilityAmounts.vendor_type || '').toUpperCase();
    const isSelf = selfTypes.includes(vendorType);
    const formattedFeasibility = f.toFixed(2);

    if (isSelf) {
        if (f <= 0) {
            return; // nothing to validate if feasibility amount is missing
        }

        if (v > f + 0.001) {
            alert(`Self vendor amount must be equal to or less than the feasibility value.`);
            document.getElementById(inputId).value = '';
        }

        return;
    }

    const requiredMinimum = +(f * 1.20).toFixed(2);
    if (f <= 0) {
        return; // nothing to validate when there is no baseline
    }

    if (v < requiredMinimum - 0.001) {
        alert(`Amount must be at least 20% higher than the feasibility value. Minimum allowed: ₹${requiredMinimum.toFixed(2)}.`);
        document.getElementById(inputId).value = '';
    }
}

function calculateTotal() {
    let t = 0;
    const links = parseInt(document.getElementById('no_of_links_dropdown').value) || 0;

    for (let i = 1; i <= links; i++) {
        t += (parseFloat(document.getElementById(`arc_link_${i}`)?.value) || 0);
        t += (parseFloat(document.getElementById(`otc_link_${i}`)?.value) || 0);
        t += (parseFloat(document.getElementById(`static_ip_link_${i}`)?.value) || 0);
    }

    document.getElementById('totalCost').value = '₹' + t.toFixed(2);
}

function redirectToFeasibilityView() {
    const select = document.getElementById('feasibility_id');
    const selectedOption = select?.selectedOptions?.[0];
    const statusId = selectedOption?.dataset?.statusId;
    if (statusId) {
        window.open(`/sm/feasibility/${statusId}/view`, '_blank');
    }
}

</script>
<style>
    .card-body.position-relative {
        transition: padding-top .2s ease;
    }

    .card-body.alert-visible {
        padding-top: 190px;
    }

    #warnBox {
        position: absolute;
        top: 1rem;
        left: 1rem;
        right: 1rem;
        z-index: 5;
        border-radius: .35rem;
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\sm\purchaseorder\create.blade.php ENDPATH**/ ?>