@extends('layouts.app')



@section('content')

<div class="container-fluid py-4">

    <h4 class="text-info fw-bold mb-3">View Feasibility Details</h4>



    <div class="card shadow border-0 p-4">



        {{-- ✅ Display Feasibility Details in Read-Only Text Format --}}

        <div class="row g-3">



            {{-- Feasibility Request ID --}}

            <div class="col-md-3">

                <label class="form-label fw-semibold">Feasibility Request ID</label>

                <p class="form-control-plaintext">

                    <span class="badge bg-info fs-6">{{ $record->feasibility->feasibility_request_id ?? 'Not Generated' }}</span>

                </p>

            </div>



            {{-- Type of Service --}}

            <div class="col-md-3">

                <label class="form-label fw-semibold">Type of Service</label>

                <p class="form-control-plaintext">{{ $record->feasibility->type_of_service }}</p>

            </div>

             {{-- Company Name --}}

            <div class="col-md-3">

                <label class="form-label fw-semibold">Company Name</label>

                <p class="form-control-plaintext">{{ $record->feasibility->company->company_name ?? 'N/A' }}</p>

            </div>

            {{-- Client Name --}}

            <div class="col-md-3">

                <label class="form-label fw-semibold">Client Name</label>

                <p class="form-control-plaintext">{{ $record->feasibility->client->client_name ?? 'N/A' }}</p>

            </div>
            {{-- Delivery Company Name --}}
            <div class="col-md-3">  

                <label class="form-label fw-semibold">Delivery Company Name</label>

                <p class="form-control-plaintext">{{ $record->feasibility->delivery_company_name ?? 'N/A' }}</p>
            </div>
            {{-- Location ID --}}
            <div class="col-md-3">

                <label class="form-label fw-semibold">Location ID</label>
                <p class="form-control-plaintext">{{ $record->feasibility->location_id ?? 'N/A' }}</p>
            </div>
            {{-- Longitude --}}
            <div class="col-md-3">

                <label class="form-label fw-semibold">Longitude</label>
                <p class="form-control-plaintext">{{ $record->feasibility->longitude ?? 'N/A' }}</p>    
            </div>
            {{-- Latitude --}}
            <div class="col-md-3">

                <label class="form-label fw-semibold">Latitude</label>
                <p class="form-control-plaintext">{{ $record->feasibility->latitude ?? 'N/A' }}</p>
            </div>


            {{-- Pincode --}}

            <div class="col-md-3">

                <label class="form-label fw-semibold">Pincode</label>

                <p class="form-control-plaintext">{{ $record->feasibility->pincode }}</p>

            </div>



            {{-- State --}}

            <div class="col-md-3">

                <label class="form-label fw-semibold">State</label>

                <p class="form-control-plaintext">{{ $record->feasibility->state }}</p>

            </div>



            {{-- District --}}

            <div class="col-md-3">

                <label class="form-label fw-semibold">District</label>

                <p class="form-control-plaintext">{{ $record->feasibility->district }}</p>

            </div>



            {{-- Area --}}

            <div class="col-md-3">

                <label class="form-label fw-semibold">Area</label>

                <p class="form-control-plaintext">{{ $record->feasibility->area }}</p>

            </div>



            {{-- Address --}}

            <div class="col-md-3">

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

            {{-- Static IP Subnet --}}

            <div class="col-md-3">

                <label class="form-label fw-semibold">Static IP Subnet</label>

                <p class="form-control-plaintext">{{ $record->feasibility->static_ip_subnet ?? 'N/A' }}</p>

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

@php
    $hardwareDetails = $record->feasibility->hardware_details;
    if (is_string($hardwareDetails)) {
        $hardwareDetails = json_decode($hardwareDetails, true);
    }
@endphp
@if(!empty($hardwareDetails) && is_array($hardwareDetails))
    <div class="col-md-3">
        <label class="form-label fw-semibold">Hardware Model Name</label>
        @foreach($hardwareDetails as $item)
            @php
                $makeName = $item['make'] ?? null;
                $modelName = $item['model'] ?? null;
                // If only make_type_id/model_id present, fetch names
                if (!$makeName && !empty($item['make_type_id'])) {
                    $makeObj = \App\Models\MakeType::find($item['make_type_id']);
                    if ($makeObj && !($makeObj instanceof \Illuminate\Database\Eloquent\Collection)) {
                        $makeName = $makeObj->make_name;
                    } else {
                        $makeName = $item['make_type_id'];
                    }
                }
                if (!$modelName && !empty($item['model_id'])) {
                        $modelObj = \App\Models\ModelType::find($item['model_id']);
                    if ($modelObj && !($modelObj instanceof \Illuminate\Database\Eloquent\Collection)) {
                            $modelName = $modelObj->model . ' (ID: ' . $item['model_id'] . ')';
                    } else {
                        $modelName = $item['model_id'];
                    }
                }
            @endphp
            <p class="mb-1">
                Make:
                @if(is_array($makeName))
                    {{ collect($makeName)->map(function($id) { $obj = \App\Models\MakeType::find($id); return $obj ? $obj->make_name : $id; })->implode(', ') }}
                @else
                    {{ is_object($makeName) ? '[object]' : ($makeName ?? 'N/A') }}
                @endif
                <br>
                Model:
                @if(is_array($modelName))
                        {{ collect($modelName)->map(function($id) { $obj = \App\Models\ModelType::find($id); return $obj ? $obj->model_name : $id; })->implode(', ') }}
                @else
                    {{ is_object($modelName) ? '[object]' : ($modelName ?? 'N/A') }}
                @endif
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

        {{-- Vendor Information Section --}}
@if(
    $record->vendor1_name || $record->vendor1_arc || $record->vendor1_otc || $record->vendor1_static_ip_cost || $record->vendor1_delivery_timeline ||
    $record->vendor2_name || $record->vendor2_arc || $record->vendor2_otc || $record->vendor2_static_ip_cost || $record->vendor2_delivery_timeline ||
    $record->vendor3_name || $record->vendor3_arc || $record->vendor3_otc || $record->vendor3_static_ip_cost || $record->vendor3_delivery_timeline ||
    $record->vendor4_name || $record->vendor4_arc || $record->vendor4_otc || $record->vendor4_static_ip_cost || $record->vendor4_delivery_timeline
)

<hr class="my-4">
<h5 class="text-primary fw-bold mb-3">Vendor Information</h5>

<div class="row g-3">

    @for($i = 1; $i <= 4; $i++)

        @php
            $vName = 'vendor'.$i.'_name';
            $vArc  = 'vendor'.$i.'_arc';
            $vOtc  = 'vendor'.$i.'_otc';
            $vIp   = 'vendor'.$i.'_static_ip_cost';
            $vTime = 'vendor'.$i.'_delivery_timeline';
            $vremark = 'vendor'.$i.'_remarks';
        @endphp

        {{-- show vendor even if name NULL but costs present --}}
    @if($record->$vName !== null || $record->$vArc !== null || $record->$vOtc !== null || $record->$vIp !== null || $record->$vTime !== null || $record->$vremark !== null)


        <div class="col-md-12">
            <div class="card bg-light">
                <div class="card-body">

                    <h6 class="fw-bold text-secondary mb-3">Vendor {{ $i }}</h6>

                    <div class="row">

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Name</label>
                            <p class="form-control-plaintext">
                                {{ ($record->$vName == 'Self' || $record->$vName == 0) ? 'Self' : $record->$vName }}
                            </p>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">ARC</label>
                            <p class="form-control-plaintext">{{ $record->$vArc ?? 'N/A' }}</p>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">OTC</label>
                            <p class="form-control-plaintext">{{ $record->$vOtc ?? 'N/A' }}</p>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Static IP Cost</label>
                            <p class="form-control-plaintext">{{ $record->$vIp ?? 'N/A' }}</p>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Delivery Timeline</label>
                            <p class="form-control-plaintext">{{ $record->$vTime ?? 'N/A' }}</p>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Remarks</label>
                            <p class="form-control-plaintext">{{ $record->$vremark ?? 'N/A' }}</p>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        @endif

    @endfor

</div>

@endif

        {{-- ✅ Back button --}}

        <div class="mt-4 text-end">

            @if($record->status == 'Open')

                <a href="{{ route('sm.feasibility.open') }}" class="btn btn-secondary">

                    <i class="bi bi-arrow-left"></i> Back to Open

                </a>

            @elseif($record->status == 'InProgress')

                <a href="{{ route('sm.feasibility.inprogress') }}" class="btn btn-secondary">

                    <i class="bi bi-arrow-left"></i> Back to In Progress

                </a>

            @else

                <a href="{{ route('sm.feasibility.closed') }}" class="btn btn-secondary">

                    <i class="bi bi-arrow-left"></i> Back to Closed

                </a>

            @endif

        </div>

    </div>

</div>

@endsection