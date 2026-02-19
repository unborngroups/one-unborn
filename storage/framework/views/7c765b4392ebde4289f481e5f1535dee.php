



<?php $__env->startSection('content'); ?>

<div class="container-fluid py-4">

<div class="">
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


            <?php if($permissions->can_delete): ?>
            <form id="bulkDeleteForm" action="<?php echo e(route('operations.feasibility.bulk-delete')); ?>" method="POST" class="d-inline">
                <?php echo csrf_field(); ?>
                <div id="bulkDeleteInputs"></div>
            </form>
            <button id="deleteSelectedBtn" class="btn btn-danger d-none">
                <i class="bi bi-trash"></i>
            </button>
            <?php endif; ?>

</div>
    

    <div class="row">

        <div class="col-12">

            <div class="card shadow border-0">

                <div class="card-header text-dark d-flex justify-content-between align-items-center">

                    <h5 class="mb-0"><i class="bi bi-hourglass-split me-2"></i>Open Feasibilities</h5>

                    <form id="searchForm" method="GET" class="d-flex align-items-center w-25">
                        <input type="text" name="search" class="form-control form-control-sm w-100" placeholder="Search..." value="<?php echo e($search ?? ''); ?>" oninput="this.form.submit()">
                        <input type="hidden" name="per_page" value="<?php echo e(request('per_page', 10)); ?>">
                    </form>
                </div>



                <div class="card-body">

                    <!-- Check if there are records to display -->

                    <?php if($records->count() > 0): ?>

                        <div class="table-responsive">

                            <table class="table table-striped table-hover" id="open">

                                <!-- Table headers -->

                                <thead class="table-dark-primary">

                                    <tr>
                                        <th width="50" class="text-center"><input type="checkbox" id="select_all" style="width: 18px; height: 18px; cursor: pointer;"></th>

                                        <th>S.No</th>

                                        <th>Request ID</th>

                                        <th>Action</th>

                                        <th>Company Name</th>

                                        <th>Name</th>

                                        <th>Area / State</th>

                                        <th>Type of Service</th>

                                        <th>Speed</th>

                                        <th>Links</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <!-- Loop through each record and display in table rows -->

                                    <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                        <tr>

                                            <!-- Display serial number -->

                                            <td class="text-center">
                                    <input type="checkbox" class="rowCheckbox" value="<?php echo e($record->id); ?>" style="width: 18px; height: 18px; cursor: pointer;">
                                </td>
                                            <td><?php echo e(($records->currentPage() - 1) * $records->perPage() + $loop->iteration); ?></td>

                                            <!-- Display feasibility request ID -->

                                            <td>

                                                <span class=""><?php echo e($record->feasibility->feasibility_request_id ?? 'N/A'); ?></span>

                                            </td>

                                            <td>

                                                 <!-- Action buttons for View and Update -->

                                                <div class="btn-group" role="group">

                                                    <!-- View button with route to the view page -->
                                                     <?php if($permissions->can_view): ?>

                                                    <a href="<?php echo e(route('operations.feasibility.view', $record->id)); ?>" 

                                                       class="btn btn-info btn-sm" title="View">

                                                        <i class="bi bi-eye"></i>

                                                    </a>
                                                    <?php endif; ?>


                                                    <!-- Update button with route to the edit page -->
                                                    <?php if($permissions->can_edit): ?>
                                                        <a href="<?php echo e(route('operations.feasibility.edit', $record->id)); ?>" 
                                                           class="btn btn-warning btn-sm" title="Update">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <?php endif; ?>
                                                        <!-- Not-Feasible button -->
                                                        <?php if($record->status === 'Open'): ?>
                                                            <form action="<?php echo e(route('operations.feasibility.notfeasible', $record->id)); ?>" method="POST" style="display:inline;">
                                                                <?php echo csrf_field(); ?>
                                                                <button type="submit" class="btn btn-success btn-sm" title="Mark as Not-Feasible" onclick="return confirm('Mark this feasibility as Not-Feasible?');">
                                                                    <i class="bi bi-check-circle"></i> Feasible
                                                                </button>
                                                            </form>
                                                        <?php elseif($record->status === 'Not-Feasible'): ?>
                                                            <button type="button" class="btn btn-danger btn-sm" disabled>
                                                                <i class="bi bi-x-octagon"></i> Not-Feasible
                                                            </button>
                                                        <?php endif; ?>
                                                    

                                                </div>

                                            </td>

                                            <!-- Display company name -->

                                            <td><?php echo e($record->feasibility->company->company_name ?? 'N/A'); ?></td>

                                            <!-- Display client name -->

                                            <td><?php echo e($record->feasibility->client->client_name ?? 'N/A'); ?></td>

                                            <!-- Display area and state -->
                                            <td><?php echo e($record->feasibility->area ?? 'N/A'); ?> / <?php echo e($record->feasibility->state ?? 'N/A'); ?></td>

                                            <!-- Display type of service -->

                                            <td><?php echo e($record->feasibility->type_of_service ?? 'N/A'); ?></td>

                                            <!-- Display speed -->

                                            <td><?php echo e($record->feasibility->speed ?? 'N/A'); ?></td>

                                            <!-- Display number of links -->

                                            <td><?php echo e($record->feasibility->no_of_links ?? 'N/A'); ?></td>

                                        </tr>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                </tbody>

                            </table>

                        </div>

                    <?php else: ?>

                    <!-- Message when no open feasibilities are found -->

                        <div class="text-center py-4">

                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>

                            <h5 class="text-muted mt-3">No open feasibilities found</h5>

                            <p class="text-muted">All feasibilities have been processed or none have been created yet.</p>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>
        <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
            <div class="text-muted small">
                Showing
                <?php echo e($records->firstItem() ?? 0); ?>

                to
                <?php echo e($records->lastItem() ?? 0); ?>

                of
                <?php echo e(number_format($records->total())); ?> entries
            </div>
            <div class="ms-auto">
                <nav>
                    <ul class="pagination mb-0">
                        
                        <?php if($records->onFirstPage()): ?>
                            <li class="page-item disabled"><span class="page-link">Previous</span></li>
                        <?php else: ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($records->previousPageUrl()); ?>" rel="prev">Previous</a></li>
                        <?php endif; ?>

                        
                        <?php
                            $total = $records->lastPage();
                            $current = $records->currentPage();
                            $max = 5; // Number of page links to show
                            $start = max(1, $current - floor($max / 2));
                            $end = min($total, $start + $max - 1);
                            if ($end - $start < $max - 1) {
                                $start = max(1, $end - $max + 1);
                            }
                        ?>

                        <?php if($start > 1): ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($records->url(1)); ?>">1</a></li>
                            <?php if($start > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for($i = $start; $i <= $end; $i++): ?>
                            <?php if($i == $current): ?>
                                <li class="page-item active"><span class="page-link"><?php echo e($i); ?></span></li>
                            <?php else: ?>
                                <li class="page-item"><a class="page-link" href="<?php echo e($records->url($i)); ?>"><?php echo e($i); ?></a></li>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if($end < $total): ?>
                            <?php if($end < $total - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($records->url($total)); ?>"><?php echo e($total); ?></a></li>
                        <?php endif; ?>

                        
                        <?php if($records->hasMorePages()): ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($records->nextPageUrl()); ?>" rel="next">Next</a></li>
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
// (No client-side search, now server-side search is used)



// ✅ Select / Deselect all checkboxes

document.getElementById('select_all').addEventListener('change', function(){
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

    if (!confirm(`Delete ${selectedIds.length} selected termination(s)?`)) {
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
    .table th,  .table td {
        width: 130px;

    white-space: nowrap;

    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/operations/feasibility/open.blade.php ENDPATH**/ ?>