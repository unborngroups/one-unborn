@extends('layouts.app')

@section('title', 'Edit Purchase Invoice')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Edit Purchase Invoice</h4>
        <a href="{{ route('finance.purchases.index') }}" class="btn btn-dark">Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('finance.purchases.update', $purchase->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row mb-3">

                    {{-- Vendor --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Vendor</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="bi bi-building text-primary"></i></span>
                            <input type="text"
                                   name="vendor_name_raw"
                                   class="form-control"
                                   value="{{ old('vendor_name_raw', $displayVendorName) }}"
                                   placeholder="Enter vendor name"
                                   required>
                        </div>
                        @if($purchase->vendor_id)
                            <div class="small mb-2">
                                <span class="badge bg-success">Mapped</span>
                            </div>
                        @endif
                        @if(!empty($displayGstin))
                            <div class="small text-muted mb-2">
                                GSTIN: <strong>{{ $displayGstin }}</strong>
                            </div>
                        @endif
                        {{-- Dropdown to change vendor if needed --}}
                        <!-- <select name="vendor_id" id="vendor_id" class="form-select form-select-sm">
                            <option value="">-- Change Vendor (optional) --</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}"
                                        data-gstin="{{ strtoupper(trim((string) ($vendor->gstin ?? ''))) }}"
                                        {{ old('vendor_id', $purchase->vendor_id) == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->vendor_name }}
                                </option>
                            @endforeach
                        </select> -->
                    </div>

                    

                    {{-- Invoice Number --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Invoice Number</label>
                        <input type="text" id="invoice_number" name="invoice_number" class="form-control"
                               value="{{ old('invoice_number', $displayInvoiceNo) }}" required>
                    </div>

                    {{-- Invoice Date --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Invoice Date</label>
                        <input type="date" name="invoice_date" class="form-control"
                               value="{{ old('invoice_date', $displayInvoiceDate) }}">
                    </div>

                    {{-- Deliverable --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Deliverable</label>
                        <div class="form-control bg-light">
                            {{ $purchase->deliverable->id ?? '-' }}
                            @if($purchase->deliverable)
                                <span class="badge bg-info text-dark ms-2">
                                    PO: {{ $purchase->deliverable->purchase_order_id ?? '-' }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-12 mb-2">
                        <div class="d-flex flex-wrap gap-3 align-items-center small">
                            <div><strong>Status:</strong> {{ ucfirst($displayStatus) }}</div>
                            <div><strong>Accuracy:</strong> {{ !is_null($displayAccuracy) ? rtrim(rtrim(number_format((float) $displayAccuracy, 2), '0'), '.') . '%' : '-' }}</div>
                            <div><strong>Total:</strong> ₹ {{ number_format((float) ($displayGrandTotal ?? 0), 2) }}</div>
                        </div>
                    </div>

                    @php
                        $importFailureReason = data_get($purchase->raw_json, 'import_failure_reason');
                    @endphp
                    @if(!empty($importFailureReason))
                        <div class="col-md-12 mb-3">
                            <div class="alert alert-danger mb-0 py-2">
                                <strong>Import Alert:</strong> {{ $importFailureReason }}
                            </div>
                        </div>
                    @endif

                </div>

                <hr>

                @if(!empty($valueChangeHistory))
                    <div class="mb-4">
                        <h6 class="mb-2">Re-import Value Changes (Old vs New)</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 190px;">Changed At</th>
                                        <th style="width: 160px;">Field</th>
                                        <th>Old Value</th>
                                        <th>New Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($valueChangeHistory as $change)
                                        @php $changedFields = $change['changed_fields'] ?? []; @endphp
                                        @if(is_array($changedFields) && !empty($changedFields))
                                            @foreach($changedFields as $fieldName => $fieldValues)
                                                <tr>
                                                    <td>{{ $change['changed_at'] ?? '-' }}</td>
                                                    <td>{{ ucwords(str_replace('_', ' ', (string) $fieldName)) }}</td>
                                                    <td>{{ data_get($fieldValues, 'old', '-') !== null ? data_get($fieldValues, 'old') : '-' }}</td>
                                                    <td>{{ data_get($fieldValues, 'new', '-') !== null ? data_get($fieldValues, 'new') : '-' }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <h5>Items</h5>

                <div class="table-responsive">
                    <table class="table table-bordered" id="itemsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Item & Description</th>
                                <th width="120">HSN/SAC</th>
                                <th width="90">Qty</th>
                                <th width="120">Rate</th>
                                <th width="90">CGST %</th>
                                <th width="120">CGST Amt</th>
                                <th width="90">SGST %</th>
                                <th width="120">SGST Amt</th>
                                <th width="130">Amount</th>
                                <th width="80">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $existingItems = $purchase->items;
                                $autoRows = $prefillRows ?? [];

                                if (empty($autoRows) && $existingItems->isEmpty() && (float) ($displaySubTotal ?? 0) > 0) {
                                    $autoRows = [[
                                        'item_label' => 'Invoice Services',
                                        'item_id' => null,
                                        'hsn' => '',
                                        'quantity' => 1,
                                        'price' => (float) ($displaySubTotal ?? 0),
                                        'cgst_percent' => (float) (($displaySubTotal ?? 0) > 0 ? (($displayCgstTotal ?? 0) / $displaySubTotal) * 100 : 0),
                                        'sgst_percent' => (float) (($displaySubTotal ?? 0) > 0 ? (($displaySgstTotal ?? 0) / $displaySubTotal) * 100 : 0),
                                    ]];
                                }
                            @endphp
                            @if(!empty($autoRows))
                                @foreach($autoRows as $idx => $autoRow)
                                <tr>
                                    <td>
                                        <input type="text" name="items[{{ $idx }}][item_name]" class="form-control item-name"
                                               value="{{ $autoRow['item_label'] ?? '' }}" readonly required>
                                        <input type="hidden" name="items[{{ $idx }}][item_id]" class="item-id" value="{{ $autoRow['item_id'] ?? '' }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control hsn" value="{{ $autoRow['hsn'] ?? '' }}" readonly>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $idx }}][quantity]"
                                               class="form-control qty" value="{{ $autoRow['quantity'] ?? 1 }}" min="1" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $idx }}][price]"
                                               class="form-control price" step="0.01" value="{{ number_format((float) ($autoRow['price'] ?? 0), 2, '.', '') }}" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $idx }}][cgst_percent]"
                                               class="form-control cgst-percent" step="0.01" min="0" value="{{ number_format((float) ($autoRow['cgst_percent'] ?? 0), 2, '.', '') }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control cgst-amount" readonly>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $idx }}][sgst_percent]"
                                               class="form-control sgst-percent" step="0.01" min="0" value="{{ number_format((float) ($autoRow['sgst_percent'] ?? 0), 2, '.', '') }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control sgst-amount" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control line-total" readonly>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
                                    </td>
                                </tr>
                                @endforeach
                            @elseif($existingItems->isNotEmpty())
                                @foreach($existingItems as $idx => $invoiceItem)
                                <tr>
                                    <td>
                                        <input type="text" name="items[{{ $idx }}][item_name]" class="form-control item-name"
                                               value="{{ $invoiceItem->item->item_description ?? $invoiceItem->item->item_name ?? '' }}" readonly required>
                                        <input type="hidden" name="items[{{ $idx }}][item_id]" class="item-id" value="{{ $invoiceItem->item_id }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control hsn" value="{{ $invoiceItem->item->hsn_sac_code ?? '' }}" readonly>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $idx }}][quantity]"
                                               class="form-control qty" value="{{ $invoiceItem->quantity }}" min="1" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $idx }}][price]"
                                               class="form-control price" step="0.01" value="{{ $invoiceItem->price }}" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $idx }}][cgst_percent]"
                                               class="form-control cgst-percent" step="0.01" min="0" value="9">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control cgst-amount" readonly>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $idx }}][sgst_percent]"
                                               class="form-control sgst-percent" step="0.01" min="0" value="9">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control sgst-amount" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control line-total"
                                               value="{{ number_format($invoiceItem->total, 2) }}" readonly>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                {{-- Fallback: one empty row if no items saved yet --}}
                                <tr>
                                    <td>
                                        <input type="text" name="items[0][item_name]" class="form-control item-name" placeholder="Item Name" required>
                                        <input type="hidden" name="items[0][item_id]" class="item-id" value="">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control hsn" readonly>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][quantity]"
                                               class="form-control qty" value="1" min="1" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][price]"
                                               class="form-control price" step="0.01" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][cgst_percent]"
                                               class="form-control cgst-percent" step="0.01" min="0" value="9">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control cgst-amount" readonly>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][sgst_percent]"
                                               class="form-control sgst-percent" step="0.01" min="0" value="9">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control sgst-amount" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control line-total" readonly>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-primary mb-3" id="addRow">+ Add Item</button>

                <div class="mb-3">
                    <label class="form-label fw-semibold mb-1">Source File</label>
                    <div class="small text-muted mb-2">Email source file is available below. You can also upload a new file manually (PDF/JPG/JPEG/PNG).</div>

                    <div class="row g-2 align-items-center">
                        <div class="col-md-8">
                            <input type="file"
                                   name="po_invoice_file"
                                   class="form-control form-control-sm @error('po_invoice_file') is-invalid @enderror"
                                   accept=".pdf,.jpg,.jpeg,.png">
                            @error('po_invoice_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 text-md-end">
                            <a href="{{ route('finance.purchases.download-source-pdf', $purchase->id) }}"
                               class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-download"></i> Download Source File
                            </a>
                        </div>
                    </div>
                </div>

                <div class="ms-auto" style="max-width: 420px;">
                    <table class="table table-sm mb-3">
                        <tr>
                            <th>Sub Total</th>
                            <td class="text-end">₹ <span id="subTotal">{{ number_format($displaySubTotal ?? 0, 2) }}</span></td>
                        </tr>
                        <tr>
                            <th>CGST Total</th>
                            <td class="text-end">₹ <span id="cgstTotal">{{ number_format($displayCgstTotal ?? 0, 2) }}</span></td>
                        </tr>
                        <tr>
                            <th>SGST Total</th>
                            <td class="text-end">₹ <span id="sgstTotal">{{ number_format($displaySgstTotal ?? 0, 2) }}</span></td>
                        </tr>
                        <tr class="table-light">
                            <th>Grand Total</th>
                            <td class="text-end fw-bold">₹ <span id="grandTotal">{{ number_format($displayGrandTotal ?? 0, 2) }}</span></td>
                        </tr>
                    </table>
                </div>

                <input type="hidden" name="sub_total" id="subTotalInput" value="{{ $displaySubTotal ?? 0 }}">
                <input type="hidden" name="cgst_total" id="cgstTotalInput" value="{{ $displayCgstTotal ?? 0 }}">
                <input type="hidden" name="sgst_total" id="sgstTotalInput" value="{{ $displaySgstTotal ?? 0 }}">
                <input type="hidden" name="tax_amount" id="taxAmountInput" value="{{ ($displayCgstTotal ?? 0) + ($displaySgstTotal ?? 0) }}">
                <input type="hidden" name="grand_total" id="grandTotalInput" value="{{ $displayGrandTotal ?? 0 }}">
                <input type="hidden" name="total_amount" id="totalAmountInput" value="{{ $displayGrandTotal ?? 0 }}">

                <div class="text-end">
                    <button type="submit" class="btn btn-success">Update Invoice</button>
                </div>

            </form>

        </div>
    </div>

</div>

<script>
let rowIndex = {{ $purchase->items->count() ?: (count($prefillRows ?? []) ?: 1) }};

function calculateTotals() {
    let subTotal = 0;
    let cgstTotal = 0;
    let sgstTotal = 0;

    document.querySelectorAll('#itemsTable tbody tr').forEach(row => {
        const qty = parseFloat(row.querySelector('.qty').value) || 0;
        const price = parseFloat(row.querySelector('.price').value) || 0;
        const cgstPercent = parseFloat(row.querySelector('.cgst-percent').value) || 0;
        const sgstPercent = parseFloat(row.querySelector('.sgst-percent').value) || 0;

        const taxableAmount = qty * price;
        const cgstAmount = (taxableAmount * cgstPercent) / 100;
        const sgstAmount = (taxableAmount * sgstPercent) / 100;

        row.querySelector('.cgst-amount').value = cgstAmount.toFixed(2);
        row.querySelector('.sgst-amount').value = sgstAmount.toFixed(2);
        row.querySelector('.line-total').value = taxableAmount.toFixed(2);

        subTotal += taxableAmount;
        cgstTotal += cgstAmount;
        sgstTotal += sgstAmount;
    });

    const taxAmount = cgstTotal + sgstTotal;
    const grandTotal = subTotal + taxAmount;

    document.getElementById('subTotal').innerText = subTotal.toFixed(2);
    document.getElementById('cgstTotal').innerText = cgstTotal.toFixed(2);
    document.getElementById('sgstTotal').innerText = sgstTotal.toFixed(2);
    document.getElementById('grandTotal').innerText = grandTotal.toFixed(2);

    document.getElementById('subTotalInput').value = subTotal.toFixed(2);
    document.getElementById('cgstTotalInput').value = cgstTotal.toFixed(2);
    document.getElementById('sgstTotalInput').value = sgstTotal.toFixed(2);
    document.getElementById('taxAmountInput').value = taxAmount.toFixed(2);
    document.getElementById('grandTotalInput').value = grandTotal.toFixed(2);
    document.getElementById('totalAmountInput').value = grandTotal.toFixed(2);
}

// Only recalculate on load if rows have actual price data
// (avoids zeroing out server-passed display totals for auto-imported invoices)
const hasItemData = Array.from(document.querySelectorAll('#itemsTable tbody .price'))
    .some(el => parseFloat(el.value) > 0);
if (hasItemData) {
    calculateTotals();
}

document.addEventListener('input', function(e) {
    if (
        e.target.classList.contains('qty') ||
        e.target.classList.contains('price') ||
        e.target.classList.contains('cgst-percent') ||
        e.target.classList.contains('sgst-percent')
    ) {
        calculateTotals();
    }
});

document.getElementById('addRow').addEventListener('click', function() {
    const table  = document.querySelector('#itemsTable tbody');
    const newRow = table.rows[0].cloneNode(true);
    newRow.querySelectorAll('input:not([readonly])').forEach(el => el.value = '');
    newRow.querySelectorAll('input[readonly]').forEach(el => el.value = '');
    const itemNameInput = newRow.querySelector('.item-name');
    if (itemNameInput) {
        itemNameInput.removeAttribute('readonly');
        itemNameInput.placeholder = 'Item Name';
    }
    newRow.querySelector('.qty').value = 1;
    newRow.querySelector('.cgst-percent').value = 9;
    newRow.querySelector('.sgst-percent').value = 9;
    newRow.querySelector('.item-name').name     = `items[${rowIndex}][item_name]`;
    newRow.querySelector('.item-id').name       = `items[${rowIndex}][item_id]`;
    newRow.querySelector('.qty').name           = `items[${rowIndex}][quantity]`;
    newRow.querySelector('.price').name         = `items[${rowIndex}][price]`;
    newRow.querySelector('.cgst-percent').name  = `items[${rowIndex}][cgst_percent]`;
    newRow.querySelector('.sgst-percent').name  = `items[${rowIndex}][sgst_percent]`;
    table.appendChild(newRow);
    rowIndex++;
    calculateTotals();
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('removeRow')) {
        const rows = document.querySelectorAll('#itemsTable tbody tr');
        if (rows.length > 1) {
            e.target.closest('tr').remove();
            calculateTotals();
        }
    }
});

</script>

@endsection