

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

    <div class="d-flex justify-content-between align-items-center mb-3">
        
        
    </div>

    <?php if(!empty($lastFetchStatus)): ?>
    <div class="alert alert-<?php echo e($lastFetchStatus['level'] === 'error' ? 'danger' : ($lastFetchStatus['level'] === 'success' ? 'success' : 'info')); ?> py-2 mb-3" role="alert">
        <div class="fw-semibold">Mail Fetch Status: <?php echo e($lastFetchStatus['message'] ?? 'Mail checked.'); ?></div>
        <?php if(!empty($lastFetchStatus['checked_at'])): ?>
        <div class="small mt-1">
            Checked at: <?php echo e(\Carbon\Carbon::parse($lastFetchStatus['checked_at'])->format('d-M-Y h:i A')); ?>

        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="mb-2">
        <h4 class="mb-0">
            <?php if(request('status') === 'failed'): ?>
                Failed Invoices
            <?php elseif(request('status') === 'needs_review'): ?>
                Needs Review
            <?php elseif(request('status') === 'verified'): ?>
                Verified Invoices
            <?php elseif(request('status') === 'approved'): ?>
                Approved Invoices
            <?php elseif(request('status') === 'paid'): ?>
                Paid Invoices
            <?php else: ?>
                Purchase Invoice Automation
            <?php endif; ?>
        </h4>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-2 mb-4">

            <?php if(isset($lastMailReadAt) && $lastMailReadAt): ?>
            <span class="text-muted small me-2">
                <i class="bi bi-envelope-check"></i>
                Last mail read: <strong><?php echo e(\Carbon\Carbon::parse($lastMailReadAt)->format('d-M-Y h:i A')); ?></strong>
            </span>
            <?php else: ?>
            <span class="text-muted small me-2"><i class="bi bi-envelope"></i> No mail read yet</span>
            <?php endif; ?>

            <a href="<?php echo e(route('finance.purchase_invoices.index', ['status' => 'needs_review'])); ?>"
               class="btn btn-warning btn-sm <?php echo e(request('status') === 'needs_review' ? 'active' : ''); ?>">
                Needs Review
            </a>

            <a href="<?php echo e(route('finance.purchase_invoices.index', ['status' => 'verified'])); ?>"
               class="btn btn-info btn-sm <?php echo e(request('status') === 'verified' ? 'active' : ''); ?>">
                Verified
            </a>

            <a href="<?php echo e(route('finance.purchase_invoices.index', ['status' => 'approved'])); ?>"
               class="btn btn-success btn-sm <?php echo e(request('status') === 'approved' ? 'active' : ''); ?>">
                Approved
            </a>

            <a href="<?php echo e(route('finance.purchase_invoices.index', ['status' => 'paid'])); ?>"
               class="btn btn-dark btn-sm <?php echo e(request('status') === 'paid' ? 'active' : ''); ?>">
                Paid
            </a>

            <a href="<?php echo e(route('finance.purchase_invoices.index', ['status' => 'failed'])); ?>"
                    class="btn btn-danger btn-sm <?php echo e(request('status') === 'failed' ? 'active' : ''); ?>">
                Failed
            </a>

            <a href="<?php echo e(route('finance.purchase_invoices.index')); ?>

               class="btn btn-secondary btn-sm <?php echo e(request('status') === null ? 'active' : ''); ?>">
                All
            </a>

            <button type="button" class="btn btn-primary btn-sm" id="fetchNowBtn" onclick="fetchNow()">
                <i class="bi bi-envelope-arrow-down" id="fetchIcon"></i>
                <span class="spinner-border spinner-border-sm d-none" id="fetchSpinner" role="status"></span>
                <span id="fetchBtnText">Fetch Now</span>
            </button>

    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark-primary">
                    <tr>
                        <?php if(request('status') === 'failed'): ?>
                            <th>#</th>
                            <th>Vendor Name</th>
                            <th>GST Number</th>
                            <th>Failure Details</th>
                        <?php else: ?>
                            <th>#</th>
                            <th>Invoice No</th>
                            <th>Vendor</th>
                            <th>GSTIN</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Accuracy</th>
                            <th>Status</th>
                            <th width="250">Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>

                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($loop->iteration); ?></td>

                            <?php
                                $invoiceNoDisplay = trim((string) ($invoice->invoice_no ?? ''));
                                $rawInvoiceNo = trim((string) data_get($invoice->raw_json, 'invoice_number', ''));
                                if ($invoiceNoDisplay !== '' && str_starts_with(strtoupper($invoiceNoDisplay), 'GMAIL-') && $rawInvoiceNo !== '') {
                                    $invoiceNoDisplay = $rawInvoiceNo;
                                }
                            ?>

                            <?php if(request('status') !== 'failed'): ?>
                            <td>
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
                                        <?php case ('failed'): ?> bg-danger <?php break; ?>
                                    <?php endswitch; ?>">
                                    <?php echo e(ucfirst(str_replace('_',' ', $invoice->status))); ?>

                                </span>
                            </td>
                            <?php endif; ?>

                            <?php if(request('status') === 'failed'): ?>
                            <td>
                                <?php echo e(data_get($invoice->raw_json, 'vendor_name') ?? $invoice->vendor_name_raw ?? $invoice->vendor_name ?? optional($invoice->vendor)->vendor_name ?? '-'); ?>

                            </td>

                            <td><?php echo e($invoice->gstin ?? $invoice->gst_number ?? $invoice->vendor_gstin ?? '-'); ?></td>

                            <td>
                                <?php
                                    $failureReason = trim((string) (
                                        data_get($invoice->raw_json, 'import_failure_reason')
                                        ?? data_get($invoice->raw_json, 'parse_error')
                                        ?? data_get($invoice->raw_json, 'error')
                                        ?? optional($invoice->emailLog)->error_message
                                        ?? ''
                                    ));

                                    $failureStage = trim((string) (data_get($invoice->raw_json, 'failure_stage') ?? ''));
                                    $failureSource = trim((string) (data_get($invoice->raw_json, 'failure_source') ?? ''));
                                ?>

                                <?php if($failureReason !== ''): ?>
                                    <div class="small text-danger fw-semibold"><?php echo e($failureReason); ?></div>
                                    <?php if($failureStage !== '' || $failureSource !== ''): ?>
                                        <div class="small text-muted">
                                            <?php echo e($failureStage !== '' ? 'Stage: ' . ucfirst($failureStage) : ''); ?>

                                            <?php echo e($failureSource !== '' ? ($failureStage !== '' ? ' | ' : '') . 'Source: ' . str_replace('_', ' ', ucfirst($failureSource)) : ''); ?>

                                        </div>
                                    <?php endif; ?>
                                    <button class="btn btn-link btn-sm text-danger" data-bs-toggle="modal" data-bs-target="#failureModal<?php echo e($invoice->id); ?>">
                                        <i class="bi bi-info-circle"></i> Full Details
                                    </button>

                                    <!-- Failure Details Modal -->
                                    <div class="modal fade" id="failureModal<?php echo e($invoice->id); ?>" tabindex="-1" aria-labelledby="failureModalLabel<?php echo e($invoice->id); ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="failureModalLabel<?php echo e($invoice->id); ?>">Invoice Failure Details - <?php echo e($invoiceNoDisplay); ?></h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label class="fw-semibold">Invoice No:</label>
                                                            <p><?php echo e($invoiceNoDisplay !== '' ? $invoiceNoDisplay : '-'); ?></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="fw-semibold">Status:</label>
                                                            <p><span class="badge bg-danger"><?php echo e(ucfirst(str_replace('_', ' ', $invoice->status))); ?></span></p>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label class="fw-semibold">Vendor:</label>
                                                            <p><?php echo e(data_get($invoice->raw_json, 'vendor_name') ?? $invoice->vendor_name_raw ?? $invoice->vendor_name ?? optional($invoice->vendor)->vendor_name ?? '-'); ?></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="fw-semibold">GSTIN:</label>
                                                            <p><?php echo e($invoice->gstin ?? $invoice->gst_number ?? $invoice->vendor_gstin ?? '-'); ?></p>
                                                        </div>
                                                    </div>

                                                    <hr>

                                                    <h6 class="fw-bold text-danger mb-3">Failure Information</h6>

                                                    <div class="mb-3">
                                                        <label class="fw-semibold">Primary Reason:</label>
                                                        <p class="text-danger"><?php echo e($failureReason !== '' ? $failureReason : '-'); ?></p>
                                                    </div>

                                                    <?php if($failureStage !== ''): ?>
                                                        <div class="mb-3">
                                                            <label class="fw-semibold">Failure Stage:</label>
                                                            <p><?php echo e(ucfirst($failureStage)); ?></p>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if($failureSource !== ''): ?>
                                                        <div class="mb-3">
                                                            <label class="fw-semibold">Failure Source:</label>
                                                            <p><?php echo e(str_replace('_', ' ', ucfirst($failureSource))); ?></p>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if(optional($invoice->emailLog)->sender): ?>
                                                        <div class="mb-3">
                                                            <label class="fw-semibold">Email From:</label>
                                                            <p><?php echo e(optional($invoice->emailLog)->sender); ?></p>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if(optional($invoice->emailLog)->subject): ?>
                                                        <div class="mb-3">
                                                            <label class="fw-semibold">Email Subject:</label>
                                                            <p><?php echo e(optional($invoice->emailLog)->subject); ?></p>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php
                                                        $attachmentPath = trim((string) (optional($invoice->emailLog)->attachment_path ?? ''));
                                                        if ($attachmentPath === '' && !empty($invoice->po_invoice_file)) {
                                                            $attachmentPath = 'images/poinvoice_files/' . $invoice->po_invoice_file;
                                                        }

                                                        $attachmentName = $attachmentPath !== '' ? basename($attachmentPath) : '-';
                                                        $attachmentExt = strtolower((string) pathinfo($attachmentName, PATHINFO_EXTENSION));
                                                        $attachmentType = match ($attachmentExt) {
                                                            'pdf' => 'PDF',
                                                            'txt', 'log' => 'Text',
                                                            'csv', 'xls', 'xlsx' => 'Excel',
                                                            default => ($attachmentExt !== '' ? strtoupper($attachmentExt) : '-'),
                                                        };
                                                    ?>

                                                    <div class="mb-3">
                                                        <label class="fw-semibold">Attachment Type:</label>
                                                        <p><?php echo e($attachmentType); ?></p>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="fw-semibold">Attachment File:</label>
                                                        <p><?php echo e($attachmentName); ?></p>
                                                    </div>

                                                    <hr>

                                                    <?php
                                                        $rawJson = is_array($invoice->raw_json) ? $invoice->raw_json : [];
                                                        $readInvoiceNo = trim((string) (
                                                            data_get($rawJson, 'invoice_number')
                                                            ?? $invoice->invoice_no
                                                            ?? ''
                                                        ));
                                                        $readInvoiceDate = trim((string) (
                                                            data_get($rawJson, 'invoice_date')
                                                            ?? ($invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y') : '')
                                                        ));
                                                        $readVendor = trim((string) (
                                                            data_get($rawJson, 'vendor_name')
                                                            ?? $invoice->vendor_name_raw
                                                            ?? $invoice->vendor_name
                                                            ?? ''
                                                        ));
                                                        $readGstin = trim((string) (
                                                            data_get($rawJson, 'gst')
                                                            ?? data_get($rawJson, 'gstin')
                                                            ?? $invoice->gstin
                                                            ?? $invoice->gst_number
                                                            ?? $invoice->vendor_gstin
                                                            ?? ''
                                                        ));
                                                        $readArc = (float) (data_get($rawJson, 'arc', $invoice->arc_amount ?? 0));
                                                        $readOtc = (float) (data_get($rawJson, 'otc', $invoice->otc_amount ?? 0));
                                                        $readStatic = (float) (data_get($rawJson, 'static', $invoice->static_amount ?? 0));
                                                        $readTotal = (float) (data_get($rawJson, 'total', $invoice->total_amount ?? $invoice->grand_total ?? $invoice->amount ?? 0));
                                                        $readConfidence = data_get($rawJson, 'matching.combined_confidence', $invoice->confidence_score);
                                                    ?>

                                                    <h6 class="fw-bold mb-2">Invoice Read Data:</h6>
                                                    <div class="table-responsive" style="max-height: 260px; overflow-y: auto;">
                                                        <table class="table table-sm table-bordered mb-0">
                                                            <tbody>
                                                                <tr>
                                                                    <th style="width: 180px;">Invoice Number</th>
                                                                    <td><?php echo e($readInvoiceNo !== '' ? $readInvoiceNo : '-'); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Invoice Date</th>
                                                                    <td><?php echo e($readInvoiceDate !== '' ? $readInvoiceDate : '-'); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Vendor Read</th>
                                                                    <td><?php echo e($readVendor !== '' ? $readVendor : '-'); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>GST Read</th>
                                                                    <td><?php echo e($readGstin !== '' ? $readGstin : '-'); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>ARC Read</th>
                                                                    <td>₹ <?php echo e(number_format($readArc, 2)); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>OTC Read</th>
                                                                    <td>₹ <?php echo e(number_format($readOtc, 2)); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Static Read</th>
                                                                    <td>₹ <?php echo e(number_format($readStatic, 2)); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Total Read</th>
                                                                    <td>₹ <?php echo e(number_format($readTotal, 2)); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Read Accuracy</th>
                                                                    <td><?php echo e(!is_null($readConfidence) ? rtrim(rtrim(number_format((float) $readConfidence, 2), '0'), '.') . '%' : '-'); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Source File</th>
                                                                    <td>
                                                                        <a href="<?php echo e(route('finance.purchases.download-source-pdf', $invoice->id)); ?>" class="btn btn-sm btn-outline-primary">
                                                                            Open Attachment
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>

                            <?php if(request('status') !== 'failed'): ?>
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
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                                <td colspan="<?php echo e(request('status') === 'failed' ? 4 : 9); ?>" class="text-center">
                                No Purchase Invoices Found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>

    <!-- Pagination Links -->
    <?php if(method_exists($invoices, 'links')): ?>
        <div class="d-flex justify-content-center mt-4">
            <?php echo e($invoices->links()); ?>

        </div>
    <?php endif; ?>

</div>

<script>
function fetchNow() {
    const btn = document.getElementById('fetchNowBtn');
    const icon = document.getElementById('fetchIcon');
    const spinner = document.getElementById('fetchSpinner');
    const btnText = document.getElementById('fetchBtnText');
    
    // Show loading state
    icon.classList.add('d-none');
    spinner.classList.remove('d-none');
    btnText.textContent = 'Fetching...';
    btn.disabled = true;
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                     document.querySelector('input[name="_token"]')?.value;
    
    // Make AJAX request
    fetch('<?php echo e(route("finance.purchases.fetch-gmail")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        // Reset button state
        resetFetchButton();
        
        // Show appropriate message
        if (data.success) {
            showAlert('success', data.message);
            // Reload page after 2 seconds to show new data
            setTimeout(() => location.reload(), 2000);
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        resetFetchButton();
        showAlert('error', 'Fetch failed. Please try again.');
    });
    
    // Safety timeout - reset button after 60 seconds max
    setTimeout(() => {
        if (btn.disabled) {
            resetFetchButton();
            showAlert('warning', 'Fetch is taking longer than expected. Please check results later.');
        }
    }, 60000);
}

function resetFetchButton() {
    const btn = document.getElementById('fetchNowBtn');
    const icon = document.getElementById('fetchIcon');
    const spinner = document.getElementById('fetchSpinner');
    const btnText = document.getElementById('fetchBtnText');
    
    icon.classList.remove('d-none');
    spinner.classList.add('d-none');
    btnText.textContent = 'Fetch Now';
    btn.disabled = false;
}

function showAlert(type, message) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create new alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at the top of the container
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto-remove after 10 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 10000);
}
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\purchase_invoices\index.blade.php ENDPATH**/ ?>