

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h3 class="mb-3 text-primary">Add Asset Type</h3>
    <div class="card p-4 shadow">
        <form action="<?php echo e(route('assetmaster.asset_type.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>

            <div class="mb-3">
                <label class="form-label">Company</label>
                <select name="company_id" class="form-control">
                    <option value="">Select company</option>
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
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
<!-- 
            <div class="mb-3">
                <label class="form-label">Asset Type Name</label>
                <input type="text" name="type_name" class="form-control" value="<?php echo e(old('type_name')); ?>">
                <?php $__errorArgs = ['type_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div> -->
            <!-- select Asset -->
              <div class="col-md-12">

                    <label class="form-label">Asset Type</label>

                    <select name="type_name" class="form-select select2-tags">

                        <option value="">Select or Type City</option>

                        <option>Switch</option>

                        <option>Router</option>

                        <option>SD WAN</option>

                    </select>

                </div>


<div class="p-3">
            <button class="btn btn-primary">Save Asset Type</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/assetmaster/asset_type/create.blade.php ENDPATH**/ ?>