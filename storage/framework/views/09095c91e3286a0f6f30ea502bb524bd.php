<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice PDF</title>
</head>
<body>

    <?php echo $__env->make('finance.invoice.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</body>
</html>
<?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\invoices\pdf.blade.php ENDPATH**/ ?>