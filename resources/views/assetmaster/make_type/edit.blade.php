@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3 text-primary">Edit Make Type</h3>
    <div class="card p-4 shadow">
        <form action="{{ route('assetmaster.make_type.update', $makeType->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Company</label>
                <select name="company_id" class="form-control">
                    <option value="">Select company</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id', $makeType->company_id) == $company->id ? 'selected' : '' }}>
                            {{ $company->company_name }}
                        </option>
                    @endforeach
                </select>
                @error('company_id')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Make Name</label>
                <input type="text" name="make_name" class="form-control" value="{{ old('make_name', $makeType->make_name) }}">
                @error('make_name')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <button class="btn btn-primary">Save Make Type</button>
        </form>
    </div>
</div>
@endsection
