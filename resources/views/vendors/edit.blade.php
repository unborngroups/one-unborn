@extends('layouts.app')

@section('content')
   {{-- ✅ Display validation errors if any --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
<div class="container py-4">
    <h3 class="mb-3 text-primary">Edit Vendor</h3>
    <div class="card shadow border-0 p-4">
        {{-- 📝 Form for updating vendor --}}
        <form action="{{ route('vendors.update', $vendor->id) }}" method="POST">
            @csrf
            @method('PUT')
            
             {{-- 🌟 Vendor Creation Form --}}
             <div class="col-md-4">
    <label class="form-label">PAN Number</label>
    <input type="text" id="pan_number" name="pan_number" class="form-control" 
           placeholder="Enter PAN Number">
           <!-- Button commented out (optional verification trigger) -->
           <button type="button" id="verifyPanBtn" class="btn btn-primary">Verify</button>
</div>
<!--  PAN status message area -->
  <small id="panStatus" class="text-muted mt-1 d-block"></small>


            {{-- Basic Details Section --}}
            <h5 class="text-secondary">Basic Details</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Vendor Name</label>
                    <input type="text" name="vendor_name" class="form-control"
                           value="{{ old('vendor_name', $vendor->vendor_name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Vendor Code</label>
                    <input type="text" class="form-control"
                           value="{{ $vendor->vendor_code }}" readonly>
                </div>
            </div>

             {{-- 🏢 Business Display Name --}}
            <div class="mb-3">
                <label class="form-label">Business Display Name</label>
                <input type="text" name="business_display_name" class="form-control"
                       value="{{ old('business_display_name', $vendor->business_display_name) }}">
            </div>

            {{-- 📍 Address Section --}}
            <h5 class="text-secondary mt-3">Address</h5>
            <input type="text" name="address1" class="form-control mb-2" placeholder="Address Line 1"
                   value="{{ old('address1', $vendor->address1) }}">
            <input type="text" name="address2" class="form-control mb-2" placeholder="Address Line 2"
                   value="{{ old('address2', $vendor->address2) }}">
            <input type="text" name="address3" class="form-control mb-2" placeholder="Address Line 3"
                   value="{{ old('address3', $vendor->address3) }}">
            {{-- 🗺️ City, State, Country dropdowns --}}
            <div class="row">
    <div class="col-md-4">
        <label class="form-label">City</label>
        <select name="city" id="city" class="form-select select2-tags">
            <option value="">Select or Type City</option>
            <option value="Bangalore">Bangalore</option>
            <option value="Chennai">Chennai</option>
            <option value="Hyderabad">Hyderabad</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">State</label>
        <select name="state" id="state" class="form-select select2-tags">
            <option value="">Select or Type State</option>
            <option value="Karnataka">Karnataka</option>
            <option value="Tamil Nadu">Tamil Nadu</option>
            <option value="Telangana">Telangana</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">Country</label>
        <select name="country" id="country" class="form-select select2-tags">
            <option value="">Select or Type Country</option>
            <option value="India">India</option>
            <option value="USA">USA</option>
            <option value="UK">UK</option>
        </select>
    </div>
</div>
<br>
            <input type="text" name="pincode" class="form-control mb-3" placeholder="Pincode"
                   value="{{ old('pincode', $vendor->pincode) }}">

            {{-- Contact Person Section--}}
            <h5 class="text-secondary mt-3">Contact Person</h5>
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="contact_person_name" class="form-control mb-2" placeholder="Contact Person Name"
                           value="{{ old('contact_person_name', $vendor->contact_person_name) }}">
                </div>
                <div class="col-md-4">
                    <input type="text" name="contact_person_mobile" class="form-control mb-2" placeholder="Mobile Number"
                           value="{{ old('contact_person_mobile', $vendor->contact_person_mobile) }}">
                </div>
                <div class="col-md-4">
                    <input type="email" name="contact_person_email" class="form-control mb-2" placeholder="Email"
                           value="{{ old('contact_person_email', $vendor->contact_person_email) }}">
                </div>
            </div>

            {{-- Legal Details Section--}}
            <h5 class="text-secondary mt-3">Legal Details</h5>
            <div class="row">
                <div class="col-md-6">
                    <input type="text" name="gstin" class="form-control mb-2" placeholder="GSTIN"
                           value="{{ old('gstin', $vendor->gstin) }}">
                </div>
                <div class="col-md-6">
                    <input type="text" name="pan_no" class="form-control mb-2" placeholder="PAN No"
                           value="{{ old('pan_no', $vendor->pan_no) }}">
                </div>
                <div class="col-md-6">
                    <input type="text" name="bank_account_no" class="form-control mb-2" placeholder="bank_account_no"
                           value="{{ old('bank_account_no', $vendor->bank_account_no) }}">
                </div>
                <div class="col-md-6">
                    <input type="text" name="ifsc_code" class="form-control mb-2" placeholder="ifsc_code"
                           value="{{ old('ifsc_code', $vendor->ifsc_code) }}">
                </div>
            </div>

            {{-- Status Dropdown--}}
            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="Active" {{ old('status', $vendor->status) == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ old('status', $vendor->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            {{-- Buttons --}}
            <button type="submit" class="btn btn-primary mt-3">Update Vendor</button>
            <a href="{{ route('vendors.index') }}" class="btn btn-secondary mt-3">Cancel</a>
        </form>
    </div>
</div>
{{-- ⚙️ JS section for GST autofill using external API --}}
<script>
document.addEventListener("DOMContentLoaded", function() {
    const gstInput = document.querySelector('[name="gstin"]');

    gstInput.addEventListener('blur', function() {
        let gstin = this.value.trim();
        if (gstin.length === 15) {
            fetch(`/gst/fetch/${gstin}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // ✅ Autofill form fields
                    document.querySelector('[name="business_display_name"]').value = data.data.tradeNam || '';
                    document.querySelector('[name="pan_no"]').value = data.pan || '';

                    // ✅ Address fill (if available)
                    if (data.data.pradr && data.data.pradr.addr) {
                        let addr = data.data.pradr.addr;
                        document.querySelector('[name="address1"]').value = (addr.bnm || '') + ' ' + (addr.st || '');
                        document.querySelector('[name="city"]').value = addr.loc || '';
                        document.querySelector('[name="state"]').value = addr.stcd || '';
                    }

                    alert("✅ Company details filled successfully!");
                } else {
                    alert("❌ Invalid GST Number");
                }
            })
            .catch(err => {
                console.error(err);
                alert("⚠️ Error fetching GST details");
            });
        }
    });
});
 // ✅ Verify PAN button click
    const verifyPanBtn = document.getElementById('verifyPanBtn');
    const panInput = document.getElementById('pan_number');
    const panStatus = document.getElementById('panStatus');

    verifyPanBtn.addEventListener('click', function() {
        let pan = panInput.value.trim().toUpperCase();
        // 🧩 Basic validation (PAN must be 10 chars)
        if (pan.length !== 10) {
            panStatus.innerHTML = '<span class="text-danger">⚠️ Enter a valid 10-character PAN number</span>';
            return;
        }

        // 🕐 Disable button while verifying
        verifyPanBtn.disabled = true;
        verifyPanBtn.textContent = "Verifying...";
        panStatus.textContent = "";

        // 🌐 Call Laravel route for PAN check
        fetch(`/company/fetch/${pan}`)
            .then(res => res.json())
            .then(data => {
                verifyPanBtn.disabled = false;
                verifyPanBtn.textContent = "Verify";

                if (data.success) {
                    // ✅ Fill data from company table
                    let c = data.data;
                    document.querySelector('[name="gstin"]').value = c.gst_no || '';
                    document.querySelector('[name="business_display_name"]').value = c.company_name || '';
                    document.querySelector('[name="contact_person_email"]').value = c.email_1 || '';
                    document.querySelector('[name="address1"]').value = c.address_line1 || '';

                    panStatus.innerHTML = '<span class="text-success">✅ PAN Verified & details filled!</span>';
                } else {
                    panStatus.innerHTML = '<span class="text-danger">❌ No company found for this PAN</span>';
                }
            })
            .catch(err => {
                verifyPanBtn.disabled = false;
                verifyPanBtn.textContent = "Verify";
                console.error(err);
                panStatus.innerHTML = '<span class="text-danger">⚠️ Error verifying PAN number</span>';
            });
    });

</script>
@endsection
