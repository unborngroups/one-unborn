 





<?php $__env->startSection('content'); ?> 





<div class="container py-4"> 

    

    <h3 class="mb-3 text-primary">Add Leave Type</h3>


    <div class="card shadow border-0 p-4">
        <form method="POST" action="<?php echo e(route('hr.leavetype.store')); ?>">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
                <label for="leavetype" class="form-label">Leave Type</label>
                <input type="text" class="form-control" id="leavetype" name="leavetype" required>
            </div>
            <div class="mb-3">
                <label for="shortcode" class="form-label">Shortcode</label>
                <input type="text" class="form-control" id="shortcode" name="shortcode" required>
            </div>
            <button type="submit" class="btn btn-success">Save</button>
            <a href="<?php echo e(route('hr.leavetype.index')); ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>



</div>



<?php $__env->stopSection(); ?> 


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\hr\leavetype\create.blade.php ENDPATH**/ ?>