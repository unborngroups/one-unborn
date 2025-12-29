@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Add Vendor Invoice</h4>

    <form method="POST" action="{{ route('finance.vendor-invoices.store') }}">
        @csrf

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Vendor</label>
                <select name="vendor_id" class="form-control" required>
                    <option value="">Select vendor</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                            {{ $vendor->vendor_name }} ({{ $vendor->gstin }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Invoice No</label>
                <input type="text" name="invoice_no" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label>Invoice Date</label>
                <input type="date" name="invoice_date" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="Pending">Pending</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Subtotal</label>
                <input type="number" step="0.01" name="subtotal" class="form-control">
            </div>

            <div class="col-md-4">
                <label>GST Amount</label>
                <input type="number" step="0.01" name="gst_amount" class="form-control">
            </div>

            <div class="col-md-4">
                <label>Total Amount</label>
                <input type="number" step="0.01" name="total_amount" id="vendor-invoice-total" class="form-control" readonly>
            </div>
        </div>

        <button class="btn btn-success">Save Invoice</button>
        <a href="{{ route('finance.vendor-invoices.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var subtotal = document.querySelector('[name="subtotal"]');
        var gstAmount = document.querySelector('[name="gst_amount"]');
        var total = document.getElementById('vendor-invoice-total');

        function updateTotal() {
            var subValue = parseFloat(subtotal.value) || 0;
            var gstValue = parseFloat(gstAmount.value) || 0;
            total.value = (subValue + gstValue).toFixed(2);
        }

        subtotal.addEventListener('input', updateTotal);
        gstAmount.addEventListener('input', updateTotal);
        updateTotal();
    });
</script>
@endsection
