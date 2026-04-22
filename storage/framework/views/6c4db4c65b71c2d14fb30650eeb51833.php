

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="bi bi-shield-check me-2"></i>Assurance
                    </h3>
                    <?php if($permissions && $permissions->can_add): ?>
                    <button class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Add New
                    </button>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Assurance Module</strong> - Quality assurance and compliance tracking system.
                    </div>
                    
                    <?php if($permissions && $permissions->can_view): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <?php if($permissions->can_edit || $permissions->can_delete): ?>
                                    <th>Actions</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="<?php echo e($permissions->can_edit || $permissions->can_delete ? 5 : 4); ?>" class="text-center text-muted">
                                        No records found. Add new records to get started.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        You do not have permission to view this content.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\assurance\index.blade.php ENDPATH**/ ?>