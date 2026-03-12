



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

        <form id="filterForm" method="GET" class="d-flex align-items-center gap-2 w-100">
            <label for="entriesSelect" class="mb-0">Show</label>
            <select id="entriesSelect" name="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                <option value="10" <?php echo e(request('per_page', 10) == 10 ? 'selected' : ''); ?>>10</option>
                <option value="25" <?php echo e(request('per_page') == 25 ? 'selected' : ''); ?>>25</option>
                <option value="50" <?php echo e(request('per_page') == 50 ? 'selected' : ''); ?>>50</option>
                <option value="100" <?php echo e(request('per_page') == 100 ? 'selected' : ''); ?>>100</option>
                
            </select>
            <!-- <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search..."> -->
        </form>

            <!-- <h5 class="mb-0 text-danger">MANAGE USER</h5> -->

            <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search...">

             
         <?php if($permissions->can_delete): ?>
         <form id="bulkDeleteForm" action="<?php echo e(route('companies.bulk-delete')); ?>" method="POST" class="d-inline">
             <?php echo csrf_field(); ?>
             <div id="bulkDeleteInputs"></div>
         </form>
         <button id="deleteSelectedBtn" class="btn btn-danger d-none">
             <i class="bi bi-trash"></i>
         </button>
         <?php endif; ?>
        </div>
        </div>

        <div class="card-body table-responsive">


            <table class="table table-bordered table-hover align-middle" id="userTable">

                <thead class="table-dark-primary text-center">

                    <tr>

                        <th><input type="checkbox" id="selectAll"></th>

                        <th>S.No</th>

                        <th>Action</th>

                        <th>User Name</th>

                        <th class="col">Company Name</th>

                        <th class="col">CIN / LLPIN</th>

                        <th class="col">Contact No</th>

                        <th class="col">Email</th>

                        <th class="col">GST No</th>

                        <th class="col">PAN No</th>

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
                             <td><?php echo e($company->user_name); ?></td>

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
         <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
            <div class="text-muted small">
                Showing
                <?php echo e($companies->firstItem() ?? 0); ?>

                to
                <?php echo e($companies->lastItem() ?? 0); ?>

                of
                <?php echo e(number_format($companies->total())); ?> entries
            </div>
            <div class="ms-auto">
                <nav>
                    <ul class="pagination mb-0">
                        
                        <?php if($companies->onFirstPage()): ?>
                            <li class="page-item disabled"><span class="page-link">Previous</span></li>
                        <?php else: ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($companies->previousPageUrl()); ?>" rel="prev">Previous</a></li>
                        <?php endif; ?>

                        
                        <?php
                            $total = $companies->lastPage();
                            $current = $companies->currentPage();
                            $max = 5; // Number of page links to show
                            $start = max(1, $current - floor($max / 2));
                            $end = min($total, $start + $max - 1);
                            if ($end - $start < $max - 1) {
                                $start = max(1, $end - $max + 1);
                            }
                        ?>

                        <?php if($start > 1): ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($companies->url(1)); ?>">1</a></li>
                            <?php if($start > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for($i = $start; $i <= $end; $i++): ?>
                            <?php if($i == $current): ?>
                                <li class="page-item active"><span class="page-link"><?php echo e($i); ?></span></li>
                            <?php else: ?>
                                <li class="page-item"><a class="page-link" href="<?php echo e($companies->url($i)); ?>"><?php echo e($i); ?></a></li>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if($end < $total): ?>
                            <?php if($end < $total - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($companies->url($total)); ?>"><?php echo e($total); ?></a></li>
                        <?php endif; ?>

                        
                        <?php if($companies->hasMorePages()): ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($companies->nextPageUrl()); ?>" rel="next">Next</a></li>
                        <?php else: ?>
                            <li class="page-item disabled"><span class="page-link">Next</span></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
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




// ✅ Select / Deselect all checkboxes

document.getElementById('selectAll').addEventListener('change', function(){
    let isChecked = this.checked;
    document.querySelectorAll('.rowCheckbox').forEach(cb => {
        cb.checked = isChecked;
    });
    updateDeleteButtonVisibility();
});

// Update Delete Button Visibility
document.getElementById('deleteSelectedBtn')?.addEventListener('click', function () {
    const selectedIds = Array.from(document.querySelectorAll('.rowCheckbox:checked')).map(cb => cb.value);
    if (!selectedIds.length) {
        return;
    }

    if (!confirm(`Delete ${selectedIds.length} selected company(s)?`)) {
        return;
    }

    const inputsContainer = document.getElementById('bulkDeleteInputs');
    inputsContainer.innerHTML = '';
    selectedIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        inputsContainer.appendChild(input);
    });

    document.getElementById('bulkDeleteForm')?.submit();
});
// 
function updateDeleteButtonVisibility() {
    const totalChecked = document.querySelectorAll('.rowCheckbox:checked').length;
    const deleteBtn = document.getElementById('deleteSelectedBtn');
    if (!deleteBtn) {
        return;
    }
    if (totalChecked > 0) {
        deleteBtn.classList.remove('d-none');
    } else {
        deleteBtn.classList.add('d-none');
    }
}

document.querySelectorAll('.rowCheckbox').forEach(cb => {
    cb.addEventListener('change', updateDeleteButtonVisibility);
});

// Keep the delete button state correct on page load
updateDeleteButtonVisibility();



</script>





<style>

    .col {

    width: 130px;

    white-space: nowrap;

}

</style>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/companies/index.blade.php ENDPATH**/ ?>