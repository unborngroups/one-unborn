@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Add Debit Note</h4>

    <form method="POST" action="{{ route('finance.debit-notes.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Vendor Invoice</label>
            <select name="vendor_invoice_id" class="form-select">
                <option value="">Choose vendor invoice</option>
                @foreach($vendorInvoices as $invoice)
                <option value="{{ $invoice->id }}" {{ old('vendor_invoice_id') == $invoice->id ? 'selected' : '' }}>
                    {{ optional($invoice->vendor)->vendor_name ?? 'Vendor #' . $invoice->vendor_id }} - {{ $invoice->invoice_no }}
                    (₹ {{ number_format($invoice->total_amount, 2) }})
                </option>
                @endforeach
            </select>
            @error('vendor_invoice_id')
            <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Debit Note No</label>
            <input type="text" name="debit_note_no" class="form-control" value="{{ old('debit_note_no') }}">
            @error('debit_note_no')
            <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="text" name="date" class="form-control" placeholder="DD-MM-YYYY" value="{{ old('date') }}">
            @error('date')
            <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount') }}">
            @error('amount')
            <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Reason</label>
            <textarea name="reason" class="form-control" rows="3">{{ old('reason') }}</textarea>
            @error('reason')
            <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-success">Save</button>
        <a href="{{ route('finance.debit-notes.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
