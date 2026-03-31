@extends('layouts.app')

@section('title', 'Purchase Invoices')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Purchase Invoices</h2>
        <div class="d-flex gap-2 align-items-center">
            {{--create--}}
            <a href="{{ route('finance.purchases.create') }}"
               class="btn btn-sm btn-info p-2 text-white"> <h2>+  create invoice</h2>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <pre style="margin:0;white-space:pre-wrap;">{{ session('success') }}</pre>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @php
        $vendorNameFailedCount = $purchases->filter(function ($purchase) {
            $reason = strtolower((string) data_get($purchase->raw_json, 'import_failure_reason', ''));
            return strtolower((string) ($purchase->status ?? '')) === 'failed'
                && str_contains($reason, 'vendor name');
        })->count();
    @endphp

    @if($vendorNameFailedCount > 0)
        <div class="alert alert-danger">
            {{ $vendorNameFailedCount }} invoice(s) failed: Vendor name mistake. Please correct vendor name in Vendor Master.
        </div>
    @endif

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
                    @forelse($purchases as $purchase)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                @php
                                    $invoiceNoDisplay = trim((string) ($purchase->invoice_no ?? ''));
                                    $rawInvoiceNo = trim((string) data_get($purchase->raw_json, 'invoice_number', ''));
                                    if ($invoiceNoDisplay !== '' && str_starts_with(strtoupper($invoiceNoDisplay), 'GMAIL-') && $rawInvoiceNo !== '') {
                                        $invoiceNoDisplay = $rawInvoiceNo;
                                    }
                                @endphp
                                {{ $invoiceNoDisplay !== '' ? $invoiceNoDisplay : '-' }}
                            </td>

                            <td>
                                {{ data_get($purchase->raw_json, 'vendor_name') ?? $purchase->vendor_name_raw ?? $purchase->vendor_name ?? optional($purchase->vendor)->vendor_name ?? '-' }}
                            </td>

                            <td>{{ $purchase->gstin ?? $purchase->gst_number ?? $purchase->vendor_gstin ?? '-' }}</td>

                            <td>
                                {{ $purchase->invoice_date 
                                    ? \Carbon\Carbon::parse($purchase->invoice_date)->format('d-m-Y') 
                                    : '-' }}
                            </td>

                            <td>
                                ₹ {{ number_format($purchase->total_amount, 2) }}
                            </td>

                            <td>
                                @php
                                    $accuracy = $purchase->confidence_score;

                                    if ((is_null($accuracy) || (float) $accuracy <= 0) && is_array($purchase->raw_json)) {
                                        $accuracy = data_get($purchase->raw_json, 'matching.combined_confidence');
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

                            {{-- STATUS --}}
                            <td>
                                @php
                                    $st = strtolower($purchase->status ?? '');
                                @endphp
                                @if($st === 'needs_review')
                                    <form action="{{ route('finance.purchases.approve', $purchase->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success"
                                                onclick="return confirm('Approve this invoice?')">
                                            <i class="bi bi-check-circle"></i> Approve
                                        </button>
                                    </form>
                                @else
                                    @php
                                        $statusMap = [
                                            'ok'       => ['label' => 'OK',       'class' => 'bg-success'],
                                            'higher'   => ['label' => 'Higher',   'class' => 'bg-danger'],
                                            'lower'    => ['label' => 'Lower',    'class' => 'bg-warning text-dark'],
                                            'verified' => ['label' => 'Verified', 'class' => 'bg-info text-dark'],
                                            'approved' => ['label' => 'Approved', 'class' => 'bg-primary'],
                                            'paid'     => ['label' => 'Paid',     'class' => 'bg-dark'],
                                            'failed'   => ['label' => 'Failed',   'class' => 'bg-danger'],
                                        ];
                                        $statusInfo = $statusMap[$st] ?? ['label' => ucfirst($st ?: 'Pending'), 'class' => 'bg-secondary'];
                                    @endphp
                                    <span class="badge {{ $statusInfo['class'] }}">{{ $statusInfo['label'] }}</span>
                                @endif
                            </td>

                            <td>

                                {{-- VIEW --}}
                                <a href="{{ route('finance.purchases.show', $purchase->id) }}"
                                   class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>

                                {{-- EDIT --}}
                                <a href="{{ route('finance.purchases.edit', $purchase->id) }}"
                                   class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <!-- {{-- PDF --}}
                                <a href="{{ route('finance.purchases.pdf', $purchase->id) }}"
                                   class="btn btn-sm btn-secondary"
                                   target="_blank">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a> -->

                                {{-- DELETE --}}
                                <form action="{{ route('finance.purchases.destroy', $purchase->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this invoice?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>

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