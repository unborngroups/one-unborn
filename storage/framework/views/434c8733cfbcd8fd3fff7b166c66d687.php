

<?php $__env->startSection('content'); ?>
<div class="container">

    <?php if(session('success')): ?>
        <div class="alert alert-success">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <h4 class="mb-3">Invoice List</h4>

    <table class="table table-bordered">
        <thead class="table-dark-primary">
            <tr>
                <th>S.No</th>
                <th>Invoice No</th>
                <th>Client</th>
                <th>Date</th>
                <th>Total</th>
                <th width="220">Action</th>
            </tr>
        </thead>
        <tbody>
        <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($key + 1); ?></td>
                <td><?php echo e($invoice->invoice_no); ?></td>
                <td><?php echo e($invoice->deliverable->feasibility->client->client_name); ?></td>
                <td><?php echo e(\Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y')); ?></td>
                <td><?php echo e(number_format($invoice->total_amount,2)); ?></td>
                <td>
                    <a href="<?php echo e(route('finance.invoices.view',$invoice->id)); ?>"
                       class="btn btn-info btn-sm">View</a>

                    

                    <a href="<?php echo e(route('finance.invoices.pdf',$invoice->id)); ?>"
                       class="btn btn-secondary btn-sm">PDF</a>

                    <form action="<?php echo e(route('finance.invoices.delete',$invoice->id)); ?>"
                          method="POST"
                          style="display:inline;">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\invoices\index.blade.php ENDPATH**/ ?>