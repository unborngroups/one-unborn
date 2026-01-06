

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Deliverable</h5>
        </div>

        

        <?php if($errors->any()): ?>

            <div class="alert alert-danger">

                <ul class="mb-0">

                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <li><?php echo e($error); ?></li>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </ul>

            </div>

        <?php endif; ?>


        <div class="card-body">
            
            <div class="card mb-4 border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Feasibility Closed Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Feasibility ID:</strong><br>
                            <?php echo e($record->feasibility->feasibility_request_id ?? 'N/A'); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>Type of Service:</strong><br>
                            <?php echo e($record->feasibility->type_of_service ?? 'N/A'); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>Company Name:</strong><br>
                            <?php echo e($record->feasibility->company->company_name ?? 'N/A'); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>Client Name:</strong><br>
                            <?php echo e($record->feasibility->client->client_name ?? 'N/A'); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>Pincode:</strong><br>
                            <?php echo e($record->feasibility->pincode ?? 'N/A'); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>State:</strong><br>
                            <?php echo e($record->feasibility->state ?? 'N/A'); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>District:</strong><br>
                            <?php echo e($record->feasibility->district ?? 'N/A'); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>Area:</strong><br>
                            <?php echo e($record->feasibility->area ?? 'N/A'); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>Address:</strong><br>
                            <?php echo e($record->feasibility->address ?? 'N/A'); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>Speed:</strong><br>
                            <?php echo e($record->feasibility->speed ?? 'N/A'); ?> 
                        </div>
                        <div class="col-md-3">
                            <strong>SPOC Name:</strong><br>
                            <?php echo e($record->feasibility->spoc_name ?? 'N/A'); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>SPOC Contact1:</strong><br>
                            <?php echo e($record->feasibility->spoc_contact1 ?? 'N/A'); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>No. Of Links:</strong><br>
                            <?php echo e($record->feasibility->no_of_links ?? 'N/A'); ?>

                        </div>
                        
                        <div class="col-md-3">
                            <strong>Vendor Type:</strong><br>
                            <?php echo e($record->feasibility->vendor_type ?? 'N/A'); ?>

                        </div>
                      
                        <div class="col-md-3">
                            <strong>Static IP:</strong><br>
                            <?php echo e($record->feasibility->static_ip ?? 'N/A'); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>Static IP Subnet:</strong><br>
                            <?php echo e($record->feasibility->static_ip_subnet ?? 'N/A'); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>Expected Delivery:</strong><br>
                            <?php echo e($record->feasibility->expected_delivery ? \Carbon\Carbon::parse($record->feasibility->expected_delivery)->format('Y-m-d') : 'N/A'); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>Expected Activation:</strong><br>
                            <?php echo e($record->feasibility->expected_activation ? \Carbon\Carbon::parse($record->feasibility->expected_activation)->format('Y-m-d') : 'N/A'); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>Hardware Required:</strong><br>
                            <?php echo e($record->feasibility->hardware_required ?? 'N/A'); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>Hardware Model Name:</strong><br>
                            <?php echo e($record->feasibility->hardware_model_name ?? 'N/A'); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>PO Number:</strong><br>
                            <span class="badge bg-primary"><?php echo e($record->po_number ?? 'N/A'); ?></span>
                        </div>
                        <div class="col-md-3">
                            <strong>PO Date:</strong><br>
                            <?php echo e($record->po_date ? \Carbon\Carbon::parse($record->po_date)->format('Y-m-d') : 'N/A'); ?>

                        </div>

                    </div>
                    
                </div>
            </div>

            <?php
                $selectedAssetId = old('asset_id', $record->asset_id ?? '');
                $selectedAssetSerial = old('asset_serial_no', $record->asset_serial_no ?? '');
                $selectedAssetMac = old('asset_mac_no', $record->asset_mac_no ?? '');
            ?>

            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">Hardware & Asset Details</h6>
                </div>
                <div class="card-body">
                    <div class="row gy-2">
                        <?php if(!empty($hardwareDetails)): ?>
                            <?php $__currentLoopData = $hardwareDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6">
                                    <div class="border rounded p-2">
                                        <small class="text-muted">Hardware <?php echo e($index + 1); ?></small>
                                        <p class="mb-1"><strong>Make:</strong> <?php echo e($detail['make'] ?? '-'); ?></p>
                                        <p class="mb-0"><strong>Model:</strong> <?php echo e($detail['model'] ?? '-'); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <div class="col-12">
                                <p class="mb-0 text-muted">No hardware information was carried over from the feasibility request.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Asset ID</label>
                            <select name="asset_id" id="asset_selector" class="form-select">
                                <option value="">-- Select Asset --</option>
                                <?php $__currentLoopData = $assetOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($asset->asset_id); ?>"
                                            data-serial="<?php echo e($asset->serial_no); ?>"
                                            data-mac="<?php echo e($asset->mac_no); ?>"
                                            <?php echo e($selectedAssetId && $selectedAssetId === $asset->asset_id ? 'selected' : ''); ?>>
                                        <?php echo e($asset->asset_id); ?><?php if(!empty($asset->serial_no)): ?> - <?php echo e($asset->serial_no); ?><?php endif; ?>
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if($selectedAssetId && !$assetOptions->contains('asset_id', $selectedAssetId)): ?>
                                    <option value="<?php echo e($selectedAssetId); ?>" data-serial="<?php echo e($selectedAssetSerial); ?>" data-mac="<?php echo e($selectedAssetMac); ?>" selected>
                                        <?php echo e($selectedAssetId); ?><?php if(!empty($selectedAssetSerial)): ?> - <?php echo e($selectedAssetSerial); ?><?php endif; ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Serial Number</label>
                            <input type="text" id="asset_serial_no" name="asset_serial_no" class="form-control" readonly value="<?php echo e($selectedAssetSerial); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">MAC Number</label>
                            <input type="text" id="asset_mac_no" name="asset_mac_no" class="form-control" readonly value="<?php echo e($selectedAssetMac ?? ''); ?>">
                        </div>
                    </div>
                </div>
            </div>

            
            <form action="<?php echo e(route('operations.deliverables.save', $record->id)); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                
            <?php
                $linkCount = $record->feasibility->no_of_links ?? 1;
                $plans = $record->deliverablePlans->keyBy('link_number');
            ?>
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        Plan Information
                    </div>
                    <div class="card-body">
                        <?php for($i = 1; $i <= $linkCount; $i++): ?>
                            <?php
                                $plan = $plans->get($i);
                                $defaultPlanName = isset($plan) ? $plan->plans_name : ($i == 1 ? '' : null);
                                $defaultSpeed = isset($plan) ? $plan->speed_in_mbps_plan : ($i == 1 ? '' : null);
                                $defaultRenewal = isset($plan) ? $plan->no_of_months_renewal : ($i == 1 ? '' : null);

                                $today = \Carbon\Carbon::now()->format('Y-m-d');

                                if (isset($plan) && !empty($plan->date_of_activation)) {
                                    $defaultActivationDate = $plan->date_of_activation->format('Y-m-d');
                                } elseif ($i == 1 && isset($record->date_of_activation)) {
                                    $defaultActivationDate = \Carbon\Carbon::parse($record->date_of_activation)->format('Y-m-d');
                                } else {
                                    $defaultActivationDate = $today;
                                }

                                if (isset($plan) && !empty($plan->date_of_expiry)) {
                                    $defaultExpiryDate = $plan->date_of_expiry->format('Y-m-d');
                                } elseif ($i == 1 && isset($record->date_of_expiry)) {
                                    $defaultExpiryDate = \Carbon\Carbon::parse($record->date_of_expiry)->format('Y-m-d');
                                } else {
                                    $defaultExpiryDate = $today;
                                }
                            ?>
                            <div class="row border rounded mb-3 p-2">
                                <div class="col-12 mb-2">
                                    <strong>Plan Information for Link <?php echo e($i); ?></strong>
                                </div>
                                
                                 <div class="col-md-3 mb-3">
                                <label class="form-label">Circuit ID</label>
                                <input type="text" class="form-control" value="Auto Generated" readonly>
                               </div>

                               <div class="col-md-3 mb-3">
                                   <label class="form-label">Vendor Name</label>
                                   <input type="text" class="form-control" value="<?php echo e($vendor->vendor_name ?? ''); ?>" readonly>
                               </div>
                               <div class="col-md-3 mb-3">
                                   <label class="form-label">Vendor Email</label>
                                   <input type="text" class="form-control" value="<?php echo e($vendor->contact_person_email ?? ''); ?>" readonly>
                               </div>
                               <div class="col-md-3 mb-3">
                                   <label class="form-label">Vendor Contact</label>
                                   <input type="text" class="form-control" value="<?php echo e($vendor->contact_person_mobile ?? ''); ?>" readonly>
                               </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Plans Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="plans_name_<?php echo e($i); ?>"
                                           value="<?php echo e(old('plans_name_'.$i, $defaultPlanName ?? '' )); ?>" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Speed in Mbps (Plan) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="speed_in_mbps_plan_<?php echo e($i); ?>"
                                           value="<?php echo e(old('speed_in_mbps_plan_'.$i, $defaultSpeed ?? '' )); ?>" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">No of Months Renewal <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="no_of_months_renewal_<?php echo e($i); ?>"
                                           value="<?php echo e(old('no_of_months_renewal_'.$i, $defaultRenewal ?? ''  )); ?>" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Date of Activation <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="date_of_activation_<?php echo e($i); ?>"
                                        value="<?php echo e(old('date_of_activation_'.$i, $defaultActivationDate)); ?>" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Date of Expiry  <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="date_of_expiry_<?php echo e($i); ?>"
                                        value="<?php echo e(old('date_of_expiry_'.$i, $defaultExpiryDate)); ?>" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">SLA <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="sla_<?php echo e($i); ?>"
                                       value="<?php echo e(old('sla_'.$i, $plan->sla ?? ($i == 1 ? $record->sla : ''))); ?>" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Status of Link <span class="text-danger">*</span></label>
                                <select class="form-select" name="status_of_link_<?php echo e($i); ?>" required>
                                    <option value="">Select Status</option>
                                    <option value="Delivered and Activated" <?php echo e(old('status_of_link_'.$i, $plan->status_of_link ?? '') == 'Delivered and Activated' ? 'selected' : ''); ?>>Delivered and Activated</option>
                                    <option value="Delivered" <?php echo e(old('status_of_link_'.$i, $plan->status_of_link ?? '') == 'Delivered' ? 'selected' : ''); ?>>Delivered</option>
                                    <option value="Inprogress" <?php echo e(old('status_of_link_'.$i, $plan->status_of_link ?? '') == 'Inprogress' ? 'selected' : ''); ?>>Inprogress</option>
                                </select>
                            </div>

                            <!-- mode of Delivery -->
                             <div class="col-md-3 mb-3">
                                <?php
                                    $selectedMode = old("mode_of_delivery_$i", $plan->mode_of_delivery ?? '');
                                ?>
                                <label class="form-label">Mode of Delivery <span class="text-danger">*</span></label>
                                <select class="form-select mode_of_delivery" name="mode_of_delivery_<?php echo e($i); ?>" data-link="<?php echo e($i); ?>" onchange="toggleSectionsByLink(<?php echo e($i); ?>, this.value)" required>
                                    <option value="">Select Mode</option>
                                    <option value="PPPoE" <?php echo e($selectedMode === 'PPPoE' ? 'selected' : ''); ?>>PPPoE</option>
                                    <option value="DHCP" <?php echo e($selectedMode === 'DHCP' ? 'selected' : ''); ?>>DHCP</option>
                                    <!-- <option value="Static IP" <?php echo e(in_array($selectedMode, ['Static IP', 'Static']) ? 'selected' : ''); ?>>Static IP</option> -->
                                    <!-- <option value="PAYMENTS" <?php echo e($selectedMode === 'PAYMENTS' ? 'selected' : ''); ?>>PAYMENTS</option> -->

                                </select>
                            </div>

                            <!-- Client Circuit ID -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Client Circuit ID</label>
                                <!-- Removed duplicate: use only $plan->client_circuit_id version below -->
                                                    <input type="text" class="form-control" name="client_circuit_id_<?php echo e($i); ?>" value="<?php echo e(old('client_circuit_id_'.$i, $plan->client_circuit_id ?? '')); ?>">
                            </div>

                            <!-- Client Feasibility -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Client Feasibility</label>
                            
                                                    <input type="text" class="form-control" name="client_feasibility_<?php echo e($i); ?>" value="<?php echo e(old('client_feasibility_'.$i, $plan->client_feasibility ?? '')); ?>">
                            </div>

                            <!-- Vendor Code -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Vendor Code</label>
                                <!-- Removed duplicate: use only $plan->vendor_code version below -->
                                                    <input type="text" class="form-control" name="vendor_code_<?php echo e($i); ?>" value="<?php echo e(old('vendor_code_'.$i, $plan->vendor_code ?? '')); ?>">
                            </div>
                            <!-- MTU -->
                            <div class="col-md-3 mb-3">
                            <label class="form-label">MTU <span class="text-danger">*</span></label>
                            <!-- Removed duplicate: use only $plan->mtu version below -->
                                                <input type="text" name="mtu_<?php echo e($i); ?>" class="form-control" placeholder="Enter MTU" value="<?php echo e(old('mtu_'.$i, $plan->mtu ?? '')); ?>" required>
                        </div>
                        <!-- Wifi Username -->
                         <div class="col-md-3 mb-3">
                            <label class="form-label">Wifi Username</label>
                            <!-- Removed duplicate: use only $plan->wifi_username version below -->
                                                <input type="text" name="wifi_username_<?php echo e($i); ?>" class="form-control" placeholder="Enter Wifi Username" value="<?php echo e(old('wifi_username_'.$i, $plan->wifi_username ?? '')); ?>">
                        </div>
                        <!-- Wifi Password -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Wifi Password</label>
                            <!-- Removed duplicate: use only $plan->wifi_password version below -->
                                                <input type="text" name="wifi_password_<?php echo e($i); ?>" class="form-control" placeholder="Enter Wifi Password" value="<?php echo e(old('wifi_password_'.$i, $plan->wifi_password ?? '')); ?>">
                        </div>
                        <!-- Router Username -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Router Username</label>
                            <!-- Removed duplicate: use only $plan->router_username version below -->
                                                <input type="text" name="router_username_<?php echo e($i); ?>" class="form-control" placeholder="Enter Router Username" value="<?php echo e(old('router_username_'.$i, $plan->router_username ?? '')); ?>">
                        </div>
                        <!-- Router Password -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Router Password</label>
                            <!-- Removed duplicate: use only $plan->router_password version below -->
                                                <input type="text" name="router_password_<?php echo e($i); ?>" class="form-control" placeholder="Enter Router Password" value="<?php echo e(old('router_password_'.$i, $plan->router_password ?? '')); ?>">
                        </div>

                        <!-- payment details -->
                         
                <div class="card mb-1">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">PAYMENTS Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
        <label>Login URL</label>
        <input type="text" name="payment_login_url_<?php echo e($i); ?>" class="form-control"
               value="<?php echo e(old('payment_login_url_'.$i, $plan->payment_login_url ?? '')); ?>">
    </div>

    <div class="col-md-3 mb-2">
        <label>Quick URL</label>
        <input type="text" name="payment_quick_url_<?php echo e($i); ?>" class="form-control"
               value="<?php echo e(old('payment_quick_url_'.$i, $plan->payment_quick_url ?? '')); ?>">
    </div>

    <div class="col-md-3 mb-2">
        <label>Account Number / Username</label>
        <input type="text" name="payment_account_or_username_<?php echo e($i); ?>" class="form-control"
               value="<?php echo e(old('payment_account_or_username_'.$i, $plan->payment_account_or_username ?? '')); ?>">
    </div>
    <div class="col-md-3 mb-2">
        <label>Password</label>
        <input type="text" name="payment_password_<?php echo e($i); ?>" class="form-control" placeholder="Enter new password"
                   value="<?php echo e(old('payment_password_'.$i, $plan->payment_password ?? '')); ?>" placeholder="Enter new password">
    </div>

                        </div>
                    </div>
                </div>

                <!-- static_ip -->

<?php if(($record->feasibility->static_ip ?? 'No') === 'Yes'): ?>
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">Static IP Configuration</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">IP Address <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="static_ip_address_<?php echo e($i); ?>" name="static_ip_address_<?php echo e($i); ?>"
                                       value="<?php echo e(old('static_ip_address_'.$i, $plan->static_ip_address ?? '')); ?>">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Subnet <span class="text-danger">*</span></label>
                                <select class="form-select" id="static_ip_subnet_<?php echo e($i); ?>" name="static_subnet_mask_<?php echo e($i); ?>">
                                    <option value="">Select Subnet</option>
                                    <?php $__currentLoopData = ['/32','/31','/30','/29','/28','/27','/26','/25','/24']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subnet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($subnet); ?>" <?php echo e(old('static_subnet_mask_'.$i, $plan->static_subnet_mask ?? '') == $subnet ? 'selected' : ''); ?>><?php echo e($subnet); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                    <label class="form-label">VLAN <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="static_vlan_tag_<?php echo e($i); ?>" name="static_vlan_tag_<?php echo e($i); ?>"
                                        value="<?php echo e(old('static_vlan_tag_'.$i, $plan->static_vlan ?? '')); ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div id="static_info_message_<?php echo e($i); ?>" class="alert alert-secondary small mb-3">
                                    Select static IP and subnet to preview network details (Network IP, Gateway, Subnet Mask, and Usable IP range).
                                </div>
                            </div>
                        </div>

                        <div class="row static-ip-summary" id="static_info_pane_<?php echo e($i); ?>">
    <div class="col-md-3 mb-3 static-ip-col">
        <label class="form-label">Network IP</label>
        <!-- Removed duplicate: use only $plan->network_ip version below -->
        <input type="text" class="form-control" id="network_ip_<?php echo e($i); ?>" name="network_ip_<?php echo e($i); ?>" value="<?php echo e(old('network_ip_'.$i, $plan->network_ip ?? '')); ?>">
    </div>
    <div class="col-md-3 mb-3 static-ip-col">
        <label class="form-label">Gateway</label>
        <!-- Removed duplicate: use only $plan->static_gateway version below -->
        <input type="text" class="form-control" id="gateway_<?php echo e($i); ?>" name="gateway_<?php echo e($i); ?>" value="<?php echo e(old('gateway_'.$i, $plan->static_gateway ?? '')); ?>">
    </div>
    <div class="col-md-3 mb-3 static-ip-col">
        <label class="form-label">Subnet Mask</label>
        <!-- Removed duplicate: use only $plan->static_subnet_mask version below -->
        <input type="text" class="form-control" id="subnet_mask_<?php echo e($i); ?>" name="subnet_mask_<?php echo e($i); ?>" value="<?php echo e(old('subnet_mask_'.$i, $plan->static_subnet_mask ?? '')); ?>">
    </div>
    <div class="col-md-3 mb-3 static-ip-col">
        <label class="form-label">Usable IPs</label>
        <!-- Removed duplicate: use only $plan->usable_ips version below -->
        <input type="text" class="form-control" id="usable_ips_<?php echo e($i); ?>" name="usable_ips_<?php echo e($i); ?>" value="<?php echo e(old('usable_ips_'.$i, $plan->usable_ips ?? '')); ?>">
    </div>
                        </div>
                    </div>
                </div>
<?php endif; ?>

                <!-- end Static_ip -->

                        </div>
                    <?php endfor; ?>
                              
                    </div>
                </div>
                <?php
    $linkCount = $record->feasibility->no_of_links ?? 1;  // align fields with feasibility\'s link count
?>
<!--  -->

<?php for($i = 1; $i <= $linkCount; $i++): ?>
    <?php
        $plan = $record->deliverablePlans->where('link_number', $i)->first();
    ?>

                
                <div class="card mb-3" id="pppoe_section_<?php echo e($i); ?>" style="display: none;">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">PPPoE Configuration</h6>
                    </div>
                    <div class="card-body">
                        
        <div class="row mb-2">
            <div class="col-md-4">
                <label class="form-label">Username</label>
                <input type="text" name="pppoe_username_<?php echo e($i); ?>" class="form-control" placeholder="Enter pppoe_username_" value="<?php echo e(old('pppoe_username_'.$i, $plan->pppoe_username ?? '')); ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Password</label>
                <input type="text" name="pppoe_password_<?php echo e($i); ?>" class="form-control" placeholder="Enter password"
                       value="<?php echo e(old('pppoe_password_'.$i, $plan->pppoe_password ?? '')); ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">VLAN</label>
                <input type="text" name="pppoe_vlan_<?php echo e($i); ?>" class="form-control" placeholder="Enter VLAN"
                       value="<?php echo e(old('pppoe_vlan_'.$i, $plan->pppoe_vlan ?? '')); ?>">
            </div>
        </div>
    
                    </div>
                </div>

                
                <div class="card mb-3" id="dhcp_section_<?php echo e($i); ?>" style="display: none;">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">DHCP Configuration</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">IP Address</label>
                                <input type="text" class="form-control" name="dhcp_ip_address_<?php echo e($i); ?>" 
                                       value="<?php echo e(old('dhcp_ip_address_'.$i, $plan->dhcp_ip_address ?? '')); ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">VLAN</label>
                                <input type="text" class="form-control" name="dhcp_vlan_<?php echo e($i); ?>" 
                                       value="<?php echo e(old('dhcp_vlan_'.$i, $plan->dhcp_vlan ?? '')); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <?php endfor; ?>
                <!--  -->
                 <div class="card mb-3 ">
                <div class="card-header bg-primary text-white">
                <h6 class="mb-0"> Configuration</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label>LAN IP 1 <span style="color:red">*</span></label>
                                <input type="text" name="lan_ip_1" class="form-control" required value="<?php echo e(old('lan_ip_1', $record->lan_ip_1 ?? '')); ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>LAN IP 2</label>
                                <input type="text" name="lan_ip_2" class="form-control" value="<?php echo e(old('lan_ip_2', $record->lan_ip_2 ?? '')); ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>LAN IP 3</label>
                                <input type="text" name="lan_ip_3" class="form-control" value="<?php echo e(old('lan_ip_3', $record->lan_ip_3 ?? '')); ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>LAN IP 4</label>
                                <input type="text" name="lan_ip_4" class="form-control" value="<?php echo e(old('lan_ip_4', $record->lan_ip_4 ?? '')); ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>IPSEC</label>
                                <select name="ipsec" id="ipsec" class="form-control">
                                    <option value="">-- Select --</option>
                                    <option value="Yes" <?php echo e(old('ipsec', $record->ipsec ?? '') == 'Yes' ? 'selected' : ''); ?>>Yes</option>
                                    <option value="No" <?php echo e(old('ipsec', $record->ipsec ?? '') == 'No' ? 'selected' : ''); ?>>No</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3 ipsec-fields d-none">
                                <label>Phase 1</label>
                                <input type="text" name="phase_1" class="form-control" value="<?php echo e(old('phase_1', $record->phase_1 ?? '')); ?>">
                            </div>
                            <div class="col-md-3 mb-3 ipsec-fields d-none">
                                <label>Phase 2</label>
                                <input type="text" name="phase_2" class="form-control" value="<?php echo e(old('phase_2', $record->phase_2 ?? '')); ?>">
                            </div>
                            <div class="col-md-3 mb-3 ipsec-fields d-none">
                                <label>IPSEC Interface</label>
                                <input type="text" name="ipsec_interface" class="form-control" value="<?php echo e(old('ipsec_interface', $record->ipsec_interface ?? '')); ?>">
                            </div>
                        </div>
                    </div>
                </div>



                
                <div class="card mb-1 mx-3">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">OTC Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">OTC (Extra if any)</label>
                                <input type="number" step="0.01" class="form-control" name="otc_extra_charges" 
                                       value="<?php echo e(old('otc_extra_charges', $record->otc_extra_charges)); ?>">
                            </div>

                               <!-- Upload OTC Bill -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Upload OTC Bill</label>
                                <?php if($record->otc_bill_file): ?>
                                    <a href="<?php echo e(asset($record->otc_bill_file)); ?>" target="_blank">View OTC Bill</a>
                                <?php endif; ?>
                                <input type="file" class="form-control" name="otc_bill_file" accept=".pdf,.jpg,.jpeg,.png">
                                <?php if($record->otc_bill_file): ?>
                                    <small class="text-muted">Current: <?php echo e(basename($record->otc_bill_file)); ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!--  -->
                <div class="col-md-4 mb-3 mx-3">
    <label class="form-label">Upload Export File</label>
    <?php if($record->export_file): ?>
        <a href="<?php echo e(asset($record->export_file)); ?>" target="_blank">View Export File</a>
    <?php endif; ?>
    <input type="file" name="export_file" class="form-control" accept=".pdf,.xlsx,.xls,.csv,.jpg,.jpeg,.png">
</div>


                
                <?php
                    // Default back route based on status
                    $backRoute = 'operations.deliverables.open';
                    if ($record->status === 'InProgress') {
                        $backRoute = 'operations.deliverables.inprogress';
                    } elseif ($record->status === 'Delivery') {
                        $backRoute = 'operations.deliverables.delivery';
                    }

                    // If this is an ILL deliverable (shown under Accepted), go back to acceptance list
                    if (optional($record->feasibility)->type_of_service === 'ILL') {
                        $backRoute = 'operations.deliverables.acceptance';
                    }
                ?>

                <div class="d-flex justify-content-between mt-4">
                    <a href="<?php echo e(route($backRoute)); ?>" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                    <!-- upload conf -->

                    <div>
                        <?php if($record->status == 'Open'): ?>
                            <button type="submit" name="action" value="save" class="btn btn-primary me-2">
                                <i class="bi bi-floppy"></i> Save (Move to In Progress)
                            </button>
                            
                            <button type="submit" name="action" value="submit" class="btn btn-success">
                                <i class="bi bi-check2-all"></i> Submit (Move to Delivery)
                            </button>
                        <?php elseif($record->status == 'InProgress'): ?>
                            <button type="submit" name="action" value="save" class="btn btn-primary me-2">
                                <i class="bi bi-floppy"></i> Save
                            </button>
                            
                            <button type="submit" name="action" value="submit" class="btn btn-success">
                                <i class="bi bi-check2-all"></i> Submit (Move to Delivery)
                            </button>
                        <?php else: ?>
                       
                        
                            <button type="submit" name="action" value="save" class="btn btn-primary">
                                <i class="bi bi-floppy"></i> Save Changes
                            </button>
                            <button type="submit" name="action" value="submit" class="btn btn-success">
                                <i class="bi bi-check2-all"></i> Submit (Move to Delivery)
                            </button>
                        <?php endif; ?>
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

    /* Ensure Static IP summary fields show 4-in-a-row on desktop */
    .static-ip-summary {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        column-gap: 1rem;
    }

    .static-ip-summary .static-ip-col {
        width: 100%;
        max-width: 100%;
    }

    @media (max-width: 991px) {
        .static-ip-summary {
            grid-template-columns: 1fr;
        }
    }
</style>


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
<?php $linkCount = $record->feasibility->no_of_links ?? 1;
?>
document.addEventListener('DOMContentLoaded', function () {
    <?php for($i = 1; $i <= $linkCount; $i++): ?>
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
                const response = await fetch(`<?php echo e(route('calculate.subnet')); ?>?ip=${encodeURIComponent(ip)}&subnet=${encodeURIComponent(subnet)}`);
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
    })(<?php echo e($i); ?>);
    <?php endfor; ?>
});

 // For multiple links
    function toggleSectionsByLink(linkNo, selectedMode) {
    const pppoe = document.getElementById(`pppoe_section_${linkNo}`);
    const dhcp = document.getElementById(`dhcp_section_${linkNo}`);
    // const stat = document.getElementById(`static_section_${linkNo}`);
    // const pay  = document.getElementById(`payments_section_${linkNo}`);

    [pppoe, dhcp, stat, pay].forEach(sec => sec && (sec.style.display = 'none')) ;

    if (selectedMode === 'PPPoE') pppoe.style.display = 'block';
    if (selectedMode === 'DHCP') dhcp.style.display = 'block';
    // if (selectedMode === 'Static IP' || selectedMode === 'Static') stat.style.display = 'block';
    // if (selectedMode === 'PAYMENTS') pay.style.display = 'block';
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function syncAssetFields() {
            const select = document.getElementById('asset_selector');
            if (!select) return;

            const selected = select.options[select.selectedIndex];
            if (!selected) return;  

            const serial = selected.getAttribute('data-serial') || '';
            const mac = selected.getAttribute('data-mac') || '';

            const serialInput = document.getElementById('asset_serial_no');
            const macInput = document.getElementById('asset_mac_no');

            if (serialInput) serialInput.value = serial;
            if (macInput) macInput.value = mac;
        }

        // Initial sync on page load (for already-selected asset)
        syncAssetFields();

        // Update fields whenever asset selection changes
        const selectEl = document.getElementById('asset_selector');
        if (selectEl) {
            selectEl.addEventListener('change', syncAssetFields);
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\operations\deliverables\edit.blade.php ENDPATH**/ ?>