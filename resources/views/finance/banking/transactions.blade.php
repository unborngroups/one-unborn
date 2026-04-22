@extends('layouts.app')

@section('content')
    <h4>{{ $bank->bank_name }} - Transactions</h4>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('finance.banking.transaction.store') }}">
@csrf
<input type="hidden" name="bank_account_id" value="{{ $bank->id }}">
<input type="date" name="transaction_date" class="form-control mb-2" value="{{ old('transaction_date') }}">
<select name="type" class="form-control mb-2">
<option value="Receipt" {{ old('type') === 'Receipt' ? 'selected' : '' }}>Receipt</option>
<option value="Payment" {{ old('type') === 'Payment' ? 'selected' : '' }}>Payment</option>
</select>
<input name="amount" class="form-control mb-2" placeholder="Amount" value="{{ old('amount') }}">
<input name="reference" class="form-control mb-2" placeholder="Reference" value="{{ old('reference') }}">
<button class="btn btn-primary">Add</button>
</form>

<hr>

<table class="table table-bordered">
<tr>
<th>Date</th><th>Type</th><th>Amount</th><th>Status</th><th></th>
</tr>
@foreach($transactions as $t)
<tr>
<td>{{ $t->transaction_date }}</td>
<td>{{ $t->type }}</td>
<td>{{ $t->amount }}</td>
<td>{{ $t->is_reconciled?'Reconciled':'Pending' }}</td>
<td>
@if(!$t->is_reconciled)
<a href="{{ route('finance.banking.reconcile',$t->id) }}"
 class="btn btn-sm btn-success">Reconcile</a>
@endif
</td>
</tr>
@endforeach
</table>

@endsection
