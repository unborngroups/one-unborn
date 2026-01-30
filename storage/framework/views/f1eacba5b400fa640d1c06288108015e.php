<?php
$columns = [
    'client_name' => 'Client Name',
    'location_id' => 'Location ID',
    'address' => 'Address',
    'circuit_id' => 'Circuit ID',
    'date_of_activation' => 'Date of Activation',
    'mode_of_delivery' => 'Mode of Delivery',
    'static_ip' => 'Static IP Address',
    'static_ip_subnet' => 'Static IP Subnet',
    'static_vlan_tag' => 'Static VLAN Tag',
    'network_ip' => 'Network IP',
    'gateway' => 'Gateway',
    'subnet_mask' => 'Subnet Mask',
    'usable_ips' => 'Usable IPs',
];
?>

<div class="container-fluid py-4">
    <div class="card shadow border-0">

        
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-table me-2"></i>

                <?php if($type === 'open'): ?>
                    Open Deliverables
                <?php elseif($type === 'inprogress'): ?>
                    In Progress Deliverables
                <?php elseif($type === 'delivery'): ?>
                    Delivered Deliverables
                <?php else: ?>
                    Deliverables
                <?php endif; ?>
            </h5>

            <button id="downloadExcelBtn"
                    class="btn btn-success d-none"
                    onclick="downloadSelectedExcel()">
                <i class="bi bi-download me-1"></i> Download Excel
            </button>
        </div>

        
        <div class="card-body">
            <?php if($records->count() > 0): ?>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" id="deliverableTable">
                        <thead class="table-dark">
                            <tr>
                                <th width="50">
                                    <input type="checkbox" id="select_all">
                                </th>
                                <th width="50">S.No</th>

                                <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <th><?php echo e($label); ?></th>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <input type="checkbox"
                                               class="row-checkbox"
                                               value="<?php echo e($record->id); ?>">
                                    </td>

                                    <td>
                                        <?php echo e(($records->firstItem() ?? 1) + $index); ?>

                                    </td>

                                    <td><?php echo e($record->feasibility->client->client_name ?? 'N/A'); ?></td>
                                    <td><?php echo e($record->feasibility->location_id ?? 'N/A'); ?></td>
                                    <td><?php echo e($record->feasibility->address ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if($record->deliverablePlans->count()): ?>
                                            <?php echo e($record->deliverablePlans->pluck('circuit_id')->implode(', ')); ?>

                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($record->deliverablePlans->count()): ?>
                                            <?php echo e($record->deliverablePlans->pluck('date_of_activation')->map(fn($d) => $d ? \Carbon\Carbon::parse($d)->format('Y-m-d') : 'N/A')->implode(', ')); ?>

                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($record->deliverablePlans->count()): ?>
                                            <?php echo e($record->deliverablePlans->pluck('mode_of_delivery')->implode(', ')); ?>

                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($record->deliverablePlans->count()): ?>
                                            <?php $val = $record->deliverablePlans->pluck('static_ip_address')->filter()->implode(', '); ?>
                                            <?php echo e($val !== '' ? $val : 'N/A'); ?>

                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($record->deliverablePlans->count()): ?>
                                            <?php $val = $record->deliverablePlans->pluck('static_ip_subnet')->filter()->implode(', '); ?>
                                            <?php echo e($val !== '' ? $val : 'N/A'); ?>

                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($record->deliverablePlans->count()): ?>
                                            <?php $val = $record->deliverablePlans->pluck('static_vlan_tag')->filter()->implode(', '); ?>
                                            <?php echo e($val !== '' ? $val : 'N/A'); ?>

                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($record->deliverablePlans->count()): ?>
                                            <?php $val = $record->deliverablePlans->pluck('network_ip')->filter()->implode(', '); ?>
                                            <?php echo e($val !== '' ? $val : 'N/A'); ?>

                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($record->deliverablePlans->count()): ?>
                                            <?php $val = $record->deliverablePlans->pluck('gateway')->filter()->implode(', '); ?>
                                            <?php echo e($val !== '' ? $val : 'N/A'); ?>

                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($record->deliverablePlans->count()): ?>
                                            <?php $val = $record->deliverablePlans->pluck('subnet_mask')->filter()->implode(', '); ?>
                                            <?php echo e($val !== '' ? $val : 'N/A'); ?>

                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($record->deliverablePlans->count()): ?>
                                            <?php $val = $record->deliverablePlans->pluck('usable_ips')->filter()->implode(', '); ?>
                                            <?php echo e($val !== '' ? $val : 'N/A'); ?>

                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <h5 class="text-muted mt-3">No Deliverables Found</h5>
                </div>
            <?php endif; ?>
        </div>

        
        <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap px-3 pb-3">
            <div class="text-muted small">
                <?php if(method_exists($records, 'firstItem')): ?>
                    Showing <?php echo e($records->firstItem() ?? 0); ?>

                    to <?php echo e($records->lastItem() ?? 0); ?>

                    of <?php echo e(number_format($records->total())); ?> entries
                <?php else: ?>
                    Showing <?php echo e($records->count() ? 1 : 0); ?>

                    to <?php echo e($records->count()); ?>

                    of <?php echo e(number_format($records->count())); ?> entries
                <?php endif; ?>
            </div>

            <div class="ms-auto">
                <?php if(method_exists($records, 'links')): ?>
                    <?php echo e($records->links()); ?>

                <?php endif; ?>
            </div>
        </div>

    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const selectAll = document.getElementById('select_all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const downloadBtn = document.getElementById('downloadExcelBtn');

    function updateDownloadBtn() {
        const anyChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
        downloadBtn.classList.toggle('d-none', !anyChecked);
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
            updateDownloadBtn();
        });
    }

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            const allChecked = Array.from(rowCheckboxes).every(x => x.checked);
            const noneChecked = Array.from(rowCheckboxes).every(x => !x.checked);

            if (selectAll) {
                selectAll.checked = allChecked;
                selectAll.indeterminate = !allChecked && !noneChecked;
            }

            updateDownloadBtn();
        });
    });

});

function downloadSelectedExcel() {

    const checked = Array.from(
        document.querySelectorAll('.row-checkbox:checked')
    ).map(cb => cb.value);

    if (checked.length === 0) return;

    const url = `<?php echo e(route('report.deliverable.downloadExcel')); ?>`;
    const form = document.createElement('form');

    form.method = 'POST';
    form.action = url;
    form.target = '_blank';

    form.innerHTML = `
        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
    `;

    checked.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>
<?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\report\deliverable\partials\table.blade.php ENDPATH**/ ?>