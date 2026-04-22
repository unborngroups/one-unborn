@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Account</h4>

    <form method="POST" action="{{ route('finance.accounts.update',$account->id) }}">
        @csrf
        @method('PUT')

        {{-- Account Group --}}
        <select name="account_group_id" class="form-control mb-2">
            @foreach($groups as $g)
                <option value="{{ $g->id }}" {{ $account->account_group_id==$g->id?'selected':'' }}>
                    {{ $g->name }}
                </option>
            @endforeach
        </select>

        {{-- Account Name --}}
        <input type="text"
               name="account_name"
               value="{{ $account->account_name }}"
               class="form-control mb-2"
               required>

        {{-- Account Code (readonly recommended) --}}
        <input type="text"
               name="account_code"
               value="{{ $account->account_code }}"
               class="form-control mb-2"
               readonly>

        {{-- Opening Balance (LOCKED) --}}
        <input type="number"
               class="form-control mb-2"
               value="{{ $account->opening_balance }}"
               readonly>

        {{-- Balance Type (LOCKED) --}}
        <input type="text"
               class="form-control mb-2"
               value="{{ $account->balance_type }}"
               readonly>

        <button class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
