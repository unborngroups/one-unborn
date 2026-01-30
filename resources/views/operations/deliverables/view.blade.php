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
                        <div class="col-md-3"><strong>PO Date:</strong><br>{{ $record->po_date ? \Carbon\Carbon::parse($record->po_date)->format('Y-m-d') : 'N/A' }}</div>
                    </div>
                </div>
            </div>

            {{-- ================= Asset Details ================= --}}
            <div class="card mb-4 border-warning">
                <div class="card-header bg-warning">
                    <h6 class="mb-0">Asset Details</h6>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        
                        <div class="col-md-3">
                            <strong>Asset ID</strong><br>
                            <span>{{ $record->asset_id }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Serial No</strong><br>
                            <span>{{ $record->asset_serial_no }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Mac No</strong><br>
                            <span>{{ $record->asset_mac_no ?? '-' }}</span>
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
                    @php
                        $isSelfVendorType = in_array($record->feasibility->vendor_type ?? '', ['UBN', 'UBS', 'UBL', 'INF']);
                    @endphp
                    @foreach($record->deliverablePlans as $plan)
                    <div class="row border rounded mb-3 p-2">
                        <div class="col-12 mb-2">
                            <strong class="text-primary">Plan Information for Link {{ $plan->link_number }}</strong>
                        </div>
                        @if(!$isSelfVendorType && ($plan->vendor_name || $plan->vendor_email || $plan->vendor_contact))
                            <div class="col-md-3"><strong>Vendor Name</strong><br>{{ $plan->vendor_name ?? '-' }}</div>
                            <div class="col-md-3"><strong>Vendor Email</strong><br>{{ $plan->vendor_email ?? '-' }}</div>
                            <div class="col-md-3"><strong>Vendor Contact</strong><br>{{ $plan->vendor_contact ?? '-' }}</div>
                        @endif
                        <div class="col-md-3"><strong>Circuit ID</strong><br>{{ $plan->circuit_id ?? '-' }}</div>
                        <div class="col-md-3"><strong>Plan Name</strong><br>{{ $plan->plans_name ?? '-' }}</div>
                        <div class="col-md-3"><strong>Speed (Plan)</strong><br>{{ $plan->speed_in_mbps_plan ?? '-' }}</div>
                        <div class="col-md-3"><strong>Renewal Months</strong><br>{{ $plan->no_of_months_renewal ?? '-' }}</div>
                        <div class="col-md-3"><strong>Date of Activation</strong><br>{{ $plan->date_of_activation ? \Carbon\Carbon::parse($plan->date_of_activation)->format('Y-m-d') : '-' }}</div>
                        <div class="col-md-3"><strong>Date of Expiry</strong><br>{{ $plan->date_of_expiry ? \Carbon\Carbon::parse($plan->date_of_expiry)->format('Y-m-d') : '-' }}</div>
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
                        <!-- PPPoE Configuration -->
                        @if($plan->mode_of_delivery === 'PPPoE')
                        <div class="col-12 mt-2 text-danger"><strong>PPPoE Configuration</strong></div>
                        <div class="col-md-4"><strong>PPPoE Username:</strong><br>{{ $plan->pppoe_username ?? 'N/A' }}</div>
                        <div class="col-md-4"><strong>PPPoE Password:</strong><br>{{ $plan->pppoe_password ?? 'N/A' }}</div>
                        <div class="col-md-4"><strong>PPPoE VLAN:</strong><br>{{ $plan->pppoe_vlan ?? 'N/A' }}</div>
                        @endif
                        <!-- DHCP Configuration -->
                        @if($plan->mode_of_delivery === 'DHCP')
                        <div class="col-12 mt-2 text-danger"><strong>DHCP Configuration</strong></div>
                        <div class="col-md-6 mb-3"><strong>DHCP IP Address:</strong><br>{{ $plan->dhcp_ip_address ?? 'N/A' }}</div>
                        <div class="col-md-6 mb-3"><strong>DHCP VLAN:</strong><br>{{ $plan->dhcp_vlan ?? 'N/A' }}</div>
                        @endif
                        <!-- Static IP Configuration -->
                        @if(($record->feasibility->static_ip ?? 'No') === 'Yes' && (
                            $plan->static_ip_address ||
                            $plan->static_subnet_mask ||
                            $plan->static_vlan ||
                            $plan->network_ip ||
                            $plan->static_gateway ||
                            $plan->usable_ips
                        ))
                        <div class="col-12 mt-2 text-danger"><strong>Static IP Configuration</strong></div>
                        <div class="col-md-3 mb-3"><strong>Static IP Address:</strong><br>{{ $plan->static_ip_address ?? 'N/A' }}</div>
                        <div class="col-md-3 mb-3"><strong>Subnet:</strong><br>{{ $plan->static_subnet_mask ?? 'N/A' }}</div>
                        <div class="col-md-3 mb-3"><strong>VLAN Tag:</strong><br>{{ $plan->static_vlan ?? 'N/A' }}</div>
                        <div class="col-md-3 mb-3"><strong>Network IP:</strong><br>{{ $plan->network_ip ?? 'N/A' }}</div>
                        <div class="col-md-3 mb-3"><strong>Gateway:</strong><br>{{ $plan->static_gateway ?? 'N/A' }}</div>
                        <div class="col-md-3 mb-3"><strong>Usable IPs:</strong><br>{{ $plan->usable_ips ?? 'N/A' }}</div>
                        @endif
                        <!-- Payment Information -->
                        @if($plan->payment_login_url || $plan->payment_quick_url || $plan->payment_account_or_username || $plan->payment_password)
                        <div class="col-12 mt-2 text-danger"><strong>Payment Information</strong></div>
                        <div class="col-md-3 mb-2"><strong>Login URL:</strong><br>{{ $plan->payment_login_url ?? 'N/A' }}</div>
                        <div class="col-md-3 mb-2"><strong>Quick URL:</strong><br>{{ $plan->payment_quick_url ?? 'N/A' }}</div>
                        <div class="col-md-3 mb-2"><strong>Account Number:</strong><br>{{ $plan->payment_account ?? 'N/A' }}</div>
                        <div class="col-md-3 mb-2"><strong>Account Username:</strong><br>{{ $plan->payment_username ?? 'N/A' }}</div>
                        <div class="col-md-3 mb-2"><strong>Password:</strong><br>{{ $plan->payment_password ?? 'N/A' }}</div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>


            {{-- Mode of Delivery Details --}}

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
                        <div class="col-md-3">
                            <strong>OTC Bill</strong><br>
                            @if($record->otc_bill_file)
                                @php
                                    $otcPath = $record->otc_bill_file;
                                @endphp
                                @if($otcPath && file_exists(public_path($otcPath)))
                                    <a href="{{ asset($otcPath) }}" target="_blank">View File</a>
                                @else
                                    <span class="text-danger">File not found</span>
                                @endif
                            @else
                                -
                            @endif
                        </div>

                        <div class="col-md-3">
                            <strong>Export File</strong><br>
                            @if($record->export_file)
                                @php
                                    $exportPath = $record->export_file;
                                @endphp
                                @if($exportPath && file_exists(public_path($exportPath)))
                                    <a href="{{ asset($exportPath) }}" target="_blank">View File</a>
                                @else
                                    <span class="text-danger">File not found</span>
                                @endif
                            @else
                                -
                            @endif
                        </div>

                        <div class="col-md-3">
                            <strong>Speed Test</strong><br>
                            @if($record->speed_test_file)
                                @php
                                    $speedTestPath = $record->speed_test_file;
                                @endphp
                                @if($speedTestPath && file_exists(public_path($speedTestPath)))
                                    <a href="{{ asset($speedTestPath) }}" target="_blank">View File</a>
                                @else
                                    <span class="text-danger">File not found</span>
                                @endif
                            @else
                                -
                            @endif
                        </div>

                        <div class="col-md-3">
                            <strong>Ping Report (DNS)</strong><br>
                            @if($record->ping_report_dns_file)
                                @php
                                    $pingDnsPath = $record->ping_report_dns_file;
                                @endphp
                                @if($pingDnsPath && file_exists(public_path($pingDnsPath)))
                                    <a href="{{ asset($pingDnsPath) }}" target="_blank">View File</a>
                                @else
                                    <span class="text-danger">File not found</span>
                                @endif
                            @else
                                -
                            @endif
                        </div>

                        <div class="col-md-3">
                            <strong>Ping Report (GateWay)</strong><br>
                            @if($record->ping_report_gateway_file)
                                @php
                                    $pingGatewayPath = $record->ping_report_gateway_file;
                                @endphp
                                @if($pingGatewayPath && file_exists(public_path($pingGatewayPath)))
                                    <a href="{{ asset($pingGatewayPath) }}" target="_blank">View File</a>
                                @else
                                    <span class="text-danger">File not found</span>
                                @endif
                            @else
                                -
                            @endif
                        </div>

                        <div class="col-md-3">
                            <strong>ONU / ONT Device </strong><br>
                            @if($record->onu_ont_device_file)
                                @php
                                    $onuOntPath = $record->onu_ont_device_file;
                                @endphp
                                @if($onuOntPath && file_exists(public_path($onuOntPath)))
                                    <a href="{{ asset($onuOntPath) }}" target="_blank">View File</a>
                                @else
                                    <span class="text-danger">File not found</span>
                                @endif
                            @else
                                -
                            @endif
                        </div>

                        <div class="col-md-3">
                            <strong>Static IP</strong><br>
                            @if($record->static_ip_file)
                                @php
                                    $staticIpPath = $record->static_ip_file;
                                @endphp
                                @if($staticIpPath && file_exists(public_path($staticIpPath)))
                                    <a href="{{ asset($staticIpPath) }}" target="_blank">View File</a>
                                @else
                                    <span class="text-danger">File not found</span>
                                @endif
                            @else
                                -
                            @endif
                        </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= Back Button ================= --}}
            @php
                // Default back route based on status
                $backRoute = 'operations.deliverables.open';
                if ($record->status === 'InProgress') {
                    $backRoute = 'operations.deliverables.inprogress';
                } elseif ($record->status === 'Delivery') {
                    $backRoute = 'operations.deliverables.delivery';
                }

                // If this is an ILL deliverable, treat it as Accepted
                if (optional($record->feasibility)->type_of_service === 'ILL') {
                    $backRoute = 'operations.deliverables.acceptance';
                }
            @endphp

            <div class="text-end">
                <a href="{{ route($backRoute) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>

        </div>
    </div>
</div>
@endsection