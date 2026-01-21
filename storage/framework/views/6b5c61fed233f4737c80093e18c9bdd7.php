

<?php $__env->startSection('content'); ?>

<div class="container py-4 d-flex flex-column align-items-center justify-content-center" style="min-height: 80vh;">

    <h3 class="mb-3 text-center">Add Renewal</h3>

    <div class="card shadow border-0 p-4 w-100" style="max-width: 800px;">

        <form action="<?php echo e(route('operations.renewals.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>

            
            <div class="mb-3">
                <label for="deliverable_id" class="form-label">Deliverable (Circuit ID)</label>
                <select name="deliverable_id" id="deliverable_id" class="form-select select2-tags" required>
                    <option value="">Select Circuit ID</option>
                    <?php $__currentLoopData = $deliverables_plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($d->deliverable_id); ?>">
                            <?php echo e($d->circuit_id); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            
            <div class="mb-3">
                <label for="renewalDate" class="form-label">Date of Renewal</label>
                <input type="date" name="date_of_renewal" id="renewalDate" class="form-control" required>
            </div>

            
            <div class="mb-3">
                <label for="months" class="form-label">Renewal Months</label>
                <input type="number" name="renewal_months" id="months" min="1" max="36" class="form-control" placeholder="Enter months" required>
            </div>

            
            <div class="mb-3">
                <label for="expiry" class="form-label">New Expiry Date</label>
                <input type="text" id="expiry" name="new_expiry_date" class="form-control" readonly placeholder="Auto-calculated">
            </div>

            
            <div class="d-flex justify-content-center gap-2">
                <button type="submit" class="btn btn-success">Save</button>
                <a href="<?php echo e(route('operations.renewals.index')); ?>" class="btn btn-secondary">Back</a>
            </div>

        </form>
    </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', function () {
    const renewalInput = document.getElementById('renewalDate');
    const monthsInput  = document.getElementById('months');
    const expiryInput  = document.getElementById('expiry');

    function calcExpiry() {
        if (!renewalInput.value || !monthsInput.value) {
            expiryInput.value = '';
            return;
        }

        const months = Number(monthsInput.value);
        if (!months || months <= 0) return;

        const baseDate = new Date(renewalInput.value + 'T00:00:00');
        if (isNaN(baseDate.getTime())) {
            expiryInput.value = '';
            return;
        }

        const expiryDate = new Date(baseDate);
        expiryDate.setMonth(expiryDate.getMonth() + months);
        expiryDate.setDate(expiryDate.getDate() - 1);

        expiryInput.value = expiryDate.toISOString().split('T')[0];
    }

    renewalInput.addEventListener('change', calcExpiry);
    monthsInput.addEventListener('input', calcExpiry);
    monthsInput.addEventListener('change', calcExpiry);
    // Call once on load in case values are pre-filled
    calcExpiry();
});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\operations\renewals\create.blade.php ENDPATH**/ ?>