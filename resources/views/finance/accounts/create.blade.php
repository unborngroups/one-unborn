@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Create Account</h4>

    {{-- Validation Errors --}}
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

        {{-- Account Group --}}
        <select name="account_group_id" class="form-control mb-2" required>
            <option value="">Select Account Group</option>
            @foreach($groups as $g)
                <option value="{{ $g->id }}" {{ old('account_group_id') == $g->id ? 'selected' : '' }}>
                    {{ $g->name }}
                </option>
            @endforeach
        </select>

        {{-- Account Name --}}
        <input type="text"
               name="account_name"
               class="form-control mb-2"
               placeholder="Account Name"
               value="{{ old('account_name') }}"
               required>

        {{-- Account Code (optional) --}}
        <input type="text"
               name="account_code"
               class="form-control mb-2"
               placeholder="Account Code (optional)"
               value="{{ old('account_code') }}">

        {{-- Opening Balance --}}
        <input type="number"
               step="0.01"
               name="opening_balance"
               class="form-control mb-2"
               placeholder="Opening Balance"
               value="{{ old('opening_balance', 0) }}">

        {{-- Balance Type --}}
        <select name="balance_type" class="form-control mb-2" required>
            <option value="Debit" {{ old('balance_type') == 'Debit' ? 'selected' : '' }}>Debit</option>
            <option value="Credit" {{ old('balance_type') == 'Credit' ? 'selected' : '' }}>Credit</option>
        </select>

        <button class="btn btn-success">Save</button>
    </form>
</div>
@endsection
