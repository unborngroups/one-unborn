

<?php $__env->startSection('content'); ?>
<div class="container mt-4">
    <h3>Feasibility Notification Settings</h3>
    <form method="POST" action="<?php echo e(route('settings.feasibility-notifications.update')); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="mb-3">
            <label for="open_user_type" class="form-label">User Type for Open Status</label>
            <select name="open_user_type" id="open_user_type" class="form-select">
                <?php $__currentLoopData = $userTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($type->name); ?>" <?php echo e((old('open_user_type', $config['Open'] ?? '') == $type->name) ? 'selected' : ''); ?>><?php echo e($type->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="closed_user_type" class="form-label">User Type for Closed Status</label>
            <select name="closed_user_type" id="closed_user_type" class="form-select">
                <?php $__currentLoopData = $userTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($type->name); ?>" <?php echo e((old('closed_user_type', $config['Closed'] ?? '') == $type->name) ? 'selected' : ''); ?>><?php echo e($type->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\settings\feasibility-notifications.blade.php ENDPATH**/ ?>