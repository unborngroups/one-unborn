

<?php $__env->startSection('content'); ?>
<div class="container mt-3">
    <h4 class="fw-bold">SLA Reports — <?php echo e($link->service_id); ?></h4>

    <table class="table table-bordered table-striped mt-3">
        <thead>
        <tr>
            <th>Month</th>
            <th>Uptime %</th>
            <th>Downtime Hours</th>
            <th>Avg Latency</th>
            <th>Packet Loss</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php $__currentLoopData = $slaReports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($r->month); ?>/<?php echo e($r->year); ?></td>
                <td><?php echo e($r->uptime_percentage); ?>%</td>
                <td><?php echo e($r->downtime_hours); ?></td>
                <td><?php echo e($r->avg_latency_ms); ?> ms</td>
                <td><?php echo e($r->avg_packet_loss); ?>%</td>
                <td>
                    <?php if($r->breached): ?>
                        <span class="badge bg-danger">Breached</span>
                    <?php else: ?>
                        <span class="badge bg-success">OK</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <h4 class="mt-4">SLA Graph Overview (Monthly)</h4>

    <canvas id="uptimeChart" height="100"></canvas>
    <canvas id="latencyChart" height="130" class="mt-4"></canvas>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const labels = <?= json_encode($labels, JSON_THROW_ON_ERROR) ?>;
    const uptime = <?= json_encode($uptime, JSON_THROW_ON_ERROR) ?>;
    const latency = <?= json_encode($latency, JSON_THROW_ON_ERROR) ?>;
    const packetLoss = <?= json_encode($packetLoss, JSON_THROW_ON_ERROR) ?>;


    // Line Chart for Uptime %
    new Chart(document.getElementById('uptimeChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Uptime %',
                data: uptime,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.2)',
                borderWidth: 2,
                tension: 0.4
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true, max: 100 }
            }
        }
    });

    // Bar Chart for Latency + Packet Loss
    new Chart(document.getElementById('latencyChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Latency (ms)',
                    data: latency,
                    backgroundColor: 'rgba(255, 193, 7, 0.7)',
                    borderWidth: 1
                },
                {
                    label: 'Packet Loss %',
                    data: packetLoss,
                    backgroundColor: 'rgba(220, 53, 69, 0.7)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('client_portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\client_portal\sla_reports.blade.php ENDPATH**/ ?>