 





<?php $__env->startSection('content'); ?> 





<div class="container py-4"> 

    

    <h3 class="mb-3">Add User</h3> 

    



    <div class="card shadow border-0 p-4"> 

        



        <form action="<?php echo e(route('usertypetable.store')); ?>" method="POST">

            

            <?php echo csrf_field(); ?> 

            



            <div class="mb-3">

                <label>Name</label>

                <input type="text" name="name" class="form-control" required>

                

            </div>

            <div class="mb-3">

                <label>Email</label>

                <input type="email" name="email" class="form-control" required>

                
            </div>





            <div class="mb-3">

                <label for="">Description</label>

                <input type="text" name="Description" class="form-control">

                

            </div>

           

            <!-- <div class="mb-3">

                <label>Status</label>

                <select name="status" class="form-control">

                    <option value="Active">Active</option>

                    <option value="Inactive">Inactive</option>

                </select>

                

            </div> -->

             

            <input type="hidden" name="status" value="Active">





            <button class="btn btn-success">Save</button>

            



            <a href="<?php echo e(route('usertypetable.index')); ?>" class="btn btn-secondary">Back</a>

            

        </form>

    </div>

</div>



<?php $__env->stopSection(); ?> 




<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\usertypetable\create.blade.php ENDPATH**/ ?>