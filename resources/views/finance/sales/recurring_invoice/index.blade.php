@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-repeat me-2"></i>Recurring Invoices</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if(!empty($selectedClient))
                <div class="alert alert-info">
                    Showing recurring entries for client: <strong>{{ $selectedClient->client_name }}</strong>
                </div>
            @endif

            @if(!empty($summary))
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-muted d-block">Total ARC Component</small>
                            <strong>{{ number_format($summary['total_arc_component'] ?? 0, 2) }}</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-muted d-block">Total Static Component</small>
                            <strong>{{ number_format($summary['total_static_component'] ?? 0, 2) }}</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-muted d-block">Total Formula Amount</small>
                            <strong>{{ number_format($summary['total_formula_amount'] ?? 0, 2) }}</strong>
                        </div>
                    </div>
                </div>
            @endif

            @if(($invoiceRows ?? collect())->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Client</th>
                                <th>Circuit ID</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Months</th>
                                <th>Days</th>
                                <th>ARC (Annual)</th>
                                <th>Static (Annual)</th>
                                <th>Total (Annual)</th>
                                <th>Day Rate</th>
                                <th>Formula Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoiceRows as $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $row['client_name'] }}</td>
                                    <td>{{ $row['circuit_id'] }}</td>
                                    <td>{{ $row['start_date'] }}</td>
                                    <td>{{ $row['end_date'] }}</td>
                                    <td>{{ $row['renewal_months'] }}</td>
                                    <td>{{ $row['billable_days'] }}</td>
                                    <td>{{ number_format($row['annual_arc'], 2) }}</td>
                                    <td>{{ number_format($row['annual_static'], 2) }}</td>
                                    <td>{{ number_format($row['annual_total'], 2) }}</td>
                                    <td>{{ number_format($row['day_rate'], 6) }}</td>
                                    <td><strong>{{ number_format($row['formula_amount'], 2) }}</strong></td>
                                    <td>
                                        <form action="{{ route('finance.sales.recurring-invoice.send-email', $row['renewal']->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Send recurring invoice email to client now?')">
                                                Click Here to Send Email
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                    <h5 class="text-muted mt-3">No Recurring Invoices Found</h5>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
