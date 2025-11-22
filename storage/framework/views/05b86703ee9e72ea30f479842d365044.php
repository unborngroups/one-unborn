



<?php $__env->startSection('content'); ?>

<div class="container-fluid py-4">

    <h4 class="text-info fw-bold mb-3">View Feasibility Details</h4>



    <div class="card shadow border-0 p-4">



        

        <div class="row g-3">



            

            <div class="col-md-4">

                <label class="form-label fw-semibold">Feasibility Request ID</label>

                <p class="form-control-plaintext">

                    <span class="badge bg-info fs-6"><?php echo e($record->feasibility->feasibility_request_id ?? 'Not Generated'); ?></span>

                </p>

            </div>



            

            <div class="col-md-4">

                <label class="form-label fw-semibold">Type of Service</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->type_of_service); ?></p>

            </div>



            

            <div class="col-md-4">

                <label class="form-label fw-semibold">Client Name</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->client->client_name ?? 'N/A'); ?></p>

            </div>



            

            <div class="col-md-4">

                <label class="form-label fw-semibold">Pincode</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->pincode); ?></p>

            </div>



            

            <div class="col-md-4">

                <label class="form-label fw-semibold">State</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->state); ?></p>

            </div>



            

            <div class="col-md-4">

                <label class="form-label fw-semibold">District</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->district); ?></p>

            </div>



            

            <div class="col-md-4">

                <label class="form-label fw-semibold">Area</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->area); ?></p>

            </div>



            

            <div class="col-md-6">

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



            

            <?php if($record->feasibility->hardware_required): ?>

            <div class="col-md-3">

                <label class="form-label fw-semibold">Hardware Model Name</label>

                <p class="form-control-plaintext"><?php echo e($record->feasibility->hardware_model_name); ?></p>

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

        

       
<?php if($record->vendor1_name || $record->vendor2_name || $record->vendor3_name || $record->vendor4_name): ?>

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
        ?>

        
    <?php if($record->$vName !== null || $record->$vArc !== null || $record->$vOtc !== null || $record->$vIp !== null || $record->$vTime !== null): ?>


        <div class="col-md-12">
            <div class="card bg-light">
                <div class="card-body">

                    <h6 class="fw-bold text-secondary mb-3">Vendor <?php echo e($i); ?></h6>

                    <div class="row">

                        <div class="col-md-3">
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

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Delivery Timeline</label>
                            <p class="form-control-plaintext"><?php echo e($record->$vTime ?? 'N/A'); ?></p>
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
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\wlcome\multipleuserpage\resources\views/sm/feasibility/view.blade.php ENDPATH**/ ?>