

<?php $__env->startSection('title', ucfirst($type) . ' Contact'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><?php echo e(ucfirst($type)); ?> Contact</h4>
        <?php if($permissions->can_add): ?>
            <a href="<?php echo e(route('contacts.create', $type)); ?>" class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle"></i> Create
            </a>
        <?php endif; ?>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
        <form method="GET" action="<?php echo e(route('contacts.' . $type . '.index')); ?>" class="d-flex align-items-center gap-2">
            <label class="mb-0">Show</label>
            <select name="per_page" class="form-select form-select-sm" style="width:80px;" onchange="this.form.submit()">
                <?php $__currentLoopData = [10, 25, 50, 100]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($size); ?>" <?php echo e((int) request('per_page', 10) === $size ? 'selected' : ''); ?>><?php echo e($size); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <label class="mb-0">entries</label>
            <input type="hidden" name="search" value="<?php echo e(request('search')); ?>">
        </form>

        <form method="GET" action="<?php echo e(route('contacts.' . $type . '.index')); ?>" class="d-flex align-items-center gap-2">
            <label class="mb-0">Search:</label>
            <input type="text" name="search" class="form-control form-control-sm" style="width:240px;" value="<?php echo e(request('search')); ?>" placeholder="Search...">
            <input type="hidden" name="per_page" value="<?php echo e((int) request('per_page', 10)); ?>">
            <button type="submit" class="btn btn-primary btn-sm">Go</button>
            <?php if(request('search')): ?>
                <a href="<?php echo e(route('contacts.' . $type . '.index', ['per_page' => (int) request('per_page', 10)])); ?>" class="btn btn-secondary btn-sm">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-dark-primary">
                    <tr>
                        <th>S.No</th>
                        <th>Name</th>
                        <th>Area</th>
                        <th>State</th>
                        <th>Contact1</th>
                        <th>Contact2</th>
                        <th width="260">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($contacts->firstItem() + $loop->index); ?></td>
                            <td><?php echo e($contact->name); ?></td>
                            <td><?php echo e($contact->area ?: '-'); ?></td>
                            <td><?php echo e($contact->state ?: '-'); ?></td>
                            <td><?php echo e($contact->contact1); ?></td>
                            <td><?php echo e($contact->contact2 ?: '-'); ?></td>
                            <td>
                                <?php if($permissions->can_edit): ?>
                                    <a href="<?php echo e(route('contacts.edit', [$type, $contact->id])); ?>" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                <?php endif; ?>

                                <?php if($permissions->can_view): ?>
                                    <a href="<?php echo e(route('contacts.show', [$type, $contact->id])); ?>" class="btn btn-info btn-sm">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                <?php endif; ?>

                                <?php if($permissions->can_delete): ?>
                                    <form action="<?php echo e(route('contacts.destroy', [$type, $contact->id])); ?>" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this contact?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if($permissions->can_edit): ?>
                                    <form action="<?php echo e(route('contacts.toggle-status', [$type, $contact->id])); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="btn btn-sm <?php echo e(strtolower($contact->status) === 'active' ? 'btn-success' : 'btn-secondary'); ?>">
                                            <?php echo e(strtolower($contact->status) === 'active' ? 'Active' : 'Inactive'); ?>

                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No contacts found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            Showing <?php echo e($contacts->firstItem() ?? 0); ?> to <?php echo e($contacts->lastItem() ?? 0); ?> of <?php echo e($contacts->total()); ?> entries
        </div>
        <div>
            <?php echo e($contacts->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\contact\index.blade.php ENDPATH**/ ?>