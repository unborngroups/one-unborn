@extends('layouts.app')
@section('content')
<div class="container">
<h4 class="mb-3">Add Asset</h4>

{{-- Show validation errors --}}

    @if ($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

<form action="{{ route('asset.store') }}" method="POST">
@csrf

@include('asset.form')

<button type="submit" class="btn btn-primary mt-3 float-start">Save</button>


            <a href="{{ route('asset.index') }}" class="btn btn-secondary mt-3 float-end"><--Back</a>

    
</form>
</div>
@endsection
