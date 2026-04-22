@extends('layouts.app') 

@section('content') 

<div class="container py-4"> 

    {{-- ✅ Bootstrap container with vertical padding --}}

    <h3 class="mb-3">Add Items</h3> 

    {{-- ✅ Show Validation Errors --}}

        @if ($errors->any())

            <div class="alert alert-danger">

                <ul class="mb-0">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif
    {{-- ✅ Page heading --}}



    <div class="card shadow border-0 p-4"> 

        {{-- ✅ Bootstrap card with shadow and no border, padding 4 --}}



        <form action="{{ route('finance.items.store') }}" method="POST">

            {{-- ✅ Form submits to the route named "finance.items.store" (defined in web.php) --}}

            @csrf 

            {{-- ✅ Protects against CSRF attacks (required for POST forms in Laravel) --}}

            <div class="mb-3">

                <label>Name</label>

                <input type="text" name="item_name" class="form-control" required>

                {{-- ✅ Input field for item's name (required) --}}

            </div>

            <div class="mb-3">

                <label for="">Description</label>

                <input type="text" name="item_description" class="form-control" required>

                {{-- ✅ Input for item description (optional) --}}

            </div>

            <div class="mb-3">

                <label for="">Rate</label>

                <input type="text" name="item_rate" class="form-control" required>

                {{-- ✅ Input for item rate (optional) --}}

            </div>

            <div class="mb-3">

                <label for="">HSN / SAC</label>

                <input type="text" name="hsn_sac_code" class="form-control" required>

                {{-- ✅ Input for item HSN/SAC (optional) --}}

            </div>

            <div class="mb-3">

                <label for="">Usage Unit</label>

                <input type="text" name="usage_unit" class="form-control" required>

                {{-- ✅ Input for item usage unit (optional) --}}

            </div>

             {{--  Status Dropdown --}}

            <input type="hidden" name="status" value="Active">





            <button class="btn btn-success">Save</button>

            {{-- ✅ Submits the form --}}


            <a href="{{ route('finance.items.index') }}" class="btn btn-secondary">Back</a>

            {{-- ✅ Link to return to the index/listing page --}}

        </form>

    </div>

</div>



@endsection 

{{-- ✅ Ends the content section --}}

