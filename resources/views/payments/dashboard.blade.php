@extends('layouts.app')

@section('title', 'Payment Dashboard')

@section('content')
<div class="container-fluid">
    <!-- 📊 Payment Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Payment Overview - {{ ucfirst($period) }}</h5>
                    <div class="card-actions">
                        <select class="form-select form-select-sm" id="periodFilter" onchange="location.href='?period='+this.value">
                            <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Today</option>
                            <option value="week" {{ $period === 'week' ? 'selected' : '' }}>This Week</option>
                            <option value="month" {{ $period === 'month' ? 'selected' : '' }}>This Month</option>
                            <option value="quarter" {{ $period === 'quarter' ? 'selected' : '' }}>This Quarter</option>
                            <option value="year" {{ $period === 'year' ? 'selected' : '' }}>This Year</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-primary">{{ number_format($stats['total_invoices']) }}</h3>
                                <p class="text-muted mb-0">Total Invoices</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-success">₹{{ number_format($stats['paid_amount'], 2) }}</h3>
                                <p class="text-muted mb-0">Paid Amount</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-warning">₹{{ number_format($stats['pending_amount'], 2) }}</h3>
                                <p class="text-muted mb-0">Pending Amount</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h3 class="text-danger">{{ $stats['overdue_count'] }}</h3>
                                <p class="text-muted mb-0">Overdue Invoices</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- 📋 Pending Invoices -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Pending Invoices</h5>
                    <a href="{{ route('payments.pending-invoices') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    @if($pendingInvoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Vendor</th>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingInvoices->take(5) as $invoice)
                                        <tr>
                                            <td>{{ $invoice->invoice_no }}</td>
                                            <td>
                                                <div>
                                                    <strong>{{ $invoice->vendor->vendor_name }}</strong>
                                                    @if($invoice->vendor->pan)
                                                        <br><small class="text-muted">PAN: {{ $invoice->vendor->pan }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>₹{{ number_format($invoice->grand_total, 2) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $invoice->due_date->isPast() ? 'danger' : 'success' }}">
                                                    {{ $invoice->due_date->format('M d') }}
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary" 
                                                        onclick="selectInvoice({{ $invoice->id }})">
                                                    Select
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                            <h6>No pending invoices</h6>
                            <p class="text-muted">All invoices are processed or no invoices are due for payment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 🔄 Recent Payment Batches -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Payment Batches</h5>
                    <a href="{{ route('payments.batches') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    @if($recentBatches->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Batch #</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBatches->take(5) as $batch)
                                        <tr>
                                            <td>{{ $batch->batch_reference }}</td>
                                            <td>₹{{ number_format($batch->total_amount, 2) }}</td>
                                            <td>
                                                <span class="badge bg-{{ getBatchStatusColor($batch->status) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $batch->status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $batch->created_at->format('M d, H:i') }}</td>
                                            <td>
                                                <a href="{{ route('payments.batches.show', $batch->id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-layer-group text-muted fa-3x mb-3"></i>
                            <h6>No payment batches</h6>
                            <p class="text-muted">No payment batches have been created yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ⚡ Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <button type="button" class="btn btn-success" onclick="runAutoDetection()">
                                    <i class="fas fa-robot me-2"></i>Run Auto Detection
                                </button>
                                <p class="text-muted small mb-0 mt-2">Automatically detect invoices ready for payment</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <button type="button" class="btn btn-primary" onclick="createManualBatch()">
                                    <i class="fas fa-plus me-2"></i>Create Manual Batch
                                </button>
                                <p class="text-muted small mb-0 mt-2">Create payment batch from selected invoices</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <a href="{{ route('payments.batches') }}" class="btn btn-info">
                                    <i class="fas fa-list me-2"></i>View All Batches
                                </a>
                                <p class="text-muted small mb-0 mt-2">View and manage all payment batches</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for batch creation -->
<form id="batchForm" method="POST" action="{{ route('payments.batches.create') }}" style="display: none;">
    @csrf
    <input type="hidden" name="invoice_ids" id="selectedInvoiceIds">
</form>

<script>
function getBatchStatusColor(status) {
    const colors = {
        'pending': 'secondary',
        'pending_approval': 'warning',
        'processing': 'info',
        'completed': 'success',
        'failed': 'danger',
        'cancelled': 'dark'
    };
    return colors[status] || 'secondary';
}

let selectedInvoices = [];

function selectInvoice(invoiceId) {
    const index = selectedInvoices.indexOf(invoiceId);
    if (index > -1) {
        selectedInvoices.splice(index, 1);
        event.target.classList.remove('btn-success');
        event.target.classList.add('btn-primary');
        event.target.textContent = 'Select';
    } else {
        selectedInvoices.push(invoiceId);
        event.target.classList.remove('btn-primary');
        event.target.classList.add('btn-success');
        event.target.textContent = 'Selected';
    }
}

function createManualBatch() {
    if (selectedInvoices.length === 0) {
        alert('Please select at least one invoice to create a batch.');
        return;
    }
    
    document.getElementById('selectedInvoiceIds').value = selectedInvoices.join(',');
    document.getElementById('batchForm').submit();
}

function runAutoDetection() {
    if (confirm('This will automatically detect and create payment batches for all due invoices. Continue?')) {
        fetch('{{ route("payments.run-auto-detection") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Auto payment detection job has been queued successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while running auto detection.');
        });
    }
}
</script>
@endsection

@php
function getBatchStatusColor($status) {
    $colors = [
        'pending' => 'secondary',
        'pending_approval' => 'warning',
        'processing' => 'info',
        'completed' => 'success',
        'failed' => 'danger',
        'cancelled' => 'dark'
    ];
    return $colors[$status] ?? 'secondary';
}
@endphp
