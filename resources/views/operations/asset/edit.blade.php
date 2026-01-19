@extends('layouts.app')
@php use Milon\Barcode\Facades\DNS1DFacade as DNS1D; @endphp
@section('content')
<div class="container">
<h4 class="mb-3">Edit Asset</h4>

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


<form action="{{ route('operations.asset.update', $asset->id) }}" method="POST">
@csrf @method('PUT')

@include('operations.asset.form')

<button type="submit" class="btn btn-success mt-3">Update</button>
<div class="text-end">

            <a href="{{ route('operations.asset.index') }}" class="btn btn-secondary">Back</a>

        </div>
</form>
</div>
@endsection
