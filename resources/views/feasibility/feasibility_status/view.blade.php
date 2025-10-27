@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h4 class="fw-bold text-primary mb-3">Update Feasibility Status</h4>

    <div class="card shadow border-0 p-4">
        <form action="{{ route('feasibility.status.update', $record->id) }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Vendor Name *</label>
                    <input type="text" name="vendor_name" class="form-control" value="{{ $record->vendor_name }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">ARC</label>
                    <input type="text" name="arc" class="form-control" value="{{ $record->arc }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">OTC</label>
                    <input type="text" name="otc" class="form-control" value="{{ $record->otc }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Static IP Cost</label>
                    <input type="text" name="static_ip_cost" class="form-control" value="{{ $record->static_ip_cost }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Delivery Timeline</label>
                    <input type="text" name="delivery_timeline" class="form-control" value="{{ $record->delivery_timeline }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="Open" {{ $record->status == 'Open' ? 'selected' : '' }}>Open</option>
                        <option value="InProgress" {{ $record->status == 'InProgress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Closed" {{ $record->status == 'Closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Changes</button>
                <a href="{{ route('feasibility.status.index', 'Open') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
