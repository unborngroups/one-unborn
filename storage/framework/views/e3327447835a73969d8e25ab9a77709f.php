



<?php $__env->startSection('content'); ?>

<div class="container py-4">

    <h3 class="fw-bold text-primary mb-4">

        Manage Menu Privileges — <span class="text-dark"><?php echo e($user->name); ?></span>

    </h3>



    <div class="card shadow-lg border-0 rounded-4 p-4">

        <form id="privilegeForm" action="<?php echo e(route('menus.updatePrivileges', $user->id)); ?>" method="POST">

            <?php echo csrf_field(); ?>

            

            <div class="form-check mb-4">

                <input type="checkbox" class="form-check-input" id="grantAllGlobal">

                <label class="form-check-label fw-semibold" for="grantAllGlobal">Grant All Permissions</label>

            </div>

            

            <div class="table-responsive privilege-table">
                <table class="table table-bordered align-middle text-center shadow-sm mb-0">
                    <thead class="table-primary table-custom">
                        <tr>
                            <th class="select-col">Select</th>
                            <th class="module-col">Module</th>
                            <th class="section-col">Section</th>
                            <!-- <th class="menu-col">Menu</th> -->
                            <th>Menu</th>
                            <th>View</th>
                            <th>Add</th>
                            <th>Edit</th>
                            <th>Delete</th>
                            <th>All</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $groupedMenus = $menus->groupBy('module_name'); ?>
                        <?php $__currentLoopData = $groupedMenus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $moduleName => $moduleMenus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $moduleSlug = \Illuminate\Support\Str::slug($moduleName ?: 'module-'.$loop->index, '-'); ?>
                            <?php $__currentLoopData = $moduleMenus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $priv = $userPrivileges[$menu->id] ?? null; ?>
                                <tr data-module="<?php echo e($moduleSlug); ?>">
                                    <td class="text-center select-cell">
                                        <?php if($loop->first): ?>
                                            <input type="checkbox" class="module-checkbox" data-module="<?php echo e($moduleSlug); ?>">
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-start fw-semibold module-cell">
                                        <?php if($loop->first): ?>
                                            <?php echo e(ucfirst($moduleName)); ?>

                                        <?php endif; ?>
                                    </td>
                                    <!-- <td class="text-start section-cell"></td> -->
                                    <td class="text-start menu-cell ps-3"><?php echo e(ucfirst($menu->name)); ?></td>
                                    <td>
                                        <input type="checkbox" name="privileges[<?php echo e($menu->id); ?>][can_menu]" value="1"
                                               class="perm-checkbox"
                                               <?php echo e($priv && $priv->can_menu ? 'checked' : ''); ?>>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="privileges[<?php echo e($menu->id); ?>][can_view]" value="1"
                                               class="perm-checkbox"
                                               <?php echo e($priv && $priv->can_view ? 'checked' : ''); ?>>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="privileges[<?php echo e($menu->id); ?>][can_add]" value="1"
                                               class="perm-checkbox"
                                               <?php echo e($priv && $priv->can_add ? 'checked' : ''); ?>>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="privileges[<?php echo e($menu->id); ?>][can_edit]" value="1"
                                               class="perm-checkbox"
                                               <?php echo e($priv && $priv->can_edit ? 'checked' : ''); ?>>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="privileges[<?php echo e($menu->id); ?>][can_delete]" value="1"
                                               class="perm-checkbox"
                                               <?php echo e($priv && $priv->can_delete ? 'checked' : ''); ?>>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="grant-row-all form-check-input"
                                               title="Grant all permissions for this menu">
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>



            

            <div class="text-end mt-4">

                <button type="submit" class="btn btn-success px-5 py-2 rounded-3 shadow-sm me-2">

                    <i class="bi bi-check-circle me-1"></i> Save

                </button>

                <a href="<?php echo e(route('users.index')); ?>" class="btn btn-secondary px-5 py-2 rounded-3">

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
        document.querySelectorAll('tbody input[type="checkbox"]:not(:disabled)').forEach(cb => cb.checked = checked);
        document.querySelectorAll('.module-checkbox').forEach(cb => {
            cb.checked = checked;
            const moduleKey = cb.dataset.module;
            document.querySelectorAll(`#privilegeForm tr[data-module="${moduleKey}"] .perm-checkbox`).forEach(rowCb => rowCb.checked = checked);
        });
    });

    document.querySelectorAll('.grant-row-all').forEach(rowCb => {
        rowCb.addEventListener('change', function () {
            const row = this.closest('tr');
            const checked = this.checked;
            row.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = checked);
        });
    });

    document.querySelectorAll('.module-checkbox').forEach(cb => {
        cb.addEventListener('change', function () {
            const moduleKey = this.dataset.module;
            const checked = this.checked;
            document.querySelectorAll(`tr[data-module="${moduleKey}"] .perm-checkbox`).forEach(rowCb => rowCb.checked = checked);
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

.privilege-table {
    border: 1px solid #dfe4eb;
    border-radius: 10px;
    background: #fff;
    padding: 0.35rem;
}
.privilege-table .table {
    margin-bottom: 0;
}
.table-custom th {
    background: linear-gradient(135deg, #0d6efd, #0a52c4) !important;
    color: #fff;
    border-color: #0a58ca;
    font-size: 0.9rem;
    letter-spacing: 0.03rem;
    text-transform: uppercase;
}
.table tbody tr:nth-of-type(odd) {
    background: #f6fafd;
}
.table tbody tr:nth-of-type(even) {
    background: #ffffff;
}
.table td,
.table th {
    border-color: #cce0ff;
    vertical-align: middle;
    padding: 0.55rem;
}
.select-col {
    width: 5%;
}
.module-col {
    width: 15%;
}
.section-col {
    width: 15%;
}
.menu-col {
    width: 20%;
    text-align: left;
}
.module-cell,
.section-cell,
.menu-cell {
    font-size: 0.95rem;
    color: #1c1f26;
}
.text-center input[type="checkbox"] {
    cursor: pointer;
    width: 1.1rem;
    height: 1.1rem;
}
.text-center input[type="checkbox"]:focus-visible {
    outline: none;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.45);
}
.perm-checkbox {
    accent-color: #0d6efd;
}

</style>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\menus\editprivileges.blade.php ENDPATH**/ ?>