@extends('layouts.app')



@section('content')

<div class="container-fluid py-4">

    <h4 class="fw-bold text-primary mb-4">Edit Feasibility Status</h4>



    <div class="card shadow border-0 p-4">

        {{-- Feasibility Details Header --}}

        <div class="row mb-4">

            {{-- Feasibility Request ID --}}

                    <div class="col-md-6">

                <h6 class="fw-semibold text-muted">Feasibility Request ID:</h6>

                <p><span class="badge bg-info fs-6">{{ $record->feasibility->feasibility_request_id ?? 'N/A' }}</span></p>

            </div>



            {{-- Client Name --}}

            <div class="col-md-6">

                <h6 class="fw-semibold text-muted">Client:</h6>

                <p>{{ $record->feasibility->client->client_name ?? 'N/A' }}</p>

            </div>



            {{-- Type of Service --}}

            <div class="col-md-6">

                <h6 class="fw-semibold text-muted">Feasibility Type:</h6>

                <p>{{ $record->feasibility->type_of_service ?? 'N/A' }}</p>

            </div>



            {{-- No of links --}}

            <div class="col-md-6">

                <h6 class="fw-semibold text-muted">No. of Links:</h6>

                <p>{{ $record->feasibility->no_of_links ?? 'N/A' }}</p>

            </div>

            

            {{-- Current Status --}}

            <div class="col-md-6">

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

                    <div class="col-md-3">

                        <label class="form-label fw-semibold">Name 

                            @if($i <= $noOfLinks)

                                <span class="text-danger">*</span>

                            @endif

                        </label>



                        {{-- Vendor dropdown with duplicate validation --}}

                        <select name="vendor{{ $i }}_name" 

                                class="form-select vendor-dropdown" 

                                data-vendor-number="{{ $i }}"

                                @if($i <= $noOfLinks) required @endif>

                            <option value="">Select Vendor</option>

                             {{-- Populate vendor list --}}

                            @foreach($vendors as $vendor)

                                <option value="{{ $vendor->vendor_name }}" 

                                        @if($record->{'vendor' . $i . '_name'} == $vendor->vendor_name) selected @endif>

                                    {{ $vendor->vendor_name }}

                                </option>

                            @endforeach

                        </select>

                        <div class="invalid-feedback">

                            This vendor is already selected in another section.

                        </div>

                    </div>

                    {{-- ARC --}}

                    <div class="col-md-2">

                        <label class="form-label fw-semibold">ARC</label>

                        <input type="number" name="vendor{{ $i }}_arc" class="form-control" value="{{ $record->{'vendor' . $i . '_arc'} }}">

                    </div>

                     {{-- OTC --}}

                    <div class="col-md-2">

                        <label class="form-label fw-semibold">OTC</label>

                        <input type="number" name="vendor{{ $i }}_otc" class="form-control" value="{{ $record->{'vendor' . $i . '_otc'} }}">

                    </div>

                    {{-- Static IP Cost --}}

                    <div class="col-md-2 static-ip-cost-column">
    <label class="form-label fw-semibold">Static IP Cost</label>
    <input type="number" name="vendor{{ $i }}_static_ip_cost"
           class="form-control"
           value="{{ $record->{'vendor' . $i . '_static_ip_cost'} }}">
</div>


                    {{-- Delivery Timeline --}}

                    <div class="col-md-3">

                        <label class="form-label fw-semibold">Delivery Timeline</label>

                        <input type="text" name="vendor{{ $i }}_delivery_timeline" class="form-control" value="{{ $record->{'vendor' . $i . '_delivery_timeline'} }}">

                    </div>

                </div>

            @endfor



            <hr>



            {{-- Action Buttons --}}

            <div class="mt-4">

                <div class="row">

                   

                    <div class="col-md-8">

                        {{-- Save → Move to InProgress --}}

                        <button type="button" class="btn btn-warning me-2" onclick="saveToInProgress()">

                            <i class="bi bi-save"></i> Save (Move to In Progress)

                        </button>



                        {{-- Submit → Move to Closed --}}

                        <button type="button" class="btn btn-success me-2" onclick="submitToClosed()">

                            <i class="bi bi-check-circle"></i> Submit (Move to Closed)

                        </button>



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
    function setNormalVendors() {
        vendorDropdowns.forEach(dd => {
            dd.disabled = false;

            let vendorOptions = `
                <option value="">Select Vendor</option>
                @foreach($vendors as $v)
                    <option value="{{ $v->vendor_name }}">{{ $v->vendor_name }}</option>
                @endforeach
            `;

            dd.innerHTML = vendorOptions;

            // restore previous selected value if exists
            let oldValue = dd.getAttribute("data-old") ?? "";
            if (oldValue) {
                dd.value = oldValue;
            }
        });
    }

    // Apply vendor type logic
    if (ownCompanies.includes(vendorType)) {
        setSelfVendor();
    } else {
        setNormalVendors();
    }



    // ---------------------------------------------
    // Duplicate Vendor Validation + Required Fields
    // ---------------------------------------------

    function validateVendorNames() {
        const dropdowns = document.querySelectorAll('.vendor-dropdown');
        const names = [];
        let isValid = true;

        const noOfLinks = parseInt('{{ $noOfLinks }}');

        dropdowns.forEach((dd, index) => {

            // IGNORE validation for SELF mode (duplicates allowed)
            if (ownCompanies.includes(vendorType)) {
                dd.classList.remove('is-invalid');
                return;
            }

            dd.classList.remove('is-invalid');
            const vendorNumber = index + 1;
            const name = dd.value.trim().toLowerCase();

            // Required vendor
            if (vendorNumber <= noOfLinks && !name) {
                dd.classList.add('is-invalid');
                isValid = false;
                return;
            }

            // Duplicate check
            if (name && names.includes(name)) {
                dd.classList.add('is-invalid');
                isValid = false;
            } else if (name) {
                names.push(name);
            }
        });

        return isValid;
    }


    // ----------------------------------------------------
    // Hide Used Vendors From Other Dropdowns (Normal Only)
    // ----------------------------------------------------
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


    // -----------------------------
    // Save → InProgress
    // -----------------------------
    window.saveToInProgress = function () {
        if (!validateVendorNames()) {
            alert('Please fill all required vendor names and ensure they are different.');
            return false;
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
            alert('Please fill all required vendor names and ensure they are different.');
            return false;
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
    const staticIpEnabled = "{{ strtolower($record->feasibility->static_ip ?? 'No') }}" === 'yes';

    function applyStaticIPRule() {
        for (let i = 1; i <= 4; i++) {
            const field = document.querySelector(`input[name="vendor${i}_static_ip_cost"]`);
            if (!field) continue;

            field.readOnly = false;
            field.required = feasibilityType === "ILL" ? false : true;
            field.placeholder = feasibilityType === "ILL" ? "Optional for ILL" : "Required";
        }
    }

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
});


</script>

@endsection