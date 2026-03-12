

<?php $__env->startSection('title','Sales Invoices'); ?>

<?php $__env->startSection('content'); ?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Sales Invoices</h4>


    </div>


    
    <?php if(session('success')): ?>
        <div class="alert alert-success">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>


    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover">

                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Invoice No</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total Amount</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>

                <tbody>

                <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>

                        <td>
                            INV-<?php echo e(str_pad($sale->id,5,'0',STR_PAD_LEFT)); ?>

                        </td>

                        <td>
                            <?php echo e($sale->client->name ?? '-'); ?>

                        </td>

                        <td>
                            <?php echo e($sale->invoice_date); ?>

                        </td>

                        <td>
                            ₹ <?php echo e(number_format($sale->total_amount,2)); ?>

                        </td>

                        <td>

                            
                            <a href="<?php echo e(route('finance.sales.show',$sale->id)); ?>"
                               class="btn btn-info btn-sm">
                                View
                            </a>

                            
                            <a href="<?php echo e(route('finance.sales.edit',$sale->id)); ?>"
                               class="btn btn-warning btn-sm">
                                Edit
                            </a>

                            
                            <form action="<?php echo e(route('finance.sales.destroy',$sale->id)); ?>"
                                  method="POST"
                                  style="display:inline;">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>

                                <button class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete this invoice?')">
                                    Delete
                                </button>
                            </form>

                        </td>
                    </tr>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                    <tr>
                        <td colspan="6" class="text-center">
                            No Sales Invoices Found
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>
    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\sales\index.blade.php ENDPATH**/ ?>