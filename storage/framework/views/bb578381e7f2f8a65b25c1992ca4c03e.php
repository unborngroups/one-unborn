

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>Balance Sheet</h4>

    <table class="table table-bordered">
        <tr class="table-primary">
            <th colspan="2">Assets</th>
        </tr>
        <tr>
            <td>Total Assets</td>
            <td>₹ <?php echo e(number_format($assets,2)); ?></td>
        </tr>

        <tr class="table-warning">
            <th colspan="2">Liabilities</th>
        </tr>
        <tr>
            <td>Total Liabilities</td>
            <td>₹ <?php echo e(number_format($liabilities,2)); ?></td>
        </tr>

        <tr class="table-success">
            <th>Equity</th>
            <td>₹ <?php echo e(number_format($equity,2)); ?></td>
        </tr>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\reports\balance_sheet.blade.php ENDPATH**/ ?>