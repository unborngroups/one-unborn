



<?php $__env->startSection('content'); ?>

<div class="container-fluid py-4">

    <h4 class="text-info fw-bold mb-3">View Feasibility Details</h4>



    <div class="card shadow border-0 p-4">



        

        <div class="row g-3">



            

            <div class="col-md-3">

                <label class="form-label fw-semibold">Feasibility Request ID</label>

                <p class="form-control-plaintext">

                    <span class="badge bg-info fs-6"><?php echo e($record->feasibility->feasibility_request_id ?? 'Not Generated'); ?></span>

                </p>

            </div>



            

            <div class="col-md-3">

                <label class="form-label fw-semibold">Type of Service</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->type_of_service); ?></p>

            </div>

             

            <div class="col-md-3">

                <label class="form-label fw-semibold">Company Name</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->company->company_name ?? 'N/A'); ?></p>

            </div>

            

            <div class="col-md-3">

                <label class="form-label fw-semibold">Client Name</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->client->client_name ?? 'N/A'); ?></p>

            </div>
            
            <div class="col-md-3">  

                <label class="form-label fw-semibold">Delivery Company Name</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->delivery_company_name ?? 'N/A'); ?></p>
            </div>
            
            <div class="col-md-3">

                <label class="form-label fw-semibold">Location ID</label>
                <p class="form-control-plaintext"><?php echo e($record->feasibility->location_id ?? 'N/A'); ?></p>
            </div>
            
            <div class="col-md-3">

                <label class="form-label fw-semibold">Longitude</label>
                <p class="form-control-plaintext"><?php echo e($record->feasibility->longitude ?? 'N/A'); ?></p>    
            </div>
            
            <div class="col-md-3">

                <label class="form-label fw-semibold">Latitude</label>
                <p class="form-control-plaintext"><?php echo e($record->feasibility->latitude ?? 'N/A'); ?></p>
            </div>


            

            <div class="col-md-3">

                <label class="form-label fw-semibold">Pincode</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->pincode); ?></p>

            </div>



            

            <div class="col-md-3">

                <label class="form-label fw-semibold">State</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->state); ?></p>

            </div>



            

            <div class="col-md-3">

                <label class="form-label fw-semibold">District</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->district); ?></p>

            </div>



            

            <div class="col-md-3">

                <label class="form-label fw-semibold">Area</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->area); ?></p>

            </div>



            

            <div class="col-md-3">

                <label class="form-label fw-semibold">Address</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->address); ?></p>

            </div>



            

            <div class="col-md-3">

                <label class="form-label fw-semibold">SPOC Name</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->spoc_name); ?></p>

            </div>



            

            <div class="col-md-3">

                <label class="form-label fw-semibold">SPOC Contact 1</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->spoc_contact1); ?></p>

            </div>



            

            <div class="col-md-3">

                <label class="form-label fw-semibold">SPOC Contact 2</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->spoc_contact2); ?></p>

            </div>



            

            <div class="col-md-3">

                <label class="form-label fw-semibold">SPOC Email</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->spoc_email); ?></p>

            </div>



            

            <div class="col-md-3">

                <label class="form-label fw-semibold">No. of Links</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->no_of_links); ?></p>

            </div>



            

            <div class="col-md-3">

                <label class="form-label fw-semibold">Vendor Type</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->vendor_type); ?></p>

            </div>



            

            <div class="col-md-3">

                <label class="form-label fw-semibold">Speed</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->speed); ?></p>

            </div>



            

            <div class="col-md-3">

                <label class="form-label fw-semibold">Static IP</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->static_ip); ?></p>

            </div>

            

            <div class="col-md-3">

                <label class="form-label fw-semibold">Static IP Subnet</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->static_ip_subnet ?? 'N/A'); ?></p>

            </div>



            

            <div class="col-md-3">

                <label class="form-label fw-semibold">Expected Delivery</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->expected_delivery); ?></p>

            </div>



            

            <div class="col-md-3">

                <label class="form-label fw-semibold">Expected Activation</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->expected_activation); ?></p>

            </div>



            

            <div class="col-md-3">

                <label class="form-label fw-semibold">Hardware Required</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->hardware_required ? 'Yes' : 'No'); ?></p>

            </div>

<?php
    $hardwareDetails = $record->feasibility->hardware_details;
    if (is_string($hardwareDetails)) {
        $hardwareDetails = json_decode($hardwareDetails, true);
    }
?>
<?php if(!empty($hardwareDetails) && is_array($hardwareDetails)): ?>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Hardware Model Name</label>
        <?php $__currentLoopData = $hardwareDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
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
            ?>
            <p class="mb-1">
                Make:
                <?php if(is_array($makeName)): ?>
                    <?php echo e(collect($makeName)->map(function($id) { $obj = \App\Models\MakeType::find($id); return $obj ? $obj->make_name : $id; })->implode(', ')); ?>

                <?php else: ?>
                    <?php echo e(is_object($makeName) ? '[object]' : ($makeName ?? 'N/A')); ?>

                <?php endif; ?>
                <br>
                Model:
                <?php if(is_array($modelName)): ?>
                        <?php echo e(collect($modelName)->map(function($id) { $obj = \App\Models\ModelType::find($id); return $obj ? $obj->model_name : $id; })->implode(', ')); ?>

                <?php else: ?>
                    <?php echo e(is_object($modelName) ? '[object]' : ($modelName ?? 'N/A')); ?>

                <?php endif; ?>
            </p>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php else: ?>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Hardware Model Name</label>
        <p class="form-control-plaintext">N/A</p>
    </div>
<?php endif; ?>


            

            <div class="col-md-3">

                <label class="form-label fw-semibold">Feasibility Status</label>

                <p class="form-control-plaintext">

                    <span class="badge 

                        <?php if($record->status == 'Open'): ?> bg-primary

                        <?php elseif($record->status == 'InProgress'): ?> bg-warning text-dark

                        <?php elseif($record->status == 'Closed'): ?> bg-success

                        <?php endif; ?>">

                        <?php echo e($record->status); ?>


                    </span>

                </p>

            </div>

        </div>

        
<?php if(
    $record->vendor1_name || $record->vendor1_arc || $record->vendor1_otc || $record->vendor1_static_ip_cost || $record->vendor1_delivery_timeline ||
    $record->vendor2_name || $record->vendor2_arc || $record->vendor2_otc || $record->vendor2_static_ip_cost || $record->vendor2_delivery_timeline ||
    $record->vendor3_name || $record->vendor3_arc || $record->vendor3_otc || $record->vendor3_static_ip_cost || $record->vendor3_delivery_timeline ||
    $record->vendor4_name || $record->vendor4_arc || $record->vendor4_otc || $record->vendor4_static_ip_cost || $record->vendor4_delivery_timeline
): ?>

<hr class="my-4">
<h5 class="text-primary fw-bold mb-3">Vendor Information</h5>

<div class="row g-3">

    <?php for($i = 1; $i <= 4; $i++): ?>

        <?php
            $vName = 'vendor'.$i.'_name';
            $vArc  = 'vendor'.$i.'_arc';
            $vOtc  = 'vendor'.$i.'_otc';
            $vIp   = 'vendor'.$i.'_static_ip_cost';
            $vTime = 'vendor'.$i.'_delivery_timeline';
            $vremark = 'vendor'.$i.'_remarks';
        ?>

        
    <?php if($record->$vName !== null || $record->$vArc !== null || $record->$vOtc !== null || $record->$vIp !== null || $record->$vTime !== null || $record->$vremark !== null): ?>


        <div class="col-md-12">
            <div class="card bg-light">
                <div class="card-body">

                    <h6 class="fw-bold text-secondary mb-3">Vendor <?php echo e($i); ?></h6>

                    <div class="row">

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Name</label>
                            <p class="form-control-plaintext">
                                <?php echo e(($record->$vName == 'Self' || $record->$vName == 0) ? 'Self' : $record->$vName); ?>

                            </p>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">ARC</label>
                            <p class="form-control-plaintext"><?php echo e($record->$vArc ?? 'N/A'); ?></p>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">OTC</label>
                            <p class="form-control-plaintext"><?php echo e($record->$vOtc ?? 'N/A'); ?></p>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Static IP Cost</label>
                            <p class="form-control-plaintext"><?php echo e($record->$vIp ?? 'N/A'); ?></p>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Delivery Timeline</label>
                            <p class="form-control-plaintext"><?php echo e($record->$vTime ?? 'N/A'); ?></p>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Remarks</label>
                            <p class="form-control-plaintext"><?php echo e($record->$vremark ?? 'N/A'); ?></p>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <?php endif; ?>

    <?php endfor; ?>

</div>

<?php endif; ?>

        

        <div class="mt-4 text-end">

            <?php if($record->status == 'Open'): ?>

                <a href="<?php echo e(route('sm.feasibility.open')); ?>" class="btn btn-secondary">

                    <i class="bi bi-arrow-left"></i> Back to Open

                </a>

            <?php elseif($record->status == 'InProgress'): ?>

                <a href="<?php echo e(route('sm.feasibility.inprogress')); ?>" class="btn btn-secondary">

                    <i class="bi bi-arrow-left"></i> Back to In Progress

                </a>

            <?php else: ?>

                <a href="<?php echo e(route('sm.feasibility.closed')); ?>" class="btn btn-secondary">

                    <i class="bi bi-arrow-left"></i> Back to Closed

                </a>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/sm/feasibility/view.blade.php ENDPATH**/ ?>