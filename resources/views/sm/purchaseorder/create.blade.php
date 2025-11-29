@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-dark">
                    <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Create Purchase Order</h4>
                </div>
                <div class="card-body position-relative">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Validation Error:</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
<div class="alert alert-warning p-3" id="warnBox">
    <h6 class="mb-2">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        Some issues found. Do you want to continue?
    </h6>

    <ul class="small mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>

    <div class="mt-2">
        <button type="button" class="btn btn-success btn-sm" id="continueYes">YES</button>
        <button type="button" class="btn btn-danger btn-sm" id="continueNo">NO</button>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById('purchaseOrderForm');
    const forceSubmit = document.getElementById('forceSubmit');
    const warnBox = document.getElementById('warnBox');
    const cardBody = warnBox?.closest('.card-body');

    const syncCardPadding = show => {
        if (!cardBody) return;
        cardBody.classList.toggle('alert-visible', !!show);
    };

    if (warnBox) {
        syncCardPadding(true);

        // Block submit when warnings exist
        form.addEventListener('submit', function (e) {
            if (forceSubmit.value == "0") {
                e.preventDefault();   // stop refresh
            }
        });

        // YES → allow submit next time (but do NOT submit immediately)
        document.getElementById('continueYes').addEventListener('click', function () {
            forceSubmit.value = "1";       // allow next submit
            warnBox.style.display = "none";
            syncCardPadding(false);
            form.requestSubmit();          // correct way - prevents page flush
        });

        // NO → just close box, do nothing
        document.getElementById('continueNo').addEventListener('click', function () {
            warnBox.style.display = "none";
            syncCardPadding(false);
            forceSubmit.value = "0";
        });
    }
});
</script>

@endif

                    <form action="{{ route('sm.purchaseorder.store') }}" method="POST" id="purchaseOrderForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="feasibility_id" class="form-label"><strong>Feasibility Request ID <span class="text-danger">*</span></strong></label>
                                <select class="form-select" id="feasibility_id" name="feasibility_id" required onchange="loadFeasibilityDetails()">
                                    <option value="">Select Available Feasibility</option>
                                    @foreach($closedFeasibilities as $feas)
                                        <option value="{{ $feas->feasibility->id }}"
                                                data-status-id="{{ $feas->id }}">
                                            {{ $feas->feasibility->feasibility_request_id }}
                                        </option>
                                    @endforeach
                                </select>
                               
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>PO Number *</strong></label>
                                <input type="text" class="form-control" id="po_number" name="po_number" onblur="checkPoNumber()" required>
                                <input type="hidden" id="allow_reuse" name="allow_reuse" value="0">
                                <input type="hidden" id="forceSubmit" name="forceSubmit" value="0">
                                <div class="alert alert-warning alert-dismissible fade show mt-2 d-none" id="poTakenAlert" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                    PO number already taken. Do you want to use it?
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-success me-1" id="reuseYes">YES</button>
                                        <button type="button" class="btn btn-sm btn-danger" id="reuseNo">NO</button>
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>PO Date *</strong></label>
                                <input type="date" class="form-control" id="po_date" name="po_date" required value="{{ date('Y-m-d') }}">
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
                                <a href="{{ route('sm.purchaseorder.index') }}" class="btn btn-secondary me-2">Cancel</a>
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
    const isSelf = feasibilityAmounts.vendor_type === 'SELF' || feasibilityAmounts.vendor_type === 'INF' || feasibilityAmounts.vendor_type === 'UBN';

    if (isSelf && v > f) {
        alert('Self vendor cannot exceed feasibility amount');
        document.getElementById(inputId).value = '';
        return;
    }

    if (!isSelf && v <= f) {
        alert('External vendor must be higher than feasibility amount');
        document.getElementById(inputId).value = '';
        return;
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
@endsection