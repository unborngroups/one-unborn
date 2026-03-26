@extends('layouts.app')

@section('content')

<div class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-primary">Purchase Invoice Details</h4>

    <div>
        <a href="{{ route('finance.purchases.print', $purchase->id) }}" class="btn btn-outline-secondary">
            🖨 Print
        </a>

        <a href="{{ route('finance.purchases.edit', $purchase->id) }}" class="btn btn-warning text-white">
            ✏ Edit
        </a>

        <a href="{{ route('finance.purchases.index') }}" class="btn btn-dark">
            Back
        </a>
    </div>
</div>

<div class="card shadow-lg border-0">
    <div class="card-body">

        <div class="row mb-3">

            <div class="col-md-4">
                <label class="text-muted">Vendor</label>
                <p class="fw-semibold">{{ $purchase->vendor->vendor_name ?? '-' }}</p>
            </div>

            <div class="col-md-4">
                <label class="text-muted">Deliverable</label>
                <p class="fw-semibold">
                    {{ $purchase->deliverable->id ?? '-' }}
                    <span class="badge bg-info text-dark">
                        PO: {{ $purchase->deliverable->purchase_order_id ?? '-' }}
                    </span>
                </p>
            </div>

            <div class="col-md-4">
                <label class="text-muted">Invoice Number</label>
                <p class="fw-semibold text-primary">{{ $purchase->invoice_no }}</p>
            </div>

            <div class="col-md-4">
                <label class="text-muted">Invoice Date</label>
                <p>{{ $purchase->invoice_date ?? '-' }}</p>
            </div>

            <div class="col-md-4">
                <label class="text-muted">Status</label>

                @if($purchase->status == 'higher')
                    <span class="badge bg-danger">Higher</span>
                @elseif($purchase->status == 'lower')
                    <span class="badge bg-warning text-dark">Lower</span>
                @else
                    <span class="badge bg-success">Matched</span>
                @endif
            </div>

        </div>

        <hr>

        {{-- ITEMS --}}
        <h5 class="mb-3 text-secondary">Items</h5>

        <table class="table table-hover table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                @forelse($purchase->items as $item)
                    <tr>
                        <td>{{ $item->item->item_name ?? '-' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>₹ {{ number_format($item->price, 2) }}</td>
                        <td class="fw-semibold">₹ {{ number_format($item->total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No items found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <hr>

        {{-- TOTAL --}}
        <div class="text-end">
            <h4 class="fw-bold text-success">
                Total: ₹ {{ number_format($purchase->total_amount, 2) }}
            </h4>
        </div>

        {{-- FILE --}}
        <div class="mt-3">
            <label class="text-muted">Invoice File</label><br>

            @if($purchase->po_invoice_file)
                <a href="{{ asset('images/poinvoice_files/'.$purchase->po_invoice_file) }}"
                   target="_blank"
                   class="btn btn-outline-primary btn-sm mt-1">
                   📄 View Invoice
                </a>
            @else
                <p class="text-muted">No file uploaded</p>
            @endif
        </div>

    </div>
</div>

</div>

@endsection