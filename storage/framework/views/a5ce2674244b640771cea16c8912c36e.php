



<?php $__env->startSection('content'); ?>

<div class="container mt-5">



    <div class="row justify-content-center">

        <div class="col-md-10">



            <div class="card shadow-sm border-0">

                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">My Profile</h5>

                    <a href="<?php echo e(route('profile.edit')); ?>" class="btn btn-light btn-sm">Edit Profile</a>

                </div>



                <div class="card-body">

                     <?php if($profile): ?>

                        

                        <div class="text-center mb-4">

                           <?php

    $photoPath = $profile->profile_photo 

        ? asset('images/profile_photos/' . basename($profile->profile_photo))

        : asset('images/default-user.png');

?>



<img 

    src="<?php echo e($photoPath); ?>" 

    alt="Profile Photo" 

    class="rounded-circle shadow-sm border" 

    width="130" height="130"

/>



                            <h5 class="mt-3 fw-bold text-primary"><?php echo e($profile->fname); ?> <?php echo e($profile->lname); ?></h5>

                            <p class="text-muted"><?php echo e($profile->designation); ?></p>

                        </div>



                        <hr>



                        

                        <div class="row">

                            

                            <div class="col-md-6 mb-3">

                                <h6 class="text-secondary fw-bold">Basic Information</h6>

                                <p><strong>Name:</strong> <?php echo e($profile->fname); ?> <?php echo e($profile->lname); ?></p>

                                <p><strong>Designation:</strong> <?php echo e($profile->designation); ?></p>

                                <p><strong>Date of Birth:</strong> <?php echo e(\Carbon\Carbon::parse($profile->Date_of_Birth)->format('Y-m-d')); ?></p>

                            </div>



                            

                            <div class="col-md-6 mb-3">

                                <h6 class="text-secondary fw-bold">Contact Information</h6>

                                <p><strong>Official Email:</strong> <?php echo e($profile->official_email); ?></p>

                                <p><strong>Personal Email:</strong> <?php echo e($profile->personal_email); ?></p>

                                <p><strong>Phone 1:</strong> <?php echo e($profile->phone1); ?></p>

                                <p><strong>Phone 2:</strong> <?php echo e($profile->phone2 ?? 'N/A'); ?></p>

                            </div>



                            

                            <div class="col-md-6 mb-3">

                                <h6 class="text-secondary fw-bold">Aadhaar Information</h6>

                                <p><strong>Aadhaar Number:</strong> <?php echo e($profile->aadhaar_number); ?></p>

                                <?php if($profile->aadhaar_upload): ?>

                                    <p>

                                        <strong>Aadhaar File:</strong>

                                        <a href="<?php echo e(asset($profile->aadhaar_upload)); ?>" target="_blank" class="text-primary">View / Download</a>

                                    </p>

                                <?php endif; ?>

                            </div>



                            <div class="col-md-6 mb-3">

                                <h6 class="text-secondary fw-bold">PAN Information</h6>

                                <p><strong>PAN Number:</strong> <?php echo e($profile->pan); ?></p>

                                <?php if($profile->pan_upload): ?>

                                    <p>

                                        <strong>PAN File:</strong>

                                        <a href="<?php echo e(asset($profile->pan_upload)); ?>" target="_blank" class="text-primary">View / Download</a>

                                    </p>

                                <?php endif; ?>

                            </div>



                            

                            <div class="col-md-6 mb-3">

                                <h6 class="text-secondary fw-bold">Bank Details</h6>

                                <p><strong>Bank:</strong> <?php echo e($profile->bank_name ?? 'N/A'); ?></p>

                                <p><strong>Branch:</strong> <?php echo e($profile->branch ?? 'N/A'); ?></p>

                                <p><strong>Account No:</strong> <?php echo e($profile->bank_account_no ?? 'N/A'); ?></p>

                                <p><strong>IFSC:</strong> <?php echo e($profile->ifsc_code ?? 'N/A'); ?></p>

                            </div>

                        </div>

                    <?php else: ?>

                        <div class="alert alert-warning text-center">

                            Profile not found! <a href="<?php echo e(route('profile.create')); ?>" class="alert-link">Create your profile here</a>.

                        </div>

                    <?php endif; ?>

                </div>

            </div>



        </div>

        

        <div class="mt-3">

            <a href="<?php echo e(route('welcome')); ?>" class="btn btn-secondary">Back</a>

            <!-- <a href="<?php echo e(route('users.edit', $user)); ?>" class="btn btn-warning">Edit</a> -->

        </div>

    </div>

</div>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\profile\view.blade.php ENDPATH**/ ?>