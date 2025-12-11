 



<?php $__env->startSection('content'); ?> 



<div class="container-fluid py-4">

    

    

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h3 class="fw-bold text-primary">User Type</h3>

        

        <?php if($permissions->can_add): ?>

        <a href="<?php echo e(route('usertypetable.create')); ?>" class="btn btn-success">

            <i class="bi bi-plus-circle"></i> Add New User

        </a>

         <?php endif; ?>

    </div>



    <?php if(session('success')): ?>

        

        <div class="alert alert-success"><?php echo e(session('success')); ?></div>

    <?php endif; ?>



    <div class="card shadow-lg border-0">

        <div class="card-header bg-light d-flex justify-content-between">

            <!-- <h5 class="mb-0 text-danger">USER TYPE</h5> -->

            <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search...">
 
         <?php if($permissions->can_delete): ?>
         <form id="bulkDeleteForm" action="<?php echo e(route('usertypetable.bulk-delete')); ?>" method="POST" class="d-inline">
             <?php echo csrf_field(); ?>
             <div id="bulkDeleteInputs"></div>
         </form>
         <button id="deleteSelectedBtn" class="btn btn-danger d-none">
             <i class="bi bi-trash"></i>
         </button>
         <?php endif; ?>
        </div>
        </div>



            

        <div class="card-body p-0 table-responsive">
            


            <table class="table table-hover table-bordered mb-0" id="userTable">

                <thead class="table-dark-primary text-center">

                    <tr>

                        <th><input type="checkbox" id="selectAll"></th>

                        

                        <th>S.No</th>

                        <th>Action</th>

                        <th>Name</th>
                        <th>Email</th>

                        <th>Description</th>

                        <th>Status</th>

                    </tr>

                </thead>



                <tbody>

                    

                    <?php $__currentLoopData = $usertypetable; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $usertypedata): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <tr>

                            <td><input type="checkbox" class="rowCheckbox" value="<?php echo e($usertypedata->id); ?>"></td>

                            <td class="text-center"><?php echo e($index+1); ?></td>



                            <td class="text-center d-flex justify-content-center gap-1">



                            

                                <?php if($permissions->can_edit): ?>

                                

                                <a href="<?php echo e(route('usertypetable.edit', $usertypedata)); ?>" class="btn btn-sm btn-primary">

                                    <i class="bi bi-pencil"></i>

                                </a>

                                <?php endif; ?>



                                 

                                 <?php if($permissions->can_delete): ?>



                                

                                <form action="<?php echo e(route('usertypetable.destroy', $usertypedata)); ?>" method="POST" class="d-inline">

                                    <?php echo csrf_field(); ?>

                                    <?php echo method_field('DELETE'); ?> 

                                    

                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </form>

                                 <?php endif; ?>



                                

                                <form action="<?php echo e(route('usertypetable.toggle-status', $usertypedata->id)); ?>" method="POST" class="d-inline">

                                    <?php echo csrf_field(); ?>

                                    <?php echo method_field('PATCH'); ?>

                                    

                                    <button type="submit" class="btn btn-sm <?php echo e($usertypedata->status == 'Active' ? 'btn-success' : 'btn-secondary'); ?>">

                                        <?php echo e($usertypedata->status); ?>


                                    </button>

                                </form>



                                 

                                   <?php if($permissions->can_view): ?>

                                   <a href="<?php echo e(route('usertypetable.view', $usertypedata->id)); ?>" class="btn btn-sm btn-warning">

                                    <i class="bi bi-eye"></i>

                                    </a>

                                     <?php endif; ?>


                                      <?php

                                     $role = strtolower(auth()->user()->userType->name ?? '');

                                     ?>

                                     <?php if($permissions->can_edit && in_array($role, ['superadmin', 'admin'])): ?>

                                    <a href="<?php echo e(route('menus.editUserTypePrivileges', $usertypedata->id)); ?>" class="btn btn-sm btn-info" title="Manage User Type Default Privileges">

                                        <i class="bi bi-gear"></i> 

                                    </a>

                                    <?php endif; ?>
                            </td>



                            

                            <td class="col"><?php echo e($usertypedata->name); ?></td>
                            <td class="col"><?php echo e($usertypedata->email ?? '-'); ?></td>

                            <td class="col"><?php echo e($usertypedata->Description ?? '-'); ?></td>

                            



                            <td class="text-center">

                                

                                <span class="badge <?php echo e($usertypedata->status=='Active'?'bg-success':'bg-secondary'); ?>">

                                    <?php echo e($usertypedata->status); ?>


                                </span>

                            </td>

                        </tr>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



                    

                    <?php if($usertypetable->isEmpty()): ?>

                        <tr>

                            <td colspan="9" class="text-center text-muted">No Users Found</td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>





<script>

document.getElementById('tableSearch').addEventListener('keyup', function() {

    // ✅ Filter table rows by search value

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

    if (!confirm(`Delete ${selectedIds.length} selected user type(s)?`)) {
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




<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\usertypetable\index.blade.php ENDPATH**/ ?>