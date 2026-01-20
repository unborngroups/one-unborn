@extends('layouts.app')

@section('content')

<div class="container py-4 d-flex flex-column align-items-center justify-content-center" style="min-height: 80vh;">

    <h3 class="mb-3 text-center">Add Renewal</h3>

    <div class="card shadow border-0 p-4 w-100" style="max-width: 800px;">

        <form action="{{ route('operations.renewals.store') }}" method="POST">
            @csrf

            {{-- Deliverable --}}
            <div class="mb-3">
                <label for="deliverable_id" class="form-label">Deliverable (Circuit ID)</label>
                <select name="deliverable_id" id="deliverable_id" class="form-select select2-tags" required>
                    <option value="">Select Circuit ID</option>
                    @foreach($deliverables_plans as $d)
                        <option value="{{ $d->deliverable_id }}">
                            {{ $d->circuit_id }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Date of Renewal --}}
            <div class="mb-3">
                <label for="renewalDate" class="form-label">Date of Renewal</label>
                <input type="date" name="date_of_renewal" id="renewalDate" class="form-control" required>
            </div>

            {{-- Renewal Months --}}
            <div class="mb-3">
                <label for="months" class="form-label">Renewal Months</label>
                <input type="number" name="renewal_months" id="months" min="1" max="36" class="form-control" placeholder="Enter months" required>
            </div>

            {{-- New Expiry Date --}}
            <div class="mb-3">
                <label for="expiry" class="form-label">New Expiry Date</label>
                <input type="text" id="expiry" name="new_expiry_date" class="form-control" readonly placeholder="Auto-calculated">
            </div>

            {{-- Buttons --}}
            <div class="d-flex justify-content-center gap-2">
                <button type="submit" class="btn btn-success">Save</button>
                <a href="{{ route('operations.renewals.index') }}" class="btn btn-secondary">Back</a>
            </div>

        </form>
    </div>
</div>

{{-- ================= JS ================= --}}

<script>
document.addEventListener('DOMContentLoaded', function () {
    const renewalInput = document.getElementById('renewalDate');
    const monthsInput  = document.getElementById('months');
    const expiryInput  = document.getElementById('expiry');

    function calcExpiry() {
        if (!renewalInput.value || !monthsInput.value) {
            expiryInput.value = '';
            return;
        }

        const months = Number(monthsInput.value);
        if (!months || months <= 0) return;

        const baseDate = new Date(renewalInput.value + 'T00:00:00');
        if (isNaN(baseDate.getTime())) {
            expiryInput.value = '';
            return;
        }

        const expiryDate = new Date(baseDate);
        expiryDate.setMonth(expiryDate.getMonth() + months);
        expiryDate.setDate(expiryDate.getDate() - 1);

        expiryInput.value = expiryDate.toISOString().split('T')[0];
    }

    renewalInput.addEventListener('change', calcExpiry);
    monthsInput.addEventListener('input', calcExpiry);
    monthsInput.addEventListener('change', calcExpiry);
    // Call once on load in case values are pre-filled
    calcExpiry();
});
</script>

@endsection
