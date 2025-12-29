@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4>Purchasing &amp; Payables</h4>
            <!-- <p class="text-muted mb-0">Track vendor invoices, expenses, and debit notes along with TDS/GST workflows.</p> -->
        </div>
        <!-- <a href="{{ route('finance.vendor-invoices.create') }}" class="btn btn-primary">New Vendor Invoice</a> -->
    </div>

    <div class="row gy-3">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Vendor Invoices</h5>
                    <p class="card-text">Capture invoice details, GST breakup, and maker/checker approvals before posting.</p>
                    <a href="{{ route('finance.vendor-invoices.index') }}" class="btn btn-outline-primary btn-sm">View invoices</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Expenses</h5>
                    <p class="card-text">Record inwards expenses, tag cost centres, and enforce expense policies.</p>
                    <a href="{{ route('finance.expenses.index') }}" class="btn btn-outline-primary btn-sm">View expenses</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Debit Notes</h5>
                    <p class="card-text">Issue credit or debit adjustments linked to original vendor invoices.</p>
                    <a href="{{ route('finance.debit-notes.index') }}" class="btn btn-outline-primary btn-sm">View notes</a>
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6>GST &amp; TDS Monitor</h6>
                    <p class="small text-muted">Required fields are enforced for GSTIN, HSN/SAC, TDS sections, and PAN validation before invoices can be approved.</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6>Approvals &amp; Auditing</h6>
                    <p class="small text-muted">Maker, checker, and approver remarks are logged for every transaction. Disputes and audits can surface activity history from this view.</p>
                </div>
            </div>
        </div>
    </div> -->
</div>

<style>
    .card {
        border: none;
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    .card-body {
        background-color: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem;
    }

    .card-title {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .card-text {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .btn-outline-primary {
        border-radius: 20px;
        padding: 0.35rem 0.9rem;
        font-size: 0.85rem;
    }

    .page-header h4 {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .page-header p {
        color: #6c757d;
        font-size: 0.85rem;
    }
</style>

@endsection
