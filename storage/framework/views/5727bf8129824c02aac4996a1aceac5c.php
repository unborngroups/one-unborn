

<?php $__env->startSection('content'); ?>

<div class="container py-4">




    <div class="card shadow border-0 p-4">


    <h3 class="mb-3 text-primary">View Item</h3>

        <table class="table table-bordered">

            <tr>

                <th>Name</th>

                <td><?php echo e($items->item_name ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Description</th>

                <td><?php echo e($items->item_description ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Rate</th>

                <td><?php echo e($items->item_rate ?? '-'); ?></td>

            </tr>

            <tr>

                <th>HSN / SAC</th>

                <td><?php echo e($items->hsn_sac_code ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Unit</th>

                <td><?php echo e($items->usage_unit ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Status</th>

                <td>

                    <span class="badge <?php echo e($items->status === 'Active' ? 'bg-success' : 'bg-danger'); ?>">

                        <?php echo e($items->status); ?>


                    </span>

                </td>

            </tr>

        </table>



        <div class="text-end">

            <a href="<?php echo e(route('finance.items.index')); ?>" class="btn btn-secondary">Back</a>

        </div>

    </div>

</div>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\items\view.blade.php ENDPATH**/ ?>