<?php $__env->startSection('title', 'Payment Batches'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- 🔍 Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Filter Payment Batches</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('payments.batches')); ?>">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="<?php echo e(request('search')); ?>" placeholder="Batch reference...">
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Status</option>
                                    <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>Pending</option>
                                    <option value="pending_approval" <?php echo e(request('status') === 'pending_approval' ? 'selected' : ''); ?>>Pending Approval</option>
                                    <option value="processing" <?php echo e(request('status') === 'processing' ? 'selected' : ''); ?>>Processing</option>
                                    <option value="completed" <?php echo e(request('status') === 'completed' ? 'selected' : ''); ?>>Completed</option>
                                    <option value="failed" <?php echo e(request('status') === 'failed' ? 'selected' : ''); ?>>Failed</option>
                                    <option value="cancelled" <?php echo e(request('status') === 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" 
                                       value="<?php echo e(request('date_from')); ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" 
                                       value="<?php echo e(request('date_to')); ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="per_page" class="form-label">Per Page</label>
                                <select class="form-select" id="per_page" name="per_page">
                                    <option value="10" <?php echo e(request('per_page') == 10 ? 'selected' : ''); ?>>10</option>
                                    <option value="25" <?php echo e(request('per_page') == 25 ? 'selected' : ''); ?>>25</option>
                                    <option value="50" <?php echo e(request('per_page') == 50 ? 'selected' : ''); ?>>50</option>
                                    <option value="100" <?php echo e(request('per_page') == 100 ? 'selected' : ''); ?>>100</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 📊 Payment Batches Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Payment Batches</h5>
                    <span class="badge bg-primary"><?php echo e($batches->total()); ?> batches</span>
                </div>
                <div class="card-body p-0">
                    <?php if($batches->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Batch Reference</th>
                                        <th>Total Amount</th>
                                        <th>Invoices</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Processed</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo e($batch->batch_reference); ?></strong>
                                                <?php if($batch->approvedBy): ?>
                                                    <br><small class="text-muted">By: <?php echo e($batch->approvedBy->name); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>₹<?php echo e(number_format($batch->total_amount, 2)); ?></td>
                                            <td><?php echo e($batch->total_invoices); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo e(getBatchStatusColor($batch->status)); ?>">
                                                    <?php echo e(ucfirst(str_replace('_', ' ', $batch->status))); ?>

                                                </span>
                                                <?php if($batch->status === 'completed_with_failures'): ?>
                                                    <br><small class="text-warning">With failures</small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($batch->created_at->format('M d, Y H:i')); ?></td>
                                            <td>
                                                <?php if($batch->processed_at): ?>
                                                    <?php echo e($batch->processed_at->format('M d, Y H:i')); ?>

                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?php echo e(route('payments.batches.show', $batch->id)); ?>" 
                                                       class="btn btn-sm btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    <?php if($batch->status === 'accountant_approval'): ?>
                                                        <button type="button" class="btn btn-success" 
                                                                onclick="approveAccountant(<?php echo e($batch->id); ?>)" title="Approve (Accountant)">
                                                            <i class="fas fa-check"></i> Accountant
                                                        </button>
                                                        <button type="button" class="btn btn-danger" 
                                                                onclick="rejectAccountant(<?php echo e($batch->id); ?>)" title="Reject (Accountant)">
                                                            <i class="fas fa-times"></i> Accountant
                                                        </button>
                                                    <?php endif; ?>

                                                    <?php if($batch->status === 'finance_manager_approval'): ?>
                                                        <button type="button" class="btn btn-success" 
                                                                onclick="approveFinanceManager(<?php echo e($batch->id); ?>)" title="Approve (Finance Manager)">
                                                            <i class="fas fa-check"></i> Finance Manager
                                                        </button>
                                                        <button type="button" class="btn btn-danger" 
                                                                onclick="rejectFinanceManager(<?php echo e($batch->id); ?>)" title="Reject (Finance Manager)">
                                                            <i class="fas fa-times"></i> Finance Manager
                                                        </button>
                                                    <?php endif; ?>

                                                    <?php if($batch->status === 'pending'): ?>
                                                        <button type="button" class="btn btn-sm btn-info" 
                                                                onclick="processBatch(<?php echo e($batch->id); ?>)" title="Process">
                                                            <i class="fas fa-play"></i>
                                                        </button>
                                                    <?php endif; ?>

                                                    <a href="<?php echo e(route('payments.batches.export', $batch->id)); ?>" 
                                                       class="btn btn-sm btn-outline-success" title="Export Excel">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <div>
                                Showing <?php echo e($batches->firstItem()); ?> to <?php echo e($batches->lastItem()); ?> 
                                of <?php echo e($batches->total()); ?> entries
                            </div>
                            <?php echo e($batches->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox text-muted fa-3x mb-3"></i>
                            <h6>No payment batches found</h6>
                            <p class="text-muted">
                                <?php if(request()->hasAny(['search', 'status', 'date_from', 'date_to'])): ?>
                                    No batches match your search criteria.
                                <?php else: ?>
                                    No payment batches have been created yet.
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approvalForm" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <input type="hidden" id="actionType" name="action_type">
                    <input type="hidden" id="batchId" name="batch_id">
                    
                    <div id="approveContent" style="display: none;">
                        <p>Are you sure you want to approve this payment batch?</p>
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks (optional)</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3" 
                                      placeholder="Add any remarks for this approval..."></textarea>
                        </div>
                    </div>
                    
                    <div id="rejectContent" style="display: none;">
                        <p>Are you sure you want to reject this payment batch?</p>
                        <div class="mb-3">
                            <label for="reject_remarks" class="form-label">Rejection Reason *</label>
                            <textarea class="form-control" id="reject_remarks" name="reject_remarks" rows="3" 
                                      placeholder="Please provide reason for rejection..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function approveAccountant(batchId) {
    document.getElementById('actionType').value = 'approve-accountant';
    document.getElementById('batchId').value = batchId;
    document.getElementById('approveContent').style.display = 'block';
    document.getElementById('rejectContent').style.display = 'none';
    document.getElementById('submitBtn').className = 'btn btn-success';
    document.getElementById('submitBtn').textContent = 'Approve (Accountant)';
    
    const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
    modal.show();
}

function approveFinanceManager(batchId) {
    document.getElementById('actionType').value = 'approve-finance-manager';
    document.getElementById('batchId').value = batchId;
    document.getElementById('approveContent').style.display = 'block';
    document.getElementById('rejectContent').style.display = 'none';
    document.getElementById('submitBtn').className = 'btn btn-success';
    document.getElementById('submitBtn').textContent = 'Approve (Finance Manager)';
    
    const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
    modal.show();
}

function rejectAccountant(batchId) {
    document.getElementById('actionType').value = 'reject-accountant';
    document.getElementById('batchId').value = batchId;
    document.getElementById('approveContent').style.display = 'none';
    document.getElementById('rejectContent').style.display = 'block';
    document.getElementById('submitBtn').className = 'btn btn-danger';
    document.getElementById('submitBtn').textContent = 'Reject (Accountant)';
    
    const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
    modal.show();
}

function rejectFinanceManager(batchId) {
    document.getElementById('actionType').value = 'reject-finance-manager';
    document.getElementById('batchId').value = batchId;
    document.getElementById('approveContent').style.display = 'none';
    document.getElementById('rejectContent').style.display = 'block';
    document.getElementById('submitBtn').className = 'btn btn-danger';
    document.getElementById('submitBtn').textContent = 'Reject (Finance Manager)';
    
    const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
    modal.show();
}

// Legacy functions for backward compatibility
function approveBatch(batchId) {
    approveAccountant(batchId);
}

function rejectBatch(batchId) {
    rejectAccountant(batchId);
}

document.getElementById('approvalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const actionType = document.getElementById('actionType').value;
    const batchId = document.getElementById('batchId').value;
    const formData = new FormData(this);
    
    let url, method;
    
    switch(actionType) {
        case 'approve-accountant':
            url = `<?php echo e(route("payments.batches.approve-accountant", ":id")); ?>`.replace(':id', batchId);
            method = 'POST';
            break;
        case 'approve-finance-manager':
            url = `<?php echo e(route("payments.batches.approve-finance-manager", ":id")); ?>`.replace(':id', batchId);
            method = 'POST';
            break;
        case 'reject-accountant':
            url = `<?php echo e(route("payments.batches.reject-accountant", ":id")); ?>`.replace(':id', batchId);
            method = 'POST';
            break;
        case 'reject-finance-manager':
            url = `<?php echo e(route("payments.batches.reject-finance-manager", ":id")); ?>`.replace(':id', batchId);
            method = 'POST';
            break;
        default:
            // Legacy fallback
            url = `<?php echo e(route("payments.batches.approve-accountant", ":id")); ?>`.replace(':id', batchId);
            method = 'POST';
            break;
    }
    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success || data.message) {
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php
function getBatchStatusColor($status) {
    $colors = [
        'pending' => 'secondary',
        'pending_approval' => 'warning',
        'processing' => 'info',
        'completed' => 'success',
        'failed' => 'danger',
        'cancelled' => 'dark'
    ];
    return $colors[$status] ?? 'secondary';
}
?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\payments\batches.blade.php ENDPATH**/ ?>