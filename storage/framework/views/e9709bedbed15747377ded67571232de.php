

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>Profit & Loss Report</h4>

    <table class="table table-bordered">
        <tr>
            <th>Total Income</th>
            <td>₹ <?php echo e(number_format($totalIncome,2)); ?></td>
        </tr>
        <tr>
            <th>Total Expenses</th>
            <td>₹ <?php echo e(number_format($totalExpense,2)); ?></td>
        </tr>
        <tr class="table-success">
            <th>Net Profit / Loss</th>
            <td>₹ <?php echo e(number_format($profit,2)); ?></td>
        </tr>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\reports\profit_loss.blade.php ENDPATH**/ ?>