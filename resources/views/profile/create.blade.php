@extends('layouts.app')



@section('content')

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">



            {{-- ✅ Show Validation Errors --}}

            @if ($errors->any())

                <div class="alert alert-danger">

                    <ul class="mb-0">

                        @foreach ($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif



            <div class="card shadow-sm">

                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">Create / Update Your Profile</h5>

                </div>



                <div class="card-body">

                    <!-- ✅ enctype required for file upload -->

                    <form method="POST" action="{{ route('profile.store') }}" enctype="multipart/form-data">

                        @csrf



                        {{-- 🌟 Profile Photo --}}

                        <div class="text-center mb-4">

                            <div class="position-relative d-inline-block">

                                <div class="border rounded-circle overflow-hidden shadow-sm" style="width: 130px; height: 130px;">

                                    <img id="photoPreview"

                                         src="{{ asset('images/default-avatar.png') }}"

                                         alt="Profile Preview"

                                         class="img-fluid w-100 h-100"

                                         style="object-fit: cover;">

                                </div>



                                <label for="photo" 

                                       class="btn btn-sm btn-primary position-absolute bottom-0 end-0 translate-middle"

                                       style="border-radius: 50%; padding: 6px 8px; cursor: pointer;">

                                    <i class="fa fa-camera"></i>

                                </label>



                                <input type="file" name="photo" id="photo" class="d-none" accept="image/*"

                                       onchange="previewImage(event)">

                            </div>

                            <p class="mt-2 text-muted small">Upload a profile photo (JPG, PNG)</p>

                        </div>



                        {{-- Basic Info --}}

                        <div class="mb-3">

                            <label class="form-label">First Name <span class="text-danger">*</span></label>

                            <input type="text" class="form-control" name="fname" value="{{ old('fname') }}" required>

                        </div>



                        <div class="mb-3">

                            <label class="form-label">Last Name <span class="text-danger">*</span></label>

                            <input type="text" class="form-control" name="lname" value="{{ old('lname') }}" required>

                        </div>



                        <div class="mb-3">

                            <label class="form-label">Designation <span class="text-danger">*</span></label>

                            <input type="text" class="form-control" name="designation" value="{{ old('designation') }}" required>

                        </div>



                        {{-- Date of Birth --}}

                        <div class="mb-3">

                            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>

                            <input type="date" name="Date_of_Birth" class="form-control" value="{{ old('Date_of_Birth') }}" required>

                        </div>



                        {{-- Email --}}

                        <div class="mb-3">

                            <label class="form-label">Official Email <span class="text-danger">*</span></label>

                            <input type="email" name="official_email" class="form-control" value="{{ old('official_email') }}" required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">Personal Email <span class="text-danger">*</span></label>

                            <input type="email" name="personal_email" class="form-control" value="{{ old('personal_email') }}" required>

                        </div>



                        {{-- Phone Numbers --}}

                        <div class="mb-3">

                            <label class="form-label">Phone Number 1 <span class="text-danger">*</span></label>

                            <input type="number" class="form-control" name="phone1" value="{{ old('phone1') }}" required>

                        </div>



                        <div class="mb-3">

                            <label class="form-label">Phone Number 2 <span class="text-danger">*</span></label>

                            <input type="number" class="form-control" name="phone2" value="{{ old('phone2') }}">

                        </div>



                        {{-- Aadhaar --}}

                        <h5 class="text-secondary mt-4">Aadhaar Details </h5>

                        <div class="mb-3">

                            <label class="form-label">Aadhaar Number <span class="text-danger">*</span></label>

                            <input type="number" class="form-control" name="aadhaar_number" value="{{ old('aadhaar_number') }}" required>

                        </div>



                        <div class="mb-3">

                            <label class="form-label">Aadhaar Upload <span class="text-danger">*</span></label>

                            <input type="file" class="form-control" name="aadhaar_upload" required>

                        </div>



                        {{-- PAN --}}

                        <h5 class="text-secondary mt-4">PAN Details</h5>

                        <div class="mb-3">

                            <label class="form-label">PAN Number <span class="text-danger">*</span></label>

                            <input type="text" name="pan" class="form-control" value="{{ old('pan') }}" placeholder="PAN No">

                        </div>



                        <div class="mb-3">

                            <label class="form-label">PAN Upload <span class="text-danger">*</span></label>

                            <input type="file" class="form-control" name="pan_upload" required>

                        </div>



                        {{-- Bank Details --}}

                        <h5 class="text-secondary mt-4">Bank Details <span class="text-danger">*</span></h5>

                        <input type="text" name="bank_name" class="form-control mb-2" placeholder="Bank Name" value="{{ old('bank_name') }}">

                        <input type="text" name="branch" class="form-control mb-2" placeholder="Branch" value="{{ old('branch') }}">

                        <input type="text" name="bank_account_no" class="form-control mb-2" placeholder="Account No" value="{{ old('bank_account_no') }}">

                        <input type="text" name="ifsc_code" class="form-control mb-4" placeholder="IFSC Code" value="{{ old('ifsc_code') }}">



                        <button type="submit" class="btn btn-success w-100 py-2">Save Profile</button>

                    </form>

                </div>

            </div>



        </div>

    </div>

</div>



{{-- ✅ JS for instant image preview --}}

<script>

function previewImage(event) {

    const input = event.target;

    const preview = document.getElementById('photoPreview');

    if (input.files && input.files[0]) {

        const reader = new FileReader();

        reader.onload = e => preview.src = e.target.result;

        reader.readAsDataURL(input.files[0]);

    }

}

</script>



{{-- ✅ FontAwesome for Camera Icon --}}

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@endsection

