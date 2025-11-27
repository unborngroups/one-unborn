

<?php $__env->startSection('content'); ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
    .dashboard-card {
        padding: 25px;
        border-radius: 15px;
        color: white;
        margin-bottom: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        transition: 0.3s;
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
    /*  */
    .dashboard-card {
        background: #1f7a34; /* green */
        border-radius: 8px;
        padding: 20px;
        position: relative;
        color: #fff;
        overflow: hidden;
        transition: 0.3s;
    }
    .dashboard-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
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

<div class="container mt-4">

    <h1 class="mb-3">Dashboard</h1>
    <h4 class="mb-4">Welcome to <b>Unborn Technology</b></h4>

    <div class="row">

        <!-- Broadband -->
        <div class="col-md-4">
            <div class="dashboard-card" style="background: linear-gradient(135deg, #1e3c72, #2a5298);">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="dashboard-title">Broadband</div>
                        <small>Links: 10 | Locations: 4</small>
                    </div>
                    <div class="icon-box"><i class="bi bi-wifi"></i></div>
                </div>
            </div>
        </div>

        <!-- ILL -->
        <div class="col-md-4">
            <div class="dashboard-card" style="background: linear-gradient(135deg, #f7971e, #ffd200);">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="dashboard-title">ILL</div>
                        <small>Links: 6 | Locations: 3</small>
                    </div>
                    <div class="icon-box"><i class="bi bi-diagram-3-fill"></i></div>
                </div>
            </div>
        </div>

        <!-- P2P -->
        <div class="col-md-4">
            <div class="dashboard-card" style="background: linear-gradient(135deg, #11998e, #38ef7d);">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="dashboard-title">P2P</div>
                        <small>Links: 8 | Locations: 4</small>
                    </div>
                    <div class="icon-box"><i class="bi bi-diagram-2-fill"></i></div>
                </div>
            </div>
        </div>

        <!-- NNI -->
        <div class="col-md-4">
            <div class="dashboard-card" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="dashboard-title">NNI</div>
                        <small>Links: 4 | Locations: 2</small>
                    </div>
                    <div class="icon-box"><i class="bi bi-diagram-3"></i></div>
                </div>
            </div>
        </div>

        <!-- ======= FEASIBILITY ======= -->
        <div class="col-12 mt-4">
            <h3>Feasibility</h3>
        </div>

<div class="col-md-4">
    <a href="<?php echo e(route('operations.feasibility.open')); ?>" class="text-decoration-none">
        <div class="dashboard-card">
            
            <h3 class="fw-bold m-0">0</h3>
            <h3>Open</h3>
           
            <i class="bi bi-grid dashboard-icon-bg"></i>

            <div class="dashboard-bottom">
                List <i class="bi bi-arrow-right-circle"></i>
            </div>

        </div>
    </a>
</div>

        <div class="col-md-4">
            <a href="<?php echo e(route('operations.feasibility.inprogress')); ?>">
                <div class="dashboard-card" style="background: #ff6a00;">
                <div class="dashboard-title">In Progress</div>
                <div class="icon-box"><i class="bi bi-arrow-repeat"></i></div>
            </div>
            </a>
            
        </div>

        <div class="col-md-4">
            <a href="<?php echo e(route('operations.feasibility.closed')); ?>">
                <div class="dashboard-card" style="background: #0083b0;">
                <div class="dashboard-title">Closed</div>
                <div class="icon-box"><i class="bi bi-check-circle-fill"></i></div>
            </div>
            </a>
            
        </div>

        <!-- ======= DELIVERABLES ======= -->
        <div class="col-12 mt-4">
            <h3>Deliverables</h3>
        </div>

        <div class="col-md-4">
            <a href="<?php echo e(route('operations.deliverables.open')); ?>">
                <div class="dashboard-card" style="background: #ff4e50;">
                <div class="dashboard-title">Open</div>
                <div class="icon-box"><i class="bi bi-folder2-open"></i></div>
            </div>
            </a>
            
        </div>

        <div class="col-md-4">
            <a href="<?php echo e(route('operations.deliverables.inprogress')); ?>">
                <div class="dashboard-card" style="background: #fc913a;">
                <div class="dashboard-title">In Progress</div>
                <div class="icon-box"><i class="bi bi-arrow-repeat"></i></div>
            </div>

            </a>
            
        </div>

        <div class="col-md-4">
            <a href="<?php echo e(route('operations.deliverables.delivery')); ?>">
                  <div class="dashboard-card" style="background: #28c76f;">
                <div class="dashboard-title">Delivery</div>
                <div class="icon-box"><i class="bi bi-check2-all"></i></div>
            </div>
            </a>
           
        </div>

        <!-- ======= UPCOMING RENEWALS ======= -->
        <div class="col-12 mt-4">
            <h3>Upcoming Renewals</h3>
        </div>

        <div class="col-md-4">
            <a href="operation/renewals/today">
                <div class="dashboard-card" style="background: #845ec2;">
                <div class="dashboard-title">Today</div>
                <div class="icon-box"><i class="bi bi-calendar-day"></i></div>
            </div>
            </a>
            
        </div>

        <div class="col-md-4">
            <a href="">
                <div class="dashboard-card" style="background: #d65db1;">
                <div class="dashboard-title">Tomorrow</div>
                <div class="icon-box"><i class="bi bi-calendar2-week"></i></div>
            </div>
            </a>
            
        </div>

        <div class="col-md-4">
            <a href="">
                <div class="dashboard-card" style="background: #ff9671;">
                <div class="dashboard-title">This Week</div>
                <div class="icon-box"><i class="bi bi-calendar-week"></i></div>
            </div>
            </a>
            
        </div>

    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\wlcome\multipleuserpage\resources\views\welcome.blade.php ENDPATH**/ ?>