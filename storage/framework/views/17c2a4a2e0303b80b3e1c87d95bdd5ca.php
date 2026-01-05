

<?php $__env->startSection('content'); ?>
<div class="container mt-4">
    <h2 class="mb-3">HR - View Profile</h2>

    <div class="mb-3">
        <a href="<?php echo e(route('hr.index')); ?>" class="btn btn-secondary btn-sm">Back to List</a>
    </div>

    <?php $profile = $user->profile; ?>

    <?php if(!$profile): ?>
        <div class="alert alert-warning">This user has not created a profile yet.</div>
    <?php else: ?>
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3 text-center">
                        <?php
                            $photoPath = $profile->profile_photo
                                ? asset('images/profile_photos/' . basename($profile->profile_photo))
                                : asset('images/default-user.png');
                        ?>
                        <img src="<?php echo e($photoPath); ?>" alt="Profile Photo" class="rounded-circle shadow-sm border" width="130" height="130">
                        <h5 class="mt-3"><?php echo e($profile->fname); ?> <?php echo e($profile->lname); ?></h5>
                        <p class="text-muted mb-0"><?php echo e($profile->designation); ?></p>
                    </div>
                    <div class="col-md-9">
                        <table class="table table-bordered">
                            <tr><th>First Name</th><td><?php echo e($profile->fname); ?></td></tr>
                            <tr><th>Last Name</th><td><?php echo e($profile->lname); ?></td></tr>
                            <tr><th>Designation</th><td><?php echo e($profile->designation); ?></td></tr>
                            <tr><th>Official Email</th><td><?php echo e($profile->official_email); ?></td></tr>
                            <tr><th>Personal Email</th><td><?php echo e($profile->personal_email); ?></td></tr>
                            <tr><th>Phone 1</th><td><?php echo e($profile->phone1); ?></td></tr>
                            <tr><th>Phone 2</th><td><?php echo e($profile->phone2 ?? '-'); ?></td></tr>
                            <tr><th>Date of Birth</th><td><?php echo e($profile->Date_of_Birth); ?></td></tr>
                            <tr><th>Aadhaar Number</th><td><?php echo e($profile->aadhaar_number); ?></td></tr>
                            <tr>
                                <th>Aadhaar Upload</th>
                                <td>
                                    <?php if($profile->aadhaar_upload): ?>
                                        <a href="<?php echo e(asset($profile->aadhaar_upload)); ?>" target="_blank">View Aadhaar</a>
                                    <?php else: ?>
                                        Not Uploaded
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr><th>PAN Number</th><td><?php echo e($profile->pan); ?></td></tr>
                            <tr>
                                <th>PAN Upload</th>
                                <td>
                                    <?php if($profile->pan_upload): ?>
                                        <a href="<?php echo e(asset($profile->pan_upload)); ?>" target="_blank">View PAN</a>
                                    <?php else: ?>
                                        Not Uploaded
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr><th>Bank Name</th><td><?php echo e($profile->bank_name ?? 'N/A'); ?></td></tr>
                            <tr><th>Branch</th><td><?php echo e($profile->branch ?? 'N/A'); ?></td></tr>
                            <tr><th>Account Number</th><td><?php echo e($profile->bank_account_no ?? 'N/A'); ?></td></tr>
                            <tr><th>IFSC Code</th><td><?php echo e($profile->ifsc_code ?? 'N/A'); ?></td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\hr\view.blade.php ENDPATH**/ ?>