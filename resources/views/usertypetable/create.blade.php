@extends('layouts.app') 

{{-- ✅ Extends the main layout file located at resources/views/layouts/app.blade.php --}}



@section('content') 

{{-- ✅ Defines the "content" section that will be injected into the layout --}}



<div class="container py-4"> 

    {{-- ✅ Bootstrap container with vertical padding --}}

    <h3 class="mb-3">Add User</h3> 

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



        <form action="{{ route('usertypetable.store') }}" method="POST">

            {{-- ✅ Form submits to the route named "usertypetable.store" (defined in web.php) --}}

            @csrf 

            {{-- ✅ Protects against CSRF attacks (required for POST forms in Laravel) --}}

            <div class="mb-3">

                <label>Name</label>

                <input type="text" name="name" class="form-control" required>

                {{-- ✅ Input field for user's name (required) --}}

            </div>

            <div class="mb-3">

                <label>Email</label>

                <input type="email" name="email" class="form-control" required>

                {{-- ✅ Input field for user's email (required) --}}
            </div>

            <div class="mb-3">

                <label for="">Description</label>

                <input type="text" name="Description" class="form-control" required>

                {{-- ✅ Input for user description (optional) --}}

            </div>

            <!-- <div class="mb-3">

                <label>Status</label>

                <select name="status" class="form-control">

                    <option value="Active">Active</option>

                    <option value="Inactive">Inactive</option>

                </select>

                {{-- ✅ Dropdown for choosing the status (Active or Inactive) --}}

            </div> -->

             {{--  Status Dropdown --}}

            <input type="hidden" name="status" value="Active">





            <button class="btn btn-success">Save</button>

            {{-- ✅ Submits the form --}}



            <a href="{{ route('usertypetable.index') }}" class="btn btn-secondary">Back</a>

            {{-- ✅ Link to return to the index/listing page --}}

        </form>

    </div>

</div>



@endsection 

{{-- ✅ Ends the content section --}}

