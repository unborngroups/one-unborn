

<?php $__env->startSection('content'); ?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h3 class="fw-bold text-primary">Upcoming Renewals</h3>

        

        <?php if($permissions->can_add): ?>

        <a href="<?php echo e(route('operations.renewals.create')); ?>" class="btn btn-success">

            <i class="bi bi-plus-circle"></i> Add New Renewal

        </a>

         <?php endif; ?>
        


    </div>


    

    <?php if(session('success')): ?>

        <div class="alert alert-success">

            <?php echo e(session('success')); ?>


        </div>

    <?php endif; ?>

   <div class="card-header bg-light d-flex justify-content-between">
        <form id="filterForm" method="GET" class="d-flex align-items-center gap-2 w-100">
            <label for="entriesSelect" class="mb-0">Show</label>
            <select id="entriesSelect" name="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                <option value="10" <?php echo e(request('per_page', 10) == 10 ? 'selected' : ''); ?>>10</option>
                <option value="25" <?php echo e(request('per_page') == 25 ? 'selected' : ''); ?>>25</option>
                <option value="50" <?php echo e(request('per_page') == 50 ? 'selected' : ''); ?>>50</option>
                <option value="100" <?php echo e(request('per_page') == 100 ? 'selected' : ''); ?>>100</option>
                
            </select>
            <!-- <span>entries</span> -->
            <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search...">
        </form>
   </div>


    

    <div class="card shadow border-0">

        <div class="card-body table-responsive">

            <!-- <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search..."> -->

            <table class="table table-bordered table-hover align-middle" id="renewalTable">

                <thead class="table-dark-primary text-center">

                    <tr>

                        <th><input type="checkbox" id="selectAll"></th>

                        <th>S.No</th>

                        <th class="col">Action</th>

                        <th class="col">Date of Renewal</th>

                        <th class="col">Date of Expiry</th>

                        <th>Status</th>

                    </tr>

                </thead>

                <tbody>

                    <?php $__empty_1 = true; $__currentLoopData = $renewals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $renewal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                        <tr>

                              <td><input type="checkbox" class="rowCheckbox" value="<?php echo e($renewal->id); ?>"></td>
                            <td class="text-center"><?php echo e($key+1); ?></td>

                             <td class="text-center d-flex justify-content-center gap-1">

                                

                                <?php if($permissions->can_edit): ?>

                               <a href="<?php echo e(route('operations.renewals.edit', $renewal)); ?>" class="btn btn-sm btn-primary">

                                    <i class="bi bi-pencil"></i>

                                </a>

                                <?php endif; ?>
                                 

                                 <?php if($permissions->can_delete): ?>

                                 <form action="<?php echo e(route('operations.renewals.destroy',$renewal)); ?>" method="POST" class="d-inline">

                                    <?php echo csrf_field(); ?>

                                    <?php echo method_field('DELETE'); ?> 

                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this Client?')">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </form>

                                   <?php endif; ?>
                                   
                                   <?php if($renewal->status): ?>
                                   <form action="<?php echo e(route('operations.renewals.toggle-status', $renewal->id)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="btn btn-sm <?php echo e($renewal->status == 'Active' ? 'btn-success' : 'btn-secondary'); ?>">
                                        <?php echo e($renewal->status); ?>

                                    </button>
                                    </form>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">Active</span>
                                    <?php endif; ?>

                                

                                   <?php if($permissions->can_view): ?>

                                   <a href="<?php echo e(route('operations.renewals.view', $renewal->id)); ?>" class="btn btn-sm btn-warning">

                                    <i class="bi bi-eye"></i>

                                    </a>

                                     <?php endif; ?>
                            </td>

                            <td><?php echo e($renewal->date_of_renewal); ?></td>
                            <td><?php echo e($renewal->new_expiry_date); ?></td>

                            <td>

                                <span class="badge <?php echo e($renewal->status == 'Active' ? 'bg-success' : 'bg-danger'); ?>">

                                <?php echo e($renewal->status); ?>


                                </span>

                            </td>

                        </tr>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                        <tr>

                            <td colspan="13" class="text-center text-muted">No renewals found.</td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>


       <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
            <div class="text-muted small">
                Showing
                <?php echo e($renewals->firstItem() ?? 0); ?>

                to
                <?php echo e($renewals->lastItem() ?? 0); ?>

                of
                <?php echo e(number_format($renewals->total())); ?> entries
            </div>
            <div class="ms-auto">
                <nav>
                    <ul class="pagination mb-0">
                        
                        <?php if($renewals->onFirstPage()): ?>
                            <li class="page-item disabled"><span class="page-link">Previous</span></li>
                        <?php else: ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($renewals->previousPageUrl()); ?>" rel="prev">Previous</a></li>
                        <?php endif; ?>

                        
                        <?php
                            $total = $renewals->lastPage();
                            $current = $renewals->currentPage();
                            $max = 5; // Number of page links to show
                            $start = max(1, $current - floor($max / 2));
                            $end = min($total, $start + $max - 1);
                            if ($end - $start < $max - 1) {
                                $start = max(1, $end - $max + 1);
                            }
                        ?>

                        <?php if($start > 1): ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($renewals->url(1)); ?>">1</a></li>
                            <?php if($start > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for($i = $start; $i <= $end; $i++): ?>
                            <?php if($i == $current): ?>
                                <li class="page-item active"><span class="page-link"><?php echo e($i); ?></span></li>
                            <?php else: ?>
                                <li class="page-item"><a class="page-link" href="<?php echo e($renewals->url($i)); ?>"><?php echo e($i); ?></a></li>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if($end < $total): ?>
                            <?php if($end < $total - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($renewals->url($total)); ?>"><?php echo e($total); ?></a></li>
                        <?php endif; ?>

                        
                        <?php if($renewals->hasMorePages()): ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($renewals->nextPageUrl()); ?>" rel="next">Next</a></li>
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

    document.querySelectorAll('#renewalTable tbody tr').forEach(row => {

        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';

    });

});

document.getElementById('selectAll').addEventListener('change', function(){
    let isChecked = this.checked;
    document.querySelectorAll('.rowCheckbox').forEach(cb => {
        cb.checked = isChecked;
    });
    updateDeleteButtonVisibility();
});

document.getElementById('deleteSelectedBtn')?.addEventListener('click', function () {
    const selectedIds = Array.from(document.querySelectorAll('.rowCheckbox:checked')).map(cb => cb.value);
    if (!selectedIds.length) {
        return;
    }

    if (!confirm(`Delete ${selectedIds.length} selected renewal(s)?`)) {
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/operations/renewals/index.blade.php ENDPATH**/ ?>