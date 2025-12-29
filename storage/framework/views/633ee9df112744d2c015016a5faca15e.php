
<?php use Milon\Barcode\Facades\DNS1DFacade as DNS1D; ?>
<?php $__env->startSection('content'); ?>

<div class="container">
<h3 class="mb-3">Asset List</h3>

    <?php if($permissions->can_add): ?>

        <a href="<?php echo e(route('operations.asset.create')); ?>" class="btn btn-success float-end mb-3">

            <i class="bi bi-plus-circle"></i> Add New Asset

        </a>

         <?php endif; ?>


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

            <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search...">

            <div class="d-flex align-items-center gap-2">
                <?php if($permissions->can_delete): ?>
                    <form id="bulkDeleteForm" action="<?php echo e(route('operations.asset.bulk-delete')); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <div id="bulkDeleteInputs"></div>
                    </form>
                    <button id="deleteSelectedBtn" class="btn btn-danger d-none">
                        <i class="bi bi-trash"></i>
                    </button>
                <?php endif; ?>

                <?php if($permissions->can_view): ?>
                    <form id="bulkPrintForm" action="<?php echo e(route('operations.asset.bulk-print')); ?>" method="POST" target="_blank" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <div id="bulkPrintInputs"></div>
                    </form> 
                    <button id="printSelectedBtn" class="btn btn-dark d-none">
                        <i class="bi bi-printer"></i>
                    </button>
                <?php endif; ?>
            </div>
        </div>

<table class="table table-bordered" id="asset">
<thead class="table-dark-primary">
<tr>
    <th><input type="checkbox" id="selectAll"></th>
    <th>S.No</th>
    <th>Action</th>
    <th>Asset ID / Barcode</th>
    <th>Brand</th>
    <th>Model</th>
    <th>Serial No</th>
    <th>Purchase Date</th>
    <th>Print</th>
    
</tr>
</thead>

<tbody>
<?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<tr>
      <td><input type="checkbox" class="rowCheckbox" value="<?php echo e($a->id); ?>"></td>

    <td>
        <?php echo e($assets instanceof \Illuminate\Pagination\LengthAwarePaginator 
            ? $loop->iteration + ($assets->currentPage() - 1) * $assets->perPage() 
            : $loop->iteration); ?>

    </td>
    <td>
        <!-- edit action -->
    <?php if($permissions->can_edit): ?>
    <a href="<?php echo e(route('operations.asset.edit', $a->id)); ?>" class="btn btn-sm btn-primary">Edit</a>
    <?php endif; ?>

        <!-- delete action -->
    <?php if($permissions->can_delete): ?>
            <form action="<?php echo e(route('operations.asset.destroy', $a->id)); ?>" method="POST" style="display:inline-block;">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
    <button onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">Delete</button>
</form>
        <?php endif; ?>
        
 

                                   <?php if($permissions->can_view): ?>

                                   <a href="<?php echo e(route('operations.asset.view', $a->id)); ?>" class="btn btn-sm btn-warning">

                                    <i class="bi bi-eye"></i>

                                    </a>

                                     <?php endif; ?>
           
    </td>

   <td>
   <img src="/barcode.php?code=<?php echo e($a->asset_id); ?>" height="2px" >
<p><?php echo e($a->asset_id); ?></p>

</td>

    <td><?php echo e($a->brand); ?></td>
    <td><?php echo e($a->model); ?></td>
    <td><?php echo e($a->serial_no); ?></td>
    <td><?php echo e($a->purchase_date); ?></td>
    <!-- print button -->
    <td>
        <a href="<?php echo e(route('operations.asset.print', $a->id)); ?>" class="btn btn-dark" target="_blank">
    <i class="bi bi-printer"></i> Print
</a>
    </td>
    
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tbody>
</table>
<div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
            <div class="text-muted small">
                Showing
                <?php echo e($assets->firstItem() ?? 0); ?>

                to
                <?php echo e($assets->lastItem() ?? 0); ?>

                of
                <?php echo e(number_format($assets->total())); ?> entries
            </div>
            <div class="ms-auto">
                <nav>
                    <ul class="pagination mb-0">
                        
                        <?php if($assets->onFirstPage()): ?>
                            <li class="page-item disabled"><span class="page-link">Previous</span></li>
                        <?php else: ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($assets->previousPageUrl()); ?>" rel="prev">Previous</a></li>
                        <?php endif; ?>

                        
                        <?php
                            $total = $assets->lastPage();
                            $current = $assets->currentPage();
                            $max = 5; // Number of page links to show
                            $start = max(1, $current - floor($max / 2));
                            $end = min($total, $start + $max - 1);
                            if ($end - $start < $max - 1) {
                                $start = max(1, $end - $max + 1);
                            }
                        ?>

                        <?php if($start > 1): ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($assets->url(1)); ?>">1</a></li>
                            <?php if($start > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for($i = $start; $i <= $end; $i++): ?>
                            <?php if($i == $current): ?>
                                <li class="page-item active"><span class="page-link"><?php echo e($i); ?></span></li>
                            <?php else: ?>
                                <li class="page-item"><a class="page-link" href="<?php echo e($assets->url($i)); ?>"><?php echo e($i); ?></a></li>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if($end < $total): ?>
                            <?php if($end < $total - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($assets->url($total)); ?>"><?php echo e($total); ?></a></li>
                        <?php endif; ?>

                        
                        <?php if($assets->hasMorePages()): ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($assets->nextPageUrl()); ?>" rel="next">Next</a></li>
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

    document.querySelectorAll('#asset tbody tr').forEach(row => {

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

    if (!confirm(`Delete ${selectedIds.length} selected asset(s)?`)) {
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

document.getElementById('printSelectedBtn')?.addEventListener('click', function () {
    const selectedIds = Array.from(document.querySelectorAll('.rowCheckbox:checked')).map(cb => cb.value);
    if (!selectedIds.length) {
        return;
    }

    const inputsContainer = document.getElementById('bulkPrintInputs');
    inputsContainer.innerHTML = '';
    selectedIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        inputsContainer.appendChild(input);
    });

    document.getElementById('bulkPrintForm')?.submit();
});


function updateDeleteButtonVisibility() {
    const totalChecked = document.querySelectorAll('.rowCheckbox:checked').length;
    const deleteBtn = document.getElementById('deleteSelectedBtn');
    const printBtn = document.getElementById('printSelectedBtn');

    if (deleteBtn) {
        if (totalChecked > 0) {
            deleteBtn.classList.remove('d-none');
        } else {
            deleteBtn.classList.add('d-none');
        }
    }

    if (printBtn) {
        if (totalChecked > 0) {
            printBtn.classList.remove('d-none');
        } else {
            printBtn.classList.add('d-none');
        }
    }
}

document.querySelectorAll('.rowCheckbox').forEach(cb => {
    cb.addEventListener('change', updateDeleteButtonVisibility);
});

// Keep the delete button state correct on page load
updateDeleteButtonVisibility();

</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\operations\asset\index.blade.php ENDPATH**/ ?>