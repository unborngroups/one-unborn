

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>Vendor Invoices</h4>
        <a href="<?php echo e(route('finance.vendor-invoices.create')); ?>" class="btn btn-primary">
            + Add Invoice
        </a>
    </div>

    <table class="table table-bordered">
        <thead class="table-dark-primary">
            <tr>
                <th>S.No</th>
                <th>Action</th>
                <th>Vendor</th>
                <th>Invoice No</th>
                <th>Date</th>
                <th>Total</th>
                <th>Status</th>
                <!-- <th width="120">Action</th> -->
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($loop->iteration); ?></td>
                <td>
                    <?php if($permissions->can_edit): ?>

                               <a href="<?php echo e(route('finance.vendor-invoices.edit', $invoice)); ?>" class="btn btn-sm btn-primary">

                                    <i class="bi bi-pencil"></i>

                                </a>

                                <?php endif; ?>
                                 

                                 <?php if($permissions->can_delete): ?>

                                 <form action="<?php echo e(route('finance.vendor-invoices.destroy',$invoice)); ?>" method="POST" class="d-inline">

                                    <?php echo csrf_field(); ?>

                                    <?php echo method_field('DELETE'); ?> 

                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this Client?')">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </form>

                                   <?php endif; ?>
                </td>
                <td><?php echo e($invoice->vendor->vendor_name ?? '—'); ?></td>
                <td><?php echo e($invoice->invoice_no); ?></td>
                <td><?php echo e($invoice->invoice_date); ?></td>
                <td>₹ <?php echo e(number_format($invoice->total_amount,2)); ?></td>
                <td>
                    <span class="badge bg-info"><?php echo e($invoice->status); ?></span>
                </td>
                <!-- <td>
                    <a href="<?php echo e(route('finance.vendor-invoices.edit',$invoice->id)); ?>" class="btn btn-sm btn-warning">
                        Edit
                    </a>
                </td> -->
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="6" class="text-center">No records found</td>
            </tr>
            <?php endif; ?>

            <a href="<?php echo e(route('finance.purchases.index')); ?>" class="btn btn-secondary"><- Back</a>

        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\purchases\vendor_invoices\index.blade.php ENDPATH**/ ?>