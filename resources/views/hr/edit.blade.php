@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">HR - Edit Profile</h2>

    <div class="mb-3">
        <a href="{{ route('hr.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
    </div>

    @php $profile = $user->profile; @endphp

    @if(!$profile)
        <div class="alert alert-warning">This user has not created a profile yet.</div>
    @else
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('profile.update', $profile->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">First Name</label>
                            <input type="text" name="fname" class="form-control" value="{{ old('fname', $profile->fname) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Last Name</label>
                            <input type="text" name="lname" class="form-control" value="{{ old('lname', $profile->lname) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Designation</label>
                            <input type="text" name="designation" class="form-control" value="{{ old('designation', $profile->designation) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Date of Birth</label>
                            <input type="date" name="Date_of_Birth" class="form-control" value="{{ old('Date_of_Birth', $profile->Date_of_Birth) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Official Email</label>
                            <input type="email" name="official_email" class="form-control" value="{{ old('official_email', $profile->official_email) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Personal Email</label>
                            <input type="email" name="personal_email" class="form-control" value="{{ old('personal_email', $profile->personal_email) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Phone 1</label>
                            <input type="text" name="phone1" class="form-control" value="{{ old('phone1', $profile->phone1) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Phone 2</label>
                            <input type="text" name="phone2" class="form-control" value="{{ old('phone2', $profile->phone2) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Aadhaar Number</label>
                            <input type="text" name="aadhaar_number" class="form-control" value="{{ old('aadhaar_number', $profile->aadhaar_number) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">PAN Number</label>
                            <input type="text" name="pan" class="form-control" value="{{ old('pan', $profile->pan) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $profile->bank_name) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Branch</label>
                            <input type="text" name="branch" class="form-control" value="{{ old('branch', $profile->branch) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Account Number</label>
                            <input type="text" name="bank_account_no" class="form-control" value="{{ old('bank_account_no', $profile->bank_account_no) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">IFSC Code</label>
                            <input type="text" name="ifsc_code" class="form-control" value="{{ old('ifsc_code', $profile->ifsc_code) }}">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
