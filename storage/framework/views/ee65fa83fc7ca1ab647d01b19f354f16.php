<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8">

    <title>Welcome to <?php echo e($company ?? config('app.name')); ?></title>

    <style>

        body {

            font-family: 'Arial', sans-serif;

            background-color: #f7f7f7;

            margin: 0;

            padding: 30px;

        }

        .email-container {

            background: #fff;

            max-width: 600px;

            margin: auto;

            border-radius: 8px;

            box-shadow: 0 0 10px rgba(0,0,0,0.1);

            overflow: hidden;

        }

        .header {

            background-color: #6a1b9a;

            color: #fff;

            text-align: center;

            padding: 20px;

            font-size: 22px;

            font-weight: bold;

        }

        .content {

            padding: 30px;

            line-height: 1.6;

            color: #333;

        }

        .footer {

            background-color: #eee;

            text-align: center;

            padding: 12px;

            font-size: 13px;

            color: #555;

        }

    </style>

</head>

<body>



<?php

    // ✅ Normalize variables

    $company     = $company ?? ($emailData['company'] ?? config('app.name'));

    $name        = $name ?? ($emailData['name'] ?? ($emailData['user']['name'] ?? 'User'));

    $email       = $email ?? ($emailData['email'] ?? ($emailData['user']['email'] ?? ''));

    $password    = $password ?? ($emailData['password'] ?? '');

    $templateBody = $emailData['template_body'] ?? '';

    

    // ✅ Footer & signature from company settings

    $footerText     = $emailData['mail_footer'] ?? '';

    $signatureText  = $emailData['mail_signature'] ?? 'HR Team';



    // ✅ Whether template exists

    $templateExists = !empty($templateBody);

?>



<div class="email-container">



    <!-- ✅ HEADER -->

    <div class="header">

        Welcome to <?php echo e($company); ?>


    </div>



    <!-- ✅ MAIN CONTENT -->

    <div class="content">



        <!-- ✅ Default email content -->

        <p>Hi <?php echo e(strtoupper($name)); ?>,</p>



        <p>Welcome to <?php echo e($company); ?>!</p>



        <p><strong>Your Login Credentials:</strong></p>

        <p>Email: <b><?php echo e($email); ?></b></p>

        <p>Temporary Password: <b><?php echo e($password); ?></b></p>



        <p>Please login and update your profile.</p>



        <p>

            <a href="<?php echo e(url('/login')); ?>" 

               style="display:inline-block; background-color:#6a1b9a; color:#fff; 

                      padding:12px 25px; text-decoration:none; border-radius:6px; font-weight:bold;">

               Login Now

            </a>

        </p>



        <!-- ✅ Show Best Regards only when NO template master -->

        <!-- <?php if(!$templateExists): ?>

            <p>Best regards,<br><?php echo e($signatureText); ?></p>

        <?php endif; ?>

         -->

        <?php if(!empty($mail_footer)): ?>

    <div style="margin-top:15px; font-size:12px; color:#666;">

        <?php echo nl2br($mail_footer); ?>


    </div>

<?php endif; ?>





        <?php if(!empty($signatureText)): ?>

    <p><?php echo nl2br($signatureText); ?></p>

<?php else: ?>

    <p>Best regards,<br><?php echo e($company); ?></p>

<?php endif; ?>








        <!-- ✅ TEMPLATE MASTER CONTENT (only if exists) -->
        <?php if($templateExists): ?>
            <div style="margin-top: 25px; padding: 20px; background-color: #f8f9fa; border-left: 4px solid #6a1b9a; border-radius: 4px;">
                <?php echo // Replace image URLs with CIDs if available
                    preg_replace_callback(
                        '/<img[^>]+src=("|\')([^"\']+)("|\')[^>]*>/i',
                        function($matches) use ($emailData) {
                            $src = $matches[2];
                            if (!empty($emailData) && is_array($emailData)) {
                                foreach ($emailData as $key => $val) {
                                    if (strpos($src, $key) !== false && strpos($val, 'cid:') === 0) {
                                        return str_replace($src, $val, $matches[0]);
                                    }
                                }
                            }
                            return $matches[0];
                        },
                        $templateBody
                    ); ?>

            </div>
        <?php endif; ?>



    </div>



    <!-- ✅ FOOTER -->

    <div class="footer">

        © <?php echo e(date('Y')); ?> <?php echo e($company); ?>. 

        <?php echo e($footerText ?: 'All rights reserved.'); ?>


    </div>



</div>



</body>

</html>

<?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\emails\dynamic-template.blade.php ENDPATH**/ ?>