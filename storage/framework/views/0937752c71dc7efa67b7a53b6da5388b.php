



<?php $__env->startSection('content'); ?>

<div class="container-fluid py-4">

    <div class="row">

        <div class="col-12">

            <div class="card shadow border-0">

                

                <div class="card-header text-dark d-flex justify-content-between align-items-center">

                    <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i>Closed Feasibilities</h5>

                </div>



                <div class="card-body">

                    

                    <?php if($records->count() > 0): ?>

                        <div class="table-responsive">

                            <table class="table table-striped table-hover">

                                

                                <thead class="table-dark-primary">

                                    <tr>
                                        <th width="50" class="text-center"><input type="checkbox" id="select_all" style="width: 18px; height: 18px; cursor: pointer;"></th>

                                        <th>S.No</th>

                                        <th>Request ID</th>

                                        <th>Action</th>

                                        <th>Company Name</th>

                                        <th>Name</th>

                                        <th>Type of Service</th>

                                        <th>Speed</th>

                                        <th>Links</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    

                                    <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                        <tr>
                                            <td class="text-center">
                                    <input type="checkbox" class="row-checkbox" value="<?php echo e($record->id); ?>" style="width: 18px; height: 18px; cursor: pointer;">
                                </td>

                                            

                                            <td><?php echo e($index + 1); ?></td>

                                            

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

    </div>

</div>

<script>
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
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\sm\feasibility\closed.blade.php ENDPATH**/ ?>