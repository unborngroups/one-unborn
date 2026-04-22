@extends('layouts.app')



@section('content')

<div class="container py-4">

    <h3 class="mb-3 text-primary">View User Type</h3>



    <div class="card shadow border-0 p-4">

         {{-- 🟢 Row 1: Basic Details (Name, User Type) --}}

        <div class="row mb-3">

            <div class="col-md-6">

                <label class="fw-bold">Name:</label>

                <div>{{ $usertypetable->name }}</div>

            </div>

             <div class="col-md-6">

                <label class="fw-bold">Email:</label>

                <div>{{ $usertypetable->email ?? '-' }}</div>

            </div>



            <div class="col-md-6">

                <label class="fw-bold">Description:</label>

                <div>{{ $usertypetable->Description ?? '-' }}</div>

            </div>

        </div>



        {{-- 🟢 Status Display --}}

        <div class="mb-3">

            <label class="fw-bold">Status:</label>

            <div>

                @if($usertypetable->status === 'Active')

                    <span class="badge bg-success">Active</span>

                @else

                    <span class="badge bg-danger">Inactive</span>

                @endif

            </div>

        </div>



        {{-- 🟢 Action Buttons --}}

        <div class="mt-3">

            <a href="{{ route('usertypetable.index') }}" class="btn btn-secondary">Back</a>

        </div>

    </div>

</div>

@endsection

