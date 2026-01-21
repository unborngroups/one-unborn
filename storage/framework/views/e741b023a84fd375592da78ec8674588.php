

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
	<h4 class="text-primary fw-bold mb-3">Edit Termination Request</h4>
	<div class="card shadow border-0 p-4">
		<form action="<?php echo e(route('termination.update', $termination->id)); ?>" method="POST" enctype="multipart/form-data">
			<?php echo csrf_field(); ?>
			<?php echo method_field('PUT'); ?>
			<div class="row g-3">
				
                    <div class="col-md-4">
                    <label>Circuit ID</label>
                    <input type="text" class="form-control" value="<?php echo e($termination->circuit_id); ?>" readonly></div>
			
                    <div class="col-md-4">
                    <label>Company Name</label>
                    <input type="text" name="company_name" class="form-control" value="<?php echo e($termination->company_name); ?>" required></div>
				
                    <div class="col-md-4">
                    <label>Address</label>
                    <input type="text" name="address" class="form-control" value="<?php echo e($termination->address); ?>" required></div>
				
                    <div class="col-md-4">
                    <label>Bandwidth</label>
                    <input type="text" name="bandwidth" class="form-control" value="<?php echo e($termination->bandwidth); ?>"></div>
				
                    <div class="col-md-4">
                    <label>Asset Make</label>
                    <input type="text" name="asset_make" class="form-control" value="<?php echo e($termination->asset_make); ?>"></div>
				
                    <div class="col-md-4">
                    <label>Asset MAC</label>
                    <input type="text" name="asset_mac" class="form-control" value="<?php echo e($termination->asset_mac); ?>"></div>
				
                    <div class="col-md-4">
                    <label>Asset Serial</label>
                    <input type="text" name="asset_serial" class="form-control" value="<?php echo e($termination->asset_serial); ?>"></div>
				
                    <div class="col-md-4">
                    <label>Date of Activation</label>
                    <input type="date" name="date_of_activation" class="form-control" value="<?php echo e($termination->date_of_activation); ?>"></div>
				
                    <div class="col-md-4">
                    <label>Date of Delivered</label>
                    <input type="date" name="date_of_delivered" class="form-control" value="<?php echo e($termination->date_of_delivered); ?>"></div>
				
                    <div class="col-md-4">
                    <label>Date of Last Renewal</label>
                    <input type="date" name="date_of_last_renewal" class="form-control" value="<?php echo e($termination->date_of_last_renewal); ?>"></div>
				
                    <div class="col-md-4">
                        <label>Date of Expiry</label>
                        <input type="date" name="date_of_expiry" class="form-control" value="<?php echo e($termination->date_of_expiry); ?>"></div>
				
                    <div class="col-md-4">
                        <label>Termination Request Date</label>
                        <input type="date" name="termination_request_date" class="form-control" value="<?php echo e($termination->termination_request_date); ?>"></div>
				
                    <div class="col-md-4">
                        <label>Termination Requested By</label>
                        <input type="text" name="termination_requested_by" class="form-control" value="<?php echo e($termination->termination_requested_by); ?>"></div>
				
                    <div class="col-md-4"><label>Termination Request Document</label>
					<?php if($termination->termination_request_document): ?>
						<a href="<?php echo e(asset('storage/'.$termination->termination_request_document)); ?>" target="_blank">View Document</a>
					<?php endif; ?>
					<input type="file" name="termination_request_document" class="form-control" accept=".pdf,.png,.jpg,.jpeg">
				</div>
				<div class="col-md-4"><label>Termination Date</label><input type="date" name="termination_date" class="form-control" value="<?php echo e($termination->termination_date); ?>"></div>
				<div class="col-md-4"><label>Status</label><input type="text" name="status" class="form-control" value="<?php echo e($termination->status); ?>"></div>
			</div>
			<div class="mt-4 text-end">
				<button type="submit" class="btn btn-primary">Update</button>
			</div>
		</form>
	</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\operations\termination\edit.blade.php ENDPATH**/ ?>