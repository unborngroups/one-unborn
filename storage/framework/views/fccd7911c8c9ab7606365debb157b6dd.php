



<?php $__env->startSection('content'); ?>

<div class="container py-4">

    <h3 class="mb-3 text-primary">View Leave Type</h3>



    <div class="card shadow border-0 p-4">

         

        <div class="row mb-3">

            <div class="col-md-6">

                <label class="fw-bold">Leavetype:</label>

                <div><?php echo e($leavetypetable->leavetype ?? '-'); ?></div>

            </div>

             <div class="col-md-6">

                <label class="fw-bold">Shortcode:</label>

                <div><?php echo e($leavetypetable->shortcode ?? '-'); ?></div>

            </div>



        </div>



        

        <div class="mb-3">

            <label class="fw-bold">Status:</label>

            <div>

                <?php if($leavetypetable->status === 'Active'): ?>

                    <span class="badge bg-success">Active</span>

                <?php else: ?>

                    <span class="badge bg-danger">Inactive</span>

                <?php endif; ?>

            </div>

        </div>



        

        <div class="mt-3">

            <a href="<?php echo e(route('hr.leavetype.index')); ?>" class="btn btn-secondary">Back</a>

        </div>

    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\hr\leavetype\view.blade.php ENDPATH**/ ?>