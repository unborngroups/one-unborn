@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">HR - View Profile</h2>

    <div class="mb-3">
        <a href="{{ route('hr.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
    </div>

    @php $profile = $user->profile; @endphp

    @if(!$profile)
        <div class="alert alert-warning">This user has not created a profile yet.</div>
    @else
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3 text-center">
                        @php
                            $photoPath = $profile->profile_photo
                                ? asset('images/profile_photos/' . basename($profile->profile_photo))
                                : asset('images/default-user.png');
                        @endphp
                        <img src="{{ $photoPath }}" alt="Profile Photo" class="rounded-circle shadow-sm border" width="130" height="130">
                        <h5 class="mt-3">{{ $profile->fname }} {{ $profile->lname }}</h5>
                        <p class="text-muted mb-0">{{ $profile->designation }}</p>
                    </div>
                    <div class="col-md-9">
                        <table class="table table-bordered">
                            <tr><th>First Name</th><td>{{ $profile->fname }}</td></tr>
                            <tr><th>Last Name</th><td>{{ $profile->lname }}</td></tr>
                            <tr><th>Designation</th><td>{{ $profile->designation }}</td></tr>
                            <tr><th>Official Email</th><td>{{ $profile->official_email }}</td></tr>
                            <tr><th>Personal Email</th><td>{{ $profile->personal_email }}</td></tr>
                            <tr><th>Phone 1</th><td>{{ $profile->phone1 }}</td></tr>
                            <tr><th>Phone 2</th><td>{{ $profile->phone2 ?? '-' }}</td></tr>
                            <tr><th>Date of Birth</th><td>{{ $profile->Date_of_Birth }}</td></tr>
                            <tr><th>Aadhaar Number</th><td>{{ $profile->aadhaar_number }}</td></tr>
                            <tr>
                                <th>Aadhaar Upload</th>
                                <td>
                                    @if($profile->aadhaar_upload)
                                        <a href="{{ asset($profile->aadhaar_upload) }}" target="_blank">View Aadhaar</a>
                                    @else
                                        Not Uploaded
                                    @endif
                                </td>
                            </tr>
                            <tr><th>PAN Number</th><td>{{ $profile->pan }}</td></tr>
                            <tr>
                                <th>PAN Upload</th>
                                <td>
                                    @if($profile->pan_upload)
                                        <a href="{{ asset($profile->pan_upload) }}" target="_blank">View PAN</a>
                                    @else
                                        Not Uploaded
                                    @endif
                                </td>
                            </tr>
                            <tr><th>Bank Name</th><td>{{ $profile->bank_name ?? 'N/A' }}</td></tr>
                            <tr><th>Branch</th><td>{{ $profile->branch ?? 'N/A' }}</td></tr>
                            <tr><th>Account Number</th><td>{{ $profile->bank_account_no ?? 'N/A' }}</td></tr>
                            <tr><th>IFSC Code</th><td>{{ $profile->ifsc_code ?? 'N/A' }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
