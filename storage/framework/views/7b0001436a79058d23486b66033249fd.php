



<?php $__env->startSection('content'); ?>

<div class="container py-4">

    <h3 class="mb-3 text-primary">Add User</h3>

    <div class="card shadow border-0 p-4">



        

        <?php if($errors->any()): ?>

            <div class="alert alert-danger">

                <ul class="mb-0">

                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <li><?php echo e($error); ?></li>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </ul>

            </div>

        <?php endif; ?>



        <form action="<?php echo e(route('users.store')); ?>" method="POST">

            <?php echo csrf_field(); ?>

<div class="row">

            

            <div class="col-md-6 mb-3">

                <label class="form-label">Name</label>

                <input type="text" name="name" class="form-control" value="<?php echo e(old('name')); ?>" required>

            </div>



            

            <div class="col-md-6 mb-3">

                <label class="form-label">User Type</label>

                <select name="user_type_id" class="form-select" required>

                    <option value="">Select User Type</option>

                    <?php $__currentLoopData = $userTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <option value="<?php echo e($type->id); ?>" <?php echo e(old('user_type_id') == $type->id ? 'selected' : ''); ?>>

                            <?php echo e(ucfirst($type->name)); ?>


                        </option>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </select>

                <?php $__errorArgs = ['user_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>

                    <span class="text-danger"><?php echo e($message); ?></span>

                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            </div>



            

            <!-- <div class="mb-3">

                <label class="form-label">Email</label>

                <input type="email" name="email" class="form-control" value="<?php echo e(old('email')); ?>" required>

            </div> -->

            <div class="col-md-6 mb-3">

    <label>Official Email</label>

    <input type="email" name="official_email" value="<?php echo e(old('official_email', $user->official_email ?? '')); ?>" class="form-control" required>

</div>

<!-- User want to enter the OTP requirement preference -->
<div class="col-md-12 mb-3">
    <div class="form-check">
        <input type="checkbox" name="require_otp_always" value="1" class="form-check-input" id="requireOtpAlwaysCheckbox" <?php echo e(old('require_otp_always', $user->require_otp_always ?? false) ? 'checked' : ''); ?>>
        <label for="requireOtpAlwaysCheckbox" class="form-check-label">
            Require OTP on every login
        </label>
    </div>
</div>



<div class="col-md-6 mb-3">

    <label>Personal Email</label>

    <input type="email" name="personal_email" value="<?php echo e(old('personal_email', $user->personal_email ?? '')); ?>" class="form-control">

</div>





            

            <div class="col-md-6 mb-3">

                <label class="form-label">Mobile</label>

                <input type="text" name="mobile" class="form-control" value="<?php echo e(old('mobile')); ?>">

            </div>



            

<div class="col-md-6 mb-3">

    <label class="form-label">Company</label>

    <select id="company_id" name="companies[]" class="form-select" multiple required>

        <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <option value="<?php echo e($company->id); ?>" 

                <?php echo e(in_array($company->id, old('companies', [])) ? 'selected' : ''); ?>>

                <?php echo e($company->company_name); ?>


            </option>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </select>

    <small class="text-muted">Hold <b>Ctrl</b> (Windows) or <b>Cmd</b> (Mac) to select multiple.</small>

</div>



            

            <div class="col-md-6 mb-3">

                <label class="form-label">Date of Birth</label>

                <input type="date" name="Date_of_Birth" class="form-control" placeholder="select DOB" value="<?php echo e(old('Date_of_Birth')); ?>" required>

            </div>



            

            <div class="col-md-6 mb-3">

                <label class="form-label">Date of Joining</label>

                <input type="date" name="Date_of_Joining" class="form-control" placeholder="select DOJ" value="<?php echo e(old('Date_of_Joining')); ?>" required>

            </div>
</div>


            

            <!-- <div class="mb-3">

                <label class="form-label">Status</label>

                <select name="status" class="form-select">

                    <option value="Active" <?php echo e(old('status', 'Active') == 'Active' ? 'selected' : ''); ?>>Active</option>

                    <option value="Inactive" <?php echo e(old('status') == 'Inactive' ? 'selected' : ''); ?>>Inactive</option>

                </select>

            </div> -->

             

            <input type="hidden" name="status" value="Active">





            

            <div class="d-flex justify-content-between">

                <button type="submit" class="btn btn-success">

                    <i class="bi bi-save"></i> Save

                </button>

                <a href="<?php echo e(route('users.index')); ?>" class="btn btn-secondary">

                    <i class="bi bi-arrow-left"></i> Back

                </a>

            </div>

        </form>

    </div>

</div>





<script>

document.getElementById('company_id').addEventListener('change', function () {

    let companyId = this.value;

    let templateDropdown = document.getElementById('email_template_id');

    templateDropdown.innerHTML = '<option value="">-- Select Template --</option>';



    if (companyId) {

        fetch('/companies/' + companyId + '/templates')

            .then(response => response.json())

            .then(data => {

                if (data.length > 0) {

                    data.forEach(function (template) {

                        templateDropdown.innerHTML += 

                            `<option value="${template.id}">${template.subject}</option>`;

                    });

                }

            })

            .catch(error => console.error('Error fetching templates:', error));

    }

});

</script>
<style>
    label{
        font-weight: 600;
    }
</style>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\users\create.blade.php ENDPATH**/ ?>