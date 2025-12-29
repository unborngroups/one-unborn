@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>Vendor Invoices</h4>
        <a href="{{ route('finance.vendor-invoices.create') }}" class="btn btn-primary">
            + Add Invoice
        </a>
    </div>

    <table class="table table-bordered">
        <thead class="table-dark-primary">
            <tr>
                <th>S.No</th>
                <th>Action</th>
                <th>Vendor</th>
                <th>Invoice No</th>
                <th>Date</th>
                <th>Total</th>
                <th>Status</th>
                <!-- <th width="120">Action</th> -->
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    @if($permissions->can_edit)

                               <a href="{{ route('finance.vendor-invoices.edit', $invoice) }}" class="btn btn-sm btn-primary">

                                    <i class="bi bi-pencil"></i>

                                </a>

                                @endif
                                 {{-- Delete --}}

                                 @if($permissions->can_delete)

                                 <form action="{{ route('finance.vendor-invoices.destroy',$invoice) }}" method="POST" class="d-inline">

                                    @csrf

                                    @method('DELETE') 

                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this Client?')">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </form>

                                   @endif
                </td>
                <td>{{ $invoice->vendor->vendor_name ?? '—' }}</td>
                <td>{{ $invoice->invoice_no }}</td>
                <td>{{ $invoice->invoice_date }}</td>
                <td>₹ {{ number_format($invoice->total_amount,2) }}</td>
                <td>
                    <span class="badge bg-info">{{ $invoice->status }}</span>
                </td>
                <!-- <td>
                    <a href="{{ route('finance.vendor-invoices.edit',$invoice->id) }}" class="btn btn-sm btn-warning">
                        Edit
                    </a>
                </td> -->
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">No records found</td>
            </tr>
            @endforelse

            <a href="{{ route('finance.purchases.index') }}" class="btn btn-secondary"><- Back</a>

        </tbody>
    </table>
</div>
@endsection
