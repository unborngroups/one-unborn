

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-primary">Manage Users</h3>
        
 
        <?php if($permissions && $permissions->can_add): ?>
    <a href="<?php echo e(route('users.create')); ?>" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Add New User
    </a>
<?php endif; ?>
    </div>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    
    <div class="card shadow-lg border-0">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-danger">MANAGE USER</h5>
            <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search...">
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover table-bordered align-middle mb-0" id="userTable">
                <thead class="table-primary text-center">
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>S.No</th>
                        <th>Action</th>
                        <th>Name</th>
                        <th>User Type</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Company</th>
                        <th>Date of Birth</th>
                        <th>Date of Join</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><input type="checkbox" class="rowCheckbox" value="<?php echo e($user->id); ?>"></td>
                            <td class="text-center"><?php echo e($index + 1); ?></td>

                            
                            <td class="text-center d-flex justify-content-center gap-1">
                                <?php
                                $role = strtolower(auth()->user()->userType->name ?? '');
                                $canManage = in_array($role, ['superadmin', 'admin']);
                                ?>

                                
                                <?php if($permissions && $permissions->can_edit): ?>
                            <a href="<?php echo e(route('users.edit', $user)); ?>" class="btn btn-sm btn-primary">
                            <i class="bi bi-pencil"></i>
                            </a>
                                <?php endif; ?>

                            
                                 <?php if($permissions && $permissions->can_delete): ?>
                            <form action="<?php echo e(route('users.destroy', $user)); ?>" method="POST" class="d-inline">
                                 <?php echo csrf_field(); ?>
                                 <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                            <i class="bi bi-trash"></i>
                            </button>
                            </form>
                                <?php endif; ?>          
                                
                                
                                <?php
                                $role = strtolower(auth()->user()->userType->name);
                                $canToggle = in_array($role, ['superadmin', 'admin']);
                                ?>
                                <form action="<?php echo e(route('users.toggle-status', $user->id)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="btn btn-sm <?php echo e($user->status === 'Active' ? 'btn-success' : 'btn-secondary'); ?>"   <?php echo e(!$canToggle ? 'disabled title=You don\'t have permission' : ''); ?>>
                                    
                                    <?php echo e($user->status); ?>

                                    </button>
                                    </form>

                                    
                                   <?php if($permissions && $permissions->can_view): ?>
                                   <a href="<?php echo e(route('users.view', $user->id)); ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-eye"></i>
                                    </a>
                                     <?php endif; ?>

                                     <?php
                                     $role = strtolower(auth()->user()->userType->name ?? '');
                                     ?>
                                     <?php if($permissions->can_edit && in_array($role, ['superadmin', 'admin'])): ?>

                                    <a href="<?php echo e(route('menus.editPrivileges', $user->id)); ?>" class="btn btn-sm btn-info">
                                        <i class="bi bi-gear"></i> 
                                    </a>
                                    <?php endif; ?>
                                     <ul class="sidebar-menu">
      



                            </td>

                            <td><?php echo e(ucfirst($user->name)); ?></td>

                            
                            <td class="text-center">
                                <span class="badge <?php echo e($user->userType->name === 'superadmin' ? 'bg-dark' : 'bg-info'); ?>">
                                    <?php echo e(ucfirst($user->userType->name ?? '-')); ?>

                                </span>
                            </td>

                            <td><?php echo e($user->email); ?></td>
                            <td><?php echo e($user->mobile ?? '-'); ?></td>

                            
                            <td>
                                <?php echo e($user->companies->pluck('company_name')->join(', ') ?: 'No Company Assigned'); ?>

                            </td>

                            
                            <td><?php echo e($user->Date_of_Birth ? \Carbon\Carbon::parse($user->Date_of_Birth)->format('d M Y') : '-'); ?></td>
                            <td><?php echo e($user->Date_of_Joining ? \Carbon\Carbon::parse($user->Date_of_Joining)->format('d M Y') : '-'); ?></td>

                            
                            <td class="text-center">
                                <span class="badge <?php echo e($user->status === 'Active' ? 'bg-success' : 'bg-danger'); ?>">
                                    <?php echo e($user->status); ?>

                                </span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted">No Users Found</td>
                        </tr>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\new\multipleuserpage\resources\views/users/index.blade.php ENDPATH**/ ?>