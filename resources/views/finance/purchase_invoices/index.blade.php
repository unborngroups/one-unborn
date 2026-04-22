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

    <div class="d-flex justify-content-between align-items-center mb-3">
        
        
    </div>

    @if(!empty($lastFetchStatus))
    <div class="alert alert-{{ $lastFetchStatus['level'] === 'error' ? 'danger' : ($lastFetchStatus['level'] === 'success' ? 'success' : 'info') }} py-2 mb-3" role="alert">
        <div class="fw-semibold">Mail Fetch Status: {{ $lastFetchStatus['message'] ?? 'Mail checked.' }}</div>
        @if(!empty($lastFetchStatus['checked_at']))
        <div class="small mt-1">
            Checked at: {{ \Carbon\Carbon::parse($lastFetchStatus['checked_at'])->format('d-M-Y h:i A') }}
        </div>
        @endif
    </div>
    @endif

    <div class="mb-2">
        <h4 class="mb-0">
            @if(request('status') === 'failed')
                Failed Invoices
            @elseif(request('status') === 'needs_review')
                Needs Review
            @elseif(request('status') === 'verified')
                Verified Invoices
            @elseif(request('status') === 'approved')
                Approved Invoices
            @elseif(request('status') === 'paid')
                Paid Invoices
            @else
                Purchase Invoice Automation
            @endif
        </h4>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-2 mb-4">

            @if(isset($mailReadDays))
            <span class="text-info small me-2">
                <i class="bi bi-calendar-range"></i>
                Showing invoices from last <strong>{{ $mailReadDays }} days</strong>
            </span>
            @endif

            @if(isset($lastMailReadAt) && $lastMailReadAt)
            <span class="text-muted small me-2">
                <i class="bi bi-envelope-check"></i>
                Last mail read: <strong>{{ \Carbon\Carbon::parse($lastMailReadAt)->format('d-M-Y h:i A') }}</strong>
            </span>
            @else
            <span class="text-muted small me-2"><i class="bi bi-envelope"></i> No mail read yet</span>
            @endif

            <a href="{{ route('finance.purchase_invoices.index', ['status' => 'needs_review']) }}"
               class="btn btn-warning btn-sm {{ request('status') === 'needs_review' ? 'active' : '' }}">
                Needs Review
            </a>

            <a href="{{ route('finance.purchase_invoices.index', ['status' => 'verified']) }}"
               class="btn btn-info btn-sm {{ request('status') === 'verified' ? 'active' : '' }}">
                Verified
            </a>

            <a href="{{ route('finance.purchase_invoices.index', ['status' => 'approved']) }}"
               class="btn btn-success btn-sm {{ request('status') === 'approved' ? 'active' : '' }}">
                Approved
            </a>

            <a href="{{ route('finance.purchase_invoices.index', ['status' => 'paid']) }}"
               class="btn btn-dark btn-sm {{ request('status') === 'paid' ? 'active' : '' }}">
                Paid
            </a>

            <a href="{{ route('finance.purchase_invoices.index', ['status' => 'failed']) }}"
                    class="btn btn-danger btn-sm {{ request('status') === 'failed' ? 'active' : '' }}">
                Failed
            </a>

            <a href="{{ route('finance.purchase_invoices.index') }}
               class="btn btn-secondary btn-sm {{ request('status') === null ? 'active' : '' }}">
                All
            </a>

            <button type="button" class="btn btn-primary btn-sm" id="fetchNowBtn" onclick="fetchNow()">
                <i class="bi bi-envelope-arrow-down" id="fetchIcon"></i>
                <span class="spinner-border spinner-border-sm d-none" id="fetchSpinner" role="status"></span>
                <span id="fetchBtnText">Fetch Now</span>
            </button>

    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark-primary">
                    <tr>
                        @if(request('status') === 'failed')
                            <th>#</th>
                            <th>Vendor Name</th>
                            <th>GST Number</th>
                            <th>Failure Details</th>
                        @else
                            <th>#</th>
                            <th>Invoice No</th>
                            <th>Vendor</th>
                            <th>GSTIN</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Accuracy</th>
                            <th>Status</th>
                            <th width="250">Action</th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            @php
                                $invoiceNoDisplay = trim((string) ($invoice->invoice_no ?? ''));
                                $rawInvoiceNo = trim((string) data_get($invoice->raw_json, 'invoice_number', ''));
                                if ($invoiceNoDisplay !== '' && str_starts_with(strtoupper($invoiceNoDisplay), 'GMAIL-') && $rawInvoiceNo !== '') {
                                    $invoiceNoDisplay = $rawInvoiceNo;
                                }
                            @endphp

                            @if(request('status') !== 'failed')
                            <td>
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
                                        @case('failed') bg-danger @break
                                    @endswitch">
                                    {{ ucfirst(str_replace('_',' ', $invoice->status)) }}
                                </span>
                            </td>
                            @endif

                            @if(request('status') === 'failed')
                            <td>
                                {{ data_get($invoice->raw_json, 'vendor_name') ?? $invoice->vendor_name_raw ?? $invoice->vendor_name ?? optional($invoice->vendor)->vendor_name ?? '-' }}
                            </td>

                            <td>{{ $invoice->gstin ?? $invoice->gst_number ?? $invoice->vendor_gstin ?? '-' }}</td>

                            <td>
                                @php
                                    $failureReason = trim((string) (
                                        data_get($invoice->raw_json, 'import_failure_reason')
                                        ?? data_get($invoice->raw_json, 'parse_error')
                                        ?? data_get($invoice->raw_json, 'error')
                                        ?? optional($invoice->emailLog)->error_message
                                        ?? ''
                                    ));

                                    $failureStage = trim((string) (data_get($invoice->raw_json, 'failure_stage') ?? ''));
                                    $failureSource = trim((string) (data_get($invoice->raw_json, 'failure_source') ?? ''));
                                @endphp

                                @if($failureReason !== '')
                                    <div class="small text-danger fw-semibold">{{ $failureReason }}</div>
                                    @if($failureStage !== '' || $failureSource !== '')
                                        <div class="small text-muted">
                                            {{ $failureStage !== '' ? 'Stage: ' . ucfirst($failureStage) : '' }}
                                            {{ $failureSource !== '' ? ($failureStage !== '' ? ' | ' : '') . 'Source: ' . str_replace('_', ' ', ucfirst($failureSource)) : '' }}
                                        </div>
                                    @endif
                                    <button class="btn btn-link btn-sm text-danger" data-bs-toggle="modal" data-bs-target="#failureModal{{ $invoice->id }}">
                                        <i class="bi bi-info-circle"></i> Full Details
                                    </button>

                                    <!-- Failure Details Modal -->
                                    <div class="modal fade" id="failureModal{{ $invoice->id }}" tabindex="-1" aria-labelledby="failureModalLabel{{ $invoice->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="failureModalLabel{{ $invoice->id }}">Invoice Failure Details - {{ $invoiceNoDisplay }}</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label class="fw-semibold">Invoice No:</label>
                                                            <p>{{ $invoiceNoDisplay !== '' ? $invoiceNoDisplay : '-' }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="fw-semibold">Status:</label>
                                                            <p><span class="badge bg-danger">{{ ucfirst(str_replace('_', ' ', $invoice->status)) }}</span></p>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label class="fw-semibold">Vendor:</label>
                                                            <p>{{ data_get($invoice->raw_json, 'vendor_name') ?? $invoice->vendor_name_raw ?? $invoice->vendor_name ?? optional($invoice->vendor)->vendor_name ?? '-' }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="fw-semibold">GSTIN:</label>
                                                            <p>{{ $invoice->gstin ?? $invoice->gst_number ?? $invoice->vendor_gstin ?? '-' }}</p>
                                                        </div>
                                                    </div>

                                                    <hr>

                                                    <h6 class="fw-bold text-danger mb-3">Failure Information</h6>

                                                    <div class="mb-3">
                                                        <label class="fw-semibold">Primary Reason:</label>
                                                        <p class="text-danger">{{ $failureReason !== '' ? $failureReason : '-' }}</p>
                                                    </div>

                                                    @if($failureStage !== '')
                                                        <div class="mb-3">
                                                            <label class="fw-semibold">Failure Stage:</label>
                                                            <p>{{ ucfirst($failureStage) }}</p>
                                                        </div>
                                                    @endif

                                                    @if($failureSource !== '')
                                                        <div class="mb-3">
                                                            <label class="fw-semibold">Failure Source:</label>
                                                            <p>{{ str_replace('_', ' ', ucfirst($failureSource)) }}</p>
                                                        </div>
                                                    @endif

                                                    @if(optional($invoice->emailLog)->sender)
                                                        <div class="mb-3">
                                                            <label class="fw-semibold">Email From:</label>
                                                            <p>{{ optional($invoice->emailLog)->sender }}</p>
                                                        </div>
                                                    @endif

                                                    @if(optional($invoice->emailLog)->subject)
                                                        <div class="mb-3">
                                                            <label class="fw-semibold">Email Subject:</label>
                                                            <p>{{ optional($invoice->emailLog)->subject }}</p>
                                                        </div>
                                                    @endif

                                                    @php
                                                        $attachmentPath = trim((string) (optional($invoice->emailLog)->attachment_path ?? ''));
                                                        if ($attachmentPath === '' && !empty($invoice->po_invoice_file)) {
                                                            $attachmentPath = 'images/poinvoice_files/' . $invoice->po_invoice_file;
                                                        }

                                                        $attachmentName = $attachmentPath !== '' ? basename($attachmentPath) : '-';
                                                        $attachmentExt = strtolower((string) pathinfo($attachmentName, PATHINFO_EXTENSION));
                                                        $attachmentType = match ($attachmentExt) {
                                                            'pdf' => 'PDF',
                                                            'txt', 'log' => 'Text',
                                                            'csv', 'xls', 'xlsx' => 'Excel',
                                                            default => ($attachmentExt !== '' ? strtoupper($attachmentExt) : '-'),
                                                        };
                                                    @endphp

                                                    <div class="mb-3">
                                                        <label class="fw-semibold">Attachment Type:</label>
                                                        <p>{{ $attachmentType }}</p>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="fw-semibold">Attachment File:</label>
                                                        <p>{{ $attachmentName }}</p>
                                                    </div>

                                                    <hr>

                                                    @php
                                                        $rawJson = is_array($invoice->raw_json) ? $invoice->raw_json : [];
                                                        $readInvoiceNo = trim((string) (
                                                            data_get($rawJson, 'invoice_number')
                                                            ?? $invoice->invoice_no
                                                            ?? ''
                                                        ));
                                                        $readInvoiceDate = trim((string) (
                                                            data_get($rawJson, 'invoice_date')
                                                            ?? ($invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y') : '')
                                                        ));
                                                        $readVendor = trim((string) (
                                                            data_get($rawJson, 'vendor_name')
                                                            ?? $invoice->vendor_name_raw
                                                            ?? $invoice->vendor_name
                                                            ?? ''
                                                        ));
                                                        $readGstin = trim((string) (
                                                            data_get($rawJson, 'gst')
                                                            ?? data_get($rawJson, 'gstin')
                                                            ?? $invoice->gstin
                                                            ?? $invoice->gst_number
                                                            ?? $invoice->vendor_gstin
                                                            ?? ''
                                                        ));
                                                        $readArc = (float) (data_get($rawJson, 'arc', $invoice->arc_amount ?? 0));
                                                        $readOtc = (float) (data_get($rawJson, 'otc', $invoice->otc_amount ?? 0));
                                                        $readStatic = (float) (data_get($rawJson, 'static', $invoice->static_amount ?? 0));
                                                        $readTotal = (float) (data_get($rawJson, 'total', $invoice->total_amount ?? $invoice->grand_total ?? $invoice->amount ?? 0));
                                                        $readConfidence = data_get($rawJson, 'matching.combined_confidence', $invoice->confidence_score);
                                                    @endphp

                                                    <h6 class="fw-bold mb-2">Invoice Read Data:</h6>
                                                    <div class="table-responsive" style="max-height: 260px; overflow-y: auto;">
                                                        <table class="table table-sm table-bordered mb-0">
                                                            <tbody>
                                                                <tr>
                                                                    <th style="width: 180px;">Invoice Number</th>
                                                                    <td>{{ $readInvoiceNo !== '' ? $readInvoiceNo : '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Invoice Date</th>
                                                                    <td>{{ $readInvoiceDate !== '' ? $readInvoiceDate : '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Vendor Read</th>
                                                                    <td>{{ $readVendor !== '' ? $readVendor : '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>GST Read</th>
                                                                    <td>{{ $readGstin !== '' ? $readGstin : '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>ARC Read</th>
                                                                    <td>₹ {{ number_format($readArc, 2) }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>OTC Read</th>
                                                                    <td>₹ {{ number_format($readOtc, 2) }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Static Read</th>
                                                                    <td>₹ {{ number_format($readStatic, 2) }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Total Read</th>
                                                                    <td>₹ {{ number_format($readTotal, 2) }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Read Accuracy</th>
                                                                    <td>{{ !is_null($readConfidence) ? rtrim(rtrim(number_format((float) $readConfidence, 2), '0'), '.') . '%' : '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Source File</th>
                                                                    <td>
                                                                        <a href="{{ route('finance.purchases.download-source-pdf', $invoice->id) }}" class="btn btn-sm btn-outline-primary">
                                                                            Open Attachment
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            @endif

                            @if(request('status') !== 'failed')
                            <td>

                                <a href="{{ route('finance.purchase_invoices.show', $invoice->id) }}"
                                   class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="{{ route('finance.purchase_invoices.edit', $invoice->id) }}"
                                   class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                @if($invoice->po_invoice_file)
                                    <a href="{{ asset('images/poinvoice_files/' . $invoice->po_invoice_file) }}"
                                       download="{{ $invoice->invoice_no ?? 'invoice_' . $invoice->id . '.pdf' }}"
                                       target="_blank"
                                       class="btn btn-sm btn-success"
                                       title="Download Invoice PDF">
                                        <i class="bi bi-download"></i>
                                    </a>
                                @endif

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
                            @endif
                        </tr>
                    @empty
                        <tr>
                                <td colspan="{{ request('status') === 'failed' ? 4 : 9 }}" class="text-center">
                                No Purchase Invoices Found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

    <!-- Pagination Links -->
    @if(method_exists($invoices, 'links'))
        <div class="d-flex justify-content-center mt-4">
            {{ $invoices->links() }}
        </div>
    @endif

</div>

<script>
function fetchNow() {
    const btn = document.getElementById('fetchNowBtn');
    const icon = document.getElementById('fetchIcon');
    const spinner = document.getElementById('fetchSpinner');
    const btnText = document.getElementById('fetchBtnText');
    
    // Show loading state
    icon.classList.add('d-none');
    spinner.classList.remove('d-none');
    btnText.textContent = 'Fetching...';
    btn.disabled = true;
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                     document.querySelector('input[name="_token"]')?.value;
    
    // Make AJAX request
    fetch('{{ route("finance.purchases.fetch-gmail") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        // Reset button state
        resetFetchButton();
        
        // Show appropriate message
        if (data.success) {
            showAlert('success', data.message);
            // Reload page after 2 seconds to show new data
            setTimeout(() => location.reload(), 2000);
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        resetFetchButton();
        showAlert('error', 'Fetch failed. Please try again.');
    });
    
    // Safety timeout - reset button after 60 seconds max
    setTimeout(() => {
        if (btn.disabled) {
            resetFetchButton();
            showAlert('warning', 'Fetch is taking longer than expected. Please check results later.');
        }
    }, 60000);
}

function resetFetchButton() {
    const btn = document.getElementById('fetchNowBtn');
    const icon = document.getElementById('fetchIcon');
    const spinner = document.getElementById('fetchSpinner');
    const btnText = document.getElementById('fetchBtnText');
    
    icon.classList.remove('d-none');
    spinner.classList.add('d-none');
    btnText.textContent = 'Fetch Now';
    btn.disabled = false;
}

function showAlert(type, message) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create new alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at the top of the container
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto-remove after 10 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 10000);
}
</script>

@endsection