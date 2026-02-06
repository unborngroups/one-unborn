@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h4 class="text-primary fw-bold mb-3">Edit Feasibility</h4>

    <div class="card shadow border-0 p-4">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))

            <div class="alert alert-success">
                {{ session('success') }}
            </div>

        @endif
        
        @if (session('import_errors'))

            <div class="alert alert-warning">
                <strong>Import could not process some rows:</strong>
                <ul class="mb-0">
                    @foreach (session('import_errors') as $importError)
                        <li>{{ $importError }}</li>
                    @endforeach
                </ul>
            </div>

        @endif

        @php
            $importRow = session('imported_row', []);
        @endphp

       
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
                            <form action="{{ route('feasibility.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="input-group">
                                    <input type="file" name="file" class="form-control" required>
                                    <a href="{{ asset('images/feasibilityimport/feasibility_import_69255ee5caa5f.xlsx') }}" target="_blank" class="btn btn-outline-secondary" title="Download import template">Download Format</a>
                                    <button type="submit" class="btn btn-primary">Import Excel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('feasibility.update', $feasibility->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Feasibility Request ID</label>
                    <input type="text" class="form-control bg-light" value="{{ $feasibility->feasibility_request_id }}" readonly>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Type of Service <span class="text-danger">*</span></label>
                    @php $typeSelection = old('type_of_service', $feasibility->type_of_service); @endphp
                    <select name="type_of_service" id="type_of_service" class="form-select" required>
                        <option value="" {{ $typeSelection === '' ? 'selected' : '' }}>Select</option>
                        <option value="Broadband" {{ $typeSelection === 'Broadband' ? 'selected' : '' }}>Broadband</option>
                        <option value="ILL" {{ $typeSelection === 'ILL' ? 'selected' : '' }}>ILL</option>
                        <option value="P2P" {{ $typeSelection === 'P2P' ? 'selected' : '' }}>P2P</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Company <span class="text-danger">*</span></label>
                    <select name="company_id" id="company_id" class="form-select" required>
                        <option value="">Select Company</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ (string) old('company_id', $feasibility->company_id) === (string) $company->id ? 'selected' : '' }}>
                                {{ $company->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Client Name <span class="text-danger">*</span></label>
                    <select name="client_id" id="client_id" class="form-select" required>
                        <option value="">Select Client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ (string) old('client_id', $feasibility->client_id) === (string) $client->id ? 'selected' : '' }}>
                                {{ $client->business_name ?: $client->client_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Delivery Company Name</label>
                    <input type="text" name="delivery_company_name" class="form-control" value="{{ old('delivery_company_name', $feasibility->delivery_company_name) }}">
                </div>

                 <!-- Story ID -->
                <div class="col-md-3">
                    <label for="form-label fw-semibold">Location ID</label>
                    <input type="text" name="location_id" class="form-control" value="{{ old('location_id', $feasibility->location_id ?? '') }}">
                </div>

                <!-- Longitude Name -->
                <div class="col-md-3">
                    <label for="form-label fw-semibold">Longitude </label>
                    <input type="text" name="longitude" class="form-control" value="{{ old('longitude', $feasibility->longitude ?? '') }}">
                </div>

                <!-- Delivery Company Name -->
                <div class="col-md-3">
                    <label for="form-label fw-semibold">Latitude</label>
                    <input type="text" name="latitude" class="form-control" value="{{ old('latitude', $feasibility->latitude ?? '') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Pincode <span class="text-danger">*</span></label>
                    <input type="text" name="pincode" id="pincode" maxlength="6" value="{{ old('pincode', $feasibility->pincode) }}" class="form-control" required>
                </div>


                <div class="col-md-3">
                    <label class="form-label fw-semibold">State <span class="text-danger">*</span></label>
                    @php $stateValue = old('state', $feasibility->state); @endphp
                    <select name="state" id="state" class="form-select select2-tags">
                        <option value="" {{ $stateValue === '' ? 'selected' : '' }}>Select or Type State</option>
                        @if($stateValue)
                            <option value="{{ $stateValue }}" selected>{{ $stateValue }}</option>
                        @endif
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">District <span class="text-danger">*</span></label>
                    @php $districtValue = old('district', $feasibility->district); @endphp
                    <select name="district" id="district" class="form-select select2-tags">
                        <option value="" {{ $districtValue === '' ? 'selected' : '' }}>Select or Type District</option>
                        @if($districtValue)
                            <option value="{{ $districtValue }}" selected>{{ $districtValue }}</option>
                        @endif
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Area <span class="text-danger">*</span></label>
                    @php $areaValue = old('area', $feasibility->area); @endphp
                    <select name="area" id="post_office" class="form-select select2-tags">
                        <option value="" {{ $areaValue === '' ? 'selected' : '' }}>Select or Type Area</option>
                        @if($areaValue)
                            <option value="{{ $areaValue }}" selected>{{ $areaValue }}</option>
                        @endif
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Address <span class="text-danger">*</span></label>
                    <textarea name="address" class="form-control" rows="1" required>{{ old('address', $feasibility->address) }}</textarea>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">SPOC Name <span class="text-danger">*</span></label>
                    <input type="text" name="spoc_name" value="{{ old('spoc_name', $feasibility->spoc_name) }}" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">SPOC Contact 1 <span class="text-danger">*</span></label>
                    <input type="text" name="spoc_contact1" value="{{ old('spoc_contact1', $feasibility->spoc_contact1) }}" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">SPOC Contact 2</label>
                    <input type="text" name="spoc_contact2" value="{{ old('spoc_contact2', $feasibility->spoc_contact2) }}" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">SPOC Email</label>
                    <input type="email" name="spoc_email" value="{{ old('spoc_email', $feasibility->spoc_email) }}" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">No. of Links <span class="text-danger">*</span></label>
                    @php $linkValue = (string) old('no_of_links', (string) $feasibility->no_of_links); @endphp
                    <select name="no_of_links" id="no_of_links" class="form-select" required>
                        <option value="" {{ $linkValue === '' ? 'selected' : '' }}>Select</option>
                        <option value="1" {{ $linkValue === '1' ? 'selected' : '' }}>1</option>
                        <option value="2" {{ $linkValue === '2' ? 'selected' : '' }}>2</option>
                        <option value="3" {{ $linkValue === '3' ? 'selected' : '' }}>3</option>
                        <option value="4" {{ $linkValue === '4' ? 'selected' : '' }}>4</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Vendor Type <span class="text-danger">*</span></label>
                    @php $vendorTypeValue = old('vendor_type', $feasibility->vendor_type); @endphp
                    <select name="vendor_type" id="vendor_type" class="form-select" required>
                        <option value="" {{ $vendorTypeValue === '' ? 'selected' : '' }}>Select</option>
                        <option value="Same Vendor" {{ $vendorTypeValue === 'Same Vendor' ? 'selected' : '' }}>Same Vendor</option>
                        <option value="Different Vendor" {{ $vendorTypeValue === 'Different Vendor' ? 'selected' : '' }}>Different Vendor</option>
                        <option value="UBN" {{ $vendorTypeValue === 'UBN' ? 'selected' : '' }}>UBN</option>
                        <option value="UBS" {{ $vendorTypeValue === 'UBS' ? 'selected' : '' }}>UBS</option>
                        <option value="UBL" {{ $vendorTypeValue === 'UBL' ? 'selected' : '' }}>UBL</option>
                        <option value="INF" {{ $vendorTypeValue === 'INF' ? 'selected' : '' }}>INF</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Speed <span class="text-danger">*</span></label>
                    <input type="text" name="speed" value="{{ old('speed', $feasibility->speed) }}" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Static IP <span class="text-danger">*</span></label>
                    @php $staticIpValue = old('static_ip', $feasibility->static_ip); @endphp
                    <select name="static_ip" id="static_ip" class="form-select" required>
                        <option value="" {{ $staticIpValue === '' ? 'selected' : '' }}>Select</option>
                        <option value="Yes" {{ $staticIpValue === 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ $staticIpValue === 'No' ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Static IP Subnet</label>
                    @php $staticIpSubnetValue = old('static_ip_subnet', $feasibility->static_ip_subnet); @endphp
                    <select name="static_ip_subnet" id="static_ip_subnet" class="form-select" {{ $staticIpValue === 'Yes' ? '' : 'disabled' }}>
                        <option value="" {{ $staticIpSubnetValue === '' ? 'selected' : '' }}>Select Subnet</option>
                        @foreach(['/32','/31','/30','/29','/28','/27','/26','/25','/24'] as $sub)
                            <option value="{{ $sub }}" {{ $staticIpSubnetValue === $sub ? 'selected' : '' }}>{{ $sub }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Static IP Duration</label>
                    @php $staticIpDurationValue = old('static_ip_duration', $feasibility->static_ip_duration); @endphp
                    <select name="static_ip_duration" id="static_ip_duration" class="form-select" {{ $staticIpValue === 'Yes' ? '' : 'disabled' }} {{ $staticIpValue === 'Yes' ? 'required' : '' }}>
                        <option value="" {{ $staticIpDurationValue === '' ? 'selected' : '' }}>Select Duration</option>
                        <option value="Monthly" {{ $staticIpDurationValue === 'Monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="Yearly" {{ $staticIpDurationValue === 'Yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Expected Delivery <span class="text-danger">*</span></label>
                    <input type="date" name="expected_delivery" value="{{ old('expected_delivery', $feasibility->expected_delivery) }}" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Expected Activation <span class="text-danger">*</span></label>
                    <input type="date" name="expected_activation" value="{{ old('expected_activation', $feasibility->expected_activation) }}" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Hardware Required <span class="text-danger">*</span></label>
                    @php $hardwareRequiredValue = old('hardware_required', $feasibility->hardware_required); @endphp
                    <select name="hardware_required" id="hardware_required" class="form-select" required>
                        <option value="" {{ $hardwareRequiredValue === '' ? 'selected' : '' }}>Select</option>
                        <option value="1" {{ (string) $hardwareRequiredValue === '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ (string) $hardwareRequiredValue === '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
    @php
    $hardwareDetails = $feasibility->hardware_details;
    if (is_string($hardwareDetails)) {
        $hardwareDetails = json_decode($hardwareDetails, true) ?? [];
    }
@endphp

<!-- container for all hardware rows -->
<div id="hardware_container">
    <div class="row hardware_row" style="display:none;">
        <div class="col-md-3">
            <label>Make</label>
            <select name="make_type_id[]" class="form-control">
                <option value="">Select Make</option>
                @foreach($makes as $m)
                    <option value="{{ $m->id }}">{{ $m->make_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <div class="flex-grow-1">
                <label>Model</label>
                <select name="model_id[]" class="form-control">
                    <option value="">Select Model</option>
                    @foreach($models as $m)
                        <option value="{{ $m->id }}">{{ $m->model_name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="button" class="btn btn-danger btn-sm ms-2 mb-1 remove-hardware" style="height:38px;">X</button>
        </div>
    </div>
</div>

<div class="col-md-3 mt-3" id="add_btn_div" >
    <button type="button" id="add_hardware_btn" class="btn btn-primary btn-sm">Add</button>
</div>


                                <input type="hidden" name="status" value="{{ $feasibility->status }}">
                        </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update</button>
                <a href="{{ route('feasibility.index') }}" class="btn btn-secondary">Cancel</a>
            </div>

        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>

document.addEventListener('DOMContentLoaded', function() {

    const hardwareRequiredSelect = document.getElementById('hardware_required');
    const hardwareContainer = document.getElementById('hardware_container');
    const addBtnDiv = document.getElementById('add_btn_div');
    const addHardwareBtn = document.getElementById('add_hardware_btn');

    // Function to toggle visibility based on hardware required
    function toggleHardwareRows() {
        if (hardwareRequiredSelect.value === '1') {
            // Show first row and Add button
            const firstRow = hardwareContainer.querySelector('.hardware_row');
            if (firstRow) {
                firstRow.style.display = 'flex';
                firstRow.querySelectorAll('input, select, textarea').forEach(el => {
                    el.disabled = false;
                    if (el.name === 'make_type_id[]' || el.name === 'model_id[]') {
                        el.required = true;
                    }
                });
            }
            addBtnDiv.style.display = 'block';
        } else {
            // Hide all rows and Add button
            hardwareContainer.querySelectorAll('.hardware_row').forEach(row => {
                row.style.display = 'none';
                row.querySelectorAll('input, select, textarea').forEach(el => {
                    el.value = '';
                    el.disabled = true;
                    el.required = false;
                });
            });
            addBtnDiv.style.display = 'none';
        }
    }

    // Initial toggle on page load
    toggleHardwareRows();

    // Listen for change on Hardware Required select
    hardwareRequiredSelect.addEventListener('change', toggleHardwareRows);

    // Add new hardware row
    addHardwareBtn.addEventListener('click', function() {
        const originalRow = hardwareContainer.querySelector('.hardware_row');
        if (!originalRow) return;

        const newRow = originalRow.cloneNode(true);
        newRow.querySelectorAll('input, select, textarea').forEach(el => {
            el.value = '';
            el.disabled = false;
            if (el.name === 'make_type_id[]' || el.name === 'model_id[]') {
                el.required = true;
            }
        }); // reset values and enable
        newRow.style.display = 'flex';
        hardwareContainer.appendChild(newRow);
    });

});
// 
// Remove hardware row
document.getElementById('hardware_container').addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('remove-hardware')) {
        const row = e.target.closest('.hardware_row');
        if (row) {
            row.remove();
        }
    }
});

// 
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
    if (noOfLinksSelect && noOfLinksSelect.value) {
        noOfLinksSelect.dispatchEvent(new Event('change'));
    }
    const staticIPSelect = document.getElementById('static_ip');
    const subnetSelect = document.getElementById('static_ip_subnet');
    const durationSelect = document.getElementById('static_ip_duration');
    const typeOfServiceSelect = document.getElementById('type_of_service');

    function updateStaticIpDependentFields() {
        if (!staticIPSelect) return;
        const isStaticYes = staticIPSelect.value === 'Yes';
        [subnetSelect, durationSelect].forEach(select => {
            if (!select) return;
            select.disabled = !isStaticYes;
            select.required = isStaticYes;
            if (!isStaticYes) {
                select.value = '';
            }
        });
    }

    function enforceStaticIpForILL() {
        if (!typeOfServiceSelect || !staticIPSelect) return;
        if (typeOfServiceSelect.value === 'ILL') {
            staticIPSelect.value = 'Yes';
        }
        staticIPSelect.required = true;
        updateStaticIpDependentFields();
    }

    function checkStaticIP() {
        if (typeOfServiceSelect && typeOfServiceSelect.value === 'ILL' && staticIPSelect && staticIPSelect.value === 'No') {
            alert("For ILL service, Static IP is mandatory. Please select Yes.");
            staticIPSelect.value = 'Yes';
            updateStaticIpDependentFields();
        }
    }

    if (staticIPSelect) {
        staticIPSelect.addEventListener('change', function() {
            checkStaticIP();
            updateStaticIpDependentFields();
        });
    }
    if (typeOfServiceSelect) {
        typeOfServiceSelect.addEventListener('change', enforceStaticIpForILL);
    }

    updateStaticIpDependentFields();
    enforceStaticIpForILL();
});
</script>
@endsection