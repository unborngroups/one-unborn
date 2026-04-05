

<?php $__env->startSection('title', 'Purchase Invoices'); ?>

<?php $__env->startSection('content'); ?>

<div class="container-fluid">

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Purchase Invoices</h2>

        <div class="d-flex gap-2 align-items-center">
            <a href="<?php echo e(route('finance.purchases.create')); ?>"
               class="btn btn-sm btn-info p-2 text-white">
                <h2 class="mb-0">+ create invoice</h2>
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark-primary">
                    <tr>
                        <th>#</th>
                        <th>Invoice No</th>
                        <th>Vendor</th>
                        <th>GSTIN</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Accuracy</th>
                        <th>Status</th>
                        <th width="250">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($loop->iteration); ?></td>

                            <td>
                                <?php
                                    $invoiceNoDisplay = trim((string) ($invoice->invoice_no ?? ''));
                                    $rawInvoiceNo = trim((string) data_get($invoice->raw_json, 'invoice_number', ''));
                                    if ($invoiceNoDisplay !== '' && str_starts_with(strtoupper($invoiceNoDisplay), 'GMAIL-') && $rawInvoiceNo !== '') {
                                        $invoiceNoDisplay = $rawInvoiceNo;
                                    }
                                ?>
                                <?php echo e($invoiceNoDisplay !== '' ? $invoiceNoDisplay : '-'); ?>

                            </td>

                            <td>
                                <?php echo e(data_get($invoice->raw_json, 'vendor_name') ?? $invoice->vendor_name_raw ?? $invoice->vendor_name ?? optional($invoice->vendor)->vendor_name ?? '-'); ?>

                            </td>

                            <td><?php echo e($invoice->gstin ?? $invoice->gst_number ?? $invoice->vendor_gstin ?? '-'); ?></td>

                            <td>
                                <?php echo e($invoice->invoice_date
                                    ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y')
                                    : '-'); ?>

                            </td>

                            <td>₹ <?php echo e(number_format($invoice->total_amount ?? $invoice->grand_total ?? $invoice->amount ?? 0, 2)); ?></td>

                            <td>
                                <?php
                                    $accuracy = $invoice->confidence_score;

                                    if ((is_null($accuracy) || (float) $accuracy <= 0) && is_array($invoice->raw_json)) {
                                        $accuracy = data_get($invoice->raw_json, 'matching.combined_confidence');
                                    }
                                ?>

                                <?php if(!is_null($accuracy) && (float) $accuracy > 0): ?>
                                    <span class="badge 
                                        <?php if($accuracy >= 80): ?>
                                            bg-success
                                        <?php elseif($accuracy >= 50): ?>
                                            bg-warning text-dark
                                        <?php else: ?>
                                            bg-danger
                                        <?php endif; ?>">
                                        <?php echo e(rtrim(rtrim(number_format($accuracy, 2), '0'), '.')); ?>%
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
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
                                   class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="<?php echo e(route('finance.purchase_invoices.edit', $invoice->id)); ?>"
                                   class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <?php if($invoice->status == 'needs_review'): ?>
                                    <form action="<?php echo e(route('finance.purchase_invoices.verify', $invoice->id)); ?>"
                                          method="POST"
                                          class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button class="btn btn-sm btn-info">
                                            <i class="bi bi-check2-square"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if($invoice->status == 'verified'): ?>
                                    <form action="<?php echo e(route('finance.purchase_invoices.approve', $invoice->id)); ?>"
                                          method="POST"
                                          class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button class="btn btn-sm btn-success">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if($invoice->status == 'approved'): ?>
                                    <form action="<?php echo e(route('finance.purchase_invoices.markPaid', $invoice->id)); ?>"
                                          method="POST"
                                          class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button class="btn btn-sm btn-dark">
                                            <i class="bi bi-cash-stack"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="text-center">
                                No Purchase Invoices Found.
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