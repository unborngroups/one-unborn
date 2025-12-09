@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3 text-primary">Add Asset Type</h3>
    <div class="card p-4 shadow">
        <form action="{{ route('assetmaster.asset_type.store') }}" method="POST">
            @csrf
<!-- select company in company master -->
            <div class="mb-3">
                <label class="form-label">Company</label>
                <select name="company_id" class="form-control">
                    <option value="">Select company</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                            {{ $company->company_name }}
                        </option>
                    @endforeach
                </select>
                @error('company_id')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
<!-- 
            <div class="mb-3">
                <label class="form-label">Asset Type Name</label>
                <input type="text" name="type_name" class="form-control" value="{{ old('type_name') }}">
                @error('type_name')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div> -->
            <!-- select Asset -->
              <div class="col-md-12">

                    <label class="form-label">Asset Type</label>

                    <select name="type_name" class="form-select select2-tags">

                        <option value="">Select or Type City</option>

                        <option>Switch</option>

                        <option>Router</option>

                        <option>SD WAN</option>

                    </select>

                </div>


<div class="p-3">
            <button class="btn btn-primary">Save Asset Type</button>
            </div>
        </form>
    </div>
</div>
@endsection