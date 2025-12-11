@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3 text-primary">Add Asset Type</h3>
    <div class="card p-4 shadow">
        <form action="{{ route('assetmaster.asset_type.store') }}" method="POST">
            @csrf

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