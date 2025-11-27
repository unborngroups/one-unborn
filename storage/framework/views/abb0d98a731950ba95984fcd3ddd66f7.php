



<?php $__env->startSection('content'); ?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h3 class="fw-bold text-primary">Manage feasibility</h3>

       

    </div>


    <?php if(session('success')): ?>

        <div class="alert alert-success"><?php echo e(session('success')); ?></div>

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

                        <th>Action</th>

                        <th class="col">Feasibility Request ID</th>

                        <th class="col">Type of Service</th>

                        <th class="col">Company Name</th>

                        <th class="col">Client Name</th>

                        <th>Status</th>

                    </tr>

                </thead>

                <tbody>

                    <?php $__empty_1 = true; $__currentLoopData = $feasibilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $feasibility): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                        <tr>

                            <td><input type="checkbox" class="rowCheckbox" value="<?php echo e($feasibility->id); ?>"></td>

                            <td class="text-center"><?php echo e($index+1); ?></td>

                             <td class="text-center">

                                <div class="d-flex justify-content-center gap-1">

                                

                                <a href="<?php echo e(route('feasibility.edit', $feasibility)); ?>" class="btn btn-sm btn-primary" title="Edit">

                                    <i class="bi bi-pencil"></i>

                                </a>

                                 

                                   <a href="<?php echo e(route('feasibility.show', $feasibility->id)); ?>" class="btn btn-sm btn-info" title="View">

                                    <i class="bi bi-eye"></i>

                                    </a>


                                </div>

                            </td>

                            <td class="col"><?php echo e($feasibility->feasibility_request_id ?? 'N/A'); ?></td>

                            <td class="col"><?php echo e($feasibility->type_of_service ?? 'N/A'); ?></td>

                            <td class="col"><?php echo e($feasibility->company->company_name ?? 'N/A'); ?></td>

                            <td class="col"><?php echo e($feasibility->client->client_name ?? 'N/A'); ?></td>

                            <td>

                                <span class="badge bg-success">

                                    Created

                                </span>

                            </td>

                        </tr>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                        <tr><td colspan="8" class="text-center">No Feasibility Found</td></tr>

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


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\wlcome\multipleuserpage\resources\views\feasibility\index.blade.php ENDPATH**/ ?>