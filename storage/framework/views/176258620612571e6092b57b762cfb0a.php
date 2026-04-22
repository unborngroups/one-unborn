 





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

    

    <h3 class="mb-3">Edit User</h3>

    



    <div class="card shadow border-0 p-4">

        



        <form action="<?php echo e(route('usertypetable.update', $usertypetable)); ?>" method="POST">

            

            <?= csrf_field() ?> 

            



            <?php echo method_field('PUT'); ?> 

            



            <div class="mb-3">

                <label>Name</label>

                

                <input type="text" 

                       name="name" 

                       value="<?php echo e(old('name', $usertypetable->name)); ?>" 

                       class="form-control" 

                       required>

                

            </div>

            <div class="mb-3">

                <label>Email</label>

                <input type="email" name="email" class="form-control" value="<?php echo e(old('email', $usertypetable->email)); ?>" required>

                
</div>


            <div class="mb-3">

                <label for="">Description</label>

                

                <input type="text" 

                       name="Description" 

                       value="<?php echo e(old('Description', $usertypetable->Description)); ?>" 

                       class="form-control">

                

            </div>



            <!-- <div class="mb-3">

                <label>Status</label>

                <select name="status" class="form-control">

                    

                    <option value="Active" <?php echo e($usertypetable->status == 'Active' ? 'selected' : ''); ?>>Active</option>

                    <option value="Inactive" <?php echo e($usertypetable->status == 'Inactive' ? 'selected' : ''); ?>>Inactive</option>

                </select>

                

            </div> -->

             

            <input type="hidden" name="status" value="Active">





            <button class="btn btn-warning">Update</button>

            

            

            <a href="<?php echo e(route('usertypetable.index')); ?>" class="btn btn-secondary">Back</a>

            

        </form>

    </div>

</div>



<?php $__env->stopSection(); ?> 




<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\usertypetable\edit.blade.php ENDPATH**/ ?>