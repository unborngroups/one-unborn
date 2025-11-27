



<?php $__env->startSection('content'); ?>

<div class="container py-4">

    <h3 class="mb-3 text-primary">View User Type</h3>



    <div class="card shadow border-0 p-4">

         

        <div class="row mb-3">

            <div class="col-md-6">

                <label class="fw-bold">Name:</label>

                <div><?php echo e($usertypetable->name); ?></div>

            </div>

             <div class="col-md-6">

                <label class="fw-bold">Email:</label>

                <div><?php echo e($usertypetable->email ?? '-'); ?></div>

            </div>



            <div class="col-md-6">

                <label class="fw-bold">Description:</label>

                <div><?php echo e($usertypetable->Description ?? '-'); ?></div>

            </div>

        </div>



        

        <div class="mb-3">

            <label class="fw-bold">Status:</label>

            <div>

                <?php if($usertypetable->status === 'Active'): ?>

                    <span class="badge bg-success">Active</span>

                <?php else: ?>

                    <span class="badge bg-danger">Inactive</span>

                <?php endif; ?>

            </div>

        </div>



        

        <div class="mt-3">

            <a href="<?php echo e(route('usertypetable.index')); ?>" class="btn btn-secondary">Back</a>

        </div>

    </div>

</div>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\wlcome\multipleuserpage\resources\views\usertypetable\view.blade.php ENDPATH**/ ?>