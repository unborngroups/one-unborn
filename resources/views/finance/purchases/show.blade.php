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
                <p class="fw-semibold">{{ $displayVendorName ?: (optional($purchase->vendor)->vendor_name ?? $vendorFromMaster ?? $purchase->vendor_name ?? $purchase->vendor_name_raw ?? '-') }}</p>
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
                <p class="fw-semibold text-primary">{{ $displayInvoiceNo ?: $purchase->invoice_no ?: '-' }}</p>
            </div>

            <div class="col-md-4">
                <label class="text-muted">Invoice Date</label>
                <p>{{ $displayInvoiceDate ? \Carbon\Carbon::parse($displayInvoiceDate)->format('d-m-Y') : '-' }}</p>
            </div>

            <div class="col-md-4">
                <label class="text-muted">GSTIN</label>
                <p>{{ $displayGstin ?: '-' }}</p>
            </div>

            <div class="col-md-4">
                <label class="text-muted">Status</label>

                <span class="badge 
                    @switch($displayStatus)
                        @case('draft') bg-secondary @break
                        @case('needs_review') bg-warning text-dark @break
                        @case('verified') bg-info @break
                        @case('approved') bg-primary @break
                        @case('paid') bg-dark @break
                        @case('failed') bg-danger @break
                        @case('duplicate') bg-danger @break
                        @case('higher') bg-danger @break
                        @case('lower') bg-warning text-dark @break
                        @default bg-success
                    @endswitch">
                    {{ ucfirst(str_replace('_', ' ', $displayStatus)) }}
                </span>
            </div>

            <div class="col-md-4">
                <label class="text-muted">Accuracy</label>
                <p>{{ !is_null($displayAccuracy) ? rtrim(rtrim(number_format((float) $displayAccuracy, 2), '0'), '.') . '%' : '-' }}</p>
            </div>

            @php
                $importFailureReason = data_get($purchase->raw_json, 'import_failure_reason');
            @endphp
            @if(!empty($importFailureReason))
                <div class="col-md-12">
                    <div class="alert alert-danger py-2 mb-0">
                        <strong>Import Alert:</strong> {{ $importFailureReason }}
                    </div>
                </div>
            @endif

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
                @php
                    $showRows = $purchase->items->isNotEmpty()
                        ? $purchase->items->map(function ($item) {
                            return [
                                'item_name' => $item->item->item_description ?? $item->item->item_name ?? '-',
                                'quantity' => $item->quantity,
                                'price' => $item->price,
                                'total' => $item->total,
                            ];
                        })->toArray()
                        : array_map(function ($row) {
                            $quantity = (float) ($row['quantity'] ?? 1);
                            $price = (float) ($row['price'] ?? 0);
                            return [
                                'item_name' => $row['item_label'] ?? 'Invoice Item',
                                'quantity' => $quantity,
                                'price' => $price,
                                'total' => $quantity * $price,
                            ];
                        }, $prefillRows ?? []);
                @endphp
                @forelse($showRows as $item)
                    <tr>
                        <td>{{ $item['item_name'] ?? '-' }}</td>
                        <td>{{ $item['quantity'] ?? 0 }}</td>
                        <td>₹ {{ number_format((float) ($item['price'] ?? 0), 2) }}</td>
                        <td class="fw-semibold">₹ {{ number_format((float) ($item['total'] ?? 0), 2) }}</td>
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
                Total: ₹ {{ number_format((float) ($displayGrandTotal ?? $purchase->total_amount ?? 0), 2) }}
            </h4>
        </div>

        {{-- FILE --}}
        <div class="mt-3">
            <label class="text-muted">Invoice File</label><br>

            @if($purchase->po_invoice_file)
                <a href="{{ route('finance.purchases.download-source-pdf', $purchase->id) }}"
                   download
                   class="btn btn-outline-primary btn-sm mt-1">
                   <i class="bi bi-download"></i> Download Source File
                </a>
            @else
                <p class="text-muted">No file uploaded</p>
            @endif
        </div>

    </div>
</div>

</div>

@endsection