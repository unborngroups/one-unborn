
<?php $__env->startSection('content'); ?>

<div class="container">
<h4 class="mb-3">Asset List</h4>

    <a href="<?php echo e(route('asset.create')); ?>" class="btn btn-success mb-3 float-end">+ Add Asset</a>


<table class="table table-bordered">
<thead class="table-dark-primary">
<tr>
    <th>S.No</th>
    <th>Action</th>
    <th>Asset ID / Barcode</th>
    <th>Brand</th>
    <th>Model</th>
    <th>Serial No</th>
    <th>Purchase Date</th>
    
</tr>
</thead>

<tbody>
<?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<tr>
    <td><?php echo e($loop->iteration); ?></td>
    <td>
        <a href="<?php echo e(route('asset.edit', $a->id)); ?>" class="btn btn-sm btn-primary">Edit</a>
    </td>

    <td class="text-center">
        <?php echo DNS1D::getBarcodeHTML($a->asset_id, 'C128', 1.4, 40); ?>

        <br><b><?php echo e($a->asset_id); ?></b>
    </td>

    <td><?php echo e($a->brand); ?></td>
    <td><?php echo e($a->model); ?></td>
    <td><?php echo e($a->serial_no); ?></td>
    <td><?php echo e($a->purchase_date); ?></td>
    
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tbody>
</table>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\asset\index.blade.php ENDPATH**/ ?>