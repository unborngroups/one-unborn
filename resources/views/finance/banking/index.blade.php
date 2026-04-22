@extends('layouts.app')

@section('content')
    <h4>Bank Accounts</h4>

    
    {{-- Success Message --}}

    @if(session('success'))

        <div class="alert alert-success">

            {{ session('success') }}

        </div>

    @endif

<a href="{{ route('finance.banking.create') }}" class="btn btn-primary mb-2">+ Add Bank</a>

<table class="table table-bordered">
<tr>
<th>Bank</th><th>Account No</th><th>Balance</th><th>Action</th>
</tr>
@foreach($banks as $b)
<tr>
<td>{{ $b->bank_name }}</td>
<td>{{ $b->account_number }}</td>
<td>{{ $b->opening_balance }}</td>
<td>
<a href="{{ route('finance.banking.transactions',$b->id) }}"
 class="btn btn-sm btn-info">Transactions</a>
</td>
</tr>
@endforeach
</table>

@endsection
