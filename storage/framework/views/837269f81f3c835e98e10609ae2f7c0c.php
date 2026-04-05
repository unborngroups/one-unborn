

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-repeat me-2"></i>Recurring Invoices</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">Recurring Invoice listing will appear here.<br>
                <ul>
                    <li>Recurrence: Monthly, Quarterly, Yearly, Half-yearly</li>
                    <li>Auto-send invoice 30 days before due date</li>
                    <li>Show all relevant recurring invoice data here</li>
                </ul>
            </div>
            <?php if(empty($recurringInvoices)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                    <h5 class="text-muted mt-3">No Recurring Invoices Found</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\sales\recurring_invoice\index.blade.php ENDPATH**/ ?>