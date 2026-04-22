<table>
    <thead>
        <tr>
            <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <th><?php echo e($label); ?></th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($record->feasibility->client->client_name ?? 'N/A'); ?></td>
                <td><?php echo e($record->location_id ?? 'N/A'); ?></td>
                <td><?php echo e($record->feasibility->address ?? 'N/A'); ?></td>
                <td><?php echo e($record->circuit_id ?? 'N/A'); ?></td>
                <td><?php echo e($record->date_of_activation ? \Carbon\Carbon::parse($record->date_of_activation)->format('Y-m-d') : 'N/A'); ?></td>
                <td><?php echo e($record->mode_of_delivery ?? 'N/A'); ?></td>
                <td><?php echo e($record->static_ip ?? 'N/A'); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\exports\deliverables.blade.php ENDPATH**/ ?>