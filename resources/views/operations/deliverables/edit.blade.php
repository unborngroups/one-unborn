@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Deliverable</h5>
        </div>

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


        <div class="card-body">
            {{-- Feasibility Closed Details Card (Read-only) --}}
            <div class="card mb-4 border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Feasibility Closed Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Feasibility ID:</strong><br>
                            {{ $record->feasibility->feasibility_request_id ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Type of Service:</strong><br>
                            {{ $record->feasibility->type_of_service ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Company Name:</strong><br>
                            {{ $record->feasibility->company->company_name ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Client Name:</strong><br>
                            {{ $record->feasibility->client->client_name ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Pincode:</strong><br>
                            {{ $record->feasibility->pincode ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>State:</strong><br>
                            {{ $record->feasibility->state ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>District:</strong><br>
                            {{ $record->feasibility->district ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Area:</strong><br>
                            {{ $record->feasibility->area ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Address:</strong><br>
                            {{ $record->feasibility->address ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Speed:</strong><br>
                            {{ $record->feasibility->speed ?? 'N/A' }} 
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <strong>PO Number:</strong><br>
                            <span class="badge bg-primary">{{ $record->po_number ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>PO Date:</strong><br>
                            {{ $record->po_date ? \Carbon\Carbon::parse($record->po_date)->format('d-m-Y') : 'N/A' }}
                        </div>

                    </div>
                    
                </div>
            </div>

            @php
                $selectedAssetId = old('asset_id', $record->asset_id ?? '');
                $selectedAssetSerial = old('asset_serial_no', $record->asset_serial_no ?? '');
            @endphp

            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">Hardware & Asset Details</h6>
                </div>
                <div class="card-body">
                    <div class="row gy-2">
                        @if(!empty($hardwareDetails))
                            @foreach($hardwareDetails as $index => $detail)
                                <div class="col-md-6">
                                    <div class="border rounded p-2">
                                        <small class="text-muted">Hardware {{ $index + 1 }}</small>
                                        <p class="mb-1"><strong>Make:</strong> {{ $detail['make'] ?? '-' }}</p>
                                        <p class="mb-0"><strong>Model:</strong> {{ $detail['model'] ?? '-' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12">
                                <p class="mb-0 text-muted">No hardware information was carried over from the feasibility request.</p>
                            </div>
                        @endif
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Asset ID</label>
                            <select name="asset_id" id="asset_selector" class="form-select">
                                <option value="">-- Select Asset --</option>
                                @foreach($assetOptions as $asset)
                                    <option value="{{ $asset->asset_id }}"
                                            data-serial="{{ $asset->serial_no }}"
                                            {{ $selectedAssetId && $selectedAssetId === $asset->asset_id ? 'selected' : '' }}>
                                        {{ $asset->asset_id }}@if(!empty($asset->serial_no)) - {{ $asset->serial_no }}@endif ({{ $asset->vendor_name }})
                                    </option>
                                @endforeach
                                @if($selectedAssetId && !$assetOptions->contains('asset_id', $selectedAssetId))
                                    <option value="{{ $selectedAssetId }}" data-serial="{{ $selectedAssetSerial }}" selected>
                                        {{ $selectedAssetId }}@if(!empty($selectedAssetSerial)) - {{ $selectedAssetSerial }}@endif (assigned)
                                    </option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Serial Number</label>
                            <input type="text" id="asset_serial_no" name="asset_serial_no" class="form-control" readonly value="{{ $selectedAssetSerial }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Editable Form --}}
            <form action="{{ route('operations.deliverables.save', $record->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                {{-- Plan Information --}}
            @php
                $linkCount = $record->feasibility->no_of_links ?? 1;
                $plans = $record->deliverablePlans->keyBy('link_number');
            @endphp
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        Plan Information
                    </div>
                    <div class="card-body">
                        @for($i = 1; $i <= $linkCount; $i++)
                            @php
                                $plan = $plans->get($i);
                                $defaultPlanName = isset($plan) ? $plan->plans_name : ($i == 1 ? '' : null);
                                $defaultSpeed = isset($plan) ? $plan->speed_in_mbps_plan : ($i == 1 ? '' : null);
                                $defaultRenewal = isset($plan) ? $plan->no_of_months_renewal : ($i == 1 ? '' : null);
                            @endphp
                            <div class="row border rounded mb-3 p-2">
                                <div class="col-12 mb-2">
                                    <strong>Plan Information for Link {{ $i }}</strong>
                                </div>
                                
                                 <div class="col-md-3 mb-3">
                                <label class="form-label">Circuit ID</label>
                                <input type="text" class="form-control" value="Auto Generated" readonly>
                               </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Plans Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="plans_name_{{ $i }}"
                                           value="{{ old('plans_name_'.$i, $defaultPlanName ?? '' ) }}" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Speed in Mbps (Plan) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="speed_in_mbps_plan_{{ $i }}"
                                           value="{{ old('speed_in_mbps_plan_'.$i, $defaultSpeed ?? '' ) }}" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">No of Months Renewal <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="no_of_months_renewal_{{ $i }}"
                                           value="{{ old('no_of_months_renewal_'.$i, $defaultRenewal ?? ''  ) }}" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                <label class="form-label">Date of Activation <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="date_of_activation_{{ $i }}"
                                       value="{{ old('date_of_activation_'.$i, isset($plan->date_of_activation) ? $plan->date_of_activation->format('Y-m-d') : ($i == 1 && isset($record->date_of_activation) ? \Carbon\Carbon::parse($record->date_of_activation)->format('Y-m-d') : '')) }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Date of Expiry  <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="date_of_expiry_{{ $i }}"
                                       value="{{ old('date_of_expiry_'.$i, isset($plan->date_of_expiry) ? $plan->date_of_expiry->format('Y-m-d') : ($i == 1 && isset($record->date_of_expiry) ? \Carbon\Carbon::parse($record->date_of_expiry)->format('Y-m-d') : '')) }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">SLA <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="sla_{{ $i }}"
                                       value="{{ old('sla_'.$i, $plan->sla ?? ($i == 1 ? $record->sla : '')) }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Status of Link <span class="text-danger">*</span></label>
                                <select class="form-select" name="status_of_link_{{ $i }}" required>
                                    <option value="">Select Status</option>
                                    <option value="Delivered and Activated" {{ old('status_of_link_'.$i, $plan->status_of_link ?? '') == 'Delivered and Activated' ? 'selected' : '' }}>Delivered and Activated</option>
                                    <option value="Delivered" {{ old('status_of_link_'.$i, $plan->status_of_link ?? '') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="Inprogress" {{ old('status_of_link_'.$i, $plan->status_of_link ?? '') == 'Inprogress' ? 'selected' : '' }}>Inprogress</option>
                                </select>
                            </div>

                            <!-- mode of Delivery -->
                             <div class="col-md-3 mb-3">
                                @php
                                    $selectedMode = old("mode_of_delivery_$i", $plan->mode_of_delivery ?? '');
                                @endphp
                                <label class="form-label">Mode of Delivery <span class="text-danger">*</span></label>
                                <select class="form-select mode_of_delivery" name="mode_of_delivery_{{ $i }}" data-link="{{ $i }}" onchange="toggleSectionsByLink({{ $i }}, this.value)" required>
                                    <option value="">Select Mode</option>
                                    <option value="PPPoE" {{ $selectedMode === 'PPPoE' ? 'selected' : '' }}>PPPoE</option>
                                    <option value="DHCP" {{ $selectedMode === 'DHCP' ? 'selected' : '' }}>DHCP</option>
                                    <option value="Static IP" {{ in_array($selectedMode, ['Static IP', 'Static']) ? 'selected' : '' }}>Static IP</option>
                                    <option value="PAYMENTS" {{ $selectedMode === 'PAYMENTS' ? 'selected' : '' }}>PAYMENTS</option>

                                </select>
                            </div>

                            <!-- Client Circuit ID -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Client Circuit ID</label>
                                <input type="text" class="form-control" name="client_circuit_id_{{ $i }}" value="{{ old('client_circuit_id_'.$i, $record->{'client_circuit_id_'.$i} ?? '') }}">
                            </div>

                            <!-- Client Feasibility -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Client Feasibility</label>
                                <input type="text" class="form-control" name="client_feasibility_{{ $i }}" value="{{ old('client_feasibility_'.$i, $record->{'client_feasibility_'.$i} ?? '') }}">
                            </div>

                            <!-- Vendor Code -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Vendor Code</label>
                                <input type="text" class="form-control" name="vendor_code_{{ $i }}" value="{{ old('vendor_code_'.$i, $record->{'vendor_code_'.$i} ?? '') }}">
                            </div>
                            <!-- MTU -->
                            <div class="col-md-3 mb-3">
                            <label class="form-label">MTU <span class="text-danger">*</span></label>
                            <input type="text" name="mtu_{{ $i }}" class="form-control" placeholder="Enter MTU" value="{{ old('mtu_'.$i, $record->{'mtu_'.$i} ?? '') }}" required>
                        </div>
                        <!-- Wifi Username -->
                         <div class="col-md-3 mb-3">
                            <label class="form-label">Wifi Username</label>
                            <input type="text" name="wifi_username_{{ $i }}" class="form-control" placeholder="Enter Wifi Username" value="{{ old('wifi_username_'.$i, $record->{'wifi_username_'.$i} ?? '') }}">
                        </div>
                        <!-- Wifi Password -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Wifi Password</label>
                            <input type="text" name="wifi_password_{{ $i }}" class="form-control" placeholder="Enter Wifi Password" value="{{ old('wifi_password_'.$i, $record->{'wifi_password_'.$i} ?? '') }}">
                        </div>
                        <!-- Router Username -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Router Username</label>
                            <input type="text" name="router_username_{{ $i }}" class="form-control" placeholder="Enter Router Username" value="{{ old('router_username_'.$i, $record->{'router_username_'.$i} ?? '') }}">
                        </div>
                        <!-- Router Password -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Router Password</label>
                            <input type="text" name="router_password_{{ $i }}" class="form-control" placeholder="Enter Router Password" value="{{ old('router_password_'.$i, $record->{'router_password_'.$i} ?? '') }}">
                        </div>

                        </div>
                    @endfor
                              
                            

                    </div>
                </div>
                @php
    $linkCount = $record->feasibility->no_of_links ?? 1;  // align fields with feasibility\'s link count
@endphp
<!--  -->
@for($i = 1; $i <= $linkCount; $i++)

                {{-- PPPoE Configuration --}}
                <div class="card mb-3" id="pppoe_section_{{ $i }}" style="display: none;">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">PPPoE Configuration</h6>
                    </div>
                    <div class="card-body">
                        
        <div class="row mb-2">
            <div class="col-md-4">
                <label>Username</label>
                <input type="text" name="pppoe_username_{{ $i }}" class="form-control"
                       value="{{ old('pppoe_username_'.$i, $record->{'pppoe_username_'.$i} ?? '') }}">
            </div>

            <div class="col-md-4">
                <label>Password</label>
                <input type="text" name="pppoe_password_{{ $i }}" class="form-control"
                       value="{{ old('pppoe_password_'.$i, $record->{'pppoe_password_'.$i} ?? '') }}">
            </div>

            <div class="col-md-4">
                <label>VLAN</label>
                <input type="text" name="pppoe_vlan_{{ $i }}" class="form-control"
                       value="{{ old('pppoe_vlan_'.$i, $record->{'pppoe_vlan_'.$i} ?? '') }}">
            </div>
        </div>
    
                    </div>
                </div>

                {{-- DHCP Configuration --}}
                <div class="card mb-3" id="dhcp_section_{{ $i }}" style="display: none;">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">DHCP Configuration</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">IP Address</label>
                                <input type="text" class="form-control" name="dhcp_ip_address_{{ $i }}" 
                                       value="{{ old('dhcp_ip_address_'.$i, $record->{'dhcp_ip_address_'.$i} ?? '') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">VLAN</label>
                                <input type="text" class="form-control" name="dhcp_vlan_{{ $i }}" 
                                       value="{{ old('dhcp_vlan_'.$i, $record->{'dhcp_vlan_'.$i} ?? '') }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Static IP Configuration --}}
                <div class="card mb-3" id="static_section_{{ $i }}" style="display: none;">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">Static IP Configuration</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">IP Address <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="static_ip_address_{{ $i }}" name="static_ip_address_{{ $i }}"
                                       value="{{ old('static_ip_address_'.$i, $record->{'static_ip_address_'.$i} ?? '') }}">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Subnet <span class="text-danger">*</span></label>
                                <select class="form-select" id="static_ip_subnet_{{ $i }}" name="static_subnet_mask_{{ $i }}">
                                    <option value="">Select Subnet</option>
                                    @foreach(['/32','/31','/30','/29','/28','/27','/26','/25','/24'] as $subnet)
                                        <option value="{{ $subnet }}" {{ old('static_subnet_mask_'.$i, $record->{'static_subnet_mask_'.$i} ?? '') == $subnet ? 'selected' : '' }}>{{ $subnet }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">VLAN</label>
                                <input type="text" class="form-control" name="static_vlan_tag_{{ $i }}"
                                       value="{{ old('static_vlan_tag_'.$i, $record->{'static_vlan_tag_'.$i} ?? '') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div id="static_info_message_{{ $i }}" class="alert alert-secondary small mb-3">
                                    Select static IP and subnet to preview network details (Network IP, Gateway, Subnet Mask, and Usable IP range).
                                </div>
                            </div>
                        </div>

                        <div class="row" id="static_info_pane_{{ $i }}">
    <div class="col-md-3 mb-3">
        <label class="form-label">Network IP</label>
        <input type="text" class="form-control" id="network_ip_{{ $i }}" name="network_ip_{{ $i }}" value="{{ old('network_ip_'.$i, $record->{'network_ip_'.$i} ?? '') }}" readonly>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Gateway</label>
        <input type="text" class="form-control" id="gateway_{{ $i }}" name="gateway_{{ $i }}" value="{{ old('gateway_'.$i, $record->{'gateway_'.$i} ?? '') }}" readonly>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Subnet Mask</label>
        <input type="text" class="form-control" id="subnet_mask_{{ $i }}" name="subnet_mask_{{ $i }}" value="{{ old('subnet_mask_'.$i, $record->{'subnet_mask_'.$i} ?? '') }}" readonly>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Usable IPs</label>
        <input type="text" class="form-control" id="usable_ips_{{ $i }}" name="usable_ips_{{ $i }}" value="{{ old('usable_ips_'.$i, $record->{'usable_ips_'.$i} ?? '') }}" readonly>
    </div>
                        </div>
                    </div>
                </div>

                <!--  -->
                <div class="card mb-3"  id="payments_section_{{ $i }}" style="display: none;">

                <div class="card-header bg-primary text-dark">
                <h6 class="mb-0">Payment Information</h6>
               </div>
<div class="card-body">
<div class="row">
    <div class="col-md-3 mb-2">
        <label>Login URL</label>
        <input type="text" name="payment_login_url_{{ $i }}" class="form-control"
               value="{{ $record->payment_login_url }}">
    </div>

    <div class="col-md-3 mb-2">
        <label>Quick URL</label>
        <input type="text" name="payment_quick_url_{{ $i }}" class="form-control"
               value="{{ $record->payment_quick_url }}">
    </div>

    <div class="col-md-3 mb-2">
        <label>Account Number / Username</label>
        <input type="text" name="payment_account_or_username_{{ $i }}" class="form-control"
               value="{{ $record->payment_account_or_username }}">
    </div>

    <div class="col-md-3 mb-2">
        <label>Password</label>
        <input type="text" name="payment_password_{{ $i }}" class="form-control" placeholder="Enter new password">
    </div>
</div>

</div>
                </div>
                @endfor
                <!-- {{-- LAN --}} -->
                 <div class="card mb-3 ">
                <div class="card-header bg-primary text-white">
                <h6 class="mb-0"> Configuration</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        
                        <div class="col-md-3 mb-3">
                            <label>LAN IP 1 <span style="color:red">*</span></label>
                            <input type="text" name="lan_ip_1" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>LAN IP 2</label>
                            <input type="text" name="lan_ip_2" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>LAN IP 3</label>
                            <input type="text" name="lan_ip_3" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>LAN IP 4</label>
                            <input type="text" name="lan_ip_4" class="form-control">
                        </div>
                    </div>
                   
                    
                        <div class="col-md-3 mb-3">
                            <label>IPSEC</label>
                            <select name="ipsec" id="ipsec" class="form-control">
                                <option value="">-- Select --</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="col-md-3 ipsec-fields d-none">
                            <label>Phase 1</label>
                            <input type="text" name="phase_1" class="form-control">
                        </div>
                        <div class="col-md-3 ipsec-fields d-none">
                            <label>Phase 2</label>
                            <input type="text" name="phase_2" class="form-control">
                        </div>
                        <div class="col-md-3 ipsec-fields d-none">
                            <label>IPSEC Interface</label>
                            <input type="text" name="ipsec_interface" class="form-control">
                        </div>
                </div>
                    
                    

                </div>
                 </div>



                {{-- OTC Information --}}
                <div class="card mb-1 mx-3">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">OTC Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">OTC (Extra if any)</label>
                                <input type="number" step="0.01" class="form-control" name="otc_extra_charges" 
                                       value="{{ old('otc_extra_charges', $record->otc_extra_charges) }}">
                            </div>

                               <!-- Upload OTC Bill -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Upload OTC Bill</label>
                                @if($record->otc_bill_file)
                                    <a href="{{ asset($record->otc_bill_file) }}" target="_blank">View OTC Bill</a>
                                @endif
                                <input type="file" class="form-control" name="otc_bill_file" accept=".pdf,.jpg,.jpeg,.png">
                                @if($record->otc_bill_file)
                                    <small class="text-muted">Current: {{ basename($record->otc_bill_file) }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!--  -->
                <div class="col-md-4 mb-3 mx-3">
    <label class="form-label">Upload Export File</label>
    @if($record->export_file)
        <a href="{{ asset($record->export_file) }}" target="_blank">View Export File</a>
    @endif
    <input type="file" name="export_file" class="form-control" accept=".pdf,.xlsx,.xls,.csv,.jpg,.jpeg,.png">
</div>


                {{-- Action Buttons --}}
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('operations.deliverables.open') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                    <!-- upload conf -->

                    <div>
                        @if($record->status == 'Open')
                            <button type="submit" name="action" value="save" class="btn btn-primary me-2">
                                <i class="bi bi-floppy"></i> Save (Move to In Progress)
                            </button>
                            
                            <button type="submit" name="action" value="submit" class="btn btn-success">
                                <i class="bi bi-check2-all"></i> Submit (Move to Delivery)
                            </button>
                        @elseif($record->status == 'InProgress')
                            <button type="submit" name="action" value="save" class="btn btn-primary me-2">
                                <i class="bi bi-floppy"></i> Save
                            </button>
                            
                            <button type="submit" name="action" value="submit" class="btn btn-success">
                                <i class="bi bi-check2-all"></i> Submit (Move to Delivery)
                            </button>
                        @else
                            <button type="submit" name="action" value="save" class="btn btn-primary">
                                <i class="bi bi-floppy"></i> Save Changes
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .row > [class^='col-'],
    .row > [class*=' col-'] {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
    }
    .form-control, .form-select {
        min-width: 0;
        width: 100%;
        box-sizing: border-box;
    }
    .row {
        row-gap: 0.5rem;
    }
    @media (max-width: 991px) {
        .row > [class^='col-'],
        .row > [class*=' col-'] {
            flex: 1 1 100%;
        }
    }
</style>

{{-- JavaScript for Dynamic Form Sections --}}
<script>
// Function to get selected mode of delivery
// document.querySelectorAll('.mode_of_delivery').forEach(select => {
//     select.addEventListener('change', function () {
//         const linkNo = this.dataset.link;
//         const planFields = document.getElementById(`plan_info_fields_${linkNo}`);
//         if (planFields) {
//             planFields.style.display = this.value ? 'block' : 'none';
//         }
//         toggleSectionsByLink(linkNo, this.value);
//     });

//     // initial load
//     const linkNo = select.dataset.link;
//     const planFields = document.getElementById(`plan_info_fields_${linkNo}`);
//     if (planFields) {
//         planFields.style.display = select.value ? 'block' : 'none';
//     }
//     if (select.value) {
//         toggleSectionsByLink(linkNo, select.value);
//     }
// });

// --- Per-link Static IP/Subnet JS ---
@php $linkCount = $record->feasibility->no_of_links ?? 1;
@endphp
document.addEventListener('DOMContentLoaded', function () {
    @for($i = 1; $i <= $linkCount; $i++)
    (function(linkNo) {
        const ipInput = document.getElementById('static_ip_address_' + linkNo);
        const subnetSelect = document.getElementById('static_ip_subnet_' + linkNo);
        const infoPanel = document.getElementById('static_info_pane_' + linkNo);
        const infoMsg = document.getElementById('static_info_message_' + linkNo);
        const networkInput = document.getElementById('network_ip_' + linkNo);
        const gatewayInput = document.getElementById('gateway_' + linkNo);
        const subnetMaskInput = document.getElementById('subnet_mask_' + linkNo);
        const usableIpsInput = document.getElementById('usable_ips_' + linkNo);
        const infoIpAddressInput = document.getElementById('info_ip_address_' + linkNo);

        function hideStaticInfo() {
            if (infoPanel) infoPanel.style.display = 'none';
            if (infoMsg) infoMsg.style.display = 'block';
            if (networkInput) networkInput.value = '';
            if (gatewayInput) gatewayInput.value = '';
            if (subnetMaskInput) subnetMaskInput.value = '';
            if (usableIpsInput) usableIpsInput.value = '';
            if (infoIpAddressInput && ipInput) infoIpAddressInput.value = ipInput.value.trim();
        }

        function showStaticInfo() {
            if (infoPanel) infoPanel.style.display = 'block';
            if (infoMsg) infoMsg.style.display = 'none';
        }

        async function fetchSubnetDetails() {
            if (!ipInput || !subnetSelect) {
                hideStaticInfo();
                return;
            }
            const ip = ipInput.value.trim();
            const subnet = subnetSelect.value;
            if (!ip || !subnet) {
                hideStaticInfo();
                return;
            }
            try {
                const response = await fetch(`{{ route('calculate.subnet') }}?ip=${encodeURIComponent(ip)}&subnet=${encodeURIComponent(subnet)}`);
                if (!response.ok) {
                    hideStaticInfo();
                    return;
                }
                const data = await response.json();
                if (!data.network_ip) {
                    hideStaticInfo();
                    return;
                }
                if (networkInput) networkInput.value = data.network_ip || '';
                if (gatewayInput) gatewayInput.value = data.gateway || '';
                if (subnetMaskInput) subnetMaskInput.value = data.subnet_mask || '';
                if (usableIpsInput) usableIpsInput.value = data.usable_ips || '';
                if (infoIpAddressInput) infoIpAddressInput.value = ip;
                showStaticInfo();
            } catch (error) {
                hideStaticInfo();
            }
        }

        if (ipInput) ipInput.addEventListener('input', fetchSubnetDetails);
        if (subnetSelect) subnetSelect.addEventListener('change', fetchSubnetDetails);
        // Initial load
        fetchSubnetDetails();
    })({{ $i }});
    @endfor
});

 // For multiple links
    function toggleSectionsByLink(linkNo, selectedMode) {
    const pppoe = document.getElementById(`pppoe_section_${linkNo}`);
    const dhcp = document.getElementById(`dhcp_section_${linkNo}`);
    const stat = document.getElementById(`static_section_${linkNo}`);
    const pay  = document.getElementById(`payments_section_${linkNo}`);

    [pppoe, dhcp, stat, pay].forEach(sec => sec && (sec.style.display = 'none')) ;

    if (selectedMode === 'PPPoE') pppoe.style.display = 'block';
    if (selectedMode === 'DHCP') dhcp.style.display = 'block';
    if (selectedMode === 'Static IP' || selectedMode === 'Static') stat.style.display = 'block';
    if (selectedMode === 'PAYMENTS') pay.style.display = 'block';
}

function toggleIpsecFields() {
    const ipsecFields = document.querySelectorAll('.ipsec-fields');
    const shouldShow = document.getElementById('ipsec')?.value === 'Yes';
    ipsecFields.forEach((el) => {
        el.classList.toggle('d-none', !shouldShow);
    });

    if (!shouldShow) {
        document.querySelectorAll('input[name="phase_1"], input[name="phase_2"], input[name="ipsec_interface"]').forEach((input) => {
            input.value = '';
        });
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const ipsecSelect = document.getElementById('ipsec');
    if (ipsecSelect) {
        ipsecSelect.addEventListener('change', toggleIpsecFields);
        toggleIpsecFields();
    }
});

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.mode_of_delivery').forEach(select => {
        if (select.value) {
            toggleSectionsByLink(select.dataset.link, select.value);
        }
    });
});


</script>
@endsection