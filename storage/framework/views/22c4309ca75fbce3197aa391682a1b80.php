

<?php $__env->startSection('content'); ?>
<div class="container mt-4">
    <h2 class="mb-3">HR - Edit Profile</h2>

    <div class="mb-3">
        <a href="<?php echo e(route('hr.index')); ?>" class="btn btn-secondary btn-sm">Back to List</a>
    </div>

    <?php $profile = $user->profile; ?>

    <?php if(!$profile): ?>
        <div class="alert alert-warning">This user has not created a profile yet.</div>
    <?php else: ?>
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="<?php echo e(route('profile.update', $profile->id)); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">First Name</label>
                            <input type="text" name="fname" class="form-control" value="<?php echo e(old('fname', $profile->fname)); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Last Name</label>
                            <input type="text" name="lname" class="form-control" value="<?php echo e(old('lname', $profile->lname)); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Designation</label>
                            <input type="text" name="designation" class="form-control" value="<?php echo e(old('designation', $profile->designation)); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Date of Birth</label>
                            <input type="date" name="Date_of_Birth" class="form-control" value="<?php echo e(old('Date_of_Birth', $profile->Date_of_Birth)); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Official Email</label>
                            <input type="email" name="official_email" class="form-control" value="<?php echo e(old('official_email', $profile->official_email)); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Personal Email</label>
                            <input type="email" name="personal_email" class="form-control" value="<?php echo e(old('personal_email', $profile->personal_email)); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Phone 1</label>
                            <input type="text" name="phone1" class="form-control" value="<?php echo e(old('phone1', $profile->phone1)); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Phone 2</label>
                            <input type="text" name="phone2" class="form-control" value="<?php echo e(old('phone2', $profile->phone2)); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Aadhaar Number</label>
                            <input type="text" name="aadhaar_number" class="form-control" value="<?php echo e(old('aadhaar_number', $profile->aadhaar_number)); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">PAN Number</label>
                            <input type="text" name="pan" class="form-control" value="<?php echo e(old('pan', $profile->pan)); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" value="<?php echo e(old('bank_name', $profile->bank_name)); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Branch</label>
                            <input type="text" name="branch" class="form-control" value="<?php echo e(old('branch', $profile->branch)); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Account Number</label>
                            <input type="text" name="bank_account_no" class="form-control" value="<?php echo e(old('bank_account_no', $profile->bank_account_no)); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">IFSC Code</label>
                            <input type="text" name="ifsc_code" class="form-control" value="<?php echo e(old('ifsc_code', $profile->ifsc_code)); ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\hr\edit.blade.php ENDPATH**/ ?>