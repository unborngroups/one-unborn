



<?php $__env->startSection('content'); ?>

<div class="container-fluid">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header text-dark d-flex justify-content-between align-items-center">

                    <h4 class="mb-0">

                        <i class="bi bi-receipt"></i> Purchase Orders

                    </h4>

                    <a href="<?php echo e(route('sm.purchaseorder.create')); ?>" class="btn btn-success">

                            <i class="bi bi-plus-circle"></i> Create New Purchase Order

                        </a>

                </div>

                <div class="card-body">
                    

              



                    

                    <?php if(session('success')): ?>

                        <div class="alert alert-success alert-dismissible fade show" role="alert">

                            <?php echo e(session('success')); ?>


                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

                        </div>

                    <?php endif; ?>



                    <?php if(session('error')): ?>

                        <div class="alert alert-danger alert-dismissible fade show" role="alert">

                            <?php echo e(session('error')); ?>


                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

                        </div>

                    <?php endif; ?>



                    

                    <div class="table-responsive">

                        <table class="table table-striped table-bordered" id="purchaseorder">

                            <thead class="table-dark-primary">

                                <tr>
                                <th width="50"><input type="checkbox" id="select_all"></th>


                                    <th>S.No</th>

                                    <th>Actions</th>

                                    <th>PO Number</th>

                                    <th>PO Date</th>

                                    <th>Client Name</th>

                                    <th>Feasibility ID</th>

                                    <th>No. of Links</th>

                                    <th>Total Cost</th>

                                    <th>Status</th>

                                    

                                </tr>

                            </thead>

                            <tbody>

                                <?php $__empty_1 = true; $__currentLoopData = $purchaseOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                                    <tr>
                                        <td>
                                    <input type="checkbox" class="row-checkbox" value="<?php echo e($po->id); ?>">
                                </td>

                                        <td><?php echo e($index + 1); ?></td>

                                        <td class="text-center d-flex justify-content-center gap-1">

                                

                                <?php if($permissions->can_edit): ?>

                               <a href="<?php echo e(route('sm.purchaseorder.edit', $po->id)); ?>" class="btn btn-sm btn-primary">

                                    <i class="bi bi-pencil"></i>

                                </a>

                                <?php endif; ?>



                                

                                <form action="<?php echo e(route('sm.purchaseorder.toggle-status', $po->id)); ?>" method="POST" class="d-inline">

                                    <?php echo csrf_field(); ?>

                                    <?php echo method_field('PATCH'); ?>

                                     <button type="submit" class="btn btn-sm <?php echo e($po->status == 'Active' ? 'btn-success' : 'btn-secondary'); ?>">

                                <?php echo e($po->status); ?>


                                    </button>

                                </form>



                                 

                                 <?php if($permissions->can_delete): ?>

                                 <form action="<?php echo e(route('sm.purchaseorder.destroy',$po->id)); ?>" method="POST" class="d-inline">

                                    <?php echo csrf_field(); ?>

                                    <?php echo method_field('DELETE'); ?> 

                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this Purchase Order?')">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </form>

                                   <?php endif; ?>

                                

                                   <?php if($permissions->can_view): ?>

                                   <a href="<?php echo e(route('sm.purchaseorder.view', $po->id)); ?>" class="btn btn-sm btn-warning">

                                    <i class="bi bi-eye"></i>

                                    </a>

                                     <?php endif; ?>



                            </td>

                                       

                                        <td>

                                            <strong class="text-primary"><?php echo e($po->po_number); ?></strong>

                                        </td>

                                        <td><?php echo e($po->po_date->format('d-m-Y')); ?></td>

                                        <td><?php echo e($po->feasibility->client->client_name ?? 'N/A'); ?></td>

                                        <td><?php echo e($po->feasibility->feasibility_request_id ?? 'N/A'); ?></td>

                                        <td><?php echo e($po->no_of_links); ?></td>

                                        <td>

                                            ₹<?php echo e(number_format(($po->arc_per_link + $po->otc_per_link + $po->static_ip_cost_per_link) * $po->no_of_links, 2)); ?>


                                        </td>

                        <td>

                            <?php if($po->status === 'Active'): ?>

                                <span class="badge bg-success"><?php echo e($po->status); ?></span>

                            <?php else: ?>

                                <span class="badge bg-danger"><?php echo e($po->status); ?></span>

                            <?php endif; ?>

                        </td>                                    </tr>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                                    <tr>

                                        <td colspan="9" class="text-center text-muted">

                                            <i class="bi bi-inbox"></i> No Purchase Orders found. 

                                            <a href="<?php echo e(route('sm.purchaseorder.create')); ?>" class="text-decoration-none">Create your first Purchase Order</a>

                                        </td>

                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select_all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');

    selectAll.addEventListener('change', function() {
        rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
    });

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const allChecked = [...rowCheckboxes].every(x => x.checked);
            const noneChecked = [...rowCheckboxes].every(x => !x.checked);

            selectAll.checked = allChecked;
            selectAll.indeterminate = !allChecked && !noneChecked;
        });
    });
});

    
document.getElementById('tableSearch').addEventListener('keyup', function() {

    let value = this.value.toLowerCase();

    document.querySelectorAll('#purchaseorder tbody tr').forEach(row => {

        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';

    });

});


</script>
<style>
    .table th,  .table td {
    width: 230px;
    white-space: nowrap;
    text-align: center;
    }
</style>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/sm/purchaseorder/index.blade.php ENDPATH**/ ?>