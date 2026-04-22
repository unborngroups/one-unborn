

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
                <p class="fw-semibold"><?php echo e($displayVendorName ?: (optional($purchase->vendor)->vendor_name ?? $vendorFromMaster ?? $purchase->vendor_name ?? $purchase->vendor_name_raw ?? '-')); ?></p>
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
                <p class="fw-semibold text-primary"><?php echo e($displayInvoiceNo ?: $purchase->invoice_no ?: '-'); ?></p>
            </div>

            <div class="col-md-4">
                <label class="text-muted">Invoice Date</label>
                <p><?php echo e($displayInvoiceDate ? \Carbon\Carbon::parse($displayInvoiceDate)->format('d-m-Y') : '-'); ?></p>
            </div>

            <div class="col-md-4">
                <label class="text-muted">GSTIN</label>
                <p><?php echo e($displayGstin ?: '-'); ?></p>
            </div>

            <div class="col-md-4">
                <label class="text-muted">Status</label>

                <span class="badge 
                    <?php switch($displayStatus):
                        case ('draft'): ?> bg-secondary <?php break; ?>
                        <?php case ('needs_review'): ?> bg-warning text-dark <?php break; ?>
                        <?php case ('verified'): ?> bg-info <?php break; ?>
                        <?php case ('approved'): ?> bg-primary <?php break; ?>
                        <?php case ('paid'): ?> bg-dark <?php break; ?>
                        <?php case ('failed'): ?> bg-danger <?php break; ?>
                        <?php case ('duplicate'): ?> bg-danger <?php break; ?>
                        <?php case ('higher'): ?> bg-danger <?php break; ?>
                        <?php case ('lower'): ?> bg-warning text-dark <?php break; ?>
                        <?php default: ?> bg-success
                    <?php endswitch; ?>">
                    <?php echo e(ucfirst(str_replace('_', ' ', $displayStatus))); ?>

                </span>
            </div>

            <div class="col-md-4">
                <label class="text-muted">Accuracy</label>
                <p><?php echo e(!is_null($displayAccuracy) ? rtrim(rtrim(number_format((float) $displayAccuracy, 2), '0'), '.') . '%' : '-'); ?></p>
            </div>

            <?php
                $importFailureReason = data_get($purchase->raw_json, 'import_failure_reason');
            ?>
            <?php if(!empty($importFailureReason)): ?>
                <div class="col-md-12">
                    <div class="alert alert-danger py-2 mb-0">
                        <strong>Import Alert:</strong> <?php echo e($importFailureReason); ?>

                    </div>
                </div>
            <?php endif; ?>

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
                <?php
                    $showRows = $purchase->items->isNotEmpty()
                        ? $purchase->items->map(function ($item) {
                            return [
                                'item_name' => $item->item->item_description ?? $item->item->item_name ?? '-',
                                'quantity' => $item->quantity,
                                'price' => $item->price,
                                'total' => $item->total,
                            ];
                        })->toArray()
                        : array_map(function ($row) {
                            $quantity = (float) ($row['quantity'] ?? 1);
                            $price = (float) ($row['price'] ?? 0);
                            return [
                                'item_name' => $row['item_label'] ?? 'Invoice Item',
                                'quantity' => $quantity,
                                'price' => $price,
                                'total' => $quantity * $price,
                            ];
                        }, $prefillRows ?? []);
                ?>
                <?php $__empty_1 = true; $__currentLoopData = $showRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($item['item_name'] ?? '-'); ?></td>
                        <td><?php echo e($item['quantity'] ?? 0); ?></td>
                        <td>₹ <?php echo e(number_format((float) ($item['price'] ?? 0), 2)); ?></td>
                        <td class="fw-semibold">₹ <?php echo e(number_format((float) ($item['total'] ?? 0), 2)); ?></td>
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
                Total: ₹ <?php echo e(number_format((float) ($displayGrandTotal ?? $purchase->total_amount ?? 0), 2)); ?>

            </h4>
        </div>

        
        <div class="mt-3">
            <label class="text-muted">Invoice File</label><br>

            <?php if($purchase->po_invoice_file): ?>
                <a href="<?php echo e(route('finance.purchases.download-source-pdf', $purchase->id)); ?>"
                   download
                   class="btn btn-outline-primary btn-sm mt-1">
                   <i class="bi bi-download"></i> Download Source File
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