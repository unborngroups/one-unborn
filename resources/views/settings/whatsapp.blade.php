@extends('layouts.app')

@section('content')

<div class="container py-4">

    <h3 class="mb-4 text-primary"><i class="bi bi-whatsapp"></i> WhatsApp API Settings</h3>

    {{-- ✅ Success Message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ✅ Tab Navigation --}}
    <ul class="nav nav-tabs mb-4" id="whatsappTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="official-tab" data-bs-toggle="tab" data-bs-target="#official" type="button" role="tab">
                <i class="bi bi-shield-check"></i> Official WhatsApp API
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="unofficial-tab" data-bs-toggle="tab" data-bs-target="#unofficial" type="button" role="tab">
                <i class="bi bi-gear"></i> Unofficial WhatsApp API
            </button>
        </li>
    </ul>

    {{-- ✅ Tab Content --}}
    <div class="tab-content" id="whatsappTabsContent">
        
        {{-- 🟢 OFFICIAL WhatsApp API --}}
        <div class="tab-pane fade show active" id="official" role="tabpanel">
            <div class="card shadow border-0 p-4">
              
                <form action="{{ route('settings.whatsapp.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="api_type" value="official">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">WhatsApp Business Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="official_phone" value="{{ old('official_phone', $settings->official_phone ?? '') }}" 
                                   class="form-control" placeholder="+91XXXXXXXXXX" required>
                            <!-- <small class="text-muted">Enter with country code (e.g., +919876543210)</small> -->
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">WhatsApp Business Account ID <span class="text-danger">*</span></label>
                            <input type="text" name="official_account_id" value="{{ old('official_account_id', $settings->official_account_id ?? '') }}" 
                                   class="form-control" placeholder="Business Account ID" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-save"></i> Save Official API Settings
                    </button>
                </form>
            </div>
        </div>

        {{-- 🔵 UNOFFICIAL WhatsApp API (WaHub.pro) --}}
        <div class="tab-pane fade" id="unofficial" role="tabpanel">
            <div class="card shadow border-0 p-4">
               
                <form action="{{ route('settings.whatsapp.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="api_type" value="unofficial">

                    <div class="row mb-3">
                        <div class="col-md-6 p-4">
                            <label class="form-label fw-bold">API URL <span class="text-danger">*</span></label>
                            <input type="url"
                                   name="unofficial_api_url"
                                   value="{{ old('unofficial_api_url', $settings->unofficial_api_url ?? 'https://wahub.pro/api/send') }}"
                                   class="form-control"
                                   required readonly>
                           
                        </div>
                  
                        <div class="col-md-6 p-4">
                            <label class="form-label fw-bold">Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" name="unofficial_mobile" value="{{ old('unofficial_mobile', $settings->unofficial_mobile ?? '') }}" 
                                   class="form-control" placeholder="919876543210" required>
                        </div>

                        <div class="col-md-6 p-4">
                            <label class="form-label fw-bold">Instance ID <span class="text-danger">*</span></label>
                            <input type="text" name="unofficial_instance_id" value="{{ old('unofficial_instance_id', $settings->unofficial_instance_id ?? '691AEEF33256E') }}" 
                                   class="form-control" placeholder="691AEEF33256E" required readonly>
                        </div>

                        <div class="col-md-6 p-4">
                            <label class="form-label fw-bold">Access Token <span class="text-danger">*</span></label>
                            <input type="text" name="unofficial_access_token" value="{{ old('unofficial_access_token', $settings->unofficial_access_token ?? '68f9df1ac354c') }}" 
                                   class="form-control" placeholder="68f9df1ac354c" required readonly>
                        </div>
                    </div>

<!--                   
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="unofficial_enabled" id="unofficialEnabled" 
                               {{ ($settings->unofficial_enabled ?? false) ? 'checked' : '' }} value="1">
                        <label class="form-check-label" for="unofficialEnabled">
                            <strong>Enable Unofficial WhatsApp API</strong>
                        </label>
                    </div> -->

                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save"></i> Save Unofficial API Settings
                    </button>

                    <a href="{{ route('settings.whatsapp.test') }}" class="btn btn-info px-4 ms-2" target="_blank">
                        <i class="bi bi-send"></i> Test WhatsApp Message
                    </a>

                </form>
            </div>
        </div>

    </div>

</div>

@endsection
