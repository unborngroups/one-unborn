

<?php $__env->startSection('title', 'Purchase Invoices'); ?>

<?php $__env->startSection('content'); ?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Purchase Invoices</h4>


    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Invoice No</th>
                        <th>Vendor</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th width="250">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($loop->iteration); ?></td>

                            <td><?php echo e($purchase->invoice_number ?? '-'); ?></td>

                            <td>
                                <?php if($purchase->deliverable && $purchase->deliverable->feasibility && $purchase->deliverable->feasibility->client): ?>
                                    <?php echo e($purchase->deliverable->feasibility->client->client_name); ?>

                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php echo e($purchase->invoice_date 
                                    ? \Carbon\Carbon::parse($purchase->invoice_date)->format('d-m-Y') 
                                    : '-'); ?>

                            </td>

                            <td>
                                ₹ <?php echo e(number_format($purchase->total_amount, 2)); ?>

                            </td>

                            <td>

                                
                                <a href="<?php echo e(route('finance.purchases.show', $purchase->id)); ?>"
                                   class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>

                                
                                <a href="<?php echo e(route('finance.purchases.edit', $purchase->id)); ?>"
                                   class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                
                                <a href="<?php echo e(route('finance.purchases.pdf', $purchase->id)); ?>"
                                   class="btn btn-sm btn-secondary"
                                   target="_blank">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>

                                
                                <form action="<?php echo e(route('finance.purchases.destroy', $purchase->id)); ?>"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this invoice?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center">
                                No Purchase Invoices Found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\purchases\index.blade.php ENDPATH**/ ?>