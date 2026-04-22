 





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

    

    <h3 class="mb-3">Edit Leavetype</h3>

    



    <div class="card shadow border-0 p-4">

        



        <form action="<?php echo e(route('hr.leavetype.update', $leavetypetable->id)); ?>" method="POST">

            

            <?= csrf_field() ?> 

            



            <?php echo method_field('PUT'); ?> 

            



            <div class="mb-3">

                <label>Leavetype</label>

                

                <input type="text" 

                       name="leavetype" 

                       value="<?php echo e(old('leavetype', $leavetypetable->leavetype)); ?>" 

                       class="form-control" 

                       required>

                

            </div>

            <div class="mb-3">

                <label>Shortcode</label>

                <input type="text" name="shortcode" class="form-control" value="<?php echo e(old('shortcode', $leavetypetable->shortcode)); ?>" required>

                
</div>


           


            <!-- <div class="mb-3">

                <label>Status</label>

                <select name="status" class="form-control">

                    

                    <option value="Active" <?php echo e($leavetypetable->status == 'Active' ? 'selected' : ''); ?>>Active</option>

                    <option value="Inactive" <?php echo e($leavetypetable->status == 'Inactive' ? 'selected' : ''); ?>>Inactive</option>

                </select>

                

            </div> -->

             

            <input type="hidden" name="status" value="Active">





            <button class="btn btn-warning">Update</button>

            

            

            <a href="<?php echo e(route('hr.leavetype.index')); ?>" class="btn btn-secondary">Back</a>

            

        </form>

    </div>

</div>



<?php $__env->stopSection(); ?> 


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\hr\leavetype\edit.blade.php ENDPATH**/ ?>