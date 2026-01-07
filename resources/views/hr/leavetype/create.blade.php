@extends('layouts.app') 

{{-- ✅ Extends the main layout file located at resources/views/layouts/app.blade.php --}}



@section('content') 

{{-- ✅ Defines the "content" section that will be injected into the layout --}}



<div class="container py-4"> 

    {{-- ✅ Bootstrap container with vertical padding --}}

    <h3 class="mb-3 text-primary">Add Leave Type</h3>


    <div class="card shadow border-0 p-4">
        <form method="POST" action="{{ route('hr.leavetype.store') }}">
            @csrf
            <div class="mb-3">
                <label for="leavetype" class="form-label">Leave Type</label>
                <input type="text" class="form-control" id="leavetype" name="leavetype" required>
            </div>
            <div class="mb-3">
                <label for="shortcode" class="form-label">Shortcode</label>
                <input type="text" class="form-control" id="shortcode" name="shortcode" required>
            </div>
            <button type="submit" class="btn btn-success">Save</button>
            <a href="{{ route('hr.leavetype.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>



</div>



@endsection 

{{-- ✅ Ends the content section --}}