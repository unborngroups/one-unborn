@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-eye me-2"></i>View Deliverable - {{ $record->delivery_id ?? 'N/A' }}
            </h5>
            <div class="d-flex gap-2">
                <span class="badge bg-light text-dark">{{ $record->status }}</span>
                <!-- <a href="{{ route('operations.deliverables.edit', $record->id) }}" class="btn btn-light btn-sm">
                    <i class="bi bi-pencil"></i> Edit
                </a> -->
                <a href="{{ route('operations.deliverables.open') }}" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card-body">
            {{-- Feasibility Closed Details Card (Read-only) --}}
            <div class="card mb-4 border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Feasibility Closed Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <strong>Feasibility ID:</strong><br>
                            {{ $record->feasibility->feasibility_request_id ?? 'N/A' }}
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong>Type of Service:</strong><br>
                            {{ $record->feasibility->type_of_service ?? 'N/A' }}
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong>Company Name:</strong><br>
                            {{ $record->feasibility->company->company_name ?? 'N/A' }}
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong>Client Name:</strong><br>
                            {{ $record->feasibility->client->client_name ?? 'N/A' }}
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong>Pincode:</strong><br>
                            {{ $record->feasibility->pincode ?? 'N/A' }}
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong>State:</strong><br>
                            {{ $record->feasibility->state ?? 'N/A' }}
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong>District:</strong><br>
                            {{ $record->feasibility->district ?? 'N/A' }}
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong>Area:</strong><br>
                            {{ $record->feasibility->area ?? 'N/A' }}
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong>Address:</strong><br>
                            {{ $record->feasibility->address ?? 'N/A' }}
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong>Speed:</strong><br>
                            {{ $record->feasibility->speed ?? 'N/A' }}
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong>SPOC Name:</strong><br>
                            {{ $record->feasibility->spoc_name ?? 'N/A' }}
                        </div>
                         <div class="col-md-3">
                            <strong>SPOC Contact1:</strong><br>
                            {{ $record->feasibility->spoc_contact1 ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>No. Of Links:</strong><br>
                            {{ $record->feasibility->no_of_links ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Vendor Type:</strong><br>
                            {{ $record->feasibility->vendor_type ?? 'N/A' }}
                        </div>
                        
                        <div class="col-md-3">
                            <strong>Static IP:</strong><br>
                            {{ $record->feasibility->static_ip ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Static IP Subnet:</strong><br>
                            {{ $record->feasibility->static_ip_subnet ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Expected Delivery:</strong><br>
                            {{ $record->feasibility->expected_delivery ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Expected Activation:</strong><br>
                            {{ $record->feasibility->expected_activation ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Hardware Required:</strong><br>
                            {{ $record->feasibility->hardware_required ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Hardware Model Name:</strong><br>
                            {{ $record->feasibility->hardware_model_name ?? 'N/A' }}
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong>PO Number:</strong><br>
                            <span class="badge bg-success">{{ $record->po_number ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong>PO Date:</strong><br>
                            {{ $record->po_date ? \Carbon\Carbon::parse($record->po_date)->format('d-m-Y') : 'N/A' }}
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong>Vendor:</strong><br>
                            {{ $record->vendor ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Deliverable Information --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-clipboard-data me-2"></i>Deliverable Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <strong>Plans Name:</strong><br>
                            {{ $record->plans_name ?? 'N/A' }}
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Speed (Mbps) - Plan:</strong><br>
                            {{ $record->speed_in_mbps_plan ?? 'N/A' }}
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>No. of Months Renewal:</strong><br>
                            {{ $record->no_of_months_renewal ?? 'N/A' }}
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Date of Activation:</strong><br>
                            {{ $record->date_of_activation ? \Carbon\Carbon::parse($record->date_of_activation)->format('d-m-Y') : 'N/A' }}
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Date of Expiry:</strong><br>
                            {{ $record->date_of_expiry ? \Carbon\Carbon::parse($record->date_of_expiry)->format('d-m-Y') : 'N/A' }}
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>SLA:</strong><br>
                            {{ $record->sla ?? 'N/A' }}
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Status of Link:</strong><br>
                            <span class="badge {{ $record->status_of_link == 'Active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ $record->status_of_link ?? 'N/A' }}
                            </span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Circuit ID:</strong><br>
                            <span>
                                {{ $record->circuit_id ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mode of Delivery Details --}}
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-send me-2"></i>Mode of Delivery: {{ $record->mode_of_delivery ?? 'N/A' }}</h6>
                </div>
                <div class="card-body">
                    @if($record->mode_of_delivery === 'PPPoE')
                        <div class="row">
                            <!-- <div class="col-md-3 mb-3">
                                <strong>Circuit ID:</strong><br>
                                {{ $record->circuit_id ?? 'N/A' }}
                            </div> -->
                            <div class="col-md-3 mb-3">
                                <strong>PPPoE Username:</strong><br>
                                {{ $record->pppoe_username ?? 'N/A' }}
                            </div>
                            <div class="col-md-3 mb-3">
                                <strong>PPPoE Password:</strong><br>
                                {{ $record->pppoe_password ?? 'N/A' }}
                            </div>
                            <div class="col-md-3 mb-3">
                                <strong>PPPoE VLAN:</strong><br>
                                {{ $record->pppoe_vlan ?? 'N/A' }}
                            </div>
                        </div>
                    @elseif($record->mode_of_delivery === 'DHCP')
                        <div class="row">
                            <!-- <div class="col-md-3 mb-3">
                                <strong>Circuit ID:</strong><br>
                                {{ $record->circuit_id ?? 'N/A' }}
                            </div> -->
                            <div class="col-md-3 mb-3">
                                <strong>DHCP IP Address:</strong><br>
                                {{ $record->dhcp_ip_address ?? 'N/A' }}
                            </div>
                            <div class="col-md-3 mb-3">
                                <strong>DHCP VLAN:</strong><br>
                                {{ $record->dhcp_vlan ?? 'N/A' }}
                            </div>
                        </div>
                    @elseif(in_array($record->mode_of_delivery, ['Static', 'Static IP']))
                        <div class="row">
                            <!-- <div class="col-md-3 mb-3">
                                <strong>Circuit ID:</strong><br>
                                {{ $record->circuit_id ?? 'N/A' }}
                            </div> -->
                            <div class="col-md-3 mb-3">
                                <strong>Static IP Address:</strong><br>
                                {{ $record->static_ip_address ?? 'N/A' }}
                            </div>
                            <div class="col-md-3 mb-3">
                                <strong>Subnet :</strong><br>
                                {{ $record->static_ip_subnet ?? 'N/A' }}
                            </div>
                            <div class="col-md-3 mb-3">
                                <strong>VLAN Tag:</strong><br>
                                {{ $record->static_vlan_tag ?? 'N/A' }}
                            </div>
                            <div class="col-md-3 mb-3">
                                <strong>Network IP:</strong><br>
                                {{ $record->network_ip ?? 'N/A' }}
                            </div>
                            <div class="col-md-3 mb-3">
                                <strong>Gateway:</strong><br>
                                {{ $record->static_gateway ?? 'N/A' }}
                            </div>
                            <div class="col-md-3 mb-3">
                                <strong>IP Address:</strong><br>
                                {{ $record->static_ip_address ?? 'N/A' }}
                            </div>                            
                            <div class="col-md-3 mb-3">
                                <strong>Subnet Mask:</strong><br>
                                {{ $record->subnet_mask ?? 'N/A' }}
                            </div>
                            <div class="col-md-3 mb-3">
                                <strong>Usable IPs:</strong><br>
                                {{ $record->usable_ips ?? 'N/A' }}
                            </div> 
                            
                        </div>
                    @elseif($record->mode_of_delivery === 'PAYMENTS')
                     <div class="row">
                            <!-- <div class="col-md-3 mb-3">
                                <strong>Circuit ID:</strong><br>
                                {{ $record->circuit_id ?? 'N/A' }}
                            </div> -->
                            <div class="col-md-3 mb-3">
                                <strong>Login URL:</strong><br>
                                {{ $record->payment_login_url ?? 'N/A' }}
                            </div>
                            <div class="col-md-3 mb-3">
                                <strong>Quick URL:</strong><br>
                                {{ $record->payment_quick_url ?? 'N/A' }}
                            </div>
                            <div class="col-md-3 mb-3">
                                <strong>Account or Username:</strong><br>
                                {{ $record->payment_account_or_username ?? 'N/A' }}
                            </div>
                            <div class="col-md-3 mb-3">
                                <strong>Password:</strong><br>
                                {{ $record->payment_password ?? 'N/A' }}
                            </div>
                        </div>



                    @else
                        <p class="text-muted">Mode of delivery not specified</p>
                    @endif
                </div>
            </div>

            {{-- OTC Charges & Bill --}}
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="bi bi-currency-rupee me-2"></i>OTC Charges & Bill</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>OTC Extra Charges:</strong><br>
                            ₹{{ number_format($record->otc_extra_charges ?? 0, 2) }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>OTC Bill:</strong><br>
                            @if($record->otc_bill_file)
                                <a href="{{ asset($record->otc_bill_file) }}" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="bi bi-download"></i> View OTC Bill
                                </a>
                            @else
                                <span class="text-muted">No file uploaded</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex gap-2 justify-content-end">
                <!-- <a href="{{ route('operations.deliverables.edit', $record->id) }}" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Edit Deliverable
                </a> -->
                <a href="{{ route('operations.deliverables.open') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection