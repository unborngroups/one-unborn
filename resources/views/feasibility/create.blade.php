@extends('layouts.app')



@section('content')

<div class="container-fluid py-4">

    <h4 class="text-primary fw-bold mb-3 ">Add Feasibility</h4>
    
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
                                    <a href="{{ asset('images/feasibilityimport/Book 4 (9).xlsx') }}" target="_blank" class="btn btn-outline-secondary" title="Download import template">Download Format</a>
                                    <button type="submit" class="btn btn-primary">Import Excel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    <div class="card shadow border-0 p-4">



     {{-- ⚠️ Display validation errors if any --}}

        @if ($errors->any())

            <div class="alert alert-danger">

                <ul class="mb-0">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li> {{-- List each validation error --}}

                    @endforeach

                </ul>

            </div>

        @endif

        @if (session('success'))

            <div class="alert alert-success">
                {{ session('success') }}
            </div>

        @endif


<!--         

        <h5 class="mb-3">Import Feasibility</h5>
        <div class="row g-3 align-items-center ml-2 mb-4">
            <div class="col-md-4">
                <form action="{{ route('feasibility.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="input-group">
                        <input type="file" name="file" class="form-control" required>
                        <button type="submit" class="btn btn-primary">Import Excel</button>
                    </div>
                </form>
            </div>
    </div>
 -->



         {{-- Form starts here --}}

        <form action="{{ route('feasibility.store') }}" method="POST">

            @csrf

            <div class="row g-3">
                <!-- Feasibility ID -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">Feasibility Request ID</label>

                    <input type="text" class="form-control bg-light" value="Auto-generated" readonly>

                    <!-- <small class="text-muted">ID will be generated automatically when saved</small> -->

                </div>
                <!-- Type Of Service -->

                <div class="col-md-3">

                    <label class="form-label fw-semibold">Type of Service <span class="text-danger">*</span></label>

                    @php $typeSelection = old('type_of_service', $importRow['type_of_service'] ?? ''); @endphp
                    <select name="type_of_service" id="type_of_service" class="form-select" required>

                        <option value="" {{ $typeSelection === '' ? 'selected' : '' }}>Select</option>

                        <option value="Broadband" {{ $typeSelection === 'Broadband' ? 'selected' : '' }}>Broadband</option>

                        <option value="ILL" {{ $typeSelection === 'ILL' ? 'selected' : '' }}>ILL</option>

                        <option value="P2P" {{ $typeSelection === 'P2P' ? 'selected' : '' }}>P2P</option>

                    </select>

                </div>
                <!-- Company Name -->

                <div class="col-md-3">

                    <label class="form-label fw-semibold">Company <span class="text-danger">*</span></label>

                    <select name="company_id" id="company_id" class="form-select" required>

                        <option value="">Select Company</option>

                        @foreach($companies as $company)

                            <option value="{{ $company->id }}" {{ (string) old('company_id', $importRow['company_id'] ?? '') === (string) $company->id ? 'selected' : '' }}>{{ $company->company_name }}</option>

                        @endforeach

                    </select>

                </div>

                <!-- Client Name -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">Client Name <span class="text-danger">*</span></label>

                    <select name="client_id" id="client_id" class="form-select" required>

                        <option value="">Select Client</option>

                        @foreach($clients as $client)

                            <option value="{{ $client->id }}" {{ (string) old('client_id', $importRow['client_id'] ?? '') === (string) $client->id ? 'selected' : '' }}>{{ $client->business_name ?: $client->client_name }}</option>

                        @endforeach

                    </select>

                </div>

                <!-- Delivery Company Name -->
                <div class="col-md-3">
                    <label for="form-label fw-semibold">Delivery Company Name</label>
                    <input type="text" name="delivery_company_name" class="form-control" value="{{ old('delivery_company_name', $importRow['delivery_company_name'] ?? '') }}">
                </div>

                <!-- pincode -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">Pincode <span class="text-danger">*</span></label>

                    <input type="text" name="pincode" id="pincode" maxlength="6" class="form-control" required value="{{ old('pincode', $importRow['pincode'] ?? '') }}">

           <!-- <button type="button" id="pincodeVerifyBtn" class="btn btn-primary">Verify</button> -->

                </div>
                <!-- Select State -->

                <div class="col-md-3">

                    <label class="form-label fw-semibold">State <span class="text-danger">*</span></label>

                    @php $stateValue = old('state', $importRow['state'] ?? ''); @endphp
                    <select name="state" id="state" class="form-select select2-tags">

                        <option value="" {{ $stateValue === '' ? 'selected' : '' }}>Select or Type State</option>

                        <option value="Karnataka" {{ $stateValue === 'Karnataka' ? 'selected' : '' }}>Karnataka</option>

                        <option value="Tamil Nadu" {{ $stateValue === 'Tamil Nadu' ? 'selected' : '' }}>Tamil Nadu</option>

                        <option value="Telangana" {{ $stateValue === 'Telangana' ? 'selected' : '' }}>Telangana</option>

                    </select>

                </div>
                <!-- Select District -->

                <div class="col-md-3">

                    <label class="form-label fw-semibold">District <span class="text-danger">*</span></label>

                   @php $districtValue = old('district', $importRow['district'] ?? ''); @endphp
                   <select name="district" id="district" class="form-select select2-tags">

                        <option value="" {{ $districtValue === '' ? 'selected' : '' }}>Select or Type District</option>

                        <option value="Salem" {{ $districtValue === 'Salem' ? 'selected' : '' }}>Salem</option>

                        <option value="Dharmapuri" {{ $districtValue === 'Dharmapuri' ? 'selected' : '' }}>Dharmapuri</option>

                        <option value="Erode" {{ $districtValue === 'Erode' ? 'selected' : '' }}>Erode</option>

                    </select>

                </div>
                <!-- Select Area -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">Area <span class="text-danger">*</span></label>
                    @php $areaValue = old('area', $importRow['area'] ?? ''); @endphp
                      
                    <select name="area" id="post_office" class="form-select select2-tags">

                        <option value="">Select or Type Area</option>

                        <option value="Uthagarai" {{ $areaValue === 'Uthagarai' ? 'selected' : '' }}>Uthagarai</option>

                        <option value="Harur" {{ $areaValue === 'Harur' ? 'selected' : '' }}>Harur</option>

                        <option value="Kottaiyur" {{ $areaValue === 'Kottaiyur' ? 'selected' : '' }}>Kottaiyur</option>
                    </select>

                </div>
                <!-- Address -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">Address <span class="text-danger">*</span></label>

                    <textarea name="address" class="form-control" rows="1" required>{{ old('address', $importRow['address'] ?? '') }}</textarea>

                </div>
                <!-- SPOC Name -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">SPOC Name <span class="text-danger">*</span></label>

                    <input type="text" name="spoc_name" class="form-control" value="{{ old('spoc_name', $importRow['spoc_name'] ?? '') }}" required>

                </div>
                <!-- SPOC Contact 1-->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">SPOC Contact 1 <span class="text-danger">*</span></label>

                    <input type="text" name="spoc_contact1" class="form-control" value="{{ old('spoc_contact1', $importRow['spoc_contact1'] ?? '') }}" required>

                </div>
                <!-- SPOC Contact2 -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">SPOC Contact 2</label>

                    <input type="text" name="spoc_contact2" class="form-control" value="{{ old('spoc_contact2', $importRow['spoc_contact2'] ?? '') }}">

                </div>
                <!-- SPOC Email -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">SPOC Email</label>

                    <input type="email" name="spoc_email" class="form-control" value="{{ old('spoc_email', $importRow['spoc_email'] ?? '') }}" >

                </div>
                <!-- No Of Links -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">No. of Links <span class="text-danger">*</span></label>
                    @php $linkValue = old('no_of_links', $importRow['no_of_links'] ?? ''); @endphp
                      
                    <select name="no_of_links" class="form-select" required>

                        <option value="" {{ $linkValue === '' ? 'selected' : '' }}>Select</option>

                        <option value="1" {{ $linkValue === '1' ? 'selected' : '' }}>1</option>

                        <option value="2" {{ $linkValue === '2' ? 'selected' : '' }}>2</option>
                        <option value="3" {{ $linkValue === '3' ? 'selected' : '' }}>3</option>

                        <option value="4" {{ $linkValue === '4' ? 'selected' : '' }}>4</option>

                    </select>

                </div>
                <!-- Vendor Type -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">Vendor Type <span class="text-danger">*</span></label>
                    @php $vendorTypeValue = old('vendor_type', $importRow['vendor_type'] ?? ''); @endphp
                      
                    <select name="vendor_type" class="form-select" required>

                        <option value="" {{ $vendorTypeValue === '' ? 'selected' : '' }}>Select</option>

                        <option {{ $vendorTypeValue === 'Same Vendor' ? 'selected' : '' }}>Same Vendor</option>

                        <option {{ $vendorTypeValue === 'Different Vendor' ? 'selected' : '' }}>Different Vendor</option>
                        <option {{ $vendorTypeValue === 'UBN' ? 'selected' : '' }}>UBN</option>

                        <option {{ $vendorTypeValue === 'UBS' ? 'selected' : '' }}>UBS</option>

                        <option {{ $vendorTypeValue === 'UBL' ? 'selected' : '' }}>UBL</option>

                        <option {{ $vendorTypeValue === 'INF' ? 'selected' : '' }}>INF</option>

                    </select>

                </div>

                <!-- Speed -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">Speed <span class="text-danger">*</span></label>

                    <input type="text" name="speed" placeholder="Mbps or Gbps" class="form-control" value="{{ old('speed', $importRow['speed'] ?? '') }}" required>

                </div>
                <!-- Static IP -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">Static IP <span class="text-danger">*</span></label>
                    @php $staticIpValue = old('static_ip', $importRow['static_ip'] ?? ''); @endphp
                        
                    <select name="static_ip" id="static_ip" class="form-select" required>

                        <option value="" {{ $staticIpValue === '' ? 'selected' : '' }}>Select</option>

                        <option value="Yes" {{ $staticIpValue === 'Yes' ? 'selected' : '' }}>Yes</option>

                        <option value="No" {{ $staticIpValue === 'No' ? 'selected' : '' }}>No</option>

                    </select>

                </div>
                <!-- Static IP Subnet -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">Static IP Subnet</label>
                    @php $staticIpSubnetValue = old('static_ip_subnet', $importRow['static_ip_subnet'] ?? ''); @endphp
                    <select name="static_ip_subnet" id="static_ip_subnet" class="form-select" disabled>

                        <option value="" {{ $staticIpSubnetValue === '' ? 'selected' : '' }}>Select Subnet</option>

                        <option value="/32" {{ $staticIpSubnetValue === '/32' ? 'selected' : '' }}>/32</option>

                        <option value="/31" {{ $staticIpSubnetValue === '/31' ? 'selected' : '' }}>/31</option>

                        <option value="/30" {{ $staticIpSubnetValue === '/30' ? 'selected' : '' }}>/30</option>
                        <option value="/29" {{ $staticIpSubnetValue === '/29' ? 'selected' : '' }}>/29</option>

                        <option value="/28" {{ $staticIpSubnetValue === '/28' ? 'selected' : '' }}>/28</option>

                        <option value="/27" {{ $staticIpSubnetValue === '/27' ? 'selected' : '' }}>/27</option>

                        <option value="/26" {{ $staticIpSubnetValue === '/26' ? 'selected' : '' }}>/26</option>

                        <option value="/25" {{ $staticIpSubnetValue === '/25' ? 'selected' : '' }}>/25</option>
                        <option value="/24" {{ $staticIpSubnetValue === '/24' ? 'selected' : '' }}>/24</option>

                    </select>

                    <small class="text-muted">Select subnet only if Static IP is Yes</small>

                </div>
                <!-- Expected Delivery -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">Expected Delivery <span class="text-danger">*</span></label>

                    <input type="date" name="expected_delivery" class="form-control" value="{{ old('expected_delivery', $importRow['expected_delivery'] ?? '') }}" required>

                </div>
                <!-- Expected Activation -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">Expected Activation <span class="text-danger">*</span></label>

                    <input type="date" name="expected_activation" class="form-control" value="{{ old('expected_activation', $importRow['expected_activation'] ?? '') }}" required>

                </div>
                <!-- Hardware Required -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">Hardware Required <span class="text-danger">*</span></label>
                    @php $hardwareRequiredValue = old('hardware_required', $importRow['hardware_required'] ?? ''); @endphp
                    <select name="hardware_required" id="hardware_required" class="form-select" required>

                        <option value="" {{ $hardwareRequiredValue === '' ? 'selected' : '' }}>Select</option>

                        <option value="1" {{ $hardwareRequiredValue === '1' ? 'selected' : '' }}>Yes</option>

                        <option value="0" {{ $hardwareRequiredValue === '0' ? 'selected' : '' }}>No</option>

                    </select>

                </div>
                
<!-- container for all hardware rows -->
<div id="hardware_container">
    <div class="row hardware_row" style="display:none;">
        <div class="col-md-3">
            <label>Make</label>
            <select name="make_type_id[]" class="form-control" required>
                <option value="">Select Make</option>
                @foreach($makes as $m)
                    <option value="{{ $m->id }}">{{ $m->make_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <div class="flex-grow-1">
                <label>Model</label>
                <select name="model_id[]" class="form-control" required>
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

<div class="col-md-3 mt-3" id="add_btn_div" style="display:none;">
    <button type="button" id="add_hardware_btn" class="btn btn-primary btn-sm">Add</button>
</div>
               

                    {{--  Status Dropdown --}}

            <input type="hidden" name="status" value="Active">





            </div>



            <div class="mt-4 text-end">

                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>

                <!-- <a href="{{ route('feasibility.index') }}" class="btn btn-secondary">Cancel</a> -->

            </div>

        </form>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>



<script>

// document.getElementById('hardware_required').addEventListener('change', function() {

//     document.getElementById('hardware_name_div').style.display = this.value == '1' ? 'block' : 'none';
//     document.getElementById('hardware_make_div').style.display = this.value == '1' ? 'block' : 'none';

// });
const hardwareRequiredSelect = document.getElementById('hardware_required');
const hardwareRow = document.querySelector('.hardware_row');
const addHardwareBtnDiv = document.getElementById('add_btn_div');

function setHardwareRowState(row, enabled) {
    const selects = row.querySelectorAll('select');
    selects.forEach(select => {
        select.disabled = !enabled;
        if (!enabled) {
            select.value = '';
        }
    });
    row.style.display = enabled ? 'flex' : 'none';
}

setHardwareRowState(hardwareRow, false);

hardwareRequiredSelect.addEventListener('change', function () {
    const isRequired = this.value === '1';
    setHardwareRowState(hardwareRow, isRequired);
    addHardwareBtnDiv.style.display = isRequired ? 'block' : 'none';
});

document.getElementById('add_hardware_btn').addEventListener('click', function () {
    let originalRow = document.querySelector('.hardware_row');
    let newRow = originalRow.cloneNode(true);
    newRow.querySelectorAll('input').forEach(function (input) {
        input.value = "";
    });
    newRow.style.display = 'flex';
    newRow.querySelectorAll('select').forEach(select => {
        select.disabled = false;
        select.value = '';
    });
    // Attach remove button event
    let removeBtn = newRow.querySelector('.remove-hardware');
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            let allRows = document.querySelectorAll('.hardware_row');
            if (allRows.length > 1) {
                newRow.remove();
            } else {
                // Only one row: clear values
                newRow.querySelectorAll('input, select').forEach(el => el.value = '');
            }
        });
    }
    document.getElementById('hardware_container').appendChild(newRow);
});

// Attach remove event to initial row
let initialRemoveBtn = document.querySelector('.hardware_row .remove-hardware');
if (initialRemoveBtn) {
    initialRemoveBtn.addEventListener('click', function () {
        let allRows = document.querySelectorAll('.hardware_row');
        let row = this.closest('.hardware_row');
        if (allRows.length > 1) {
            row.remove();
        } else {
            row.querySelectorAll('input, select').forEach(el => el.value = '');
        }
    });
}



// Helper function to set value in select dropdown, creating option if needed

function setSelectValue(selectElement, value) {

  console.log('setSelectValue called with:', selectElement.id, 'value:', value);

  

  if (!value || value === '') {

    selectElement.value = '';

    // If it's a Select2 element, trigger change

    if (typeof $ !== 'undefined' && typeof $(selectElement).select2 === 'function') {

      $(selectElement).val('').trigger('change');

    }

    return;

  }

  

  // Check if option already exists

  let optionExists = false;

  for (let option of selectElement.options) {

    if (option.value === value) {

      optionExists = true;

      break;

    }

  }

  // If option doesn't exist, create it

  if (!optionExists) {

    const newOption = document.createElement('option');

    newOption.value = value;

    newOption.text = value;

    selectElement.appendChild(newOption);

    console.log('Created new option:', value, 'for', selectElement.id);

  }

  

  // Set the value

  selectElement.value = value;

  console.log('Set native value for', selectElement.id, 'to:', value);

  

  // Handle Select2 if available

  if (typeof $ !== 'undefined') {

    try {

      const $element = $(selectElement);

      // Check if Select2 is initialized

      if (typeof $element.select2 === 'function' && $element.hasClass('select2-hidden-accessible')) {

        $element.val(value).trigger('change');

        console.log('Triggered Select2 change for', selectElement.id);

      }

    } catch (error) {

      console.log('Select2 not available or error:', error);

    }

  }

  

  console.log('Final value for', selectElement.id, ':', selectElement.value);

}



// Pincode lookup function

function lookupPincode() {

  const pincodeField = document.getElementById('pincode');

  const p = pincodeField.value.trim();

  

  // Only proceed if we have exactly 6 digits

  if (!/^\d{6}$/.test(p)) return;

  

  // Get field references

  const stateField = document.getElementById('state');

  const districtField = document.getElementById('district');

  const areaField = document.getElementById('post_office');

  

  // Store original values in case of error

  const originalState = stateField.value;

  const originalDistrict = districtField.value;

  const originalArea = areaField.value;

  

  // Show loading state

  setSelectValue(stateField, 'Loading...');

  setSelectValue(districtField, 'Loading...');

  setSelectValue(areaField, 'Loading...');

  

  console.log('🔍 Looking up pincode:', p);

  

  // Make API call

  axios.post('/api/pincode/lookup', { pincode: p })

    .then(r => {

      const d = r.data;

      console.log('✅ Pincode lookup successful:', d);

      console.log('State field element:', stateField);

      console.log('District field element:', districtField);

      console.log('Area field element:', areaField);

      

      // Update fields with fetched data

      console.log('Setting state to:', d.state);

      setSelectValue(stateField, d.state || '');

      

      console.log('Setting district to:', d.district);

      setSelectValue(districtField, d.district || '');

      

      console.log('Setting area to:', d.post_office);

      setSelectValue(areaField, d.post_office || '');

      

      // Show success message briefly

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

      

      // Remove notification after 3 seconds

      setTimeout(() => {

        if (notification.parentNode) {

          notification.parentNode.removeChild(notification);

        }

      }, 3000);

    })

    .catch(err => {

      console.error('❌ Pincode lookup failed:', err);

      

      // Restore original values

      setSelectValue(stateField, originalState);

      setSelectValue(districtField, originalDistrict);

      setSelectValue(areaField, originalArea);

      

      // Show error message

      let errorMessage = 'Unable to fetch pincode details. Please try again or enter manually.';

      if (err.response && err.response.status === 404) {

        errorMessage = 'Pincode not found. Please check the pincode and try again.';

      } else if (err.response && err.response.status === 422) {

        errorMessage = 'Invalid pincode format. Please enter a 6-digit pincode.';

      }

      

      // Show error notification

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

      

      // Remove error notification after 5 seconds

      setTimeout(() => {

        if (errorNotification.parentNode) {

          errorNotification.parentNode.removeChild(errorNotification);

        }

      }, 5000);

    });

}
// Add multiple event listeners for better responsiveness

const pincodeInput = document.getElementById('pincode');

// Trigger on blur (when user clicks outside the field)

pincodeInput.addEventListener('blur', lookupPincode);

// Trigger on Enter key press

document.addEventListener('DOMContentLoaded', function () {

    const staticIpSelect   = document.getElementById('static_ip');
    const subnetSelect     = document.getElementById('static_ip_subnet');
    const typeServiceSelect = document.getElementById('type_of_service');

    function updateStaticIpSubnetState() {
        if (!staticIpSelect || !subnetSelect) return;

        if (staticIpSelect.value === 'Yes') {
            subnetSelect.disabled = false;
            subnetSelect.required = true;
        } else {
            subnetSelect.disabled = true;
            subnetSelect.required = false;
            subnetSelect.value = '';
        }
    }

    function enforceStaticIpForILL() {
        if (!typeServiceSelect || !staticIpSelect) return;

        if (typeServiceSelect.value === 'ILL') {
            staticIpSelect.value = 'Yes';
            updateStaticIpSubnetState();
        }
    }

    // Events
    staticIpSelect.addEventListener('change', updateStaticIpSubnetState);
    typeServiceSelect.addEventListener('change', enforceStaticIpForILL);

    // Initial load (IMPORTANT for edit/import)
    updateStaticIpSubnetState();
    enforceStaticIpForILL();
});
// document.getElementById('importexcel').

// document.getElementById('importexcel').

function excelImport() {
    // Excel import logic removed; reintroduce carefully if required.
}


</script>
@endsection