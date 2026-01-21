@extends('layouts.app')



@section('content')

<div class="container py-4">

    <h3 class="mb-3 text-primary">Add Client</h3>



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



        <form action="{{ route('clients.store') }}" method="POST">

            @csrf



            {{-- ✅ PAN Input --}}

            <!-- <div class="col-md-4">

                <label class="form-label">PAN Number</label>

                <input type="text" id="pan_number" name="pan_number" class="form-control" placeholder="Enter PAN Number">

            </div>



            <small id="panStatus" class="text-muted mt-1 d-block"></small> -->



            {{-- ✅ GST State removed: state now derived per GSTIN; master-level selection no longer needed --}}



            <small id="gstStatus" class="mt-2 d-block text-muted"></small>



            <hr>



            {{-- ✅ Basic Details --}}

            <h5 class="text-secondary">Basic Details</h5>


            {{-- Client Name --}}
            <div class="row mb-3">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Type of Office</label>
                    <select name="office_type" id="office_type" class="form-select" required>
                        <option value="">Select</option>
                        <option value="head">Head Office</option>
                        <option value="branch">Branch Office</option>
                    </select>
                </div>

                <!-- PAN Number: only for Head Office -->
                <div class="col-md-3 mb-3" id="panDiv">
                    <label class="form-label">PAN Number</label>
                    <input type="text"  name="pan_number" class="form-control">
                </div>

                <!-- Head Office selection: only for Branch Office -->
                <div class="col-md-3 mb-3 d-none" id="headOfficeDiv">
                    <label class="form-label">Head Office</label>
                    <select name="head_office_id" class="form-select">
    <option value="">Select Head Office</option>
    @foreach($headOffices as $ho)
        <option value="{{ $ho->id }}">
            {{ $ho->client_name }} ({{ $ho->client_code }})
        </option>
    @endforeach
</select>
                </div>

                <div class="col-md-3">
                    <label for="form-label">Short Name</label>
                    <input type="text" name="short_name" class="form-control">
                </div>


                <div class="col-md-3">
                    <label class="form-label">Client Name</label>
                    <input type="text" name="client_name" class="form-control" required>
                </div>

                <!-- Client Code: only for Head Office -->
                <div class="col-md-3" id="clientCodeDiv">
                    <label class="form-label">Client Code</label>
                    <input type="text" class="form-control" value="Auto Generated" readonly>
                </div>

                <!-- User Name -->
                <div class="col-md-3">
                    <label class="form-label">User Name</label>
                    <div class="input-group">
                        <input type="text" name="user_name" id="user_name" class="form-control" required>
                        <button type="button" name="portal_password" id="sendPwdBtn" class="btn btn-outline-primary">PWD</button>
                    </div>
                    <small id="pwdStatus" class="text-muted d-block mt-1"></small>
                </div>

                <!-- Business Display Name -->
                <div class="col-md-3 mb-3">
                    <label class="form-label">Business Display Name</label>
                    <input type="text" name="business_display_name" id="business_display_name" class="form-control">
                </div>

                <!-- Type of Office -->
                

            </div>

            {{-- ✅ Address --}}

            <h5 class="text-secondary mt-3">Address</h5>



            <input type="text" name="address1" id="address1" class="form-control mb-2" placeholder="Address Line 1">

            <input type="text" name="address2" class="form-control mb-2" placeholder="Address Line 2">

            <input type="text" name="address3" class="form-control mb-2" placeholder="Address Line 3">



            <div class="row p-2">

                <div class="col-md-3">

                    <label class="form-label">City</label>

                    <select name="city" class="form-select select2-tags">

                        <option value="">Select or Type City</option>

                        <option>Bangalore</option>

                        <option>Chennai</option>

                        <option>Hyderabad</option>

                    </select>

                </div>



                <div class="col-md-3">

                    <label class="form-label">State</label>

                    <select name="state" class="form-select select2-tags">

                        <option value="">Select or Type State</option>

                        <option>Karnataka</option>

                        <option>Tamil Nadu</option>

                        <option>Telangana</option>

                    </select>

                </div>



                <div class="col-md-3">

                    <label class="form-label">Country</label>

                    <select name="country" class="form-select select2-tags">

                        <option value="">Select or Type Country</option>

                        <option>India</option>

                        <option>USA</option>

                        <option>UK</option>

                    </select>

                </div>
                <div class="col-md-3">
                    <label for="">Pincode</label>
            <input type="text" name="pincode" class="form-control mb-3 mt-1" placeholder="Pincode">
                    
                </div>

            </div>

            {{-- ✅ Billing Details --}}

            <h5 class="text-secondary mt-3">Business Contact Details</h5>



            <div class="row">

                <div class="col-md-3">

                    <input type="text" name="billing_spoc_name" class="form-control mb-2" placeholder="Billing SPOC Name">

                </div>



                <div class="col-md-3">

                    <input type="text" name="billing_spoc_contact" id="billing_spoc_contact" class="form-control mb-2" placeholder="Contact Number">

                </div>



                <div class="col-md-3">

                    <input type="email" name="billing_spoc_email" id="billing_spoc_email" class="form-control mb-2" placeholder="Email">

                </div>

                <div class="col-md-3">
            <input type="text" name="gstin" id="gstin" class="form-control mb-3" placeholder="GSTIN">
                    
                </div>

                <div class="col-md-3">
                    <!-- <label for="form-label">Billing Sequence</label> -->
                    <select name="billing_sequence" class="form-select">
                        <option value="">select billing sequence</option>
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="half-yearly">Half-Yearly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>

            </div>

            {{-- ✅ Invoice --}}

            <h5 class="text-secondary mt-3">Invoice Details</h5>

            <div class="row mb-3">

                <div class="col-md-4">

                    <label class="form-label">Invoice Email</label>

                    <input type="email" name="invoice_email" class="form-control">

                </div>

                <div class="col-md-4">

                    <label class="form-label">Invoice CC</label>

                    <input type="text" name="invoice_cc" class="form-control" 

                           placeholder="email1@example.com; email2@example.com">

                    <small class="text-muted">Use semicolon (;) to separate multiple emails</small>

                </div>

            </div>

            {{-- ✅ Support --}}

            <h5 class="text-secondary mt-3">Technical Support</h5>



            <div class="row">

                <div class="col-md-4">
                    <input type="text" name="support_spoc_name" class="form-control mb-2" placeholder="SPOC Name">
                </div>

                <div class="col-md-4">
                    <input type="text" name="support_spoc_mobile" class="form-control mb-2" placeholder="Mobile Number">
                </div>

                <div class="col-md-4">
                    <input type="email" name="support_spoc_email" id="support_spoc_email" class="form-control mb-2" placeholder="Email">
                </div>
                
            </div>



            <input type="hidden" name="status" value="Active">

            <button type="submit" class="btn btn-success mt-3">Save Client</button>

            <a href="{{ route('clients.index') }}" class="btn btn-secondary mt-3">Cancel</a>

        </form>

    </div>

</div>



{{-- ✅ GST Fetch JS --}}

<script>

// GST Fetch (only if PAN field exists)
function fetchGST() {
    const panInput = document.getElementById("pan_number");
    const gstStatus = document.getElementById("gstStatus");
    if (!panInput || !gstStatus) {
        return;
    }

    const pan = panInput.value.trim();
    if (pan.length !== 10) {
        gstStatus.innerHTML = "⚠️ Enter valid PAN";
        return;
    }

    gstStatus.innerHTML = "⏳ Fetching GST details...";

    fetch(`/api/gst/fetch/${pan}`)
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
                gstStatus.innerHTML = "❌ GST Not Found for this PAN";
            }
        })
        .catch(() => gstStatus.innerHTML = "⚠ Server Error");
}

const panInput = document.getElementById("pan_number");
if (panInput) {
    panInput.addEventListener("blur", fetchGST);
}


// ⭐ Password Send Btn
const sendPwdBtn = document.getElementById("sendPwdBtn");
if (sendPwdBtn) {
    sendPwdBtn.addEventListener("click", function () {
    let email = document.getElementById("support_spoc_email").value;
    let userName = document.getElementById("user_name").value;
    let pwdStatus = document.getElementById("pwdStatus");

    if (!email) {
        pwdStatus.innerHTML = "⚠️ Enter Technical support Email first!";
        setTimeout(() => { pwdStatus.innerHTML = ""; }, 4000);
        return;
    }

    if (!userName) {
        pwdStatus.innerHTML = "⚠️ Enter Username!";
        setTimeout(() => { pwdStatus.innerHTML = ""; }, 4000);
        return;
    }

    pwdStatus.innerHTML = "⏳ Sending password...";

    fetch("/api/client/send-password", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": '{{ csrf_token() }}'
        },
        body: JSON.stringify({ email: email, user_name: userName })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            pwdStatus.innerHTML = "✔ Password sent!";
        } else {
            pwdStatus.innerHTML = "❌ " + data.message;
        }
        setTimeout(() => { pwdStatus.innerHTML = ""; }, 4000);
    })
    .catch(() => {
        pwdStatus.innerHTML = "⚠ Server error";
        setTimeout(() => { pwdStatus.innerHTML = ""; }, 4000);
    });
    });
}

// ⭐ Office Type Toggle
document.getElementById('office_type').addEventListener('change', function () {
    let type = this.value;
    let panDiv = document.getElementById('panDiv');
    let headOfficeDiv = document.getElementById('headOfficeDiv');
    let userName = document.getElementById('user_name');
    let displayName = document.getElementById('business_display_name');
    let panNumber = document.getElementById('pan_number');
    let clientCodeDiv = document.getElementById('clientCodeDiv');

    if (type === 'head') {
        // Show PAN, Client Code
        panDiv.classList.remove('d-none');
        headOfficeDiv.classList.add('d-none');
        clientCodeDiv.classList.remove('d-none');

        // Mandatory
        userName.required = true;
        displayName.required = true;
        panNumber.required = true;
    } else if (type === 'branch') {
        // Hide PAN, Client Code
        panDiv.classList.add('d-none');
        headOfficeDiv.classList.remove('d-none');
        clientCodeDiv.classList.add('d-none');

        // Remove mandatory
        userName.required = false;
        displayName.required = false;
        panNumber.required = false;
    } else {
        // Default: show all
        panDiv.classList.remove('d-none');
        headOfficeDiv.classList.add('d-none');
        clientCodeDiv.classList.remove('d-none');
        userName.required = false;
        displayName.required = false;
        panNumber.required = false;
    }
});

// ⭐ Auto-fill Basic Details from Head Office
document.querySelector('[name="head_office_id"]').addEventListener('change', function () {
    let headOfficeId = this.value;
    if (!headOfficeId) return;

    fetch(`/api/clients/head-office/${headOfficeId}`)
        .then(res => res.json())
        .then(data => {
            document.querySelector('[name="client_name"]').value = data.client_name;
            document.querySelector('[name="short_name"]').value = data.short_name;
            document.querySelector('[name="business_display_name"]').value = data.business_display_name;
        });
});


</script>


{{-- ✅ GSTIN by PAN Fetch Script --}}
<script src="{{ asset('js/gstin-fetch.js') }}"></script>

@endsection

