

<?php $__env->startSection('content'); ?>

<!DOCTYPE html>
<html>
<head>
    <title>State-wise Invoice Report</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: center; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2>State-wise Invoice Report (Monthly)</h2>
    <table>
        <thead>
            <tr>
                <th>State</th>
                <th>April</th>
                <th>May</th>
                <th>June</th>
                <th>July</th>
                <th>August</th>
                <th>September</th>
                <th>October</th>
                <th>November</th>
                <th>December</th>
                <th>January</th>
                <th>February</th>
                <th>March</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($state->name); ?></td>
                <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td><?php echo e($state->monthly_invoices[$month] ?? 0); ?></td>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <h2>State-wise Invoice Report (Quarterly)</h2>
    <table>
        <thead>
            <tr>
                <th>State</th>
                <th>Q1 (Apr-Jun)</th>
                <th>Q2 (Jul-Sep)</th>
                <th>Q3 (Oct-Dec)</th>
                <th>Q4 (Jan-Mar)</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($state->name); ?></td>
                <td><?php echo e($state->quarterly_invoices[1] ?? 0); ?></td>
                <td><?php echo e($state->quarterly_invoices[2] ?? 0); ?></td>
                <td><?php echo e($state->quarterly_invoices[3] ?? 0); ?></td>
                <td><?php echo e($state->quarterly_invoices[4] ?? 0); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\invoices\state_report.blade.php ENDPATH**/ ?>