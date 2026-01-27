<div class="card p-2 p-md-4 shadow border-0">
<div class="row">
    <!-- Asset ID -->
   
<div class="col-md-4 mt-3">
        <label class="form-label">Asset ID</label>
            <input type="text" name="asset_id" id="asset_id" class="form-control" value="Auto Generated" readonly required>
</div>
<!-- company -->
    <div class="col-md-4 mt-3 p-2">
        <label>Company</label>
        <select name="company_id" class="form-control" required>
            <option value="">Select Company</option>
            <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($c->id); ?>" <?php echo e(isset($asset) && $asset->company_id == $c->id ? 'selected' : ''); ?>>
                    <?php echo e($c->company_name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
<!-- Asset type -->
    <div class="col-md-4 mt-3 p-2">
        <label>Asset Type</label>
        <select name="asset_type_id" class="form-control" required>
            <option value="">Select Asset Type</option>
            <?php $__currentLoopData = $assetTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($t->id); ?>" <?php echo e(isset($asset) && $asset->asset_type_id == $t->id ? 'selected' : ''); ?>>
                    <?php echo e($t->type_name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
<!-- Make -->
    <div class="col-md-4 mt-3">
        <label>Make</label>
        <select name="make_type_id" class="form-control" required>
            <option value="">Select Make</option>
            <?php $__currentLoopData = $makes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($m->id); ?>" <?php echo e(isset($asset) && $asset->make_type_id == $m->id ? 'selected' : ''); ?>>
                    <?php echo e($m->make_name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

<!-- <div class="row mt-3"> -->
    <div class="col-md-4 mt-3">
        <label>Model</label>
        <select name="model" id="model" class="form-control" required>
        <option value="">Select Model</option>
        <?php $__currentLoopData = $models; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($model->model_name); ?>" <?php echo e(old('model', $asset->model ?? '') == $model->model_name ? 'selected' : ''); ?>>
                <?php echo e($model->model_name); ?>

            </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
        <!-- <input type="text" value="<?php echo e($asset->model ?? ''); ?>" name="model" class="form-control" required> -->
    </div>
    <div class="col-md-4 mt-3">
        <label>Brand</label>
        <input type="text" value="<?php echo e($asset->brand ?? ''); ?>" name="brand" class="form-control">
    </div>
    <div class="col-md-4 mt-3">
        <label>Serial No</label>
        <input type="text" value="<?php echo e($asset->serial_no ?? ''); ?>" name="serial_no" class="form-control" required>
    </div>
    <div class="col-md-4 mt-3">
        <label>MAC No</label>
        <input type="text" value="<?php echo e($asset->mac_no ?? ''); ?>" name="mac_no" class="form-control">
    </div>
   
    <!--  -->

    <!-- <div class="col-md-4 mt-3">

        <label class="form-label">Procured From</label>

            <select name="vendor_id" id="vendor_id" class="form-select warranty-box" style="height:48px;">

                <option value="">Select Vendor</option>

                        <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($vendor->id); ?>" <?php echo e((string) old('vendor_id', $importRow['vendor_id'] ?? '') === (string) $vendor->id ? 'selected' : ''); ?>><?php echo e($vendor->vendor_name); ?></option>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </select>

                </div> -->
    <!--  -->
                <div class="col-md-4 mt-3">
        <label>Procured From</label>
        <select name="vendor_id" class="form-control" required>
            <option value="">Select Vendor</option>
            <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($vendor->id); ?>" <?php echo e(isset($asset) && $asset->vendor_id == $vendor->id ? 'selected' : ''); ?>>
                    <?php echo e($vendor->vendor_name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="col-md-4 mt-3">
        <label>Purchase Date</label>
        <input type="date" value="<?php echo e($asset->purchase_date ?? ''); ?>" name="purchase_date" class="form-control">
    </div>
 
     <div class="col-md-4 mt-3">

                    <label class="form-label">Warranty (year)</label>

                    <select name="warranty" class="form-select select2-tags warranty-box" style="height:48px;">

                        <option value="">Select or Type City</option>

                        <option value="1 year" <?php echo e(($asset->warranty ?? '') == '1 year' ? 'selected' : ''); ?>>1 year</option>
                       <option value="2 years" <?php echo e(($asset->warranty ?? '') == '2 years' ? 'selected' : ''); ?>>2 years</option>
                       <option value="3 years" <?php echo e(($asset->warranty ?? '') == '3 years' ? 'selected' : ''); ?>>3 years</option>

                    </select>

                </div>
    <div class="col-md-4 mt-3">
        <label>PO No</label>
        <input type="text" value="<?php echo e($asset->po_no ?? ''); ?>" name="po_no" class="form-control">
    </div>
    <div class="col-md-4 mt-3">
        <label>MRP</label>
        <input type="number" value="<?php echo e($asset->mrp ?? ''); ?>" name="mrp" class="form-control">
    </div>
    <div class="col-md-4 mt-3">
        <label>Purchase Cost</label>
        <input type="number" value="<?php echo e($asset->purchase_cost ?? ''); ?>" name="purchase_cost" class="form-control">
    </div>
<!-- </div> -->
   
</div>
</div>
<!-- Asset ID -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    function generateAssetID() {
        let company = document.querySelector('select[name="company_id"] option:checked')?.text || '';
        let brand = document.querySelector('input[name="brand"]').value;
        let model = document.querySelector('input[name="model"]').value;

        if (company && brand) {
            fetch("<?php echo e(url('/assets/next-asset-id')); ?>?company=" + encodeURIComponent(company) + "&brand=" + encodeURIComponent(brand) + "&model=" + encodeURIComponent(model))
                .then(res => res.json())
                .then(data => {
                    document.getElementById('asset_id').value = data.prefix + data.no;
                });
        }
    }

    document.querySelector('select[name="company_id"]').addEventListener('change', generateAssetID);
    document.querySelector('input[name="brand"]').addEventListener('input', generateAssetID);
    document.querySelector('input[name="model"]').addEventListener('input', generateAssetID);
});
</script>

<style>
    .warranty-box {
    width: 100%;
    height: 48px; /* bigger height */
    font-size: 16px;
}


</style>
<?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\operations\asset\form.blade.php ENDPATH**/ ?>