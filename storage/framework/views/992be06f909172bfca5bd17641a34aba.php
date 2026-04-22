



<?php $__env->startSection('content'); ?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h3 class="fw-bold text-primary">Manage User</h3>

        

 

        <?php if($permissions && $permissions->can_add): ?>

    <a href="<?php echo e(route('users.create')); ?>" class="btn btn-success">

        <i class="bi bi-plus-circle"></i> Add New User

    </a>

<?php endif; ?>

    </div>



    

    <?php if(session('success')): ?>

        <div class="alert alert-success"><?php echo e(session('success')); ?></div>

    <?php endif; ?>



    

    <div class="card shadow-lg border-0">
        
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
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
         <form id="bulkDeleteForm" action="<?php echo e(route('vendors.bulk-delete')); ?>" method="POST" class="d-inline">
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

            <table class="table table-striped table-hover table-bordered align-middle mb-0" id="userTable">

                <thead class="table-dark-primary text-center">

                    <tr>

                        <th><input type="checkbox" id="selectAll"></th>

                        <th>S.No</th>

                        <th>Action</th>

                        <th>Name</th>

                        <th>User Type</th>

                        <th>Email</th>

                        <th>Mobile</th>

                        <th>Company</th>

                        <th class="col">Date of Birth</th>

                        <th class="col">Date of Join</th>

                        <th class="col">Status</th>

                    </tr>

                </thead>

                <tbody>

                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                        <tr>

                            <td><input type="checkbox" class="rowCheckbox" value="<?php echo e($user->id); ?>"></td>

                            <td class="text-center"><?php echo e($index + 1); ?></td>



                            

                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                

                                <?php if($permissions->can_edit): ?>

                            <a href="<?php echo e(route('users.edit', $user)); ?>" class="btn btn-sm btn-primary">

                            <i class="bi bi-pencil"></i>

                            </a>

                                <?php endif; ?>


                            

                                 <?php if($permissions->can_delete): ?>

                            <form action="<?php echo e(route('users.destroy', $user)); ?>" method="POST" class="d-inline">

                                 <?php echo csrf_field(); ?>

                                 <?php echo method_field('DELETE'); ?>

                            <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">

                            <i class="bi bi-trash"></i>

                            </button>

                            </form>

                                <?php endif; ?>          

                                

                                

                                <?php

                                $role = strtolower(auth()->user()->userType->name);

                                $canToggle = in_array($role, ['superadmin', 'admin']);

                                ?>

                                <form action="<?php echo e(route('users.toggle-status', $user->id)); ?>" method="POST" class="d-inline">

                                    <?php echo csrf_field(); ?>

                                    <?php echo method_field('PATCH'); ?>

                                    <button type="submit" class="btn btn-sm <?php echo e($user->status === 'Active' ? 'btn-success' : 'btn-secondary'); ?>"   <?php echo e(!$canToggle ? 'disabled title=You don\'t have permission' : ''); ?>>

                                    

                                    <?php echo e($user->status); ?>


                                    </button>

                                    </form>



                                    

                                   <?php if($permissions->can_view): ?>

                                   <a href="<?php echo e(route('users.view', $user->id)); ?>" class="btn btn-sm btn-warning">

                                    <i class="bi bi-eye"></i>

                                    </a>

                                     <?php endif; ?>



                                     <?php

                                     $role = strtolower(auth()->user()->userType->name ?? '');

                                     ?>

                                     <?php if($permissions->can_edit && in_array($role, ['superadmin', 'admin'])): ?>
                                    <a href="<?php echo e(route('menus.editPrivileges', $user->id)); ?>" class="btn btn-sm btn-info">
                                        <i class="bi bi-gear"></i> 

                                    </a>

                                    <?php endif; ?>

                                     <ul class="sidebar-menu">

                            </td>



                            <td class="col"><?php echo e(ucfirst($user->name)); ?></td>



                            

                            <td class="text-center col ">

                                <span class="badge <?php echo e($user->userType->name === 'superadmin' ? 'bg-dark' : 'bg-info'); ?>">

                                    <?php echo e(ucfirst($user->userType->name ?? '-')); ?>


                                </span>

                            </td>



                            <td><?php echo e($user->email); ?></td>

                            <td><?php echo e($user->mobile ?? '-'); ?></td>



                            

                            <td class="col">

                                <?php echo e($user->companies->pluck('company_name')->join(', ') ?: 'No Company Assigned'); ?>


                            </td>



                            

                            <td class="col"><?php echo e($user->Date_of_Birth ? \Carbon\Carbon::parse($user->Date_of_Birth)->format('d M Y') : '-'); ?></td>

                            <td class="col"><?php echo e($user->Date_of_Joining ? \Carbon\Carbon::parse($user->Date_of_Joining)->format('d M Y') : '-'); ?></td>




                            
                            <td class="text-center">
                                <?php
                                    $latestLog = $user->loginLogs()->latest()->first();
                                    $isOnline = false;
                                    if ($latestLog && $latestLog->last_activity) {
                                        $isOnline = now()->diffInMinutes($latestLog->last_activity) < 15;
                                    }
                                ?>
                                <span class="badge <?php echo e($isOnline ? 'bg-success' : 'bg-secondary'); ?>">
                                    <?php echo e($isOnline ? 'Online' : 'Offline'); ?>

                                </span>
                            </td>

                        </tr>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                        <tr>

                            <td colspan="11" class="text-center text-muted">No Users Found</td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>
        <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
            <div class="text-muted small">
                Showing
                <?php echo e($users->firstItem() ?? 0); ?>

                to
                <?php echo e($users->lastItem() ?? 0); ?>

                of
                <?php echo e(number_format($users->total())); ?> entries
            </div>
            <div class="ms-auto">
                <nav>
                    <ul class="pagination mb-0">
                        
                        <?php if($users->onFirstPage()): ?>
                            <li class="page-item disabled"><span class="page-link">Previous</span></li>
                        <?php else: ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($users->previousPageUrl()); ?>" rel="prev">Previous</a></li>
                        <?php endif; ?>

                        
                        <?php
                            $total = $users->lastPage();
                            $current = $users->currentPage();
                            $max = 5; // Number of page links to show
                            $start = max(1, $current - floor($max / 2));
                            $end = min($total, $start + $max - 1);
                            if ($end - $start < $max - 1) {
                                $start = max(1, $end - $max + 1);
                            }
                        ?>

                        <?php if($start > 1): ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($users->url(1)); ?>">1</a></li>
                            <?php if($start > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for($i = $start; $i <= $end; $i++): ?>
                            <?php if($i == $current): ?>
                                <li class="page-item active"><span class="page-link"><?php echo e($i); ?></span></li>
                            <?php else: ?>
                                <li class="page-item"><a class="page-link" href="<?php echo e($users->url($i)); ?>"><?php echo e($i); ?></a></li>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if($end < $total): ?>
                            <?php if($end < $total - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($users->url($total)); ?>"><?php echo e($total); ?></a></li>
                        <?php endif; ?>
  
                        
                        <?php if($users->hasMorePages()): ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($users->nextPageUrl()); ?>" rel="next">Next</a></li>
                        <?php else: ?>
                            <li class="page-item disabled"><span class="page-link">Next</span></li>
                        <?php endif; ?>
                    </ul>
                </nav>
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

    if (!confirm(`Delete ${selectedIds.length} selected user(s)?`)) {
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


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\users\index.blade.php ENDPATH**/ ?>