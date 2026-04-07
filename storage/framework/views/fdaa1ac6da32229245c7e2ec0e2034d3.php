

<?php $__env->startSection('title', 'Purchase Invoices'); ?>

<?php $__env->startSection('content'); ?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Purchase Invoice Automation</h2>
        <div class="d-flex gap-2 align-items-center">
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

    <?php if(session('success')): ?>
        <div class="alert alert-success">
            <pre style="margin:0;white-space:pre-wrap;"><?php echo e(session('success')); ?></pre>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark-primary">
                    <tr>
                        <th>#</th>
                        <th>Vendor</th>
                        <th>GSTIN</th>
                        <th>Invoice No</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Confidence</th>
                        <th>Status</th>
                        <th width="250">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($loop->iteration); ?></td>

                            <td>
                                <?php echo e(data_get($purchase->raw_json, 'vendor_name') ?? $purchase->vendor_name_raw ?? $purchase->vendor_name ?? optional($purchase->vendor)->vendor_name ?? '-'); ?>

                            </td>

                            <td><?php echo e($purchase->gstin ?? $purchase->gst_number ?? $purchase->vendor_gstin ?? '-'); ?></td>

                            <td>
                                <?php
                                    $invoiceNoDisplay = trim((string) ($purchase->invoice_no ?? ''));
                                    $rawInvoiceNo = trim((string) data_get($purchase->raw_json, 'invoice_number', ''));
                                    if ($invoiceNoDisplay !== '' && str_starts_with(strtoupper($invoiceNoDisplay), 'GMAIL-') && $rawInvoiceNo !== '') {
                                        $invoiceNoDisplay = $rawInvoiceNo;
                                    }
                                ?>
                                <?php echo e($invoiceNoDisplay !== '' ? $invoiceNoDisplay : '-'); ?>

                            </td>

                            <td>
                                <?php echo e($purchase->invoice_date 
                                    ? \Carbon\Carbon::parse($purchase->invoice_date)->format('d-m-Y') 
                                    : '-'); ?>

                            </td>

                            <td>
                                ₹ <?php echo e(number_format($purchase->total_amount, 2)); ?>

                            </td>

                            <td>
                                <?php
                                    $accuracy = $purchase->confidence_score;

                                    if ((is_null($accuracy) || (float) $accuracy <= 0) && is_array($purchase->raw_json)) {
                                        $accuracy = data_get($purchase->raw_json, 'matching.combined_confidence');
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
                                <?php
                                    $st = strtolower($purchase->status ?? '');
                                ?>
                                <?php if($st === 'needs_review'): ?>
                                    <form action="<?php echo e(route('finance.purchases.approve', $purchase->id)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-success"
                                                onclick="return confirm('Approve this invoice?')">
                                            <i class="bi bi-check-circle"></i> Approve
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <?php
                                        $statusMap = [
                                            'ok'       => ['label' => 'OK',       'class' => 'bg-success'],
                                            'higher'   => ['label' => 'Higher',   'class' => 'bg-danger'],
                                            'lower'    => ['label' => 'Lower',    'class' => 'bg-warning text-dark'],
                                            'verified' => ['label' => 'Verified', 'class' => 'bg-info text-dark'],
                                            'approved' => ['label' => 'Approved', 'class' => 'bg-primary'],
                                            'paid'     => ['label' => 'Paid',     'class' => 'bg-dark'],
                                            'failed'   => ['label' => 'Failed',   'class' => 'bg-danger'],
                                        ];
                                        $statusInfo = $statusMap[$st] ?? ['label' => ucfirst($st ?: 'Pending'), 'class' => 'bg-secondary'];
                                    ?>
                                    <span class="badge <?php echo e($statusInfo['class']); ?>"><?php echo e($statusInfo['label']); ?></span>
                                <?php endif; ?>
                            </td>

                            <td>

                                
                                <a href="<?php echo e(route('finance.purchases.show', $purchase->id)); ?>"
                                   class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>

                                
                                <a href="<?php echo e(route('finance.purchases.edit', $purchase->id)); ?>"
                                   class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <!-- 
                                <a href="<?php echo e(route('finance.purchases.pdf', $purchase->id)); ?>"
                                   class="btn btn-sm btn-secondary"
                                   target="_blank">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a> -->

                                
                                <form action="<?php echo e(route('finance.purchases.destroy', $purchase->id)); ?>"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this invoice?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>

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
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/finance/purchases/index.blade.php ENDPATH**/ ?>