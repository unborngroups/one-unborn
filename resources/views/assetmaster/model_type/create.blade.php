@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3 text-primary">Add Model Type</h3>
    <div class="card p-4 shadow">
        
        @if ($errors->any())

            <div class="alert alert-danger">

                <ul class="mb-0">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif
        <form action="{{ route('assetmaster.model_type.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Model Name</label>
                <input type="text" name="model_name" class="form-control" value="{{ old('model_name') }}">
                @error('model_name')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <button class="btn btn-primary float-start">Save Model Type</button>
            <a href="{{ route('assetmaster.model_type.index') }}" class="btn btn-secondary ms-2 float-end">Cancel</a>
        </form>
    </div>
</div>
@endsection
