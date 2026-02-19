@extends('layouts.app')

@section('content')
<div class="container">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <h4 class="mb-3">Invoice List</h4>

    <table class="table table-bordered">
        <thead class="table-dark-primary">
            <tr>
                <th>S.No</th>
                <th>Invoice No</th>
                <th>Client</th>
                <th>Date</th>
                <th>Total</th>
                <th width="220">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($invoices as $key => $invoice)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $invoice->invoice_no }}</td>
                <td>{{ $invoice->deliverable->feasibility->client->client_name }}</td>
                <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y') }}</td>
                <td>{{ number_format($invoice->total_amount,2) }}</td>
                <td>
                    <a href="{{ route('finance.invoices.view',$invoice->id) }}"
                       class="btn btn-info btn-sm">View</a>

                    <a href="{{ route('finance.invoices.edit',$invoice->id) }}"
                       class="btn btn-warning btn-sm">Edit</a>

                    <a href="{{ route('finance.invoices.pdf',$invoice->id) }}"
                       class="btn btn-secondary btn-sm">PDF</a>

                    <form action="{{ route('finance.invoices.delete',$invoice->id) }}"
                          method="POST"
                          style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>
@endsection
