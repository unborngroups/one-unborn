@extends('layouts.app')

@section('title', 'Purchase Invoices')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Purchase Invoices</h4>


    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Invoice No</th>
                        <th>Vendor</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th width="250">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($purchases as $purchase)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>{{ $purchase->invoice_number ?? '-' }}</td>

                            <td>
                                @if($purchase->deliverable && $purchase->deliverable->feasibility && $purchase->deliverable->feasibility->client)
                                    {{ $purchase->deliverable->feasibility->client->client_name }}
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                {{ $purchase->invoice_date 
                                    ? \Carbon\Carbon::parse($purchase->invoice_date)->format('d-m-Y') 
                                    : '-' }}
                            </td>

                            <td>
                                ₹ {{ number_format($purchase->total_amount, 2) }}
                            </td>

                            <td>

                                {{-- VIEW --}}
                                <a href="{{ route('finance.purchases.show', $purchase->id) }}"
                                   class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>

                                {{-- EDIT --}}
                                <a href="{{ route('finance.purchases.edit', $purchase->id) }}"
                                   class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                {{-- PDF --}}
                                <a href="{{ route('finance.purchases.pdf', $purchase->id) }}"
                                   class="btn btn-sm btn-secondary"
                                   target="_blank">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>

                                {{-- DELETE --}}
                                <form action="{{ route('finance.purchases.destroy', $purchase->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this invoice?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                No Purchase Invoices Found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

</div>

@endsection