

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>Add Vendor Invoice</h4>

    <form method="POST" action="<?php echo e(route('finance.vendor-invoices.store')); ?>">
        <?php echo csrf_field(); ?>

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Vendor</label>
                <select name="vendor_id" class="form-control" required>
                    <option value="">Select vendor</option>
                    <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($vendor->id); ?>" <?php echo e(old('vendor_id') == $vendor->id ? 'selected' : ''); ?>>
                            <?php echo e($vendor->vendor_name); ?> (<?php echo e($vendor->gstin); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Invoice No</label>
                <input type="text" name="invoice_no" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label>Invoice Date</label>
                <input type="date" name="invoice_date" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="Pending">Pending</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Subtotal</label>
                <input type="number" step="0.01" name="subtotal" class="form-control">
            </div>

            <div class="col-md-4">
                <label>GST Amount</label>
                <input type="number" step="0.01" name="gst_amount" class="form-control">
            </div>

            <div class="col-md-4">
                <label>Total Amount</label>
                <input type="number" step="0.01" name="total_amount" id="vendor-invoice-total" class="form-control" readonly>
            </div>
        </div>

        <button class="btn btn-success">Save Invoice</button>
        <a href="<?php echo e(route('finance.vendor-invoices.index')); ?>" class="btn btn-secondary">Back</a>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var subtotal = document.querySelector('[name="subtotal"]');
        var gstAmount = document.querySelector('[name="gst_amount"]');
        var total = document.getElementById('vendor-invoice-total');

        function updateTotal() {
            var subValue = parseFloat(subtotal.value) || 0;
            var gstValue = parseFloat(gstAmount.value) || 0;
            total.value = (subValue + gstValue).toFixed(2);
        }

        subtotal.addEventListener('input', updateTotal);
        gstAmount.addEventListener('input', updateTotal);
        updateTotal();
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\purchases\vendor_invoices\create.blade.php ENDPATH**/ ?>