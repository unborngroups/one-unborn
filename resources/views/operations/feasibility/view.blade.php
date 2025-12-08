@extends('layouts.app')



@section('content')

<div class="container-fluid py-4">

    <h4 class="text-primary fw-bold mb-3">View Feasibility Details</h4>



    <div class="card shadow border-0 p-4">

        {{-- ✅ Display Feasibility Details in Read-Only Text Format --}}

        <div class="row g-3">



            {{-- Feasibility Request ID --}}

            <div class="col-md-4">

                <label class="form-label fw-semibold">Feasibility Request ID</label>

                <p class="form-control-plaintext">

                    <span class="badge bg-primary fs-6">{{ $record->feasibility->feasibility_request_id ?? 'Not Generated' }}</span>

                </p>

            </div>



            {{-- Type of Service --}}

            <div class="col-md-4">

                <label class="form-label fw-semibold">Type of Service</label>

                <p class="form-control-plaintext">{{ $record->feasibility->type_of_service }}</p>

            </div>



            {{-- Client Name --}}

            <div class="col-md-4">

                <label class="form-label fw-semibold">Client Name</label>

                <p class="form-control-plaintext">{{ $record->feasibility->client->client_name ?? 'N/A' }}</p>

            </div>



            {{-- Pincode --}}

            <div class="col-md-4">

                <label class="form-label fw-semibold">Pincode</label>

                <p class="form-control-plaintext">{{ $record->feasibility->pincode }}</p>

            </div>



            {{-- State --}}

            <div class="col-md-4">

                <label class="form-label fw-semibold">State</label>

                <p class="form-control-plaintext">{{ $record->feasibility->state }}</p>

            </div>



            {{-- District --}}

            <div class="col-md-4">

                <label class="form-label fw-semibold">District</label>

                <p class="form-control-plaintext">{{ $record->feasibility->district }}</p>

            </div>



            {{-- Area --}}

            <div class="col-md-4">

                <label class="form-label fw-semibold">Area</label>

                <p class="form-control-plaintext">{{ $record->feasibility->area }}</p>

            </div>



            {{-- Address --}}

            <div class="col-md-6">

                <label class="form-label fw-semibold">Address</label>

                <p class="form-control-plaintext">{{ $record->feasibility->address }}</p>

            </div>



            {{-- SPOC Name --}}

            <div class="col-md-3">

                <label class="form-label fw-semibold">SPOC Name</label>

                <p class="form-control-plaintext">{{ $record->feasibility->spoc_name }}</p>

            </div>



            {{-- SPOC Contact 1 --}}

            <div class="col-md-3">

                <label class="form-label fw-semibold">SPOC Contact 1</label>

                <p class="form-control-plaintext">{{ $record->feasibility->spoc_contact1 }}</p>

            </div>



            {{-- SPOC Contact 2 --}}

            <div class="col-md-3">

                <label class="form-label fw-semibold">SPOC Contact 2</label>

                <p class="form-control-plaintext">{{ $record->feasibility->spoc_contact2 }}</p>

            </div>



            {{-- SPOC Email --}}

            <div class="col-md-3">

                <label class="form-label fw-semibold">SPOC Email</label>

                <p class="form-control-plaintext">{{ $record->feasibility->spoc_email }}</p>

            </div>



            {{-- No. of Links --}}

            <div class="col-md-3">

                <label class="form-label fw-semibold">No. of Links</label>

                <p class="form-control-plaintext">{{ $record->feasibility->no_of_links }}</p>

            </div>



            {{-- Vendor Type --}}

            <div class="col-md-3">

                <label class="form-label fw-semibold">Vendor Type</label>

                <p class="form-control-plaintext">{{ $record->feasibility->vendor_type }}</p>

            </div>



            {{-- Speed --}}

            <div class="col-md-3">

                <label class="form-label fw-semibold">Speed</label>

                <p class="form-control-plaintext">{{ $record->feasibility->speed }}</p>

            </div>



            {{-- Static IP --}}

            <div class="col-md-3">

                <label class="form-label fw-semibold">Static IP</label>

                <p class="form-control-plaintext">{{ $record->feasibility->static_ip }}</p>

            </div>



            {{-- Expected Delivery --}}

            <div class="col-md-3">

                <label class="form-label fw-semibold">Expected Delivery</label>

                <p class="form-control-plaintext">{{ $record->feasibility->expected_delivery }}</p>

            </div>



            {{-- Expected Activation --}}

            <div class="col-md-3">

                <label class="form-label fw-semibold">Expected Activation</label>

                <p class="form-control-plaintext">{{ $record->feasibility->expected_activation }}</p>

            </div>



            {{-- Hardware Required --}}

            <div class="col-md-3">

                <label class="form-label fw-semibold">Hardware Required</label>

                <p class="form-control-plaintext">{{ $record->feasibility->hardware_required ? 'Yes' : 'No' }}</p>

            </div>


@if(!empty($record->feasibility->hardware_details))
    <div class="col-md-3">
        <label class="form-label fw-semibold">Hardware Model Name</label>
        @foreach(json_decode($record->feasibility->hardware_details, true) as $item)
            <p class="mb-1">
                Make: {{ $item['make'] }} <br>
                Model: {{ $item['model'] }}
            </p>
        @endforeach
    </div>
@else
    <div class="col-md-3">
        <label class="form-label fw-semibold">Hardware Model Name</label>
        <p class="form-control-plaintext">N/A</p>
    </div>
@endif



            {{-- Status --}}

            <div class="col-md-3">

                <label class="form-label fw-semibold">Feasibility Status</label>

                <p class="form-control-plaintext">

                    <span class="badge 

                        @if($record->status == 'Open') bg-primary

                        @elseif($record->status == 'InProgress') bg-warning text-dark

                        @elseif($record->status == 'Closed') bg-success

                        @endif">

                        {{ $record->status }}

                    </span>

                </p>

            </div>



        </div>



        {{-- ✅ Back button --}}

        <div class="mt-4 text-end">

            @if($record->status == 'Open')

                <a href="{{ route('operations.feasibility.open') }}" class="btn btn-secondary">

                    <i class="bi bi-arrow-left"></i> Back to Open

                </a>

            @elseif($record->status == 'InProgress')

                <a href="{{ route('operations.feasibility.inprogress') }}" class="btn btn-secondary">

                    <i class="bi bi-arrow-left"></i> Back to In Progress

                </a>

            @else

                <a href="{{ route('operations.feasibility.closed') }}" class="btn btn-secondary">

                    <i class="bi bi-arrow-left"></i> Back to Closed

                </a>

            @endif

        </div>

    </div>

</div>

@endsection