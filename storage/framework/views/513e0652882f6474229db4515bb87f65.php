

<?php $__env->startSection('content'); ?>

<div class="container py-4">
    <div class="row">
    <div class="col-md-6">
    <h3 class="mb-3 text-primary float-start">Model Type List</h3>
    </div>
<div class="col-md-6">
    <a href="<?php echo e(route('assetmaster.model_type.create')); ?>" class="btn btn-success mb-3 float-end">+ Add Model Type</a>
</div>
</div>
    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <!--  -->
<div class="row">
    <div class="card-header bg-light d-flex flex-wrap align-items-center gap-2">
        <form id="filterForm" method="GET" class="d-flex align-items-center gap-2 w-100">
            <label for="entriesSelect" class="mb-0">Show</label>
            <select id="entriesSelect" name="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                <option value="10" <?php echo e(request('per_page', 10) == 10 ? 'selected' : ''); ?>>10</option>
                <option value="25" <?php echo e(request('per_page') == 25 ? 'selected' : ''); ?>>25</option>
                <option value="50" <?php echo e(request('per_page') == 50 ? 'selected' : ''); ?>>50</option>
                <option value="100" <?php echo e(request('per_page') == 100 ? 'selected' : ''); ?>>100</option>
            </select>
            <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search...">

        </form>

             
         <?php if($permissions->can_delete): ?>
         <form id="bulkDeleteForm" action="<?php echo e(route('assetmaster.model_type.bulk-delete')); ?>" method="POST" class="d-inline">
             <?php echo csrf_field(); ?>
             <div id="bulkDeleteInputs"></div>
         </form>
<button id="deleteSelectedBtn" class="btn btn-danger d-none">
    <i class="bi bi-trash"></i>
</button>
<?php endif; ?>
        </div>
        </div>
    <!--  -->

    <table class="table table-bordered table-striped" id="modeltypeTable">
        <thead class="table-dark-primary">
            <tr>
                <th><input type="checkbox" id="selectAll"></th>

                <th>S.No</th>
                <!-- <th>Company</th> -->
                <th>Model Type</th>
                <th>Created Date</th>
                <th width="140">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $modelTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $at): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><input type="checkbox" class="rowCheckbox" value="<?php echo e($at->id); ?>"></td>

                    <td><?php echo e($key + 1); ?></td>
                    <td><?php echo e($at->model_name); ?></td>
                    <td><?php echo e($at->created_at->format('d-m-Y')); ?></td>
                    <td>
                        <a href="<?php echo e(route('assetmaster.model_type.edit', $at->id)); ?>" class="btn btn-primary btn-sm">Edit</a>
                        <form action="<?php echo e(route('assetmaster.model_type.destroy', $at->id)); ?>" method="POST" style="display:inline-block;">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="text-center">No records found</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>
<div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
    
    
    <div class="text-muted small">
        Showing 
        <?php echo e($modelTypes->firstItem() ?? 0); ?> 
        to 
        <?php echo e($modelTypes->lastItem() ?? 0); ?> 
        of 
        <?php echo e(number_format($modelTypes->total())); ?> entries
    </div>

    
    <div>
        <?php if($modelTypes->hasPages()): ?>
            <nav>
                <ul class="pagination">
                    
                    <?php if($modelTypes->onFirstPage()): ?>
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                    <?php else: ?>
                        <li class="page-item"><a class="page-link" href="<?php echo e($modelTypes->previousPageUrl()); ?>" rel="prev">Previous</a></li>
                    <?php endif; ?>

                    
                    <?php
                        $total = $modelTypes->lastPage();
                        $current = $modelTypes->currentPage();
                        $max = 5; // Number of page links to show
                        $start = max(1, $current - floor($max / 2));
                        $end = min($total, $start + $max - 1);
                        if ($end - $start < $max - 1) {
                            $start = max(1, $end - $max + 1);
                        }
                    ?>

                    <?php if($start > 1): ?>
                        <li class="page-item"><a class="page-link" href="<?php echo e($modelTypes->url(1)); ?>">1</a></li>
                        <?php if($start > 2): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for($i = $start; $i <= $end; $i++): ?>
                        <?php if($i == $current): ?>
                            <li class="page-item active"><span class="page-link"><?php echo e($i); ?></span></li>
                        <?php else: ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($modelTypes->url($i)); ?>"><?php echo e($i); ?></a></li>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if($end < $total): ?>
                        <?php if($end < $total - 1): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <li class="page-item"><a class="page-link" href="<?php echo e($modelTypes->url($total)); ?>"><?php echo e($total); ?></a></li>
                    <?php endif; ?>

                    
                    <?php if($modelTypes->hasMorePages()): ?>
                        <li class="page-item"><a class="page-link" href="<?php echo e($modelTypes->nextPageUrl()); ?>" rel="next">Next</a></li>
                    <?php else: ?>
                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

</div>

<script>
    document.getElementById('tableSearch').addEventListener('keyup', function() {

    let value = this.value.toLowerCase();

    document.querySelectorAll('#modeltypeTable tbody tr').forEach(row => {

        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';

    });

});

//checkall functionality 

document.getElementById('selectAll').addEventListener('change', function(){
    let isChecked = this.checked;
    document.querySelectorAll('.rowCheckbox').forEach(cb => {
        cb.checked = isChecked;
    });
    updateDeleteButtonVisibility();
});

// Bulk Delete Functionality
document.getElementById('deleteSelectedBtn')?.addEventListener('click', function () {
    const selectedIds = Array.from(document.querySelectorAll('.rowCheckbox:checked')).map(cb => cb.value);
    if (!selectedIds.length) {
        return;
    }

    if (!confirm(`Delete ${selectedIds.length} selected client(s)?`)) {
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

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\assetmaster\model_type\index.blade.php ENDPATH**/ ?>