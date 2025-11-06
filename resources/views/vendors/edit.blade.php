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
            
             {{-- ✅ PAN Input --}}
            <div class="col-md-4">
                <label class="form-label">PAN Number</label>
                <input type="text" id="pan_number" name="pan_number" class="form-control" placeholder="Enter PAN Number">
            </div>

            <small id="panStatus" class="text-muted mt-1 d-block"></small>

            {{-- ✅ GST State --}}
            <div class="col-md-4 mt-3">
                <label class="form-label">Select GST State</label>
                <select id="gst_state" class="form-select select2-tags">
                    <option value="">-- Select State --</option>
                    <option value="29">Karnataka</option>
                    <option value="33">Tamil Nadu</option>
                    <option value="36">Telangana</option>
                    <option value="27">Maharashtra</option>
                    <option value="07">Delhi</option>
                </select>
            </div>

            <small id="gstStatus" class="mt-2 d-block text-muted"></small>

            <hr>

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
            <!-- <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="Active" {{ old('status', $vendor->status) == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ old('status', $vendor->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div> -->
            <input type="hidden" name="status" value="Active">


            {{-- Buttons --}}
            <button type="submit" class="btn btn-primary mt-3">Update Vendor</button>
            <a href="{{ route('vendors.index') }}" class="btn btn-secondary mt-3">Cancel</a>
        </form>
    </div>
</div>

{{-- ✅ GST Fetch JS --}}
<script>
function fetchGST() {
    let pan = document.getElementById("pan_number").value.trim();
    let state = document.getElementById("gst_state").value;
    let gstStatus = document.getElementById("gstStatus");

    if (pan.length !== 10 || state === "") {
        gstStatus.innerHTML = "⚠️ Enter valid PAN + Select State";
        return;
    }

    gstStatus.innerHTML = "⏳ Fetching GST details...";

    fetch(`/api/gst/fetch/${pan}/${state}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById("gstin").value = data.data.gstin;
                document.getElementById("business_display_name").value = data.data.trade_name;
                document.getElementById("address1").value = data.data.address;
                document.getElementById("billing_spoc_email").value = data.data.company_email;
                document.getElementById("billing_spoc_contact").value = data.data.company_phone;

                gstStatus.innerHTML = "✅ GST Details Auto-filled!";
            } else {
                gstStatus.innerHTML = "❌ GST Not Found for this PAN + State";
            }
        })
        .catch(() => {
            gstStatus.innerHTML = "⚠️ Server Error";
        });
}

document.getElementById("pan_number").addEventListener("blur", fetchGST);
document.getElementById("gst_state").addEventListener("change", fetchGST);
</script>

@endsection
