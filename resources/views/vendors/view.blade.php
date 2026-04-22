@extends('layouts.app')



@section('content')

<div class="container py-4">

    <h3 class="mb-3 text-primary">View Vendor Details</h3>



    <div class="card shadow border-0 p-4">

        {{-- Basic Details --}}

        <h5 class="text-secondary">Basic Details</h5>

        <div class="row mb-3">

            <div class="col-md-6">

                <label class="form-label fw-bold">Vendor Name:</label>

                <p class="form-control-plaintext">{{ $vendor->vendor_name }}</p>

            </div>

            <div class="col-md-6">

                <label class="form-label fw-bold">Vendor Code:</label>

                <p class="form-control-plaintext">{{ $vendor->vendor_code }}</p>

            </div>

        </div>



        <div class="mb-3">

            <label class="form-label fw-bold">Business Display Name:</label>

            <p class="form-control-plaintext">{{ $vendor->business_display_name }}</p>

        </div>

        {{-- Address --}}

        <h5 class="text-secondary mt-3">Address</h5>

        <p class="form-control-plaintext mb-0">{{ $vendor->address1 }}</p>

        <p class="form-control-plaintext mb-0">{{ $vendor->address2 }}</p>

        <p class="form-control-plaintext mb-2">{{ $vendor->address3 }}</p>



        <div class="row mb-3">

            <div class="col-md-4">

                <label class="form-label fw-bold">City:</label>

                <p class="form-control-plaintext">{{ $vendor->city }}</p>

            </div>

            <div class="col-md-4">

                <label class="form-label fw-bold">State:</label>

                <p class="form-control-plaintext">{{ $vendor->state }}</p>

            </div>

            <div class="col-md-4">

                <label class="form-label fw-bold">Country:</label>

                <p class="form-control-plaintext">{{ $vendor->country }}</p>

            </div>

        </div>



        <div class="mb-3">

            <label class="form-label fw-bold">Pincode:</label>

            <p class="form-control-plaintext">{{ $vendor->pincode }}</p>

        </div>



        {{-- Contact Person --}}

        <h5 class="text-secondary mt-3">Contact Person</h5>

        <div class="row">

            <div class="col-md-4">

                <label class="form-label fw-bold">Name:</label>

                <p class="form-control-plaintext">{{ $vendor->contact_person_name }}</p>

            </div>

            <div class="col-md-4">

                <label class="form-label fw-bold">Mobile:</label>

                <p class="form-control-plaintext">{{ $vendor->contact_person_mobile }}</p>

            </div>

            <div class="col-md-4">

                <label class="form-label fw-bold">Email:</label>

                <p class="form-control-plaintext">{{ $vendor->contact_person_email }}</p>

            </div>

        </div>



        {{-- Legal Details --}}

        <h5 class="text-secondary mt-3">Legal Details</h5>

        <div class="row">

            <div class="col-md-6">

                <label class="form-label fw-bold">GSTIN:</label>

                <p class="form-control-plaintext">{{ $vendor->gstin }}</p>

            </div>

            <div class="col-md-6">

                <label class="form-label fw-bold">PAN No:</label>

                <p class="form-control-plaintext">{{ $vendor->pan_no }}</p>

            </div>

            <div class="col-md-6">

                <label class="form-label fw-bold">Bank Account No:</label>

                <p class="form-control-plaintext">{{ $vendor->bank_account_no }}</p>

            </div>

            <div class="col-md-6">

                <label class="form-label fw-bold">IFSC Code:</label>

                <p class="form-control-plaintext">{{ $vendor->ifsc_code }}</p>

            </div>

        </div>



        {{-- Status --}}

        <div class="mt-3">

            <label class="form-label fw-bold">Status:</label>

            <span class="badge bg-{{ $vendor->status == 'Active' ? 'success' : 'secondary' }}">

                {{ $vendor->status }}

            </span>

        </div>



        {{-- Buttons --}}

        <div class="mt-4">

            <a href="{{ route('vendors.index') }}" class="btn btn-secondary">Back</a>

            <!-- <a href="{{ route('vendors.edit', $vendor->id) }}" class="btn btn-primary">Edit Vendor</a> -->

        </div>

    </div>

</div>

@endsection

