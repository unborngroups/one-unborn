

<?php $__env->startSection('content'); ?>
<?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<div class="container py-4">
    <h3 class="fw-bold text-primary mb-3">Edit Email Template</h3>

    <div class="card shadow border-0 p-4">
        <form action="<?php echo e(route('emails.update', $template->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            
            <div class="mb-3">
                <label class="form-label">Company</label>
                <select name="company_id" class="form-control" required>
                    <option value="">-- Select Company --</option>
                    <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($company->id); ?>" <?php echo e($template->company_id == $company->id ? 'selected' : ''); ?>>
                            <?php echo e($company->company_name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            
            <div class="mb-3">
                <label class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control" 
                       value="<?php echo e(old('subject', $template->subject)); ?>" required>
                <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger small"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="mb-3">
                <label class="form-label">Body</label>
                <textarea name="body" class="form-control" rows="8" required><?php echo e(old('body', $template->body)); ?></textarea>
                <small class="text-muted">
                    You can use placeholders like <code><?php echo e('{name}'); ?></code>, 
                    <code><?php echo e('{company}'); ?></code>, 
                    <code><?php echo e('{email}'); ?></code>
                    <code><?php echo e('{joining_date}'); ?></code>.
                </small>
                <?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger small"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <button type="submit" class="btn btn-primary">Update Template</button>
            <a href="<?php echo e(route('emails.index')); ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/emails/edit.blade.php ENDPATH**/ ?>