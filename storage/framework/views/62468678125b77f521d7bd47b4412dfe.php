<div class="row">
    <div class="col-md-4">
        <label>Company</label>
        <select name="company_id" class="form-control" required>
            <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($c->id); ?>" <?php echo e(isset($asset) && $asset->company_id == $c->id ? 'selected' : ''); ?>>
                    <?php echo e($c->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="col-md-4">
        <label>Asset Type</label>
        <select name="asset_type_id" class="form-control" required>
            <?php $__currentLoopData = $assetTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($t->id); ?>" <?php echo e(isset($asset) && $asset->asset_type_id == $t->id ? 'selected' : ''); ?>>
                    <?php echo e($t->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="col-md-4">
        <label>Make</label>
        <select name="make_type_id" class="form-control" required>
            <?php $__currentLoopData = $makes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($m->id); ?>" <?php echo e(isset($asset) && $asset->make_type_id == $m->id ? 'selected' : ''); ?>>
                    <?php echo e($m->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-4"><label>Model</label><input type="text" value="<?php echo e($asset->model ?? ''); ?>" name="model" class="form-control" required></div>
    <div class="col-md-4"><label>Brand</label><input type="text" value="<?php echo e($asset->brand ?? ''); ?>" name="brand" class="form-control"></div>
    <div class="col-md-4"><label>Serial No</label><input type="text" value="<?php echo e($asset->serial_no ?? ''); ?>" name="serial_no" class="form-control" required></div>
    <div class="col-md-4 mt-3"><label>MAC No</label><input type="text" value="<?php echo e($asset->mac_no ?? ''); ?>" name="mac_no" class="form-control"></div>
    <div class="col-md-4 mt-3"><label>Procured From</label><input type="text" value="<?php echo e($asset->procured_from ?? ''); ?>" name="procured_from" class="form-control"></div>
    <div class="col-md-4 mt-3"><label>Purchase Date</label><input type="date" value="<?php echo e($asset->purchase_date ?? ''); ?>" name="purchase_date" class="form-control"></div>
    <div class="col-md-4 mt-3"><label>Warranty (months)</label><input type="number" value="<?php echo e($asset->warranty ?? ''); ?>" name="warranty" class="form-control"></div>
    <div class="col-md-4 mt-3"><label>PO No</label><input type="text" value="<?php echo e($asset->po_no ?? ''); ?>" name="po_no" class="form-control"></div>
    <div class="col-md-4 mt-3"><label>MRP</label><input type="number" value="<?php echo e($asset->mrp ?? ''); ?>" name="mrp" class="form-control"></div>
    <div class="col-md-4 mt-3"><label>Purchase Cost</label><input type="number" value="<?php echo e($asset->purchase_cost ?? ''); ?>" name="purchase_cost" class="form-control"></div>
</div>
<?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\asset\form.blade.php ENDPATH**/ ?>