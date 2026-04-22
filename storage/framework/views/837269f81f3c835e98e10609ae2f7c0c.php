

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-repeat me-2"></i>Recurring Invoices</h5>
        </div>
        <div class="card-body">
            <?php if(session('success')): ?>
                <div class="alert alert-success"><?php echo e(session('success')); ?></div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
            <?php endif; ?>

            <?php if(!empty($selectedClient)): ?>
                <div class="alert alert-info">
                    Showing recurring entries for client: <strong><?php echo e($selectedClient->client_name); ?></strong>
                </div>
            <?php endif; ?>

            <?php if(!empty($summary)): ?>
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-muted d-block">Total ARC Component</small>
                            <strong><?php echo e(number_format($summary['total_arc_component'] ?? 0, 2)); ?></strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-muted d-block">Total Static Component</small>
                            <strong><?php echo e(number_format($summary['total_static_component'] ?? 0, 2)); ?></strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-muted d-block">Total Formula Amount</small>
                            <strong><?php echo e(number_format($summary['total_formula_amount'] ?? 0, 2)); ?></strong>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(($invoiceRows ?? collect())->isNotEmpty()): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Client</th>
                                <th>Circuit ID</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Months</th>
                                <th>Days</th>
                                <th>ARC (Annual)</th>
                                <th>Static (Annual)</th>
                                <th>Total (Annual)</th>
                                <th>Day Rate</th>
                                <th>Formula Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $invoiceRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($loop->iteration); ?></td>
                                    <td><?php echo e($row['client_name']); ?></td>
                                    <td><?php echo e($row['circuit_id']); ?></td>
                                    <td><?php echo e($row['start_date']); ?></td>
                                    <td><?php echo e($row['end_date']); ?></td>
                                    <td><?php echo e($row['renewal_months']); ?></td>
                                    <td><?php echo e($row['billable_days']); ?></td>
                                    <td><?php echo e(number_format($row['annual_arc'], 2)); ?></td>
                                    <td><?php echo e(number_format($row['annual_static'], 2)); ?></td>
                                    <td><?php echo e(number_format($row['annual_total'], 2)); ?></td>
                                    <td><?php echo e(number_format($row['day_rate'], 6)); ?></td>
                                    <td><strong><?php echo e(number_format($row['formula_amount'], 2)); ?></strong></td>
                                    <td>
                                        <form action="<?php echo e(route('finance.sales.recurring-invoice.send-email', $row['renewal']->id)); ?>" method="POST" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Send recurring invoice email to client now?')">
                                                Click Here to Send Email
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                    <h5 class="text-muted mt-3">No Recurring Invoices Found</h5>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\sales\recurring_invoice\index.blade.php ENDPATH**/ ?>