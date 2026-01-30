@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-dark">
                    <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Create Purchase Order</h4>
                </div>

                {{-- Show validation errors --}}

    @if ($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif
    
                <div class="card-body position-relative">
                    <form method="POST" action="{{ route('sm.purchaseorder.store') }}" id="purchaseOrderForm" enctype="multipart/form-data">
                        <div id="poDuplicateAlert" class="alert alert-white mt-3 d-none" role="alert">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <strong id="poDuplicateMessage">PO number already exists.</strong>
                                    <p class="mb-2 small text-muted">You can either reuse the existing PO or enter a new number.</p>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-primary btn-sm me-2" id="poDuplicateReuse">Use this PO</button>
                                    <button type="button" class="btn btn-danger btn-sm" id="poDuplicateNew">Create new</button>
                                </div>
                            </div>
                        </div>
                    @if(session('error') && !session('po_duplicate'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Validation Error:</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

    <!-- .dynamic-pricing-row.hide-static-ip .pricing-col {
        flex: 0 0 50%;
        max-width: 50%;
    } -->

</style>
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="feasibility_id" class="form-label"><strong>Feasibility Request ID <span class="text-danger">*</span></strong></label>
                                <select class="form-select select2" id="feasibility_id" name="feasibility_id" required onchange="loadFeasibilityDetails()">
                                    <option value="">Select Available Feasibility</option>
                                    @foreach($closedFeasibilities as $feas)
                                        <option value="{{ $feas->feasibility->id }}"
                                                data-status-id="{{ $feas->id }}">
                                            {{ $feas->feasibility->feasibility_request_id }}
                                        </option>
                                    @endforeach
                                </select>
                                <!-- Feasibility Address Preview -->
                                 <div class="row">
                                    <div id="feasibility_address_box" class="col-md-12 d-none">
                                    <label class="form-label mb-1"><strong>Feasibility Address</strong></label>
                                    <textarea id="feasibility_address" class="form-control bg-light" rows="2" readonly></textarea>
                                </div>
                                <!-- Feasibility Speed Preview -->
                                <div id="feasibility_speed_box" class="col-md-3 d-none">
                                    <label class="form-label mb-1"> Speed</label>
                                    <input id="feasibility_speed" class="form-control bg-light" type="text" readonly />
                                </div>
                                 </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>PO Number *</strong></label>
                                <input type="text" class="form-control" id="po_number" name="po_number" onblur="checkPoNumber()" required>
                                <input type="hidden" id="allow_reuse" name="allow_reuse" value="0">
                                <input type="hidden" id="forceSubmit" name="forceSubmit" value="0">
                                @error('po_number')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror

                                <!-- <div id="poDuplicateAlert" class="alert alert-warning mt-3 d-none" role="alert">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div>
                                            <strong id="poDuplicateMessage">PO number already exists.</strong>
                                            <p class="mb-2 small text-muted">You can either reuse the existing PO or enter a new number.</p>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-warning btn-sm me-2" id="poDuplicateReuse">Use this PO</button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" id="poDuplicateNew">Create new</button>
                                        </div>
                                    </div>
                                </div> -->

                            </div>

                            

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>PO Date *</strong></label>
                                <input type="date" class="form-control" id="po_date" name="po_date" required value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">No. of Links *</label>
                                <select id="no_of_links_dropdown" class="form-select" required onchange="showDynamicPricing()">
                                    <option value="">Select</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                </select>
                            </div>
                            <input type="hidden" name="no_of_links" id="no_of_links_hidden">
                            <input type="hidden" id="static_ip_required" name="static_ip_required" value="0">
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

                            <!-- <div class="col-md-4 mb-3">
                                <label for="form-label"><strong>PDF</strong></label>
                                <img src="/images/purchaseorder" alt="">
                            </div> -->
                        </div>

                        <div class="row">
                            <!-- Import Document Upload -->
                            <div class="col-md-4 mb-3">
                                <label for="import_file">Import Document</label>
                                <input type="file" class="form-control" name="import_file" id="import_file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                                <div id="importFileInfo" class="mt-2"></div>
                            </div>
                        </div>

                        <!-- already po import file -->
                         {{-- Existing file --}}
                        <div class="mb-3 d-none" id="existingFileContainer">
                            <label class="form-label">Existing PO Document</label><br>
                            <a href="#"
                               id="existingFileLink"
                               target="_blank"
                               class="btn btn-sm btn-outline-primary">
                                View Existing File
                            </a>
                            <small class="text-muted d-block">
                                (Uploading a new file will replace this)
                            </small>
                        </div>

                        <div class="row">
                            <div class="col-12 text-end">
                                <a href="{{ route('sm.purchaseorder.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-success">Create Purchase Order</button>
                            </div>
                        </div>
                        <!-- <input type="hidden" id="duplicatePoNumberFromServer" value="{{ session('po_duplicate') ?? '' }}"> -->
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



<!--  -->
{{-- ================= JS ================= --}}
<script>
document.getElementById('import_file')?.addEventListener('change', () => {
    document.getElementById('existingFileContainer').classList.add('d-none');
});

document.getElementById('poDuplicateReuse')?.addEventListener('click', () => {
    const po = document.getElementById('po_number').value;
    fetch(`/sm/purchaseorder/fetch-by-number/${po}`)
        .then(r => r.json())
        .then(res => {
            if (res.success && res.data?.import_file) {
                document.getElementById('existing_import_file').value = res.data.import_file;
                document.getElementById('existingFileLink').href = '/' + res.data.import_file;
                document.getElementById('existingFileContainer').classList.remove('d-none');
            }
        });
});

// function checkPoNumber() {
//     const po = document.getElementById('po_number').value;
//     if (!po) return;

//     fetch(`{{ route('sm.purchaseorder.check-po-number') }}?po_number=${po}`)
//         .then(r => r.json())
//         .then(d => {
//             if (d.exists) {
//                 document.getElementById('poDuplicateAlert').classList.remove('d-none');
//                 document.getElementById('poDuplicateMessage').innerText =
//                     `PO number ${po} already exists`;
//             }
//         });
// }
</script>





<script>
// --- Autofill PO fields when 'Use this PO' is clicked ---
document.addEventListener('DOMContentLoaded', function () {
        // Ensure disabled no_of_links is submitted
        const purchaseOrderForm = document.getElementById('purchaseOrderForm');
        if (purchaseOrderForm) {
            // purchaseOrderForm.addEventListener('submit', function () {
            //     const linksDropdown = document.getElementById('no_of_links_dropdown');
            //     if (linksDropdown && linksDropdown.disabled) {
            //         linksDropdown.disabled = false;
            //         setTimeout(() => { linksDropdown.disabled = true; }, 100);
            //     }
            // });
        }
    const reuseButton = document.getElementById('poDuplicateReuse');
    if (reuseButton) {
        reuseButton.addEventListener('click', function () {
    const poNumber = document.getElementById('po_number').value.trim();
    if (!poNumber) return;

    fetch(`/sm/purchaseorder/fetch-by-number/${encodeURIComponent(poNumber)}`)
        .then(r => r.json())
        .then(res => {
            const importFileInfo = document.getElementById('importFileInfo');

            if (res.success && res.data && res.data.import_file) {

    // 🔥 STORE old file path for backend reuse
    document.getElementById('existing_import_file').value = res.data.import_file;

    const fileUrl = `/${res.data.import_file}`;

    importFileInfo.innerHTML = `
        <div class="alert alert-info p-2">
            <strong>Previously uploaded document:</strong><br>
            <a href="${fileUrl}" target="_blank">
                ${res.data.import_file.split('/').pop()}
            </a>
            <p class="mb-0 small text-muted">
                This document will be reused if you don’t upload a new file.
            </p>
        </div>
    `;
} else {
                importFileInfo.innerHTML = '';
            }
        });
      });

    }
});
let poDuplicateAlert = null;
let poDuplicateMessage = null;
function checkPoNumber(forceCheck = false) {
    const poNumberInput = document.getElementById("po_number");
    const allowReuseInput = document.getElementById('allow_reuse');
    const poNumber = poNumberInput?.value.trim();
    if (!poNumber) {
        return Promise.resolve({ exists: false });
    }

    const allowReuse = allowReuseInput?.value === '1';
    if (allowReuse && !forceCheck) {
        return Promise.resolve({ exists: true, skipped: true });
    }

     return fetch("{{ route('sm.purchaseorder.check-po-number') }}?po_number=" + encodeURIComponent(poNumber))
        .then(response => response.json())
        .then(data => {
            if (data.exists && !allowReuse) {
                allowReuseInput.value = 0;
                showPoDuplicateModal(poNumber);
            }
            return data;
        })
        .catch(error => {
            console.error("Error checking PO number:", error);
            return { exists: false };
        });
}
function triggerPoCheck(force = false) {
    const poNumberInput = document.getElementById('po_number');
    if (!poNumberInput?.value.trim()) {
        hideDuplicateAlert();
        return;
    }
    checkPoNumber(force);
}
document.addEventListener('DOMContentLoaded', function () {
    const reuseButton = document.getElementById('poDuplicateReuse');
    const newButton = document.getElementById('poDuplicateNew');
    poDuplicateAlert = document.getElementById('poDuplicateAlert');
    poDuplicateMessage = document.getElementById('poDuplicateMessage');
    const poNumberInput = document.getElementById('po_number');
    const allowReuseInput = document.getElementById('allow_reuse');
    const purchaseOrderForm = document.getElementById('purchaseOrderForm');
    let skipSubmitCheck = false;

    if (reuseButton) {
        reuseButton.addEventListener('click', function () {
            allowReuseInput.value = 1;
            hideDuplicateAlert();
        });
    }

    if (newButton) {
        newButton.addEventListener('click', function () {
            allowReuseInput.value = 0;
            if (poNumberInput) {
                poNumberInput.value = '';
                poNumberInput.focus();
            }
            hideDuplicateAlert();
        });
    }

    if (poNumberInput) {
        poNumberInput.addEventListener('input', function () {
            if (allowReuseInput) {
                allowReuseInput.value = 0;
            }
            triggerPoCheck();
        });

        poNumberInput.addEventListener('blur', function () {
            triggerPoCheck(true);
        });

        poNumberInput.addEventListener('focusout', function () {
            triggerPoCheck(true);
        });

        poNumberInput.addEventListener('keydown', function (event) {
            if (event.key === 'Tab' || event.key === 'Enter') {
                triggerPoCheck(true);
            }
        });
    }

    const duplicatePoNumber = <?php echo json_encode(session('po_duplicate', null)); ?>;
    if (duplicatePoNumber) {
        if (poNumberInput) {
            poNumberInput.value = duplicatePoNumber;
            poNumberInput.focus();
        }
        showPoDuplicateModal(duplicatePoNumber);
    }

    const poDateInput = document.getElementById('po_date');
    if (poDateInput && poNumberInput) {
        poDateInput.addEventListener('focus', function () {
            if (poNumberInput.value.trim()) {
                checkPoNumber(true);
            }
        });
    }

    document.addEventListener('focusin', function (event) {
        if (!poNumberInput) {
            return;
        }

        if (event.target === poNumberInput) {
            poLastFocusedElement = poNumberInput;
            return;
        }

        if (poLastFocusedElement === poNumberInput) {
            triggerPoCheck(true);
        }

        poLastFocusedElement = event.target;
    });

    if (purchaseOrderForm) {
        purchaseOrderForm.addEventListener('submit', function (event) {
            if (skipSubmitCheck) {
                skipSubmitCheck = false;
                return;
            }

            if (allowReuseInput?.value === '1') {
                return;
            }

            event.preventDefault();
            checkPoNumber().then(data => {
                if (!data.exists) {
                    skipSubmitCheck = true;
                    purchaseOrderForm.submit();
                }
            });
        });
    }
});
function hideDuplicateAlert() {
    if (poDuplicateAlert) {
        poDuplicateAlert.classList.add('d-none');
    }
}
function showPoDuplicateModal(poNumber) {
    if (!poDuplicateAlert) {
        poDuplicateAlert = document.getElementById('poDuplicateAlert');
        poDuplicateMessage = document.getElementById('poDuplicateMessage');
    }
    if (!poDuplicateAlert || !poDuplicateMessage) return;
    poDuplicateMessage.textContent = `PO number ${poNumber} already exists. You can reuse it or create a new one.`;
    poDuplicateAlert.classList.remove('d-none');
}
let feasibilityAmounts = {};
let staticIpRequiredForFeasibility = false;

function showDynamicPricing() {


        const dropdown = document.getElementById('no_of_links_dropdown');
    const hidden = document.getElementById('no_of_links_hidden');

    hidden.value = dropdown.value; // 🔥 KEY LINE

    const links = parseInt(dropdown.value);
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

        <div class="row mb-3 dynamic-pricing-row">
            <div class="col-md-4 pricing-col">
                <label class="form-label">ARC - Link ${i} *</label>
                <div class="input-group">
                    <input type="number" step="0.01" min="0" id="arc_link_${i}" name="arc_link_${i}" class="form-control" onblur="validateEnteredAmount('arc_link_${i}', 'arc_per_link')" oninput="calculateTotal()">
                    <button type="button" class="btn btn-outline-info btn-sm" onclick="redirectToFeasibilityView()"><i class="bi bi-info-circle"></i></button>
                </div>
            </div>

            <div class="col-md-4 pricing-col">
                <label class="form-label">OTC - Link ${i} *</label>
                <div class="input-group">
                    <input type="number" step="0.01" min="0" id="otc_link_${i}" name="otc_link_${i}" class="form-control" onblur="validateEnteredAmount('otc_link_${i}', 'otc_per_link')" oninput="calculateTotal()">
                    <button type="button" class="btn btn-outline-info btn-sm" onclick="redirectToFeasibilityView()"><i class="bi bi-info-circle"></i></button>
                </div>
            </div>

            <div class="col-md-4 pricing-col static-ip-field static-ip-cost-column">
                <label class="form-label">Static IP - Link ${i} *</label>
                <div class="input-group">
                    <input type="number" step="0.01" min="0" id="static_ip_link_${i}" name="static_ip_link_${i}" class="form-control" onblur="validateEnteredAmount('static_ip_link_${i}', 'static_ip_cost_per_link')" oninput="calculateTotal()">
                    <button type="button" class="btn btn-outline-info btn-sm" onclick="redirectToFeasibilityView()"><i class="bi bi-info-circle"></i></button>
                </div>
            </div>
        </div>`;
    }

    document.getElementById('dynamicPricingContainer').style.display = 'block';
    updateStaticIpFieldVisibility();
}
function updateStaticIpFieldVisibility() {
    const staticIpRequiredInput = document.getElementById('static_ip_required');
    if (staticIpRequiredInput) {
        staticIpRequiredInput.value = staticIpRequiredForFeasibility ? '1' : '0';
    }
    document.querySelectorAll('.dynamic-pricing-row').forEach(row => {
        const staticIpContainer = row.querySelector('.static-ip-field');
        if (!staticIpContainer) return;
        const input = staticIpContainer.querySelector('input');
        if (staticIpRequiredForFeasibility) {
            row.classList.remove('hide-static-ip');
            staticIpContainer.style.display = '';
            if (input) {
                input.disabled = false;
                input.required = true;
            }
        } else {
            row.classList.add('hide-static-ip');
            staticIpContainer.style.display = 'none';
            if (input) {
                input.disabled = true;
                input.required = false;
                input.value = '';
            }
        }
    });
}
// function validateEnteredAmount(inputId, key) {
//     const v = parseFloat(document.getElementById(inputId).value) || 0;
//     const f = feasibilityAmounts[key] || 0;
//     const selfTypes = ['SELF', 'INF', 'UBN', 'UBS', 'UBL'];
//     const vendorType = (feasibilityAmounts.vendor_type || '').toUpperCase();
//     const isSelf = selfTypes.includes(vendorType);
//     const formattedFeasibility = f.toFixed(2);

//     if (isSelf) {
//         if (f <= 0) {
//             return; // nothing to validate if feasibility amount is missing
//         }

//         if (v > f + 0.001) {
//             alert(`Self vendor amount must be equal to or less than the feasibility value.`);
//             document.getElementById(inputId).value = '';
//         }

//         return;
//     }

//     const requiredMinimum = +(f * 1.20).toFixed(2);
//     if (f <= 0) {
//         return; // nothing to validate when there is no baseline
//     }

//     if (v < requiredMinimum - 0.001) {
//         alert(`Amount must be at least 20% higher than the feasibility value. Minimum allowed: ₹${requiredMinimum.toFixed(2)}.`);
//         document.getElementById(inputId).value = '';
//     }
// }
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

function loadFeasibilityDetails() {
    const id = document.getElementById('feasibility_id').value;

    if (!id) {
        feasibilityAmounts = {};
        staticIpRequiredForFeasibility = false;
        document.getElementById('dynamicPricingContainer').style.display = 'none';
        const addrBox = document.getElementById('feasibility_address_box');
        const addrField = document.getElementById('feasibility_address');
        if (addrBox && addrField) {
            addrField.value = '';
            addrBox.classList.add('d-none');
        }
        const linksDropdown = document.getElementById('no_of_links_dropdown');
        if (linksDropdown) linksDropdown.disabled = false;
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

            const staticIpFlag = (d.static_ip || '').toString().trim().toLowerCase();
            staticIpRequiredForFeasibility =
                ['yes', 'true', '1'].includes(staticIpFlag) ||
                (parseFloat(d.static_ip_cost_per_link) || 0) > 0;

            // Populate feasibility address preview
            const addrBox = document.getElementById('feasibility_address_box');
            const addrField = document.getElementById('feasibility_address');
            if (addrBox && addrField) {
                const f = d.feasibility || {};
                const parts = [];
                if (f.address) parts.push(f.address);
                if (f.area) parts.push(f.area);
                if (f.district) parts.push(f.district);
                if (f.state) parts.push(f.state);
                if (f.pincode) parts.push(f.pincode);
                const text = parts.filter(Boolean).join(', ');
                if (text) {
                    addrField.value = text;
                    addrBox.classList.remove('d-none');
                } else {
                    addrField.value = '';
                    addrBox.classList.add('d-none');
                }
            }

            // Populate feasibility speed preview
            const speedBox = document.getElementById('feasibility_speed_box');
            const speedField = document.getElementById('feasibility_speed');
            let speedValue = '';
            if (d.feasibility && d.feasibility.speed) {
                speedValue = d.feasibility.speed;
            } else if (d.speed) {
                speedValue = d.speed;
            }
            if (speedBox && speedField) {
                if (speedValue) {
                    speedField.value = speedValue;
                    speedBox.classList.remove('d-none');
                } else {
                    speedField.value = '';
                    speedBox.classList.add('d-none');
                }
            }

            // Auto-select No. of Links from feasibility and make it read-only
            const linksDropdown = document.getElementById('no_of_links_dropdown');
            if (d.no_of_links) {
                linksDropdown.value = d.no_of_links;
                document.getElementById('no_of_links_hidden').value = d.no_of_links; // hidden field to submit
                linksDropdown.disabled = true;
                linksDropdown.dispatchEvent(new Event('change'));
            } else {
                // fallback: keep previous logic and make editable
                linksDropdown.disabled = false;
                const links = linksDropdown.value;
                if (links) {
                    showDynamicPricing(); // redraw + static ip handled
                } else {
                    updateStaticIpFieldVisibility();
                }
            }
        })
        .catch(err => {
            console.error('Feasibility load error:', err);
        });
}

// 

$(document).ready(function () {

    $('.select2').select2({

        theme: 'bootstrap-5',

        tags: false, // ✅ allows typing new values

        placeholder: 'Select or Type',

        allowClear: true,

        width: '100%'

    });

});

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
@endsection