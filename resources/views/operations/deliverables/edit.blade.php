@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Deliverable</h5>
        </div>

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
                            <strong>Speed:</strong><br>
                            {{ $record->feasibility->speed ?? 'N/A' }} Mbps
                        </div>
                        <div class="col-md-3">
                            <strong>Vendor Type:</strong><br>
                            {{ $record->feasibility->vendor_type ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Speed:</strong><br>
                            {{ $record->feasibility->speed ?? 'N/A' }}
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
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <strong>PO Number:</strong><br>
                            <span class="badge bg-primary">{{ $record->po_number ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>PO Date:</strong><br>
                            {{ $record->po_date ? \Carbon\Carbon::parse($record->po_date)->format('d-m-Y') : 'N/A' }}
                        </div>
                        <!-- <div class="col-md-3">
                            <strong>Vendor:</strong><br>
                            {{ $record->vendor ?? 'N/A' }}
                        </div> -->
                        
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
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Plans Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="plans_name" 
                                       value="{{ old('plans_name', $record->plans_name) }}" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Speed in Mbps (Plan) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="speed_in_mbps_plan" 
                                       value="{{ old('speed_in_mbps_plan', $record->speed_in_mbps_plan) }}" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">No of Months Renewal <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="no_of_months_renewal" 
                                       value="{{ old('no_of_months_renewal', $record->no_of_months_renewal) }}" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Date of Activation <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="date_of_activation" 
                                       value="{{ old('date_of_activation', $record->date_of_activation) }}" required>
                            </div>

                            <div class="col-md-3 mb-3">
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
                                <label class="form-label">Mode of Delivery <span class="text-danger">*</span></label>
                                <select class="form-select" name="mode_of_delivery" id="mode_of_delivery" required>
                                    <option value="">Select Mode</option>
                                    <option value="PPPoE" {{ old('mode_of_delivery', $record->mode_of_delivery) == 'PPPoE' ? 'selected' : '' }}>PPPoE</option>
                                    <option value="DHCP" {{ old('mode_of_delivery', $record->mode_of_delivery) == 'DHCP' ? 'selected' : '' }}>DHCP</option>
                                    <option value="Static IP" {{ old('mode_of_delivery', $record->mode_of_delivery) == 'Static IP' ? 'selected' : '' }}>Static IP</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Circuit ID</label>
                                <input type="text" class="form-control" name="circuit_id" 
                                       value="{{ old('circuit_id', $record->circuit_id) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PPPoE Configuration --}}
                <div class="card mb-3" id="pppoe_section" style="display: none;">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">PPPoE Configuration</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="pppoe_username" 
                                       value="{{ old('pppoe_username', $record->pppoe_username) }}">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="pppoe_password" 
                                       value="{{ old('pppoe_password', $record->pppoe_password) }}">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">VLAN</label>
                                <input type="text" class="form-control" name="pppoe_vlan" 
                                       value="{{ old('pppoe_vlan', $record->static_vlan) }}">
                            </div>
                        </div>
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
                                <label class="form-label">IP Address</label>
                                <input type="text" class="form-control" name="static_ip_address" 
                                       value="{{ old('static_ip_address', $record->static_ip_address) }}">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Subnet Mask</label>
                                <input type="text" class="form-control" name="static_subnet_mask" 
                                       value="{{ old('static_subnet_mask', $record->static_subnet_mask) }}">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Gateway</label>
                                <input type="text" class="form-control" name="static_gateway" 
                                       value="{{ old('static_gateway', $record->static_gateway) }}">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">VLAN</label>
                                <input type="text" class="form-control" name="static_vlan_tag" 
                                       value="{{ old('static_vlan_tag', $record->static_vlan_tag) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- OTC Information --}}
                <div class="card mb-3">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">OTC Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">OTC (Extra if any)</label>
                                <input type="number" step="0.01" class="form-control" name="otc_extra_charges" 
                                       value="{{ old('otc_extra_charges', $record->otc_extra_charges) }}">
                            </div>

                            <div class="col-md-6 mb-3">
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

                {{-- Action Buttons --}}
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('operations.deliverables.open') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>

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
    
    function toggleSections() {
        // Hide all sections first
        pppoeSection.style.display = 'none';
        dhcpSection.style.display = 'none';
        staticSection.style.display = 'none';
        
        // Show relevant section
        if (modeSelect.value === 'PPPoE') {
            pppoeSection.style.display = 'block';
        } else if (modeSelect.value === 'DHCP') {
            dhcpSection.style.display = 'block';
        } else if (modeSelect.value === 'Static IP') {
            staticSection.style.display = 'block';
        }
    }
    
    modeSelect.addEventListener('change', toggleSections);
    toggleSections(); // Initialize on page load
});
</script>
@endsection
