

<?php $__env->startSection('title', 'Purchase Invoice - ' . ($invoice->invoice_no ?? 'View')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Purchase Invoice Details</h4>
            <small class="text-muted">Auto-received from email</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('finance.purchase_invoices.edit', $invoice->id)); ?>"
               class="btn btn-warning btn-sm">
                ✏ Edit
            </a>

            <?php if($invoice->status == 'needs_review'): ?>
                <form action="<?php echo e(route('finance.purchase_invoices.verify', $invoice->id)); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button class="btn btn-info btn-sm">Verify</button>
                </form>
            <?php endif; ?>

            <?php if($invoice->status == 'verified'): ?>
                <form action="<?php echo e(route('finance.purchase_invoices.approve', $invoice->id)); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button class="btn btn-success btn-sm">Approve</button>
                </form>
            <?php endif; ?>

            <?php if($invoice->status == 'approved'): ?>
                <form action="<?php echo e(route('finance.purchase_invoices.markPaid', $invoice->id)); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button class="btn btn-dark btn-sm">Mark Paid</button>
                </form>
            <?php endif; ?>

            <a href="<?php echo e(route('finance.purchase_invoices.index')); ?>" class="btn btn-outline-secondary btn-sm">
                &larr; Back
            </a>
        </div>
    </div>

    <div class="row g-4">

        
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-2 fw-semibold d-flex justify-content-between">
                    <span>Invoice Information</span>
                    <span class="badge
                        <?php switch($invoice->status):
                            case ('draft'): ?> bg-secondary <?php break; ?>
                            <?php case ('needs_review'): ?> bg-warning text-dark <?php break; ?>
                            <?php case ('verified'): ?> bg-info <?php break; ?>
                            <?php case ('approved'): ?> bg-success <?php break; ?>
                            <?php case ('paid'): ?> bg-dark <?php break; ?>
                            <?php case ('duplicate'): ?> bg-danger <?php break; ?>
                            <?php default: ?> bg-secondary
                        <?php endswitch; ?>">
                        <?php echo e(ucfirst(str_replace('_',' ', $invoice->status))); ?>

                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="text-muted mb-1 small">Vendor (Master)</p>
                            <p class="fw-semibold mb-0">
                                <?php echo e(optional($invoice->vendor)->vendor_name ?? '—'); ?>

                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1 small">Vendor Name (from invoice)</p>
                            <p class="fw-semibold mb-0">
                                <?php echo e($invoice->vendor_name ?? $invoice->vendor_name_raw ?? '—'); ?>

                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1 small">GSTIN</p>
                            <p class="fw-semibold mb-0 font-monospace">
                                <?php echo e($invoice->gstin ?? $invoice->vendor_gstin ?? $invoice->gst_number ?? '—'); ?>

                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1 small">Invoice Number</p>
                            <p class="fw-semibold mb-0"><?php echo e($invoice->invoice_no ?? '—'); ?></p>
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted mb-1 small">Invoice Date</p>
                            <p class="mb-0">
                                <?php echo e($invoice->invoice_date ? $invoice->invoice_date->format('d-m-Y') : '—'); ?>

                            </p>
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted mb-1 small">Due Date</p>
                            <p class="mb-0">
                                <?php echo e($invoice->due_date ? $invoice->due_date->format('d-m-Y') : '—'); ?>

                            </p>
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted mb-1 small">Confidence</p>
                            <p class="mb-0">
                                <span class="badge
                                    <?php if(($invoice->confidence_score ?? 0) >= 80): ?>
                                        bg-success
                                    <?php elseif(($invoice->confidence_score ?? 0) >= 50): ?>
                                        bg-warning text-dark
                                    <?php else: ?>
                                        bg-danger
                                    <?php endif; ?>">
                                    <?php echo e($invoice->confidence_score ?? 0); ?>%
                                </span>
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted mb-1 small">Source</p>
                            <p class="mb-0">
                                <?php echo e(optional($invoice->emailLog)->source ?? 'email'); ?>

                            </p>
                        </div>
                        <?php if($invoice->notes): ?>
                        <div class="col-12">
                            <p class="text-muted mb-1 small">Notes</p>
                            <p class="mb-0"><?php echo e($invoice->notes); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div class="card shadow-sm">
                <div class="card-header py-2 fw-semibold">Amount Breakdown</div>
                <div class="card-body p-0">
                    <table class="table table-sm table-bordered mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted">Sub Total</td>
                                <td class="text-end">₹ <?php echo e(number_format($invoice->amount ?? 0, 2)); ?></td>
                            </tr>
                            <?php if(($invoice->cgst_total ?? 0) > 0): ?>
                            <tr>
                                <td class="text-muted">CGST</td>
                                <td class="text-end">₹ <?php echo e(number_format($invoice->cgst_total, 2)); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if(($invoice->sgst_total ?? 0) > 0): ?>
                            <tr>
                                <td class="text-muted">SGST / IGST</td>
                                <td class="text-end">₹ <?php echo e(number_format($invoice->sgst_total, 2)); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if(($invoice->tax_amount ?? 0) > 0): ?>
                            <tr>
                                <td class="text-muted">Tax Amount</td>
                                <td class="text-end">₹ <?php echo e(number_format($invoice->tax_amount, 2)); ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr class="table-success fw-bold">
                                <td>Grand Total</td>
                                <td class="text-end">₹ <?php echo e(number_format($invoice->grand_total ?? $invoice->total_amount ?? 0, 2)); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
        <div class="col-lg-4">

            <?php if($invoice->po_invoice_file): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header py-2 fw-semibold">Invoice Attachment</div>
                <div class="card-body text-center">
                    <a href="<?php echo e(Storage::url($invoice->po_invoice_file)); ?>"
                       target="_blank"
                       class="btn btn-outline-primary w-100">
                        View Invoice PDF
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <?php if(!empty($raw['matching'])): ?>
            <div class="card shadow-sm mb-4 border-info">
                <div class="card-header py-2 fw-semibold bg-info text-white">
                    Vendor Match Details
                </div>
                <div class="card-body small">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Matched By</td>
                            <td><?php echo e($raw['matching']['matched_by'] ?? '—'); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Parser Score</td>
                            <td><?php echo e($raw['matching']['parser_confidence'] ?? '—'); ?>%</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Vendor Match</td>
                            <td><?php echo e($raw['matching']['vendor_match_score'] ?? '—'); ?>%</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Combined</td>
                            <td class="fw-bold"><?php echo e($raw['matching']['combined_confidence'] ?? '—'); ?>%</td>
                        </tr>
                        <?php if($raw['matching']['vendor_master_name'] ?? null): ?>
                        <tr>
                            <td class="text-muted">Master Name</td>
                            <td><?php echo e($raw['matching']['vendor_master_name']); ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <?php if(!empty($raw)): ?>
            <div class="card shadow-sm border-secondary">
                <div class="card-header py-2 fw-semibold">
                    Raw OCR Data
                    <small class="text-muted ms-2 fw-normal">(as extracted)</small>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            <?php $__currentLoopData = [
                                'vendor_name'    => 'Vendor Name',
                                'gstin'          => 'GSTIN',
                                'invoice_number' => 'Invoice No',
                                'invoice_date'   => 'Invoice Date',
                                'amount'         => 'Sub Total',
                                'tax'            => 'Tax',
                                'total'          => 'Grand Total',
                            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(!empty($raw[$key])): ?>
                                <tr>
                                    <td class="ps-3 py-1 text-muted small"><?php echo e($label); ?></td>
                                    <td class="py-1 small"><?php echo e($raw[$key]); ?></td>
                                </tr>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\purchase_invoices\show.blade.php ENDPATH**/ ?>