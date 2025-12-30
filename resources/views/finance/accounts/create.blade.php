@extends('layouts.app')

@section('content')
<div class="container">
<h4>Create Account</h4>


        @if ($errors->any())

            <div class="alert alert-danger">

                <ul class="mb-0">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif


<form method="POST" action="{{ route('finance.accounts.store') }}">
@csrf


<select name="account_group_id" class="form-control mb-2" required>
    <option value="">Select Account Group</option>
    @foreach($groups as $g)
        <option value="{{ $g->id }}">{{ $g->name }}</option>
        <option value="">Asset</option>
    @endforeach
</select>

<input type="text" name="account_name" class="form-control mb-2" placeholder="Account Name">

<input type="text" name="account_code" class="form-control mb-2" placeholder="Account Code">

<input type="number" step="0.01" name="opening_balance" class="form-control mb-2" placeholder="Opening Balance">

<select name="balance_type" class="form-control mb-2">
    <option value="Debit">Debit</option>
    <option value="Credit">Credit</option>
</select>

<button class="btn btn-success">Save</button>
</form>
</div>
@endsection
