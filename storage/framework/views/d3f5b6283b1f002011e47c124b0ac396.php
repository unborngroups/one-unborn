



<?php $__env->startSection('content'); ?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">



            

            <?php if($errors->any()): ?>

                <div class="alert alert-danger">

                    <ul class="mb-0">

                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <li><?php echo e($error); ?></li>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </ul>

                </div>

            <?php endif; ?>



            <div class="card shadow-sm">

                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">Create / Update Your Profile</h5>

                </div>



                <div class="card-body">

                    <!-- ✅ enctype required for file upload -->

                    <form method="POST" action="<?php echo e(route('profile.store')); ?>" enctype="multipart/form-data">

                        <?php echo csrf_field(); ?>



                        

                        <div class="text-center mb-4">

                            <div class="position-relative d-inline-block">

                                <div class="border rounded-circle overflow-hidden shadow-sm" style="width: 130px; height: 130px;">

                                    <img id="photoPreview"

                                         src="<?php echo e(asset('images/default-avatar.png')); ?>"

                                         alt="Profile Preview"

                                         class="img-fluid w-100 h-100"

                                         style="object-fit: cover;">

                                </div>



                                <label for="photo" 

                                       class="btn btn-sm btn-primary position-absolute bottom-0 end-0 translate-middle"

                                       style="border-radius: 50%; padding: 6px 8px; cursor: pointer;">

                                    <i class="fa fa-camera"></i>

                                </label>



                                <input type="file" name="photo" id="photo" class="d-none" accept="image/*"

                                       onchange="previewImage(event)">

                            </div>

                            <p class="mt-2 text-muted small">Upload a profile photo (JPG, PNG)</p>

                        </div>



                        

                        <div class="mb-3">

                            <label class="form-label">First Name <span class="text-danger">*</span></label>

                            <input type="text" class="form-control" name="fname" value="<?php echo e(old('fname')); ?>" required>

                        </div>



                        <div class="mb-3">

                            <label class="form-label">Last Name <span class="text-danger">*</span></label>

                            <input type="text" class="form-control" name="lname" value="<?php echo e(old('lname')); ?>" required>

                        </div>



                        <div class="mb-3">

                            <label class="form-label">Designation <span class="text-danger">*</span></label>

                            <input type="text" class="form-control" name="designation" value="<?php echo e(old('designation')); ?>" required>

                        </div>



                        

                        <div class="mb-3">

                            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>

                            <input type="date" name="Date_of_Birth" class="form-control" value="<?php echo e(old('Date_of_Birth')); ?>" required>

                        </div>



                        

                        <div class="mb-3">

                            <label class="form-label">Official Email <span class="text-danger">*</span></label>

                            <input type="email" name="official_email" class="form-control" value="<?php echo e(old('official_email')); ?>" required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">Personal Email <span class="text-danger">*</span></label>

                            <input type="email" name="personal_email" class="form-control" value="<?php echo e(old('personal_email')); ?>" required>

                        </div>



                        

                        <div class="mb-3">

                            <label class="form-label">Phone Number 1 <span class="text-danger">*</span></label>

                            <input type="number" class="form-control" name="phone1" value="<?php echo e(old('phone1')); ?>" required>

                        </div>



                        <div class="mb-3">

                            <label class="form-label">Phone Number 2 <span class="text-danger">*</span></label>

                            <input type="number" class="form-control" name="phone2" value="<?php echo e(old('phone2')); ?>">

                        </div>



                        

                        <h5 class="text-secondary mt-4">Aadhaar Details </h5>

                        <div class="mb-3">

                            <label class="form-label">Aadhaar Number <span class="text-danger">*</span></label>

                            <input type="number" class="form-control" name="aadhaar_number" value="<?php echo e(old('aadhaar_number')); ?>" required>

                        </div>



                        <div class="mb-3">

                            <label class="form-label">Aadhaar Upload <span class="text-danger">*</span></label>

                            <input type="file" class="form-control" name="aadhaar_upload" required>

                        </div>



                        

                        <h5 class="text-secondary mt-4">PAN Details</h5>

                        <div class="mb-3">

                            <label class="form-label">PAN Number <span class="text-danger">*</span></label>

                            <input type="text" name="pan" class="form-control" value="<?php echo e(old('pan')); ?>" placeholder="PAN No">

                        </div>



                        <div class="mb-3">

                            <label class="form-label">PAN Upload <span class="text-danger">*</span></label>

                            <input type="file" class="form-control" name="pan_upload" required>

                        </div>



                        

                        <h5 class="text-secondary mt-4">Bank Details <span class="text-danger">*</span></h5>

                        <input type="text" name="bank_name" class="form-control mb-2" placeholder="Bank Name" value="<?php echo e(old('bank_name')); ?>">

                        <input type="text" name="branch" class="form-control mb-2" placeholder="Branch" value="<?php echo e(old('branch')); ?>">

                        <input type="text" name="bank_account_no" class="form-control mb-2" placeholder="Account No" value="<?php echo e(old('bank_account_no')); ?>">

                        <input type="text" name="ifsc_code" class="form-control mb-4" placeholder="IFSC Code" value="<?php echo e(old('ifsc_code')); ?>">



                        <button type="submit" class="btn btn-success w-100 py-2">Save Profile</button>

                    </form>

                </div>

            </div>



        </div>

    </div>

</div>





<script>

function previewImage(event) {

    const input = event.target;

    const preview = document.getElementById('photoPreview');

    if (input.files && input.files[0]) {

        const reader = new FileReader();

        reader.onload = e => preview.src = e.target.result;

        reader.readAsDataURL(input.files[0]);

    }

}

</script>





<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\wlcome\multipleuserpage\resources\views\profile\create.blade.php ENDPATH**/ ?>