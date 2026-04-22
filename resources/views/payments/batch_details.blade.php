@extends('layouts.app')

@section('title', 'Payment Batch Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Payment Batch Details</h4>
                <a href="{{ route('payments.batches') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Batches
                </a>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Batch #{{ $batch->id }} - {{ $batch->batch_reference }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <h6 class="text-muted mb-2">Total Amount</h6>
                                <h4 class="text-primary">₹{{ number_format($batch->total_amount, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <h6 class="text-muted mb-2">Total Invoices</h6>
                                <h4 class="text-info">{{ $batch->total_invoices }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <h6 class="text-muted mb-2">Status</h6>
                                <h4>
                                    <span class="badge bg-{{ getBatchStatusColor($batch->status) }} text-white">
                                        {{ ucfirst($batch->status) }}
                                    </span>
                                </h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <h6 class="text-muted mb-2">Created</h6>
                                <h4 class="text-secondary">{{ $batch->created_at->format('M d, Y') }}</h4>
                            </div>
                        </div>
                    </div>

                    @if($batch->status === 'accountant_approval' && $batch->accountantApproval)
                        <div class="alert alert-info">
                            <strong>Accountant Approval:</strong> 
                            Approved by {{ $batch->accountantApproval->user->name }} 
                            on {{ $batch->accountantApproval->approved_at->format('M d, Y H:i') }}
                            @if($batch->accountantApproval->remarks)
                                <br><em>Remarks: {{ $batch->accountantApproval->remarks }}</em>
                            @endif
                        </div>
                    @endif

                    @if($batch->status === 'finance_manager_approval' && $batch->financeManagerApproval)
                        <div class="alert alert-success">
                            <strong>Finance Manager Approval:</strong> 
                            Approved by {{ $batch->financeManagerApproval->user->name }} 
                            on {{ $batch->financeManagerApproval->approved_at->format('M d, Y H:i') }}
                            @if($batch->financeManagerApproval->remarks)
                                <br><em>Remarks: {{ $batch->financeManagerApproval->remarks }}</em>
                            @endif
                        </div>
                    @endif

                    <div class="table-responsive mt-4">
                        <h6>Invoices in this Batch</h6>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Vendor</th>
                                    <th>PAN</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($batch->paymentTransactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->purchaseInvoice->invoice_no }}</td>
                                        <td>
                                            <div>
                                                <strong>{{ $transaction->purchaseInvoice->vendor->vendor_name }}</strong>
                                                @if($transaction->purchaseInvoice->vendor->pan)
                                                    <br><small class="text-muted">PAN: {{ $transaction->purchaseInvoice->vendor->pan }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($transaction->purchaseInvoice->vendor->pan)
                                                <span class="badge bg-secondary">{{ $transaction->purchaseInvoice->vendor->pan }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>₹{{ number_format($transaction->amount, 2) }}</td>
                                        <td>{{ $transaction->purchaseInvoice->due_date->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ getTransactionStatusColor($transaction->status) }}">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($batch->processing_notes)
                        <div class="mt-3">
                            <h6>Processing Notes</h6>
                            <div class="alert alert-light">
                                {{ $batch->processing_notes }}
                            </div>
                        </div>
                    @endif

                    @if($batch->failure_reason)
                        <div class="mt-3">
                            <h6>Failure Reason</h6>
                            <div class="alert alert-danger">
                                {{ $batch->failure_reason }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@php
function getTransactionStatusColor($status) {
    $colors = [
        'pending' => 'secondary',
        'processing' => 'primary',
        'completed' => 'success',
        'failed' => 'danger',
        'refunded' => 'warning',
    ];
    return $colors[$status] ?? 'secondary';
}
@endphp
