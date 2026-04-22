@extends('layouts.app')

@section('content')
    <h4>Add Bank</h4>
<form method="POST" action="{{ route('finance.banking.store') }}">
@csrf
<input name="bank_name" class="form-control mb-2" placeholder="Bank Name">
<input name="account_name" class="form-control mb-2" placeholder="Account Name">
<input name="account_number" class="form-control mb-2" placeholder="Account Number">
<input name="ifsc_code" class="form-control mb-2" placeholder="IFSC">
<input name="opening_balance" class="form-control mb-2" placeholder="Opening Balance">
<button class="btn btn-success">Save</button>
</form>

@endsection
