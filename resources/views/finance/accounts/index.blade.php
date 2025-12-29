@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Chart of Accounts</h4>

    <a href="{{ route('finance.accounts.create') }}" class="btn btn-primary mb-3">
        + Add Account
    </a>


    
    {{-- Success Message --}}

    @if(session('success'))

        <div class="alert alert-success">

            {{ session('success') }}

        </div>

    @endif

        @php $currentUserId = auth()->id(); @endphp
        <table class="table table-bordered">
        <thead>
            <tr>
                <th>Account</th>
                <th>Group</th>
                <th>Opening Balance</th>
                <th>Status</th>
                <th width="260">Workflow</th>
                <th width="200">Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($accounts as $acc)
            <tr>
                <td>{{ $acc->account_name }}</td>
                <td>{{ $acc->group->name }}</td>
                <td>{{ $acc->opening_balance }} ({{ $acc->balance_type }})</td>
                <td>
                    <span class="badge bg-{{ $acc->status === 'approved' || $acc->status === 'locked' ? 'success' : ($acc->status === 'pending_checker' ? 'warning' : 'secondary') }} text-uppercase">
                        {{ str_replace('_', ' ', $acc->status) }}
                    </span>
                    <div class="small text-muted mt-1">
                        Maker: {{ optional($acc->maker)->name ?? '—' }}<br>
                        Checker: {{ optional($acc->checker)->name ?? '—' }}
                    </div>
                </td>
                <td>
                    @if($acc->status === 'draft')
                        <form method="POST" action="{{ route('finance.accounts.submit', $acc) }}">
                            @csrf
                            <button class="btn btn-sm btn-outline-primary w-100">Submit for Approval</button>
                        </form>
                    @elseif($acc->status === 'pending_checker')
                        @if($currentUserId && $currentUserId !== $acc->maker_id)
                            <form method="POST" action="{{ route('finance.accounts.approve', $acc) }}" class="mb-1">
                                @csrf
                                <button class="btn btn-sm btn-success w-100">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('finance.accounts.reject', $acc) }}">
                                @csrf
                                <div class="input-group input-group-sm mb-1">
                                    <input type="text" name="remarks" class="form-control" placeholder="Rejection reason" required>
                                    <button class="btn btn-sm btn-danger" type="submit">Reject</button>
                                </div>
                            </form>
                        @else
                            <span class="text-muted">Awaiting checker approval</span>
                        @endif
                    @elseif($acc->status === 'rejected')
                        <span class="text-danger">Rejected · edit & resubmit</span>
                    @else
                        <span class="text-muted">Locked</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('finance.accounts.edit',$acc->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <a href="{{ route('finance.accounts.toggle',$acc->id) }}" class="btn btn-sm btn-secondary">
                        Toggle
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
