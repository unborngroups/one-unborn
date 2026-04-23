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

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Purchase Invoice Details</h4>
            <small class="text-muted">Auto-received from email</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('finance.purchase_invoices.edit', $invoice->id) }}"
               class="btn btn-warning btn-sm">
                ✏ Edit
            </a>

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
                                {{ optional($invoice->vendor)->vendor_name ?? '—' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1 small">Vendor Name (from invoice)</p>
                            <p class="fw-semibold mb-0">
                                {{ $invoice->vendor_name ?? $invoice->vendor_name_raw ?? '—' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1 small">GSTIN</p>
                            <p class="fw-semibold mb-0 font-monospace">
                                {{ $invoice->gstin ?? $invoice->vendor_gstin ?? $invoice->gst_number ?? '—' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1 small">Invoice Number</p>
                            <p class="fw-semibold mb-0">{{ $invoice->invoice_no ?? '—' }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted mb-1 small">Invoice Date</p>
                            <p class="mb-0">
                                {{ $invoice->invoice_date ? $invoice->invoice_date->format('d-m-Y') : '—' }}
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted mb-1 small">Due Date</p>
                            <p class="mb-0">
                                {{ $invoice->due_date ? $invoice->due_date->format('d-m-Y') : '—' }}
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted mb-1 small">Confidence</p>
                            <p class="mb-0">
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
                            <p class="text-muted mb-1 small">Source</p>
                            <p class="mb-0">
                                {{ optional($invoice->emailLog)->source ?? 'email' }}
                            </p>
                        </div>
                        @if($invoice->notes)
                        <div class="col-12">
                            <p class="text-muted mb-1 small">Notes</p>
                            <p class="mb-0">{{ $invoice->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Amount breakdown --}}
            <div class="card shadow-sm">
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
                            </tr>
                            @endif
                            @if(($invoice->sgst_total ?? 0) > 0)
                            <tr>
                                <td class="text-muted">SGST / IGST</td>
                                <td class="text-end">₹ {{ number_format($invoice->sgst_total, 2) }}</td>
                            </tr>
                            @endif
                            @if(($invoice->tax_amount ?? 0) > 0)
                            <tr>
                                <td class="text-muted">Tax Amount</td>
                                <td class="text-end">₹ {{ number_format($invoice->tax_amount, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="table-success fw-bold">
                                <td>Grand Total</td>
                                <td class="text-end">₹ {{ number_format($invoice->grand_total ?? $invoice->total_amount ?? 0, 2) }}</td>
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
                <div class="card-header py-2 fw-semibold">Invoice Attachment</div>
                <div class="card-body text-center">
                    <a href="{{ route('finance.purchases.download-source-pdf', $invoice->id) }}"
                       target="_blank"
                       class="btn btn-outline-primary w-100">
                        View Invoice PDF
                    </a>
                </div>
            </div>
            @endif

            @if(!empty($raw['matching']))
            <div class="card shadow-sm mb-4 border-info">
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
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            @endif

            @if(!empty($raw))
            <div class="card shadow-sm border-secondary">
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
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
