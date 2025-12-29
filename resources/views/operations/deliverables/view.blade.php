@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow border-0">

        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="bi bi-eye me-2"></i> View Deliverable
            </h5>
        </div>

        <div class="card-body">

            {{-- ================= Feasibility Details ================= --}}
            <div class="card mb-4 border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">Feasibility Closed Details</h6>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3"><strong>Feasibility ID</strong><br>{{ $record->feasibility->feasibility_request_id ?? '-' }}</div>
                        <div class="col-md-3"><strong>Company</strong><br>{{ $record->feasibility->company->company_name ?? '-' }}</div>
                        <div class="col-md-3"><strong>Client</strong><br>{{ $record->feasibility->client->client_name ?? '-' }}</div>
                        <div class="col-md-3"><strong>Service Type</strong><br>{{ $record->feasibility->type_of_service ?? '-' }}</div>

                        <div class="col-md-3"><strong>State</strong><br>{{ $record->feasibility->state ?? '-' }}</div>
                        <div class="col-md-3"><strong>District</strong><br>{{ $record->feasibility->district ?? '-' }}</div>
                        <div class="col-md-3"><strong>Area</strong><br>{{ $record->feasibility->area ?? '-' }}</div>
                        <div class="col-md-3"><strong>Address</strong><br>{{ $record->feasibility->address ?? '-' }}</div>
                        <div class="col-md-3"><strong>Pincode</strong><br>{{ $record->feasibility->pincode ?? '-' }}</div>

                        <div class="col-md-3"><strong>SPOC Name</strong><br>{{ $record->feasibility->spoc_name ?? '-' }}</div>
                        <div class="col-md-3"><strong>SPOC Contact 1</strong><br>{{ $record->feasibility->spoc_contact1 ?? '-' }}</div>
                        <div class="col-md-3"><strong>No of Links</strong><br>{{ $record->feasibility->no_of_links ?? '-' }}</div>
                        <div class="col-md-3"><strong>Speed</strong><br>{{ $record->feasibility->speed ?? '-' }}</div>
                        <div class="col-md-3"><strong>Vendor Type</strong><br>{{ $record->feasibility->vendor_type ?? '-' }}</div>
                        <div class="col-md-3"><strong>Static IP</strong><br>{{ $record->feasibility->static_ip ?? '-' }}</div>
                        <div class="col-md-3"><strong>static IP Subnet</strong><br>{{ $record->feasibility->static_ip_subnet ?? '-' }}</div>
                        <div class="col-md-3"><strong>Expected Delivery:</strong><br>{{ $record->feasibility->expected_delivery ?? 'N/A' }}</div>
                        <div class="col-md-3"><strong>Expected Activation:</strong><br>{{ $record->feasibility->expected_activation ?? 'N/A' }}</div>
                        <div class="col-md-3"><strong>Hardware Required:</strong><br>{{ $record->feasibility->hardware_required ?? 'N/A' }}</div>
                        <div class="col-md-3"><strong>Hardware Model Name:</strong><br>{{ $record->feasibility->hardware_model_name ?? 'N/A' }}</div>
                        <div class="col-md-3"><strong>PO Number:</strong><br><span class="badge bg-primary">{{ $record->po_number ?? 'N/A' }}</span></div>
                        <div class="col-md-3"><strong>PO Date:</strong><br>{{ $record->po_date ? \Carbon\Carbon::parse($record->po_date)->format('d-m-Y') : 'N/A' }}</div>
                    </div>
                </div>
            </div>

            {{-- ================= PO Details ================= --}}
            <div class="card mb-4 border-warning">
                <div class="card-header bg-warning">
                    <h6 class="mb-0">Purchase Order Details</h6>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <strong>PO Number</strong><br>
                            <span class="badge bg-dark">{{ $record->po_number }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>PO Date</strong><br>
                            {{ $record->po_date ? \Carbon\Carbon::parse($record->po_date)->format('d-m-Y') : '-' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Status</strong><br>
                            <span class="badge bg-success">{{ $record->status }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Circuit ID</strong><br>
                            {{ $record->circuit_id ?? 'Auto Generated' }}
                        </div>
                    </div>
                </div>
            </div>
            <!-- {{--Asset--}}
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6>Asset Details</h6>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3"><strong>Asset Name</strong><br>{{ $record->asset->asset_name ?? '-' }}</div>
                        <div class="col-md-3"><strong>Asset Serial No</strong><br>{{ $record->asset_serial_no ?? '-' }}</div>
                        
                    </div>
                </div>
            </div> -->

            {{-- ================= Delivery / Plan Details ================= --}}
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">Plan & Delivery Details</h6>
                </div>

                <div class="card-body">
                    @foreach($record->deliverablePlans as $plan)
                    <div class="row border rounded mb-3 p-2">
                        <div class="col-12 mb-2">
                            <strong>Plan Information for Link {{ $plan->link_number }}</strong>
                        </div>
                        <div class="col-md-3"><strong>Circuit ID</strong><br>{{ $plan->circuit_id ?? '-' }}</div>

                        <div class="col-md-3"><strong>Plan Name</strong><br>{{ $plan->plans_name ?? '-' }}</div>
                        <div class="col-md-3"><strong>Speed (Plan)</strong><br>{{ $plan->speed_in_mbps_plan ?? '-' }}</div>
                        <div class="col-md-3"><strong>Renewal Months</strong><br>{{ $plan->no_of_months_renewal ?? '-' }}</div>
                        <div class="col-md-3"><strong>Date of Activation</strong><br>{{ $plan->date_of_activation ? \Carbon\Carbon::parse($plan->date_of_activation)->format('d-m-Y') : '-' }}</div>
                        <div class="col-md-3"><strong>Date of Expiry</strong><br>{{ $plan->date_of_expiry ? \Carbon\Carbon::parse($plan->date_of_expiry)->format('d-m-Y') : '-' }}</div>
                        <div class="col-md-3"><strong>SLA</strong><br>{{ $plan->sla ?? '-' }}</div>
                        <div class="col-md-3"><strong>Status of Link</strong><br>{{ $plan->status_of_link ?? '-' }}</div>
                        <div class="col-md-3"><strong>Mode of Delivery</strong><br>{{ $plan->mode_of_delivery ?? '-' }}</div>
                        <div class="col-md-3"><strong>MTU</strong><br>{{ $plan->mtu ?? '-' }}</div>
                        <div class="col-md-3"><strong>Wifi Username</strong><br>{{ $plan->wifi_username ?? '-' }}</div>
                        <div class="col-md-3"><strong>Wifi Password</strong><br>{{ $plan->wifi_password ?? '-' }}</div>
                        <div class="col-md-3"><strong>Router Username</strong><br>{{ $plan->router_username ?? '-' }}</div>
                        <div class="col-md-3"><strong>Router Password</strong><br>{{ $plan->router_password ?? '-' }}</div>
                        <div class="col-md-3"><strong>Client Circuit ID</strong><br>{{ $plan->client_circuit_id ?? '-' }}</div>
                        <div class="col-md-3"><strong>Client Feasibility</strong><br>{{ $plan->client_feasibility ?? '-' }}</div>
                        <div class="col-md-3"><strong>Vendor Code</strong><br>{{ $plan->vendor_code ?? '-' }}</div>
                        <!-- Add more per-link fields as needed -->
                    </div>
                    @endforeach
                </div>
            </div>


            {{-- Mode of Delivery Details --}}
            @php $linkCount = $record->feasibility->no_of_links ?? 1; @endphp
            @if($record->mode_of_delivery === 'PPPoE')
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">PPPoE Configuration</h6>
                </div>
                <div class="card-body">
                    @for($i = 1; $i <= $linkCount; $i++)
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>PPPoE Username {{ $i }}:</strong><br>{{ $plan->{'pppoe_username_'.$i} ?? 'N/A' }}</div>
                            <div class="col-md-4"><strong>PPPoE Password {{ $i }}:</strong><br>{{ $plan->{'pppoe_password_'.$i} ?? 'N/A' }}</div>
                            <div class="col-md-4"><strong>PPPoE VLAN {{ $i }}:</strong><br>{{ $plan->{'pppoe_vlan_'.$i} ?? 'N/A' }}</div>
                        </div>
                    @endfor
                </div>
            </div>
            @elseif($record->mode_of_delivery === 'DHCP')
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">DHCP Configuration</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3"><strong>DHCP IP Address:</strong><br>{{ $record->dhcp_ip_address ?? 'N/A' }}</div>
                        <div class="col-md-6 mb-3"><strong>DHCP VLAN:</strong><br>{{ $record->dhcp_vlan ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
            @elseif(in_array($record->mode_of_delivery, ['Static', 'Static IP']))
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">Static IP Configuration</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3"><strong>Static IP Address:</strong><br>{{ $record->static_ip_address ?? 'N/A' }}</div>
                        <div class="col-md-3 mb-3"><strong>Subnet:</strong><br>{{ $record->static_subnet_mask ?? 'N/A' }}</div>
                        <div class="col-md-3 mb-3"><strong>VLAN Tag:</strong><br>{{ $record->static_vlan_tag ?? 'N/A' }}</div>
                        <div class="col-md-3 mb-3"><strong>Network IP:</strong><br>{{ $record->network_ip ?? 'N/A' }}</div>
                        <div class="col-md-3 mb-3"><strong>Gateway:</strong><br>{{ $record->gateway ?? 'N/A' }}</div>
                        <div class="col-md-3 mb-3"><strong>IP Address:</strong><br>{{ $record->static_ip_address ?? 'N/A' }}</div>
                        <div class="col-md-3 mb-3"><strong>Subnet Mask:</strong><br>{{ $record->subnet_mask ?? 'N/A' }}</div>
                        <div class="col-md-3 mb-3"><strong>Usable IPs:</strong><br>{{ $record->usable_ips ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
            @elseif($record->mode_of_delivery === 'PAYMENTS')
            <div class="card mb-4">
                <div class="card-header bg-primary text-dark">
                    <h6 class="mb-0">Payment Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2"><strong>Login URL:</strong><br>{{ $record->payment_login_url ?? 'N/A' }}</div>
                        <div class="col-md-3 mb-2"><strong>Quick URL:</strong><br>{{ $record->payment_quick_url ?? 'N/A' }}</div>
                        <div class="col-md-3 mb-2"><strong>Account Number / Username:</strong><br>{{ $record->payment_account_or_username ?? 'N/A' }}</div>
                        <div class="col-md-3 mb-2"><strong>Password:</strong><br>{{ $record->payment_password ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
            @endif

            {{-- ================= Network Details ================= --}}
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">Network Configuration</h6>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3"><strong>LAN IP 1</strong><br>{{ $record->lan_ip_1 ?? '-' }}</div>
                        <div class="col-md-3"><strong>LAN IP 2</strong><br>{{ $record->lan_ip_2 ?? '-' }}</div>
                        <div class="col-md-3"><strong>LAN IP 3</strong><br>{{ $record->lan_ip_3 ?? '-' }}</div>
                        <div class="col-md-3"><strong>LAN IP 4</strong><br>{{ $record->lan_ip_4 ?? '-' }}</div>
                        <div class="col-md-3"><strong>IPSEC</strong><br>{{ $record->ipsec ?? '-' }}</div>
                        <div class="col-md-3"><strong>Phase 1</strong><br>{{ $record->phase_1 ?? '-' }}</div>
                        <div class="col-md-3"><strong>Phase 2</strong><br>{{ $record->phase_2 ?? '-' }}</div>
                        <div class="col-md-3"><strong>IPSEC Interface</strong><br>{{ $record->ipsec === 'Yes' ? ($record->ipsec_interface ?? '-') : '-' }}</div>
                         
                    </div>
                </div>
            </div>
           
            {{-- ================= Files ================= --}}
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0">Uploaded Files</h6>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <strong>OTC Bill</strong><br>
                            @if($record->otc_bill_file)
                                <a href="{{ asset($record->otc_bill_file) }}" target="_blank">View File</a>
                            @else
                                -
                            @endif
                        </div>

                        <div class="col-md-6">
                            <strong>Export File</strong><br>
                            @if($record->export_file)
                                <a href="{{ asset($record->export_file) }}" target="_blank">View File</a>
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= Back Button ================= --}}
            <div class="text-end">
                <a href="{{ route('operations.deliverables.open') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>

        </div>
    </div>
</div>
@endsection