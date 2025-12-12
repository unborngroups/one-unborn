



<?php $__env->startSection('content'); ?>

<div class="container-fluid py-4">

    <h4 class="text-primary fw-bold mb-3">View Feasibility Details</h4>



    <div class="card shadow border-0 p-4">

        

        <div class="row g-3">



            

            <div class="col-md-4">

                <label class="form-label fw-semibold">Feasibility Request ID</label>

                <p class="form-control-plaintext">

                    <span class="badge bg-primary fs-6"><?php echo e($record->feasibility->feasibility_request_id ?? 'Not Generated'); ?></span>

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


<?php if(!empty($record->feasibility->hardware_details)): ?>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Hardware Model Name</label>
        <?php $__currentLoopData = json_decode($record->feasibility->hardware_details, true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <p class="mb-1">
                Make: <?php echo e($item['make']); ?> <br>
                Model: <?php echo e($item['model']); ?>

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



        

        <div class="mt-4 text-end">

            <?php if($record->status == 'Open'): ?>

                <a href="<?php echo e(route('operations.feasibility.open')); ?>" class="btn btn-secondary">

                    <i class="bi bi-arrow-left"></i> Back to Open

                </a>

            <?php elseif($record->status == 'InProgress'): ?>

                <a href="<?php echo e(route('operations.feasibility.inprogress')); ?>" class="btn btn-secondary">

                    <i class="bi bi-arrow-left"></i> Back to In Progress

                </a>

            <?php else: ?>

                <a href="<?php echo e(route('operations.feasibility.closed')); ?>" class="btn btn-secondary">

                    <i class="bi bi-arrow-left"></i> Back to Closed

                </a>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/operations/feasibility/view.blade.php ENDPATH**/ ?>