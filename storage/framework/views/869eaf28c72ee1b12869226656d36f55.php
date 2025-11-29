

<?php $__env->startSection('content'); ?>

<p>Hello <strong><?php echo e($user->name); ?></strong>,</p>

<p>We received a request to reset your password. Click the button below to set a new one:</p>

<a href="<?php echo e($resetUrl); ?>" 
   style="
        display:inline-block;
        background-color:#007bff;
        color:#ffffff !important;
        padding:12px 22px;
        text-decoration:none;
        border-radius:6px;
        font-weight:600;
        font-size:15px;
        text-align:center;
   ">
    Reset Password
</a>


<p>This link will expire in 60 minutes. If you did not request a password reset, you can safely ignore this email.</p>

<p>Thanks,<br>

<!-- <?php echo e(config('app.name')); ?>  -->
Team</p>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('emails.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\emails\password_reset.blade.php ENDPATH**/ ?>