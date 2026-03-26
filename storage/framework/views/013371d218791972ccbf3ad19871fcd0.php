

<?php $__env->startSection('title', 'Create Purchase Invoice'); ?>

<?php $__env->startSection('content'); ?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Create Purchase Invoice</h4>
        <a href="<?php echo e(route('finance.purchases.index')); ?>" class="btn btn-dark">Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <form id="purchaseForm" action="<?php echo e(route('finance.purchases.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>

                <div class="row mb-3">

                    
                    <div class="col-md-4">
                        <label>Vendor</label>
                        <select name="vendor_id" id="vendor_id" class="form-select" required>
                            <option value="">Select Vendor</option>
                            <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($vendor->id); ?>"><?php echo e($vendor->vendor_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="col-md-4">
    <label>Deliverable</label>
    <select name="deliverable_id" id="deliverable_id" class="form-select" required>
        <option value="">Select Deliverable</option>
        <?php $__currentLoopData = $deliverables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $del): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($del->id); ?>">
                <?php echo e($del->id); ?> - PO: <?php echo e($del->purchase_order_id); ?>

            </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
</div>

                    
                    <div class="col-md-4">
                        <label>Invoice Number</label>
                        <input type="text" name="invoice_number" class="form-control" required>
                    </div>

                    
                    <div class="col-md-4">
                        <label>Invoice Date</label>
                        <input type="date" name="invoice_date" class="form-control">
                    </div>

                </div>

                <hr>

                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>PO Total</label>
                        <input type="text" id="poTotal" class="form-control" readonly>
                    </div>
                </div>

                <hr>

                
                <h5>Items (Router / Cable)</h5>

                <table class="table table-bordered" id="itemsTable">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>
                                <select name="items[0][item_id]" class="form-select">
                                    <option value="">Select Item</option>
                                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($item->id); ?>"><?php echo e($item->item_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>

                            <td>
                                <input type="number" name="items[0][quantity]" class="form-control qty" value="1">
                            </td>

                            <td>
                                <input type="number" name="items[0][price]" class="form-control price">
                            </td>

                            <td>
                                <input type="text" class="form-control total" readonly>
                            </td>

                            <td>
                                <button type="button" class="btn btn-danger removeRow">X</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <button type="button" id="addRow" class="btn btn-primary mb-3">+ Add Item</button>

                
                <label>Upload Invoice</label>
                <input type="file" id="invoiceFile"  name="po_invoice_file" class="form-control" required>

                
                <div class="text-end mt-3">
                    <h4>Invoice Total: ₹ <span id="grandTotal">0.00</span></h4>
                </div>

                <input type="hidden" name="total_amount" id="totalAmountInput">
                <input type="hidden" id="poTotalInput">

                
                <div class="text-end mt-3">
                    <button type="button" id="saveBtn" class="btn btn-success">Save Invoice</button>
                </div>

            </form>

        </div>
    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

let rowIndex = 1;

// ✅ Calculate totals
function calculateTotals() {
    let grandTotal = 0;

    document.querySelectorAll('#itemsTable tbody tr').forEach(row => {
        let qty = parseFloat(row.querySelector('.qty').value) || 0;
        let price = parseFloat(row.querySelector('.price').value) || 0;

        let total = qty * price;
        row.querySelector('.total').value = total.toFixed(2);

        grandTotal += total;
    });

    document.getElementById('grandTotal').innerText = grandTotal.toFixed(2);
    document.getElementById('totalAmountInput').value = grandTotal.toFixed(2);
}

// Auto calculate
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('qty') || e.target.classList.contains('price')) {
        calculateTotals();
    }
});

// Add row
document.getElementById('addRow').addEventListener('click', function() {
    let table = document.querySelector('#itemsTable tbody');
    let newRow = table.rows[0].cloneNode(true);

    newRow.querySelectorAll('input').forEach(input => input.value = '');
    newRow.querySelector('.qty').value = 1;

    newRow.querySelector('select').name = `items[${rowIndex}][item_id]`;
    newRow.querySelector('.qty').name = `items[${rowIndex}][quantity]`;
    newRow.querySelector('.price').name = `items[${rowIndex}][price]`;

    table.appendChild(newRow);
    rowIndex++;
});

// Remove row
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('removeRow')) {
        let rows = document.querySelectorAll('#itemsTable tbody tr');

        if (rows.length > 1) {
            e.target.closest('tr').remove();
            calculateTotals();
        }
    }
});

// ✅ Fetch PO total
document.getElementById('deliverable_id').addEventListener('change', function () {

    let vendorId = this.value;

    fetch('/get-po-data/' + vendorId)
        .then(res => res.json())
        .then(data => {

        console.log("PO DATA:", data);

            let poTotal = (data.arc_total || 0) + (data.otc_total || 0) + (data.static_total || 0);

            document.getElementById('poTotal').value = poTotal.toFixed(2);
            document.getElementById('poTotalInput').value = poTotal.toFixed(2);
        });
});

// ✅ Validate before save
document.getElementById('saveBtn').addEventListener('click', function () {

    calculateTotals();

    let poTotal = parseFloat(document.getElementById('poTotalInput').value) || 0;
    let invoiceTotal = parseFloat(document.getElementById('totalAmountInput').value) || 0;

    let msg = '';
    let icon = '';

    if (invoiceTotal > poTotal) {
        msg = 'Invoice HIGHER than PO';
        icon = 'warning';
    } else if (invoiceTotal < poTotal) {
        msg = 'Invoice LOWER than PO';
        icon = 'info';
    } else {
        msg = 'Perfect match';
        icon = 'success';
    }

    Swal.fire({
        title: msg,
        icon: icon,
        showCancelButton: true,
        confirmButtonText: 'Yes Save'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('purchaseForm').submit();
        }
    });
});



document.getElementById('invoiceFile').addEventListener('change', function () {

    let file = this.files[0];
    let formData = new FormData();
    formData.append('file', file);
    formData.append('_token', '<?php echo e(csrf_token()); ?>');

    fetch('/parse-invoice', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        console.log(data);

        // ✅ Fill Service total
        let serviceTotal = (data.arc || 0) + (data.otc || 0) + (data.static || 0);

        // 👉 Add Router as item (if exists)
        if (data.router > 0) {

            let table = document.querySelector('#itemsTable tbody');
            let newRow = table.rows[0].cloneNode(true);

            newRow.querySelector('select').value = ''; // select manually or map id

            newRow.querySelector('.qty').value = 1;
            newRow.querySelector('.price').value = data.router;

            table.appendChild(newRow);
        }

        // 👉 Update totals
        calculateTotals();

        alert("Invoice parsed successfully!");
    })
    .catch(err => {
        console.error(err);
        alert("Parsing failed");
    });
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\purchases\create.blade.php ENDPATH**/ ?>