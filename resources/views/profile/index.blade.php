@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm p-4">
        <h2 class="mb-4">My Profile</h2>

        {{-- ✅ Messages --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('alert'))
            <div class="alert alert-warning">{{ session('alert') }}</div>
        @endif

        {{-- ✅ Profile Details --}}
        @if(isset($profile))
        <table class="table table-bordered">
            <tr><th>First Name</th><td>{{ $profile->fname ?? 'N/A' }}</td></tr>
            <tr><th>Last Name</th><td>{{ $profile->lname ?? 'N/A' }}</td></tr>
            <tr><th>Designation</th><td>{{ $profile->designation ?? 'N/A' }}</td></tr>

            <tr><th>Email</th><td>{{ $profile->email ?? 'N/A' }}</td></tr>
            <tr><th>Phone 1</th><td>{{ $profile->phone1 ?? 'N/A' }}</td></tr>
            <tr><th>Phone 2</th><td>{{ $profile->phone2 ?? '-' }}</td></tr>
            <tr><th>Date of Birth</th><td>{{ $profile->Date_of_Birth ?? 'N/A' }}</td></tr>

            {{-- Address --}}
            <tr>
                <th>Address</th>
                <td>
                    {{ $profile->address1 ?? '' }}<br>
                    {{ $profile->address2 ?? '' }}<br>
                    {{ $profile->address3 ?? '' }}
                </td>
            </tr>

            {{-- Aadhaar --}}
            <tr><th>Aadhaar Number</th><td>{{ $profile->aadhaar_number ?? 'N/A' }}</td></tr>
            <tr>
                <th>Aadhaar Upload</th>
                <td>
                    @if(!empty($profile->aadhaar_upload))
                        <a href="{{ asset($profile->aadhaar_upload) }}" target="_blank">View Aadhaar</a>
                    @else
                        Not Uploaded
                    @endif
                </td>
            </tr>

            {{-- PAN --}}
            <tr><th>PAN Number</th><td>{{ $profile->pan ?? 'N/A' }}</td></tr>
            <tr>
                <th>PAN Upload</th>
                <td>
                    @if(!empty($profile->pan_upload))
                        <a href="{{ asset($profile->pan_upload) }}" target="_blank">View PAN</a>
                    @else
                        Not Uploaded
                    @endif
                </td>
            </tr>

            {{-- Bank Details --}}
            <tr><th>Bank Name</th><td>{{ $profile->bank_name ?? 'N/A' }}</td></tr>
            <tr><th>Branch</th><td>{{ $profile->branch ?? 'N/A' }}</td></tr>
            <tr><th>Account Number</th><td>{{ $profile->bank_account_no ?? 'N/A' }}</td></tr>
            <tr><th>IFSC Code</th><td>{{ $profile->ifsc_code ?? 'N/A' }}</td></tr>
        </table>

        @else
        <div class="alert alert-info">
            No profile found. Please create your profile below.
        </div>
        @endif

        <div class="mt-4 d-flex justify-content-between">
            <a href="{{ route('profile.create') }}" class="btn btn-primary">
                Edit / Create Profile
            </a>

            <a href="{{ route('welcome') }}" class="btn btn-secondary">
                Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
