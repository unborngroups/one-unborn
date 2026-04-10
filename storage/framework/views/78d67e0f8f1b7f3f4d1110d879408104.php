

<?php $__env->startSection('title', 'Edit ' . ucfirst($type) . ' Contact'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-semibold">Edit <?php echo e(ucfirst($type)); ?> Contact</h4>
        <a href="<?php echo e(route('contacts.' . $type . '.index')); ?>" class="btn btn-outline-secondary btn-sm">
            Back
        </a>
    </div>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="<?php echo e(route('contacts.update', [$type, $contact->id])); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <input type="hidden" name="status" value="<?php echo e(old('status', strtolower($contact->status))); ?>">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $contact->name)); ?>" placeholder="Enter name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Area</label>
                        <input type="text" name="area" class="form-control" value="<?php echo e(old('area', $contact->area)); ?>" placeholder="Enter area">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">State</label>
                        <input type="text" name="state" class="form-control" value="<?php echo e(old('state', $contact->state)); ?>" placeholder="Enter state">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contact1 <span class="text-danger">*</span></label>
                        <input type="text" name="contact1" class="form-control" value="<?php echo e(old('contact1', $contact->contact1)); ?>" placeholder="Enter primary number" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contact2</label>
                        <input type="text" name="contact2" class="form-control" value="<?php echo e(old('contact2', $contact->contact2)); ?>" placeholder="Enter alternate number">
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\contact\edit.blade.php ENDPATH**/ ?>