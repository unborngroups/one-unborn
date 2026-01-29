



<?php $__env->startSection('content'); ?>

<div class="container py-4">

    <h3 class="fw-bold text-primary mb-3">Create Email Template</h3>



    <div class="card shadow border-0 p-4">

        <form action="<?php echo e(route('emails.store')); ?>" method="POST">

            <?php echo csrf_field(); ?>



            

            <div class="mb-3">

                <label class="form-label">Company</label>

                <select name="company_id" class="form-control" required>

                    <option value="">-- Select Company --</option>

                    <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <option value="<?php echo e($company->id); ?>" <?php echo e(old('company_id') == $company->id ? 'selected' : ''); ?>>

                            <?php echo e($company->company_name); ?>


                        </option>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </select>

                <?php $__errorArgs = ['company_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger small"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            </div>



            

            <div class="mb-3">

                <label class="form-label">Subject</label>

                <input type="text" name="subject" class="form-control" 

                       value="<?php echo e(old('subject')); ?>" required>

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

                <textarea name="body" id="email_body" class="form-control" rows="8" required><?php echo e(old('body')); ?></textarea>

                <small class="text-muted">

    You can use placeholders like 

    <code>{{name}}</code>, 

    <code>{{company_name}}</code>,

    <code>{{email}}</code>, 

    <code>{{joining_date}}</code>.

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
                <input type="text" name="event_key" class="form-control" value="<?php echo e(old('event_key')); ?>" required>
                <small class="text-muted">Unique key for this event (e.g. user_registered, po_approved)</small>
                <?php $__errorArgs = ['event_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger small"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            

            <button type="submit" class="btn btn-success">Save Template</button>

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
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/emails/create.blade.php ENDPATH**/ ?>