

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-primary">Email Templates</h3>
         
        <?php if($permissions->can_add): ?>
        <a href="<?php echo e(route('emails.create')); ?>" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> New Template
        </a>
        <?php endif; ?>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

       <div class="card shadow-lg border-0">
        <div class="card-header bg-light d-flex justify-content-between">
            <h5 class="mb-0 text-danger">USER TYPE</h5>
            <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search...">
        </div>
        <div class="card-body p-0">
            <table class="table table-hover table-bordered mb-0" id="userTable">
                <thead class="table-dark-primary text-center">
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                            <th>S.No</th>
                        <th>Action</th>
                        <th>Company Name</th>
                        <th>Subject</th>
                        <th>Status</th>          
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                                <td><input type="checkbox" class="rowCheckbox" value="<?php echo e($template->id); ?>"></td>
                            <td class="text-center"><?php echo e($index+1); ?></td>
                         
                            <td class="text-center d-flex justify-content-center gap-1">
                                
                                <?php if($permissions->can_edit): ?>
                                <a href="<?php echo e(route('emails.edit', $template->id)); ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                 <?php endif; ?>

                                 
                                 <?php if($permissions->can_delete): ?>
                                <form action="<?php echo e(route('emails.destroy', $template->id)); ?>" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this template?')">
                                    <?php echo csrf_field(); ?> 
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                 <?php endif; ?>

                                <form action="<?php echo e(route('templates.toggle-status', $template->id)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="btn btn-sm <?php echo e($template->status == 'Active' ? 'btn-success' : 'btn-secondary'); ?>">
                                        <?php echo e($template->status); ?>
                                    </button>
                                </form>
                            </td>
                             <td><?php echo e($template->company ? $template->company->company_name : '-'); ?></td>
                            <td><?php echo e($template->subject); ?></td>
                            <td class="text-center">
                                <span class="badge <?php echo e($template->status=='Active'?'bg-success':'bg-secondary'); ?>">
                                    <?php echo e($template->status); ?>
                                </span>
                            </td>
                         
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="text-center">No templates found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



<script>
document.getElementById('tableSearch').addEventListener('keyup', function() {
    let value = this.value.toLowerCase();
    document.querySelectorAll('#userTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';
    });
});

document.getElementById('selectAll').addEventListener('change', function() {
    let isChecked = this.checked;
    document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = isChecked);
});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\wlcome\multipleuserpage\resources\views\emails\index.blade.php ENDPATH**/ ?>