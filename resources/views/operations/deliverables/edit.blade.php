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
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">Plan Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Plans Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="plans_name" 
                                       value="{{ old('plans_name', $record->plans_name) }}" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Speed in Mbps (Plan) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="speed_in_mbps_plan" 
                                       value="{{ old('speed_in_mbps_plan', $record->speed_in_mbps_plan) }}" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">No of Months Renewal <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="no_of_months_renewal" 
                                       value="{{ old('no_of_months_renewal', $record->no_of_months_renewal) }}" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Date of Activation <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="date_of_activation" 
                                       value="{{ old('date_of_activation', $record->date_of_activation) }}" required>
                            </div>

                             <div class="col-md-4 mb-3">
                                <label class="form-label">Date of Expiry  <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="date_of_expiry" 
                                       value="{{ old('date_of_expiry', $record->date_of_expiry) }}" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">SLA <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="sla" 
                                       value="{{ old('sla', $record->sla) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Status of Link <span class="text-danger">*</span></label>
                                <select class="form-select" name="status_of_link" required>
                                    <option value="">Select Status</option>
                                    <option value="Delivered and Activated" {{ old('status_of_link', $record->status_of_link) == 'Delivered and Activated' ? 'selected' : '' }}>Delivered and Activated</option>
                                    <option value="Delivered" {{ old('status_of_link', $record->status_of_link) == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="Inprogress" {{ old('status_of_link', $record->status_of_link) == 'Inprogress' ? 'selected' : '' }}>Inprogress</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                @php
                                    $selectedMode = old('mode_of_delivery', $record->mode_of_delivery);
                                @endphp
                                <label class="form-label">Mode of Delivery <span class="text-danger">*</span></label>
                                <select class="form-select" name="mode_of_delivery" id="mode_of_delivery" required>
                                    <option value="">Select Mode</option>
                                    <option value="PPPoE" {{ $selectedMode === 'PPPoE' ? 'selected' : '' }}>PPPoE</option>
                                    <option value="DHCP" {{ $selectedMode === 'DHCP' ? 'selected' : '' }}>DHCP</option>
                                    <option value="Static IP" {{ in_array($selectedMode, ['Static IP', 'Static']) ? 'selected' : '' }}>Static IP</option>
                                    <option value="PAYMENTS" {{ $selectedMode === 'PAYMENTS' ? 'selected' : '' }}>PAYMENTS</option>

                                </select>
                            </div>

                            <!-- <div class="col-md-4 mb-3">
                                <label class="form-label">Circuit ID</label>
                                <input type="text" class="form-control" name="circuit_id" 
                                       value="{{ old('circuit_id', $record->circuit_id) }}">
                            </div> -->

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Circuit ID</label>
                                <input type="text" class="form-control" value="Auto Generated" readonly>
                            </div>

                        </div>
                    </div>
                </div>
                @php
    $linkCount = $record->feasibility->no_of_links ?? 1;  // align fields with feasibility\'s link count
@endphp

                {{-- PPPoE Configuration --}}
                <div class="card mb-3" id="pppoe_section" style="display: none;">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">PPPoE Configuration</h6>
                    </div>
                    <div class="card-body">
                        @for($i = 1; $i <= $linkCount; $i++)
        <div class="row mb-2">
            <div class="col-md-4">
                <label>Username {{ $i }}</label>
                <input type="text" name="pppoe_username_{{ $i }}" class="form-control"
                       value="{{ old('pppoe_username_'.$i, $record->{'pppoe_username_'.$i} ?? '') }}">
            </div>

            <div class="col-md-4">
                <label>Password {{ $i }}</label>
                <input type="text" name="pppoe_password_{{ $i }}" class="form-control"
                       value="{{ old('pppoe_password_'.$i, $record->{'pppoe_password_'.$i} ?? '') }}">
            </div>

            <div class="col-md-4">
                <label>VLAN {{ $i }}</label>
                <input type="text" name="vlan_{{ $i }}" class="form-control"
                       value="{{ old('vlan_'.$i, $record->{'vlan_'.$i} ?? '') }}">
            </div>
        </div>
    @endfor
                    </div>
                </div>

                {{-- DHCP Configuration --}}
                <div class="card mb-3" id="dhcp_section" style="display: none;">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">DHCP Configuration</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">IP Address</label>
                                <input type="text" class="form-control" name="dhcp_ip_address" 
                                       value="{{ old('dhcp_ip_address', $record->static_ip_address) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">VLAN</label>
                                <input type="text" class="form-control" name="dhcp_vlan" 
                                       value="{{ old('dhcp_vlan', $record->static_vlan) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Static IP Configuration --}}
                <div class="card mb-3" id="static_section" style="display: none;">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">Static IP Configuration</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">IP Address <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="static_ip_address" name="static_ip_address"
                                       value="{{ old('static_ip_address', $record->static_ip_address) }}">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Subnet <span class="text-danger">*</span></label>
                                <select class="form-select" id="static_ip_subnet" name="static_subnet_mask">
                                    <option value="">Select Subnet</option>
                                    @foreach(['/32','/31','/30','/29','/28','/27','/26','/25','/24'] as $subnet)
                                        <option value="{{ $subnet }}" {{ old('static_subnet_mask', $record->static_subnet_mask) == $subnet ? 'selected' : '' }}>{{ $subnet }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">VLAN</label>
                                <input type="text" class="form-control" name="static_vlan_tag"
                                       value="{{ old('static_vlan_tag', $record->static_vlan_tag) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div id="static_info_message" class="alert alert-secondary small mb-3">
                                    Select static IP and subnet to preview network details (Network IP, Gateway, Subnet Mask, and Usable IP range).
                                </div>
                            </div>
                        </div>

                        <div class="row" id="static_info_panel" style="display: none;">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Network IP</label>
                                <input type="text" class="form-control" id="network_ip" name="network_ip"
                                       value="{{ old('network_ip', $record->network_ip) }}" readonly>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Gateway</label>
                                <input type="text" class="form-control" id="gateway" name="gateway"
                                       value="{{ old('gateway', $record->gateway) }}" readonly>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">IP Address</label>
                                <input type="text" class="form-control" id="info_ip_address" readonly
                                       value="{{ old('static_ip_address', $record->static_ip_address) }}">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Subnet Mask</label>
                                <input type="text" class="form-control" id="subnet_mask" name="subnet_mask"
                                       value="{{ old('subnet_mask', $record->subnet_mask) }}" readonly>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Usable IPs</label>
                                <input type="text" class="form-control" id="usable_ips" name="usable_ips"
                                       value="{{ old('usable_ips', $record->usable_ips) }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!--  -->
                <div class="card mb-3"  id="payments_section" style="display: none;">

                <div class="card-header bg-primary text-dark">
                <h6 class="mb-0">Payment Information</h6>
               </div>
<div class="card-body">
<div class="row">
    <div class="col-md-3 mb-2">
        <label>Login URL</label>
        <input type="text" name="payment_login_url" class="form-control"
               value="{{ $record->payment_login_url }}">
    </div>

    <div class="col-md-3 mb-2">
        <label>Quick URL</label>
        <input type="text" name="payment_quick_url" class="form-control"
               value="{{ $record->payment_quick_url }}">
    </div>

    <div class="col-md-3 mb-2">
        <label>Account Number / Username</label>
        <input type="text" name="payment_account_or_username" class="form-control"
               value="{{ $record->payment_account_or_username }}">
    </div>

    <div class="col-md-3 mb-2">
        <label>Password</label>
        <input type="password" name="payment_password" class="form-control"
               value="{{ $record->payment_password }}">
    </div>
</div>

</div>
                </div>
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
                        
<div class="col-md-3 mb-3" >
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
<div class="col-md-3">
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
<div class="col-md-4 mb-3">
                            <label class="form-label">MTU <span class="text-danger">*</span></label>
                            <input type="text" name="mtu" class="form-control" placeholder="Enter MTU" value="{{ old('mtu', $record->mtu) }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"> Wifi Username</label>
                            <input type="text" name="wifi_username" class="form-control" placeholder="Enter Wifi Username" value="{{ old('wifi_username', $record->wifi_username) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Wifi Password</label>
                            <input type="password" name="wifi_password" class="form-control" placeholder="Enter Wifi Password" value="{{ old('wifi_password', $record->wifi_password) }}">
                        </div>

                    </div>

                </div>
                 </div>



                {{-- OTC Information --}}
                <div class="card mb-1">
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

                            <!-- Account ID -->
                            <div class="col-md-4">
                                    <label>Account ID</label>
                                     <input type="text" name="account_id" class="form-control"
                                      value="{{ old('account_id', $record->account_id) }}">
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
                <div class="col-md-4 mb-3">
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

{{-- JavaScript for Dynamic Form Sections --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modeSelect = document.getElementById('mode_of_delivery');
    const pppoeSection = document.getElementById('pppoe_section');
    const dhcpSection = document.getElementById('dhcp_section');
    const staticSection = document.getElementById('static_section');
    const paymentsSection = document.getElementById('payments_section');

    const staticIpInput = document.getElementById('static_ip_address');
    const staticSubnetSelect = document.getElementById('static_ip_subnet');
    const staticInfoPanel = document.getElementById('static_info_panel');
    const staticInfoMessage = document.getElementById('static_info_message');
    const networkInput = document.getElementById('network_ip');
    const gatewayInput = document.getElementById('gateway');
    const subnetMaskInput = document.getElementById('subnet_mask');
    const usableIpsInput = document.getElementById('usable_ips');
    const infoIpAddressInput = document.getElementById('info_ip_address');

    const staticModes = ['Static IP', 'Static'];

    const isStaticMode = () => staticModes.includes(modeSelect?.value);

    function hideStaticInfo() {
        if (staticInfoPanel) {
            staticInfoPanel.style.display = 'none';
        }
        if (staticInfoMessage) {
            staticInfoMessage.style.display = 'block';
        }
        if (networkInput) networkInput.value = '';
        if (gatewayInput) gatewayInput.value = '';
        if (subnetMaskInput) subnetMaskInput.value = '';
        if (usableIpsInput) usableIpsInput.value = '';
        if (infoIpAddressInput && staticIpInput) {
            infoIpAddressInput.value = staticIpInput.value.trim();
        }
    }

    function showStaticInfo() {
        if (staticInfoPanel) {
            staticInfoPanel.style.display = 'flex';
        }
        if (staticInfoMessage) {
            staticInfoMessage.style.display = 'none';
        }
    }

    function toggleSections() {
        if (pppoeSection) pppoeSection.style.display = 'none';
        if (dhcpSection) dhcpSection.style.display = 'none';
        if (staticSection) staticSection.style.display = 'none';
        if(paymentsSection) paymentsSection.style.display = 'none';

        if (!modeSelect) return;

        if (modeSelect.value === 'PPPoE') {
            if (pppoeSection) pppoeSection.style.display = 'block';
        } else if (modeSelect.value === 'DHCP') {
            if (dhcpSection) dhcpSection.style.display = 'block';
        } else if (modeSelect.value === 'PAYMENTS') {
            if (paymentsSection) paymentsSection.style.display = 'block';
        }
         else if (isStaticMode()) {
            if (staticSection) staticSection.style.display = 'block';
        }
       

        if (!isStaticMode()) {
            hideStaticInfo();
            return;
        }

        fetchSubnetDetails();
    }

    async function fetchSubnetDetails() {
        if (!isStaticMode() || !staticIpInput || !staticSubnetSelect) {
            hideStaticInfo();
            return;
        }

        const ip = staticIpInput.value.trim();
        const subnet = staticSubnetSelect.value;

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

    if (modeSelect) {
        modeSelect.addEventListener('change', toggleSections);
    }

    staticIpInput?.addEventListener('input', fetchSubnetDetails);
    staticSubnetSelect?.addEventListener('change', fetchSubnetDetails);

    const assetSelector = document.getElementById('asset_selector');
    const assetSerialInput = document.getElementById('asset_serial_no');

    const syncAssetSerial = () => {
        if (!assetSerialInput) {
            return;
        }
        const selected = assetSelector?.selectedOptions?.[0];
        assetSerialInput.value = selected?.dataset?.serial || '';
    };

    assetSelector?.addEventListener('change', syncAssetSerial);
    syncAssetSerial();

    toggleSections();
});
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

</script>
@endsection
