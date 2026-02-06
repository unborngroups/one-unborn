

<?php $__env->startSection('content'); ?>

<div class="container-fluid py-4">

    <h4 class="text-primary fw-bold mb-3 ">Add Feasibility</h4>
    
    
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
                <?php if(session('imported_rows')): ?>
                    <div class="card border-success mb-3 mt-2">
                        <div class="card-header bg-success text-white py-1">Imported Rows Summary</div>
                        <div class="card-body p-2">
                            <div style="max-height:300px;overflow:auto;">
                                <table class="table table-bordered table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <?php $__currentLoopData = session('import_headers', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <th><?php echo e($header); ?></th>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = session('imported_rows', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cell): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <td><?php echo e($cell); ?></td>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
        <?php endif; ?>

    
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>


    
    <?php if(session('import_errors')): ?>
        <div class="alert alert-warning">
            <strong>Import could not process some rows:</strong>
            <ul class="mb-2">
                <?php $__currentLoopData = session('import_errors'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <?php if(session('failed_rows') && count(session('failed_rows', [])) > 0): ?>
                <form action="<?php echo e(route('feasibility.downloadFailedRows')); ?>" method="POST" class="mb-2">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        Download Failed Rows (CSV)
                    </button>
                </form>
                <div id="failedRowsTable" style="max-height:300px; overflow:auto;">
                    <input type="text" id="failedFilterInput" class="form-control form-control-sm mb-2" placeholder="Filter by error reason..." onkeyup="filterFailedRows()">
                    <table class="table table-bordered table-sm mt-2">
                        <thead>
                            <tr>
                                <?php $__currentLoopData = session('import_headers', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <th><?php echo e($header); ?></th>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <th>Error Reason</th>
                            </tr>
                        </thead>
                        <tbody id="failedRowsTbody">
                            <?php $__currentLoopData = session('failed_rows', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <?php $__currentLoopData = session('import_headers', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td><?php echo e($row[$header] ?? ''); ?></td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <td class="text-danger"><?php echo e($row['Error Reason'] ?? ''); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <script>
                    function filterFailedRows() {
                        var input = document.getElementById('failedFilterInput');
                        var filter = input.value.toLowerCase();
                        var tbody = document.getElementById('failedRowsTbody');
                        var rows = tbody.getElementsByTagName('tr');
                        for (var i = 0; i < rows.length; i++) {
                            var show = false;
                            var cells = rows[i].getElementsByTagName('td');
                            for (var j = 0; j < cells.length; j++) {
                                if (cells[j].innerText.toLowerCase().indexOf(filter) > -1) {
                                    show = true;
                                    break;
                                }
                            }
                            rows[i].style.display = show ? '' : 'none';
                        }
                    }
                </script>
            <?php else: ?>
                <div class="alert alert-info mt-2">No failed rows to display.</div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

        <?php
            $importRow = session('imported_row', []);
        ?>
     
    <!-- <h5 class="mb-3 ">Import Feasibility</h5> -->
        <div class="row g-3 mb-3">
            <div class="col-md-12">
                <button class="btn btn-info" type="button" onclick="toggleImportFeasibility()">
                    Import Feasibility
                </button>
                <div id="importFeasibilityBox" style="display: none;">
                    <div class="card border-info">
                        <div class="card-body">
                            <p class="mb-3 small text-muted">Download the sample format, populate it with feasibility data, and then upload it via Import Excel.</p>
                            <form id="importExcelForm" action="<?php echo e(route('feasibility.import')); ?>" method="POST" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                <div class="input-group">
                                    <input type="file" name="file" class="form-control" required>
                                    <a href="<?php echo e(asset('images/feasibilityimport/Import Example Feasibility.csv')); ?>" target="_blank" class="btn btn-outline-secondary" title="Download import template">Download Format</a>
                                    <button type="submit" class="btn btn-primary">Import Excel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Loader Overlay -->
                <div id="importLoaderOverlay" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(255,255,255,0.7);z-index:9999;align-items:center;justify-content:center;">
                    <div style="text-align:center;">
                        <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="mt-2 fw-bold">Importing, please wait...</div>
                    </div>
                </div>
                <script>
                    function toggleImportFeasibility() {
                        var box = document.getElementById('importFeasibilityBox');
                        box.style.display = (box.style.display === 'none' || box.style.display === '') ? 'block' : 'none';
                    }
                    // Show loader on import form submit
                    document.addEventListener('DOMContentLoaded', function() {
                        var form = document.getElementById('importExcelForm');
                        if (form) {
                            form.addEventListener('submit', function() {
                                document.getElementById('importLoaderOverlay').style.display = 'flex';
                            });
                        }
                    });
                </script>
            </div>
        </div>
<!--  -->


    <div class="card shadow border-0 p-4">



     

        <?php if($errors->any()): ?>

            <div class="alert alert-danger">

                <ul class="mb-0">

                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <li><?php echo e($error); ?></li> 

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </ul>

            </div>

        <?php endif; ?>

        <?php if(session('success')): ?>

            <div class="alert alert-success">
                <?php echo e(session('success')); ?>

            </div>

        <?php endif; ?>


<!--         

        <h5 class="mb-3">Import Feasibility</h5>
        <div class="row g-3 align-items-center ml-2 mb-4">
            <div class="col-md-4">
                <form action="<?php echo e(route('feasibility.import')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="input-group">
                        <input type="file" name="file" class="form-control" required>
                        <button type="submit" class="btn btn-primary">Import Excel</button>
                    </div>
                </form>
            </div>
    </div>
 -->



         

        <form action="<?php echo e(route('feasibility.store')); ?>" method="POST">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Link Type <span class="text-danger">*</span></label>
                    <select id="link_type" name="link_type" class="form-select" required>
                        <option value="">Select</option>
                        <option value="new">New Link</option>
                        <option value="existing">Existing Link</option>
                    </select>
                </div>
                <div class="col-md-4" id="existing_circuit_box" style="display:none;">
                    <label class="form-label fw-semibold">Select Circuit ID</label>
                    <select id="circuit_id" name="circuit_id" class="form-select select2-tags">
                        <option value="">Select Circuit ID</option>
                        <?php $__currentLoopData = $deliverables_plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($d['circuit_id']); ?>"><?php echo e($d['circuit_id']); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <?php echo csrf_field(); ?>

            <div class="row g-3" id="new_link_fields">
                <!-- Feasibility ID -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">Feasibility Request ID</label>

                    <input type="text" class="form-control bg-light" value="Auto-generated" readonly>

                    <!-- <small class="text-muted">ID will be generated automatically when saved</small> -->

                </div>
                <!-- Type Of Service -->

                <div class="col-md-3">

                    <label class="form-label fw-semibold">Type of Service <span class="text-danger">*</span></label>

                    <?php $typeSelection = old('type_of_service', $importRow['type_of_service'] ?? ''); ?>
                    <select name="type_of_service" id="type_of_service" class="form-select" required>

                        <option value="" <?php echo e($typeSelection === '' ? 'selected' : ''); ?>>Select</option>

                        <option value="Broadband" <?php echo e($typeSelection === 'Broadband' ? 'selected' : ''); ?>>Broadband</option>

                        <option value="ILL" <?php echo e($typeSelection === 'ILL' ? 'selected' : ''); ?>>ILL</option>

                        <option value="P2P" <?php echo e($typeSelection === 'P2P' ? 'selected' : ''); ?>>P2P</option>

                    </select>

                </div>
                <!-- Company Name -->

                <div class="col-md-3">

                    <label class="form-label fw-semibold">Company <span class="text-danger">*</span></label>

                    <select name="company_id" id="company_id" class="form-select" required>

                        <option value="">Select Company</option>

                        <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <option value="<?php echo e($company->id); ?>" <?php echo e((string) old('company_id', $importRow['company_id'] ?? '') === (string) $company->id ? 'selected' : ''); ?>><?php echo e($company->company_name); ?></option>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </select>

                </div>

                <!-- Client Name -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">Client Name <span class="text-danger">*</span></label>

                    <select name="client_id" id="client_id" class="form-select" required>

                        <option value="">Select Client</option>

                        <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <option value="<?php echo e($client->id); ?>" <?php echo e((string) old('client_id', $importRow['client_id'] ?? '') === (string) $client->id ? 'selected' : ''); ?>><?php echo e($client->business_name ?: $client->client_name); ?></option>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </select>

                </div>

                <!-- Delivery Company Name -->
                <div class="col-md-3">
                    <label for="form-label fw-semibold">Delivery Company Name</label>
                    <input type="text" name="delivery_company_name" class="form-control" value="<?php echo e(old('delivery_company_name', $importRow['delivery_company_name'] ?? '')); ?>">
                </div>

                <!-- Story ID -->
                <div class="col-md-3">
                    <label for="form-label fw-semibold">Location ID</label>
                    <input type="text" name="location_id" class="form-control" value="<?php echo e(old('location_id', $importRow['location_id'] ?? '')); ?>">
                </div>

                <!-- Longitude Name -->
                <div class="col-md-3">
                    <label for="form-label fw-semibold">Longitude </label>
                    <input type="text" name="longitude" class="form-control" value="<?php echo e(old('longitude', $importRow['longitude'] ?? '')); ?>">
                </div>

                <!-- Delivery Company Name -->
                <div class="col-md-3">
                    <label for="form-label fw-semibold">Latitude</label>
                    <input type="text" name="latitude" class="form-control" value="<?php echo e(old('latitude', $importRow['latitude'] ?? '')); ?>">
                </div>

                <!-- pincode -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">Pincode <span class="text-danger">*</span></label>

                    <input type="text" name="pincode" id="pincode" maxlength="6" class="form-control" required value="<?php echo e(old('pincode', $importRow['pincode'] ?? '')); ?>">

           <!-- <button type="button" id="pincodeVerifyBtn" class="btn btn-primary">Verify</button> -->

                </div>
                <!-- Select State -->

                <div class="col-md-3">

                    <label class="form-label fw-semibold">State <span class="text-danger">*</span></label>

                    <?php $stateValue = old('state', $importRow['state'] ?? ''); ?>
                    <select name="state" id="state" class="form-select select2-tags">

                        <option value="" <?php echo e($stateValue === '' ? 'selected' : ''); ?>>Select or Type State</option>

                        <option value="Karnataka" <?php echo e($stateValue === 'Karnataka' ? 'selected' : ''); ?>>Karnataka</option>

                        <option value="Tamil Nadu" <?php echo e($stateValue === 'Tamil Nadu' ? 'selected' : ''); ?>>Tamil Nadu</option>

                        <option value="Telangana" <?php echo e($stateValue === 'Telangana' ? 'selected' : ''); ?>>Telangana</option>

                    </select>

                </div>
                <!-- Select District -->

                <div class="col-md-3">

                    <label class="form-label fw-semibold">District <span class="text-danger">*</span></label>

                   <?php $districtValue = old('district', $importRow['district'] ?? ''); ?>
                   <select name="district" id="district" class="form-select select2-tags">

                        <option value="" <?php echo e($districtValue === '' ? 'selected' : ''); ?>>Select or Type District</option>

                        <option value="Salem" <?php echo e($districtValue === 'Salem' ? 'selected' : ''); ?>>Salem</option>

                        <option value="Dharmapuri" <?php echo e($districtValue === 'Dharmapuri' ? 'selected' : ''); ?>>Dharmapuri</option>

                        <option value="Erode" <?php echo e($districtValue === 'Erode' ? 'selected' : ''); ?>>Erode</option>

                    </select>

                </div>
                <!-- Select Area -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">Area <span class="text-danger">*</span></label>
                    <?php $areaValue = old('area', $importRow['area'] ?? ''); ?>
                      
                    <select name="area" id="post_office" class="form-select select2-tags">

                        <option value="">Select or Type Area</option>

                        <option value="Uthagarai" <?php echo e($areaValue === 'Uthagarai' ? 'selected' : ''); ?>>Uthagarai</option>

                        <option value="Harur" <?php echo e($areaValue === 'Harur' ? 'selected' : ''); ?>>Harur</option>

                        <option value="Kottaiyur" <?php echo e($areaValue === 'Kottaiyur' ? 'selected' : ''); ?>>Kottaiyur</option>
                    </select>

                </div>
                <!-- Address -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">Address <span class="text-danger">*</span></label>

                    <textarea name="address" class="form-control" rows="1" required><?php echo e(old('address', $importRow['address'] ?? '')); ?></textarea>

                </div>
                <!-- SPOC Name -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">SPOC Name <span class="text-danger">*</span></label>

                    <input type="text" name="spoc_name" class="form-control" value="<?php echo e(old('spoc_name', $importRow['spoc_name'] ?? '')); ?>" required>

                </div>
                <!-- SPOC Contact 1-->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">SPOC Contact 1 <span class="text-danger">*</span></label>

                    <input type="text" name="spoc_contact1" class="form-control" value="<?php echo e(old('spoc_contact1', $importRow['spoc_contact1'] ?? '')); ?>" required>

                </div>
                <!-- SPOC Contact2 -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">SPOC Contact 2</label>

                    <input type="text" name="spoc_contact2" class="form-control" value="<?php echo e(old('spoc_contact2', $importRow['spoc_contact2'] ?? '')); ?>">

                </div>
                <!-- SPOC Email -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">SPOC Email</label>

                    <input type="email" name="spoc_email" class="form-control" value="<?php echo e(old('spoc_email', $importRow['spoc_email'] ?? '')); ?>" >

                </div>
                <!-- No Of Links -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">No. of Links <span class="text-danger">*</span></label>
                    <?php $linkValue = old('no_of_links', $importRow['no_of_links'] ?? ''); ?>
                      
                    <select name="no_of_links" class="form-select" required>

                        <option value="" <?php echo e($linkValue === '' ? 'selected' : ''); ?>>Select</option>

                        <option value="1" <?php echo e($linkValue === '1' ? 'selected' : ''); ?>>1</option>

                        <option value="2" <?php echo e($linkValue === '2' ? 'selected' : ''); ?>>2</option>
                        <option value="3" <?php echo e($linkValue === '3' ? 'selected' : ''); ?>>3</option>

                        <option value="4" <?php echo e($linkValue === '4' ? 'selected' : ''); ?>>4</option>

                    </select>

                </div>
                <!-- Vendor Type -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">Vendor Type <span class="text-danger">*</span></label>
                    <?php $vendorTypeValue = old('vendor_type', $importRow['vendor_type'] ?? ''); ?>
                      
                    <select name="vendor_type" class="form-select" required>

                        <option value="" <?php echo e($vendorTypeValue === '' ? 'selected' : ''); ?>>Select</option>

                        <option <?php echo e($vendorTypeValue === 'Same Vendor' ? 'selected' : ''); ?>>Same Vendor</option>

                        <option <?php echo e($vendorTypeValue === 'Different Vendor' ? 'selected' : ''); ?>>Different Vendor</option>
                        <option <?php echo e($vendorTypeValue === 'UBN' ? 'selected' : ''); ?>>UBN</option>

                        <option <?php echo e($vendorTypeValue === 'UBS' ? 'selected' : ''); ?>>UBS</option>

                        <option <?php echo e($vendorTypeValue === 'UBL' ? 'selected' : ''); ?>>UBL</option>

                        <option <?php echo e($vendorTypeValue === 'INF' ? 'selected' : ''); ?>>INF</option>

                    </select>

                </div>

                <!-- Speed -->
                <div class="col-md-3" id="speed_box">
                    <label class="form-label fw-semibold">Speed <span class="text-danger">*</span></label>
                    <input type="text" name="speed" id="speed" placeholder="Mbps or Gbps" class="form-control" value="<?php echo e(old('speed', $importRow['speed'] ?? '')); ?>" required>
                </div>
                <!-- Static IP -->
                <div class="col-md-3" id="static_ip_box">
                    <label class="form-label fw-semibold">Static IP <span class="text-danger">*</span></label>
                    <?php $staticIpValue = old('static_ip', $importRow['static_ip'] ?? ''); ?>
                    <select name="static_ip" id="static_ip" class="form-select" required>
                        <option value="" <?php echo e($staticIpValue === '' ? 'selected' : ''); ?>>Select</option>
                        <option value="Yes" <?php echo e($staticIpValue === 'Yes' ? 'selected' : ''); ?>>Yes</option>
                        <option value="No" <?php echo e($staticIpValue === 'No' ? 'selected' : ''); ?>>No</option>
                    </select>
                </div>
                <!-- Static IP Subnet -->
                <div class="col-md-3" id="static_ip_subnet_box">
                    <label class="form-label fw-semibold">Static IP Subnet</label>
                    <?php $staticIpSubnetValue = old('static_ip_subnet', $importRow['static_ip_subnet'] ?? ''); ?>
                    <select name="static_ip_subnet" id="static_ip_subnet" class="form-select" disabled>
                        <option value="" <?php echo e($staticIpSubnetValue === '' ? 'selected' : ''); ?>>Select Subnet</option>
                        <option value="/32" <?php echo e($staticIpSubnetValue === '/32' ? 'selected' : ''); ?>>/32</option>
                        <option value="/31" <?php echo e($staticIpSubnetValue === '/31' ? 'selected' : ''); ?>>/31</option>
                        <option value="/30" <?php echo e($staticIpSubnetValue === '/30' ? 'selected' : ''); ?>>/30</option>
                        <option value="/29" <?php echo e($staticIpSubnetValue === '/29' ? 'selected' : ''); ?>>/29</option>
                        <option value="/28" <?php echo e($staticIpSubnetValue === '/28' ? 'selected' : ''); ?>>/28</option>
                        <option value="/27" <?php echo e($staticIpSubnetValue === '/27' ? 'selected' : ''); ?>>/27</option>
                        <option value="/26" <?php echo e($staticIpSubnetValue === '/26' ? 'selected' : ''); ?>>/26</option>
                        <option value="/25" <?php echo e($staticIpSubnetValue === '/25' ? 'selected' : ''); ?>>/25</option>
                        <option value="/24" <?php echo e($staticIpSubnetValue === '/24' ? 'selected' : ''); ?>>/24</option>
                    </select>
                    <small class="text-muted">Select subnet only if Static IP is Yes</small>
                </div>
                <div class="col-md-3" id="static_ip_duration_box">
                    <label class="form-label fw-semibold">Static IP Duration</label>
                    <?php $staticIpDurationValue = old('static_ip_duration', $importRow['static_ip_duration'] ?? ''); ?>
                    <select name="static_ip_duration" id="static_ip_duration" class="form-select" <?php echo e($staticIpValue === 'Yes' ? '' : 'disabled'); ?> <?php echo e($staticIpValue === 'Yes' ? 'required' : ''); ?>>
                        <option value="" <?php echo e($staticIpDurationValue === '' ? 'selected' : ''); ?>>Select Duration</option>
                        <option value="Monthly" <?php echo e($staticIpDurationValue === 'Monthly' ? 'selected' : ''); ?>>Monthly</option>
                        <option value="Yearly" <?php echo e($staticIpDurationValue === 'Yearly' ? 'selected' : ''); ?>>Yearly</option>
                    </select>
                    <small class="text-muted">Applicable only when Static IP is Yes</small>
                </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const linkType = document.getElementById('link_type');
                                    const existingBox = document.getElementById('existing_circuit_box');
                                    const newLinkFields = document.getElementById('new_link_fields');
                                    const circuitSelect = document.getElementById('circuit_id');
                                    // Show/hide fields based on link type
                                    linkType.addEventListener('change', function() {
                                        if (this.value === 'existing') {
                                            existingBox.style.display = '';
                                            newLinkFields.style.display = '';
                                        } else if (this.value === 'new') {
                                            existingBox.style.display = 'none';
                                            newLinkFields.style.display = '';
                                            // Clear circuit selection
                                            if (circuitSelect) circuitSelect.value = '';
                                        } else {
                                            existingBox.style.display = 'none';
                                            newLinkFields.style.display = '';
                                        }
                                    });

                                    
                                });

                                // 


                               
$(document).ready(function () {

    $('#circuit_id').on('change', function () {
        const circuitId = $(this).val();

        console.log('Circuit changed:', circuitId);

        if (!circuitId) return;

        $.ajax({
            url: `/feasibility/by-circuit/${circuitId}`,
            type: 'GET',
            success: function (res) {
                console.log('Response:', res);

                if (res.success) {
                    const f = res.feasibility;

                    setSelectValue(document.getElementById('type_of_service'),f.type_of_service);

                    setSelectValue(document.getElementById('company_id'), String(f.company_id));
                    setSelectValue(document.getElementById('client_id'), String(f.client_id));

                    $('input[name="delivery_company_name"]').val(f.delivery_company_name);
                    $('input[name="location_id"]').val(f.location_id);
                    $('input[name="longitude"]').val(f.longitude);
                    $('input[name="latitude"]').val(f.latitude);

                    $('input[name="pincode"]').val(f.pincode);
                   setSelectValue(document.getElementById('state'), f.state);
                   setSelectValue(document.getElementById('district'), f.district);
                   setSelectValue(document.getElementById('post_office'), f.area);

                    $('textarea[name="address"]').val(f.address);
                    $('input[name="spoc_name"]').val(f.spoc_name);
                    $('input[name="spoc_contact1"]').val(f.spoc_contact1);
                    $('input[name="spoc_contact2"]').val(f.spoc_contact2);
                    $('input[name="spoc_email"]').val(f.spoc_email);

                    setSelectValue(document.querySelector('select[name="vendor_type"]'),f.vendor_type);
                    $('input[name="speed"]').val(f.speed);
                    $('select[name="no_of_links"]').val(f.no_of_links).trigger('change');
                    setSelectValue(document.getElementById('static_ip'), f.static_ip);  
                    setSelectValue(document.getElementById('static_ip_subnet'), f.static_ip_subnet);
                    setSelectValue(document.getElementById('static_ip_duration'), f.static_ip_duration);
                    const hardwareSelect = document.getElementById('hardware_required');

                   setSelectValue(hardwareSelect, f.hardware_required ? '1' : '0');

                    // 🔥 FORCE change event so UI reacts
                    hardwareSelect.dispatchEvent(new Event('change'));

                    const firstHardwareRow = document.querySelector('.hardware_row');

if (f.hardware_details) {
    let hardwares = [];

    try {
        hardwares = JSON.parse(f.hardware_details);
    } catch (e) {
        console.error(e);
    }

    if (hardwares.length > 0) {
        setSelectValue(hardwareSelect, '1');
        hardwareSelect.dispatchEvent(new Event('change'));

        const hw = hardwares[0];
        const row = document.querySelector('.hardware_row');

        const makeSelect  = row.querySelector('select[name="make_type_id[]"]');
        const modelSelect = row.querySelector('select[name="model_id[]"]');

        // ⏳ WAIT FOR MAKE OPTIONS
        setTimeout(() => {
            setSelectValue(makeSelect, String(hw.make_type_id));
            $(makeSelect).trigger('change');

            // ⏳ WAIT FOR MODEL OPTIONS (after make change)
            setTimeout(() => {
                setSelectValue(modelSelect, String(hw.model_id));
                $(modelSelect).trigger('change');
            }, 500);

        }, 500);
    }
}

                    $('input[name="expected_delivery"]').val(f.expected_delivery);
                    $('input[name="expected_activation"]').val(f.expected_activation);
                } else {
                    alert(res.message || 'No feasibility found');
                }
            },
            error: function (err) {
                console.error('AJAX error:', err);
                alert('Failed to fetch feasibility');
            }
        });
    });

});
                            </script>
                <!-- Expected Delivery -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">Expected Delivery <span class="text-danger">*</span></label>

                    <input type="date" name="expected_delivery" class="form-control" value="<?php echo e(old('expected_delivery', $importRow['expected_delivery'] ?? date('Y-m-d'))); ?>" required>

                </div>
                <!-- Expected Activation -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">Expected Activation <span class="text-danger">*</span></label>

                    <input type="date" name="expected_activation" class="form-control" value="<?php echo e(old('expected_activation', $importRow['expected_activation'] ?? date('Y-m-d'))); ?>" required>

                </div>
                <!-- Hardware Required -->
                <div class="col-md-3">

                    <label class="form-label fw-semibold">Hardware Required <span class="text-danger">*</span></label>
                    <?php $hardwareRequiredValue = old('hardware_required', $importRow['hardware_required'] ?? ''); ?>
                    <select name="hardware_required" id="hardware_required" class="form-select" required>

                        <option value="" <?php echo e($hardwareRequiredValue === '' ? 'selected' : ''); ?>>Select</option>

                        <option value="1" <?php echo e($hardwareRequiredValue === '1' ? 'selected' : ''); ?>>Yes</option>

                        <option value="0" <?php echo e($hardwareRequiredValue === '0' ? 'selected' : ''); ?>>No</option>

                    </select>

                </div>
                
<!-- container for all hardware rows -->
<div id="hardware_container">
    <div class="row hardware_row" style="display:none;">
        <div class="col-md-3">
            <label>Make</label>
            <select name="make_type_id[]" class="form-control" required>
                <option value="">Select Make</option>
                <?php $__currentLoopData = $makes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($m->id); ?>"><?php echo e($m->make_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <div class="flex-grow-1">
                <label>Model</label>
                <select name="model_id[]" class="form-control" required>
                    <option value="">Select Model</option>
                    <?php $__currentLoopData = $models; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($m->id); ?>"><?php echo e($m->model_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="button" class="btn btn-danger btn-sm ms-2 mb-1 remove-hardware" style="height:38px;">X</button>
        </div>
    </div>
</div>

<div class="col-md-3 mt-3" id="add_btn_div" style="display:none;">
    <button type="button" id="add_hardware_btn" class="btn btn-primary btn-sm">Add</button>
</div>
               

                    

            <input type="hidden" name="status" value="Active">





            </div>



            <div class="mt-4 text-end">

                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>

                <!-- <a href="<?php echo e(route('feasibility.index')); ?>" class="btn btn-secondary">Cancel</a> -->

            </div>

        </form>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>



<script>

// document.getElementById('hardware_required').addEventListener('change', function() {

//     document.getElementById('hardware_name_div').style.display = this.value == '1' ? 'block' : 'none';
//     document.getElementById('hardware_make_div').style.display = this.value == '1' ? 'block' : 'none';

// });
const hardwareRequiredSelect = document.getElementById('hardware_required');
const hardwareRow = document.querySelector('.hardware_row');
const addHardwareBtnDiv = document.getElementById('add_btn_div');

function setHardwareRowState(row, enabled) {
    const selects = row.querySelectorAll('select');
    selects.forEach(select => {
        select.disabled = !enabled;
        if (!enabled) {
            select.value = '';
        }
    });
    row.style.display = enabled ? 'flex' : 'none';
}

setHardwareRowState(hardwareRow, false);

hardwareRequiredSelect.addEventListener('change', function () {
    const isRequired = this.value === '1';
    setHardwareRowState(hardwareRow, isRequired);
    addHardwareBtnDiv.style.display = isRequired ? 'block' : 'none';
});

document.getElementById('add_hardware_btn').addEventListener('click', function () {
    let originalRow = document.querySelector('.hardware_row');
    let newRow = originalRow.cloneNode(true);
    newRow.querySelectorAll('input').forEach(function (input) {
        input.value = "";
    });
    newRow.style.display = 'flex';
    newRow.querySelectorAll('select').forEach(select => {
        select.disabled = false;
        select.value = '';
    });
    // Attach remove button event
    let removeBtn = newRow.querySelector('.remove-hardware');
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            let allRows = document.querySelectorAll('.hardware_row');
            if (allRows.length > 1) {
                newRow.remove();
            } else {
                // Only one row: clear values
                newRow.querySelectorAll('input, select').forEach(el => el.value = '');
            }
        });
    }
    document.getElementById('hardware_container').appendChild(newRow);
});

// Attach remove event to initial row
let initialRemoveBtn = document.querySelector('.hardware_row .remove-hardware');
if (initialRemoveBtn) {
    initialRemoveBtn.addEventListener('click', function () {
        let allRows = document.querySelectorAll('.hardware_row');
        let row = this.closest('.hardware_row');
        if (allRows.length > 1) {
            row.remove();
        } else {
            row.querySelectorAll('input, select').forEach(el => el.value = '');
        }
    });
}



// Helper function to set value in select dropdown, creating option if needed

function setSelectValue(selectElement, value) {

  console.log('setSelectValue called with:', selectElement.id, 'value:', value);

  

  if (!value || value === '') {

    selectElement.value = '';

    // If it's a Select2 element, trigger change

    if (typeof $ !== 'undefined' && typeof $(selectElement).select2 === 'function') {

      $(selectElement).val('').trigger('change');

    }

    return;

  }

  

  // Check if option already exists

  let optionExists = false;

  for (let option of selectElement.options) {

    if (option.value === value) {

      optionExists = true;

      break;

    }

  }

  // If option doesn't exist, create it

  if (!optionExists) {

    const newOption = document.createElement('option');

    newOption.value = value;

    newOption.text = value;

    selectElement.appendChild(newOption);

    console.log('Created new option:', value, 'for', selectElement.id);

  }

  

  // Set the value

  selectElement.value = value;

  console.log('Set native value for', selectElement.id, 'to:', value);

  

  // Handle Select2 if available

  if (typeof $ !== 'undefined') {

    try {

      const $element = $(selectElement);

      // Check if Select2 is initialized

      if (typeof $element.select2 === 'function' && $element.hasClass('select2-hidden-accessible')) {

        $element.val(value).trigger('change');

        console.log('Triggered Select2 change for', selectElement.id);

      }

    } catch (error) {

      console.log('Select2 not available or error:', error);

    }

  }

  

  console.log('Final value for', selectElement.id, ':', selectElement.value);

}



// Pincode lookup function

function lookupPincode() {

  const pincodeField = document.getElementById('pincode');

  const p = pincodeField.value.trim();

  

  // Only proceed if we have exactly 6 digits

  if (!/^\d{6}$/.test(p)) return;

  

  // Get field references

  const stateField = document.getElementById('state');

  const districtField = document.getElementById('district');

  const areaField = document.getElementById('post_office');

  

  // Store original values in case of error

  const originalState = stateField.value;

  const originalDistrict = districtField.value;

  const originalArea = areaField.value;

  

  // Show loading state

  setSelectValue(stateField, 'Loading...');

  setSelectValue(districtField, 'Loading...');

  setSelectValue(areaField, 'Loading...');

  

  console.log('🔍 Looking up pincode:', p);

  

  // Make API call

  axios.post('/api/pincode/lookup', { pincode: p })

    .then(r => {

      const d = r.data;

      console.log('✅ Pincode lookup successful:', d);

      console.log('State field element:', stateField);

      console.log('District field element:', districtField);

      console.log('Area field element:', areaField);

      

      // Update fields with fetched data

      console.log('Setting state to:', d.state);

      setSelectValue(stateField, d.state || '');

      

      console.log('Setting district to:', d.district);

      setSelectValue(districtField, d.district || '');

      

      console.log('Setting area to:', d.post_office);

      setSelectValue(areaField, d.post_office || '');

      

      // Show success message briefly

      const notification = document.createElement('div');

      notification.style.cssText = `

        position: fixed; top: 20px; right: 20px; 

        background: #d4edda; color: #155724; 

        padding: 10px 15px; border-radius: 5px; 

        border: 1px solid #c3e6cb; z-index: 9999;

        font-size: 14px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);

      `;

      notification.innerHTML = `✅ Location found: ${d.state}, ${d.district}`;

      document.body.appendChild(notification);

      

      // Remove notification after 3 seconds

      setTimeout(() => {

        if (notification.parentNode) {

          notification.parentNode.removeChild(notification);

        }

      }, 3000);

    })

    .catch(err => {

      console.error('❌ Pincode lookup failed:', err);

      

      // Restore original values

      setSelectValue(stateField, originalState);

      setSelectValue(districtField, originalDistrict);

      setSelectValue(areaField, originalArea);

      

      // Show error message

      let errorMessage = 'Unable to fetch pincode details. Please try again or enter manually.';

      if (err.response && err.response.status === 404) {

        errorMessage = 'Pincode not found. Please check the pincode and try again.';

      } else if (err.response && err.response.status === 422) {

        errorMessage = 'Invalid pincode format. Please enter a 6-digit pincode.';

      }

      

      // Show error notification

      const errorNotification = document.createElement('div');

      errorNotification.style.cssText = `

        position: fixed; top: 20px; right: 20px; 

        background: #f8d7da; color: #721c24; 

        padding: 10px 15px; border-radius: 5px; 

        border: 1px solid #f5c6cb; z-index: 9999;

        font-size: 14px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);

      `;

      errorNotification.innerHTML = `❌ ${errorMessage}`;

      document.body.appendChild(errorNotification);

      

      // Remove error notification after 5 seconds

      setTimeout(() => {

        if (errorNotification.parentNode) {

          errorNotification.parentNode.removeChild(errorNotification);

        }

      }, 5000);

    });

}
// Add multiple event listeners for better responsiveness

const pincodeInput = document.getElementById('pincode');

// Trigger on blur (when user clicks outside the field)

pincodeInput.addEventListener('blur', lookupPincode);

// Trigger on Enter key press

document.addEventListener('DOMContentLoaded', function () {
    const staticIpSelect    = document.getElementById('static_ip');
    const subnetSelect      = document.getElementById('static_ip_subnet');
    const durationSelect    = document.getElementById('static_ip_duration');
    const typeServiceSelect = document.getElementById('type_of_service');

    function updateStaticIpDependentFields() {
        if (!staticIpSelect) return;
        const isStaticEnabled = staticIpSelect.value === 'Yes';
        [subnetSelect, durationSelect].forEach(select => {
            if (!select) return;
            select.disabled = !isStaticEnabled;
            select.required = isStaticEnabled;
            if (!isStaticEnabled) {
                select.value = '';
            }
        });
    }

    function enforceStaticIpForILL() {
        if (!typeServiceSelect || !staticIpSelect) return;

        if (typeServiceSelect.value === 'ILL' && staticIpSelect.value !== 'Yes') {
            staticIpSelect.value = 'Yes';
        }
        updateStaticIpDependentFields();
    }

    if (staticIpSelect) {
        staticIpSelect.addEventListener('change', updateStaticIpDependentFields);
    }
    if (typeServiceSelect) {
        typeServiceSelect.addEventListener('change', enforceStaticIpForILL);
    }

    // Initial load (IMPORTANT for edit/import)
    updateStaticIpDependentFields();
    enforceStaticIpForILL();
});
// document.getElementById('importexcel').

// document.getElementById('importexcel').

function excelImport() {
    // Excel import logic removed; reintroduce carefully if required.
}


// if the user select the circuit id from the dropdown, autofill the fields
// $(document).ready(function() {
//     $('#deliverable_id').on('change', function() {
//         let selected = $(this).find(':selected');
//         $('[name="type_of_service"]').val(selected.data('type_of_service') || '');
//         $('[name="company_id"]').val(selected.data('company_id') || '');
//         $('[name="client_id"]').val(selected.data('client_id') || '');
//         $('[name="delivery_company_name"]').val(selected.data('delivery_company_name') || '');
//         $('[name="location_id"]').val(selected.data('location_id') || '');
//         $('[name="longitude"]').val(selected.data('longitude') || '');
//         $('[name="latitude"]').val(selected.data('latideliverable_idtude') || '');
//         $('[name="pincode"]').val(selected.data('pincode') || '');
//         $('[name="state"]').val(selected.data('state') || '');
//         $('[name="district"]').val(selected.data('district') || '');
//         $('[name="area"]').val(selected.data('area') || '');
//         $('[name="address"]').val(selected.data('address') || '');
//         $('[name="spoc_name"]').val(selected.data('spoc_name') || '');
//         $('[name="spoc_contact1"]').val(selected.data('spoc_contact1') || '');
//         $('[name="spoc_contact2"]').val(selected.data('spoc_contact2') || '');
//         $('[name="spoc_email"]').val(selected.data('spoc_email') || '');
//         $('[name="speed"]').val(selected.data('speed') || '');
//         $('[name="static_ip"]').val(selected.data('static_ip') || '');
//         $('[name="static_ip_subnet"]').val(selected.data('static_ip_subnet') || '');
//         $('[name="expected_delivery"]').val(selected.data('expected_delivery') || '');
//         $('[name="expected_activation"]').val(selected.data('expected_activation') || '');
//         $('[name="hardware_required"]').val(selected.data('hardware_required') || '');


//     });
//     $('#deliverable_id').trigger('change');

// });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/feasibility/create.blade.php ENDPATH**/ ?>