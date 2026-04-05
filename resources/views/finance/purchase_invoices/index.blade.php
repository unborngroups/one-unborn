@extends('layouts.app')

@section('title', 'Purchase Invoices')

@section('content')

<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Purchase Invoices</h2>

        <div class="d-flex gap-2 align-items-center">
            <a href="{{ route('finance.purchases.create') }}"
               class="btn btn-sm btn-info p-2 text-white">
                <h2 class="mb-0">+ create invoice</h2>
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark-primary">
                    <tr>
                        <th>#</th>
                        <th>Invoice No</th>
                        <th>Vendor</th>
                        <th>GSTIN</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Accuracy</th>
                        <th>Status</th>
                        <th width="250">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                @php
                                    $invoiceNoDisplay = trim((string) ($invoice->invoice_no ?? ''));
                                    $rawInvoiceNo = trim((string) data_get($invoice->raw_json, 'invoice_number', ''));
                                    if ($invoiceNoDisplay !== '' && str_starts_with(strtoupper($invoiceNoDisplay), 'GMAIL-') && $rawInvoiceNo !== '') {
                                        $invoiceNoDisplay = $rawInvoiceNo;
                                    }
                                @endphp
                                {{ $invoiceNoDisplay !== '' ? $invoiceNoDisplay : '-' }}
                            </td>

                            <td>
                                {{ data_get($invoice->raw_json, 'vendor_name') ?? $invoice->vendor_name_raw ?? $invoice->vendor_name ?? optional($invoice->vendor)->vendor_name ?? '-' }}
                            </td>

                            <td>{{ $invoice->gstin ?? $invoice->gst_number ?? $invoice->vendor_gstin ?? '-' }}</td>

                            <td>
                                {{ $invoice->invoice_date
                                    ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y')
                                    : '-' }}
                            </td>

                            <td>₹ {{ number_format($invoice->total_amount ?? $invoice->grand_total ?? $invoice->amount ?? 0, 2) }}</td>

                            <td>
                                @php
                                    $accuracy = $invoice->confidence_score;

                                    if ((is_null($accuracy) || (float) $accuracy <= 0) && is_array($invoice->raw_json)) {
                                        $accuracy = data_get($invoice->raw_json, 'matching.combined_confidence');
                                    }
                                @endphp

                                @if(!is_null($accuracy) && (float) $accuracy > 0)
                                    <span class="badge 
                                        @if($accuracy >= 80)
                                            bg-success
                                        @elseif($accuracy >= 50)
                                            bg-warning text-dark
                                        @else
                                            bg-danger
                                        @endif">
                                        {{ rtrim(rtrim(number_format($accuracy, 2), '0'), '.') }}%
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge 
                                    @switch($invoice->status)
                                        @case('draft') bg-secondary @break
                                        @case('needs_review') bg-warning @break
                                        @case('verified') bg-info @break
                                        @case('approved') bg-success @break
                                        @case('paid') bg-dark @break
                                        @case('duplicate') bg-danger @break
                                    @endswitch">
                                    {{ ucfirst(str_replace('_',' ', $invoice->status)) }}
                                </span>
                            </td>

                            <td>

                                <a href="{{ route('finance.purchase_invoices.show', $invoice->id) }}"
                                   class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="{{ route('finance.purchase_invoices.edit', $invoice->id) }}"
                                   class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                @if($invoice->status == 'needs_review')
                                    <form action="{{ route('finance.purchase_invoices.verify', $invoice->id) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-info">
                                            <i class="bi bi-check2-square"></i>
                                        </button>
                                    </form>
                                @endif

                                @if($invoice->status == 'verified')
                                    <form action="{{ route('finance.purchase_invoices.approve', $invoice->id) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-success">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    </form>
                                @endif

                                @if($invoice->status == 'approved')
                                    <form action="{{ route('finance.purchase_invoices.markPaid', $invoice->id) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-dark">
                                            <i class="bi bi-cash-stack"></i>
                                        </button>
                                    </form>
                                @endif

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">
                                No Purchase Invoices Found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

</div>

@endsection