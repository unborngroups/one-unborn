

<?php $__env->startSection('content'); ?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h3 class="fw-bold text-primary mb-0">Asset</h3>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <form class="d-flex gap-2" method="GET" action="<?php echo e(route('asset.index')); ?>">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by Asset/Vendor" value="<?php echo e(request('search')); ?>">
                <button type="submit" class="btn btn-sm btn-primary">Search</button>
            </form>
            <a href="<?php echo e(route('asset.index')); ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
            <?php if($permissions->can_add): ?>
                <a href="<?php echo e(route('asset.create')); ?>" class="btn btn-sm btn-success">
                    <i class="bi bi-plus-circle"></i> Create Asset
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="card shadow border-0">

        <div class="card-body table-responsive p-0">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-dark-primary text-center">
                    <tr>
                        <th>#</th>
                        <th>Asset ID</th>
                        <th>Procured From</th>
                        <th>Purchase Date</th>
                        <th>Warranty</th>
                        <th>PO Number</th>
                        <th>MRP</th>
                        <th>Purchase Cost</th>
                        <th>Make</th>
                        <th>Brand</th>
                        <th>MAC</th>
                        <th>Serial No</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="text-center"><?php echo e($assets->firstItem() ? $assets->firstItem() + $index : $index + 1); ?></td>
                            <td><?php echo e($asset->asset_id); ?></td>
                            <td><?php echo e($asset->procured_from ?? '-'); ?></td>
                            <td><?php echo e($asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('d-m-Y') : '-'); ?></td>
                            <td><?php echo e($asset->warranty ?? '-'); ?></td>
                            <td><?php echo e($asset->po_number ?? '-'); ?></td>
                            <td><?php echo e($asset->mrp ?? '-'); ?></td>
                            <td><?php echo e($asset->purchase_cost ?? '-'); ?></td>
                            <td><?php echo e(optional($asset->make)->make_name ?? '-'); ?></td>
                            <td><?php echo e($asset->brand ?? '-'); ?></td>
                            <td><?php echo e($asset->mac_number ?? '-'); ?></td>
                            <td><?php echo e($asset->serial_no ?? '-'); ?></td>
                            <td class="text-center">
                                <span class="badge <?php echo e($asset->status === 'Active' ? 'bg-success' : 'bg-danger'); ?>">
                                    <?php echo e($asset->status); ?>

                                </span>
                            </td>
                            <td class="text-center">
                                <?php if($permissions->can_view): ?>
                                    <a class="btn btn-sm btn-outline-primary" href="<?php echo e(route('vendors.view', $asset->id)); ?>">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="14" class="text-center text-muted">No assets found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($assets->hasPages()): ?>
            <div class="card-footer bg-white border-0">
                <?php echo e($assets->links()); ?>

            </div>
        <?php endif; ?>

    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/asset/index.blade.php ENDPATH**/ ?>