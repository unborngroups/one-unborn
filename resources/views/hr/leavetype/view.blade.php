@extends('layouts.app')



@section('content')

<div class="container py-4">

    <h3 class="mb-3 text-primary">View Leave Type</h3>



    <div class="card shadow border-0 p-4">

         {{-- 🟢 Row 1: Basic Details (Name, User Type) --}}

        <div class="row mb-3">

            <div class="col-md-6">

                <label class="fw-bold">Leavetype:</label>

                <div>{{ $leavetypetable->leavetype ?? '-' }}</div>

            </div>

             <div class="col-md-6">

                <label class="fw-bold">Shortcode:</label>

                <div>{{ $leavetypetable->shortcode ?? '-' }}</div>

            </div>



        </div>



        {{-- 🟢 Status Display --}}

        <div class="mb-3">

            <label class="fw-bold">Status:</label>

            <div>

                @if($leavetypetable->status === 'Active')

                    <span class="badge bg-success">Active</span>

                @else

                    <span class="badge bg-danger">Inactive</span>

                @endif

            </div>

        </div>



        {{-- 🟢 Action Buttons --}}

        <div class="mt-3">

            <a href="{{ route('hr.leavetype.index') }}" class="btn btn-secondary">Back</a>

        </div>

    </div>

</div>

@endsection