<!DOCTYPE html>
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
        /* .company-badge {
            text-align: center;
            font-size: 5px;
            letter-spacing: 0.10em;
            text-transform: uppercase;
            margin-bottom: 2px;
            font-weight: 500;
        } */
            .company {
            font-size: 10px;
            text-transform: uppercase;
            color: #0f172a;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .label-title {
            font-size: 15px;
            font-weight: 600;
            /* color:black; */
            margin-left: 60px;
        }
        /* .barcode-box {
            margin-top: 14px;
            padding: 6px;
            background: #fff;
            text-align: left;
        } */
        .company {
            font-size: 12px;
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
    <h2>Print Preview</h2>
    <?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="print-wrapper">
        <div class="label">
            <p class="company"><?php echo e($asset->company->company_name ?? ''); ?></p>
            <div class="barcode-box">
                <img src="/barcode.php?code=<?php echo e($asset->asset_id); ?>" alt="Barcode" style="height:40px; width:200px">
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