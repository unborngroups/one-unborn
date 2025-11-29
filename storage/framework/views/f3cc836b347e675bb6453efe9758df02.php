



<?php $__env->startSection('content'); ?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-10">



            <div class="card shadow-sm border-0">

                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">Edit Profile</h5>

                    <a href="<?php echo e(route('profile.view')); ?>" class="btn btn-light btn-sm">Back</a>

                </div>



                <div class="card-body">

                    <form action="<?php echo e(route('profile.update', $profile->id)); ?>" method="POST" enctype="multipart/form-data">

                        <?php echo csrf_field(); ?>

                        <?php echo method_field('PUT'); ?>



                        

                        <div class="text-center mb-4">

                            <img 

                                src="<?php echo e(asset($profile->profile_photo ?? 'images/default-user.png')); ?>" 

                                alt="Profile Photo" 

                                class="rounded-circle shadow-sm border" 

                                width="130" height="130"

                            >

                            <div class="mt-3">

                                <label class="form-label fw-bold">Change Profile Photo</label>

                                <input type="file" name="profile_photo" class="form-control <?php $__errorArgs = ['profile_photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">

                                <?php $__errorArgs = ['profile_photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>

                                    <div class="invalid-feedback"><?php echo e($message); ?></div>

                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                            </div>

                        </div>



                        <hr>



                        <div class="row">

                            

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">First Name <span class="text-danger">*</span></label>

                                <input type="text" name="fname" value="<?php echo e(old('fname', $profile->fname)); ?>" class="form-control" required>

                            </div>



                            

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">Last Name <span class="text-danger">*</span></label>

                                <input type="text" name="lname" value="<?php echo e(old('lname', $profile->lname)); ?>" class="form-control" required>

                            </div>



                            

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">Designation <span class="text-danger">*</span></label>

                                <input type="text" name="designation" value="<?php echo e(old('designation', $profile->designation)); ?>" class="form-control">

                            </div>



                            

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">Date of Birth <span class="text-danger">*</span></label>

                                <input type="date" name="Date_of_Birth" value="<?php echo e(old('Date_of_Birth', $profile->Date_of_Birth)); ?>" class="form-control">

                            </div>



                            

                        <div class="mb-3">

                            <label class="form-label">Official Email <span class="text-danger">*</span></label>

                            <input type="email" name="official_email" class="form-control" value="<?php echo e(old('official_email', $profile->official_email)); ?>" required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">Personal Email <span class="text-danger">*</span></label>

                            <input type="email" name="personal_email" class="form-control" value="<?php echo e(old('personal_email')); ?>" required>

                        </div>



                            

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">Phone 1 <span class="text-danger">*</span></label>

                                <input type="text" name="phone1" value="<?php echo e(old('phone1', $profile->phone1)); ?>" class="form-control" required>

                            </div>



                            

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">Phone 2 <span class="text-danger">*</span></label>

                                <input type="text" name="phone2" value="<?php echo e(old('phone2', $profile->phone2)); ?>" class="form-control">

                            </div>



                            

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">Aadhaar Number <span class="text-danger">*</span></label>

                                <input type="text" name="aadhaar_number" value="<?php echo e(old('aadhaar_number', $profile->aadhaar_number)); ?>" class="form-control">

                            </div>



                            

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">Aadhaar Upload <span class="text-danger">*</span></label>

                                <input type="file" name="aadhaar_upload" class="form-control">

                                <?php if($profile->aadhaar_upload): ?>

                                    <small class="text-muted">Current: 

                                        <a href="<?php echo e(asset($profile->aadhaar_upload)); ?>" target="_blank">View File</a>

                                    </small>

                                <?php endif; ?>

                            </div>



                            

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">PAN Number <span class="text-danger">*</span></label>

                                <input type="text" name="pan" value="<?php echo e(old('pan', $profile->pan)); ?>" class="form-control">

                            </div>



                            

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">PAN Upload <span class="text-danger">*</span></label>

                                <input type="file" name="pan_upload" class="form-control">

                                <?php if($profile->pan_upload): ?>

                                    <small class="text-muted">Current: 

                                        <a href="<?php echo e(asset($profile->pan_upload)); ?>" target="_blank">View File</a>

                                    </small>

                                <?php endif; ?>

                            </div>





                            

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">Bank Name</label>

                                <input type="text" name="bank_name" value="<?php echo e(old('bank_name', $profile->bank_name)); ?>" class="form-control">

                            </div>



                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">Branch</label>

                                <input type="text" name="branch" value="<?php echo e(old('branch', $profile->branch)); ?>" class="form-control">

                            </div>



                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">Account Number</label>

                                <input type="text" name="bank_account_no" value="<?php echo e(old('bank_account_no', $profile->bank_account_no)); ?>" class="form-control">

                            </div>



                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">IFSC Code</label>

                                <input type="text" name="ifsc_code" value="<?php echo e(old('ifsc_code', $profile->ifsc_code)); ?>" class="form-control">

                            </div>

                        </div>



                        <div class="text-center mt-4">

                            <button type="submit" class="btn btn-success px-4">Update Profile</button>

                            <a href="<?php echo e(route('profile.view')); ?>" class="btn btn-secondary px-4">Cancel</a>

                        </div>

                    </form>

                </div>

            </div>



        </div>

    </div>

</div>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\profile\edit.blade.php ENDPATH**/ ?>