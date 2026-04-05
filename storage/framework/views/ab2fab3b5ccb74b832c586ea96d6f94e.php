
<?php $__env->startSection('content'); ?>
<div class="container">
<!-- Deleted: Report Dashboard blade view removed as requested -->
    <form method="get" class="mb-4 " id="dashboard-filter-form">
        <div class="row">
        <div class="col-md-3">
            <label for="filter_date">Date:</label>
            <input type="date" name="filter_date" id="filter_date" value="<?php echo e($filterDate ?? ''); ?>" class="form-input w-full" onchange="this.form.submit()">
        </div>
        <div class="col-md-3">
            <label for="filter_month">Month:</label>
            <input type="month" name="filter_month" id="filter_month" value="<?php echo e($filterMonth ?? ''); ?>" class="form-input w-full" onchange="this.form.submit()">
        </div>
        <div class="col-md-3">
            <label for="filter_day">Day(s):</label>
            <input type="number" min="1" max="31" name="filter_day" id="filter_day" value="<?php echo e($filterDay ?? ''); ?>" class="form-input w-full" placeholder="e.g. 6" onchange="this.form.submit()">
        </div>
        <div class="col-md-3">
            <label for="filter_year">Year:</label>
            <input type="number" min="2000" max="2100" name="filter_year" id="filter_year" value="<?php echo e($filterYear ?? ''); ?>" class="form-input w-full" placeholder="e.g. 2026" onchange="this.form.submit()">
        </div>
        </div>
    </form>
    <script>
    // Auto-submit form when any filter field is cleared (set to empty)
    ['filter_date','filter_month','filter_day','filter_year'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', function() {
                if (this.value === '') {
                    document.getElementById('dashboard-filter-form').submit();
                }
            });
        }
    });
    </script>
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
            cursor: pointer;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }
        .dashboard-title {
            font-size: 20px;
            font-weight: bold;
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

    <div class="row g-4 mt-2">
        <h3>Feasibility</h3>
        <div class="col-md-4">
            <div class="dashboard-card" style="background:#1f7a34;" onclick="showTable('feasibility','open')">
                <h3 class="fw-bold m-0"><?php echo e($feasibilityCounts['open']); ?></h3>
                <h3>Open</h3>
                <i class="bi bi-grid dashboard-icon-bg"></i>
                <div class="dashboard-bottom">
                    List <i class="bi bi-arrow-right-circle"></i>
                </div>
                <?php if($feasibilityUpdates['open']): ?>
                <div class="mt-2"><small>Last updated by: <?php echo e(optional($feasibilityUpdates['open']->updatedUser)->name ?? '-'); ?> at <?php echo e($feasibilityUpdates['open']->updated_at); ?></small></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card" style="background:#ff6a00;" onclick="showTable('feasibility','inprogress')">
                <h3 class="fw-bold m-0"><?php echo e($feasibilityCounts['inprogress']); ?></h3>
                <h3>In Progress</h3>
                <i class="bi bi-arrow-repeat dashboard-icon-bg"></i>
                <div class="dashboard-bottom">
                    List <i class="bi bi-arrow-right-circle"></i>
                </div>
                <?php if($feasibilityUpdates['inprogress']): ?>
                <div class="mt-2"><small>Last updated by: <?php echo e(optional($feasibilityUpdates['inprogress']->updatedUser)->name ?? '-'); ?> at <?php echo e($feasibilityUpdates['inprogress']->updated_at); ?></small></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card" style="background:#0083b0;" onclick="showTable('feasibility','closed')">
                <h3 class="fw-bold m-0"><?php echo e($feasibilityCounts['closed']); ?></h3>
                <h3>Closed</h3>
                <i class="bi bi-check-circle-fill dashboard-icon-bg"></i>
                <div class="dashboard-bottom">
                    List <i class="bi bi-arrow-right-circle"></i>
                </div>
                <?php if($feasibilityUpdates['closed']): ?>
                <div class="mt-2"><small>Last updated by: <?php echo e(optional($feasibilityUpdates['closed']->updatedUser)->name ?? '-'); ?> at <?php echo e($feasibilityUpdates['closed']->updated_at); ?></small></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <h3>Purchase Orders</h3>
        <div class="col-md-4">
            <div class="dashboard-card" style="background:#9b59b6;" onclick="showTable('po','open')">
                <h3 class="fw-bold m-0"><?php echo e($poCounts['open']); ?></h3>
                <h3>Open</h3>
                <i class="bi bi-receipt-cutoff dashboard-icon-bg"></i>
                <div class="dashboard-bottom">
                    List <i class="bi bi-arrow-right-circle"></i>
                </div>
                <?php if($poUpdates['open']): ?>
                <div class="mt-2"><small>Last updated by: <?php echo e(optional($poUpdates['open']->updatedUser)->name ?? '-'); ?> at <?php echo e($poUpdates['open']->updated_at); ?></small></div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="dashboard-card" style="background:#16a085;" onclick="showTable('po','closed')">
                <h3 class="fw-bold m-0"><?php echo e($poCounts['closed']); ?></h3>
                <h3>Closed</h3>
                <i class="bi bi-check2-circle dashboard-icon-bg"></i>
                <div class="dashboard-bottom">
                    List <i class="bi bi-arrow-right-circle"></i>
                </div>
                <?php if($poUpdates['closed']): ?>
                <div class="mt-2"><small>Last updated by: <?php echo e(optional($poUpdates['closed']->updatedUser)->name ?? '-'); ?> at <?php echo e($poUpdates['closed']->updated_at); ?></small></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <h3>Deliverables</h3>
        <div class="col-md-4">
            <div class="dashboard-card" style="background:#ff4e50;" onclick="showTable('deliverable','open')">
                <h3 class="fw-bold m-0"><?php echo e($deliverableCounts['open']); ?></h3>
                <h4>Open</h4>
                <i class="bi bi-folder2-open dashboard-icon-bg"></i>
                <div class="dashboard-bottom">
                    List <i class="bi bi-arrow-right-circle"></i>
                </div>
                <?php if($deliverableUpdates['open']): ?>
                <div class="mt-2"><small>Last updated by: <?php echo e(optional($deliverableUpdates['open']->updatedUser)->name ?? '-'); ?> at <?php echo e($deliverableUpdates['open']->updated_at); ?></small></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card" style="background:#fc913a;" onclick="showTable('deliverable','inprogress')">
                <h3 class="fw-bold m-0"><?php echo e($deliverableCounts['inprogress']); ?></h3>
                <h4>In Progress</h4>
                <i class="bi bi-arrow-repeat dashboard-icon-bg"></i>
                <div class="dashboard-bottom">
                    List <i class="bi bi-arrow-right-circle"></i>
                </div>
                <?php if($deliverableUpdates['inprogress']): ?>
                <div class="mt-2"><small>Last updated by: <?php echo e(optional($deliverableUpdates['inprogress']->updatedUser)->name ?? '-'); ?> at <?php echo e($deliverableUpdates['inprogress']->updated_at); ?></small></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card" style="background:#28c76f;" onclick="showTable('deliverable','delivered')">
                <h3 class="fw-bold m-0"><?php echo e($deliverableCounts['delivered']); ?></h3>
                <h4>Delivered</h4>
                <i class="bi bi-check2-all dashboard-icon-bg"></i>
                <div class="dashboard-bottom">
                    List <i class="bi bi-arrow-right-circle"></i>
                </div>
                <?php if($deliverableUpdates['delivered']): ?>
                <div class="mt-2"><small>Last updated by: <?php echo e(optional($deliverableUpdates['delivered']->updatedUser)->name ?? '-'); ?> at <?php echo e($deliverableUpdates['delivered']->updated_at); ?></small></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

        <!-- Modal for filtered table -->
        <div class="modal fade" id="dashboardTableModal" tabindex="-1" aria-labelledby="dashboardTableModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dashboardTableModalLabel">Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="dashboard-table-container">
                        <!-- Table loads here -->
                    </div>
                </div>
            </div>
        </div>

    <script>
    function showTable(type, status) {
        const container = document.getElementById('dashboard-table-container');
        container.innerHTML = '<div class="text-center p-4"><span class="spinner-border"></span> Loading...</div>';
        // Get current filters
        const filter_date = document.getElementById('filter_date').value;
        const filter_month = document.getElementById('filter_month').value;
        const filter_day = document.getElementById('filter_day').value;
        const filter_year = document.getElementById('filter_year').value;
        // Show modal
        var modal = new bootstrap.Modal(document.getElementById('dashboardTableModal'));
        modal.show();
        fetch(`<?php echo e(route('report_dashboard.table')); ?>?type=${type}&status=${status}`
            + `&filter_date=${encodeURIComponent(filter_date)}`
            + `&filter_month=${encodeURIComponent(filter_month)}`
            + `&filter_day=${encodeURIComponent(filter_day)}`
            + `&filter_year=${encodeURIComponent(filter_year)}`
        )
        .then(r => r.text())
        .then(html => { container.innerHTML = html; })
        .catch(() => { container.innerHTML = '<div class="text-danger p-4">Error loading data.</div>'; });
    }
    </script>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/report_dashboard.blade.php ENDPATH**/ ?>