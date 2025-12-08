@extends('layouts.app')



@section('content')

{{-- ✅ Validation Errors Display --}}

@if ($errors->any())

    <div class="alert alert-danger">

        <ul class="mb-0">

            @foreach ($errors->all() as $error)

                <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

@endif



<div class="container py-4">

    <h3 class="mb-3">Edit User</h3>

    <div class="card shadow border-0 p-4">

        <form action="{{ route('users.update',$user) }}" method="POST">

            <?= csrf_field() ?>

             @method('PUT')
             <div class="row">

            <div class="col-md-6 mb-3">

                <label>Name</label>

                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>

            </div>



            {{-- 🔹 User Type Dropdown --}}

             <div class="col-md-6 mb-3">

                <label>User Type</label>

                <select name="user_type_id" class="form-control" required>

                <option value="">Select User Type</option>

                @foreach($userTypes as $type)

               <option value="{{ $type->id }}" 

                {{ old('user_type_id', $user->user_type_id ?? '') == $type->id ? 'selected' : '' }}>

                {{ ucfirst($type->name) }}

               </option>

               @endforeach

               </select>

                @error('user_type_id')

                <span class="text-danger">{{ $message }}</span>

                 @enderror

            </div>



            <!-- <div class="mb-3">

                <label>Email</label>

                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>

            </div> -->



            {{-- 🔹 Official Email --}}

            <div class="col-md-6 mb-3">

    <label>Official Email</label>

    <input type="email" name="official_email" value="{{ old('official_email', $user->official_email) }}" class="form-control" required>

</div>

{{-- 🔘 Send Email Checkbox --}}

<div class="form-check mb-3">

    <input type="checkbox" name="send_email" value="1" class="form-check-input" id="sendEmailCheckbox">

    <label for="sendEmailCheckbox" class="form-check-label">

        Send update notification email?

    </label>

</div>





{{-- 🔹 Personal Email --}}

 <div class="col-md-6 mb-3">

    <label>Personal Email</label>

    <input type="email" name="personal_email" value="{{ old('personal_email', $user->personal_email) }}" class="form-control">

</div>



{{-- 🔹 Mobile Number --}}

             <div class="col-md-6 mb-3">

                <label>Mobile</label>

                <input type="text" name="mobile" value="{{ old('mobile', $user->mobile) }}" class="form-control">

            </div>



                     {{-- ✅ Company Dropdown (Multiple Select) --}}

 <div class="col-md-6 mb-3">

    <label class="form-label">Company</label>

    <select id="company_id" name="companies[]" class="form-select" multiple required>

        @foreach($companies as $company)

            <option value="{{ $company->id }}" 

                {{ in_array($company->id, old('companies', $selectedCompanies)) ? 'selected' : '' }}>

                {{ $company->company_name }}

            </option>

        @endforeach

    </select>

    <small class="text-muted">Hold <b>Ctrl</b> (Windows) or <b>Cmd</b> (Mac) to select multiple.</small>

</div>



{{-- 🔹 Date of Birth --}}

             <div class="col-md-6 mb-3">

                <label>Date of Birth</label>

                <input type="date" name="Date_of_Birth" value="{{ old('Date_of_Birth', $user->Date_of_Birth) }}" class="form-control" required>

            </div>

            {{-- 🔹 Date of Joining --}}

             <div class="col-md-6 mb-3">

                <label>Date of Joining</label>

                <input type="date" name="Date_of_Joining" value="{{ old('Date_of_Joining', $user->Date_of_Joining) }}" class="form-control" required>

            </div>
            </div>

            <!-- {{-- 🔹 Status Dropdown --}}

            <div class="mb-3">

                <label>Status</label>

                <select name="status" class="form-control">

                    <option {{ $user->status=='Active'?'selected':'' }}>Active</option>

                    <option {{ $user->status=='Inactive'?'selected':'' }}>Inactive</option>

                </select>

            </div> -->

             {{--  Status Dropdown --}}

            <input type="hidden" name="status" value="Active">



            {{-- 🔘 Buttons --}}

            <button class="btn btn-warning">Update</button>

            <a href="{{ route('users.index') }}" class="btn btn-secondary">Back</a>

        </form>

    </div>

</div>
<style>
    label{
        font-weight: 600;
    }
</style>

@endsection