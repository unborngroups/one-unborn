@extends('layouts.app')



@section('content')

<div class="container py-4">

    <h3 class="mb-3 text-primary">Add User</h3>

    <div class="card shadow border-0 p-4">



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



        <form action="{{ route('users.store') }}" method="POST">

            @csrf

<div class="row">

            {{-- ✅ Name --}}

            <div class="col-md-6 mb-3">

                <label class="form-label">Name</label>

                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>

            </div>



            {{-- ✅ User Type --}}

            <div class="col-md-6 mb-3">

                <label class="form-label">User Type</label>

                <select name="user_type_id" class="form-select" required>

                    <option value="">Select User Type</option>

                    @foreach($userTypes as $type)

                        <option value="{{ $type->id }}" {{ old('user_type_id') == $type->id ? 'selected' : '' }}>

                            {{ ucfirst($type->name) }}

                        </option>

                    @endforeach

                </select>

                @error('user_type_id')

                    <span class="text-danger">{{ $message }}</span>

                @enderror

            </div>



            {{-- ✅ Email --}}

            <!-- <div class="mb-3">

                <label class="form-label">Email</label>

                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>

            </div> -->

            <div class="col-md-6 mb-3">

    <label>Official Email</label>

    <input type="email" name="official_email" value="{{ old('official_email', $user->official_email ?? '') }}" class="form-control" required>



</div>



<div class="col-md-6 mb-3">

    <label>Personal Email</label>

    <input type="email" name="personal_email" value="{{ old('personal_email', $user->personal_email ?? '') }}" class="form-control">

</div>





            {{-- ✅ Mobile --}}

            <div class="col-md-6 mb-3">

                <label class="form-label">Mobile</label>

                <input type="text" name="mobile" class="form-control" value="{{ old('mobile') }}">

            </div>



            {{-- ✅ Company Dropdown (Multiple Select) --}}

<div class="col-md-6 mb-3">

    <label class="form-label">Company</label>

    <select id="company_id" name="companies[]" class="form-select" multiple required>

        @foreach($companies as $company)

            <option value="{{ $company->id }}" 

                {{ in_array($company->id, old('companies', [])) ? 'selected' : '' }}>

                {{ $company->company_name }}

            </option>

        @endforeach

    </select>

    <small class="text-muted">Hold <b>Ctrl</b> (Windows) or <b>Cmd</b> (Mac) to select multiple.</small>

</div>



            {{-- ✅ Date of Birth --}}

            <div class="col-md-6 mb-3">

                <label class="form-label">Date of Birth</label>

                <input type="date" name="Date_of_Birth" class="form-control" placeholder="select DOB" value="{{ old('Date_of_Birth') }}" required>

            </div>



            {{-- ✅ Date of Joining --}}

            <div class="col-md-6 mb-3">

                <label class="form-label">Date of Joining</label>

                <input type="date" name="Date_of_Joining" class="form-control" placeholder="select DOJ" value="{{ old('Date_of_Joining') }}" required>

            </div>
</div>


            {{-- ✅ Status --}}

            <!-- <div class="mb-3">

                <label class="form-label">Status</label>

                <select name="status" class="form-select">

                    <option value="Active" {{ old('status', 'Active') == 'Active' ? 'selected' : '' }}>Active</option>

                    <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>

                </select>

            </div> -->

             {{--  Status Dropdown --}}

            <input type="hidden" name="status" value="Active">





            {{-- ✅ Buttons --}}

            <div class="d-flex justify-content-between">

                <button type="submit" class="btn btn-success">

                    <i class="bi bi-save"></i> Save

                </button>

                <a href="{{ route('users.index') }}" class="btn btn-secondary">

                    <i class="bi bi-arrow-left"></i> Back

                </a>

            </div>

        </form>

    </div>

</div>



{{-- ✅ JS: Load Templates dynamically --}}

<script>

document.getElementById('company_id').addEventListener('change', function () {

    let companyId = this.value;

    let templateDropdown = document.getElementById('email_template_id');

    templateDropdown.innerHTML = '<option value="">-- Select Template --</option>';



    if (companyId) {

        fetch('/companies/' + companyId + '/templates')

            .then(response => response.json())

            .then(data => {

                if (data.length > 0) {

                    data.forEach(function (template) {

                        templateDropdown.innerHTML += 

                            `<option value="${template.id}">${template.subject}</option>`;

                    });

                }

            })

            .catch(error => console.error('Error fetching templates:', error));

    }

});

</script>
<style>
    label{
        font-weight: 600;
    }
</style>
@endsection

