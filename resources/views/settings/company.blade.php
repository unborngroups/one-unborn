@extends('layouts.app')



@section('content')

<div class="container py-4">

    <h3 class="text-primary mb-3">🏢 Company Settings</h3>



    {{-- ✅ Success Message --}}

    @if (session('success'))

        <div class="alert alert-success alert-dismissible fade show" role="alert">

            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

        </div>

    @endif
    
  {{-- ✅ Show Validation Errors --}}

        @if ($errors->any())

            <div class="alert alert-danger">

                <ul class="mb-0">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif


    {{-- ✅ Company Settings Update Form --}}

    <form action="{{ route('company.settings.update') }}" method="POST" enctype="multipart/form-data">

        @csrf

        @method('PUT')



        <div class="row">

            <div class="col-md-6 mb-3">

                <label>Company Name *</label>

                <input type="text" name="company_name" class="form-control"

                       value="{{ old('company_name', $company->company_name ?? '') }}">

            </div>



            {{-- Company Email --}}

            <div class="col-md-6 mb-3">

                <label>Company Email</label>

                <input type="email" name="company_email" class="form-control"

                       value="{{ old('company_email', $company->company_email ?? '') }}">

            </div>

            


            {{-- Contact Number --}}

            <div class="col-md-6 mb-3">

                <label>Contact Number</label>

                <input type="text" name="contact_no" class="form-control"

                       value="{{ old('contact_no', $company->contact_no ?? '') }}">

            </div>



             {{-- Website --}}

            <div class="col-md-6 mb-3">

                <label>Website</label>

                <input type="text" name="website" class="form-control"

                       value="{{ old('website', $company->website ?? '') }}">

            </div>



            {{-- GST Number --}}

            <div class="col-md-6 mb-3">

                <label>GST Number</label>

                <input type="text" name="gst_number" class="form-control"

                       value="{{ old('gst_number', $company->gst_number ?? '') }}">

            </div>



             {{-- Company Logo Upload --}}

            <div class="col-md-6 mb-3">

                <label>Company Logo</label>

                <input type="file" name="company_logo" class="form-control">

                @if(!empty($company->company_logo))

                    <img src="{{ asset('storage/' . $company->company_logo) }}" class="mt-2" width="100">

                @endif

            </div>



            {{-- Company Address --}}

            <div class="col-md-12 mb-3">

                <label>Address</label>

                <textarea name="address" class="form-control">{{ old('address', $company->address ?? '') }}</textarea>

            </div>

        </div>

        {{-- ===================================== --}}
        {{-- ✉️ EMAIL SETTINGS SECTION --}}
        {{-- ===================================== --}}
        <hr>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-envelope-at me-2"></i>Email Settings (Main SMTP)</h5>
            </div>
            <div class="card-body bg-light">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">SMTP Server Address *</label>
                        <input type="text" name="mail_host" class="form-control" value="{{ old('mail_host', $company->mail_host ?? '') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">SMTP Username *</label>
                        <input type="text" name="mail_username" class="form-control" value="{{ old('mail_username', $company->mail_username ?? '') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">SMTP Password *</label>
                        <input type="password" name="mail_password" class="form-control" value="{{ old('mail_password', $company->mail_password ?? '') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">SMTP Port *</label>
                        <input type="text" name="mail_port" class="form-control" value="{{ old('mail_port', $company->mail_port ?? '') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Encryption Type</label>
                        <select name="mail_encryption" class="form-select">
                            <option value="">Select Encryption Type</option>
                            <option value="ssl" {{ old('mail_encryption', $company->mail_encryption ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="tls" {{ old('mail_encryption', $company->mail_encryption ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Mail From Name *</label>
                        <input type="text" name="mail_from_name" class="form-control" value="{{ old('mail_from_name', $company->mail_from_name ?? '') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Mail From Address *</label>
                        <input type="email" name="mail_from_address" class="form-control" value="{{ old('mail_from_address', $company->mail_from_address ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mail Footer Text</label>
                        <input type="text" name="mail_footer" class="form-control" value="{{ old('mail_footer', $company->mail_footer ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mail Signature</label>
                        <input type="text" name="mail_signature" class="form-control" value="{{ old('mail_signature', $company->mail_signature ?? '') }}">
                    </div>
                </div>
            </div>
        </div>

{{-- ===================================== --}}
        {{-- ✉️ GENERAL SETTINGS SECTION --}}
        {{-- ===================================== --}}
         <hr>


<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0"><i class="bi bi-bell me-2"></i>General Notification</h5>
    </div>
    <div class="card-body bg-light">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">SMTP Server Address *</label>
                <input type="text" name="general_mail_host" class="form-control" value="{{ old('general_mail_host', $company->general_mail_host ?? '') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">SMTP Username *</label>
                <input type="text" name="general_mail_username" class="form-control" value="{{ old('general_mail_username', $company->general_mail_username ?? '') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">SMTP Password *</label>
                <input type="password" name="general_mail_password" class="form-control" value="{{ old('general_mail_password', $company->general_mail_password ?? '') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">SMTP Port *</label>
                <input type="text" name="general_mail_port" class="form-control" value="{{ old('general_mail_port', $company->general_mail_port ?? '') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Encryption Type</label>
                <select name="general_mail_encryption" class="form-select">
                    <option value="">Select Encryption Type</option>
                    <option value="ssl" {{ old('general_mail_encryption', $company->general_mail_encryption ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                    <option value="tls" {{ old('general_mail_encryption', $company->general_mail_encryption ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Mail From Name *</label>
                <input type="text" name="general_mail_from_name" class="form-control" value="{{ old('general_mail_from_name', $company->general_mail_from_name ?? '') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Mail From Address *</label>
                <input type="email" name="general_mail_from_address" class="form-control" value="{{ old('general_mail_from_address', $company->general_mail_from_address ?? '') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Mail Footer Text</label>
                <input type="text" name="general_mail_footer" class="form-control" value="{{ old('general_mail_footer', $company->general_mail_footer ?? '') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Mail Signature</label>
                <input type="text" name="general_mail_signature" class="form-control" value="{{ old('general_mail_signature', $company->general_mail_signature ?? '') }}">
            </div>
        </div>
    </div>
</div>

{{-- ===================================== --}}
{{-- 🚚 DELIVERY NOTIFICATION SECTION --}}
{{-- ===================================== --}}
<hr>
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Delivery Notification</h5>
        <input type="hidden" name="delivery_email_check" value="0">

<input type="checkbox"
       name="delivery_email_check"
       value="1"
       class="form-check-input"
       id="deliveryCheckbox"
       {{ old('delivery_email_check', $company->delivery_email_check ?? false) ? 'checked' : '' }}>
<label for="deliveryCheckbox" class="form-check-label">
    Enable Delivery Notification Emails
</label>

    </div>
    <div class="card-body bg-light">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">SMTP Server Address *</label>
                <input type="text" name="delivery_mail_host" class="form-control" value="{{ old('delivery_mail_host', $company->delivery_mail_host ?? '') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">SMTP Username *</label>
                <input type="text" name="delivery_mail_username" class="form-control" value="{{ old('delivery_mail_username', $company->delivery_mail_username ?? '') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">SMTP Password *</label>
                <input type="password" name="delivery_mail_password" class="form-control" value="{{ old('delivery_mail_password', $company->delivery_mail_password ?? '') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">SMTP Port *</label>
                <input type="text" name="delivery_mail_port" class="form-control" value="{{ old('delivery_mail_port', $company->delivery_mail_port ?? '') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Encryption Type</label>
                <select name="delivery_mail_encryption" class="form-select">
                    <option value="">Select Encryption Type</option>
                    <option value="ssl" {{ old('delivery_mail_encryption', $company->delivery_mail_encryption ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                    <option value="tls" {{ old('delivery_mail_encryption', $company->delivery_mail_encryption ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Mail From Name *</label>
                <input type="text" name="delivery_mail_from_name" class="form-control" value="{{ old('delivery_mail_from_name', $company->delivery_mail_from_name ?? '') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Mail From Address *</label>
                <input type="email" name="delivery_mail_from_address" class="form-control" value="{{ old('delivery_mail_from_address', $company->delivery_mail_from_address ?? '') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Mail Footer Text</label>
                <input type="text" name="delivery_mail_footer" class="form-control" value="{{ old('delivery_mail_footer', $company->delivery_mail_footer ?? '') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Mail Signature</label>
                <input type="text" name="delivery_mail_signature" class="form-control" value="{{ old('delivery_mail_signature', $company->delivery_mail_signature ?? '') }}">
            </div>
        </div>
    </div>
</div>

{{-- ===================================== --}}
{{-- 🧾 INVOICE SENDING SECTION --}}
{{-- ===================================== --}}
<hr>
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Invoice Sending</h5>
    </div>
    <div class="card-body bg-light">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">SMTP Server Address *</label>
                <input type="text" name="invoice_mail_host" class="form-control" value="{{ old('invoice_mail_host', $company->invoice_mail_host ?? '') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">SMTP Username *</label>
                <input type="text" name="invoice_mail_username" class="form-control" value="{{ old('invoice_mail_username', $company->invoice_mail_username ?? '') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">SMTP Password *</label>
                <input type="password" name="invoice_mail_password" class="form-control" value="{{ old('invoice_mail_password', $company->invoice_mail_password ?? '') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">SMTP Port *</label>
                <input type="text" name="invoice_mail_port" class="form-control" value="{{ old('invoice_mail_port', $company->invoice_mail_port ?? '') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Encryption Type</label>
                <select name="invoice_mail_encryption" class="form-select">
                    <option value="">Select Encryption Type</option>
                    <option value="ssl" {{ old('invoice_mail_encryption', $company->invoice_mail_encryption ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                    <option value="tls" {{ old('invoice_mail_encryption', $company->invoice_mail_encryption ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Mail From Name *</label>
                <input type="text" name="invoice_mail_from_name" class="form-control" value="{{ old('invoice_mail_from_name', $company->invoice_mail_from_name ?? '') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Mail From Address *</label>
                <input type="email" name="invoice_mail_from_address" class="form-control" value="{{ old('invoice_mail_from_address', $company->invoice_mail_from_address ?? '') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Mail Footer Text</label>
                <input type="text" name="invoice_mail_footer" class="form-control" value="{{ old('invoice_mail_footer', $company->invoice_mail_footer ?? '') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Mail Signature</label>
                <input type="text" name="invoice_mail_signature" class="form-control" value="{{ old('invoice_mail_signature', $company->invoice_mail_signature ?? '') }}">
            </div>
        </div>
    </div>
</div>

        {{-- ===================================== --}}
        {{-- 🌐 SOCIAL MEDIA LINKS SECTION --}}
        {{-- ===================================== --}}
        <hr>

<h5 class="text-primary fw-bold mb-3">🌐 Social Media Links</h5>

<div class="row mb-3">

    {{-- LinkedIn --}}

    <div class="col-md-6">

        <label for="linkedin_url" class="form-label">LinkedIn URL</label>

        <input type="url" name="linkedin_url" class="form-control" value="{{ old('linkedin_url', $company->linkedin_url) }}">

    </div>

    {{-- WhatsApp --}}

    <div class="col-md-6">

        <label for="whatsapp_number" class="form-label">WhatsApp Number</label>

        <input type="text" name="whatsapp_number" class="form-control" value="{{ old('whatsapp_number', $company->whatsapp_number) }}">

    </div>

</div>



<div class="row mb-3">

    {{-- Facebook --}}

    <div class="col-md-6">

        <label for="facebook_url" class="form-label">Facebook URL</label>

        <input type="url" name="facebook_url" class="form-control" value="{{ old('facebook_url', $company->facebook_url) }}">

    </div>

     {{-- Instagram --}}

    <div class="col-md-6">

        <label for="instagram_url" class="form-label">Instagram URL</label>

        <input type="url" name="instagram_url" class="form-control" value="{{ old('instagram_url', $company->instagram_url) }}">

    </div>

</div>

{{-- ✅ Social Icons Preview (if links exist) --}}

@if($company->linkedin_url)

    <a href="{{ $company->linkedin_url }}" target="_blank" class="me-2">

        <i class="bi bi-linkedin text-primary fs-4"></i>

    </a>

@endif



@if($company->whatsapp_number)

    <a href="https://wa.me/{{ $company->whatsapp_number }}" target="_blank" class="me-2">

        <i class="bi bi-whatsapp text-success fs-4"></i>

    </a>

@endif

<hr>
        <h5 class="text-primary fw-bold mb-3">🔔 Feasibility Notification Settings</h5>
        <div class="row mb-3">
            <div class="col-md-6 mb-3">
                <label class="form-label">Feasibility Notification User Type (Open)</label>
                <input type="text" name="feasibility_notifications[Open]" class="form-control"
                       value="{{ old('feasibility_notifications.Open', $company->feasibility_notifications['Open'] ?? '') }}">
                <small class="text-muted">Set the user type to receive feasibility creation emails (e.g., Team OPS)</small>
            </div>
        
            <div class="col-md-6 mb-3">
                <label class="form-label">Feasibility Notification Email (Open)</label>
                <input type="email" name="feasibility_notifications[Open_email]" class="form-control"
                       value="{{ old('feasibility_notifications.Open_email', $company->feasibility_notifications['Open_email'] ?? '') }}">
                <small class="text-muted">Set the email to receive feasibility creation notifications (e.g., divya@unborn.co.in)</small>
            </div>
        </div>
        
        {{-- ============================ --}}
        {{-- ✉️ Exception Permission Email Section --}}
        {{-- ============================ --}}
        <hr>
        <h5 class="text-primary fw-bold mb-3">✉️ Exception Permission Email</h5>
        <div class="col-md-6 mb-3">

                <label>Exception Permission Email</label>

                <input type="email" name="exception_permission_email" class="form-control"

                       value="{{ old('exception_permission_email', $company->exception_permission_email ?? '') }}">

                <small class="text-muted">Exception emails from Feasibility (SM) will be sent to this address.</small>

            </div>

{{-- Submit Button --}}

        <button type="submit" class="btn btn-primary">Save Settings</button>

    </form>

</div>

@endsection

