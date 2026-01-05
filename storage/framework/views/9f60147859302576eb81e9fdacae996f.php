

<?php $__env->startSection('content'); ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
    .dashboard-card {
        padding: 5px;
        border-radius: 3px;
        color: white;
        margin-bottom: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        transition: 0.3s;
        position: relative;
        overflow: hidden;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.3);
    }
    .dashboard-title {
        font-size: 20px;
        font-weight: bold;
    }
    .icon-box {
        font-size: 40px;
        opacity: 0.9;
    }
    a {
        text-decoration: none;
    }
    .dashboard-icon-bg {
        position: absolute;
        right: 15px;
        top: 25px;
        opacity: 0.15;
        font-size: 70px;
    }
    .dashboard-bottom {
        background: rgba(0,0,0,0.15);
        padding: 6px 0;
        text-align: center;
        border-radius: 4px;
        margin-top: 22px;
        font-weight: 600;
    }
</style>

<div class="container mt-1">

    <!-- <h1 class="mb-1">Dashboard</h1> -->
    <h4 class="mb-4">Welcome to <b>Unborn Network</b></h4>

    <div class="row g-4">
        <!-- Broadband -->
        <div class="col-md-3">
            <div class="dashboard-card" style="background: linear-gradient(135deg, #1e3c72, #2a5298);">
                <div class="d-flex justify-content-between">
                    <div>
                        
                        <div class="dashboard-title">Broadband</div>
                        <small>
                            Links: <?php echo e($serviceCounts['Broadband']['links']); ?> | 
                            Locations: <?php echo e($serviceCounts['Broadband']['locations']); ?>

                        </small>

                    </div>
                    <div class="icon-box"><i class="bi bi-wifi"></i></div>
                </div>
            </div>
        </div>

        <!-- ILL -->
        <div class="col-md-3">
            <div class="dashboard-card" style="background: linear-gradient(135deg, #f7971e, #ffd200);">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="dashboard-title">ILL</div>
                        <small>
                            Links: <?php echo e($serviceCounts['ILL']['links']); ?> | 
                            Locations: <?php echo e($serviceCounts['ILL']['locations']); ?>

                        </small>

                    </div>
                    <div class="icon-box"><i class="bi bi-diagram-3-fill"></i></div>
                </div>
            </div>
        </div>

        <!-- P2P -->
        <div class="col-md-3">
            <div class="dashboard-card" style="background: linear-gradient(135deg, #11998e, #38ef7d);">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="dashboard-title">P2P</div>
                        <small>
                            Links: <?php echo e($serviceCounts['P2P']['links']); ?> | 
                            Locations: <?php echo e($serviceCounts['P2P']['locations']); ?>

                        </small>

                    </div>
                    <div class="icon-box"><i class="bi bi-diagram-2-fill"></i></div>
                </div>
            </div>
        </div>

        <!-- NNI -->
        <div class="col-md-3">
            <div class="dashboard-card" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="dashboard-title">NNI</div>
                        <small>
                            Links: <?php echo e($serviceCounts['NNI']['links']); ?> | 
                            Locations: <?php echo e($serviceCounts['NNI']['locations']); ?>

                        </small>

                    </div>
                    <div class="icon-box"><i class="bi bi-diagram-3"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-0">
        <h3>Feasibility</h3>
        <div class="row g-4 mt-2">
            <div class="col-md-4">
                <a href="<?php echo e(route('operations.feasibility.open')); ?>" class="text-decoration-none">
                    <div class="dashboard-card" style="background:#1f7a34;">
                        <h3 class="fw-bold m-0"><?php echo e($feasibilityCounts['open'] ?? 0); ?></h3>
                        <h3>Open</h3>
                        <i class="bi bi-grid dashboard-icon-bg"></i>
                        <div class="dashboard-bottom">
                            List <i class="bi bi-arrow-right-circle"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="<?php echo e(route('operations.feasibility.inprogress')); ?>" class="text-decoration-none">
                    <div class="dashboard-card" style="background:#ff6a00;">
                        <h3 class="fw-bold m-0"><?php echo e($feasibilityCounts['inprogress'] ?? 0); ?></h3>
                        <h3>In Progress</h3>
                        <i class="bi bi-arrow-repeat dashboard-icon-bg"></i>
                        <div class="dashboard-bottom">
                            List <i class="bi bi-arrow-right-circle"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="<?php echo e(route('operations.feasibility.closed')); ?>" class="text-decoration-none">
                    <div class="dashboard-card" style="background:#0083b0;">
                        <h3 class="fw-bold m-0"><?php echo e($feasibilityCounts['closed'] ?? 0); ?></h3>
                        <h3>Closed</h3>
                        <i class="bi bi-check-circle-fill dashboard-icon-bg"></i>
                        <div class="dashboard-bottom">
                            List <i class="bi bi-arrow-right-circle"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="mt-0">
        <h3>Purchase Orders</h3>
        <div class="row g-4 mt-2">
            <div class="col-md-4">
                <a href="<?php echo e(route('sm.purchaseorder.create')); ?>" class="text-decoration-none">
                    <div class="dashboard-card" style="background:#9b59b6;">
                        <h3 class="fw-bold m-0"><?php echo e($purchaseOrderCounts['open'] ?? 0); ?></h3>
                        <h3>Open</h3>
                        <i class="bi bi-receipt-cutoff dashboard-icon-bg"></i>
                        <div class="dashboard-bottom">
                            List <i class="bi bi-arrow-right-circle"></i>
                        </div>
                    </div>
                </a>
            </div>

            

            <div class="col-md-4">
                <a href="<?php echo e(route('sm.purchaseorder.index')); ?>" class="text-decoration-none">
                    <div class="dashboard-card" style="background:#16a085;">
                        <h3 class="fw-bold m-0"><?php echo e($purchaseOrderCounts['closed'] ?? 0); ?></h3>
                        <h3>Closed</h3>
                        <i class="bi bi-check2-circle dashboard-icon-bg"></i>
                        <div class="dashboard-bottom">
                            List <i class="bi bi-arrow-right-circle"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="mt-0">
        <h3>Deliverables</h3>
        <div class="row g-4 mt-2">
            <div class="col-md-4 col-sm-6">
                <a href="<?php echo e(route('operations.deliverables.open')); ?>" class="text-decoration-none">
                    <div class="dashboard-card" style="background:#ff4e50;">
                        <h3 class="fw-bold m-0"><?php echo e($deliverableCounts['open'] ?? 0); ?></h3>
                        <h4>Open</h4>
                        <i class="bi bi-folder2-open dashboard-icon-bg"></i>
                        <div class="dashboard-bottom">
                            List <i class="bi bi-arrow-right-circle"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4 col-sm-6">
                <a href="<?php echo e(route('operations.deliverables.inprogress')); ?>" class="text-decoration-none">
                    <div class="dashboard-card" style="background:#fc913a;">
                        <h3 class="fw-bold m-0"><?php echo e($deliverableCounts['inprogress'] ?? 0); ?></h3>
                        <h4>In Progress</h4>
                        <i class="bi bi-arrow-repeat dashboard-icon-bg"></i>
                        <div class="dashboard-bottom">
                            List <i class="bi bi-arrow-right-circle"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4 col-sm-6">
                <a href="<?php echo e(route('operations.deliverables.delivery')); ?>" class="text-decoration-none">
                    <div class="dashboard-card" style="background:#28c76f;">
                        <h3 class="fw-bold m-0"><?php echo e($deliverableCounts['delivery'] ?? 0); ?></h3>
                        <h4>Delivered</h4>
                        <i class="bi bi-check2-all dashboard-icon-bg"></i>
                        <div class="dashboard-bottom">
                            List <i class="bi bi-arrow-right-circle"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="mt-1">
        <h3>Upcoming Renewals</h3>
        <div class="row g-4 mt-2">
            <div class="col-md-4 col-sm-6">
                <a href="<?php echo e(route('operations.renewals.index')); ?>" class="text-decoration-none">
                    <div class="dashboard-card" style="background:#845ec2;">
                        <h3 class="fw-bold m-0">0</h3>
                        <h4>Today</h4>
                        <i class="bi bi-calendar-day dashboard-icon-bg"></i>
                        <div class="dashboard-bottom">
                            List <i class="bi bi-arrow-right-circle"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4 col-sm-6">
                <a href="<?php echo e(route('operations.renewals.index')); ?>" class="text-decoration-none">
                    <div class="dashboard-card" style="background:#d65db1;">
                        <h3 class="fw-bold m-0">0</h3>
                        <h4>Tomorrow</h4>
                        <i class="bi bi-calendar2-week dashboard-icon-bg"></i>
                        <div class="dashboard-bottom">
                            List <i class="bi bi-arrow-right-circle"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4 col-sm-6">
                <a href="<?php echo e(route('operations.renewals.index')); ?>" class="text-decoration-none">
                    <div class="dashboard-card" style="background:#ff9671;">
                        <h3 class="fw-bold m-0">0</h3>
                        <h4>This Week</h4>
                        <i class="bi bi-calendar-week dashboard-icon-bg"></i>
                        <div class="dashboard-bottom">
                            List <i class="bi bi-arrow-right-circle"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\welcome.blade.php ENDPATH**/ ?>