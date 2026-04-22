<table>
    <thead>
        <tr>
            <th>Vendor Name</th>
            <th>Vendor Bank Account</th>
            <th>IFSC</th>
            <th>Amount</th>
            <th>Invoice No</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($row->vendor_name); ?></td>
                <td><?php echo e($row->vendor_bank_account); ?></td>
                <td><?php echo e($row->vendor_ifsc); ?></td>
                <td><?php echo e($row->amount); ?></td>
                <td><?php echo e($row->invoice_no); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\exports\purchases.blade.php ENDPATH**/ ?>