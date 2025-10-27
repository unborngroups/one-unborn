

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <h4 class="text-primary fw-bold mb-3">Add Feasibility</h4>

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

         
        <form action="<?php echo e(route('feasibility.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Type of Service *</label>
                    <select name="type_of_service" class="form-select" required>
                        <option value="">Select</option>
                        <option>Broadband</option>
                        <option>ILL</option>
                        <option>P2P</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Client Name *</label>
                    <select name="client_id" id="client_id" class="form-select" required>
                        <option value="">Select Client</option>
                        <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                       <option value="<?php echo e($client->id); ?>"><?php echo e($client->client_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>


                <div class="col-md-4">
                    <label class="form-label fw-semibold">Pincode *</label>
                    <input type="text" name="pincode" id="pincodeInput" class="form-control" required>
           <!-- <button type="button" id="pincodeVerifyBtn" class="btn btn-primary">Verify</button> -->
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">State *</label>
                    <select name="state" id="state" class="form-select select2-tags">
                        <option value="">Select or Type State</option>
                        <option value="Karnataka">Karnataka</option>
                        <option value="Tamil Nadu">Tamil Nadu</option>
                        <option value="Telangana">Telangana</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">District *</label>
                   <select name="district" id="district" class="form-select select2-tags">
                        <option value="">Select or Type District</option>
                        <option value="Salem">Salem</option>
                        <option value="Dharmapuri">Dharmapuri</option>
                        <option value="Erode">Erode</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Area *</label>
                    <select name="area" id="area" class="form-select select2-tags">
                        <option value="">Select or Type Area</option>
                        <option value="Uthagarai">Uthagarai</option>
                        <option value="Harur">Harur</option>
                        <option value="Kottaiyur">Kottaiyur</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Address *</label>
                    <textarea name="address" class="form-control" rows="2" required></textarea>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">SPOC Name *</label>
                    <input type="text" name="spoc_name" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">SPOC Contact 1 *</label>
                    <input type="text" name="spoc_contact1" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">SPOC Contact 2</label>
                    <input type="text" name="spoc_contact2" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">SPOC Email</label>
                    <input type="email" name="spoc_email" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">No. of Links *</label>
                    <select name="no_of_links" class="form-select" required>
                        <option value="">Select</option>
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Vendor Type *</label>
                    <select name="vendor_type" class="form-select" required>
                        <option value="">Select</option>
                        <option>Same Vendor</option>
                        <option>Different Vendor</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Speed *</label>
                    <input type="text" name="speed" placeholder="Mbps or Gbps" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Static IP *</label>
                    <select name="static_ip" class="form-select" required>
                        <option value="">Select</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>


                <div class="col-md-3">
                    <label class="form-label fw-semibold">Expected Delivery</label>
                    <input type="date" name="expected_delivery" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Expected Activation</label>
                    <input type="date" name="expected_activation" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Hardware Required *</label>
                    <select name="hardware_required" id="hardware_required" class="form-select" required>
                        <option value="">Select</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="col-md-3" id="hardware_name_div" style="display:none;">
                    <label class="form-label fw-semibold">Hardware Model Name</label>
                    <input type="text" name="hardware_model_name" class="form-control">
                </div>

                    
            <input type="hidden" name="status" value="Active">


            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
                <a href="<?php echo e(route('feasibility.index')); ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>


<script>
document.getElementById('hardware_required').addEventListener('change', function() {
    document.getElementById('hardware_name_div').style.display = this.value == '1' ? 'block' : 'none';
});

// ✅ Auto-fill Client Details when Client Selected
document.getElementById('client_id').addEventListener('change', function() {
    const clientId = this.value;
    if (!clientId) {
        // Clear all fields when no client selected
        document.querySelector('input[name="pincode"]').value = '';
        document.querySelector('input[name="state"]').value = '';
        document.querySelector('input[name="district"]').value = '';
        document.querySelector('input[name="area"]').value = '';
        document.querySelector('textarea[name="address"]').value = '';
        document.querySelector('input[name="spoc_name"]').value = '';
        document.querySelector('input[name="spoc_contact1"]').value = '';
        document.querySelector('input[name="spoc_contact2"]').value = '';
        document.querySelector('input[name="spoc_email"]').value = '';
        return;
    }

    console.log('Fetching client details for ID:', clientId);

    fetch(`/get-client-details/${clientId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('✅ Client details received:', data);
            
            // Auto-fill all available client data
            document.querySelector('input[name="pincode"]').value = data.pincode || '';
            document.querySelector('select[name="state"]').value = data.state || '';
            document.querySelector('select[name="district"]').value = data.district || '';
            document.querySelector('select[name="area"]').value = data.area || '';
            document.querySelector('textarea[name="address"]').value = data.address1 || data.address || '';
            
            // SPOC details (might be stored differently in client table)
            document.querySelector('input[name="spoc_name"]').value = data.billing_spoc_name || data.client_name || '';
            document.querySelector('input[name="spoc_contact1"]').value = data.billing_spoc_contact || data.contact_no || '';
            document.querySelector('input[name="spoc_contact2"]').value = data.alternate_contact || '';
            document.querySelector('input[name="spoc_email"]').value = data.billing_spoc_email || data.email || '';

            // 🔄 Trigger pincode auto-lookup after client filled (if pincode exists)
            if (data.pincode) {
                setTimeout(triggerPincodeLookup, 500); // Small delay to ensure field is filled
            }
        })
        .catch(err => {
            console.error('❌ Error fetching client details:', err);
            alert('Error loading client details: ' + err.message);
        });
});

// ✅ Auto pincode lookup using Laravel API
function triggerPincodeLookup() {
    const pincode = document.getElementById('pincodeInput').value.trim();
    if (!/^\d{6}$/.test(pincode)) return;

    const state = document.querySelector('input[name="state"]');
    const district = document.querySelector('input[name="district"]');
    const area = document.querySelector('input[name="area"]');

    state.value = district.value = area.value = "Loading...";

    fetch('/api/pincode/lookup', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ pincode: pincode })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            throw new Error(data.error);
        }
        state.value = data.state || '';
        district.value = data.district || '';
        area.value = data.post_office || '';
        
        console.log('✅ Pincode lookup successful:', data);
    })
    .catch(err => {
        console.error('Pincode lookup error:', err);
        alert('❌ ' + (err.message || 'Invalid or unreachable pincode'));
        state.value = district.value = area.value = '';
    });
}

// ✅ Trigger on typing or blur
document.getElementById('pincodeInput').addEventListener('change', triggerPincodeLookup);
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\new\multipleuserpage\resources\views/feasibility/create.blade.php ENDPATH**/ ?>