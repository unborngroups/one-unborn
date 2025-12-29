@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4">
        <i class="bi bi-percent"></i> TDS Settings
    </h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('finance.settings.tds.update') }}">
        @csrf

        {{-- Enable TDS --}}
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" name="tds_enabled"
                   {{ $tds->tds_enabled ? 'checked' : '' }}>
            <label class="form-check-label fw-bold">Enable TDS</label>
        </div>

        {{-- Section --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">TDS Section</label>
                <input type="text" name="section" class="form-control"
                       placeholder="194C / 194J"
                       value="{{ $tds->section }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">TDS Rate (%)</label>
                <input type="number" step="0.01" name="tds_rate"
                       class="form-control"
                       value="{{ $tds->tds_rate }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">Threshold Amount</label>
                <input type="number" step="0.01" name="threshold_amount"
                       class="form-control"
                       value="{{ $tds->threshold_amount }}">
            </div>
        </div>

        {{-- Deduction On --}}
        <div class="mb-4">
            <label class="form-label fw-bold">Deduct TDS On</label>
            <select name="deduction_on" class="form-select">
                <option value="payment" {{ $tds->deduction_on == 'payment' ? 'selected' : '' }}>
                    Payment
                </option>
                <option value="invoice" {{ $tds->deduction_on == 'invoice' ? 'selected' : '' }}>
                    Invoice
                </option>
            </select>
        </div>

        <button class="btn btn-primary">
            <i class="bi bi-save"></i> Save TDS Settings
        </button>
    </form>
</div>
@endsection
