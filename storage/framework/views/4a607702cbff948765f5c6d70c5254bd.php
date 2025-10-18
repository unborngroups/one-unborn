<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Company Name</label>
        <input type="text" name="company_name" class="form-control" 
            value="<?php echo e(old('company_name', $company->company_name ?? '')); ?>" required>
    </div>

    <div class="col-md-6 mb-3">
    <label class="form-label">CIN / LLPIN</label>
    <input type="text" name="cin_llpin" class="form-control"
        value="<?php echo e(old('cin_llpin', $company->cin_llpin ?? '')); ?>">
</div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Contact No</label>
        <input type="text" name="contact_no" class="form-control"
            value="<?php echo e(old('contact_no', $company->contact_no ?? '')); ?>">
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Phone No</label>
        <input type="text" name="phone_no" class="form-control"
            value="<?php echo e(old('phone_no', $company->phone_no ?? '')); ?>">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Email 1</label>
        <input type="email" name="email_1" class="form-control"
            value="<?php echo e(old('email_1', $company->email_1 ?? '')); ?>">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Email 2</label>
        <input type="email" name="email_2" class="form-control"
            value="<?php echo e(old('email_2', $company->email_2 ?? '')); ?>">
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Address</label>
        <textarea name="address" class="form-control" rows="2"><?php echo e(old('address', $company->address ?? '')); ?></textarea>
    </div>

    
    <div class="col-md-4 mb-3">
        <label class="form-label">Billing Logo</label>
        <input type="file" name="billing_logo" class="form-control">
        <?php if(!empty($company->billing_logo)): ?>
            <img src="<?php echo e(asset('images/logos/' . $company->billing_logo)); ?>" class="mt-2 border rounded" width="80">
        <?php endif; ?>
    </div>

    
    <div class="col-md-4 mb-3">
        <label class="form-label">Normal Sign</label>
        <input type="file" name="billing_sign_normal" class="form-control">
        <?php if(!empty($company->billing_sign_normal)): ?>
            <img src="<?php echo e(asset('images/n_signs/' . $company->billing_sign_normal)); ?>" class="mt-2 border rounded" width="80">
        <?php endif; ?>
    </div>

    
    <div class="col-md-4 mb-3">
        <label class="form-label">Digital Sign</label>
        <input type="file" name="billing_sign_digital" class="form-control">
        <?php if(!empty($company->billing_sign_digital)): ?>
            <img src="<?php echo e(asset('images/d_signs/' . $company->billing_sign_digital)); ?>" class="mt-2 border rounded" width="80">
        <?php endif; ?>
    </div>

    <div class="col-md-4 mb-3">
    <label class="form-label">GST No</label>
    <input type="text" name="gst_no" class="form-control"
        value="<?php echo e(old('gst_no', $company->gst_no ?? '')); ?>">
</div>

<div class="col-md-4 mb-3">
    <label class="form-label">PAN Number</label>
    <input type="text" name="pan_number" class="form-control"
        value="<?php echo e(old('pan_number', $company->pan_number ?? '')); ?>">
</div>

<div class="col-md-4 mb-3">
    <label class="form-label">TAN Number</label>
    <input type="text" name="tan_number" class="form-control"
        value="<?php echo e(old('tan_number', $company->tan_number ?? '')); ?>">
</div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
            <option value="Active" <?php echo e(old('status', $company->status ?? '') == 'Active' ? 'selected' : ''); ?>>Active</option>
            <option value="Inactive" <?php echo e(old('status', $company->status ?? '') == 'Inactive' ? 'selected' : ''); ?>>Inactive</option>
        </select>
    </div>
</div>
<?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/companies/partials/form.blade.php ENDPATH**/ ?>