<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Professional Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-2 fw-bold">
                        <i class="bi bi-house-heart text-primary me-2"></i>Welcome to <span class="text-primary">Unborn Network</span>
                    </h1>
                    <p class="text-muted mb-0">Comprehensive overview of services, feasibility, purchase orders, and deliverables</p>
                </div>
                <div class="text-end">
                    <div class="text-muted small">
                        <i class="bi bi-clock me-1"></i>
                        Last updated: <span id="last-updated"><?php echo e(now()->format('M j, Y H:i')); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Modern Dashboard Card Styles */
        .dashboard-card {
            padding: 1.5rem;
            border-radius: 12px;
            color: white;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 2px 4px rgba(0, 0, 0, 0.06);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            cursor: pointer;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            min-height: 140px;
        }

        .dashboard-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 25px rgba(0, 0, 0, 0.15), 0 10px 10px rgba(0, 0, 0, 0.04);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .dashboard-card:hover::before {
            opacity: 1;
        }

        .dashboard-count {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            line-height: 1;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .dashboard-label {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0.5rem 0 0 0;
            opacity: 0.95;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .dashboard-icon-bg {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.12;
            font-size: 4rem;
            transition: all 0.3s ease;
        }

        .dashboard-card:hover .dashboard-icon-bg {
            opacity: 0.2;
            transform: translateY(-50%) scale(1.1) rotate(5deg);
        }

        .dashboard-bottom {
            background: rgba(0, 0, 0, 0.2);
            padding: 0.75rem 0;
            text-align: center;
            border-radius: 8px;
            margin-top: 1rem;
            font-weight: 600;
            font-size: 0.9rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .dashboard-card:hover .dashboard-bottom {
            background: rgba(0, 0, 0, 0.3);
            transform: translateY(-2px);
        }

        .dashboard-bottom i {
            transition: transform 0.3s ease;
        }

        .dashboard-card:hover .dashboard-bottom i {
            transform: translateX(4px);
        }

        /* Section Headers */
        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e9ecef;
        }

        .section-header h3 {
            margin: 0;
            font-weight: 700;
            color: #2c3e50;
            font-size: 1.5rem;
        }

        .section-header .badge {
            margin-left: 1rem;
            font-size: 0.8rem;
        }

        /* Service Card Specific Styles */
        .service-card {
            min-height: 120px;
        }

        .service-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin: 0;
            opacity: 0.95;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .service-stats {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-top: 0.5rem;
        }

        .service-icon {
            font-size: 3rem;
            opacity: 0.15;
            transition: all 0.3s ease;
        }

        .service-card:hover .service-icon {
            opacity: 0.25;
            transform: scale(1.1) rotate(5deg);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .dashboard-card {
                padding: 1rem;
                min-height: 120px;
            }
            
            .dashboard-count {
                font-size: 2rem;
            }
            
            .dashboard-label {
                font-size: 1rem;
            }
            
            .dashboard-icon-bg {
                font-size: 3rem;
            }

            .service-icon {
                font-size: 2.5rem;
            }
        }

        a {
            text-decoration: none;
        }
    </style>

    <!-- Services Overview Section -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="section-header">
                <h3><i class="bi bi-globe text-primary me-2"></i>Services Overview</h3>
                <span class="badge bg-primary"><?php echo e(array_sum(array_column($serviceCounts, 'links'))); ?> Total Links</span>
            </div>
        </div>
        <!-- Broadband -->
        <div class="col-md-3">
            <div class="dashboard-card service-card" style="background: linear-gradient(135deg, #1e3c72, #2a5298);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="service-title">Broadband</h4>
                        <div class="service-stats">
                            <i class="bi bi-link-45deg me-1"></i> <?php echo e($serviceCounts['Broadband']['links']); ?> Links<br>
                            <i class="bi bi-geo-alt me-1"></i> <?php echo e($serviceCounts['Broadband']['locations']); ?> Locations
                        </div>
                    </div>
                    <i class="bi bi-wifi service-icon"></i>
                </div>
            </div>
        </div>

        <!-- ILL -->
        <div class="col-md-3">
            <div class="dashboard-card service-card" style="background: linear-gradient(135deg, #f7971e, #ffd200);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="service-title">ILL</h4>
                        <div class="service-stats">
                            <i class="bi bi-link-45deg me-1"></i> <?php echo e($serviceCounts['ILL']['links']); ?> Links<br>
                            <i class="bi bi-geo-alt me-1"></i> <?php echo e($serviceCounts['ILL']['locations']); ?> Locations
                        </div>
                    </div>
                    <i class="bi bi-diagram-3-fill service-icon"></i>
                </div>
            </div>
        </div>

        <!-- P2P -->
        <div class="col-md-3">
            <div class="dashboard-card service-card" style="background: linear-gradient(135deg, #11998e, #38ef7d);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="service-title">P2P</h4>
                        <div class="service-stats">
                            <i class="bi bi-link-45deg me-1"></i> <?php echo e($serviceCounts['P2P']['links']); ?> Links<br>
                            <i class="bi bi-geo-alt me-1"></i> <?php echo e($serviceCounts['P2P']['locations']); ?> Locations
                        </div>
                    </div>
                    <i class="bi bi-diagram-2-fill service-icon"></i>
                </div>
            </div>
        </div>

        <!-- NNI -->
        <div class="col-md-3">
            <div class="dashboard-card service-card" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="service-title">NNI</h4>
                        <div class="service-stats">
                            <i class="bi bi-link-45deg me-1"></i> <?php echo e($serviceCounts['NNI']['links']); ?> Links<br>
                            <i class="bi bi-geo-alt me-1"></i> <?php echo e($serviceCounts['NNI']['locations']); ?> Locations
                        </div>
                    </div>
                    <i class="bi bi-diagram-3 service-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-0">
        <h3>Feasibility</h3>
        <div class="row g-4 mt-2">
            <div class="col-md-4">
                <a href="<?php echo e(route('operations.feasibility.open')); ?>" class="text-decoration-none">
                    <div class="dashboard-card" style="background:linear-gradient(135deg, #28a745, #20c997);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="dashboard-count"><?php echo e($feasibilityCounts['open'] ?? 0); ?></h3>
                                <h4 class="dashboard-label">Open</h4>
                            </div>
                            <i class="bi bi-grid dashboard-icon-bg"></i>
                        </div>
                        <div class="dashboard-bottom">
                            <i class="bi bi-list-ul me-1"></i> View Details
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="<?php echo e(route('operations.feasibility.inprogress')); ?>" class="text-decoration-none">
                    <div class="dashboard-card" style="background:linear-gradient(135deg, #fd7e14, #ff922b);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="dashboard-count"><?php echo e($feasibilityCounts['inprogress'] ?? 0); ?></h3>
                                <h4 class="dashboard-label">In Progress</h4>
                            </div>
                            <i class="bi bi-arrow-repeat dashboard-icon-bg"></i>
                        </div>
                        <div class="dashboard-bottom">
                            <i class="bi bi-list-ul me-1"></i> View Details
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="<?php echo e(route('operations.feasibility.closed')); ?>" class="text-decoration-none">
                    <div class="dashboard-card" style="background:linear-gradient(135deg, #17a2b8, #20c997);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="dashboard-count"><?php echo e($feasibilityCounts['closed'] ?? 0); ?></h3>
                                <h4 class="dashboard-label">Closed</h4>
                            </div>
                            <i class="bi bi-check-circle-fill dashboard-icon-bg"></i>
                        </div>
                        <div class="dashboard-bottom">
                            <i class="bi bi-list-ul me-1"></i> View Details
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
                    <div class="dashboard-card" style="background:linear-gradient(135deg, #6f42c1, #9b59b6);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="dashboard-count"><?php echo e($purchaseOrderCounts['open'] ?? 0); ?></h3>
                                <h4 class="dashboard-label">Open</h4>
                            </div>
                            <i class="bi bi-receipt-cutoff dashboard-icon-bg"></i>
                        </div>
                        <div class="dashboard-bottom">
                            <i class="bi bi-list-ul me-1"></i> View Details
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="<?php echo e(route('sm.purchaseorder.index')); ?>" class="text-decoration-none">
                    <div class="dashboard-card" style="background:linear-gradient(135deg, #16a085, #20c997);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="dashboard-count"><?php echo e($purchaseOrderCounts['closed'] ?? 0); ?></h3>
                                <h4 class="dashboard-label">Closed</h4>
                            </div>
                            <i class="bi bi-check2-circle dashboard-icon-bg"></i>
                        </div>
                        <div class="dashboard-bottom">
                            <i class="bi bi-list-ul me-1"></i> View Details
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="col-md-4">
                <a href="<?php echo e(route('operations.feasibility.inprogress', ['exception' => 1])); ?>" class="text-decoration-none">
                    <div class="dashboard-card" style="background:linear-gradient(135deg, #e67e22, #fd7e14);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="dashboard-count"><?php echo e($purchaseOrderCounts['exception'] ?? 0); ?></h3>
                                <h4 class="dashboard-label">Exceptions</h4>
                            </div>
                            <i class="bi bi-exclamation-circle dashboard-icon-bg"></i>
                        </div>
                        <div class="dashboard-bottom">
                            <i class="bi bi-list-ul me-1"></i> View Details
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
                    <div class="dashboard-card" style="background:linear-gradient(135deg, #dc3545, #ff4e50);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="dashboard-count"><?php echo e($deliverableCounts['open'] ?? 0); ?></h3>
                                <h4 class="dashboard-label">Open</h4>
                            </div>
                            <i class="bi bi-folder2-open dashboard-icon-bg"></i>
                        </div>
                        <div class="dashboard-bottom">
                            <i class="bi bi-list-ul me-1"></i> View Details
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4 col-sm-6">
                <a href="<?php echo e(route('operations.deliverables.inprogress')); ?>" class="text-decoration-none">
                    <div class="dashboard-card" style="background:linear-gradient(135deg, #fd7e14, #fc913a);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="dashboard-count"><?php echo e($deliverableCounts['inprogress'] ?? 0); ?></h3>
                                <h4 class="dashboard-label">In Progress</h4>
                            </div>
                            <i class="bi bi-arrow-repeat dashboard-icon-bg"></i>
                        </div>
                        <div class="dashboard-bottom">
                            <i class="bi bi-list-ul me-1"></i> View Details
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4 col-sm-6">
                <a href="<?php echo e(route('operations.deliverables.delivery')); ?>" class="text-decoration-none">
                    <div class="dashboard-card" style="background:linear-gradient(135deg, #28a745, #28c76f);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="dashboard-count"><?php echo e($deliverableCounts['delivery'] ?? 0); ?></h3>
                                <h4 class="dashboard-label">Delivered</h4>
                            </div>
                            <i class="bi bi-check2-all dashboard-icon-bg"></i>
                        </div>
                        <div class="dashboard-bottom">
                            <i class="bi bi-list-ul me-1"></i> View Details
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="mt-1">

    <h3>Upcoming Renewals</h3>
    <div class="row g-4 mt-2">

        <!-- Renewals Today -->
         <div class="col-md-3 col-sm-6">
                <a href="<?php echo e(route('operations.renewals.index', ['filter' => 'today'])); ?>" class="text-decoration-none">
                    <div class="dashboard-card" style="background:linear-gradient(135deg, #845ec2, #6c5ce7);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="dashboard-count"><?php echo e($renewalCounts['today'] ?? 0); ?></h3>
                                <h4 class="dashboard-label">Today</h4>
                            </div>
                            <i class="bi bi-calendar2-week dashboard-icon-bg"></i>
                        </div>
                        <div class="dashboard-bottom">
                            <i class="bi bi-list-ul me-1"></i> View Details
                        </div>
                    </div>
                </a>
            </div>

        <!--  -->
        <div class="col-md-3 col-sm-6">
                <a href="<?php echo e(route('operations.renewals.index', ['filter' => 'tomorrow'])); ?>" class="text-decoration-none">
                        <div class="dashboard-card" style="background:linear-gradient(135deg, #d65db1, #ee5a6f);">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="dashboard-count"><?php echo e($renewalCounts['tomorrow'] ?? 0); ?></h3>
                                    <h4 class="dashboard-label">Tomorrow</h4>
                                </div>
                                <i class="bi bi-calendar2-week dashboard-icon-bg"></i>
                            </div>
                            <div class="dashboard-bottom">
                                <i class="bi bi-list-ul me-1"></i> View Details
                            </div>
                        </div>
                    </a>  
            </div>
      
        <div class="col-md-3 col-sm-6">
                <a href="<?php echo e(route('operations.renewals.index', ['filter' => 'week'])); ?>" class="text-decoration-none">
                        <div class="dashboard-card" style="background:linear-gradient(135deg, #ff9671, #ff6348);">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="dashboard-count"><?php echo e($renewalCounts['week'] ?? 0); ?></h3>
                                    <h4 class="dashboard-label">This Week</h4>
                                </div>
                                <i class="bi bi-calendar-week dashboard-icon-bg"></i>
                            </div>
                            <div class="dashboard-bottom">
                                <i class="bi bi-list-ul me-1"></i> View Details
                            </div>
                        </div>
                    </a>
            </div>

             <!-- Expired but not renewed Deliverables -->
    <div class="col-md-3 col-sm-6">
                <a href="<?php echo e(route('operations.renewals.index', ['filter' => 'expired'])); ?>" class="text-decoration-none">
        <div class="dashboard-card" style="background:linear-gradient(135deg, #ff6f61, #dc3545);">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h3 class="dashboard-count"><?php echo e($expiredRenewalCount ?? 0); ?></h3>
                    <h4 class="dashboard-label">Expired</h4>
                </div>
                <i class="bi bi-exclamation-triangle dashboard-icon-bg"></i>
            </div>
            <div class="dashboard-bottom">
                <i class="bi bi-list-ul me-1"></i> View Details
            </div>
        </div>
                    </a>

    </div>

    </div>

    <!-- Detailed Upcoming Renewals Table -->
    <div class="mt-4">


</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\one-unborn-main\resources\views/welcome.blade.php ENDPATH**/ ?>