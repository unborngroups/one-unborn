@extends('layouts.app')

@section('title','Sales Invoices')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Sales Invoices</h4>


    </div>

    @if(!empty($selectedClient))
        <div class="alert alert-info">
            Showing invoices for client: <strong>{{ $selectedClient->client_name }}</strong>
        </div>
    @endif


    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif


    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover">

                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Invoice No</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Total Amount</th>
                        <th width="270">Action</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($sales as $sale)

                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td>
                            INV-{{ str_pad($sale->id,5,'0',STR_PAD_LEFT) }}
                        </td>

                        <td>
                            {{ $sale->deliverable->feasibility->client->client_name ?? $sale->client_name ?? '-' }}
                        </td>

                        <td>
                            {{ $sale->invoice_date }}
                        </td>

                        <td>
                            ₹ {{ number_format((float) ($sale->grand_total ?? $sale->total_amount ?? 0),2) }}
                        </td>

                        <td>

                            {{-- View --}}
                            <a href="{{ route('finance.sales.show',$sale->id) }}"
                               class="btn btn-info btn-sm">
                                View
                            </a>

                            {{-- Edit --}}
                            <a href="{{ route('finance.sales.edit',$sale->id) }}"
                               class="btn btn-warning btn-sm">
                                Edit
                            </a>

                            {{-- Delete --}}
                            <form action="{{ route('finance.sales.destroy',$sale->id) }}"
                                  method="POST"
                                  style="display:inline;">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete this invoice?')">
                                    Delete
                                </button>
                            </form>

                            <form action="{{ route('finance.sales.send-email', $sale->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Send invoice email to client now?')">
                                    Click Here to Send Email
                                </button>
                            </form>

                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="6" class="text-center">
                            No Sales Invoices Found
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>
    </div>

</div>

@endsection