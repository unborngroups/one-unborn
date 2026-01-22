

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
	<h4 class="text-primary fw-bold mb-3">Add Termination Request</h4>
	<div class="card shadow border-0 p-4">
		<form action="<?php echo e(route('operations.termination.store')); ?>" method="POST" enctype="multipart/form-data">
			<?php echo csrf_field(); ?>
			<input type="hidden" name="circuit_id" value="">
			<div class="row mb-3">
				<div class="col-md-4">
					<label class="form-label fw-semibold">Circuit ID <span class="text-danger">*</span></label>
					<select name="deliverable_id" id="deliverable_id" class="form-select select2-tags" required>
					<option value="">Select Circuit ID</option>
					<?php $__currentLoopData = $deliverables_plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<option value="<?php echo e($d['deliverable_id']); ?>"
							data-company_name="<?php echo e($d['company_name'] ?? ''); ?>"
							data-address="<?php echo e($d['address'] ?? ''); ?>"
							data-bandwidth="<?php echo e($d['bandwidth'] ?? ''); ?>"
							data-asset_id="<?php echo e($d['asset_id'] ?? ''); ?>"
							data-asset_mac="<?php echo e($d['asset_mac'] ?? ''); ?>"
							data-asset_serial="<?php echo e($d['asset_serial'] ?? ''); ?>"
							data-date_of_activation="<?php echo e($d['date_of_activation'] ?? ''); ?>"
							data-date_of_last_renewal="<?php echo e($d['date_of_last_renewal'] ?? ''); ?>"
							data-date_of_expiry="<?php echo e($d['date_of_expiry'] ?? ''); ?>"
						>
							<?php echo e($d['circuit_id']); ?>

						</option>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				
                </select>
				</div>
			</div>
			<!-- This autofill path based in circuit ID selection in deliverable_id -->
			<div id="autofill_fields">
				<div class="row g-3">
					<div class="col-md-4">
						<label>Company Name</label>
						<input type="text" name="company_name" class="form-control" readonly>
					</div>
					<div class="col-md-4">
						<label>Address</label>
						<input type="text" name="address" class="form-control" readonly>
					</div>
					<div class="col-md-4">
						<label>Bandwidth</label>
						<input type="text" name="bandwidth" class="form-control" readonly>
					</div>
					<div class="col-md-4 asset-fields" style="display:none">
						<label>Asset ID</label>
						<input type="text" name="asset_id" class="form-control" readonly>
					</div>
					<div class="col-md-4 asset-fields" style="display:none">
						<label>Asset MAC</label>
						<input type="text" name="asset_mac" class="form-control" readonly>
					</div>
					<div class="col-md-4 asset-fields" style="display:none">
						<label>Asset Serial</label>
						<input type="text" name="asset_serial" class="form-control" readonly>
					</div>
					<div class="col-md-4">
						<label>Date of Activation</label>
						<input type="text" name="date_of_activation" class="form-control" readonly>
					</div>
					
					<div class="col-md-4">
						<label>Date of Last Renewal</label>
						<input type="text" name="date_of_last_renewal" class="form-control" readonly>
					</div>
					<div class="col-md-4">
						<label>Date of Expiry</label>
						<input type="text" name="date_of_expiry" class="form-control" readonly>
					</div>
				</div>
			</div>
			<hr>
			<!-- This all manual input fields -->
			<div class="row g-3">
				<div class="col-md-4">
					<label>Termination Request Date</label>
					<input type="date" name="termination_request_date" class="form-control" required>
				</div>
				<div class="col-md-4">
					<label>Termination Requested By</label>
					<input type="text" name="termination_requested_by" class="form-control" required>
				</div>
				<div class="col-md-4">
					<label>Termination Request Document</label>
					
					<input type="file" name="termination_request_document" class="form-control" accept=".pdf,.png,.jpg,.jpeg">
				</div>
				<div class="col-md-4">
					<label>Termination Date</label>
					<input type="date" name="termination_date" class="form-control">
				</div>
			</div>

			<div class="">
				<div class="float-start mt-4">
                <a href="<?php echo e(route('operations.termination.index')); ?>" class="btn btn-secondary">Back</a>
                </div>

				<div class="float-end mt-4">
				<button type="submit" class="btn btn-primary">Submit</button>
			    </div>

			


			</div>
			
		</form>
	</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
	$('#deliverable_id').on('change', function () {
		let selected = $(this).find(':selected');
		$('[name="company_name"]').val(selected.data('company_name') || '');
		$('[name="address"]').val(selected.data('address') || '');
		$('[name="bandwidth"]').val(selected.data('bandwidth') || '');
		var assetId = selected.data('asset_id') || '';
		var assetMac = selected.data('asset_mac') || '';
		var assetSerial = selected.data('asset_serial') || '';
		$('[name="asset_id"]').val(assetId);
		$('[name="asset_mac"]').val(assetMac);
		$('[name="asset_serial"]').val(assetSerial);
		// Show asset fields only if any asset data is present and not a NO ASSET message
		if ((assetId && !assetId.startsWith('[NO')) || assetMac || assetSerial) {
			$('.asset-fields').show();
		} else {
			$('.asset-fields').hide();
		}
		$('[name="date_of_activation"]').val(selected.data('date_of_activation') || '');
		$('[name="date_of_expiry"]').val(selected.data('date_of_expiry') || '');
		$('[name="date_of_last_renewal"]').val(selected.data('date_of_last_renewal') || '');
		$('[name="date_of_expiry"]').val(selected.data('date_of_expiry') || '');
		// Set circuit_id hidden field
		$('[name="circuit_id"]').val(selected.text().trim() || '');
	});
	// On page load, trigger change if a value is already selected
	$('#deliverable_id').trigger('change');
});
</script>





<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\operations\termination\create.blade.php ENDPATH**/ ?>