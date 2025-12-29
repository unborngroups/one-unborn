@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4">
        <i class="bi bi-receipt"></i> GST Settings
    </h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('finance.settings.gst.update') }}">
        @csrf

        {{-- Enable GST --}}
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" name="gst_enabled"
                   {{ $gst->gst_enabled ? 'checked' : '' }}>
            <label class="form-check-label fw-bold">Enable GST</label>
        </div>

        {{-- GST Number --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">GST Number</label>
                <input type="text" name="gst_number" class="form-control"
                       value="{{ $gst->gst_number }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">State Code</label>
                <input type="text" name="state_code" class="form-control"
                       value="{{ $gst->state_code }}">
            </div>
        </div>

        {{-- GST Rates --}}
        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label">CGST %</label>
                <input type="number" step="0.01" name="cgst_rate"
                       class="form-control" value="{{ $gst->cgst_rate }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">SGST %</label>
                <input type="number" step="0.01" name="sgst_rate"
                       class="form-control" value="{{ $gst->sgst_rate }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">IGST %</label>
                <input type="number" step="0.01" name="igst_rate"
                       class="form-control" value="{{ $gst->igst_rate }}">
            </div>
        </div>

        {{-- Calculation Type --}}
        <div class="mb-4">
            <label class="form-label fw-bold">GST Calculation Type</label>
            <select name="calculation_type" class="form-select">
                <option value="exclusive" {{ $gst->calculation_type == 'exclusive' ? 'selected' : '' }}>
                    Exclusive
                </option>
                <option value="inclusive" {{ $gst->calculation_type == 'inclusive' ? 'selected' : '' }}>
                    Inclusive
                </option>
            </select>
        </div>

        <button class="btn btn-primary">
            <i class="bi bi-save"></i> Save GST Settings
        </button>
    </form>
</div>
@endsection
