@extends('layouts.app')

@section('title', 'Pending Invoices')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Pending Invoices</h4>
                <div>
                    <a href="{{ route('payments.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                    <a href="{{ route('payments.batches.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Payment Batch
                    </a>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Invoices Ready for Payment</h5>
                </div>
                <div class="card-body">
                    @if($invoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Vendor</th>
                                        <th>PAN</th>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                        <th>Days Overdue</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices as $invoice)
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
                                            <td>
                                                @if($invoice->vendor->pan)
                                                    <span class="badge bg-secondary">{{ $invoice->vendor->pan }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>Rs. {{ number_format($invoice->grand_total, 2) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $invoice->due_date->isPast() ? 'danger' : 'success' }}">
                                                    {{ $invoice->due_date->format('M d, Y') }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($invoice->due_date->isPast())
                                                    <span class="badge bg-warning">
                                                        {{ $invoice->due_date->diffInDays(now()) }} days
                                                    </span>
                                                @else
                                                    <span class="badge bg-info">
                                                        {{ $invoice->due_date->diffInDays(now()) }} days
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-primary" 
                                                            onclick="selectInvoice({{ $invoice->id }})">
                                                        <i class="fas fa-check"></i> Select
                                                    </button>
                                                    <a href="{{ route('finance.purchase_invoices.show', $invoice->id) }}" 
                                                       class="btn btn-sm btn-info" title="View Details">
                                                        <i class="fas fa-eye"></i>
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
                                Showing {{ $invoices->firstItem() }} to {{ $invoices->lastItem() }} 
                                of {{ $invoices->total() }} entries
                            </div>
                            {{ $invoices->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                            <h6>No Pending Invoices</h6>
                            <p class="text-muted">
                                All invoices are processed or no invoices are due for payment.
                            </p>
                            <a href="{{ route('payments.dashboard') }}" class="btn btn-primary">
                                Go to Dashboard
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Selection Modal -->
<div class="modal fade" id="selectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Payment Batch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('payments.batches.create') }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Selected invoice will be added to a new payment batch for approval.
                    </div>
                    
                    <div class="mb-3">
                        <label for="selected_invoices" class="form-label">Selected Invoices</label>
                        <textarea class="form-control" id="selected_invoices" name="selected_invoices" rows="3" readonly></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="batch_notes" class="form-label">Batch Notes (optional)</label>
                        <textarea class="form-control" id="batch_notes" name="batch_notes" rows="3" 
                                  placeholder="Add any notes for this payment batch..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Batch
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let selectedInvoices = [];

function selectInvoice(invoiceId) {
    if (!selectedInvoices.includes(invoiceId)) {
        selectedInvoices.push(invoiceId);
        updateSelectionModal();
    }
}

function updateSelectionModal() {
    const textarea = document.getElementById('selected_invoices');
    textarea.value = selectedInvoices.join(', ');
}

// Clear selection when modal is hidden
document.getElementById('selectionModal').addEventListener('hidden.bs.modal', function () {
    selectedInvoices = [];
    updateSelectionModal();
});
</script>
@endsection
