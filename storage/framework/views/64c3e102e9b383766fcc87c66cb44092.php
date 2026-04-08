

<?php $__env->startSection('title', ucfirst($type) . ' Contact'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold"><?php echo e(ucfirst($type)); ?> Contact</h4>
        <?php if($permissions->can_add): ?>
            <a href="<?php echo e(route('contacts.create', $type)); ?>" class="btn btn-success">
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

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="row mb-4">
                <div class="col-md-6">
                    <form method="GET" action="<?php echo e(route('contacts.' . $type . '.index')); ?>" class="input-group">
                        <input type="text" name="search" class="form-control form-control-lg" 
                               placeholder="Search by name or contact number..." 
                               value="<?php echo e(request('search')); ?>">
                        <button type="submit" class="btn btn-primary" style="border-radius: 0 0.375rem 0.375rem 0;">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </form>
                </div>
                <div class="col-md-6 text-end">
                    <?php if(request('search')): ?>
                        <span class="badge bg-light text-dark me-2">
                            <i class="bi bi-funnel"></i> Filtered
                        </span>
                        <a href="<?php echo e(route('contacts.' . $type . '.index')); ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-circle"></i> Clear Search
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr style="background: linear-gradient(135deg, #0d4d6d 0%, #1a5f7a 100%); color: white;">
                            <th style="border: none; padding: 12px 15px;">S.No</th>
                            <th style="border: none; padding: 12px 15px;">Name</th>
                            <th style="border: none; padding: 12px 15px;">Area</th>
                            <th style="border: none; padding: 12px 15px;">State</th>
                            <th style="border: none; padding: 12px 15px;">Contact1</th>
                            <th style="border: none; padding: 12px 15px;">Contact2</th>
                            <th style="border: none; padding: 12px 15px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="border-bottom">
                                <td class="fw-semibold text-secondary"><?php echo e($contacts->firstItem() + $loop->index); ?></td>
                                <td class="fw-medium"><?php echo e($contact->name); ?></td>
                                <td><?php echo e($contact->area ?: '-'); ?></td>
                                <td><?php echo e($contact->state ?: '-'); ?></td>
                                <td class="fw-medium"><?php echo e($contact->contact1); ?></td>
                                <td><?php echo e($contact->contact2 ?: '-'); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <?php if($permissions->can_edit): ?>
                                            <a href="<?php echo e(route('contacts.edit', [$type, $contact->id])); ?>" 
                                               class="btn btn-warning" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if($permissions->can_view): ?>
                                            <a href="<?php echo e(route('contacts.show', [$type, $contact->id])); ?>" 
                                               class="btn btn-info" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if($permissions->can_delete): ?>
                                            <form action="<?php echo e(route('contacts.destroy', [$type, $contact->id])); ?>" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this contact?')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-danger" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="7">
                                    <form action="<?php echo e(route('contacts.toggle-status', [$type, $contact->id])); ?>" 
                                          method="POST" class="d-inline w-100">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="btn btn-sm <?php echo e(strtolower($contact->status) === 'active' ? 'btn-success' : 'btn-secondary'); ?> float-end"
                                                style="margin-top: -42px; margin-right: 10px;">
                                            <?php echo e(strtolower($contact->status) === 'active' ? 'Active' : 'Inactive'); ?>

                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                        <p class="mt-2">No contacts found.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if($contacts->hasPages()): ?>
        <div class="mt-4 d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Showing <?php echo e($contacts->firstItem() ?? 0); ?> to <?php echo e($contacts->lastItem() ?? 0); ?> of <?php echo e($contacts->total()); ?> entries
            </div>
            <div>
                <?php echo e($contacts->links()); ?>

            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\contact\index.blade.php ENDPATH**/ ?>