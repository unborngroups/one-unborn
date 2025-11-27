<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo e($subject ?? 'Notification'); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 20px;
            font-weight: bold;
        }
        .content {
            padding: 20px;
            font-size: 16px;
            color: #333;
        }
        .btn {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            font-size: 12px;
            text-align: center;
            color: #888;
            padding: 15px;
            background: #f9f9f9;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header"><?php echo e($subject ?? 'Notification'); ?></div>
    <div class="content">
        <?php echo $__env->yieldContent('content'); ?>
    </div>
    <div class="footer">
        &copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. All rights reserved.
    </div>
</div>
</body>
</html>
<?php /**PATH F:\xampp\htdocs\wlcome\multipleuserpage\resources\views\emails\layout.blade.php ENDPATH**/ ?>