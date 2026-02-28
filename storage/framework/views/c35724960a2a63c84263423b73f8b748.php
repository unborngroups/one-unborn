

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-header text-dark d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-x-octagon me-2"></i>Not-Feasible Feasibilities</h5>
                    <form id="searchForm" method="GET" class="d-flex align-items-center w-25">
                        <input type="text" name="search" class="form-control form-control-sm w-100" placeholder="Search..." value="<?php echo e($search ?? ''); ?>" oninput="this.form.submit()">
                        <input type="hidden" name="per_page" value="<?php echo e(request('per_page', 10)); ?>">
                    </form>
                </div>

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
        
                <div class="card-body">
                    <?php if($records->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark-primary">
                                    <tr>
                                        <th>S.No</th>
                                        <th>Action</th>
                                        <th>Request ID</th>
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
                                              <!-- View button with route to the view page -->
                                                     <?php if($permissions->can_view): ?>

                                                    <a href="<?php echo e(route('operations.feasibility.view', $record->id)); ?>" 

                                                       class="btn btn-info btn-sm" title="View">

                                                        <i class="bi bi-eye"></i> 

                                                    </a>
                                                    <?php endif; ?>  

                                                    <?php if($record->status === 'Not-Feasible'): ?>
                                                        <form action="<?php echo e(route('operations.feasibility.makefeasible', $record->id)); ?>" method="POST" style="display:inline;">
                                                            <?php echo csrf_field(); ?>
                                                            <button type="submit" class="btn btn-success btn-sm" title="Mark as Feasible" onclick="return confirm('Mark this feasibility as Feasible?');">
                                                                <i class="bi bi-check-circle"></i> <!-- Feasible -->
                                                            </button>
                                                        </form>
                                                        
                                                    <?php endif; ?>
                                            </td>
                                            <td><?php echo e($record->feasibility->feasibility_request_id ?? 'N/A'); ?></td>
                                            <td><?php echo e($record->feasibility->company->company_name ?? 'N/A'); ?></td>
                                            <td><?php echo e($record->feasibility->client->client_name ?? 'N/A'); ?></td>
                                            <td><?php echo e($record->feasibility->area ?? $record->feasibility->state ?? 'N/A'); ?></td>
                                            <td><?php echo e($record->feasibility->type_of_service ?? 'N/A'); ?></td>
                                            <td><?php echo e($record->feasibility->speed ?? 'N/A'); ?></td>
                                            <td><?php echo e($record->feasibility->no_of_links ?? 'N/A'); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        <?php echo e($records->links()); ?>

                    <?php else: ?>
                        <div class="alert alert-info">No Not-Feasible records found.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\operations\feasibility\notfeasible.blade.php ENDPATH**/ ?>