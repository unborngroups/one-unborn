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
        <h4 class="mb-0">Purchase Invoice Automation</h4>

        <div>
            <a href="{{ route('finance.purchase_invoices.index', ['status' => 'needs_review']) }}"
               class="btn btn-warning btn-sm">
                Needs Review
            </a>

            <a href="{{ route('finance.purchase_invoices.index', ['status' => 'verified']) }}"
               class="btn btn-info btn-sm">
                Verified
            </a>

            <a href="{{ route('finance.purchase_invoices.index', ['status' => 'approved']) }}"
               class="btn btn-success btn-sm">
                Approved
            </a>

            <a href="{{ route('finance.purchase_invoices.index', ['status' => 'paid']) }}"
               class="btn btn-dark btn-sm">
                Paid
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Vendor</th>
                        <th>GSTIN</th>
                        <th>Invoice No</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Confidence</th>
                        <th>Status</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                {{ optional($invoice->vendor)->vendor_name ?? $invoice->vendor_name_raw ?? $invoice->vendor_name ?? '-' }}
                            </td>

                            <td>{{ $invoice->gstin ?? $invoice->gst_number ?? $invoice->vendor_gstin ?? '-' }}</td>

                            <td>{{ $invoice->invoice_no ?? $invoice->invoice_number ?? '-' }}</td>

                            <td>
                                {{ $invoice->invoice_date
                                    ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y')
                                    : '-' }}
                            </td>

                            <td>₹ {{ number_format($invoice->total_amount ?? $invoice->grand_total ?? $invoice->amount ?? 0, 2) }}</td>

                            <td>
                                <span class="badge 
                                    @if($invoice->confidence_score >= 80)
                                        bg-success
                                    @elseif($invoice->confidence_score >= 50)
                                        bg-warning
                                    @else
                                        bg-danger
                                    @endif">
                                    {{ $invoice->confidence_score }}%
                                </span>
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
                                   class="btn btn-primary btn-sm">
                                    View
                                </a>

                                <a href="{{ route('finance.purchase_invoices.edit', $invoice->id) }}"
                                   class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                @if($invoice->status == 'needs_review')
                                    <form action="{{ route('finance.purchase_invoices.verify', $invoice->id) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        <button class="btn btn-info btn-sm">
                                            Verify
                                        </button>
                                    </form>
                                @endif

                                @if($invoice->status == 'verified')
                                    <form action="{{ route('finance.purchase_invoices.approve', $invoice->id) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        <button class="btn btn-success btn-sm">
                                            Approve
                                        </button>
                                    </form>
                                @endif

                                @if($invoice->status == 'approved')
                                    <form action="{{ route('finance.purchase_invoices.markPaid', $invoice->id) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        <button class="btn btn-dark btn-sm">
                                            Mark Paid
                                        </button>
                                    </form>
                                @endif

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">
                                No invoices found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

</div>

@endsection