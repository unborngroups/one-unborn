@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Invoice</h1>
    <form method="POST" action="{{ route('finance.invoices.store') }}">
        @csrf
        <input type="hidden" name="deliverable_id" value="{{ $deliverable->id }}">
        <div class="mb-3">
            <label>Client Name</label>
            <input type="text" class="form-control" value="{{ $deliverable->feasibility->client->client_name ?? '' }}" readonly>
        </div>
        <div class="mb-3">
            <label>Company Name</label>
            <input type="text" class="form-control" value="{{ $deliverable->feasibility->company->company_name ?? '' }}" readonly>
        </div>
        <div class="mb-3">
            <label>Invoice Date</label>
            <input type="date" class="form-control" name="invoice_date" required>
        </div>
        <div class="mb-3">
            <label>Due Date</label>
            <input type="date" class="form-control" name="due_date">
        </div>
        <div class="mb-3">
            <label>Amount</label>
            <input type="number" step="0.01" class="form-control" name="amount" required>
        </div>
        <!-- Add more fields as needed -->
        <button type="submit" class="btn btn-primary">Create Invoice</button>
    </form>
</div>
@endsection
