

<?php $__env->startSection('title', 'Edit Purchase Invoice'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Edit Purchase Invoice</h4>
            <small class="text-muted">Auto-received from email — review &amp; save</small>
        </div>
        <a href="<?php echo e(route('finance.purchase_invoices.show', $invoice->id)); ?>" class="btn btn-outline-secondary btn-sm">
            &larr; Back
        </a>
    </div>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('finance.purchase_invoices.update', $invoice->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="row g-4">

            
            <?php if(!empty($raw)): ?>
            <div class="col-lg-4">
                <div class="card border-info h-100">
                    <div class="card-header bg-info text-white py-2">
                        <i class="bi bi-robot"></i>
                        Auto-Extracted (OCR) — Reference
                    </div>
                    <div class="card-body small">
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
                                    <?php if(isset($raw[$key]) && $raw[$key] !== null && $raw[$key] !== ''): ?>
                                    <tr>
                                        <td class="text-muted fw-semibold pe-2"><?php echo e($label); ?></td>
                                        <td><?php echo e($raw[$key]); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <?php if(!empty($raw['matching'])): ?>
                                <tr><td colspan="2"><hr class="my-1"></td></tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Confidence</td>
                                    <td>
                                        <span class="badge
                                            <?php if(($raw['matching']['combined_confidence'] ?? 0) >= 80): ?>
                                                bg-success
                                            <?php elseif(($raw['matching']['combined_confidence'] ?? 0) >= 50): ?>
                                                bg-warning text-dark
                                            <?php else: ?>
                                                bg-danger
                                            <?php endif; ?>">
                                            <?php echo e($raw['matching']['combined_confidence'] ?? '-'); ?>%
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Matched By</td>
                                    <td><?php echo e($raw['matching']['matched_by'] ?? '-'); ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <?php if($invoice->po_invoice_file): ?>
                        <hr>
                        <p class="mb-1 text-muted fw-semibold">Attached File</p>
                        <a href="<?php echo e(Storage::url($invoice->po_invoice_file)); ?>"
                           target="_blank"
                           class="btn btn-outline-info btn-sm w-100">
                            View Invoice PDF
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            
            <div class="col-lg-<?php echo e(!empty($raw) ? '8' : '12'); ?>">
                <div class="card shadow-sm">
                    <div class="card-header py-2 fw-semibold">Invoice Details</div>
                    <div class="card-body">

                        <div class="row g-3">
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Vendor (Master)</label>
                                <select name="vendor_id" class="form-select">
                                    <option value="">— Select Vendor (optional) —</option>
                                    <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($v->id); ?>"
                                            <?php echo e(old('vendor_id', $invoice->vendor_id) == $v->id ? 'selected' : ''); ?>>
                                            <?php echo e($v->vendor_name); ?>

                                            <?php if($v->gstin): ?> (<?php echo e($v->gstin); ?>) <?php endif; ?>
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <div class="form-text">Leave blank if vendor not in master list.</div>
                            </div>

                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Vendor Name <span class="text-danger">*</span></label>
                                <input type="text" name="vendor_name" class="form-control <?php $__errorArgs = ['vendor_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('vendor_name', $invoice->vendor_name ?? $invoice->vendor_name_raw)); ?>"
                                    placeholder="As printed on invoice">
                                <?php $__errorArgs = ['vendor_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">GSTIN</label>
                                <input type="text" name="gstin" class="form-control <?php $__errorArgs = ['gstin'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('gstin', $invoice->gstin ?? $invoice->vendor_gstin ?? $invoice->gst_number)); ?>"
                                    placeholder="e.g. 29ABCDE1234F1ZK"
                                    maxlength="15" style="text-transform:uppercase">
                                <?php $__errorArgs = ['gstin'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Invoice Number</label>
                                <input type="text" name="invoice_no" class="form-control <?php $__errorArgs = ['invoice_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('invoice_no', $invoice->invoice_no)); ?>"
                                    placeholder="e.g. INV/2025/001">
                                <?php $__errorArgs = ['invoice_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Invoice Date</label>
                                <input type="date" name="invoice_date" class="form-control <?php $__errorArgs = ['invoice_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('invoice_date', $invoice->invoice_date ? $invoice->invoice_date->format('Y-m-d') : '')); ?>">
                                <?php $__errorArgs = ['invoice_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Due Date</label>
                                <input type="date" name="due_date" class="form-control"
                                    value="<?php echo e(old('due_date', $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '')); ?>">
                            </div>

                            
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Status</label>
                                <input type="text" class="form-control" readonly
                                    value="<?php echo e(ucfirst(str_replace('_',' ', $invoice->status))); ?>">
                                <div class="form-text">Change status using Verify / Approve / Mark Paid buttons.</div>
                            </div>
                        </div>

                        
                        <hr class="my-3">
                        <h6 class="text-muted mb-3">Amount Details</h6>

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Sub Total (₹)</label>
                                <input type="number" step="0.01" name="amount" class="form-control <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('amount', $invoice->amount ?? 0)); ?>"
                                    id="amt_subtotal" oninput="calcGrandTotal()">
                                <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">CGST (₹)</label>
                                <input type="number" step="0.01" name="cgst_total" class="form-control"
                                    value="<?php echo e(old('cgst_total', $invoice->cgst_total ?? 0)); ?>"
                                    id="amt_cgst" oninput="calcGrandTotal()">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">SGST / IGST (₹)</label>
                                <input type="number" step="0.01" name="sgst_total" class="form-control"
                                    value="<?php echo e(old('sgst_total', $invoice->sgst_total ?? 0)); ?>"
                                    id="amt_sgst" oninput="calcGrandTotal()">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Tax Amount (₹)</label>
                                <input type="number" step="0.01" name="tax_amount" class="form-control"
                                    value="<?php echo e(old('tax_amount', $invoice->tax_amount ?? 0)); ?>"
                                    id="amt_tax" oninput="calcGrandTotal()">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-success">Grand Total (₹)</label>
                                <input type="number" step="0.01" name="grand_total" class="form-control border-success fw-bold"
                                    value="<?php echo e(old('grand_total', $invoice->grand_total ?? 0)); ?>"
                                    id="amt_grand">
                                <div class="form-text">You can also type directly.</div>
                            </div>
                        </div>

                        
                        <hr class="my-3">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Notes / Remarks</label>
                                <textarea name="notes" class="form-control" rows="3"
                                    placeholder="Any corrections or additional info..."><?php echo e(old('notes', $invoice->notes)); ?></textarea>
                            </div>
                        </div>

                        
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                Save Invoice Details
                            </button>
                            <a href="<?php echo e(route('finance.purchase_invoices.show', $invoice->id)); ?>"
                               class="btn btn-outline-secondary">Cancel</a>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </form>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function calcGrandTotal() {
    const sub  = parseFloat(document.getElementById('amt_subtotal').value) || 0;
    const cgst = parseFloat(document.getElementById('amt_cgst').value) || 0;
    const sgst = parseFloat(document.getElementById('amt_sgst').value) || 0;
    const tax  = parseFloat(document.getElementById('amt_tax').value) || 0;
    const total = sub + cgst + sgst + tax;
    if (total > 0) {
        document.getElementById('amt_grand').value = total.toFixed(2);
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\purchase_invoices\edit.blade.php ENDPATH**/ ?>