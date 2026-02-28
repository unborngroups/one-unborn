@extends('layouts.app') 

{{-- ✅ Extend the base layout (resources/views/layouts/app.blade.php) --}}



@section('content') 

{{-- ✅ Define the content section to inject into the main layout --}}



@if ($errors->any())

    {{-- ⚠️ Display validation errors if any exist --}}

    <div class="alert alert-danger">

        <ul class="mb-0">

            @foreach ($errors->all() as $error)

                {{-- ✅ Loop through all validation error messages --}}

                <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

@endif



<div class="container py-4">

    <h3 class="mb-3">Edit Item</h3>

    <div class="card shadow border-0 p-4">

        <form action="{{ route('finance.items.update', $items) }}" method="POST">

            <?= csrf_field() ?> 

            @method('PUT') 

            <div class="mb-3">

                <label>Name</label>

                <input type="text" name="item_name" value="{{ old('item_name', $items->item_name) }}" class="form-control" required>

            </div>

            <div class="mb-3">

                <label for="">Description</label>

                <input type="text" name="item_description" value="{{ old('item_description', $items->item_description) }}" class="form-control">

            </div>

            <div class="mb-3">

                <label for="">Rate</label>

                {{-- ⚠️ Minor fix below: should use $items->Rate (was missing field) --}}

                <input type="text" name="item_rate" value="{{ old('item_rate', $items->item_rate) }}" class="form-control">

                {{-- ✅ Optional description field --}}

            </div>

            <div class="mb-3">

                <label for="">HSN / SAC</label>

                {{-- ⚠️ Minor fix below: should use $items->hsn_sac_code (was missing field) --}}

                <input type="text" name="hsn_sac_code" value="{{ old('hsn_sac_code', $items->hsn_sac_code) }}" class="form-control">

            </div>

            <div class="mb-3">

                <label for="">Unit</label>

                <input type="text" name="usage_unit" value="{{ old('usage_unit', $items->usage_unit) }}" class="form-control">

            </div>


             {{--  Status Dropdown --}}

            <input type="hidden" name="status" value="Active">

            <button class="btn btn-warning">Update</button>

            <a href="{{ route('finance.items.index') }}" class="btn btn-secondary">Back</a>

        </form>

    </div>

</div>

@endsection 