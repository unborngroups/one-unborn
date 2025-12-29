

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>Debit Notes</h4>
        <a href="<?php echo e(route('finance.debit-notes.create')); ?>" class="btn btn-primary">
            + Add Debit Note
        </a>
    </div>

    <table class="table table-bordered">
        <thead class="table-dark-primary">
            <tr>
                <th>S.No</th>
                <th>Debit Note No</th>
                <th>Vendor</th>
                <th>Invoice</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $debitNotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($loop->iteration); ?></td>
                <td><?php echo e($note->debit_note_no); ?></td>
                <td><?php echo e(optional($note->vendorInvoice->vendor)->vendor_name ?? '-'); ?></td>
                <td><?php echo e(optional($note->vendorInvoice)->invoice_no ?? '-'); ?></td>
                <td><?php echo e($note->date ? \Carbon\Carbon::parse($note->date)->format('d-m-Y') : '-'); ?></td>
                <td>₹ <?php echo e(number_format($note->amount, 2)); ?></td>
                <td><?php echo e($note->reason ?: '-'); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\purchases\debit_notes\index.blade.php ENDPATH**/ ?>