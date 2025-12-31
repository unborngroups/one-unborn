@extends('layouts.app')



@section('content')

<div class="container mt-5">



    <div class="row justify-content-center">

        <div class="col-md-10">



            <div class="card shadow-sm border-0">

                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">My Profile</h5>

                    <a href="{{ route('profile.edit') }}" class="btn btn-light btn-sm">Edit Profile</a>

                </div>



                <div class="card-body">

                     @if($profile)

                        {{-- 🖼 Profile Photo --}}

                        <div class="text-center mb-4">

                           @php

    $photoPath = $profile->profile_photo 

        ? asset('images/profile_photos/' . basename($profile->profile_photo))

        : asset('images/default-user.png');

@endphp



<img 

    src="{{ $photoPath }}" 

    alt="Profile Photo" 

    class="rounded-circle shadow-sm border" 

    width="130" height="130"

/>



                            <h5 class="mt-3 fw-bold text-primary">{{ $profile->fname }} {{ $profile->lname }}</h5>

                            <p class="text-muted">{{ $profile->designation }}</p>

                        </div>



                        <hr>



                        {{-- 📋 Profile Details --}}

                        <div class="row">

                            {{-- Basic Info --}}

                            <div class="col-md-6 mb-3">

                                <h6 class="text-secondary fw-bold">Basic Information</h6>

                                <p><strong>Name:</strong> {{ $profile->fname }} {{ $profile->lname }}</p>

                                <p><strong>Designation:</strong> {{ $profile->designation }}</p>

                                <p><strong>Date of Birth:</strong> {{ \Carbon\Carbon::parse($profile->Date_of_Birth)->format('Y-m-d') }}</p>

                            </div>



                            {{-- Contact Info --}}

                            <div class="col-md-6 mb-3">

                                <h6 class="text-secondary fw-bold">Contact Information</h6>

                                <p><strong>Official Email:</strong> {{ $profile->official_email }}</p>

                                <p><strong>Personal Email:</strong> {{ $profile->personal_email }}</p>

                                <p><strong>Phone 1:</strong> {{ $profile->phone1 }}</p>

                                <p><strong>Phone 2:</strong> {{ $profile->phone2 ?? 'N/A' }}</p>

                            </div>



                            {{-- Aadhaar / PAN --}}

                            <div class="col-md-6 mb-3">

                                <h6 class="text-secondary fw-bold">Aadhaar Information</h6>

                                <p><strong>Aadhaar Number:</strong> {{ $profile->aadhaar_number }}</p>

                                @if($profile->aadhaar_upload)

                                    <p>

                                        <strong>Aadhaar File:</strong>

                                        <a href="{{ asset($profile->aadhaar_upload) }}" target="_blank" class="text-primary">View / Download</a>

                                    </p>

                                @endif

                            </div>



                            <div class="col-md-6 mb-3">

                                <h6 class="text-secondary fw-bold">PAN Information</h6>

                                <p><strong>PAN Number:</strong> {{ $profile->pan }}</p>

                                @if($profile->pan_upload)

                                    <p>

                                        <strong>PAN File:</strong>

                                        <a href="{{ asset($profile->pan_upload) }}" target="_blank" class="text-primary">View / Download</a>

                                    </p>

                                @endif

                            </div>



                            {{-- Bank Details --}}

                            <div class="col-md-6 mb-3">

                                <h6 class="text-secondary fw-bold">Bank Details</h6>

                                <p><strong>Bank:</strong> {{ $profile->bank_name ?? 'N/A' }}</p>

                                <p><strong>Branch:</strong> {{ $profile->branch ?? 'N/A' }}</p>

                                <p><strong>Account No:</strong> {{ $profile->bank_account_no ?? 'N/A' }}</p>

                                <p><strong>IFSC:</strong> {{ $profile->ifsc_code ?? 'N/A' }}</p>

                            </div>

                        </div>

                    @else

                        <div class="alert alert-warning text-center">

                            Profile not found! <a href="{{ route('profile.create') }}" class="alert-link">Create your profile here</a>.

                        </div>

                    @endif

                </div>

            </div>



        </div>

        {{-- 🟢 Action Buttons --}}

        <div class="mt-3">

            <a href="{{ route('welcome') }}" class="btn btn-secondary">Back</a>

            <!-- <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">Edit</a> -->

        </div>

    </div>

</div>

@endsection

