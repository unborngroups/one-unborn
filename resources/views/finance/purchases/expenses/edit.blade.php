@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Expense</h4>

    <form method="POST" action="{{ route('finance.expenses.update', $expense->id) }}">
        @csrf
        @method('PUT')

        <div class="card">
        <div class="mb-3">
            <label>Expense Type</label>
            <input type="text" name="expense_type" class="form-control" value="{{ $expense->expense_type }}">
        </div>

        <div class="mb-3">
            <label>Date</label>
            <input type="date" name="expense_date" class="form-control" value="{{ $expense->expense_date }}">
        </div>

        <div class="mb-3">
            <label>Amount</label>
            <input type="number" name="amount" class="form-control" value="{{ $expense->amount }}">
        </div>

        <button class="btn btn-success">Update</button>
        <a href="{{ route('finance.expenses.index') }}" class="btn btn-secondary">Back</a>
    
        </div>
    </form>
</div>
@endsection
