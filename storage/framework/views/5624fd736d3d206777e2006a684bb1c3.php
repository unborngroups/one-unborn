

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-primary">Manage Companies</h3>
       
        <a href="<?php echo e(route('companies.create')); ?>" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add Company
        </a>
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
                <thead class="table-primary text-center">
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>S.No</th>
                        <th>Action</th>
                        <th>Company Name</th>
                        <th>CIN / LLPIN</th>
                        <th>Contact No</th>
                        <th>Email</th>
                        <th>GST No</th>
                        <th>PAN No</th>
                        <!-- <th>TAN No</th>       
                        <th>Logo</th>
                        <th>Normal Sign</th>
                        <th>Digital Sign</th> -->
                        <th>Status</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><input type="checkbox" class="rowCheckbox" value="<?php echo e($company->id); ?>"></td>
                            <td class="text-center"><?php echo e($index+1); ?></td>
                             <td class="text-center d-flex justify-content-center gap-1">
                                
                                <?php if($permissions->can_edit): ?>
                                <a href="<?php echo e(route('companies.edit', $company)); ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php endif; ?>

                                 
                                 <?php if($permissions->can_delete): ?>
                                <form action="<?php echo e(route('companies.destroy',$company)); ?>" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <?php echo method_field('DELETE'); ?> 
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                 <?php endif; ?>

                                
                                <form action="<?php echo e(route('companies.toggle-status', $company)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="btn btn-sm <?php echo e($company->status === 'Active' ? 'btn-success' : 'btn-secondary'); ?>">
                                        <?php echo e($company->status); ?>

                                    </button>
                                </form>
                                <a href="<?php echo e(route('companies.email.config', $company->id)); ?>" class="btn btn-sm btn-warning" title="Email Config">
                                    <i class="bi bi-envelope"></i>
                                </a>
                                 
                                   <?php if($permissions->can_view): ?>
                                 <!-- view path -->
                                   <a href="<?php echo e(route('companies.view', $company->id)); ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-eye"></i>
                                    </a>
                                    <?php endif; ?>

                                
                            </td>
                            <!-- <td><?php echo e($index + 1); ?></td> -->
                            <td class="col"><?php echo e($company->company_name); ?></td>
                            <td class="col"><?php echo e($company->business_number); ?></td>
                            <td><?php echo e($company->company_phone); ?></td>
                            <td>
                                <?php echo e($company->company_email); ?>

                                <!-- <br> -->
                                <!-- <?php if($company->email_2): ?><small><?php echo e($company->email_2); ?></small><?php endif; ?> -->
                            </td>
                            <td><?php echo e($company->gstin); ?></td>
                            <td><?php echo e($company->pan_number); ?></td>
                            <!-- <td><?php echo e($company->tan_number); ?></td> -->
            
                            <!-- <td>
                                <?php if($company->billing_logo): ?>
                                    <img src="<?php echo e(asset('images/logos/'.$company->billing_logo)); ?>" alt="Logo" class="rounded-circle border" width="40" height="40">
                                <?php else: ?>
                                    <span class="text-muted small">No logo</span>
                                <?php endif; ?>

                            </td>
                            <td>
                                 <?php if($company->billing_sign_normal): ?>
                                    <img src="<?php echo e(asset('images/n_signs/'.$company->billing_sign_normal)); ?>" alt="Normal Sign" class="rounded-circle border" width="40" height="40">
                                <?php else: ?>
                                    <span class="text-muted small">No sign</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($company->billing_sign_digital): ?>
                                    <img src="<?php echo e(asset('images/d_signs/'.$company->billing_sign_digital)); ?>" alt="Digital Sign" class="rounded-circle border" width="40" height="40">
                                <?php else: ?>
                                    <span class="text-muted small">No sign</span>
                                <?php endif; ?>
                            </td> -->
                            <td>
                                <span class="badge bg-<?php echo e($company->status === 'Active' ? 'success' : 'danger'); ?>">
                                    <?php echo e($company->status); ?>

                                </span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="10" class="text-center">No Companies Found</td></tr>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/companies/index.blade.php ENDPATH**/ ?>