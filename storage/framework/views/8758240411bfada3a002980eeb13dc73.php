

<?php $__env->startSection('content'); ?>

<div class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-primary">Purchase Invoice Details</h4>

    <div>
        <a href="<?php echo e(route('finance.purchases.print', $purchase->id)); ?>" class="btn btn-outline-secondary">
            🖨 Print
        </a>

        <a href="<?php echo e(route('finance.purchases.edit', $purchase->id)); ?>" class="btn btn-warning text-white">
            ✏ Edit
        </a>

        <a href="<?php echo e(route('finance.purchases.index')); ?>" class="btn btn-dark">
            Back
        </a>
    </div>
</div>

<div class="card shadow-lg border-0">
    <div class="card-body">

        <div class="row mb-3">

            <div class="col-md-4">
                <label class="text-muted">Vendor</label>
                <p class="fw-semibold"><?php echo e($purchase->vendor->vendor_name ?? '-'); ?></p>
            </div>

            <div class="col-md-4">
                <label class="text-muted">Deliverable</label>
                <p class="fw-semibold">
                    <?php echo e($purchase->deliverable->id ?? '-'); ?>

                    <span class="badge bg-info text-dark">
                        PO: <?php echo e($purchase->deliverable->purchase_order_id ?? '-'); ?>

                    </span>
                </p>
            </div>

            <div class="col-md-4">
                <label class="text-muted">Invoice Number</label>
                <p class="fw-semibold text-primary"><?php echo e($purchase->invoice_no); ?></p>
            </div>

            <div class="col-md-4">
                <label class="text-muted">Invoice Date</label>
                <p><?php echo e($purchase->invoice_date ?? '-'); ?></p>
            </div>

            <div class="col-md-4">
                <label class="text-muted">Status</label>

                <?php if($purchase->status == 'higher'): ?>
                    <span class="badge bg-danger">Higher</span>
                <?php elseif($purchase->status == 'lower'): ?>
                    <span class="badge bg-warning text-dark">Lower</span>
                <?php else: ?>
                    <span class="badge bg-success">Matched</span>
                <?php endif; ?>
            </div>

        </div>

        <hr>

        
        <h5 class="mb-3 text-secondary">Items</h5>

        <table class="table table-hover table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $purchase->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($item->item->item_name ?? '-'); ?></td>
                        <td><?php echo e($item->quantity); ?></td>
                        <td>₹ <?php echo e(number_format($item->price, 2)); ?></td>
                        <td class="fw-semibold">₹ <?php echo e(number_format($item->total, 2)); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">No items found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <hr>

        
        <div class="text-end">
            <h4 class="fw-bold text-success">
                Total: ₹ <?php echo e(number_format($purchase->total_amount, 2)); ?>

            </h4>
        </div>

        
        <div class="mt-3">
            <label class="text-muted">Invoice File</label><br>

            <?php if($purchase->po_invoice_file): ?>
                <a href="<?php echo e(asset('images/poinvoice_files/'.$purchase->po_invoice_file)); ?>"
                   target="_blank"
                   class="btn btn-outline-primary btn-sm mt-1">
                   📄 View Invoice
                </a>
            <?php else: ?>
                <p class="text-muted">No file uploaded</p>
            <?php endif; ?>
        </div>

    </div>
</div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\purchases\show.blade.php ENDPATH**/ ?>