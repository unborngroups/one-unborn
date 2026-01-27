



<?php $__env->startSection('content'); ?>

<div class="container-fluid py-4">
   

    <div class="row">


        <div class="col-12">

            <div class="card shadow border-0">

                

                <div class="card-header text-dark d-flex justify-content-between align-items-center">

                    <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i>Closed Feasibilities</h5>

                      <form id="searchForm" method="GET" class="d-flex align-items-center w-25">
                          <input type="text" name="search" id="tableSearch" class="form-control form-control-sm w-100" placeholder="Search..." value="<?php echo e($search ?? ''); ?>" oninput="this.form.submit()">
                          <input type="hidden" name="per_page" value="<?php echo e(request('per_page', 10)); ?>">
                      </form>

                </div>

                <div class="p-1">
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
                </div>

                <div class="card-body">
                    

                    

                    <?php if($records->count() > 0): ?>

                        <div class="table-responsive">

                            <table class="table table-striped table-hover" id="closedFeasibilities">

                                

                                <thead class="table-dark-primary">

                                    <tr>

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

                                    

                                    <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                        <tr>
                                           

                                            

                                            <td><?php echo e(($records->currentPage() - 1) * $records->perPage() + $loop->iteration); ?></td>

                                            

                                            <td>

                                                <span class=""><?php echo e($record->feasibility->feasibility_request_id ?? 'N/A'); ?></span>

                                            </td>

                                            

                                            <td>

                                                <div class="btn-group" role="group">

                                                    

                                                    <a href="<?php echo e(route('sm.feasibility.view', $record->id)); ?>" 

                                                       class="btn btn-info btn-sm" title="View">

                                                        <i class="bi bi-eye"></i> View

                                                    </a>

                                                

                                                </div>

                                            </td>

                                            

                                            <td><?php echo e($record->feasibility->company->company_name ?? 'N/A'); ?></td>

                                            

                                            <td><?php echo e($record->feasibility->client->client_name ?? 'N/A'); ?></td>

                                            
                                            <td><?php echo e($record->feasibility->area ?? 'N/A'); ?> / <?php echo e($record->feasibility->state ?? 'N/A'); ?></td>

                                            

                                            <td><?php echo e($record->feasibility->type_of_service ?? 'N/A'); ?></td>

                                            

                                            <td><?php echo e($record->feasibility->speed ?? 'N/A'); ?></td>

                                            

                                            <td><?php echo e($record->feasibility->no_of_links ?? 'N/A'); ?></td>

                                        </tr>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                </tbody>

                            </table>

                        </div>

                    <?php else: ?>

                    

                        <div class="text-center py-4">

                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>

                            <h5 class="text-muted mt-3">No closed feasibilities found</h5>

                            <p class="text-muted">No feasibilities have been completed yet.</p>

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

     document.getElementById('tableSearch').addEventListener('keyup', function() {

    let value = this.value.toLowerCase();

    document.querySelectorAll('#closedFeasibilities tbody tr').forEach(row => {

        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';

    });

});

    document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select_all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');

    selectAll.addEventListener('change', function() {
        rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
    });

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const allChecked = [...rowCheckboxes].every(x => x.checked);
            const noneChecked = [...rowCheckboxes].every(x => !x.checked);

            selectAll.checked = allChecked;
            selectAll.indeterminate = !allChecked && !noneChecked;
        });
    });
});
</script>
<style>
    .table th,  .table td {
        width: 130px;

    white-space: nowrap;

    }
    </style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/sm/feasibility/closed.blade.php ENDPATH**/ ?>