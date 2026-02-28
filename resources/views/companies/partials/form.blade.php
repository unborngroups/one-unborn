<div class="row">

    {{-- ✅ Company Logo --}}

    <div class="col-md-4 mb-3">

        <label class="form-label">Company Logo</label>

        <input type="file" name="company_logo" class="form-control">

        @if(!empty($company->company_logo))

            <img src="{{ asset('images/companylogos/' . $company->company_logo) }}" class="mt-2 border rounded" width="80">

        @endif

    </div>



{{-- ✅User Name --}}


    <div class="col-md-4 mb-3">

        <label class="form-label">User Name <span class="text-danger">*</span></label>

        <input type="text" name="user_name" class="form-control"

            value="{{ old('user_name', $company->user_name ?? '') }}" required>
    </div>

    {{-- ✅ Trade / Brand Name --}}

    <div class="col-md-4 mb-3">

        <label class="form-label">Trade / Brand Name <span class="text-danger">*</span></label>

        <input type="text" name="trade_name" class="form-control"

            value="{{ old('trade_name', $company->trade_name ?? '') }}" required>

    </div>



    {{-- ✅ Company Name --}}

    <div class="col-md-4 mb-3">

        <label class="form-label">Company Name <span class="text-danger">*</span></label>

        <input type="text" name="company_name" id="company_name" class="form-control"

            value="{{ old('company_name', $company->company_name ?? '') }}" required>

    </div>



    {{-- ✅ CIN / LLPIN --}}

    <div class="col-md-4 mb-3">

        <label class="form-label">Business Number (CIN / LLPIN)</label>

        <input type="text" name="business_number" class="form-control"

            value="{{ old('business_number', $company->business_number ?? '') }}">

    </div>



    {{--Company Phone--}}

    <div class="col-md-4 mb-3">

    <label class="form-label">Company Phone</label>

    <input type="text" name="company_phone" class="form-control"

        value="{{ old('company_phone', $company->company_phone ?? '') }}">

</div>

{{--Company Email 1--}}

    <div class="col-md-4 mb-3">

    <label class="form-label">Company Email (Primary) <span class="text-danger">*</span></label>

    <input type="email" name="company_email" id="email_1" class="form-control"

        value="{{ old('company_email', $company->company_email ?? '') }}" required>

</div>

<!-- {{--Company Email 2--}}

<div class="col-md-6 mb-3">

    <label class="form-label">Company Email (Secondary)</label>

    <input type="email" name="email_2" id="email_2" class="form-control"

        value="{{ old('email_2', $company->email_2 ?? '') }}">

</div> -->

{{--Alternative Contact number--}}

<div class="col-md-4 mb-3">

    <label class="form-label">Alternative Contact Number</label>

    <input type="text" name="alternative_contact_number" class="form-control"

        value="{{ old('alternative_contact_number', $company->alternative_contact_number ?? '') }}">

</div>



    {{-- ✅ GST No (Auto Fetch Details) --}}

    <div class="col-md-4 mb-3">

        <label class="form-label">GST Number</label>

        <input type="text" name="gstin" id="gst_no" class="form-control"

            value="{{ old('gstin', $company->gstin ?? '') }}">

        <!-- <small class="text-muted">Fetch details from GST API automatically</small>

        <button type="button" id="fetch_gst_btn" class="btn btn-sm btn-primary mt-2">

  Fetch details from GST

</button> -->



    </div>



    {{-- ✅ Address (Auto from GST or Manual) --}}

    <div class="col-md-4 mb-3">

        <label class="form-label">Address</label>

        <textarea name="address" id="address" class="form-control" rows="1">{{ old('address', $company->address ?? '') }}</textarea>

        <small class="text-muted">If GST not available, enter address manually</small>

    </div>



    {{-- ✅ Website --}}

    <div class="col-md-4 mb-3">

        <label class="form-label">Website</label>

        <input type="text" name="website" class="form-control"

            value="{{ old('website', $company->website ?? '') }}">

    </div>



    {{-- ✅ Branch Locations --}}

    <!-- Branch Location -->

    <div class="col-md-4">

        <label class="form-label">Branch Location</label>

        <input type="text" name="branch_location" class="form-control"

               value="{{ old('branch_location', $company->branch_location ?? '') }}"

               placeholder="Eg: Anna Nagar, Chennai">

    </div>



    <!-- Google Maps URL -->

    <div class="col-md-4">

        <label class="form-label">Google Maps URL</label>

        <input type="url" name="store_location_url" class="form-control"

               value="{{ old('store_location_url', $company->store_location_url ?? '') }}"

               placeholder="https://maps.google.com/...">

    </div>



    <!-- Google Place ID -->

    <div class="col-md-4">

        <label class="form-label">Google Place ID</label>

        <input type="text" name="google_place_id" class="form-control"

               value="{{ old('google_place_id', $company->google_place_id ?? '') }}"

               placeholder="Eg: ChIJN1t_tDeuEmsRUsoyG83frY4">

    </div>





    {{-- ✅ Social Media Links --}}

    <div class="col-md-4 mb-3">

        <label class="form-label">Instagram</label>

        <input type="text" name="instagram" class="form-control"

            value="{{ old('instagram', $company->instagram ?? '') }}">

    </div>

    <div class="col-md-4 mb-3">

        <label class="form-label">Youtube</label>

        <input type="text" name="youtube" class="form-control"

            value="{{ old('youtube', $company->youtube ?? '') }}">

    </div>

    <div class="col-md-4 mb-3">

        <label class="form-label">Facebook</label>

        <input type="text" name="facebook" class="form-control"

            value="{{ old('facebook', $company->facebook ?? '') }}">

    </div>

    <div class="col-md-4 mb-3">

        <label class="form-label">LinkedIn</label>

        <input type="text" name="linkedin" class="form-control"

            value="{{ old('linkedin', $company->linkedin ?? '') }}">

    </div>



    {{-- ✅ PAN / TAN --}}

    <div class="col-md-4 mb-3">

        <label class="form-label">PAN Number</label>

        <input type="text" name="pan_number" class="form-control"

            value="{{ old('pan_number', $company->pan_number ?? '') }}">

    </div>

    <!-- <div class="col-md-6 mb-3">

        <label class="form-label">TAN Number</label>

        <input type="text" name="tan_number" class="form-control"

            value="{{ old('tan_number', $company->tan_number ?? '') }}">

    </div> -->



    {{-- ✅ Bank Details --}}

    <div class="col-md-4 mb-3">

        <label class="form-label">Bank Name</label>

        <input type="text" name="bank_name" class="form-control"

            value="{{ old('bank_name', $company->bank_name ?? '') }}">

    </div>

    <div class="col-md-4 mb-3">

        <label class="form-label">Branch Name</label>

        <input type="text" name="branch_name" class="form-control"

            value="{{ old('branch_name', $company->branch_name ?? '') }}">

    </div>

    <div class="col-md-4 mb-3">

        <label class="form-label">Account Number</label>

        <input type="text" name="account_number" class="form-control"

            value="{{ old('account_number', $company->account_number ?? '') }}">

    </div>

    <div class="col-md-4 mb-3">

        <label class="form-label">IFSC Code</label>

        <input type="text" name="ifsc_code" id="ifsc_code" class="form-control"

            value="{{ old('ifsc_code', $company->ifsc_code ?? '') }}">

    </div>







    {{-- ✅ UPI Details --}}

    <div class="col-md-4 mb-3">

        <label class="form-label">UPI ID</label>

        <input type="text" name="upi_id" class="form-control"

            value="{{ old('upi_id', $company->upi_id ?? '') }}">

        <small class="text-muted">Used for generating Dynamic QR codes</small>

    </div>



    <div class="col-md-4 mb-3">

    <label class="form-label">UPI Number</label>

    <input type="text" name="upi_number" class="form-control"

        value="{{ old('upi_number', $company->upi_number ?? '') }}">

   </div>



    <div class="col-md-4 mb-3">

        <label class="form-label">Opening Balance</label>

        <input type="number" name="opening_balance" class="form-control"

            value="{{ old('opening_balance', $company->opening_balance ?? '') }}">

    </div>



    {{-- ✅ Billing Logo --}}

    <div class="col-md-4 mb-3">

        <label class="form-label">Billing Logo</label>

        <input type="file" name="billing_logo" class="form-control">

        @if(!empty($company->billing_logo))

            <img src="{{ asset('images/logos/' . $company->billing_logo) }}" class="mt-2 border rounded" width="80">

        @endif

    </div>



    {{-- ✅ Normal Sign --}}

    <div class="col-md-4 mb-3">

        <label class="form-label">Normal Sign</label>

        <input type="file" name="billing_sign_normal" class="form-control">

        @if(!empty($company->billing_sign_normal))

            <img src="{{ asset('images/n_signs/' . $company->billing_sign_normal) }}" class="mt-2 border rounded" width="80">

        @endif

    </div>



    {{-- ✅ Digital Sign --}}

    <div class="col-md-4 mb-3">

        <label class="form-label">Digital Sign</label>

        <input type="file" name="billing_sign_digital" class="form-control">

        @if(!empty($company->billing_sign_digital))

            <img src="{{ asset('images/d_signs/' . $company->billing_sign_digital) }}" class="mt-2 border rounded" width="80">

        @endif

    </div>



    {{-- ✅ Status --}}

    <!-- <div class="col-md-3 mb-3">

        <label class="form-label">Status</label>

        <select name="status" class="form-select" required>

            <option value="Active" {{ old('status', $company->status ?? '') == 'Active' ? 'selected' : '' }}>Active</option>

            <option value="Inactive" {{ old('status', $company->status ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>

        </select>

    </div> -->

     {{--  Status Dropdown --}}

            <input type="hidden" name="status" value="Active">



</div>



{{-- ✅ Script for Auto Fetch --}}

<script>

// ✅ Fetch GST Details



document.getElementById('fetch_gst_btn').addEventListener('click', async () => {

    const gst = document.getElementById('gst_no').value.trim();

    if (!gst) {

        alert('Please enter GST number');

        return;

    }



    // Show loading state

    const btn = document.getElementById('fetch_gst_btn');

    const originalText = btn.innerHTML;

    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Fetching...';

    btn.disabled = true;



    try {

        const response = await fetch(`/api/fetch-gst/${gst}`);

        const data = await response.json();



        console.log('GST API Response:', data); // Debug log



        if (!response.ok) {

            throw new Error(data.error || `HTTP ${response.status}`);

        }



        // Check if we got valid GST data

        if (data.error) {

            throw new Error(data.error);

        }



        // ✅ Auto-fill fields from real GST data

        const addr = data.pradr?.addr || {};



        if (data.trade_name || data.legal_name) {

            document.getElementById('company_name').value = data.trade_name || data.legal_name || '';

        }

        

        // Build address from GST data

        const addressParts = [

            addr.bno || '',

            addr.st || '', 

            addr.loc || '',

            addr.dst || ''

        ].filter(part => part.trim() !== '');

        

        if (addressParts.length > 0) {

            document.getElementById('address').value = addressParts.join(', ');

        }



        if (addr.pncd) {

            document.getElementById('pincode').value = addr.pncd;

        }



        // ✅ Fetch pincode details if available

        if (addr.pncd) {

            try {

                const pinResponse = await fetch(`/api/pincode/lookup`, {

                    method: 'POST',

                    headers: {

                        'Content-Type': 'application/json',

                        'Accept': 'application/json',

                        'X-Requested-With': 'XMLHttpRequest'

                    },

                    body: JSON.stringify({ pincode: addr.pncd })

                });



                if (pinResponse.ok) {

                    const pinData = await pinResponse.json();

                    if (!pinData.error) {

                        document.getElementById('district').value = pinData.district || addr.dst || '';

                        document.getElementById('state').value = pinData.state || addr.stcd || '';

                        document.getElementById('area').value = pinData.post_office || addr.loc || '';

                    }

                }

            } catch (pinError) {

                console.warn('Pincode lookup failed:', pinError);

                // Fill from GST data as fallback

                document.getElementById('district').value = addr.dst || '';

                document.getElementById('state').value = addr.stcd || '';

            }

        }



        // Show success message

        alert('✅ GST details fetched successfully!\n\nCompany: ' + (data.trade_name || data.legal_name || 'Unknown') + '\n\nPlease verify and complete remaining fields.');



    } catch (error) {

        console.error('GST fetch error:', error);

        

        // Check if it's actually a successful response with data

        if (error.message && error.message.includes('GST details not found')) {

            alert('ℹ️ GST not found in database.\n\nPlease fill the company details manually.');

        } else {

            alert('⚠️ Could not connect to GST service.\n\nPlease fill the company details manually or try again later.');

        }

    } finally {

        // Reset button state

        btn.innerHTML = originalText;

        btn.disabled = false;

    }

});

</script>

