@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3 text-primary">Edit Asset Type</h3>
    <div class="card p-4 shadow">
        <form action="{{ route('assetmaster.asset_type.update', $assetType->id) }}" method="POST">
            @csrf
            @method('PUT')

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

                <div class="mt-3">
            <button class="btn btn-primary float-start">Update Asset Type</button>
            </div>
            <a href="{{ route('assetmaster.asset_type.index') }}" class="btn btn-light ms-2 float-end">Cancel</a>
        </form>
    </div>
</div>
@endsection