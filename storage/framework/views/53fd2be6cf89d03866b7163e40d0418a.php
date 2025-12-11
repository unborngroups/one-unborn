
<?php use Milon\Barcode\Facades\DNS1DFacade as DNS1D; ?>
<?php $__env->startSection('content'); ?>

<div class="container">
<h3 class="mb-3">Asset List</h3>

    <?php if($permissions->can_add): ?>

        <a href="<?php echo e(route('asset.create')); ?>" class="btn btn-success float-end mb-3">

            <i class="bi bi-plus-circle"></i> Add New Asset

        </a>

         <?php endif; ?>


        <div class="card-header bg-light d-flex justify-content-between">

            <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search...">

        </div>

<table class="table table-bordered" id="asset">
<thead class="table-dark-primary">
<tr>
    <th>S.No</th>
    <th>Action</th>
    <th>Asset ID / Barcode</th>
    <th>Brand</th>
    <th>Model</th>
    <th>Serial No</th>
    <th>Purchase Date</th>
    <th>Print</th>
    
</tr>
</thead>

<tbody>
<?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<tr>
    <td>
        <?php echo e($assets instanceof \Illuminate\Pagination\LengthAwarePaginator 
            ? $loop->iteration + ($assets->currentPage() - 1) * $assets->perPage() 
            : $loop->iteration); ?>

    </td>
    <td>
        <!-- edit action -->
    <?php if($permissions->can_edit): ?>
    <a href="<?php echo e(route('asset.edit', $a->id)); ?>" class="btn btn-sm btn-primary">Edit</a>
    <?php endif; ?>

        <!-- delete action -->
    <?php if($permissions->can_delete): ?>
            <form action="<?php echo e(route('asset.destroy', $a->id)); ?>" method="POST" style="display:inline-block;">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
    <button onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">Delete</button>
</form>
        <?php endif; ?>
        
 

                                   <?php if($permissions->can_view): ?>

                                   <a href="<?php echo e(route('asset.view', $a->id)); ?>" class="btn btn-sm btn-warning">

                                    <i class="bi bi-eye"></i>

                                    </a>

                                     <?php endif; ?>
           
    </td>

   <td>
   <img src="/barcode.php?code=<?php echo e($a->asset_id); ?>" height="2px" >
<p><?php echo e($a->asset_id); ?></p>

</td>


    <td><?php echo e($a->brand); ?></td>
    <td><?php echo e($a->model); ?></td>
    <td><?php echo e($a->serial_no); ?></td>
    <td><?php echo e($a->purchase_date); ?></td>
    <!-- print button -->
    <td>
        <a href="<?php echo e(route('asset.print', $a->id)); ?>" class="btn btn-dark" target="_blank">
    <i class="bi bi-printer"></i> Print
</a>
    </td>
    
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tbody>
</table>
</div>
<script>
    
document.getElementById('tableSearch').addEventListener('keyup', function() {

    let value = this.value.toLowerCase();

    document.querySelectorAll('#asset tbody tr').forEach(row => {

        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';

    });

});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\asset\index.blade.php ENDPATH**/ ?>