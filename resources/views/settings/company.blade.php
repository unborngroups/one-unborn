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

        <!-- email -->

         <hr>

<h5 class="text-primary fw-bold mb-3">✉️ Email Settings</h5>



<div class="row">



    {{-- SMTP Server Address --}}

    <div class="col-md-6 mb-3">

        <label>SMTP Server Address *</label>

        <input type="text" name="mail_host" class="form-control"

               value="{{ old('mail_host', $company->mail_host ?? '') }}" required>

    </div>



    {{-- SMTP Username --}}

    <div class="col-md-6 mb-3">

        <label>SMTP Username *</label>

        <input type="text" name="mail_username" class="form-control"

               value="{{ old('mail_username', $company->mail_username ?? '') }}" required>

    </div>



    {{-- SMTP Password --}}

    <div class="col-md-6 mb-3">

        <label>SMTP Password *</label>

        <input type="password" name="mail_password" class="form-control"

               value="{{ old('mail_password', $company->mail_password ?? '') }}" required>

    </div>



    {{-- SMTP Port --}}

    <div class="col-md-6 mb-3">

        <label>SMTP Port *</label>

        <input type="text" name="mail_port" class="form-control"

               value="{{ old('mail_port', $company->mail_port ?? '') }}" required>

    </div>



    {{-- Encryption Type --}}

    <div class="col-md-6 mb-3">

        <label class="form-label">Encryption Type</label>

            <select name="mail_encryption" class="form-select">

                <option value="">Select Encryption Type</option>

                <option value="ssl" {{ old('mail_encryption', $company->mail_encryption ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>

                <option value="tls" {{ old('mail_encryption', $company->mail_encryption ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>

            </select>

    </div>



    {{-- Mail From Name --}}

    <div class="col-md-6 mb-3">

        <label>Mail From Name *</label>

        <input type="text" name="mail_from_name" class="form-control"

               value="{{ old('mail_from_name', $company->mail_from_name ?? '') }}" required>

    </div>



    {{-- Mail From Address --}}

    <div class="col-md-6 mb-3">

        <label>Mail From Address *</label>

        <input type="email" name="mail_from_address" class="form-control"

               value="{{ old('mail_from_address', $company->mail_from_address ?? '') }}" required>

    </div>



    {{-- Footer Text --}}

    <div class="col-md-6 mb-3">

        <label>Mail Footer Text</label>

        <input type="text" name="mail_footer" class="form-control"

               value="{{ old('mail_footer', $company->mail_footer ?? '') }}">

    </div>



    {{-- Signature --}}

    <div class="col-md-6 mb-3">

        <label>Mail Signature</label>

        <input type="text" name="mail_signature" class="form-control"

               value="{{ old('mail_signature', $company->mail_signature ?? '') }}">

    </div>


</div>

{{-- ============================ --}}
        {{-- ✉️ Exception Permission Email Section --}}

        {{-- ============================ --}}
        <hr>
        <h5>✉️ Exception Permission Email</h5>
        <div class="col-md-6 mb-3">

                <label>Exception Permission Email</label>

                <input type="email" name="exception_permission_email" class="form-control"

                       value="{{ old('exception_permission_email', $company->exception_permission_email ?? '') }}">

                <small class="text-muted">Exception emails from Feasibility (SM) will be sent to this address.</small>

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
        </div>
        <div class="row mb-3">
            <div class="col-md-6 mb-3">
                <label class="form-label">Feasibility Notification Email (Open)</label>
                <input type="email" name="feasibility_notifications[Open_email]" class="form-control"
                       value="{{ old('feasibility_notifications.Open_email', $company->feasibility_notifications['Open_email'] ?? '') }}">
                <small class="text-muted">Set the email to receive feasibility creation notifications (e.g., divya@unborn.co.in)</small>
            </div>
        </div>

{{-- Submit Button --}}

        <button type="submit" class="btn btn-primary">Save Settings</button>

    </form>

</div>

@endsection

