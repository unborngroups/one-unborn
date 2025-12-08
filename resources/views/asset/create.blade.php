@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-primary">New Asset</h3>
        <a href="{{ route('asset.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Assets
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('asset.store') }}">
                @csrf

                <div class="row gy-3">
                    <div class="col-md-6">
                        <label class="form-label">Make</label>
                        <select name="make_id" class="form-select">
                            <option value="">Choose make</option>
                            @foreach($vendorMakes as $make)
                                <option value="{{ $make->id }}" {{ old('make_id') == $make->id ? 'selected' : '' }}>{{ $make->make_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Model</label>
                        <input type="text" name="model" class="form-control" value="{{ old('model') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Brand</label>
                        <input type="text" name="brand" class="form-control" value="{{ old('brand') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Procured From</label>
                        <input type="text" name="procured_from" class="form-control" value="{{ old('procured_from') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Purchase Date</label>
                        <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Warranty</label>
                        <select name="warranty" class="form-select">
                            <option value="">Choose warranty</option>
                            @foreach(['1 year', '2 years', '3 years', '4 years', '5 years'] as $option)
                                <option value="{{ $option }}" {{ old('warranty') == $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">PO Number</label>
                        <input type="text" name="po_number" class="form-control" value="{{ old('po_number') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">MRP</label>
                        <input type="number" step="0.01" name="mrp" class="form-control" value="{{ old('mrp') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Purchase Cost</label>
                        <input type="number" step="0.01" name="purchase_cost" class="form-control" value="{{ old('purchase_cost') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Serial Number</label>
                        <input type="text" name="serial_number" class="form-control" value="{{ old('serial_number') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">MAC Number</label>
                        <input type="text" name="mac_number" class="form-control" value="{{ old('mac_number') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Asset ID</label>
                        <input type="text" name="asset_id" class="form-control" value="{{ old('asset_id') }}" required>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save Asset</button>
                    <a href="{{ route('asset.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection