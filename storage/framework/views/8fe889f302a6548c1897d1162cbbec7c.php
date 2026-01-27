



<?php $__env->startSection('content'); ?>

<div class="container py-4">

    <h3 class="mb-3 text-primary">View Asset</h3>



    <div class="card shadow border-0 p-4">

        <table class="table table-bordered">

            <tr>

            <!-- Asset ID -->
                <th>Asset ID</th>

                <td><?php echo e($asset->asset_id ?? '-'); ?></td>

            </tr>

            <tr>

            <!-- Company name in company master -->
                <th>Company</th>

                <td><?php echo e($asset->company->company_name ?? '-'); ?></td>

            </tr>

            <tr>
                <!-- asset type in asset_type master -->

                <th>Asset Type</th>

                <td><?php echo e($asset->assetType->type_name ?? '-'); ?></td>

            </tr>

            <tr>
                <!-- make type in make_type master -->

                <th>Make</th>

                <td><?php echo e($asset->makeType->make_name ?? '-'); ?></td>

            </tr>

            <tr>
                <!-- model -->

                <th>Model</th>

                <td><?php echo e($asset->model ?? '-'); ?></td>

            </tr>

            <tr>
                <!-- brand -->

                <th>Brand</th>

                <td><?php echo e($asset->brand ?? '-'); ?></td>

            </tr>

            <tr>

            <!-- Serial number -->
                <th>Serial No</th>

                <td><?php echo e($asset->serial_no ?? '-'); ?></td>

            </tr>

            <tr>
            <!-- MAC number -->

                <th>MAC No</th>

                <td><?php echo e($asset->mac_no ?? '-'); ?></td>

            </tr>

            <tr>
            <!-- Procured from -->

                <th>Procured From</th>

                <td><?php echo e($asset->vendor->vendor_name ?? '-'); ?></td>

            </tr>

            <tr>

                <!-- Purchase Date -->
                <th>Purchase Date</th>

                <td><?php echo e($asset->purchase_date ?? '-'); ?></td>

            </tr>

            <tr>
                <!-- Warranty (year) -->

                <th>Warranty (year)</th>

                <td><?php echo e($asset->warranty ?? '-'); ?></td>

            </tr>

            <tr>
                <!-- Purchase Order Number -->

                <th>Po No</th>

                <td><?php echo e($asset->po_no ?? '-'); ?></td>

            </tr>

            <tr>
                <!-- MRP -->

                <th>MRP</th>

                <td><?php echo e($asset->mrp ?? '-'); ?></td>

            </tr>

            <tr>
                <!-- Purchase Cost -->

                <th>Purchase Cost</th>

                <td><?php echo e($asset->purchase_cost ?? '-'); ?></td>

            </tr>

        </table>



        <div class="text-end">

            <a href="<?php echo e(route('operations.asset.index')); ?>" class="btn btn-secondary">Back</a>

        </div>

    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\operations\asset\view.blade.php ENDPATH**/ ?>