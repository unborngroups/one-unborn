



<?php $__env->startSection('content'); ?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h3 class="fw-bold text-primary">Manage feasibility</h3>

       
       <?php if($permissions && $permissions->can_add): ?>
           <a href="<?php echo e(route('feasibility.create')); ?>" class="btn btn-success">Add Feasibility</a>
       <?php endif; ?>

    </div>

    <?php if(session('success')): ?>

        <div class="alert alert-success"><?php echo e(session('success')); ?></div>

    <?php endif; ?>



    <div class="card shadow border-0">

        <div class="card-header bg-light d-flex justify-content-between">

            <!-- <h5 class="mb-0 text-danger">MANAGE USER</h5> -->
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
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="form-control form-control-sm w-25" placeholder="Search..." onkeyup="this.form.submit();">

            <!-- <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search..."> -->

        </div>

        <div class="card-body table-responsive">

            <!-- <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search..."> -->

            <table class="table table-bordered table-hover align-middle" id="feasibility">

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

                                

                                    <?php if($permissions && $permissions->can_edit): ?>
                                        <a href="<?php echo e(route('feasibility.edit', $feasibility)); ?>" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    <?php endif; ?>


                                 

                                   <?php if($permissions && $permissions->can_view): ?>
                                       <a href="<?php echo e(route('feasibility.show', $feasibility->id)); ?>" class="btn btn-sm btn-info" title="View">
                                           <i class="bi bi-eye"></i>
                                       </a>
                                   <?php endif; ?>


                                


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

        
        <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
            <div class="text-muted small">
                Showing
                <?php echo e($feasibilities->firstItem() ?? 0); ?>

                to
                <?php echo e($feasibilities->lastItem() ?? 0); ?>

                of
                <?php echo e(number_format($feasibilities->total())); ?> entries
            </div>
            <div class="ms-auto">
                <nav>
                    <ul class="pagination mb-0">
                        
                        <?php if($feasibilities->onFirstPage()): ?>
                            <li class="page-item disabled"><span class="page-link">Previous</span></li>
                        <?php else: ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($feasibilities->previousPageUrl()); ?>" rel="prev">Previous</a></li>
                        <?php endif; ?>

                        
                        <?php
                            $total = $feasibilities->lastPage();
                            $current = $feasibilities->currentPage();
                            $max = 5; // Number of page links to show
                            $start = max(1, $current - floor($max / 2));
                            $end = min($total, $start + $max - 1);
                            if ($end - $start < $max - 1) {
                                $start = max(1, $end - $max + 1);
                            }
                        ?>

                        <?php if($start > 1): ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($feasibilities->url(1)); ?>">1</a></li>
                            <?php if($start > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for($i = $start; $i <= $end; $i++): ?>
                            <?php if($i == $current): ?>
                                <li class="page-item active"><span class="page-link"><?php echo e($i); ?></span></li>
                            <?php else: ?>
                                <li class="page-item"><a class="page-link" href="<?php echo e($feasibilities->url($i)); ?>"><?php echo e($i); ?></a></li>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if($end < $total): ?>
                            <?php if($end < $total - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($feasibilities->url($total)); ?>"><?php echo e($total); ?></a></li>
                        <?php endif; ?>

                        
                        <?php if($feasibilities->hasMorePages()): ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($feasibilities->nextPageUrl()); ?>" rel="next">Next</a></li>
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

document.getElementById('search').addEventListener('keyup', function() {

    let value = this.value.toLowerCase();

    document.querySelectorAll('#feasibility tbody tr').forEach(row => {

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


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\feasibility\index.blade.php ENDPATH**/ ?>