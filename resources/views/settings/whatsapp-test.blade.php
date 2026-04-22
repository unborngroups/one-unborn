@extends('layouts.app')

@section('content')

<div class="container py-4">
    <h3 class="mb-4 text-primary"><i class="bi bi-whatsapp"></i> Test WhatsApp Message</h3>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Error Message --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif



    <div class="card shadow border-0 p-4">
        <form action="{{ route('settings.whatsapp.test.send') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-bold">Mobile Number <span class="text-danger">*</span></label>
                <input type="text" 
                       name="mobile" 
                       class="form-control" 
                       placeholder="919876543210" 
                       required
                       pattern="[0-9]{10,12}"
                       title="Enter 10-12 digit mobile number with country code">
                <small class="text-muted">Enter with country code (e.g., 91xxxxxxxxxx)</small>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Message <span class="text-danger">*</span></label>
                <textarea name="message" class="form-control" rows="5" required placeholder="Enter your test message here..."></textarea>
                <!-- <small class="text-muted">Message will be URL encoded automatically</small> -->
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success px-4">
                    <i class="bi bi-send"></i> Send Test Message
                </button>
                <a href="{{ route('settings.whatsapp') }}" class="btn btn-secondary px-4">
                    <i class="bi bi-arrow-left"></i> Back to Settings
                </a>
            </div>
        </form>
    </div>

    {{-- API Response Section --}}
    @if (session('api_response'))
        <div class="card shadow border-0 p-4 mt-4">
            <h5 class="text-info"><i class="bi bi-code-square"></i> API Response</h5>
            <pre class="bg-light p-3 rounded"><code>{{ session('api_response') }}</code></pre>
        </div>
    @endif
</div>

@endsection
