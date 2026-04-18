@extends('layouts.app')

@section('title', 'Payment Batches')

@section('content')
<div class="container-fluid">
    <!-- 🔍 Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Filter Payment Batches</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('payments.batches') }}">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Batch reference...">
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="pending_approval" {{ request('status') === 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" 
                                       value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" 
                                       value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="per_page" class="form-label">Per Page</label>
                                <select class="form-select" id="per_page" name="per_page">
                                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 📊 Payment Batches Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Payment Batches</h5>
                    <span class="badge bg-primary">{{ $batches->total() }} batches</span>
                </div>
                <div class="card-body p-0">
                    @if($batches->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Batch Reference</th>
                                        <th>Total Amount</th>
                                        <th>Invoices</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Processed</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($batches as $batch)
                                        <tr>
                                            <td>
                                                <strong>{{ $batch->batch_reference }}</strong>
                                                @if($batch->approvedBy)
                                                    <br><small class="text-muted">By: {{ $batch->approvedBy->name }}</small>
                                                @endif
                                            </td>
                                            <td>₹{{ number_format($batch->total_amount, 2) }}</td>
                                            <td>{{ $batch->total_invoices }}</td>
                                            <td>
                                                <span class="badge bg-{{ getBatchStatusColor($batch->status) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $batch->status)) }}
                                                </span>
                                                @if($batch->status === 'completed_with_failures')
                                                    <br><small class="text-warning">With failures</small>
                                                @endif
                                            </td>
                                            <td>{{ $batch->created_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                @if($batch->processed_at)
                                                    {{ $batch->processed_at->format('M d, Y H:i') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('payments.batches.show', $batch->id) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    @if($batch->status === 'accountant_approval')
                                                        <button type="button" class="btn btn-success" 
                                                                onclick="approveAccountant({{ $batch->id }})" title="Approve (Accountant)">
                                                            <i class="fas fa-check"></i> Accountant
                                                        </button>
                                                        <button type="button" class="btn btn-danger" 
                                                                onclick="rejectAccountant({{ $batch->id }})" title="Reject (Accountant)">
                                                            <i class="fas fa-times"></i> Accountant
                                                        </button>
                                                    @endif

                                                    @if($batch->status === 'finance_manager_approval')
                                                        <button type="button" class="btn btn-success" 
                                                                onclick="approveFinanceManager({{ $batch->id }})" title="Approve (Finance Manager)">
                                                            <i class="fas fa-check"></i> Finance Manager
                                                        </button>
                                                        <button type="button" class="btn btn-danger" 
                                                                onclick="rejectFinanceManager({{ $batch->id }})" title="Reject (Finance Manager)">
                                                            <i class="fas fa-times"></i> Finance Manager
                                                        </button>
                                                    @endif

                                                    @if($batch->status === 'pending')
                                                        <button type="button" class="btn btn-sm btn-info" 
                                                                onclick="processBatch({{ $batch->id }})" title="Process">
                                                            <i class="fas fa-play"></i>
                                                        </button>
                                                    @endif

                                                    <a href="{{ route('payments.batches.export', $batch->id) }}" 
                                                       class="btn btn-sm btn-outline-success" title="Export Excel">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <div>
                                Showing {{ $batches->firstItem() }} to {{ $batches->lastItem() }} 
                                of {{ $batches->total() }} entries
                            </div>
                            {{ $batches->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox text-muted fa-3x mb-3"></i>
                            <h6>No payment batches found</h6>
                            <p class="text-muted">
                                @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                                    No batches match your search criteria.
                                @else
                                    No payment batches have been created yet.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approvalForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="actionType" name="action_type">
                    <input type="hidden" id="batchId" name="batch_id">
                    
                    <div id="approveContent" style="display: none;">
                        <p>Are you sure you want to approve this payment batch?</p>
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks (optional)</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3" 
                                      placeholder="Add any remarks for this approval..."></textarea>
                        </div>
                    </div>
                    
                    <div id="rejectContent" style="display: none;">
                        <p>Are you sure you want to reject this payment batch?</p>
                        <div class="mb-3">
                            <label for="reject_remarks" class="form-label">Rejection Reason *</label>
                            <textarea class="form-control" id="reject_remarks" name="reject_remarks" rows="3" 
                                      placeholder="Please provide reason for rejection..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function approveAccountant(batchId) {
    document.getElementById('actionType').value = 'approve-accountant';
    document.getElementById('batchId').value = batchId;
    document.getElementById('approveContent').style.display = 'block';
    document.getElementById('rejectContent').style.display = 'none';
    document.getElementById('submitBtn').className = 'btn btn-success';
    document.getElementById('submitBtn').textContent = 'Approve (Accountant)';
    
    const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
    modal.show();
}

function approveFinanceManager(batchId) {
    document.getElementById('actionType').value = 'approve-finance-manager';
    document.getElementById('batchId').value = batchId;
    document.getElementById('approveContent').style.display = 'block';
    document.getElementById('rejectContent').style.display = 'none';
    document.getElementById('submitBtn').className = 'btn btn-success';
    document.getElementById('submitBtn').textContent = 'Approve (Finance Manager)';
    
    const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
    modal.show();
}

function rejectAccountant(batchId) {
    document.getElementById('actionType').value = 'reject-accountant';
    document.getElementById('batchId').value = batchId;
    document.getElementById('approveContent').style.display = 'none';
    document.getElementById('rejectContent').style.display = 'block';
    document.getElementById('submitBtn').className = 'btn btn-danger';
    document.getElementById('submitBtn').textContent = 'Reject (Accountant)';
    
    const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
    modal.show();
}

function rejectFinanceManager(batchId) {
    document.getElementById('actionType').value = 'reject-finance-manager';
    document.getElementById('batchId').value = batchId;
    document.getElementById('approveContent').style.display = 'none';
    document.getElementById('rejectContent').style.display = 'block';
    document.getElementById('submitBtn').className = 'btn btn-danger';
    document.getElementById('submitBtn').textContent = 'Reject (Finance Manager)';
    
    const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
    modal.show();
}

// Legacy functions for backward compatibility
function approveBatch(batchId) {
    approveAccountant(batchId);
}

function rejectBatch(batchId) {
    rejectAccountant(batchId);
}

document.getElementById('approvalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const actionType = document.getElementById('actionType').value;
    const batchId = document.getElementById('batchId').value;
    const formData = new FormData(this);
    
    let url, method;
    
    switch(actionType) {
        case 'approve-accountant':
            url = `{{ route("payments.batches.approve-accountant", ":id") }}`.replace(':id', batchId);
            method = 'POST';
            break;
        case 'approve-finance-manager':
            url = `{{ route("payments.batches.approve-finance-manager", ":id") }}`.replace(':id', batchId);
            method = 'POST';
            break;
        case 'reject-accountant':
            url = `{{ route("payments.batches.reject-accountant", ":id") }}`.replace(':id', batchId);
            method = 'POST';
            break;
        case 'reject-finance-manager':
            url = `{{ route("payments.batches.reject-finance-manager", ":id") }}`.replace(':id', batchId);
            method = 'POST';
            break;
        default:
            // Legacy fallback
            url = `{{ route("payments.batches.approve-accountant", ":id") }}`.replace(':id', batchId);
            method = 'POST';
            break;
    }
    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success || data.message) {
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});
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
