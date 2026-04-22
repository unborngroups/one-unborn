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



    <!-- Top Controls: Show/Per Page and Search -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
        <div class="d-flex align-items-center gap-2">
            <label for="perPage" class="mb-0">Show</label>
            <select id="perPage" class="form-select form-select-sm" style="width:auto;">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span class="ms-1">entries</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <label for="searchBox" class="mb-0">Search:</label>
            <input type="text" id="searchBox" class="form-control form-control-sm" style="width:200px;" placeholder="Search invoices...">
        </div>
    </div>

    <!-- Bulk Action Bar -->
    <div id="bulkActionBar" class="mb-3" style="display:none; background:#e9ecef; border:1px solid #bfc9d1; padding:12px 18px; border-radius:6px;">
        <button id="downloadBulkPdf" class="btn btn-primary btn-sm me-2">Download PDF</button>
        <button id="sendBulkEmail" class="btn btn-success btn-sm">Send Bulk Email</button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover">

                <thead class="table-dark-primary">
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>S.No</th>
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
                        <td><input type="checkbox" class="rowCheckbox" value="{{ $sale->id }}"></td>
                        <td>{{ $loop->iteration }}</td>
@push('scripts')
<script src="{{ asset('js/sales-bulk-actions.js') }}"></script>
@endpush

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