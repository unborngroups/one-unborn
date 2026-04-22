

<?php $__env->startSection('content'); ?>
<div class="container mt-3">

    <?php if(!$link): ?>
        <div class="alert alert-info">
            No links found for your account.
        </div>
    <?php else: ?>

        <h4 class="fw-bold mb-3">
            <?php echo e($link->service_id); ?> — Link Details
        </h4>

        <div class="row">

            
            <div class="col-md-4">

                
                <table class="table table-bordered">
                    <tr>
                        <th>Service ID</th>
                        <td><?php echo e($link->service_id); ?></td>
                    </tr>
                    <tr>
                        <th>Link Type</th>
                        <td><?php echo e($link->link_type); ?></td>
                    </tr>
                    <tr>
                        <th>Router</th>
                        <td><?php echo e($link->router->router_name ?? 'No Router Assigned'); ?></td>
                    </tr>
                    <tr>
                        <th>Bandwidth</th>
                        <td><?php echo e($link->bandwidth); ?> Mbps</td>
                    </tr>
                    <tr>
                        <th>Live Status</th>
                        <td>
                            <span class="badge bg-secondary" id="live_status">
                                Checking…
                            </span>
                        </td>
                    </tr>
                </table>

                
                <?php if(isset($sla)): ?>
                <div class="card mt-3">
                    <div class="card-header fw-bold">
                        SLA Summary (<?php echo e($sla['period']); ?>)
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>Availability:</strong>
                            <?php echo e($sla['availability']); ?> %
                        </p>

                        <p>
                            <strong>Status:</strong>
                            <span class="badge 
                                <?php echo e($sla['status'] === 'PASS' ? 'bg-success' : 'bg-danger'); ?>">
                                <?php echo e($sla['status']); ?>

                            </span>
                        </p>

                        <p class="text-muted mb-0">
                            SLA Target: <?php echo e($sla['sla_target']); ?> %
                        </p>
                    </div>
                </div>
                <?php else: ?>
                <div class="alert alert-warning mt-3">
                    SLA not calculated yet.
                </div>
                <?php endif; ?>

                
                <a href="<?php echo e(route('client.sla.reports', $link->id)); ?>"
                   class="btn btn-secondary w-100 mt-3">
                    View SLA Reports
                </a>

            </div>

            
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header fw-bold">
                        Live Traffic
                    </div>
                    <div class="card-body">
                        <canvas id="liveChart" height="150"></canvas>
                    </div>
                </div>
            </div>

        </div>
    <?php endif; ?>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php if($link): ?>
<script>
    setInterval(() => {
        fetch("<?php echo e(route('client.live.traffic', $link->id)); ?>")
            .then(res => res.json())
            .then(data => {
                document.getElementById('live_status').innerHTML =
                    data.link_up
                        ? '<span class="badge bg-success">UP</span>'
                        : '<span class="badge bg-danger">DOWN</span>';
            })
            .catch(() => {
                document.getElementById('live_status').innerHTML =
                    '<span class="badge bg-warning">UNKNOWN</span>';
            });
    }, 3000);
</script>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('client_portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\client_portal\link_details.blade.php ENDPATH**/ ?>