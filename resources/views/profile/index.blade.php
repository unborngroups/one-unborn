@extends('layouts.app')



@section('content')

<div class="container mt-5">

    <div class="card shadow-sm p-4">

        <h2 class="mb-4 text-primary fw-bold">My Profile</h2>



        {{-- ✅ Messages --}}

        @if(session('success'))

            <div class="alert alert-success">{{ session('success') }}</div>

        @endif



        @if(session('alert'))

            <div class="alert alert-warning">{{ session('alert') }}</div>

        @endif



        {{-- ✅ Profile Details --}}

        @if(isset($profile))

        <div class="row align-items-start mb-4">

            {{-- Profile Photo Section --}}

            <div class="col-md-3 text-center">

                <div class="position-relative d-inline-block">

                    <div class="border rounded-circle overflow-hidden shadow-sm" style="width: 130px; height: 130px;">

                        @if(!empty($profile->photo))

                            <img src="{{ asset($profile->photo) }}" alt="Profile Photo"

                                 class="img-fluid w-100 h-100" style="object-fit: cover;">

                        @else

                            <img src="{{ asset('images/default-avatar.png') }}" alt="Default"

                                 class="img-fluid w-100 h-100" style="object-fit: cover;">

                        @endif

                    </div>



                    {{-- ✏️ Change Photo Button (Overlay) --}}

                    <a href="{{ route('profile.edit', $profile->id) }}" 

                       class="btn btn-sm btn-primary position-absolute bottom-0 end-0 translate-middle"

                       title="Change Photo" style="border-radius: 50%; padding: 6px 8px;">

                        <i class="fa fa-camera"></i>

                    </a>

                </div>

                <p class="mt-3 fw-semibold mb-0">{{ $profile->fname ?? '' }} {{ $profile->lname ?? '' }}</p>

                <p class="text-muted">{{ $profile->designation ?? '—' }}</p>

            </div>



            {{-- Profile Info --}}

            <div class="col-md-9">

                <table class="table table-bordered">

                    <tr><th>First Name</th><td>{{ $profile->fname ?? 'N/A' }}</td></tr>

                    <tr><th>Last Name</th><td>{{ $profile->lname ?? 'N/A' }}</td></tr>

                    <tr><th>Designation</th><td>{{ $profile->designation ?? 'N/A' }}</td></tr>

                    <tr><th>Official Email</th><td>{{ $profile->official_email ?? 'N/A' }}</td></tr>

                    <tr><th>Personal Email</th><td>{{ $profile->personal_email ?? 'N/A' }}</td></tr>

                    <tr><th>Phone 1</th><td>{{ $profile->phone1 ?? 'N/A' }}</td></tr>

                    <tr><th>Phone 2</th><td>{{ $profile->phone2 ?? '-' }}</td></tr>

                    <tr><th>Date of Birth</th><td>{{ $profile->Date_of_Birth ?? 'N/A' }}</td></tr>



                   



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

            </div>

        </div>

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

