



<?php $__env->startSection('content'); ?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h3 class="fw-bold text-primary">Vendor Master</h3>

         

        <?php if($permissions->can_add): ?>

        <a href="<?php echo e(route('vendors.create')); ?>" class="btn btn-success">

            <i class="bi bi-plus-circle"></i> Add New Vendor

        </a>

         <?php endif; ?>

    </div>



    

    

    <?php if(session('success')): ?>

        <div class="alert alert-success">

            <?php echo e(session('success')); ?>


        </div>

    <?php endif; ?>



     

    <div class="card shadow border-0">

          

        <div class="card-header bg-light d-flex justify-content-between">

            <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search...">

        </div>

        

        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover align-middle" id="vendorTable">

                <thead class="table-dark-primary text-center">

                    <tr>

                        

                        <th><input type="checkbox" id="selectAll"></th>

                        <th>S.No</th>

                        <th>Action</th>

                        <th class="col">Vendor Code</th>

                        <th class="col">Vendor Name</th>

                        <th class="col">Business Name</th>

                        <th class="col">Contact Person</th>

                        <th class="col">Contact Email</th>

                        <th class="col">Contact Mobile</th>

                        <!-- <th>GSTIN</th> -->

                        <!-- <th>PAN No</th> -->

                        <!-- <th>bank_account_no</th> -->

                        <!-- <th>ifsc_code</th> -->

                        <th>Status</th>

                    </tr>

                </thead>

                <tbody>

                    <?php $__empty_1 = true; $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                        <tr>

                            <td><input type="checkbox" class="rowCheckbox" value="<?php echo e($vendor->id); ?>"></td>

                            <td class="text-center"><?php echo e($key+1); ?></td>

                            <td class="text-center d-flex justify-content-center gap-1">

                                 

                                <?php if($permissions->can_edit): ?>

                                <a href="<?php echo e(route('vendors.edit', $vendor)); ?>" class="btn btn-sm btn-primary">

                                    <i class="bi bi-pencil"></i>

                                </a>

                                <?php endif; ?>



                               

                                 <?php if($permissions->can_delete): ?>

                                <form action="<?php echo e(route('vendors.destroy', $vendor)); ?>" method="POST" class="d-inline">

                                    <?php echo csrf_field(); ?>

                                    <?php echo method_field('DELETE'); ?> 

                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this Vendor?')">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </form>

                                <?php endif; ?>



                                

                                <form action="<?php echo e(route('vendors.toggle-status', $vendor->id)); ?>" method="POST" class="d-inline">

                                    <?php echo csrf_field(); ?>

                                    <?php echo method_field('PATCH'); ?>

                                    <button type="submit" class="btn btn-sm <?php echo e($vendor->status == 'Active' ? 'btn-success' : 'btn-secondary'); ?>">

                                        <?php echo e($vendor->status); ?>


                                    </button>

                                </form>

                                 

                                   <?php if($permissions->can_view): ?>

                                <!-- view path -->

                                   <a href="<?php echo e(route('vendors.view', $vendor->id)); ?>" class="btn btn-sm btn-warning">

                                    <i class="bi bi-eye"></i>

                                    </a>

                                    <?php endif; ?>

                                

                            </td>

                            <td><?php echo e($vendor->vendor_code); ?></td>

                            <td class="col"><?php echo e($vendor->vendor_name); ?></td>

                            <td class="col"><?php echo e($vendor->business_display_name ?? '-'); ?></td>

                            <td class="col"><?php echo e($vendor->contact_person_name ?? '-'); ?></td>

                            <td><?php echo e($vendor->contact_person_email ?? '-'); ?></td>

                            <td><?php echo e($vendor->contact_person_mobile ?? '-'); ?></td>

                            <!-- <td><?php echo e($vendor->gstin ?? '-'); ?></td> -->

                            <!-- <td><?php echo e($vendor->pan_no ?? '-'); ?></td> -->

                            <!-- <td><?php echo e($vendor->bank_account_no ?? '-'); ?></td> -->

                            <!-- <td><?php echo e($vendor->ifsc_code ?? '-'); ?></td> -->

                            

                            <td>

                                <span class="badge <?php echo e($vendor->status == 'Active' ? 'bg-success' : 'bg-danger'); ?>">

                                    <?php echo e($vendor->status); ?>


                                </span>

                            </td>

                        </tr>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                        <tr>

                            <td colspan="12" class="text-center text-muted">No vendors found.</td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

 

<script>

document.getElementById('tableSearch').addEventListener('keyup', function() {

    let value = this.value.toLowerCase();

    document.querySelectorAll('#vendorTable tbody tr').forEach(row => {

        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';

    });

});



// ✅ Select / Deselect all checkboxes

document.getElementById('selectAll').addEventListener('change', function(){

    let isChecked = this.checked;

    document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = isChecked);

});

</script>

<style>

    .col {

    width: 130px;

    white-space: nowrap;

}

</style>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\wlcome\multipleuserpage\resources\views\vendors\index.blade.php ENDPATH**/ ?>