<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Asset Label – <?php echo e($asset->asset_id); ?></title>
    <link rel="icon" type="image/png" width="20" height="20" href="<?php echo e(asset('images/logo.jpg')); ?>">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        @page {
            
            margin: 0;
        }
        body {
            margin: 0;
            padding: 4mm;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #fff;
        }
        .print-wrapper {
            width: 4cm;
            border-radius: 6px;
            background: #fff;
            border: 1px solid #e1e7ec;
            padding: 4mm;
            box-shadow: none;
            margin: 0 auto;
        }
        .company {
            font-size: 10px;
            text-transform: uppercase;
            color: #0f172a;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .label-title {
            font-size: 10                                                                                          px;
            font-weight: 600;
            color: #0f172a;
            /* margin: 6px 0 12px; */
            margin-left: 40px;
        }
        .meta-row {
            padding: 6px 0;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #475467;
            border-bottom: 1px dashed #e4e7ec;
        }
        .meta-row:last-child {
            border-bottom: none;
        }
        .barcode-box {
            margin-top: 10px;
            padding: 5px;
            background: #fff;
            text-align: left;
        }
        .footer-note {
            margin-top: 10px;
            font-size: 11px;
            color: #94a3b8;
        }
        @media print {
            body {
                background: #fff;
            }
            .print-wrapper {
                box-shadow: none;
                border: none;
                width: auto;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="print-wrapper">
        <div class="label">
    <!-- <strong><?php echo e($asset->asset_id); ?></strong><br> -->
    <?php echo e($asset->model_no); ?><br>
    <div class="company">  <?php echo e($asset->company->company_name ?? ''); ?> </div>
  
 <img src="/barcode.php?code=<?php echo e($asset->asset_id); ?>" alt="Barcode" style="height:40px; width:200px">
        <div class="label-title"><?php echo e($asset->asset_id); ?></div>

        
     
      

</div>
        <!--  -->
    </div>
</body>
</html>
<?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\operations\asset\print.blade.php ENDPATH**/ ?>