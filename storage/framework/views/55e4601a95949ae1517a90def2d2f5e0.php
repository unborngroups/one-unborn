

<?php $__env->startSection('title', 'Purchase Invoices'); ?>

<?php $__env->startSection('content'); ?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Purchase Invoice Automation</h4>

        <div>
            <a href="<?php echo e(route('finance.purchase_invoices.index', ['status' => 'needs_review'])); ?>"
               class="btn btn-warning btn-sm">
                Needs Review
            </a>

            <a href="<?php echo e(route('finance.purchase_invoices.index', ['status' => 'verified'])); ?>"
               class="btn btn-info btn-sm">
                Verified
            </a>

            <a href="<?php echo e(route('finance.purchase_invoices.index', ['status' => 'approved'])); ?>"
               class="btn btn-success btn-sm">
                Approved
            </a>

            <a href="<?php echo e(route('finance.purchase_invoices.index', ['status' => 'paid'])); ?>"
               class="btn btn-dark btn-sm">
                Paid
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Vendor</th>
                        <th>GSTIN</th>
                        <th>Invoice No</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Confidence</th>
                        <th>Status</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($loop->iteration); ?></td>

                            <td>
                                <?php echo e($invoice->vendor_name_raw); ?>

                                <?php if($invoice->vendor_id): ?>
                                    <span class="badge bg-success">Mapped</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Unmapped</span>
                                <?php endif; ?>
                            </td>

                            <td><?php echo e($invoice->gstin ?? '-'); ?></td>

                            <td><?php echo e($invoice->invoice_number); ?></td>

                            <td>
                                <?php echo e($invoice->invoice_date
                                    ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y')
                                    : '-'); ?>

                            </td>

                            <td>₹ <?php echo e(number_format($invoice->total_amount, 2)); ?></td>

                            <td>
                                <span class="badge 
                                    <?php if($invoice->confidence_score >= 80): ?>
                                        bg-success
                                    <?php elseif($invoice->confidence_score >= 50): ?>
                                        bg-warning
                                    <?php else: ?>
                                        bg-danger
                                    <?php endif; ?>">
                                    <?php echo e($invoice->confidence_score); ?>%
                                </span>
                            </td>

                            <td>
                                <span class="badge 
                                    <?php switch($invoice->status):
                                        case ('draft'): ?> bg-secondary <?php break; ?>
                                        <?php case ('needs_review'): ?> bg-warning <?php break; ?>
                                        <?php case ('verified'): ?> bg-info <?php break; ?>
                                        <?php case ('approved'): ?> bg-success <?php break; ?>
                                        <?php case ('paid'): ?> bg-dark <?php break; ?>
                                        <?php case ('duplicate'): ?> bg-danger <?php break; ?>
                                    <?php endswitch; ?>">
                                    <?php echo e(ucfirst(str_replace('_',' ', $invoice->status))); ?>

                                </span>
                            </td>

                            <td>

                                <a href="<?php echo e(route('finance.purchase_invoices.show', $invoice->id)); ?>"
                                   class="btn btn-primary btn-sm">
                                    View
                                </a>

                                <?php if($invoice->status == 'needs_review'): ?>
                                    <form action="<?php echo e(route('finance.purchase_invoices.verify', $invoice->id)); ?>"
                                          method="POST"
                                          class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button class="btn btn-info btn-sm">
                                            Verify
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if($invoice->status == 'verified'): ?>
                                    <form action="<?php echo e(route('finance.purchase_invoices.approve', $invoice->id)); ?>"
                                          method="POST"
                                          class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button class="btn btn-success btn-sm">
                                            Approve
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if($invoice->status == 'approved'): ?>
                                    <form action="<?php echo e(route('finance.purchase_invoices.markPaid', $invoice->id)); ?>"
                                          method="POST"
                                          class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button class="btn btn-dark btn-sm">
                                            Mark Paid
                                        </button>
                                    </form>
                                <?php endif; ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="text-center">
                                No invoices found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\purchase_invoices\index.blade.php ENDPATH**/ ?>