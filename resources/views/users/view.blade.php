@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3 text-primary">View User Details</h3>

    <div class="card shadow border-0 p-4">
         {{-- 🟢 Row 1: Basic Details (Name, User Type) --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="fw-bold">Name:</label>
                <div>{{ $user->name }}</div>
            </div>

            <div class="col-md-6">
                <label class="fw-bold">User Type:</label>
                <div>{{ $user->userType->name ?? '-' }}</div>
            </div>
        </div>

        {{-- 🟢 Row 2: Emails (Official & Personal) --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="fw-bold">Official Email:</label>
                <div>{{ $user->official_email }}</div>
            </div>
            <div class="col-md-6">
                <label class="fw-bold">Personal Email:</label>
                <div>{{ $user->personal_email ?? '-' }}</div>
            </div>
        </div>

        {{-- 🟢 Row 3: Mobile & Company --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="fw-bold">Mobile:</label>
                <div>{{ $user->mobile ?? '-' }}</div>
            </div>
            <div class="col-md-6">
                <label class="fw-bold">Companies:</label>
                <div>
                    @if($user->companies && $user->companies->count() > 0)
                        @foreach($user->companies as $company)
                            <span class="badge bg-primary">{{ $company->company_name }}</span>
                        @endforeach
                    @else
                        - {{-- ✅ Show “-” if no company assigned --}}
                    @endif
                </div>
            </div>
        </div>

        {{-- 🟢 Row 4: Dates (Birth & Joining) --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="fw-bold">Date of Birth:</label>
                <div>{{ $user->Date_of_Birth ? \Carbon\Carbon::parse($user->Date_of_Birth)->format('d-M-Y') : '-' }}</div>
            </div>
            <div class="col-md-6">
                <label class="fw-bold">Date of Joining:</label>
                <div>{{ $user->Date_of_Joining ? \Carbon\Carbon::parse($user->Date_of_Joining)->format('d-M-Y') : '-' }}</div>
            </div>
        </div>

        {{-- 🟢 Status Display --}}
        <div class="mb-3">
            <label class="fw-bold">Status:</label>
            <div>
                @if($user->status === 'Active')
                    <span class="badge bg-success">Active</span>
                @else
                    <span class="badge bg-danger">Inactive</span>
                @endif
            </div>
        </div>

        {{-- 🟢 Action Buttons --}}
        <div class="mt-3">
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Back</a>
            <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">Edit</a>
        </div>
    </div>
</div>
@endsection
