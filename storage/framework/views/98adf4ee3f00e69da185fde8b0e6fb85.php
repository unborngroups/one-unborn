<?php $__env->startSection('title', 'Purchase Invoice - ' . ($invoice->invoice_no ?? 'View')); ?>

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
        <div>
            <h4 class="mb-0">Purchase Invoice Details</h4>
            <small class="text-muted">Auto-received from email</small>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?php echo e(route('finance.purchase_invoices.edit', $invoice->id)); ?>"
               class="btn btn-warning btn-sm">
                ✏ Edit
            </a>

            <?php if($invoice->po_invoice_file): ?>
                <a href="<?php echo e(asset('images/poinvoice_files/' . $invoice->po_invoice_file)); ?>"
                   download="<?php echo e($invoice->invoice_no ?? 'invoice_' . $invoice->id . '.pdf'); ?>"
                   target="_blank"
                   class="btn btn-success btn-sm">
                    <i class="bi bi-download me-1"></i>
                    Download Invoice PDF
                </a>
            <?php endif; ?>

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
                <div class="card-header py-3 fw-semibold d-flex justify-content-between align-items-center">
                    <div>
                        <span class="me-3">Invoice Information</span>
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
                    <?php if($invoice->po_invoice_file): ?>
                        <div>
                            <span class="badge bg-light text-dark me-2">
                                <i class="bi bi-file-earmark-pdf me-1"></i>
                                PDF Attached
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-building text-primary me-2"></i>
                                <p class="text-muted mb-0 small me-2">Vendor (Master)</p>
                            </div>
                            <p class="fw-semibold mb-3 p-2 bg-light rounded">
                                <?php echo e(optional($invoice->vendor)->vendor_name ?? '—'); ?>

                            </p>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-person-badge text-info me-2"></i>
                                <p class="text-muted mb-0 small me-2">Vendor Name (from invoice)</p>
                            </div>
                            <p class="fw-semibold mb-3 p-2 bg-light rounded">
                                <?php echo e($invoice->vendor_name ?? $invoice->vendor_name_raw ?? '—'); ?>

                            </p>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-receipt text-success me-2"></i>
                                <p class="text-muted mb-0 small me-2">GSTIN</p>
                            </div>
                            <p class="fw-semibold mb-3 p-2 bg-light rounded font-monospace">
                                <?php echo e($invoice->gstin ?? $invoice->vendor_gstin ?? $invoice->gst_number ?? '—'); ?>

                            </p>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-upc-scan text-warning me-2"></i>
                                <p class="text-muted mb-0 small me-2">Invoice Number</p>
                            </div>
                            <p class="fw-semibold mb-3 p-2 bg-light rounded">
                                <?php echo e($invoice->invoice_no ?? '—'); ?>

                            </p>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-calendar-date text-primary me-2"></i>
                                <p class="text-muted mb-0 small me-2">Invoice Date</p>
                            </div>
                            <p class="mb-0 p-2 bg-light rounded">
                                <?php echo e($invoice->invoice_date ? $invoice->invoice_date->format('d-m-Y') : '—'); ?>

                            </p>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-calendar-check text-danger me-2"></i>
                                <p class="text-muted mb-0 small me-2">Due Date</p>
                            </div>
                            <p class="mb-0 p-2 <?php if($invoice->due_date && $invoice->due_date->isPast()): ?> bg-warning <?php else: ?> bg-light <?php endif; ?> rounded">
                                <?php echo e($invoice->due_date ? $invoice->due_date->format('d-m-Y') : '—'); ?>

                                <?php if($invoice->due_date && $invoice->due_date->isPast()): ?>
                                    <span class="badge bg-danger ms-2">Overdue</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-award text-info me-2"></i>
                                <p class="text-muted mb-0 small me-2">Confidence</p>
                            </div>
                            <p class="mb-0 p-2 bg-light rounded">
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
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-envelope text-secondary me-2"></i>
                                <p class="text-muted mb-0 small me-2">Source</p>
                            </div>
                            <p class="mb-0 p-2 bg-light rounded">
                                <?php echo e(optional($invoice->emailLog)->source ?? 'email'); ?>

                            </p>
                        </div>
                        <?php if($invoice->notes): ?>
                        <div class="col-12">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-sticky text-warning me-2"></i>
                                <p class="text-muted mb-0 small me-2">Notes</p>
                            </div>
                            <p class="mb-0 p-3 bg-light rounded"><?php echo e($invoice->notes); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div class="card shadow-sm">
                <div class="card-header py-3 fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-currency-rupee me-2"></i>Amount Breakdown</span>
                    <span class="badge bg-light text-dark">
                        <?php echo e($invoice->currency ?? 'INR'); ?>

                    </span>
                </div>
                <div class="card-body p-3">
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted ps-3">
                                    <i class="bi bi-receipt-cutoff text-primary me-2"></i>
                                    Sub Total
                                </td>
                                <td class="text-end pe-3 fw-semibold">
                                    ₹ <?php echo e(number_format($invoice->amount ?? 0, 2)); ?>

                                </td>
                            </tr>
                            <?php if(($invoice->cgst_total ?? 0) > 0): ?>
                            <tr>
                                <td class="text-muted ps-3">
                                    <i class="bi bi-calculator text-info me-2"></i>
                                    CGST
                                </td>
                                <td class="text-end pe-3 fw-semibold">
                                    ₹ <?php echo e(number_format($invoice->cgst_total, 2)); ?>

                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php if(($invoice->sgst_total ?? 0) > 0): ?>
                            <tr>
                                <td class="text-muted ps-3">
                                    <i class="bi bi-calculator text-success me-2"></i>
                                    SGST / IGST
                                </td>
                                <td class="text-end pe-3 fw-semibold">
                                    ₹ <?php echo e(number_format($invoice->sgst_total, 2)); ?>

                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php if(($invoice->tax_amount ?? 0) > 0): ?>
                            <tr>
                                <td class="text-muted ps-3">
                                    <i class="bi bi-percent text-warning me-2"></i>
                                    Tax Amount
                                </td>
                                <td class="text-end pe-3 fw-semibold">
                                    ₹ <?php echo e(number_format($invoice->tax_amount, 2)); ?>

                                </td>
                            </tr>
                            <?php endif; ?>
                            <tr class="table-success fw-bold">
                                <td class="ps-3">
                                    <i class="bi bi-cash-stack text-success me-2"></i>
                                    Grand Total
                                </td>
                                <td class="text-end pe-3 fw-bold fs-5 text-success">
                                    ₹ <?php echo e(number_format($invoice->grand_total ?? $invoice->total_amount ?? 0, 2)); ?>

                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
        <div class="col-lg-4">

            <?php if($invoice->po_invoice_file): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-file-earmark-pdf me-2"></i>Invoice Attachment</span>
                    <span class="badge bg-primary">PDF</span>
                </div>
                <div class="card-body text-center p-3">
                    <div class="d-grid gap-2">
                        <a href="<?php echo e(route('finance.purchases.download-source-pdf', $invoice->id)); ?>"
                           target="_blank"
                           class="btn btn-outline-primary">
                            <i class="bi bi-eye me-2"></i>
                            View Invoice PDF
                        </a>
                        <a href="<?php echo e(asset('images/poinvoice_files/' . $invoice->po_invoice_file)); ?>"
                           download="<?php echo e($invoice->invoice_no ?? 'invoice_' . $invoice->id . '.pdf'); ?>"
                           target="_blank"
                           class="btn btn-primary">
                            <i class="bi bi-download me-2"></i>
                            Download Invoice PDF
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if(!empty($raw['matching'])): ?>
            <div class="card shadow-sm mb-4 border-info">
                <div class="card-header py-3 fw-semibold bg-info text-white d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-people-fill me-2"></i>Vendor Match Details</span>
                    <span class="badge bg-light text-dark">
                        Score: <?php echo e($raw['matching']['combined_confidence'] ?? 0); ?>%
                    </span>
                </div>
                <div class="card-body small">
                    <div class="mb-3">
                        <?php
                            $combinedScore = $raw['matching']['combined_confidence'] ?? 0;
                            $scoreColor = $combinedScore >= 80 ? 'success' : ($combinedScore >= 50 ? 'warning' : 'danger');
                        ?>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-<?php echo e($scoreColor); ?>" role="progressbar" 
                                 style="width: <?php echo e($combinedScore); ?>%" 
                                 aria-valuenow="<?php echo e($combinedScore); ?>" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted ps-3">
                                <i class="bi bi-search me-2"></i>Matched By
                            </td>
                            <td class="fw-semibold"><?php echo e($raw['matching']['matched_by'] ?? '—'); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">
                                <i class="bi bi-cpu me-2"></i>Parser Score
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    <?php echo e($raw['matching']['parser_confidence'] ?? '—'); ?>%
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">
                                <i class="bi bi-person-check me-2"></i>Vendor Match
                            </td>
                            <td>
                                <span class="badge 
                                    <?php if(($raw['matching']['vendor_match_score'] ?? 0) >= 80): ?>
                                        bg-success
                                    <?php elseif(($raw['matching']['vendor_match_score'] ?? 0) >= 50): ?>
                                        bg-warning text-dark
                                    <?php else: ?>
                                        bg-danger
                                    <?php endif; ?>">
                                    <?php echo e($raw['matching']['vendor_match_score'] ?? '—'); ?>%
                                </span>
                            </td>
                        </tr>
                        <tr class="table-success">
                            <td class="text-muted ps-3 fw-bold">
                                <i class="bi bi-award-fill me-2"></i>Combined Score
                            </td>
                            <td class="fw-bold fs-5">
                                <span class="badge 
                                    <?php if($combinedScore >= 80): ?>
                                        bg-success
                                    <?php elseif($combinedScore >= 50): ?>
                                        bg-warning text-dark
                                    <?php else: ?>
                                        bg-danger
                                    <?php endif; ?>">
                                    <?php echo e($combinedScore); ?>%
                                </span>
                            </td>
                        </tr>
                        <?php if($raw['matching']['vendor_master_name'] ?? null): ?>
                        <tr>
                            <td class="text-muted ps-3">
                                <i class="bi bi-building me-2"></i>Master Name
                            </td>
                            <td class="fw-semibold"><?php echo e($raw['matching']['vendor_master_name']); ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <?php if(!empty($raw)): ?>
            <div class="card shadow-sm border-secondary">
                <div class="card-header py-3 fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-file-text me-2"></i>Raw OCR Data</span>
                    <small class="text-muted ms-2 fw-normal">(as extracted)</small>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
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
                                    <tr class="border-bottom">
                                        <td class="ps-3 py-2 text-muted small fw-semibold">
                                            <i class="bi bi-tag me-2"></i><?php echo e($label); ?>

                                        </td>
                                        <td class="py-2 small font-monospace"><?php echo e($raw[$key]); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\one-unborn-main\resources\views/finance/purchase_invoices/show.blade.php ENDPATH**/ ?>