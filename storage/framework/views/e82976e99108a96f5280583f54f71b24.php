<!DOCTYPE html>
    <style>
        @page {
            margin: 0;
        }
        body {
            margin: 0;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #fff;
            text-align: center;
        }
       
            .company {
            font-size: 10px;
            padding-top: 1mm;
            text-align: center;
            text-transform: uppercase;
            color: #0f172a;
            font-weight: 500;
        }
        .label-title {
            font-size: 10px;
            font-weight: 500;
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
<body>
    <?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="print-wrapper">
        <div class="label">
            <p class="company"><?php echo e($asset->company->company_name ?? ''); ?></p>
            <div class="barcode-box">
                <img src="/barcode.php?code=<?php echo e($asset->asset_id); ?>" alt="Barcode" style="height:20mm; width:50mm">
            </div>
            <div class="label-title"><?php echo e($asset->asset_id); ?></div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            window.print();
        });
    </script>
</body>
</html>
<?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\operations\asset\bulk-print.blade.php ENDPATH**/ ?>