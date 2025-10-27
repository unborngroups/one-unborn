

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <h4 class="text-primary fw-bold mb-3">Edit Feasibility</h4>

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

        
        <form action="<?php echo e(route('feasibility.update', $feasibility->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?> 
            
            <div class="row g-3">

                
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Type of Service *</label>
                    <select name="type_of_service" class="form-select" required>
                        <option value="">Select</option>
                        <option <?php echo e($feasibility->type_of_service == 'Broadband' ? 'selected' : ''); ?>>Broadband</option>
                        <option <?php echo e($feasibility->type_of_service == 'ILL' ? 'selected' : ''); ?>>ILL</option>
                        <option <?php echo e($feasibility->type_of_service == 'P2P' ? 'selected' : ''); ?>>P2P</option>
                    </select>
                </div>

                
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Client Name *</label>
                    <select name="client_id" id="client_id" class="form-select" required>
                        <option value="">Select Client</option>
                        <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($client->id); ?>" <?php echo e($client->id == $feasibility->client_id ? 'selected' : ''); ?>>
                                <?php echo e($client->client_name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Pincode *</label>
                    <input type="text" name="pincode" id="pincodeInput" value="<?php echo e($feasibility->pincode); ?>" class="form-control" required>
                </div>

                
                <div class="col-md-4">
                    <label class="form-label fw-semibold">State *</label>
                    <select name="state" id="state" class="form-select select2-tags">
                        <option value="">Select or Type State</option>
                        <option value="Karnataka" <?php echo e($feasibility->state == 'Karnataka' ? 'selected' : ''); ?>>Karnataka</option>
                        <option value="Tamil Nadu" <?php echo e($feasibility->state == 'Tamil Nadu' ? 'selected' : ''); ?>>Tamil Nadu</option>
                        <option value="Telangana" <?php echo e($feasibility->state == 'Telangana' ? 'selected' : ''); ?>>Telangana</option>
                    </select>
                </div>

                
                <div class="col-md-4">
                    <label class="form-label fw-semibold">District *</label>
                    <select name="district" id="district" class="form-select select2-tags">
                        <option value="">Select or Type District</option>
                        <option value="Salem" <?php echo e($feasibility->district == 'Salem' ? 'selected' : ''); ?>>Salem</option>
                        <option value="Dharmapuri" <?php echo e($feasibility->district == 'Dharmapuri' ? 'selected' : ''); ?>>Dharmapuri</option>
                        <option value="Erode" <?php echo e($feasibility->district == 'Erode' ? 'selected' : ''); ?>>Erode</option>
                    </select>
                </div>

                
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Area *</label>
                    <select name="area" id="area" class="form-select select2-tags">
                        <option value="">Select or Type Area</option>
                        <option value="Uthagarai" <?php echo e($feasibility->area == 'Uthagarai' ? 'selected' : ''); ?>>Uthagarai</option>
                        <option value="Harur" <?php echo e($feasibility->area == 'Harur' ? 'selected' : ''); ?>>Harur</option>
                        <option value="Kottaiyur" <?php echo e($feasibility->area == 'Kottaiyur' ? 'selected' : ''); ?>>Kottaiyur</option>
                    </select>
                </div>

                
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Address *</label>
                    <textarea name="address" class="form-control" rows="2" required><?php echo e($feasibility->address); ?></textarea>
                </div>

                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">SPOC Name *</label>
                    <input type="text" name="spoc_name" value="<?php echo e($feasibility->spoc_name); ?>" class="form-control" required>
                </div>

                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">SPOC Contact 1 *</label>
                    <input type="text" name="spoc_contact1" value="<?php echo e($feasibility->spoc_contact1); ?>" class="form-control" required>
                </div>

                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">SPOC Contact 2</label>
                    <input type="text" name="spoc_contact2" value="<?php echo e($feasibility->spoc_contact2); ?>" class="form-control">
                </div>

                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">SPOC Email</label>
                    <input type="email" name="spoc_email" value="<?php echo e($feasibility->spoc_email); ?>" class="form-control">
                </div>

                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">No. of Links *</label>
                    <select name="no_of_links" class="form-select" required>
                        <option value="">Select</option>
                        <?php for($i = 1; $i <= 4; $i++): ?>
                            <option <?php echo e($feasibility->no_of_links == $i ? 'selected' : ''); ?>><?php echo e($i); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Vendor Type *</label>
                    <select name="vendor_type" class="form-select" required>
                        <option value="">Select</option>
                        <option <?php echo e($feasibility->vendor_type == 'Same Vendor' ? 'selected' : ''); ?>>Same Vendor</option>
                        <option <?php echo e($feasibility->vendor_type == 'Different Vendor' ? 'selected' : ''); ?>>Different Vendor</option>
                    </select>
                </div>

                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Speed *</label>
                    <input type="text" name="speed" value="<?php echo e($feasibility->speed); ?>" placeholder="Mbps or Gbps" class="form-control" required>
                </div>

                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Static IP *</label>
                    <select name="static_ip" class="form-select" required>
                        <option value="">Select</option>
                        <option value="Yes" <?php echo e($feasibility->static_ip == 'Yes' ? 'selected' : ''); ?>>Yes</option>
                        <option value="No" <?php echo e($feasibility->static_ip == 'No' ? 'selected' : ''); ?>>No</option>
                    </select>
                </div>

                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Expected Delivery</label>
                    <input type="date" name="expected_delivery" value="<?php echo e($feasibility->expected_delivery); ?>" class="form-control">
                </div>

                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Expected Activation</label>
                    <input type="date" name="expected_activation" value="<?php echo e($feasibility->expected_activation); ?>" class="form-control">
                </div>

                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Hardware Required *</label>
                    <select name="hardware_required" id="hardware_required" class="form-select" required>
                        <option value="">Select</option>
                        <option value="1" <?php echo e($feasibility->hardware_required == 1 ? 'selected' : ''); ?>>Yes</option>
                        <option value="0" <?php echo e($feasibility->hardware_required == 0 ? 'selected' : ''); ?>>No</option>
                    </select>
                </div>

                
                <div class="col-md-3" id="hardware_name_div">
                    <label class="form-label fw-semibold">Hardware Model Name</label>
                    <input type="text" name="hardware_model_name" value="<?php echo e($feasibility->hardware_model_name); ?>" class="form-control">
                </div>

                
                <input type="hidden" name="status" value="<?php echo e($feasibility->status); ?>">

            </div>

            
            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update</button>
                <a href="<?php echo e(route('feasibility.index')); ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>


<script>
document.getElementById('hardware_required').addEventListener('change', function() {
    // Show or hide hardware model name field dynamically
    document.getElementById('hardware_name_div').style.display = this.value == '1' ? 'block' : 'none';
});
</script>
<style>
    #hardware_name_div{
        display: <?php echo e($feasibility->hardware_required ? 'block' : 'none'); ?>;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\new\multipleuserpage\resources\views/feasibility/edit.blade.php ENDPATH**/ ?>