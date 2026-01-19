



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

                <textarea name="body" id="email_body" class="form-control" rows="8" required><?php echo e(old('body', $template->body)); ?></textarea>

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



            

            <div class="mb-3">

                <label class="form-label">Event Key</label>

                <input type="text" name="event_key" class="form-control" 

                       value="<?php echo e(old('event_key', $template->event_key)); ?>" required>

                <small class="text-muted">

                    Unique key for this event (e.g. user_registered, po_approved)

                </small>

                <?php $__errorArgs = ['event_key'];
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


<?php $__env->startSection('scripts'); ?>
<script>
$(function () {
    $('#email_body').summernote({
        height: 250,
        toolbar: [
            ['font', ['fontname', 'fontsize']],
            ['style', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
            ['fontstyle', ['superscript', 'subscript']],
            ['color', ['color', 'backcolor']],
            ['para', ['ul', 'ol', 'paragraph', 'lineheight']],
            ['insert', ['picture', 'link', 'table', 'hr']],
            ['view', ['fullscreen', 'codeview', 'undo', 'redo']]
        ],
        fontNames: ['Source Sans Pro', 'Arial', 'Times New Roman'],
        placeholder: 'Draft your email body here…'
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\emails\edit.blade.php ENDPATH**/ ?>