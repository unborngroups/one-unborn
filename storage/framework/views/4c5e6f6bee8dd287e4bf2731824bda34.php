

<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>📄 Create Purchase Invoice</h2>
            <p class="text-muted">Manually enter invoice details or use email import</p>
        </div>
    </div>

    <form action="<?php echo e(route('finance.purchases.store')); ?>" method="POST" enctype="multipart/form-data" class="needs-validation">
        <?php echo csrf_field(); ?>

        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="basic-tab" data-toggle="tab" href="#basic" role="tab">
                            📋 Basic Info
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="amounts-tab" data-toggle="tab" href="#amounts" role="tab">
                            💰 Amounts
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="optional-tab" data-toggle="tab" href="#optional" role="tab">
                            ⚙️ Optional
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">
                    <!-- TAB 1: BASIC INFO -->
                    <div class="tab-pane fade show active" id="basic" role="tabpanel">
                        <div class="row">
                            <!-- Vendor Selection -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-danger">*</span> Vendor Name
                                </label>
                                <select name="vendor_id" class="form-control <?php $__errorArgs = ['vendor_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="" selected disabled>-- Select Vendor --</option>
                                    <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($vendor->id); ?>" <?php echo e(old('vendor_id') == $vendor->id ? 'selected' : ''); ?>>
                                            <?php echo e($vendor->name); ?> 
                                            <?php if($vendor->gst_number): ?>
                                                (GST: <?php echo e($vendor->gst_number); ?>)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['vendor_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <!-- Invoice Number -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-danger">*</span> Invoice Number
                                </label>
                                <input 
                                    type="text" 
                                    name="invoice_number" 
                                    class="form-control <?php $__errorArgs = ['invoice_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="e.g., INV-2024-001"
                                    value="<?php echo e(old('invoice_number')); ?>"
                                    required
                                >
                                <?php $__errorArgs = ['invoice_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <small class="form-text text-muted">Vendor's invoice number</small>
                            </div>

                            <!-- Invoice Date -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> Invoice Date
                                </label>
                                <input 
                                    type="date" 
                                    name="invoice_date" 
                                    class="form-control <?php $__errorArgs = ['invoice_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    value="<?php echo e(old('invoice_date') ?? now()->format('Y-m-d')); ?>"
                                >
                                <?php $__errorArgs = ['invoice_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <small class="form-text text-muted">YYYY-MM-DD format</small>
                            </div>

                            <!-- Deliverable (Optional) -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> Deliverable
                                </label>
                                <select name="deliverable_id" class="form-control <?php $__errorArgs = ['deliverable_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="" selected>-- Select (Optional) --</option>
                                    <?php $__currentLoopData = $deliverables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deliverable): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($deliverable->id); ?>" <?php echo e(old('deliverable_id') == $deliverable->id ? 'selected' : ''); ?>>
                                            <?php echo e($deliverable->circuit_name ?? 'Deliverable #' . $deliverable->id); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['deliverable_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <!-- PDF/Image Upload -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-danger">*</span> Invoice PDF / Image
                                </label>
                                <div class="custom-file">
                                    <input 
                                        type="file" 
                                        name="po_invoice_file" 
                                        class="custom-file-input <?php $__errorArgs = ['po_invoice_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="invoiceFile"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        required
                                    >
                                    <label class="custom-file-label" for="invoiceFile">Choose file...</label>
                                </div>
                                <?php $__errorArgs = ['po_invoice_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <small class="form-text text-muted">
                                    Accepted: PDF, JPG, PNG | Max: 2MB
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: AMOUNTS -->
                    <div class="tab-pane fade" id="amounts" role="tabpanel">
                        <div class="alert alert-info">
                            💡 Enter individual cost components or just total amount
                        </div>

                        <div class="row">
                            <!-- ARC Amount -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> ARC (Annual Recurring Cost)
                                </label>
                                <input 
                                    type="number" 
                                    name="arc_amount" 
                                    class="form-control <?php $__errorArgs = ['arc_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="0.00"
                                    step="0.01"
                                    min="0"
                                    value="<?php echo e(old('arc_amount')); ?>"
                                >
                                <?php $__errorArgs = ['arc_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <!-- OTC Amount -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> OTC (One-Time Cost)
                                </label>
                                <input 
                                    type="number" 
                                    name="otc_amount" 
                                    class="form-control <?php $__errorArgs = ['otc_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="0.00"
                                    step="0.01"
                                    min="0"
                                    value="<?php echo e(old('otc_amount')); ?>"
                                >
                                <?php $__errorArgs = ['otc_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <!-- Static IP Cost -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> Static IP Cost
                                </label>
                                <input 
                                    type="number" 
                                    name="static_amount" 
                                    class="form-control <?php $__errorArgs = ['static_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="0.00"
                                    step="0.01"
                                    min="0"
                                    value="<?php echo e(old('static_amount')); ?>"
                                >
                                <?php $__errorArgs = ['static_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <!-- Total Amount (REQUIRED) -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-danger">*</span> Total Amount
                                </label>
                                <input 
                                    type="number" 
                                    name="total_amount" 
                                    class="form-control <?php $__errorArgs = ['total_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="0.00"
                                    step="0.01"
                                    min="0.01"
                                    value="<?php echo e(old('total_amount')); ?>"
                                    required
                                >
                                <?php $__errorArgs = ['total_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <small class="form-text text-muted">
                                    Total invoice amount in ₹
                                </small>
                            </div>

                            <!-- GST Number (Optional) -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> Vendor GST Number
                                </label>
                                <input 
                                    type="text" 
                                    name="gst_number" 
                                    class="form-control <?php $__errorArgs = ['gst_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="27AABCT1234H2Z0"
                                    pattern="\d{2}[A-Z]{5}\d{4}[A-Z1-9][A-Z][0-9A-Z]\d"
                                    value="<?php echo e(old('gst_number')); ?>"
                                >
                                <?php $__errorArgs = ['gst_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <small class="form-text text-muted">
                                    15-character GST number (auto-extracted from PDF if available)
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: OPTIONAL FIELDS -->
                    <div class="tab-pane fade" id="optional" role="tabpanel">
                        <div class="alert alert-warning">
                            ⚙️ These fields are optional and can be left blank
                        </div>

                        <div class="row">
                            <!-- Vendor Name (Free Text) -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> Vendor Name (Text)
                                </label>
                                <input 
                                    type="text" 
                                    name="vendor_name" 
                                    class="form-control"
                                    placeholder="Vendor company name"
                                    value="<?php echo e(old('vendor_name')); ?>"
                                >
                                <small class="form-text text-muted">
                                    If different from dropdown selection
                                </small>
                            </div>

                            <!-- Vendor Email -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> Vendor Email
                                </label>
                                <input 
                                    type="email" 
                                    name="vendor_email" 
                                    class="form-control"
                                    placeholder="vendor@company.com"
                                    value="<?php echo e(old('vendor_email')); ?>"
                                >
                            </div>

                            <!-- Vendor Phone -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> Vendor Phone
                                </label>
                                <input 
                                    type="text" 
                                    name="vendor_phone" 
                                    class="form-control"
                                    placeholder="+91-XXXXXXXXXX"
                                    value="<?php echo e(old('vendor_phone')); ?>"
                                >
                            </div>

                            <!-- Vendor Address -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> Vendor Address
                                </label>
                                <textarea 
                                    name="vendor_address" 
                                    class="form-control"
                                    rows="2"
                                    placeholder="Street, City, State, Zip"
                                ><?php echo e(old('vendor_address')); ?></textarea>
                            </div>

                            <!-- Due Date -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> Due Date
                                </label>
                                <input 
                                    type="date" 
                                    name="due_date" 
                                    class="form-control"
                                    value="<?php echo e(old('due_date')); ?>"
                                >
                            </div>

                            <!-- Tax Amount -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> Tax Amount (CGST + SGST)
                                </label>
                                <input 
                                    type="number" 
                                    name="tax_amount" 
                                    class="form-control"
                                    placeholder="0.00"
                                    step="0.01"
                                    min="0"
                                    value="<?php echo e(old('tax_amount')); ?>"
                                >
                            </div>

                            <!-- CGST -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> CGST (Central GST)
                                </label>
                                <input 
                                    type="number" 
                                    name="cgst_total" 
                                    class="form-control"
                                    placeholder="0.00"
                                    step="0.01"
                                    min="0"
                                    value="<?php echo e(old('cgst_total')); ?>"
                                >
                            </div>

                            <!-- SGST -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> SGST (State GST)
                                </label>
                                <input 
                                    type="number" 
                                    name="sgst_total" 
                                    class="form-control"
                                    placeholder="0.00"
                                    step="0.01"
                                    min="0"
                                    value="<?php echo e(old('sgst_total')); ?>"
                                >
                            </div>

                            <!-- Notes -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> Notes / Comments
                                </label>
                                <textarea 
                                    name="notes" 
                                    class="form-control"
                                    rows="3"
                                    placeholder="Any additional notes about this invoice"
                                ><?php echo e(old('notes')); ?></textarea>
                            </div>

                            <!-- Terms -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    <span class="badge badge-info">○</span> Payment Terms
                                </label>
                                <input 
                                    type="text" 
                                    name="terms" 
                                    class="form-control"
                                    placeholder="e.g., Net 30, Net 45, Due on receipt"
                                    value="<?php echo e(old('terms')); ?>"
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="float-right">
                    <a href="<?php echo e(route('finance.purchases.index')); ?>" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        🔒 Create Invoice
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // Auto-fill filename
    document.getElementById('invoiceFile').addEventListener('change', function(e) {
        var label = e.target.nextElementSibling;
        label.textContent = e.target.files[0].name;
    });

    // Form validation
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>

<style>
    .badge {
        font-size: 0.7rem;
        padding: 0.3rem 0.5rem;
    }
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    .nav-tabs .nav-link {
        color: #666;
    }
    .nav-tabs .nav-link.active {
        color: #0066cc;
        border-bottom: 3px solid #0066cc;
    }
</style>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\purchases\create_form.blade.php ENDPATH**/ ?>