



<?php $__env->startSection('content'); ?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h3 class="fw-bold text-primary">Client Master</h3>

        

        <?php if($permissions->can_add): ?>

        <a href="<?php echo e(route('clients.create')); ?>" class="btn btn-success">

            <i class="bi bi-plus-circle"></i> Add New Client

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

            <!-- <h5 class="mb-0 text-danger">MANAGE USER</h5> -->

            <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search...">

        </div>

        <div class="card-body table-responsive">

            <!-- <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search..."> -->

            <table class="table table-bordered table-hover align-middle" id="userTable">

                <thead class="table-dark-primary text-center">

                    <tr>

                        <th><input type="checkbox" id="selectAll"></th>

                        <th>S.No</th>

                        <th class="col">Action</th>

                        <th class="col">Client Code</th>

                        <th class="col">Client Name</th>

                        <th class="col">Business Name</th>

                        <!-- <th class="col">Billing SPOC</th> -->

                        <!-- <th class="col">Billing Email</th> -->

                        <!-- <th class="col">GSTIN</th> -->

                        <!-- <th>Invoice Email</th> -->

                        <!-- <th>Invoice CC</th>  -->

                        <th class="col">Technical SPOC</th>
                        <th class="col">Technical SPOC Email</th>
                        <th class="col">Technical SPOC Mobile</th>


                        <th>Status</th>

                        <!-- <th>Created At</th> -->

                        <!-- <th class="text-center">Actions</th> -->

                    </tr>

                </thead>

                <tbody>

                    <?php $__empty_1 = true; $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                        <tr>

                              <td><input type="checkbox" class="rowCheckbox" value="<?php echo e($client->id); ?>"></td>

                            <td class="text-center"><?php echo e($key+1); ?></td>

                             <td class="text-center d-flex justify-content-center gap-1">

                                

                                <?php if($permissions->can_edit): ?>

                               <a href="<?php echo e(route('clients.edit', $client)); ?>" class="btn btn-sm btn-primary">

                                    <i class="bi bi-pencil"></i>

                                </a>

                                <?php endif; ?>



                                 

                                 <?php if($permissions->can_delete): ?>

                                 <form action="<?php echo e(route('clients.destroy',$client)); ?>" method="POST" class="d-inline">

                                    <?php echo csrf_field(); ?>

                                    <?php echo method_field('DELETE'); ?> 

                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this Client?')">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </form>

                                   <?php endif; ?>



                                   

                                <form action="<?php echo e(route('clients.toggle-status', $client->id)); ?>" method="POST" class="d-inline">

                                    <?php echo csrf_field(); ?>

                                    <?php echo method_field('PATCH'); ?>

                                     <button type="submit" class="btn btn-sm <?php echo e($client->status == 'Active' ? 'btn-success' : 'btn-secondary'); ?>">

                                <?php echo e($client->status); ?>


                                    </button>

                                </form>



                                

                                   <?php if($permissions->can_view): ?>

                                   <a href="<?php echo e(route('clients.view', $client->id)); ?>" class="btn btn-sm btn-warning">

                                    <i class="bi bi-eye"></i>

                                    </a>

                                     <?php endif; ?>



                            </td>

                            <!-- <td><?php echo e($key+1); ?></td> -->

                            <td><?php echo e($client->client_code); ?></td>

                            <td class="col"><?php echo e($client->client_name); ?></td>

                            <td class="col"><?php echo e($client->business_display_name ?? '-'); ?></td>

                            <!-- <td class="col"><?php echo e($client->billing_spoc_name); ?></td> -->

                            <!-- <td><?php echo e($client->billing_spoc_email); ?></td> -->

                            <!-- <td><?php echo e($client->gstin); ?></td> -->

                            <!-- <td class="col"><?php echo e($client->invoice_email ?? '-'); ?></td> -->

                            <!-- <td class="col"><?php echo e($client->invoice_cc ?? '-'); ?></td>    -->

                            <td class="col"><?php echo e($client->support_spoc_name ?? '-'); ?></td>
                            <td class="col"><?php echo e($client->support_spoc_email ?? '-'); ?></td>
                            <td class="col"><?php echo e($client->support_spoc_mobile ?? '-'); ?></td>

                            <td>

                                <span class="badge <?php echo e($client->status == 'Active' ? 'bg-success' : 'bg-danger'); ?>">

                                <?php echo e($client->status); ?>


                                </span>

                            </td>

                        </tr>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                        <tr>

                            <td colspan="13" class="text-center text-muted">No clients found.</td>

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

    document.querySelectorAll('#userTable tbody tr').forEach(row => {

        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';

    });

});





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


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/clients/index.blade.php ENDPATH**/ ?>