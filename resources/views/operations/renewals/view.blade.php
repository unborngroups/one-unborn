@extends('layouts.app')

@section('content')

<div class="container py-4 d-flex flex-column align-items-center justify-content-center" style="min-height: 80vh;">

    <h3 class="mb-3 text-center">View Renewal</h3>

    <div class="card shadow border-0 p-4 w-100" style="max-width: 800px;">

        {{-- Deliverable --}}
        <div class="mb-3">
            <label class="form-label">Deliverable (Circuit ID)</label>
            <input type="text"
                   class="form-control"
                   value="{{ $renewal->circuit_id ?? (\App\Models\DeliverablePlan::where('deliverable_id', $renewal->deliverable_id)->value('circuit_id') ?? '-') }}"
                   readonly>
        </div>

        {{-- Date of Renewal --}}
        <div class="mb-3">
            <label class="form-label">Date of Renewal</label>
            <input type="date"
                   class="form-control"
                   value="{{ $renewal->date_of_renewal }}"
                   readonly>
        </div>

        {{-- Renewal Months --}}
        <div class="mb-3">
            <label class="form-label">Renewal Months</label>
            <input type="number"
                   class="form-control"
                   value="{{ $renewal->renewal_months }}"
                   readonly>
        </div>

        {{-- New Expiry Date --}}
        <div class="mb-3">
            <label class="form-label">New Expiry Date</label>
            <input type="text"
                   class="form-control"
                   value="{{ $renewal->new_expiry_date }}"
                   readonly>
        </div>

        {{-- Buttons --}}
        <div class="d-flex justify-content-center gap-2">
            <a href="{{ route('operations.renewals.edit', $renewal->id) }}" class="btn btn-primary">
                Edit
            </a>
            <a href="{{ route('operations.renewals.index') }}" class="btn btn-secondary">
                Back
            </a>
        </div>

    </div>
</div>

@endsection
