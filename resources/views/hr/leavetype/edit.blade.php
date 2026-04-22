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

    {{-- ✅ Add some vertical padding using Bootstrap spacing --}}

    <h3 class="mb-3">Edit Leavetype</h3>

    {{-- ✅ Page heading for clarity --}}



    <div class="card shadow border-0 p-4">

        {{-- ✅ Card with shadow, no border, and padding for nice layout --}}



        <form action="{{ route('hr.leavetype.update', $leavetypetable->id) }}" method="POST">

            {{-- ✅ Submit form to the “update” route, passing the model instance --}}

            <?= csrf_field() ?> 

            {{-- ✅ Include CSRF token for security (same as @csrf) --}}



            @method('PUT') 

            {{-- ✅ Spoofs HTTP method PUT (required for updating resources) --}}



            <div class="mb-3">

                <label>Leavetype</label>

                {{-- ✅ Input for user type name --}}

                <input type="text" 

                       name="leavetype" 

                       value="{{ old('leavetype', $leavetypetable->leavetype) }}" 

                       class="form-control" 

                       required>

                {{-- ✅ Prefills with old value (if validation fails) or database value --}}

            </div>

            <div class="mb-3">

                <label>Shortcode</label>

                <input type="text" name="shortcode" class="form-control" value="{{ old('shortcode', $leavetypetable->shortcode) }}" required>

                {{-- ✅ Input field for user's email (required) --}}
</div>


           


            <!-- <div class="mb-3">

                <label>Status</label>

                <select name="status" class="form-control">

                    {{-- ✅ Dropdown menu with two options --}}

                    <option value="Active" {{ $leavetypetable->status == 'Active' ? 'selected' : '' }}>Active</option>

                    <option value="Inactive" {{ $leavetypetable->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>

                </select>

                {{-- ✅ Automatically selects the current status --}}

            </div> -->

             {{--  Status Dropdown --}}

            <input type="hidden" name="status" value="Active">





            <button class="btn btn-warning">Update</button>

            {{-- ✅ Submit button for updating record --}}

            

            <a href="{{ route('hr.leavetype.index') }}" class="btn btn-secondary">Back</a>

            {{-- ✅ Button to navigate back to listing page --}}

        </form>

    </div>

</div>



@endsection 

{{-- ✅ End of content section --}}