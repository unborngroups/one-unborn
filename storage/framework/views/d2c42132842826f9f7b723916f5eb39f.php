

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
   <h4 class="text-primary fw-bold mb-3">Termination Requests</h4>
   <div class="mb-3">
	   <a href="<?php echo e(route('operations.termination.create')); ?>" class="btn btn-success">
		   <i class="bi bi-plus-circle"></i> Create Termination
	   </a>
   </div>
   <?php if(session('success')): ?>
	   <div class="alert alert-success"><?php echo e(session('success')); ?></div>
   <?php endif; ?>
   <div class="card shadow border-0 p-4">
		<table class="table table-bordered table-striped">
			<thead>
				<tr>
					<th>S.No</th>
					<th>Action</th>
					<th>Circuit ID</th>
					<th>Company Name</th>
					<th>Address</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<?php $__empty_1 = true; $__currentLoopData = $terminations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $termination): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
				<tr>
					<td><?php echo e($i+1); ?></td>
					<td>
						<a href="<?php echo e(route('termination.view', $termination->id)); ?>" class="btn btn-info btn-sm">View</a>
						<a href="<?php echo e(route('termination.edit', $termination->id)); ?>" class="btn btn-primary btn-sm">Edit</a>
					</td>
					<td><?php echo e($termination->circuit_id); ?></td>
					<td><?php echo e($termination->company_name); ?></td>
					<td><?php echo e($termination->address); ?></td>
					<td><?php echo e($termination->status); ?></td>
				</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
				<tr><td colspan="6" class="text-center">No records found.</td></tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\operations\termination\index.blade.php ENDPATH**/ ?>