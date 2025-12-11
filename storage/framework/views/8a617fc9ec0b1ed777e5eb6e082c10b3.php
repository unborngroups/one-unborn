

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h3 class="mb-3 text-primary">Add Asset Type</h3>
    <div class="card p-4 shadow">
        <form action="<?php echo e(route('assetmaster.asset_type.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>

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
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\assetmaster\asset_type\create.blade.php ENDPATH**/ ?>