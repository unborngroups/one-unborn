@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="text-primary fw-bold mb-4">
        <i class="bi bi-envelope"></i> Email Configuration - {{ $company->company_name }}
    </h3>

    <div class="card shadow border-0">
        <div class="card-body">
            <form action="{{ route('companies.save.email.config', $company->id) }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <!-- {{-- Mail Mailer --}}
    <div class="col-md-6">
        <label class="form-label fw-bold">Mail Mailer *</label>
        <input type="text" name="mail_mailer" class="form-control"
               value="{{ old('mail_mailer', $setting->mail_mailer ?? 'smtp') }}" required>
    </div> -->
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold">SMTP Server Address *</label>
                        <input type="text" name="mail_host" class="form-control" 
                               value="{{ old('mail_host', $company->mail_host ?? $setting->mail_host ?? 'smtp.gmail.com') }}" required>
                    </div>

                     {{-- SMTP Username --}}
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">SMTP Username *</label>
        <input type="text" name="mail_username" class="form-control"
               value="{{ old('mail_username', $company->mail_username ?? $setting->mail_username ?? '') }}" required>
    </div>

    {{-- SMTP Password --}}
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">SMTP Password *</label>
        <input type="password" name="mail_password" class="form-control"
               value="{{ old('mail_password', $company->mail_password ?? $setting->mail_password ?? '') }}" required>
    </div>

    {{-- SMTP Port --}}
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">SMTP Port *</label>
        <input type="text" name="mail_port" class="form-control"
               value="{{ old('mail_port', $company->mail_port ?? $setting->mail_port ?? '') }}" required>
    </div>

    {{-- Encryption Type --}}
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Encryption Type</label>
            <select name="mail_encryption" class="form-select">
                <option value="">Select Encryption Type</option>
                <option value="ssl" {{ old('mail_encryption', $company->mail_encryption ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                <option value="tls" {{ old('mail_encryption', $company->mail_encryption ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
            </select>
    </div>

     {{-- Mail From Name --}}
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Mail From Name *</label>
        <input type="text" name="mail_from_name" class="form-control"
               value="{{ old('mail_from_name', $company->mail_from_name ?? $setting->mail_from_name ?? '') }}" required>
    </div>

    {{-- Mail From Address --}}
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Mail From Address *</label>
        <input type="email" name="mail_from_address" class="form-control"
               value="{{ old('mail_from_address', $company->mail_from_address ?? $setting->mail_from_address ?? '') }}" required>
    </div>

    {{-- Footer Text --}}
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Mail Footer Text</label>
        <input type="text" name="mail_footer" class="form-control"
               value="{{ old('mail_footer', $company->mail_footer ?? $setting->mail_footer ?? '') }}">
    </div>

    {{-- Signature --}}
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Mail Signature</label>
        <input type="text" name="mail_signature" class="form-control"
               value="{{ old('mail_signature', $company->mail_signature ?? $setting->mail_signature ?? '') }}">
    </div>

                </div>

                <div class="text-end">
                    <a href="{{ route('companies.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Configuration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
