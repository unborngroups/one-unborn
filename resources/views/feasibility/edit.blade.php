@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h4 class="text-primary fw-bold mb-3">Edit Feasibility</h4>

    <div class="card shadow border-0 p-4">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('feasibility.update', $feasibility->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Feasibility Request ID</label>
                    <input type="text" class="form-control bg-light" value="{{ $feasibility->feasibility_request_id }}" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Type of Service <span class="text-danger">*</span></label>
                    <select name="type_of_service" id="type_of_service" class="form-select" required>
                        <option value="">Select</option>
                        <option value="Broadband" {{ $feasibility->type_of_service=='Broadband'?'selected':'' }}>Broadband</option>
                        <option value="ILL" {{ $feasibility->type_of_service=='ILL'?'selected':'' }}>ILL</option>
                        <option value="P2P" {{ $feasibility->type_of_service=='P2P'?'selected':'' }}>P2P</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Company <span class="text-danger">*</span></label>
                    <select name="company_id" id="company_id" class="form-select" required>
                        <option value="">Select Company</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ $feasibility->company_id==$company->id?'selected':'' }}>
                                {{ $company->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Client Name <span class="text-danger">*</span></label>
                    <select name="client_id" id="client_id" class="form-select" required>
                        <option value="">Select Client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ $feasibility->client_id==$client->id?'selected':'' }}>
                                {{ $client->business_name ?: $client->client_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Pincode <span class="text-danger">*</span></label>
                    <input type="text" name="pincode" id="pincode" maxlength="6" value="{{ $feasibility->pincode }}" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">State <span class="text-danger">*</span></label>
                    <select name="state" id="state" class="form-select select2-tags">
                        <option value="">Select or Type State</option>
                        <option value="{{ $feasibility->state }}" selected>{{ $feasibility->state }}</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">District <span class="text-danger">*</span></label>
                    <select name="district" id="district" class="form-select select2-tags">
                        <option value="">Select or Type District</option>
                        <option value="{{ $feasibility->district }}" selected>{{ $feasibility->district }}</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Area <span class="text-danger">*</span></label>
                    <select name="area" id="post_office" class="form-select select2-tags">
                        <option value="">Select or Type Area</option>
                        <option value="{{ $feasibility->area }}" selected>{{ $feasibility->area }}</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Address <span class="text-danger">*</span></label>
                    <textarea name="address" class="form-control" rows="2" required>{{ $feasibility->address }}</textarea>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">SPOC Name <span class="text-danger">*</span></label>
                    <input type="text" name="spoc_name" value="{{ $feasibility->spoc_name }}" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">SPOC Contact 1 <span class="text-danger">*</span></label>
                    <input type="text" name="spoc_contact1" value="{{ $feasibility->spoc_contact1 }}" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">SPOC Contact 2</label>
                    <input type="text" name="spoc_contact2" value="{{ $feasibility->spoc_contact2 }}" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">SPOC Email</label>
                    <input type="email" name="spoc_email" value="{{ $feasibility->spoc_email }}" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">No. of Links <span class="text-danger">*</span></label>
                    <select name="no_of_links" id="no_of_links" class="form-select" required>
                        <option value="">Select</option>
                        <option {{ $feasibility->no_of_links==1?'selected':'' }}>1</option>
                        <option {{ $feasibility->no_of_links==2?'selected':'' }}>2</option>
                        <option {{ $feasibility->no_of_links==3?'selected':'' }}>3</option>
                        <option {{ $feasibility->no_of_links==4?'selected':'' }}>4</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Vendor Type <span class="text-danger">*</span></label>
                    <select name="vendor_type" id="vendor_type" class="form-select" required>
                        <option value="">Select</option>
                        <option {{ $feasibility->vendor_type=='Same Vendor'?'selected':'' }}>Same Vendor</option>
                        <option {{ $feasibility->vendor_type=='Different Vendor'?'selected':'' }}>Different Vendor</option>
                        <option {{ $feasibility->vendor_type=='UBN'?'selected':'' }}>UBN</option>
                        <option {{ $feasibility->vendor_type=='UBS'?'selected':'' }}>UBS</option>
                        <option {{ $feasibility->vendor_type=='UBL'?'selected':'' }}>UBL</option>
                        <option {{ $feasibility->vendor_type=='INF'?'selected':'' }}>INF</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Speed <span class="text-danger">*</span></label>
                    <input type="text" name="speed" value="{{ $feasibility->speed }}" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Static IP <span class="text-danger">*</span></label>
                    <select name="static_ip" id="static_ip" class="form-select" required>
                        <option value="">Select</option>
                        <option value="Yes" {{ $feasibility->static_ip=='Yes'?'selected':'' }}>Yes</option>
                        <option value="No" {{ $feasibility->static_ip=='No'?'selected':'' }}>No</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Static IP Subnet</label>
                    <select name="static_ip_subnet" id="static_ip_subnet" class="form-select" {{ $feasibility->static_ip=='Yes'?'':'disabled' }}>
                        <option value="">Select Subnet</option>
                        @foreach(['/32','/31','/30','/29','/28','/27','/26','/25','/24'] as $sub)
                            <option value="{{ $sub }}" {{ $feasibility->static_ip_subnet==$sub?'selected':'' }}>{{ $sub }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Expected Delivery <span class="text-danger">*</span></label>
                    <input type="date" name="expected_delivery" value="{{ $feasibility->expected_delivery }}" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Expected Activation <span class="text-danger">*</span></label>
                    <input type="date" name="expected_activation" value="{{ $feasibility->expected_activation }}" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Hardware Required <span class="text-danger">*</span></label>
                    <select name="hardware_required" id="hardware_required" class="form-select" required>
                        <option value="">Select</option>
                        <option value="1" {{ $feasibility->hardware_required==1?'selected':'' }}>Yes</option>
                        <option value="0" {{ $feasibility->hardware_required==0?'selected':'' }}>No</option>
                    </select>
                </div>

                <div class="col-md-3" id="hardware_name_div" >
                    <label class="form-label fw-semibold">Hardware Model Name</label>
                    <input type="text" name="hardware_model_name" value="{{ $feasibility->hardware_model_name }}" class="form-control">
                </div>

                <input type="hidden" name="status" value="{{ $feasibility->status }}">
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update</button>
                <a href="{{ route('feasibility.index') }}" class="btn btn-secondary">Cancel</a>
            </div>

        </form>
    </div>
</div>
@endsection