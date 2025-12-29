@extends('layouts.app')

@section('content')
<div class="container">
<h4>Edit Account</h4>

<form method="POST" action="{{ route('finance.accounts.update',$account->id) }}">
@csrf @method('PUT')

<select name="account_group_id" class="form-control mb-2">
@foreach($groups as $g)
<option value="{{ $g->id }}" {{ $account->account_group_id==$g->id?'selected':'' }}>
    {{ $g->name }}
</option>
@endforeach
</select>

<input type="text" name="account_name" value="{{ $account->account_name }}" class="form-control mb-2">

<input type="text" name="account_code" value="{{ $account->account_code }}" class="form-control mb-2">

<input type="number" step="0.01" name="opening_balance"
       value="{{ $account->opening_balance }}" class="form-control mb-2">

<select name="balance_type" class="form-control mb-2">
<option value="Debit" {{ $account->balance_type=='Debit'?'selected':'' }}>Debit</option>
<option value="Credit" {{ $account->balance_type=='Credit'?'selected':'' }}>Credit</option>
</select>

<button class="btn btn-primary">Update</button>
</form>
</div>
@endsection
