@extends('layouts.app')



@section('content')

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-10">



            <div class="card shadow-sm border-0">

                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">Edit Profile</h5>

                    <a href="{{ route('profile.view') }}" class="btn btn-light btn-sm">Back</a>

                </div>



                <div class="card-body">

                    <form action="{{ route('profile.update', $profile->id) }}" method="POST" enctype="multipart/form-data">

                        @csrf

                        @method('PUT')



                        {{-- 🖼 Profile Photo --}}

                        <div class="text-center mb-4">

                            <img 

                                src="{{ asset($profile->profile_photo ?? 'images/default-user.png') }}" 

                                alt="Profile Photo" 

                                class="rounded-circle shadow-sm border" 

                                width="130" height="130"

                            >

                            <div class="mt-3">

                                <label class="form-label fw-bold">Change Profile Photo</label>

                                <input type="file" name="profile_photo" class="form-control @error('profile_photo') is-invalid @enderror">

                                @error('profile_photo')

                                    <div class="invalid-feedback">{{ $message }}</div>

                                @enderror

                            </div>

                        </div>



                        <hr>



                        <div class="row">

                            {{-- First Name --}}

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">First Name <span class="text-danger">*</span></label>

                                <input type="text" name="fname" value="{{ old('fname', $profile->fname) }}" class="form-control" required>

                            </div>



                            {{-- Last Name --}}

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">Last Name <span class="text-danger">*</span></label>

                                <input type="text" name="lname" value="{{ old('lname', $profile->lname) }}" class="form-control" required>

                            </div>



                            {{-- Designation --}}

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">Designation <span class="text-danger">*</span></label>

                                <input type="text" name="designation" value="{{ old('designation', $profile->designation) }}" class="form-control">

                            </div>



                            {{-- Date of Birth --}}

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">Date of Birth <span class="text-danger">*</span></label>

                                <input type="date" name="Date_of_Birth" value="{{ old('Date_of_Birth', $profile->Date_of_Birth) }}" class="form-control">

                            </div>



                            {{-- Email --}}

                        <div class="mb-3">

                            <label class="form-label">Official Email <span class="text-danger">*</span></label>

                            <input type="email" name="official_email" class="form-control" value="{{ old('official_email', $profile->official_email) }}" required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">Personal Email <span class="text-danger">*</span></label>

                            <input type="email" name="personal_email" class="form-control" value="{{ old('personal_email', $profile->personal_email) }}" required>

                        </div>



                            {{-- Phone 1 --}}

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">Phone 1 <span class="text-danger">*</span></label>

                                <input type="text" name="phone1" value="{{ old('phone1', $profile->phone1) }}" class="form-control" required>

                            </div>



                            {{-- Phone 2 --}}

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">Phone 2 <span class="text-danger">*</span></label>

                                <input type="text" name="phone2" value="{{ old('phone2', $profile->phone2) }}" class="form-control">

                            </div>



                            {{-- Aadhaar Number --}}

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">Aadhaar Number <span class="text-danger">*</span></label>

                                <input type="text" name="aadhaar_number" value="{{ old('aadhaar_number', $profile->aadhaar_number) }}" class="form-control">

                            </div>



                            {{-- Aadhaar Upload --}}

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">Aadhaar Upload <span class="text-danger">*</span></label>

                                <input type="file" name="aadhaar_upload" class="form-control">

                                @if($profile->aadhaar_upload)

                                    <small class="text-muted">Current: 

                                        <a href="{{ asset($profile->aadhaar_upload) }}" target="_blank">View File</a>

                                    </small>

                                @endif

                            </div>



                            {{-- PAN Number --}}

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">PAN Number <span class="text-danger">*</span></label>

                                <input type="text" name="pan" value="{{ old('pan', $profile->pan) }}" class="form-control">

                            </div>



                            {{-- PAN Upload --}}

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">PAN Upload <span class="text-danger">*</span></label>

                                <input type="file" name="pan_upload" class="form-control">

                                @if($profile->pan_upload)

                                    <small class="text-muted">Current: 

                                        <a href="{{ asset($profile->pan_upload) }}" target="_blank">View File</a>

                                    </small>

                                @endif

                            </div>





                            {{-- Bank Details --}}

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">Bank Name</label>

                                <input type="text" name="bank_name" value="{{ old('bank_name', $profile->bank_name) }}" class="form-control">

                            </div>



                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">Branch</label>

                                <input type="text" name="branch" value="{{ old('branch', $profile->branch) }}" class="form-control">

                            </div>



                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">Account Number</label>

                                <input type="text" name="bank_account_no" value="{{ old('bank_account_no', $profile->bank_account_no) }}" class="form-control">

                            </div>



                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">IFSC Code</label>

                                <input type="text" name="ifsc_code" value="{{ old('ifsc_code', $profile->ifsc_code) }}" class="form-control">

                            </div>

                        </div>



                        <div class="text-center mt-4">

                            <button type="submit" class="btn btn-success px-4">Update Profile</button>

                            <a href="{{ route('profile.view') }}" class="btn btn-secondary px-4">Cancel</a>

                        </div>

                    </form>

                </div>

            </div>



        </div>

    </div>

</div>

@endsection

