 

<?php $__env->startSection('content'); ?> 

<div class="container py-4"> 

    

    <h3 class="mb-3">Add Items</h3> 

    

        <?php if($errors->any()): ?>

            <div class="alert alert-danger">

                <ul class="mb-0">

                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <li><?php echo e($error); ?></li>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </ul>

            </div>

        <?php endif; ?>
    



    <div class="card shadow border-0 p-4"> 

        



        <form action="<?php echo e(route('finance.items.store')); ?>" method="POST">

            

            <?php echo csrf_field(); ?> 

            

            <div class="mb-3">

                <label>Name</label>

                <input type="text" name="item_name" class="form-control" required>

                

            </div>

            <div class="mb-3">

                <label for="">Description</label>

                <input type="text" name="item_description" class="form-control" required>

                

            </div>

            <div class="mb-3">

                <label for="">Rate</label>

                <input type="text" name="item_rate" class="form-control" required>

                

            </div>

            <div class="mb-3">

                <label for="">HSN / SAC</label>

                <input type="text" name="hsn_sac_code" class="form-control" required>

                

            </div>

            <div class="mb-3">

                <label for="">Usage Unit</label>

                <input type="text" name="usage_unit" class="form-control" required>

                

            </div>

             

            <input type="hidden" name="status" value="Active">





            <button class="btn btn-success">Save</button>

            


            <a href="<?php echo e(route('finance.items.index')); ?>" class="btn btn-secondary">Back</a>

            

        </form>

    </div>

</div>



<?php $__env->stopSection(); ?> 




<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\items\create.blade.php ENDPATH**/ ?>