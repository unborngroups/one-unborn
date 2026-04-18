<?php $__env->startSection('title', 'Payment Batch Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Payment Batch Details</h4>
                <a href="<?php echo e(route('payments.batches')); ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Batches
                </a>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Batch #<?php echo e($batch->id); ?> - <?php echo e($batch->batch_reference); ?></h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <h6 class="text-muted mb-2">Total Amount</h6>
                                <h4 class="text-primary">₹<?php echo e(number_format($batch->total_amount, 2)); ?></h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <h6 class="text-muted mb-2">Total Invoices</h6>
                                <h4 class="text-info"><?php echo e($batch->total_invoices); ?></h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <h6 class="text-muted mb-2">Status</h6>
                                <h4>
                                    <span class="badge bg-<?php echo e(getBatchStatusColor($batch->status)); ?> text-white">
                                        <?php echo e(ucfirst($batch->status)); ?>

                                    </span>
                                </h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3 text-center">
                                <h6 class="text-muted mb-2">Created</h6>
                                <h4 class="text-secondary"><?php echo e($batch->created_at->format('M d, Y')); ?></h4>
                            </div>
                        </div>
                    </div>

                    <?php if($batch->status === 'accountant_approval' && $batch->accountantApproval): ?>
                        <div class="alert alert-info">
                            <strong>Accountant Approval:</strong> 
                            Approved by <?php echo e($batch->accountantApproval->user->name); ?> 
                            on <?php echo e($batch->accountantApproval->approved_at->format('M d, Y H:i')); ?>

                            <?php if($batch->accountantApproval->remarks): ?>
                                <br><em>Remarks: <?php echo e($batch->accountantApproval->remarks); ?></em>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if($batch->status === 'finance_manager_approval' && $batch->financeManagerApproval): ?>
                        <div class="alert alert-success">
                            <strong>Finance Manager Approval:</strong> 
                            Approved by <?php echo e($batch->financeManagerApproval->user->name); ?> 
                            on <?php echo e($batch->financeManagerApproval->approved_at->format('M d, Y H:i')); ?>

                            <?php if($batch->financeManagerApproval->remarks): ?>
                                <br><em>Remarks: <?php echo e($batch->financeManagerApproval->remarks); ?></em>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive mt-4">
                        <h6>Invoices in this Batch</h6>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Vendor</th>
                                    <th>PAN</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $batch->paymentTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($transaction->purchaseInvoice->invoice_no); ?></td>
                                        <td>
                                            <div>
                                                <strong><?php echo e($transaction->purchaseInvoice->vendor->vendor_name); ?></strong>
                                                <?php if($transaction->purchaseInvoice->vendor->pan): ?>
                                                    <br><small class="text-muted">PAN: <?php echo e($transaction->purchaseInvoice->vendor->pan); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if($transaction->purchaseInvoice->vendor->pan): ?>
                                                <span class="badge bg-secondary"><?php echo e($transaction->purchaseInvoice->vendor->pan); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>₹<?php echo e(number_format($transaction->amount, 2)); ?></td>
                                        <td><?php echo e($transaction->purchaseInvoice->due_date->format('M d, Y')); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo e(getTransactionStatusColor($transaction->status)); ?>">
                                                <?php echo e(ucfirst($transaction->status)); ?>

                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if($batch->processing_notes): ?>
                        <div class="mt-3">
                            <h6>Processing Notes</h6>
                            <div class="alert alert-light">
                                <?php echo e($batch->processing_notes); ?>

                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if($batch->failure_reason): ?>
                        <div class="mt-3">
                            <h6>Failure Reason</h6>
                            <div class="alert alert-danger">
                                <?php echo e($batch->failure_reason); ?>

                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php
function getTransactionStatusColor($status) {
    $colors = [
        'pending' => 'secondary',
        'processing' => 'primary',
        'completed' => 'success',
        'failed' => 'danger',
        'refunded' => 'warning',
    ];
    return $colors[$status] ?? 'secondary';
}
?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\payments\batch_details.blade.php ENDPATH**/ ?>