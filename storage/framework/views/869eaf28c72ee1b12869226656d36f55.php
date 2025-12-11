

<?php $__env->startSection('content'); ?>

<div style="margin:0 auto; max-width:580px; font-family:'Poppins', system-ui, sans-serif; color:#1f1f1f; background:#f7f3ee; border-radius:24px; padding:32px; box-shadow:0 20px 40px rgba(10,30,60,0.15);">
     <div style="text-align:center; padding-bottom:16px;">
          <img src="<?php echo e(asset('images/logo1.png')); ?>" alt="One-Unborn" style="height:48px;">
          <h2 style="margin:16px 0 8px; font-size:28px; letter-spacing:-0.4px;">Reset Password</h2>
          <p style="margin:0; color:#5c5b5b;">A secure token was generated for your account.</p>
     </div>
     <div style="background:#ffffff; border-radius:20px; padding:28px; border:1px solid #e3e3e3;">
          <p style="margin-top:0; margin-bottom:18px; font-size:16px;">Hello <strong><?php echo e($user->name); ?></strong>,</p>
          <p style="margin-bottom:24px; color:#4f5257;">We received a request to reset your password. Tap the button below to create a new one. The link expires in 60 minutes.</p>
          <div style="text-align:center;">
               <a href="<?php echo e($resetUrl); ?>" style="display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:14px 32px; background-image:linear-gradient(135deg,#f8c68e,#1f4b84); color:#fff; border-radius:999px; text-decoration:none; font-weight:700; box-shadow:0 16px 30px rgba(31,75,132,0.35);">
                    <span>Reset Password</span>
                    <i class="bi bi-arrow-right-short" style="font-size:20px;"></i>
               </a>
          </div>
          <p style="margin-top:24px; font-size:13px; color:#7a7a7a;">If you did not request this, no action is needed. For support contact <a href="mailto:support@oneunborn.com" style="color:#1f4b84; text-decoration:none;">support@oneunborn.com</a>.</p>
     </div>
     <footer style="margin-top:24px; text-align:center; font-size:12px; color:#999; letter-spacing:1px;">© <?php echo e(date('Y')); ?> One-Unborn. All rights reserved.</footer>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('emails.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\emails\password_reset.blade.php ENDPATH**/ ?>