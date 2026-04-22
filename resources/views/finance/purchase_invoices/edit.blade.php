@extends('layouts.app')

@section('title', 'Edit Purchase Invoice')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Edit Purchase Invoice</h4>
            <small class="text-muted">Auto-received from email — review &amp; save</small>
        </div>
        <div class="d-flex gap-2">
            @if($invoice->po_invoice_file)
                <a href="{{ asset('images/poinvoice_files/' . $invoice->po_invoice_file) }}"
                   download="{{ $invoice->invoice_no ?? 'invoice_' . $invoice->id . '.pdf' }}"
                   target="_blank"
                   class="btn btn-success btn-sm">
                    <i class="bi bi-download me-1"></i>
                    Download Invoice PDF
                </a>
            @endif
            <a href="{{ route('finance.purchase_invoices.show', $invoice->id) }}" class="btn btn-outline-secondary btn-sm">
                &larr; Back
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('finance.purchase_invoices.update', $invoice->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">

            {{-- LEFT: OCR Extracted data (read-only reference) --}}
            @if(!empty($raw))
            <div class="col-lg-4">
                <div class="card border-info h-100">
                    <div class="card-header bg-info text-white py-2">
                        <i class="bi bi-robot"></i>
                        Auto-Extracted (OCR) — Reference
                    </div>
                    <div class="card-body small">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                @foreach([
                                    'vendor_name'    => 'Vendor Name',
                                    'gstin'          => 'GSTIN',
                                    'invoice_number' => 'Invoice No',
                                    'invoice_date'   => 'Invoice Date',
                                    'amount'         => 'Sub Total',
                                    'tax'            => 'Tax',
                                    'total'          => 'Grand Total',
                                ] as $key => $label)
                                    @if(isset($raw[$key]) && $raw[$key] !== null && $raw[$key] !== '')
                                    <tr>
                                        <td class="text-muted fw-semibold pe-2">{{ $label }}</td>
                                        <td>{{ $raw[$key] }}</td>
                                    </tr>
                                    @endif
                                @endforeach

                                @if(!empty($raw['matching']))
                                <tr><td colspan="2"><hr class="my-1"></td></tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Confidence</td>
                                    <td>
                                        <span class="badge
                                            @if(($raw['matching']['combined_confidence'] ?? 0) >= 80)
                                                bg-success
                                            @elseif(($raw['matching']['combined_confidence'] ?? 0) >= 50)
                                                bg-warning text-dark
                                            @else
                                                bg-danger
                                            @endif">
                                            {{ $raw['matching']['combined_confidence'] ?? '-' }}%
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Matched By</td>
                                    <td>{{ $raw['matching']['matched_by'] ?? '-' }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>

                        @if($invoice->po_invoice_file)
                        <hr>
                        <p class="mb-1 text-muted fw-semibold">Attached File</p>
                        <a href="{{ route('finance.purchases.download-source-pdf', $invoice->id) }}"
                           target="_blank"
                           class="btn btn-outline-info btn-sm w-100">
                            View Invoice PDF
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- RIGHT: Editable form --}}
            <div class="col-lg-{{ !empty($raw) ? '8' : '12' }}">
                <div class="card shadow-sm">
                    <div class="card-header py-2 fw-semibold">Invoice Details</div>
                    <div class="card-body">

                        <div class="row g-3">
                            {{-- Vendor dropdown --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Vendor (Master)</label>
                                <select name="vendor_id" class="form-select">
                                    <option value="">— Select Vendor (optional) —</option>
                                    @foreach($vendors as $v)
                                        <option value="{{ $v->id }}"
                                            {{ old('vendor_id', $invoice->vendor_id) == $v->id ? 'selected' : '' }}>
                                            {{ $v->vendor_name }}
                                            @if($v->gstin) ({{ $v->gstin }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Leave blank if vendor not in master list.</div>
                            </div>

                            {{-- Vendor name raw --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Vendor Name <span class="text-danger">*</span></label>
                                <input type="text" name="vendor_name" class="form-control @error('vendor_name') is-invalid @enderror"
                                    value="{{ old('vendor_name', $invoice->vendor_name ?? $invoice->vendor_name_raw) }}"
                                    placeholder="As printed on invoice">
                                @error('vendor_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- GSTIN --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">GSTIN</label>
                                <input type="text" name="gstin" class="form-control @error('gstin') is-invalid @enderror"
                                    value="{{ old('gstin', $invoice->gstin ?? $invoice->vendor_gstin ?? $invoice->gst_number) }}"
                                    placeholder="e.g. 29ABCDE1234F1ZK"
                                    maxlength="15" style="text-transform:uppercase">
                                @error('gstin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Invoice No --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Invoice Number</label>
                                <input type="text" name="invoice_no" class="form-control @error('invoice_no') is-invalid @enderror"
                                    value="{{ old('invoice_no', $invoice->invoice_no) }}"
                                    placeholder="e.g. INV/2025/001">
                                @error('invoice_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Invoice Date --}}
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Invoice Date</label>
                                <input type="date" name="invoice_date" class="form-control @error('invoice_date') is-invalid @enderror"
                                    value="{{ old('invoice_date', $invoice->invoice_date ? $invoice->invoice_date->format('Y-m-d') : '') }}">
                                @error('invoice_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Due Date --}}
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Due Date</label>
                                <input type="date" name="due_date" class="form-control"
                                    value="{{ old('due_date', $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '') }}">
                            </div>

                            {{-- Status (read-only display) --}}
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Status</label>
                                <input type="text" class="form-control" readonly
                                    value="{{ ucfirst(str_replace('_',' ', $invoice->status)) }}">
                                <div class="form-text">Change status using Verify / Approve / Mark Paid buttons.</div>
                            </div>
                        </div>

                        {{-- Amounts section --}}
                        <hr class="my-3">
                        <h6 class="text-muted mb-3">Amount Details</h6>

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Sub Total (₹)</label>
                                <input type="number" step="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror"
                                    value="{{ old('amount', $invoice->amount ?? 0) }}"
                                    id="amt_subtotal" oninput="calcGrandTotal()">
                                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">CGST (₹)</label>
                                <input type="number" step="0.01" name="cgst_total" class="form-control"
                                    value="{{ old('cgst_total', $invoice->cgst_total ?? 0) }}"
                                    id="amt_cgst" oninput="calcGrandTotal()">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">SGST / IGST (₹)</label>
                                <input type="number" step="0.01" name="sgst_total" class="form-control"
                                    value="{{ old('sgst_total', $invoice->sgst_total ?? 0) }}"
                                    id="amt_sgst" oninput="calcGrandTotal()">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Tax Amount (₹)</label>
                                <input type="number" step="0.01" name="tax_amount" class="form-control"
                                    value="{{ old('tax_amount', $invoice->tax_amount ?? 0) }}"
                                    id="amt_tax" oninput="calcGrandTotal()">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-success">Grand Total (₹)</label>
                                <input type="number" step="0.01" name="grand_total" class="form-control border-success fw-bold"
                                    value="{{ old('grand_total', $invoice->grand_total ?? 0) }}"
                                    id="amt_grand">
                                <div class="form-text">You can also type directly.</div>
                            </div>
                        </div>

                        {{-- Notes --}}
                        <hr class="my-3">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Notes / Remarks</label>
                                <textarea name="notes" class="form-control" rows="3"
                                    placeholder="Any corrections or additional info...">{{ old('notes', $invoice->notes) }}</textarea>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                Save Invoice Details
                            </button>
                            <a href="{{ route('finance.purchase_invoices.show', $invoice->id) }}"
                               class="btn btn-outline-secondary">Cancel</a>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
function calcGrandTotal() {
    const sub  = parseFloat(document.getElementById('amt_subtotal').value) || 0;
    const cgst = parseFloat(document.getElementById('amt_cgst').value) || 0;
    const sgst = parseFloat(document.getElementById('amt_sgst').value) || 0;
    const tax  = parseFloat(document.getElementById('amt_tax').value) || 0;
    const total = sub + cgst + sgst + tax;
    if (total > 0) {
        document.getElementById('amt_grand').value = total.toFixed(2);
    }
}
</script>
@endpush
