

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0 float-start"><i class="bi bi-check-circle me-2"></i>Delivered / Closed Deliverables</h5>
            <input type="text" id="tableSearch" class="form-control form-control-sm w-25 float-end" placeholder="Search...">

        </div>
        <div class="card-header bg-light d-flex justify-content-between">

            <!-- <h5 class="mb-0 text-danger">MANAGE USER</h5> -->


        </div>
        
        <div class="card-body">
            <?php if($records->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" id="delivery">
                        <thead class="table-dark">
                            <tr>
                                <th width="50"><input type="checkbox" id="select_all"></th>
                                <th width="50">S.No</th>
                                <th width="150">Action</th>
                                <th class="col">PO Number</th>
                                <th class="col">Feasibility ID</th>
                                <th class="col">Client Name</th>
                                <th class="col">Address</th>
                                <th class="col">Speed</th>
                                <th class="col">No. of Links</th>
                                <th class="col">Vendor</th>
                                <!-- <th class="col">Delivered At</th> -->
                                <th class="col">Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="row-checkbox" value="<?php echo e($record->id); ?>">
                                </td>

                                <td><?php echo e($index + 1); ?></td>

                                <td>
                                    <a href="<?php echo e(route('operations.deliverables.view', $record->id)); ?>" 
                                       class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>

                                <td><?php echo e($record->po_number ?? 'N/A'); ?></td>

                                <td><?php echo e($record->feasibility->feasibility_request_id ?? 'N/A'); ?></td>

                                <td><?php echo e($record->feasibility->client->client_name ?? 'N/A'); ?></td>

                                <td><?php echo e($record->site_address ?? 'N/A'); ?></td>

                                <td><?php echo e($record->speed_in_mbps ?? 'N/A'); ?></td>

                                <td><?php echo e($record->no_of_links ?? 'N/A'); ?></td>

                                <td><?php echo e($record->vendor ?? 'N/A'); ?></td>
<!-- 
                                <td>
                                    <?php echo e($record->delivered_at 
                                        ? \Carbon\Carbon::parse($record->delivered_at)->format('d-m-Y') 
                                        : 'N/A'); ?>

                                </td> -->

                                <td>
                                    <span class="badge bg-success">Delivered</span>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                    <h5 class="text-muted mt-3">No Delivered Deliverables Found</h5>
                </div>
            <?php endif; ?>
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

document.getElementById('tableSearch').addEventListener('keyup', function() {

    let value = this.value.toLowerCase();

    document.querySelectorAll('#delivery tbody tr').forEach(row => {

        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';

    });

});


</script>

<style>
.col {
    width: 130px;
    white-space: nowrap;
}
.table th,  .table td {

    width: 130px;

    white-space: nowrap;

}
</style>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\wlcome\multipleuserpage\resources\views/operations/deliverables/delivery.blade.php ENDPATH**/ ?>