

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>Cash Flow Statement</h4>

    <table class="table table-bordered">
        <tr>
            <th>Cash Inflow</th>
            <td>₹ <?php echo e(number_format($cashIn,2)); ?></td>
        </tr>
        <tr>
            <th>Cash Outflow</th>
            <td>₹ <?php echo e(number_format($cashOut,2)); ?></td>
        </tr>
        <tr class="table-success">
            <th>Net Cash Flow</th>
            <td>₹ <?php echo e(number_format($netCash,2)); ?></td>
        </tr>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\reports\cash_flow.blade.php ENDPATH**/ ?>