@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Finance Reports</h4>
        <p class="text-muted mb-0">Select a report to view the latest numbers</p>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-3">
        <div class="col">
            <a href="{{ route('finance.reports.profit_loss') }}" class="card h-100 text-decoration-none text-dark border-primary shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-graph-up-right fs-4 text-primary me-2"></i>
                        <h5 class="card-title mb-0">Profit & Loss</h5>
                    </div>
                    <p class="card-text text-muted">Compare total income against expenses to see how profitable the business is.</p>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('finance.reports.balance_sheet') }}" class="card h-100 text-decoration-none text-dark border-success shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-balance-scale fs-4 text-success me-2"></i>
                        <h5 class="card-title mb-0">Balance Sheet</h5>
                    </div>
                    <p class="card-text text-muted">Review assets, liabilities, and equity to understand the organisation's stability.</p>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('finance.reports.cash_flow') }}" class="card h-100 text-decoration-none text-dark border-warning shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-cash-stack fs-4 text-warning me-2"></i>
                        <h5 class="card-title mb-0">Cash Flow</h5>
                    </div>
                    <p class="card-text text-muted">Track cash inflows and outflows to keep an eye on liquidity.</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
