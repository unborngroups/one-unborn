
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="text-primary mb-3">💰 Tax & Invoice Settings</h3>

    {{-- ✅ Success Message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('tax.invoice.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Invoice Prefix</label>
                <input type="text" name="invoice_prefix" class="form-control"
                    value="{{ old('invoice_prefix', $setting->invoice_prefix ?? '') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Starting Invoice Number</label>
                <input type="number" name="invoice_start_no" class="form-control"
                    value="{{ old('invoice_start_no', $setting->invoice_start_no ?? 1) }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Tax Percentage (%)</label>
                <input type="number" name="tax_percentage" step="0.01" class="form-control"
                    value="{{ old('tax_percentage', $setting->tax_percentage ?? '') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Currency Symbol</label>
                <input type="text" name="currency_symbol" class="form-control"
                    value="{{ old('currency_symbol', $setting->currency_symbol ?? '₹') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Currency Code</label>
                <input type="text" name="currency_code" class="form-control"
                    value="{{ old('currency_code', $setting->currency_code ?? 'INR') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Billing Terms</label>
                <input type="text" name="billing_terms" class="form-control"
                    value="{{ old('billing_terms', $setting->billing_terms ?? 'Net 30 days') }}">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
</div>

@endsection
