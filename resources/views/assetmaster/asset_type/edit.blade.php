@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3 text-primary">Edit Asset Type</h3>
    <div class="card p-4 shadow">
        <form action="{{ route('assetmaster.asset_type.update', $assetType->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Company</label>
                <select name="company_id" class="form-control">
                    <option value="">Select company</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ (old('company_id', $assetType->company_id) == $company->id) ? 'selected' : '' }}>
                            {{ $company->company_name }}
                        </option>
                    @endforeach
                </select>
                @error('company_id')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- <div class="mb-3">
                <label class="form-label">Asset Type Name</label>
                <input type="text" name="type_name" class="form-control" value="{{ old('type_name', $assetType->type_name) }}">
                @error('type_name')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div> -->
            <!-- Select Asset type -->
             <div class="col-md-12">

                    <label class="form-label">Asset Type</label>

                    <select name="type_name" class="form-select select2-tags">

                        <option value="">Select or Type City</option>

                        <option value="Switch" {{ (old('type_name', $assetType->type_name) == 'Switch') ? 'selected' : '' }}>Switch</option>

                        <option value="Router" {{ (old('type_name', $assetType->type_name) == 'Router') ? 'selected' : '' }}>Router</option>

                        <option value="SD WAN" {{ (old('type_name', $assetType->type_name) == 'SD WAN') ? 'selected' : '' }}>SD WAN</option>
                    </select>

                </div>

            <button class="btn btn-primary">Update Asset Type</button>
            <a href="{{ route('assetmaster.asset_type.index') }}" class="btn btn-light ms-2">Cancel</a>
        </form>
    </div>
</div>
@endsection