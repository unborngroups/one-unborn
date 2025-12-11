



<?php $__env->startSection('content'); ?>

<div class="container py-4">

    <h3 class="mb-3 text-primary">View Asset</h3>



    <div class="card shadow border-0 p-4">

        <table class="table table-bordered">

            <tr>

                <th>Asset ID</th>

                <td><?php echo e($asset->asset_id ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Company</th>

                <td><?php echo e($asset->company->company_name ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Asset Type</th>

                <td><?php echo e($asset->assetType->type_name ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Make</th>

                <td><?php echo e($asset->makeType->make_name ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Model</th>

                <td><?php echo e($asset->model ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Brand</th>

                <td><?php echo e($asset->brand ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Serial No</th>

                <td><?php echo e($asset->serial_no ?? '-'); ?></td>

            </tr>

            <tr>

                <th>MAC No</th>

                <td><?php echo e($asset->mac_no ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Procured From</th>

                <td><?php echo e($asset->procured_from ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Purchase Date</th>

                <td><?php echo e($asset->purchase_date ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Warranty (year)</th>

                <td><?php echo e($asset->warranty ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Po No</th>

                <td><?php echo e($asset->po_no ?? '-'); ?></td>

            </tr>

            <tr>

                <th>MRP</th>

                <td><?php echo e($asset->mrp ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Purchase Cost</th>

                <td><?php echo e($asset->purchase_cost ?? '-'); ?></td>

            </tr>

        </table>



        <div class="text-end">

            <a href="<?php echo e(route('asset.index')); ?>" class="btn btn-secondary">Back</a>

        </div>

    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\asset\view.blade.php ENDPATH**/ ?>