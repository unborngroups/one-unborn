@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>📄 Create Purchase Invoice</h2>
            <p class="text-muted">Manually enter invoice details or use email import</p>
        </div>
    </div>

    <form action="{{ route('finance.purchases.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation">
        @csrf

        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="basic-tab" data-toggle="tab" href="#basic" role="tab">
                            📋 Basic Info
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="amounts-tab" data-toggle="tab" href="#amounts" role="tab">
                            💰 Amounts
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="optional-tab" data-toggle="tab" href="#optional" role="tab">
                            ⚙️ Optional
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">
                    <!-- TAB 1: BASIC INFO -->
                    <div class="tab-pane fade show active" id="basic" role="tabpanel">
                        <div class="row">
                            <!-- Vendor Selection -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-danger">*</span> Vendor Name
                                </label>
                                <select name="vendor_id" class="form-control @error('vendor_id') is-invalid @enderror" required>
                                    <option value="" selected disabled>-- Select Vendor --</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                            {{ $vendor->name }} 
                                            @if($vendor->gst_number)
                                                (GST: {{ $vendor->gst_number }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('vendor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Invoice Number -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-danger">*</span> Invoice Number
                                </label>
                                <input 
                                    type="text" 
                                    name="invoice_number" 
                                    class="form-control @error('invoice_number') is-invalid @enderror"
                                    placeholder="e.g., INV-2024-001"
                                    value="{{ old('invoice_number') }}"
                                    required
                                >
                                @error('invoice_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Vendor's invoice number</small>
                            </div>

                            <!-- Invoice Date -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> Invoice Date
                                </label>
                                <input 
                                    type="date" 
                                    name="invoice_date" 
                                    class="form-control @error('invoice_date') is-invalid @enderror"
                                    value="{{ old('invoice_date') ?? now()->format('Y-m-d') }}"
                                >
                                @error('invoice_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">YYYY-MM-DD format</small>
                            </div>

                            <!-- Deliverable (Optional) -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> Deliverable
                                </label>
                                <select name="deliverable_id" class="form-control @error('deliverable_id') is-invalid @enderror">
                                    <option value="" selected>-- Select (Optional) --</option>
                                    @foreach($deliverables as $deliverable)
                                        <option value="{{ $deliverable->id }}" {{ old('deliverable_id') == $deliverable->id ? 'selected' : '' }}>
                                            {{ $deliverable->circuit_name ?? 'Deliverable #' . $deliverable->id }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('deliverable_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- PDF/Image Upload -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-danger">*</span> Invoice PDF / Image
                                </label>
                                <div class="custom-file">
                                    <input 
                                        type="file" 
                                        name="po_invoice_file" 
                                        class="custom-file-input @error('po_invoice_file') is-invalid @enderror"
                                        id="invoiceFile"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        required
                                    >
                                    <label class="custom-file-label" for="invoiceFile">Choose file...</label>
                                </div>
                                @error('po_invoice_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Accepted: PDF, JPG, PNG | Max: 2MB
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: AMOUNTS -->
                    <div class="tab-pane fade" id="amounts" role="tabpanel">
                        <div class="alert alert-info">
                            💡 Enter individual cost components or just total amount
                        </div>

                        <div class="row">
                            <!-- ARC Amount -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> ARC (Annual Recurring Cost)
                                </label>
                                <input 
                                    type="number" 
                                    name="arc_amount" 
                                    class="form-control @error('arc_amount') is-invalid @enderror"
                                    placeholder="0.00"
                                    step="0.01"
                                    min="0"
                                    value="{{ old('arc_amount') }}"
                                >
                                @error('arc_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- OTC Amount -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> OTC (One-Time Cost)
                                </label>
                                <input 
                                    type="number" 
                                    name="otc_amount" 
                                    class="form-control @error('otc_amount') is-invalid @enderror"
                                    placeholder="0.00"
                                    step="0.01"
                                    min="0"
                                    value="{{ old('otc_amount') }}"
                                >
                                @error('otc_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Static IP Cost -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> Static IP Cost
                                </label>
                                <input 
                                    type="number" 
                                    name="static_amount" 
                                    class="form-control @error('static_amount') is-invalid @enderror"
                                    placeholder="0.00"
                                    step="0.01"
                                    min="0"
                                    value="{{ old('static_amount') }}"
                                >
                                @error('static_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Total Amount (REQUIRED) -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-danger">*</span> Total Amount
                                </label>
                                <input 
                                    type="number" 
                                    name="total_amount" 
                                    class="form-control @error('total_amount') is-invalid @enderror"
                                    placeholder="0.00"
                                    step="0.01"
                                    min="0.01"
                                    value="{{ old('total_amount') }}"
                                    required
                                >
                                @error('total_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Total invoice amount in ₹
                                </small>
                            </div>

                            <!-- GST Number (Optional) -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> Vendor GST Number
                                </label>
                                <input 
                                    type="text" 
                                    name="gst_number" 
                                    class="form-control @error('gst_number') is-invalid @enderror"
                                    placeholder="27AABCT1234H2Z0"
                                    pattern="\d{2}[A-Z]{5}\d{4}[A-Z1-9][A-Z][0-9A-Z]\d"
                                    value="{{ old('gst_number') }}"
                                >
                                @error('gst_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    15-character GST number (auto-extracted from PDF if available)
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: OPTIONAL FIELDS -->
                    <div class="tab-pane fade" id="optional" role="tabpanel">
                        <div class="alert alert-warning">
                            ⚙️ These fields are optional and can be left blank
                        </div>

                        <div class="row">
                            <!-- Vendor Name (Free Text) -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> Vendor Name (Text)
                                </label>
                                <input 
                                    type="text" 
                                    name="vendor_name" 
                                    class="form-control"
                                    placeholder="Vendor company name"
                                    value="{{ old('vendor_name') }}"
                                >
                                <small class="form-text text-muted">
                                    If different from dropdown selection
                                </small>
                            </div>

                            <!-- Vendor Email -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> Vendor Email
                                </label>
                                <input 
                                    type="email" 
                                    name="vendor_email" 
                                    class="form-control"
                                    placeholder="vendor@company.com"
                                    value="{{ old('vendor_email') }}"
                                >
                            </div>

                            <!-- Vendor Phone -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> Vendor Phone
                                </label>
                                <input 
                                    type="text" 
                                    name="vendor_phone" 
                                    class="form-control"
                                    placeholder="+91-XXXXXXXXXX"
                                    value="{{ old('vendor_phone') }}"
                                >
                            </div>

                            <!-- Vendor Address -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> Vendor Address
                                </label>
                                <textarea 
                                    name="vendor_address" 
                                    class="form-control"
                                    rows="2"
                                    placeholder="Street, City, State, Zip"
                                >{{ old('vendor_address') }}</textarea>
                            </div>

                            <!-- Due Date -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> Due Date
                                </label>
                                <input 
                                    type="date" 
                                    name="due_date" 
                                    class="form-control"
                                    value="{{ old('due_date') }}"
                                >
                            </div>

                            <!-- Tax Amount -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> Tax Amount (CGST + SGST)
                                </label>
                                <input 
                                    type="number" 
                                    name="tax_amount" 
                                    class="form-control"
                                    placeholder="0.00"
                                    step="0.01"
                                    min="0"
                                    value="{{ old('tax_amount') }}"
                                >
                            </div>

                            <!-- CGST -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> CGST (Central GST)
                                </label>
                                <input 
                                    type="number" 
                                    name="cgst_total" 
                                    class="form-control"
                                    placeholder="0.00"
                                    step="0.01"
                                    min="0"
                                    value="{{ old('cgst_total') }}"
                                >
                            </div>

                            <!-- SGST -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> SGST (State GST)
                                </label>
                                <input 
                                    type="number" 
                                    name="sgst_total" 
                                    class="form-control"
                                    placeholder="0.00"
                                    step="0.01"
                                    min="0"
                                    value="{{ old('sgst_total') }}"
                                >
                            </div>

                            <!-- Notes -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> Notes / Comments
                                </label>
                                <textarea 
                                    name="notes" 
                                    class="form-control"
                                    rows="3"
                                    placeholder="Any additional notes about this invoice"
                                >{{ old('notes') }}</textarea>
                            </div>

                            <!-- Terms -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> Payment Terms
                                </label>
                                <input 
                                    type="text" 
                                    name="terms" 
                                    class="form-control"
                                    placeholder="e.g., Net 30, Net 45, Due on receipt"
                                    value="{{ old('terms') }}"
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="float-right">
                    <a href="{{ route('finance.purchases.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        🔒 Create Invoice
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // Auto-fill filename
    document.getElementById('invoiceFile').addEventListener('change', function(e) {
        var label = e.target.nextElementSibling;
        label.textContent = e.target.files[0].name;
    });

    // Form validation
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>

<style>
    .badge {
        font-size: 0.7rem;
        padding: 0.3rem 0.5rem;
    }
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    .nav-tabs .nav-link {
        color: #666;
    }
    .nav-tabs .nav-link.active {
        color: #0066cc;
        border-bottom: 3px solid #0066cc;
    }
</style>

@endsection
