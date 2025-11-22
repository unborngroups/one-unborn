

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h3 class="fw-bold text-primary mb-4">
        Manage User Type Privileges — <span class="text-dark"><?php echo e($userType->name); ?></span>
    </h3>

    <!-- 
    <div class="alert alert-info mb-4">
        <i class="bi bi-info-circle me-2"></i>
        <strong>User Type Privilege Management:</strong> 
        These are default privileges for the "<?php echo e($userType->name); ?>" user type. 
        When new users are created with this user type, they will automatically inherit these privileges.
        You can still customize individual user privileges later using the gear icon in the user list.
    </div> -->

    <div class="card shadow-lg border-0 rounded-4 p-4">
        <form id="userTypePrivilegeForm" action="<?php echo e(route('menus.updateUserTypePrivileges', $userType->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>

            
            <div class="form-check mb-4">
                <input type="checkbox" class="form-check-input" id="grantAllGlobal">
                <label class="form-check-label fw-semibold" for="grantAllGlobal">Grant All Permissions for User Type</label>
            </div>

            
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center shadow-sm">
                    <thead class="table-primary">
                        <tr>
                            <th>Module Name</th>
                            <th>Menu Name</th>
                            <th>Menu</th>
                            <th>Add</th>
                            <th>Edit</th>
                            <th>Delete</th>
                            <th>View</th>
                            <th>All</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $priv = $userTypePrivileges[$menu->id] ?? null;
                            ?>
                            <tr>
                                <td class="fw-semibold text-start ps-3"><?php echo e(ucfirst($menu->module_name ?? 'General')); ?></td>
                                <td class="fw-semibold text-start ps-4"><?php echo e(ucfirst($menu->name)); ?></td>

                                <td><input type="checkbox" name="privileges[<?php echo e($menu->id); ?>][can_menu]" value="1"
                                    class="perm-checkbox"
                                    <?php echo e($priv && $priv->can_menu ? 'checked' : ''); ?>></td>

                                <td><input type="checkbox" name="privileges[<?php echo e($menu->id); ?>][can_add]" value="1"
                                    class="perm-checkbox"
                                    <?php echo e($priv && $priv->can_add ? 'checked' : ''); ?>></td>

                                <td><input type="checkbox" name="privileges[<?php echo e($menu->id); ?>][can_edit]" value="1"
                                    class="perm-checkbox"
                                    <?php echo e($priv && $priv->can_edit ? 'checked' : ''); ?>></td>

                                <td><input type="checkbox" name="privileges[<?php echo e($menu->id); ?>][can_delete]" value="1"
                                    class="perm-checkbox"
                                    <?php echo e($priv && $priv->can_delete ? 'checked' : ''); ?>></td>

                                <td><input type="checkbox" name="privileges[<?php echo e($menu->id); ?>][can_view]" value="1"
                                    class="perm-checkbox"
                                    <?php echo e($priv && $priv->can_view ? 'checked' : ''); ?>></td>

                                <td>
                                    <input type="checkbox" class="grant-row-all form-check-input"
                                        title="Grant all permissions for this menu">
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            
            <div class="text-end mt-4">
                <button type="submit" class="btn btn-success px-5 py-2 rounded-3 shadow-sm me-2">
                    <i class="bi bi-check-circle me-1"></i> Save User Type Privileges
                </button>
                <a href="<?php echo e(route('usertypetable.index')); ?>" class="btn btn-secondary px-5 py-2 rounded-3">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const globalGrant = document.getElementById('grantAllGlobal');

    // ✅ Global "Grant All" - selects all checkboxes
    globalGrant.addEventListener('change', function () {
        const checked = this.checked;
        document.querySelectorAll('tbody input[type="checkbox"]').forEach(cb => cb.checked = checked);
        document.querySelectorAll('.grant-row-all').forEach(rowCb => rowCb.checked = checked);
    });

    // ✅ Row-wise "Grant All" toggle
    document.querySelectorAll('.grant-row-all').forEach(rowCb => {
        rowCb.addEventListener('change', function () {
            const row = this.closest('tr');
            const checked = this.checked;
            row.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = checked);
        });
    });
});
</script>


<style>
.table {
    border-radius: 8px;
    overflow: hidden;
}
.table th, .table td {
    vertical-align: middle;
    padding: 0.6rem;
}
.table-primary th {
    background-color: #0d6efd !important;
    color: white;
}
.form-check-input,
input[type="checkbox"] {
    cursor: pointer;
    width: 1.1rem;
    height: 1.1rem;
    accent-color: #0d6efd;
    border-radius: 4px;
}
input[type="checkbox"]:hover {
    transform: scale(1.1);
    transition: 0.15s ease;
}
input[type="checkbox"]:focus {
    outline: none;
    box-shadow: 0 0 4px rgba(13, 110, 253, 0.6);
}
.btn-success {
    background-color: #198754;
    border: none;
}
.btn-success:hover {
    background-color: #157347;
}
.btn-secondary {
    background-color: #6c757d;
    border: none;
}
.btn-secondary:hover {
    background-color: #5c636a;
}
.alert-info {
    border-left: 4px solid #0dcaf0;
}
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\wlcome\multipleuserpage\resources\views/menus/edit-usertype-privileges.blade.php ENDPATH**/ ?>