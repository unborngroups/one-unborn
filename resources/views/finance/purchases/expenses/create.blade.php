@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Add Expense</h4>

    <form method="POST" action="{{ route('finance.expenses.store') }}">
        @csrf

        <div class="mb-3">
            <label>Expense Type</label>
            <input type="text" name="expense_type" class="form-control">
        </div>

        <div class="mb-3">
            <label>Date</label>
            <input type="date" name="expense_date" class="form-control">
        </div>

        <div class="mb-3">
            <label>Amount</label>
            <input type="number" name="amount" class="form-control">
        </div>

        <button class="btn btn-success">Save</button>
        <a href="{{ route('finance.expenses.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
