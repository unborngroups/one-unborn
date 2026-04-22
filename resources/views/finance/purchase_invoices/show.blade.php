@extends('layouts.app')

@section('title', 'Purchase Invoice - ' . ($invoice->invoice_no ?? 'View'))

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

<<<<<<< HEAD
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

=======
>>>>>>> 90f414630e61a509facbdc18cba07834240feaaf
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Purchase Invoice Details</h4>
            <small class="text-muted">Auto-received from email</small>
        </div>
<<<<<<< HEAD
        <div class="d-flex gap-2 flex-wrap">
=======
        <div class="d-flex gap-2">
>>>>>>> 90f414630e61a509facbdc18cba07834240feaaf
            <a href="{{ route('finance.purchase_invoices.edit', $invoice->id) }}"
               class="btn btn-warning btn-sm">
                ✏ Edit
            </a>

<<<<<<< HEAD
            @if($invoice->po_invoice_file)
                <a href="{{ asset('images/poinvoice_files/' . $invoice->po_invoice_file) }}"
                   download="{{ $invoice->invoice_no ?? 'invoice_' . $invoice->id . '.pdf' }}"
                   target="_blank"
                   class="btn btn-success btn-sm">
                    <i class="bi bi-download me-1"></i>
                    Download Invoice PDF
                </a>
            @endif

=======
>>>>>>> 90f414630e61a509facbdc18cba07834240feaaf
            @if($invoice->status == 'needs_review')
                <form action="{{ route('finance.purchase_invoices.verify', $invoice->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-info btn-sm">Verify</button>
                </form>
            @endif

            @if($invoice->status == 'verified')
                <form action="{{ route('finance.purchase_invoices.approve', $invoice->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-success btn-sm">Approve</button>
                </form>
            @endif

            @if($invoice->status == 'approved')
                <form action="{{ route('finance.purchase_invoices.markPaid', $invoice->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-dark btn-sm">Mark Paid</button>
                </form>
            @endif

            <a href="{{ route('finance.purchase_invoices.index') }}" class="btn btn-outline-secondary btn-sm">
                &larr; Back
            </a>
        </div>
    </div>

    <div class="row g-4">

        {{-- Main details --}}
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
<<<<<<< HEAD
                <div class="card-header py-3 fw-semibold d-flex justify-content-between align-items-center">
                    <div>
                        <span class="me-3">Invoice Information</span>
                        <span class="badge
                            @switch($invoice->status)
                                @case('draft') bg-secondary @break
                                @case('needs_review') bg-warning text-dark @break
                                @case('verified') bg-info @break
                                @case('approved') bg-success @break
                                @case('paid') bg-dark @break
                                @case('duplicate') bg-danger @break
                                @default bg-secondary
                            @endswitch">
                            {{ ucfirst(str_replace('_',' ', $invoice->status)) }}
                        </span>
                    </div>
                    @if($invoice->po_invoice_file)
                        <div>
                            <span class="badge bg-light text-dark me-2">
                                <i class="bi bi-file-earmark-pdf me-1"></i>
                                PDF Attached
                            </span>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-building text-primary me-2"></i>
                                <p class="text-muted mb-0 small me-2">Vendor (Master)</p>
                            </div>
                            <p class="fw-semibold mb-3 p-2 bg-light rounded">
=======
                <div class="card-header py-2 fw-semibold d-flex justify-content-between">
                    <span>Invoice Information</span>
                    <span class="badge
                        @switch($invoice->status)
                            @case('draft') bg-secondary @break
                            @case('needs_review') bg-warning text-dark @break
                            @case('verified') bg-info @break
                            @case('approved') bg-success @break
                            @case('paid') bg-dark @break
                            @case('duplicate') bg-danger @break
                            @default bg-secondary
                        @endswitch">
                        {{ ucfirst(str_replace('_',' ', $invoice->status)) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="text-muted mb-1 small">Vendor (Master)</p>
                            <p class="fw-semibold mb-0">
>>>>>>> 90f414630e61a509facbdc18cba07834240feaaf
                                {{ optional($invoice->vendor)->vendor_name ?? '—' }}
                            </p>
                        </div>
                        <div class="col-md-6">
<<<<<<< HEAD
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-person-badge text-info me-2"></i>
                                <p class="text-muted mb-0 small me-2">Vendor Name (from invoice)</p>
                            </div>
                            <p class="fw-semibold mb-3 p-2 bg-light rounded">
=======
                            <p class="text-muted mb-1 small">Vendor Name (from invoice)</p>
                            <p class="fw-semibold mb-0">
>>>>>>> 90f414630e61a509facbdc18cba07834240feaaf
                                {{ $invoice->vendor_name ?? $invoice->vendor_name_raw ?? '—' }}
                            </p>
                        </div>
                        <div class="col-md-6">
<<<<<<< HEAD
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-receipt text-success me-2"></i>
                                <p class="text-muted mb-0 small me-2">GSTIN</p>
                            </div>
                            <p class="fw-semibold mb-3 p-2 bg-light rounded font-monospace">
=======
                            <p class="text-muted mb-1 small">GSTIN</p>
                            <p class="fw-semibold mb-0 font-monospace">
>>>>>>> 90f414630e61a509facbdc18cba07834240feaaf
                                {{ $invoice->gstin ?? $invoice->vendor_gstin ?? $invoice->gst_number ?? '—' }}
                            </p>
                        </div>
                        <div class="col-md-6">
<<<<<<< HEAD
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-upc-scan text-warning me-2"></i>
                                <p class="text-muted mb-0 small me-2">Invoice Number</p>
                            </div>
                            <p class="fw-semibold mb-3 p-2 bg-light rounded">
                                {{ $invoice->invoice_no ?? '—' }}
                            </p>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-calendar-date text-primary me-2"></i>
                                <p class="text-muted mb-0 small me-2">Invoice Date</p>
                            </div>
                            <p class="mb-0 p-2 bg-light rounded">
=======
                            <p class="text-muted mb-1 small">Invoice Number</p>
                            <p class="fw-semibold mb-0">{{ $invoice->invoice_no ?? '—' }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted mb-1 small">Invoice Date</p>
                            <p class="mb-0">
>>>>>>> 90f414630e61a509facbdc18cba07834240feaaf
                                {{ $invoice->invoice_date ? $invoice->invoice_date->format('d-m-Y') : '—' }}
                            </p>
                        </div>
                        <div class="col-md-3">
<<<<<<< HEAD
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-calendar-check text-danger me-2"></i>
                                <p class="text-muted mb-0 small me-2">Due Date</p>
                            </div>
                            <p class="mb-0 p-2 @if($invoice->due_date && $invoice->due_date->isPast()) bg-warning @else bg-light @endif rounded">
                                {{ $invoice->due_date ? $invoice->due_date->format('d-m-Y') : '—' }}
                                @if($invoice->due_date && $invoice->due_date->isPast())
                                    <span class="badge bg-danger ms-2">Overdue</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-award text-info me-2"></i>
                                <p class="text-muted mb-0 small me-2">Confidence</p>
                            </div>
                            <p class="mb-0 p-2 bg-light rounded">
=======
                            <p class="text-muted mb-1 small">Due Date</p>
                            <p class="mb-0">
                                {{ $invoice->due_date ? $invoice->due_date->format('d-m-Y') : '—' }}
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted mb-1 small">Confidence</p>
                            <p class="mb-0">
>>>>>>> 90f414630e61a509facbdc18cba07834240feaaf
                                <span class="badge
                                    @if(($invoice->confidence_score ?? 0) >= 80)
                                        bg-success
                                    @elseif(($invoice->confidence_score ?? 0) >= 50)
                                        bg-warning text-dark
                                    @else
                                        bg-danger
                                    @endif">
                                    {{ $invoice->confidence_score ?? 0 }}%
                                </span>
                            </p>
                        </div>
                        <div class="col-md-3">
<<<<<<< HEAD
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-envelope text-secondary me-2"></i>
                                <p class="text-muted mb-0 small me-2">Source</p>
                            </div>
                            <p class="mb-0 p-2 bg-light rounded">
=======
                            <p class="text-muted mb-1 small">Source</p>
                            <p class="mb-0">
>>>>>>> 90f414630e61a509facbdc18cba07834240feaaf
                                {{ optional($invoice->emailLog)->source ?? 'email' }}
                            </p>
                        </div>
                        @if($invoice->notes)
                        <div class="col-12">
<<<<<<< HEAD
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-sticky text-warning me-2"></i>
                                <p class="text-muted mb-0 small me-2">Notes</p>
                            </div>
                            <p class="mb-0 p-3 bg-light rounded">{{ $invoice->notes }}</p>
=======
                            <p class="text-muted mb-1 small">Notes</p>
                            <p class="mb-0">{{ $invoice->notes }}</p>
>>>>>>> 90f414630e61a509facbdc18cba07834240feaaf
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Amount breakdown --}}
            <div class="card shadow-sm">
<<<<<<< HEAD
                <div class="card-header py-3 fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-currency-rupee me-2"></i>Amount Breakdown</span>
                    <span class="badge bg-light text-dark">
                        {{ $invoice->currency ?? 'INR' }}
                    </span>
                </div>
                <div class="card-body p-3">
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted ps-3">
                                    <i class="bi bi-receipt-cutoff text-primary me-2"></i>
                                    Sub Total
                                </td>
                                <td class="text-end pe-3 fw-semibold">
                                    ₹ {{ number_format($invoice->amount ?? 0, 2) }}
                                </td>
                            </tr>
                            @if(($invoice->cgst_total ?? 0) > 0)
                            <tr>
                                <td class="text-muted ps-3">
                                    <i class="bi bi-calculator text-info me-2"></i>
                                    CGST
                                </td>
                                <td class="text-end pe-3 fw-semibold">
                                    ₹ {{ number_format($invoice->cgst_total, 2) }}
                                </td>
=======
                <div class="card-header py-2 fw-semibold">Amount Breakdown</div>
                <div class="card-body p-0">
                    <table class="table table-sm table-bordered mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted">Sub Total</td>
                                <td class="text-end">₹ {{ number_format($invoice->amount ?? 0, 2) }}</td>
                            </tr>
                            @if(($invoice->cgst_total ?? 0) > 0)
                            <tr>
                                <td class="text-muted">CGST</td>
                                <td class="text-end">₹ {{ number_format($invoice->cgst_total, 2) }}</td>
>>>>>>> 90f414630e61a509facbdc18cba07834240feaaf
                            </tr>
                            @endif
                            @if(($invoice->sgst_total ?? 0) > 0)
                            <tr>
<<<<<<< HEAD
                                <td class="text-muted ps-3">
                                    <i class="bi bi-calculator text-success me-2"></i>
                                    SGST / IGST
                                </td>
                                <td class="text-end pe-3 fw-semibold">
                                    ₹ {{ number_format($invoice->sgst_total, 2) }}
                                </td>
=======
                                <td class="text-muted">SGST / IGST</td>
                                <td class="text-end">₹ {{ number_format($invoice->sgst_total, 2) }}</td>
>>>>>>> 90f414630e61a509facbdc18cba07834240feaaf
                            </tr>
                            @endif
                            @if(($invoice->tax_amount ?? 0) > 0)
                            <tr>
<<<<<<< HEAD
                                <td class="text-muted ps-3">
                                    <i class="bi bi-percent text-warning me-2"></i>
                                    Tax Amount
                                </td>
                                <td class="text-end pe-3 fw-semibold">
                                    ₹ {{ number_format($invoice->tax_amount, 2) }}
                                </td>
                            </tr>
                            @endif
                            <tr class="table-success fw-bold">
                                <td class="ps-3">
                                    <i class="bi bi-cash-stack text-success me-2"></i>
                                    Grand Total
                                </td>
                                <td class="text-end pe-3 fw-bold fs-5 text-success">
                                    ₹ {{ number_format($invoice->grand_total ?? $invoice->total_amount ?? 0, 2) }}
                                </td>
=======
                                <td class="text-muted">Tax Amount</td>
                                <td class="text-end">₹ {{ number_format($invoice->tax_amount, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="table-success fw-bold">
                                <td>Grand Total</td>
                                <td class="text-end">₹ {{ number_format($invoice->grand_total ?? $invoice->total_amount ?? 0, 2) }}</td>
>>>>>>> 90f414630e61a509facbdc18cba07834240feaaf
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- OCR raw data sidebar --}}
        <div class="col-lg-4">

            @if($invoice->po_invoice_file)
            <div class="card shadow-sm mb-4">
<<<<<<< HEAD
                <div class="card-header py-3 fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-file-earmark-pdf me-2"></i>Invoice Attachment</span>
                    <span class="badge bg-primary">PDF</span>
                </div>
                <div class="card-body text-center p-3">
                    <div class="d-grid gap-2">
                        <a href="{{ route('finance.purchases.download-source-pdf', $invoice->id) }}"
                           target="_blank"
                           class="btn btn-outline-primary">
                            <i class="bi bi-eye me-2"></i>
                            View Invoice PDF
                        </a>
                        <a href="{{ asset('images/poinvoice_files/' . $invoice->po_invoice_file) }}"
                           download="{{ $invoice->invoice_no ?? 'invoice_' . $invoice->id . '.pdf' }}"
                           target="_blank"
                           class="btn btn-primary">
                            <i class="bi bi-download me-2"></i>
                            Download Invoice PDF
                        </a>
                    </div>
=======
                <div class="card-header py-2 fw-semibold">Invoice Attachment</div>
                <div class="card-body text-center">
                    <a href="{{ route('finance.purchases.download-source-pdf', $invoice->id) }}"
                       target="_blank"
                       class="btn btn-outline-primary w-100">
                        View Invoice PDF
                    </a>
>>>>>>> 90f414630e61a509facbdc18cba07834240feaaf
                </div>
            </div>
            @endif

            @if(!empty($raw['matching']))
            <div class="card shadow-sm mb-4 border-info">
<<<<<<< HEAD
                <div class="card-header py-3 fw-semibold bg-info text-white d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-people-fill me-2"></i>Vendor Match Details</span>
                    <span class="badge bg-light text-dark">
                        Score: {{ $raw['matching']['combined_confidence'] ?? 0 }}%
                    </span>
                </div>
                <div class="card-body small">
                    <div class="mb-3">
                        @php
                            $combinedScore = $raw['matching']['combined_confidence'] ?? 0;
                            $scoreColor = $combinedScore >= 80 ? 'success' : ($combinedScore >= 50 ? 'warning' : 'danger');
                        @endphp
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-{{ $scoreColor }}" role="progressbar" 
                                 style="width: {{ $combinedScore }}%" 
                                 aria-valuenow="{{ $combinedScore }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted ps-3">
                                <i class="bi bi-search me-2"></i>Matched By
                            </td>
                            <td class="fw-semibold">{{ $raw['matching']['matched_by'] ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">
                                <i class="bi bi-cpu me-2"></i>Parser Score
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ $raw['matching']['parser_confidence'] ?? '—' }}%
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">
                                <i class="bi bi-person-check me-2"></i>Vendor Match
                            </td>
                            <td>
                                <span class="badge 
                                    @if(($raw['matching']['vendor_match_score'] ?? 0) >= 80)
                                        bg-success
                                    @elseif(($raw['matching']['vendor_match_score'] ?? 0) >= 50)
                                        bg-warning text-dark
                                    @else
                                        bg-danger
                                    @endif">
                                    {{ $raw['matching']['vendor_match_score'] ?? '—' }}%
                                </span>
                            </td>
                        </tr>
                        <tr class="table-success">
                            <td class="text-muted ps-3 fw-bold">
                                <i class="bi bi-award-fill me-2"></i>Combined Score
                            </td>
                            <td class="fw-bold fs-5">
                                <span class="badge 
                                    @if($combinedScore >= 80)
                                        bg-success
                                    @elseif($combinedScore >= 50)
                                        bg-warning text-dark
                                    @else
                                        bg-danger
                                    @endif">
                                    {{ $combinedScore }}%
                                </span>
                            </td>
                        </tr>
                        @if($raw['matching']['vendor_master_name'] ?? null)
                        <tr>
                            <td class="text-muted ps-3">
                                <i class="bi bi-building me-2"></i>Master Name
                            </td>
                            <td class="fw-semibold">{{ $raw['matching']['vendor_master_name'] }}</td>
=======
                <div class="card-header py-2 fw-semibold bg-info text-white">
                    Vendor Match Details
                </div>
                <div class="card-body small">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Matched By</td>
                            <td>{{ $raw['matching']['matched_by'] ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Parser Score</td>
                            <td>{{ $raw['matching']['parser_confidence'] ?? '—' }}%</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Vendor Match</td>
                            <td>{{ $raw['matching']['vendor_match_score'] ?? '—' }}%</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Combined</td>
                            <td class="fw-bold">{{ $raw['matching']['combined_confidence'] ?? '—' }}%</td>
                        </tr>
                        @if($raw['matching']['vendor_master_name'] ?? null)
                        <tr>
                            <td class="text-muted">Master Name</td>
                            <td>{{ $raw['matching']['vendor_master_name'] }}</td>
>>>>>>> 90f414630e61a509facbdc18cba07834240feaaf
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            @endif

            @if(!empty($raw))
            <div class="card shadow-sm border-secondary">
<<<<<<< HEAD
                <div class="card-header py-3 fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-file-text me-2"></i>Raw OCR Data</span>
                    <small class="text-muted ms-2 fw-normal">(as extracted)</small>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
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
                                    @if(!empty($raw[$key]))
                                    <tr class="border-bottom">
                                        <td class="ps-3 py-2 text-muted small fw-semibold">
                                            <i class="bi bi-tag me-2"></i>{{ $label }}
                                        </td>
                                        <td class="py-2 small font-monospace">{{ $raw[$key] }}</td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
=======
                <div class="card-header py-2 fw-semibold">
                    Raw OCR Data
                    <small class="text-muted ms-2 fw-normal">(as extracted)</small>
                </div>
                <div class="card-body p-0">
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
                                @if(!empty($raw[$key]))
                                <tr>
                                    <td class="ps-3 py-1 text-muted small">{{ $label }}</td>
                                    <td class="py-1 small">{{ $raw[$key] }}</td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
>>>>>>> 90f414630e61a509facbdc18cba07834240feaaf
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
