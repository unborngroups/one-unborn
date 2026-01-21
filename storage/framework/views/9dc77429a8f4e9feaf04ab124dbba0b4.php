

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
	<h4 class="text-primary fw-bold mb-3">View Termination Request</h4>
	<div class="card shadow border-0 p-4">
		
		<div class="row g-3">
			<div class="col-md-4">
                <label>Circuit ID</label>
                <input type="text" class="form-control" value="<?php echo e($termination->circuit_id); ?>" readonly>
           </div>
			<div class="col-md-4">
                <label>Company Name</label>
                <input type="text" class="form-control" value="<?php echo e($termination->company_name); ?>" readonly>
            </div>
			<div class="col-md-4">
                <label>Address</label>
                <input type="text" class="form-control" value="<?php echo e($termination->address); ?>" readonly>
            </div>
			<div class="col-md-4">
                <label>Bandwidth</label>
                <input type="text" class="form-control" value="<?php echo e($termination->bandwidth); ?>" readonly>
            </div>
			<div class="col-md-4">
                <label>Asset Make</label>
                <input type="text" class="form-control" value="<?php echo e($termination->asset_make); ?>" readonly>
            </div>
			<div class="col-md-4">
                <label>Asset MAC</label>
                <input type="text" class="form-control" value="<?php echo e($termination->asset_mac); ?>" readonly>
            </div>
			<div class="col-md-4">
                <label>Asset Serial</label>
                <input type="text" class="form-control" value="<?php echo e($termination->asset_serial); ?>" readonly>
            </div>
			<div class="col-md-4">
                <label>Date of Activation</label>
                <input type="text" class="form-control" value="<?php echo e($termination->date_of_activation); ?>" readonly>
            </div>
			<div class="col-md-4">
                <label>Date of Delivered</label>
                <input type="text" class="form-control" value="<?php echo e($termination->date_of_delivered); ?>" readonly>
            </div>
			<div class="col-md-4">
                <label>Date of Last Renewal</label>
                <input type="text" class="form-control" value="<?php echo e($termination->date_of_last_renewal); ?>" readonly>
            </div>
			<div class="col-md-4">
                <label>Date of Expiry</label>
                <input type="text" class="form-control" value="<?php echo e($termination->date_of_expiry); ?>" readonly>
            </div>
			<div class="col-md-4">
                <label>Termination Request Date</label>
                <input type="text" class="form-control" value="<?php echo e($termination->termination_request_date); ?>" readonly>
            </div>
			<div class="col-md-4">
                <label>Termination Requested By</label>
                <input type="text" class="form-control" value="<?php echo e($termination->termination_requested_by); ?>" readonly>
            </div>
			<div class="col-md-4">
                <label>Termination Request Document</label>
				<a href="<?php echo e(asset($termination->termination_request_document)); ?>" target="_blank"> View Document
</a>

			</div>
			<div class="col-md-4"><label>Termination Date</label><input type="text" class="form-control" value="<?php echo e($termination->termination_date); ?>" readonly></div>
			<div class="col-md-4"><label>Status</label><input type="text" class="form-control" value="<?php echo e($termination->status); ?>" readonly></div>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\operations\termination\view.blade.php ENDPATH**/ ?>