

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h3 class="mb-3 text-primary">View Company</h3>

    <div class="card shadow border-0 p-4">
        <table class="table table-bordered">
            <tr>
                <th>Trade / Brand Name</th>
                <td><?php echo e($company->trade_name ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Company Name</th>
                <td><?php echo e($company->company_name ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Business Number (CIN / LLPIN)</th>
                <td><?php echo e($company->business_number ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Company Phone</th>
                <td><?php echo e($company->company_phone ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Company Email</th>
                <td><?php echo e($company->company_email ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Alternative Contact Number</th>
                <td><?php echo e($company->alternative_contact_number ?? '-'); ?></td>
            </tr>
            <tr>
                <th>GST Number</th>
                <td><?php echo e($company->gstin ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Address</th>
                <td><?php echo e($company->address ?? '-'); ?></td>
            </tr>
           
            <tr> 
                <th>Website</th>
                <td><?php echo e($company->website ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Branch Location</th>
                <td><?php echo e($company->branch_location ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Google Maps URL</th>
                <td><?php echo e($company->store_location_url ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Google Place ID</th>
                <td><?php echo e($company->google_place_id ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Instagram</th>
                <td><?php echo e($company->instagram ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Youtube</th>
                <td><?php echo e($company->youtube ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Facebook</th>
                <td><?php echo e($company->facebook ?? '-'); ?></td>
            </tr>
            <tr>
                <th>LinkedIn</th>
                <td><?php echo e($company->linkedin ?? '-'); ?></td>
            </tr>
            <tr>
                <th>PAN Number</th>
                <td><?php echo e($company->pan_number ?? '-'); ?></td>
            </tr>
            <!-- <tr>
                <th>TAN Number</th>
                <td><?php echo e($company->tan_number ?? '-'); ?></td>
            </tr> -->
            <tr>
                <th>Bank Name</th>
                <td><?php echo e($company->bank_name ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Branch Name</th>
                <td><?php echo e($company->branch_name ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Account Number</th>
                <td><?php echo e($company->account_number ?? '-'); ?></td>
            </tr>
            <tr>
                <th>IFSC Code</th>
                <td><?php echo e($company->ifsc_code ?? '-'); ?></td>
            </tr>
            <tr>
                <th>UPI ID</th>
                <td><?php echo e($company->upi_id ?? '-'); ?></td>
            </tr>
            <tr>
                <th>UPI Number</th>
                <td><?php echo e($company->upi_number ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Opening Balance</th>
                <td><?php echo e($company->opening_balance ?? '-'); ?></td>
            </tr>
            
           
              <tr>
                <th>Billing Logo</th>
                <td>
                    <?php if(!empty($company->billing_logo)): ?>
                        <img src="<?php echo e(asset('images/logos/'.$company->billing_logo)); ?>" width="100" class="border rounded">
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>

            
            <tr>
                <th>Normal Sign</th>
                <td>
                    <?php if(!empty($company->billing_sign_normal)): ?>
                        <img src="<?php echo e(asset('images/n_signs/'.$company->billing_sign_normal)); ?>" width="100" class="border rounded">
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>

            
            <tr>
                <th>Digital Sign</th>
                <td>
                    <?php if(!empty($company->billing_sign_digital)): ?>
                        <img src="<?php echo e(asset('images/d_signs/'.$company->billing_sign_digital)); ?>" width="100" class="border rounded">
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>

            <tr>
                <th>Status</th>
                <td>
                    <span class="badge <?php echo e($company->status === 'Active' ? 'bg-success' : 'bg-danger'); ?>">
                        <?php echo e($company->status); ?>
                    </span>
                </td>
            </tr>
        </table>

        <div class="text-end">
            <a href="<?php echo e(route('companies.index')); ?>" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\wlcome\multipleuserpage\resources\views\companies\view.blade.php ENDPATH**/ ?>