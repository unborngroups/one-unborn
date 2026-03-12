

<?php $__env->startSection('title', 'Create Purchase Invoice'); ?>

<?php $__env->startSection('content'); ?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Create Purchase Invoice</h4>
        <a href="<?php echo e(route('finance.purchases.index')); ?>" class="btn btn-dark">
            Back
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <form action="<?php echo e(route('finance.purchases.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <div class="row mb-3">

                    
                    <div class="col-md-4">
                        <label class="form-label">Vendor</label>
                        <select name="vendor_id" class="form-select" required>
                            <option value="">Select Vendor</option>
                            <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($vendor->id); ?>">
                                    <?php echo e($vendor->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="col-md-4">
                        <label class="form-label">Invoice Number</label>
                        <input type="text" name="invoice_number" class="form-control" required>
                    </div>

                    
                    <div class="col-md-4">
                        <label class="form-label">Invoice Date</label>
                        <input type="date" name="invoice_date" class="form-control">
                    </div>

                </div>

                <hr>

                <h5>Items</h5>

                <div class="table-responsive">
                    <table class="table table-bordered" id="itemsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th width="120">Qty</th>
                                <th width="150">Price</th>
                                <th width="150">Total</th>
                                <th width="80">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="items[0][item_id]" class="form-select" required>
                                        <option value="">Select Item</option>
                                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item->id); ?>">
                                                <?php echo e($item->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[0][quantity]"
                                           class="form-control qty" value="1" min="1" required>
                                </td>
                                <td>
                                    <input type="number" name="items[0][price]"
                                           class="form-control price" step="0.01" required>
                                </td>
                                <td>
                                    <input type="text"
                                           class="form-control total"
                                           readonly>
                                </td>
                                <td>
                                    <button type="button"
                                            class="btn btn-danger btn-sm removeRow">
                                        X
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-primary mb-3" id="addRow">
                    + Add Item
                </button>

                <div class="text-end">
                    <h4>
                        Grand Total: ₹
                        <span id="grandTotal">0.00</span>
                    </h4>
                </div>

                <input type="hidden" name="total_amount" id="totalAmountInput">

                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-success">
                        Save Invoice
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>


<script>
let rowIndex = 1;

function calculateTotals() {
    let grandTotal = 0;

    document.querySelectorAll('#itemsTable tbody tr').forEach(row => {
        let qty = row.querySelector('.qty').value || 0;
        let price = row.querySelector('.price').value || 0;
        let total = qty * price;

        row.querySelector('.total').value = total.toFixed(2);
        grandTotal += total;
    });

    document.getElementById('grandTotal').innerText = grandTotal.toFixed(2);
    document.getElementById('totalAmountInput').value = grandTotal.toFixed(2);
}

document.addEventListener('input', function(e) {
    if (e.target.classList.contains('qty') ||
        e.target.classList.contains('price')) {
        calculateTotals();
    }
});

document.getElementById('addRow').addEventListener('click', function() {

    let table = document.querySelector('#itemsTable tbody');
    let newRow = table.rows[0].cloneNode(true);

    newRow.querySelectorAll('select, input').forEach(input => {
        input.value = '';
    });

    newRow.querySelector('select').name = `items[${rowIndex}][item_id]`;
    newRow.querySelector('.qty').name = `items[${rowIndex}][quantity]`;
    newRow.querySelector('.price').name = `items[${rowIndex}][price]`;

    table.appendChild(newRow);
    rowIndex++;
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('removeRow')) {
        let rows = document.querySelectorAll('#itemsTable tbody tr');
        if (rows.length > 1) {
            e.target.closest('tr').remove();
            calculateTotals();
        }
    }
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\purchases\create.blade.php ENDPATH**/ ?>