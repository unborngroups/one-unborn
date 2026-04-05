@extends('layouts.app')



@section('content')

<div class="container-fluid py-4">

    <h4 class="fw-bold text-primary mb-4">Edit Feasibility Status</h4>



    <div class="card shadow border-0 p-4">

        {{-- Feasibility Details Header --}}

        <div class="row mb-4">

            {{-- Feasibility Request ID --}}

                    <div class="col-md-4">

                <h6 class="fw-semibold text-muted">Feasibility Request ID:</h6>

                <p><span class="badge bg-info fs-6">{{ $record->feasibility->feasibility_request_id ?? 'N/A' }}</span></p>

            </div>



            {{-- Client Name --}}

            <div class="col-md-4">

                <h6 class="fw-semibold text-muted">Client:</h6>

                <p>{{ $record->feasibility->client->client_name ?? 'N/A' }} - {{ $record->feasibility->client_state ?? 'N/A' }}</p>

            </div>

            {{-- Company Name --}}

            <div class="col-md-4">

                <h6 class="fw-semibold text-muted">Company:</h6>
                <p>{{ $record->feasibility->company->company_name ?? 'N/A' }}</p>

            </div>



            {{-- Type of Service --}}

            <div class="col-md-4">

                <h6 class="fw-semibold text-muted">Feasibility Type:</h6>

                <p>{{ $record->feasibility->type_of_service ?? 'N/A' }}</p>

            </div>



            {{-- No of links --}}

            <div class="col-md-4">

                <h6 class="fw-semibold text-muted">No. of Links:</h6>

                <p>{{ $record->feasibility->no_of_links ?? 'N/A' }}</p>

            </div>

            {{-- Client Name --}}

            <div class="col-md-4">

                <h6 class="fw-semibold text-muted">Address:</h6>

                <p>{{ $record->feasibility->address ?? 'N/A' }}</p>

            </div>

            {{-- Link Type --}}


            <div class="col-md-4">
                <h6 class="fw-semibold text-muted">Link Type:</h6>
                <p>{{ $record->feasibility->link_type ?? 'N/A' }}</p>
                @if(($record->feasibility->link_type ?? '') === 'existing' && isset($deliverablePlan) && $deliverablePlan)
                    <span class="text-muted small">Vendor: {{ $deliverablePlan->vendor_name ?? 'N/A' }}</span><br>
                    <span class="text-muted small">ARC: {{ $deliverablePlan->plans_name ?? 'N/A' }}</span>
                @endif
            </div>

            <div class="col-md-4">
                <h6 class="fw-semibold text-muted">Circuit ID:</h6>
                <p>{{ $record->feasibility->circuit_id ?? 'N/A' }}</p>
            </div>

            {{-- Current Status --}}

            <div class="col-md-4">
                <h6 class="fw-semibold text-muted">Current Status:</h6>

                <p>

                    <span class="badge 

                        @if($record->status == 'Open') bg-primary

                        @elseif($record->status == 'InProgress') bg-warning text-dark

                        @elseif($record->status == 'Closed') bg-success

                        @endif">

                        {{ $record->status }}

                    </span>

                </p>

            </div>

        </div>



        <hr>

@php
    $linkType = $record->feasibility->link_type;
    $noOfLinks = $record->feasibility->no_of_links ?? 1;
    $maxVendors = 4;
@endphp


@php
    $previousVendorCount = 0;

    if($previous){
        for ($j = 1; $j <= 4; $j++) {
            if (!empty($previous->{'vendor'.$j.'_name'})) {
                $previousVendorCount++;
            }
        }
    }
@endphp
        {{-- ✅ Main form - no action applied, JS sets action dynamically --}}

        <form id="feasibilityForm" method="POST">

            @csrf
            <input type="hidden" name="feasibility_id" value="{{ $record->feasibility_id }}">
            <input type="hidden" name="connection_type" value="{{ $record->feasibility->type_of_service }}">



            @php

            // Number of links determines how many vendor sections are mandatory

                $noOfLinks = $record->feasibility->no_of_links ?? 1;

                // Always render 4 vendor sections

                $maxVendors = 4; // Always show all 4 vendor sections

            @endphp



            {{-- ✅ Vendor Sections Loop --}}

            @for($i = 1; $i <= $maxVendors; $i++)

            <!-- existing vendor section -->
             @php
            // ✅ Editable logic
            if ($linkType === 'new') {
    $isEditable = true;
} else {
    if ($i < $noOfLinks) {
        $isEditable = false; // already existing → readonly
    } else {
        $isEditable = true; // new/increase → editable
    }
}
        @endphp
        
            <!-- end existing vendor section -->

                <h5 class="fw-bold text-primary mb-3">

                    Vendor {{ $i }}

                    {{-- Required vendor tags --}}

                    @if($i <= $noOfLinks)

                        @if($noOfLinks == 1)

                            <small class="text-success">(Required - Default Vendor)</small>

                        @else

                            <small class="text-success">(Required - Link {{ $i }})</small>

                        @endif

                    @else

                        <small class="text-muted">(Optional - Additional Vendor)</small>

                    @endif

                </h5>

                
                {{-- ✅ Vendor Input Row --}}

                <div class="row g-3 mb-4" id="vendor{{ $i }}_section">

                    {{-- Vendor Name Dropdown --}}

                    <div class="col-md-2">

                        <label class="form-label fw-semibold">Name 

                            @if($i <= $noOfLinks)

                                <span class="text-danger">*</span>

                            @endif

                        </label>


                        {{-- Vendor dropdown with duplicate validation --}}

                        <select name="vendor{{ $i }}_name" 

                                class="form-select vendor-dropdown {{ !$isEditable ? 'readonly-field' : '' }}"

                                data-vendor-number="{{ $i }}"

                                @if($i <= $noOfLinks) required @endif
                                 @if(!$isEditable)  style="pointer-events:none; background:#f1f3f5;" @endif>  {{-- if not editable disable --}}

                            <option value="">Select Vendor</option>

                             {{-- Populate vendor list --}}

                            @foreach($vendors as $vendor)
                                
@php
    $value = !empty($previous->{'vendor'.$i.'_name'})
        ? ($previous->{'vendor'.$i.'_name'} ?? '')
        : ($record->{'vendor'.$i.'_name'} ?? '');
    // If still empty and existing link, use deliverable
    if (empty($value) && $i == 1 && isset($deliverable) && $deliverable) {
        $value = $deliverable->feasibilityStatus->vendor1_name ?? '';
    }
@endphp
<!-- <option value="{{ $vendor->vendor_name }}"
    {{ $value == $vendor->vendor_name ? 'selected' : '' }}>
    {{ $vendor->vendor_name }}
</option> -->

<option value="{{ $vendor->vendor_name }}"
    {{ strtolower(trim($value)) == strtolower(trim($vendor->vendor_name)) ? 'selected' : '' }}>
    {{ $vendor->vendor_name }}
</option>

                            @endforeach

                        </select>

                        <div class="invalid-feedback">

                            This vendor is already selected in another section.

                        </div>

                    </div>

                    {{-- ARC --}}
                    @php
    $arc = !empty($previous->{'vendor'.$i.'_arc'})
        ? ($previous->{'vendor'.$i.'_arc'} ?? '') 
        : ($record->{'vendor' . $i . '_arc'} ?? '');

    // If still empty and existing link, use deliverable
    if (empty($arc) && $i == 1 && isset($deliverable) && $deliverable) {
        $arc = $deliverable->feasibilityStatus->vendor1_arc ?? '';
    }
@endphp

                    <div class="col-md-2">

                        <label class="form-label fw-semibold">ARC</label>

                        <input type="number" name="vendor{{ $i }}_arc" class="form-control {{ !$isEditable ? 'readonly-field' : '' }}" value="{{ $arc }}" @if(!$isEditable) readonly @endif>

                    </div>

                     {{-- OTC --}}
                        @php
    $otc = !empty($previous->{'vendor'.$i.'_otc'})
        ? ($previous->{'vendor'.$i.'_otc'} ?? '') 
        : ($record->{'vendor' . $i . '_otc'} ?? '');
    // If still empty and existing link, use deliverable
    if (empty($otc) && $i == 1 && isset($deliverable) && $deliverable) {
        $otc = $deliverable->feasibilityStatus->vendor1_otc ?? '';
    }
@endphp

                    <div class="col-md-2">

                        <label class="form-label fw-semibold">OTC</label>

                        <input type="number" name="vendor{{ $i }}_otc" class="form-control {{ !$isEditable ? 'readonly-field' : '' }}" value="{{ $otc }}" @if(!$isEditable) readonly @endif>

                    </div>

                    {{-- Static IP Cost --}}

                     @php
    $staticIpCost = !empty($previous->{'vendor'.$i.'_static_ip_cost'})
        ? ($previous->{'vendor'.$i.'_static_ip_cost'} ?? '') 
        : ($record->{'vendor' . $i . '_static_ip_cost'} ?? '');
    // If still empty and existing link, use deliverable
    if (empty($staticIpCost) && $i == 1 && isset($deliverable) && $deliverable) {
        $staticIpCost = $deliverable->feasibilityStatus->vendor1_static_ip_cost ?? '';
    }

@endphp
                    <div class="col-md-2 static-ip-cost-column">
    <label class="form-label fw-semibold">Static IP Cost</label>
    <input type="number" name="vendor{{ $i }}_static_ip_cost"
           class="form-control {{ !$isEditable ? 'readonly-field' : '' }}"
           value="{{ $staticIpCost }}" @if(!$isEditable) readonly @endif>
</div>


                    {{-- Delivery Timeline --}}
                     @php
$deliveryTimeline = !empty($previous->{'vendor'.$i.'_delivery_timeline'})
    ? $previous->{'vendor'.$i.'_delivery_timeline'}
    : ($record->{'vendor'.$i.'_delivery_timeline'} ?? '');
    // If still empty and existing link, use deliverable
    if (empty($deliveryTimeline) && $i == 1 && isset($deliverable) && $deliverable) {
        $deliveryTimeline = $deliverable->feasibilityStatus->vendor1_delivery_timeline ?? '';
    }
@endphp

                    <div class="col-md-2">

                        <label class="form-label fw-semibold">Delivery Timeline</label>

                        <input type="text" name="vendor{{ $i }}_delivery_timeline" class="form-control {{ !$isEditable ? 'readonly-field' : '' }}" value="{{ $deliveryTimeline }}" @if(!$isEditable) readonly @endif>

                    </div>
                    {{-- Remarks --}}
                     @php
$remarks = !empty($previous->{'vendor'.$i.'_remarks'})
    ? $previous->{'vendor'.$i.'_remarks'}
    : ($record->{'vendor'.$i.'_remarks'} ?? '');
    // If still empty and existing link, use deliverable
    if (empty($remarks) && $i == 1 && isset($deliverable) && $deliverable) {
        $remarks = $deliverable->feasibilityStatus->vendor1_remarks ?? '';
    }
@endphp

                    <div class="col-md-2">

                        <label class="form-label fw-semibold">Remarks</label>
                        <input type="text" name="vendor{{ $i }}_remarks" class="form-control {{ !$isEditable ? 'readonly-field' : '' }}" value="{{ $remarks }}" @if(!$isEditable) readonly @endif>

                    </div>

                </div>

            @endfor



            <hr>



            {{-- Action Buttons --}}

            @php
                $settings = \App\Models\CompanySetting::first();
                $exceptionEmail = $settings->exception_permission_email ?? null;
                $user = Auth::user();
                $userEmail = $user ? ($user->official_email ?: $user->email) : null;
                $isExceptionUser = $exceptionEmail && $userEmail && strcasecmp($exceptionEmail, $userEmail) === 0;

                $vendorNamesForPermission = [];
                for ($i = 1; $i <= 4; $i++) {
                    $name = trim($record->{'vendor'.$i.'_name'} ?? '');
                    if ($name !== '') {
                        $vendorNamesForPermission[] = strtolower($name);
                    }
                }
                // Exception permission is only needed when the SAME vendor is used
                // for 2 or more links, not when there is only a single vendor.
                $allSameVendorsForPermission = count($vendorNamesForPermission) > 1 && count(array_unique($vendorNamesForPermission)) === 1;
            @endphp

            <div class="mt-4">

                <div class="row">

                    <div class="col-md-8">

                        {{-- Save → Move to InProgress --}}

                        <button type="button" class="btn btn-warning me-2" onclick="saveToInProgress()">

                            <i class="bi bi-save"></i> Save (Move to In Progress)

                        </button>



                        {{-- Send Exception (Email only, do not change status) --}}

                        <button type="button" class="btn btn-primary me-2" onclick="sendExceptionEmail()">

                            <i class="bi bi-send"></i> Send Exception

                        </button>



                        {{-- Submit → Move to Closed --}}

                        @if($record->status === 'InProgress' && $allSameVendorsForPermission && ! $isExceptionUser)
                            <button type="button" class="btn btn-success me-2" disabled
                                title="Only the Exception Permission Email user can close this feasibility.">
                                <i class="bi bi-check-circle"></i> Submit (Move to Closed)
                            </button>
                        @else
                            <button type="button" class="btn btn-success me-2" onclick="submitToClosed()">
                                <i class="bi bi-check-circle"></i> Submit (Move to Closed)
                            </button>
                        @endif



                        {{-- Cancel Route Based on Status --}}

                        @if($record->status == 'Open')

                            <a href="{{ route('operations.feasibility.open') }}" class="btn btn-secondary">Cancel</a>

                        @elseif($record->status == 'InProgress')

                            <a href="{{ route('operations.feasibility.inprogress') }}" class="btn btn-secondary">Cancel</a>

                        @else

                            <a href="{{ route('operations.feasibility.closed') }}" class="btn btn-secondary">Cancel</a>

                        @endif

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>



@endsection



@section('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    // Current feasibility status (Open / InProgress / Closed)
    const currentStatus = "{{ $record->status }}";

    // -----------------------------
    // Vendor Type Logic
    // -----------------------------
    const vendorType = "{{ $record->feasibility->vendor_type }}";
    const ownCompanies = ["UBN", "UBS", "UBL", "INF"];   // SELF vendors
    const normalVendors = ["same vendor", "different vendor"];

    const vendorDropdowns = document.querySelectorAll('.vendor-dropdown');

    // Store old values before dropdown gets overwritten
    vendorDropdowns.forEach(dd => {
        dd.setAttribute("data-old", dd.value);
    });

    // Load SELF vendor option
    function setSelfVendor() {
        vendorDropdowns.forEach(dd => {
            dd.innerHTML = "";
            let opt = document.createElement("option");
            opt.value = "Self";
            opt.textContent = "Self";
            dd.appendChild(opt);

            dd.value = "Self";
            dd.disabled = true;
        });
    }

    // Load normal vendor list
    // function setNormalVendors() {
    //     vendorDropdowns.forEach(dd => {

    //      // 🚫 Skip readonly fields (important fix)
    //     if (dd.classList.contains('readonly-field')) return;

    //         dd.disabled = false;

    //         let vendorOptions = `
    //             <option value="">Select Vendor</option>
    //             @foreach($vendors as $v)
    //                 <option value="{{ $v->vendor_name }}">{{ $v->vendor_name }}</option>
    //             @endforeach
    //         `;

    //         dd.innerHTML = vendorOptions;

    //         // restore previous selected value if exists
    //         let oldValue = dd.getAttribute("data-old") ?? "";
    //         if (oldValue) {
    //             dd.value = oldValue;
    //         }
    //     });
    // }

    // Apply vendor type logic
    if (ownCompanies.includes(vendorType)) {
        setSelfVendor();
    }
    //  else {
    //     setNormalVendors();
    // }
//     else {
//     setExistingVendor();
// }

// function setExistingVendor() {
//     vendorDropdowns.forEach(dd => {
//         dd.disabled = false;

//         // Check if the feasibility_status already exists for this feasibility
//         fetch(`/sm/feasibility/status/${feasibilityId}`)
//             .then(response => response.json())
//             .then(data => {
//                 const existingStatus = data.statuses.find(status => status.feasibility_id === feasibilityId);

//                 if (existingStatus && existingStatus.vendor1_name) {
//                     dd.value = existingStatus.vendor1_name;
//                     dd.setAttribute("data-old", existingStatus.vendor1_name);
//                 }

//                 if (existingStatus && existingStatus.circuit_id) {
//                     document.getElementById('circuit_id').value = existingStatus.circuit_id;
//                 }

//                 let vendorOptions = `
//                     <option value="">Select Vendor</option>
//                     @foreach($vendors as $v)
//                         <option value="{{ $v->vendor_name }}">{{ $v->vendor_name }}</option>
//                     @endforeach
//                 `;

//                 dd.innerHTML = vendorOptions;

//                 // Display the existing value if it exists
//                 if (dd.getAttribute("data-old")) {
//                     dd.value = dd.getAttribute("data-old");
//                 }
//             })
//             .catch(error => {
//                 console.error(error);
//             });
//     });
// }

    // ---------------------------------------------
    // Duplicate Vendor Validation + Required Fields
    // ---------------------------------------------

    function validateVendorNames() {
        const dropdowns = document.querySelectorAll('.vendor-dropdown');
        let isValid = true;

        const noOfLinks = parseInt('{{ $noOfLinks }}');

        dropdowns.forEach((dd, index) => {

            // IGNORE validation for SELF mode
            if (ownCompanies.includes(vendorType)) {
                dd.classList.remove('is-invalid');
                return;
            }

            dd.classList.remove('is-invalid');
            const vendorNumber = index + 1;
            const name = dd.value.trim().toLowerCase();

            // Required vendor (only check empty)
            if (vendorNumber <= noOfLinks && !name) {
                dd.classList.add('is-invalid');
                isValid = false;
                return;
            }
        });

        return isValid;
    }


    // ----------------------------------------------------
    // Hide Used Vendors From Other Dropdowns (Normal Only)
    // ----------------------------------------------------
    // NOTE: Disabled as per latest requirement; keeping logic commented
    // for potential future reuse.
    /*
    function updateVendorDropdowns() {

        if (ownCompanies.includes(vendorType)) return; // no need in SELF mode

        const dropdowns = document.querySelectorAll('.vendor-dropdown');

        const selectedValues = [];

        dropdowns.forEach(dd => {
            if (dd.disabled) return;
            if (dd.value) selectedValues.push(dd.value);
        });

        dropdowns.forEach(currentDD => {
            if (currentDD.disabled) return;

            const currentValue = currentDD.value;
            const options = currentDD.querySelectorAll('option');

            options.forEach(option => {
                if (option.value === '') {
                    option.style.display = '';
                } else if (selectedValues.includes(option.value) && option.value !== currentValue) {
                    option.style.display = 'none';
                } else {
                    option.style.display = '';
                }
            });
        });
    }


    // Add event listener
    vendorDropdowns.forEach(dd => {
        dd.addEventListener('change', function () {
            updateVendorDropdowns();
            validateVendorNames();
        });
    });

    updateVendorDropdowns();
    */


    // -----------------------------
    // Save → InProgress
    // -----------------------------
    window.saveToInProgress = function () {
        if (!validateVendorNames()) {
            alert('Please fill all required vendor names.');
            return false;
        }

        // When record is still Open and all vendors are same, force Exception
        if (currentStatus === 'Open') {
            const dropdowns = document.querySelectorAll('.vendor-dropdown');
            const selectedNames = [];

            dropdowns.forEach(function (dd) {
                const val = dd.value.trim();
                if (val !== '') {
                    selectedNames.push(val.toLowerCase());
                }
            });

            // Only enforce the "same vendor" rule when there are at least 2 vendors selected
            if (selectedNames.length > 1) {
                const first = selectedNames[0];
                const allSame = selectedNames.every(n => n === first);

                if (allSame) {
                    alert('Same vendor name selected for all links. Please use the "Send Exception" button to move this feasibility to In Progress.');
                    return false;
                }
            }
        }

        if (confirm('Are you sure you want to save this feasibility? This will move it to Inprogress status.')) {
        const form = document.getElementById('feasibilityForm');
        form.action = "{{ route('operations.feasibility.save', $record->id) }}";
        form.submit();
        }
    };

    // -----------------------------
    // Submit → Closed
    // -----------------------------
    window.submitToClosed = function () {

        if (!validateVendorNames()) {
            alert('Please fill all required vendor names.');
            return false;
        }

        // Bypass same vendor popup for SELF vendors
        if (ownCompanies.includes(vendorType)) {
            // Allow direct submit for SELF vendors
        } else {
            // When record is still Open and all vendors are same, force Exception instead of direct submit
            if (currentStatus === 'Open') {
                const dropdowns = document.querySelectorAll('.vendor-dropdown');
                const selectedNamesForSubmit = [];

                dropdowns.forEach(function (dd) {
                    const val = dd.value.trim();
                    if (val !== '') {
                        selectedNamesForSubmit.push(val.toLowerCase());
                    }
                });

                // Only enforce the "same vendor" rule when there are at least 2 vendors selected
                if (selectedNamesForSubmit.length > 1) {
                    const firstSubmit = selectedNamesForSubmit[0];
                    const allSameSubmit = selectedNamesForSubmit.every(n => n === firstSubmit);

                    if (allSameSubmit) {
                        alert('Same vendor name selected for all links. Please use the "Send Exception" button first before submitting this feasibility to Closed.');
                        return false;
                    }
                }
            }
        }

        if (confirm('Are you sure you want to submit this feasibility? This will move it to Closed status.')) {
            const form = document.getElementById('feasibilityForm');
            form.action = "{{ route('operations.feasibility.submit', $record->id) }}";
            form.submit();
        }
    };

    // ********************************************
    // Static IP Cost Rule (ILL → Optional, Others → Required)
    // ********************************************
    const feasibilityType = "{{ $record->feasibility->type_of_service }}";
    const staticIpValue = "{{ strtolower(trim($record->feasibility->static_ip ?? 'no')) }}";
    console.log("static_ip value:", staticIpValue);
    const staticIpEnabled = ["yes", "y", "1", "true"].includes(staticIpValue);

    // this is static IP cost field requirement based on feasibility type
    function applyStaticIPRule() {
        for (let i = 1; i <= 4; i++) {
            const field = document.querySelector(`input[name="vendor${i}_static_ip_cost"]`);
            if (!field) continue;

            field.readOnly = false;
            field.required = feasibilityType === "ILL" ? false : true;
            field.placeholder = feasibilityType === "ILL" ? "Optional for ILL" : "Required";
        }
    }

    // this is static IP cost column visibility based on static IP enabled or not
    function updateStaticIpCostVisibility() {
        document.querySelectorAll('.static-ip-cost-column').forEach(column => {
            const input = column.querySelector('input');
            if (!input) return;

            if (staticIpEnabled) {
                column.style.display = '';
                input.disabled = false;
            } else {
                column.style.display = 'none';
                input.disabled = true;
                input.required = false;
                input.value = '';
            }
        });
    }

    applyStaticIPRule();
    updateStaticIpCostVisibility();


    // -----------------------------
    // Send Exception Email (Operations)
    // -----------------------------
    window.sendExceptionEmail = function () {
        if (!validateVendorNames()) {
            alert('Please fill all required vendor names before sending exception.');
            return false;
        }

        // At least one vendor must be selected; all selected names must be same
        const dropdowns = document.querySelectorAll('.vendor-dropdown');
        const selectedNames = [];

        dropdowns.forEach(function (dd) {
            const val = dd.value.trim();
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
            form.action = "{{ route('operations.feasibility.exception', $record->id) }}";
            form.submit();
        }
    };
});


</script>

<style>
    .readonly-field{
background:#f1f3f5;
}
</style>
@endsection