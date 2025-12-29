@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>Expenses</h4>
        <a href="{{ route('finance.expenses.create') }}" class="btn btn-primary">+ Add Expense</a>
    </div>

    <table class="table table-bordered ">
        <thead class="table-dark-primary">
            <tr>
                <th>S.No</th>
                <th>Action</th>
                <th>Expense Type</th>
                <th>Date</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $expense)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    @if($permissions->can_edit)

                               <a href="{{ route('finance.expenses.edit', $expense) }}" class="btn btn-sm btn-primary">

                                    <i class="bi bi-pencil"></i>

                                </a>

                                @endif
                                 {{-- Delete --}}

                                 @if($permissions->can_delete)

                                 <form action="{{ route('finance.expenses.destroy',$expense) }}" method="POST" class="d-inline">

                                    @csrf

                                    @method('DELETE') 

                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this Client?')">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </form>

                                   @endif
                </td>
                <td>{{ $expense->expense_type }}</td>
                <td>{{ $expense->expense_date }}</td>
                <td>₹ {{ $expense->amount }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
