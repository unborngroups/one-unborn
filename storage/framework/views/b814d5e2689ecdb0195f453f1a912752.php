
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Professional Dashboard Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-2 fw-bold">
                        <i class="bi bi-speedometer2 text-primary me-2"></i>Dashboard
                    </h1>
                    <p class="text-muted mb-0">Real-time overview of feasibility, purchase orders, and deliverables</p>
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
    <!-- Professional Filter Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <h5 class="mb-0 me-3">
                    <i class="bi bi-funnel text-primary"></i> Filters
                </h5>
                <small class="text-muted">Apply date filters to refine dashboard data</small>
            </div>
            <form method="get" id="dashboard-filter-form" class="g-3">
                <div class="row align-items-end">
                    <div class="col-md-2">
                        <label for="filter_date" class="form-label fw-semibold">Date</label>
                        <input type="date" name="filter_date" id="filter_date" value="<?php echo e($filterDate ?? ''); ?>" class="form-control form-control-sm" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-2">
                        <label for="filter_month" class="form-label fw-semibold">Month</label>
                        <input type="month" name="filter_month" id="filter_month" value="<?php echo e($filterMonth ?? ''); ?>" class="form-control form-control-sm" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-2">
                        <label for="filter_day" class="form-label fw-semibold">Day(s)</label>
                        <input type="number" min="1" max="31" name="filter_day" id="filter_day" value="<?php echo e($filterDay ?? ''); ?>" class="form-control form-control-sm" placeholder="e.g. 6" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-2">
                        <label for="filter_year" class="form-label fw-semibold">Year</label>
                        <input type="number" min="2000" max="2100" name="filter_year" id="filter_year" value="<?php echo e($filterYear ?? ''); ?>" class="form-control form-control-sm" placeholder="e.g. 2026" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold d-block">&nbsp;</label>
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetFilters()">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-search"></i> Apply
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold d-block">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <span class="badge bg-info text-white align-self-center">
                                <i class="bi bi-info-circle"></i> Auto-applied
                            </span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        // Auto-submit form when any filter field is cleared (set to empty)
        ['filter_date', 'filter_month', 'filter_day', 'filter_year'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', function() {
                    if (this.value === '') {
                        document.getElementById('dashboard-filter-form').submit();
                    }
                });
            }
        });

        // Reset all filter fields
        function resetFilters() {
            ['filter_date', 'filter_month', 'filter_day', 'filter_year'].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) {
                    el.value = '';
                }
            });
            // Submit form to refresh dashboard
            document.getElementById('dashboard-filter-form').submit();
        }
    </script>
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

        /* Update info styling */
        .update-info {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 0.5rem;
            line-height: 1.3;
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
        }

        /* Pulse animation for new updates */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .has-updates {
            animation: pulse 2s infinite;
        }
    </style>

    <!-- Feasibility Section -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="section-header">
                <h3><i class="bi bi-clipboard-check text-primary me-2"></i>Feasibility</h3>
                <span class="badge bg-primary"><?php echo e(array_sum($feasibilityCounts)); ?> Total</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card" style="background:linear-gradient(135deg, #28a745, #20c997);" onclick="showTable('feasibility','open')">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="dashboard-count"><?php echo e($feasibilityCounts['open']); ?></h3>
                        <h4 class="dashboard-label">Open</h4>
                    </div>
                    <i class="bi bi-grid dashboard-icon-bg"></i>
                </div>
                <div class="dashboard-bottom">
                    <i class="bi bi-list-ul me-1"></i> View Details
                </div>
                <?php if($feasibilityUpdates['open']): ?>
                <div class="update-info">
                    <i class="bi bi-clock me-1"></i> <?php echo e(optional($feasibilityUpdates['open']->updatedUser)->name ?? '-'); ?> • <?php echo e($feasibilityUpdates['open']->updated_at->diffForHumans()); ?>

                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card" style="background:linear-gradient(135deg, #fd7e14, #ff922b);" onclick="showTable('feasibility','inprogress')">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="dashboard-count"><?php echo e($feasibilityCounts['inprogress']); ?></h3>
                        <h4 class="dashboard-label">In Progress</h4>
                    </div>
                    <i class="bi bi-arrow-repeat dashboard-icon-bg"></i>
                </div>
                <div class="dashboard-bottom">
                    <i class="bi bi-list-ul me-1"></i> View Details
                </div>
                <?php if($feasibilityUpdates['inprogress']): ?>
                <div class="update-info">
                    <i class="bi bi-clock me-1"></i> <?php echo e(optional($feasibilityUpdates['inprogress']->updatedUser)->name ?? '-'); ?> • <?php echo e($feasibilityUpdates['inprogress']->updated_at->diffForHumans()); ?>

                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card" style="background:linear-gradient(135deg, #17a2b8, #20c997);" onclick="showTable('feasibility','closed')">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="dashboard-count"><?php echo e($feasibilityCounts['closed']); ?></h3>
                        <h4 class="dashboard-label">Closed</h4>
                    </div>
                    <i class="bi bi-check-circle-fill dashboard-icon-bg"></i>
                </div>
                <div class="dashboard-bottom">
                    <i class="bi bi-list-ul me-1"></i> View Details
                </div>
                <?php if($feasibilityUpdates['closed']): ?>
                <div class="update-info">
                    <i class="bi bi-clock me-1"></i> <?php echo e(optional($feasibilityUpdates['closed']->updatedUser)->name ?? '-'); ?> • <?php echo e($feasibilityUpdates['closed']->updated_at->diffForHumans()); ?>

                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Purchase Orders Section -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="section-header">
                <h3><i class="bi bi-receipt-cutoff text-primary me-2"></i>Purchase Orders</h3>
                <span class="badge bg-primary"><?php echo e(array_sum($poCounts)); ?> Total</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card" style="background:linear-gradient(135deg, #6f42c1, #9b59b6);" onclick="showTable('po','open')">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="dashboard-count"><?php echo e($poCounts['open']); ?></h3>
                        <h4 class="dashboard-label">Open</h4>
                    </div>
                    <i class="bi bi-receipt-cutoff dashboard-icon-bg"></i>
                </div>
                <div class="dashboard-bottom">
                    <i class="bi bi-list-ul me-1"></i> View Details
                </div>
                <?php if($poUpdates['open']): ?>
                <div class="update-info">
                    <i class="bi bi-clock me-1"></i> <?php echo e(optional($poUpdates['open']->updatedUser)->name ?? '-'); ?> • <?php echo e($poUpdates['open']->updated_at->diffForHumans()); ?>

                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card" style="background:linear-gradient(135deg, #16a085, #20c997);" onclick="showTable('po','closed')">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="dashboard-count"><?php echo e($poCounts['closed']); ?></h3>
                        <h4 class="dashboard-label">Closed</h4>
                    </div>
                    <i class="bi bi-check2-circle dashboard-icon-bg"></i>
                </div>
                <div class="dashboard-bottom">
                    <i class="bi bi-list-ul me-1"></i> View Details
                </div>
                <?php if($poUpdates['closed']): ?>
                <div class="update-info">
                    <i class="bi bi-clock me-1"></i> <?php echo e(optional($poUpdates['closed']->updatedUser)->name ?? '-'); ?> • <?php echo e($poUpdates['closed']->updated_at->diffForHumans()); ?>

                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Deliverables Section -->
    <div class="row g- mb-4">
        <div class="col-12">
            <div class="section-header">
                <h3><i class="bi bi-box-seam text-primary me-2"></i>Deliverables</h3>
                <span class="badge bg-primary"><?php echo e(array_sum($deliverableCounts)); ?> Total</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card" style="background:linear-gradient(135deg, #dc3545, #ff4e50);" onclick="showTable('deliverable','open')">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="dashboard-count"><?php echo e($deliverableCounts['open']); ?></h3>
                        <h4 class="dashboard-label">Open</h4>
                    </div>
                    <i class="bi bi-folder2-open dashboard-icon-bg"></i>
                </div>
                <div class="dashboard-bottom">
                    <i class="bi bi-list-ul me-1"></i> View Details
                </div>
                <?php if($deliverableUpdates['open']): ?>
                <div class="update-info">
                    <i class="bi bi-clock me-1"></i> <?php echo e(optional($deliverableUpdates['open']->updatedUser)->name ?? '-'); ?> • <?php echo e($deliverableUpdates['open']->updated_at->diffForHumans()); ?>

                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card" style="background:linear-gradient(135deg, #fd7e14, #fc913a);" onclick="showTable('deliverable','inprogress')">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="dashboard-count"><?php echo e($deliverableCounts['inprogress']); ?></h3>
                        <h4 class="dashboard-label">In Progress</h4>
                    </div>
                    <i class="bi bi-arrow-repeat dashboard-icon-bg"></i>
                </div>
                <div class="dashboard-bottom">
                    <i class="bi bi-list-ul me-1"></i> View Details
                </div>
                <?php if($deliverableUpdates['inprogress']): ?>
                <div class="update-info">
                    <i class="bi bi-clock me-1"></i> <?php echo e(optional($deliverableUpdates['inprogress']->updatedUser)->name ?? '-'); ?> • <?php echo e($deliverableUpdates['inprogress']->updated_at->diffForHumans()); ?>

                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card" style="background:linear-gradient(135deg, #28a745, #28c76f);" onclick="showTable('deliverable','delivered')">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="dashboard-count"><?php echo e($deliverableCounts['delivered']); ?></h3>
                        <h4 class="dashboard-label">Delivered</h4>
                    </div>
                    <i class="bi bi-check2-all dashboard-icon-bg"></i>
                </div>
                <div class="dashboard-bottom">
                    <i class="bi bi-list-ul me-1"></i> View Details
                </div>
                <?php if($deliverableUpdates['delivered']): ?>
                <div class="update-info">
                    <i class="bi bi-clock me-1"></i> <?php echo e(optional($deliverableUpdates['delivered']->updatedUser)->name ?? '-'); ?> • <?php echo e($deliverableUpdates['delivered']->updated_at->diffForHumans()); ?>

                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal for filtered table -->
    <div class="modal fade" id="dashboardTableModal" tabindex="-1" aria-labelledby="dashboardTableModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h5 class="modal-title mb-0" id="dashboardTableModalLabel">Details</h5>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" id="downloadExcelBtn" class="btn btn-success btn-sm" onclick="downloadSelectedExcel()">
                                <i class="bi bi-download me-1"></i> Download Excel
                            </button>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
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
            // Download button is now always visible
            // Show modal
            var modal = new bootstrap.Modal(document.getElementById('dashboardTableModal'));
            modal.show();
            fetch(`<?php echo e(route('report_dashboard.table')); ?>?type=${type}&status=${status}` +
                    `&filter_date=${encodeURIComponent(filter_date)}` +
                    `&filter_month=${encodeURIComponent(filter_month)}` +
                    `&filter_day=${encodeURIComponent(filter_day)}` +
                    `&filter_year=${encodeURIComponent(filter_year)}`
                )
                .then(r => r.text())
                .then(html => {
                    container.innerHTML = html;
                    // Set modal title with type
                    document.getElementById('dashboardTableModalLabel').textContent = type.charAt(0).toUpperCase() + type.slice(1) + ' Details';
                    // Store the current type for download function
                    window.currentTableType = type;
                })
                .catch(() => {
                    container.innerHTML = '<div class="text-danger p-4">Error loading data.</div>';
                });
        }

        function downloadSelectedExcel() {
            const checked = Array.from(
                document.querySelectorAll('.row-checkbox:checked')
            ).map(cb => cb.value);

            // Get current filters
            const filter_date = document.getElementById('filter_date').value;
            const filter_month = document.getElementById('filter_month').value;
            const filter_day = document.getElementById('filter_day').value;
            const filter_year = document.getElementById('filter_year').value;

            const url = `<?php echo e(route('report_dashboard.downloadExcel')); ?>`;
            const form = document.createElement('form');

            form.method = 'POST';
            form.action = url;
            form.target = '_blank';

            form.innerHTML = `
                <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                <input type="hidden" name="type" value="${window.currentTableType || 'feasibility'}">
                <input type="hidden" name="download_all" value="${checked.length === 0 ? 'true' : 'false'}">
            `;

            // Add filter parameters
            if (filter_date) form.innerHTML += `<input type="hidden" name="filter_date" value="${filter_date}">`;
            if (filter_month) form.innerHTML += `<input type="hidden" name="filter_month" value="${filter_month}">`;
            if (filter_day) form.innerHTML += `<input type="hidden" name="filter_day" value="${filter_day}">`;
            if (filter_year) form.innerHTML += `<input type="hidden" name="filter_year" value="${filter_year}">`;

            // Add selected IDs or leave empty for all records
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
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\report_dashboard.blade.php ENDPATH**/ ?>