





<?php $__env->startSection('content'); ?>

<div class="container py-4">

    <h3 class="text-primary mb-3">🏢 Company Settings</h3>



    

    <?php if(session('success')): ?>

        <div class="alert alert-success alert-dismissible fade show" role="alert">

            <?php echo e(session('success')); ?>


            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

        </div>

    <?php endif; ?>
    
  

        <?php if($errors->any()): ?>

            <div class="alert alert-danger">

                <ul class="mb-0">

                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <li><?php echo e($error); ?></li>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </ul>

            </div>

        <?php endif; ?>


    

    <form action="<?php echo e(route('company.settings.update')); ?>" method="POST" enctype="multipart/form-data">

        <?php echo csrf_field(); ?>

        <?php echo method_field('PUT'); ?>



        <div class="row">

            <div class="col-md-6 mb-3">

                <label>Company Name *</label>

                <input type="text" name="company_name" class="form-control"

                       value="<?php echo e(old('company_name', $company->company_name ?? '')); ?>">

            </div>



            

            <div class="col-md-6 mb-3">

                <label>Company Email</label>

                <input type="email" name="company_email" class="form-control"

                       value="<?php echo e(old('company_email', $company->company_email ?? '')); ?>">

            </div>

            


            

            <div class="col-md-6 mb-3">

                <label>Contact Number</label>

                <input type="text" name="contact_no" class="form-control"

                       value="<?php echo e(old('contact_no', $company->contact_no ?? '')); ?>">

            </div>



             

            <div class="col-md-6 mb-3">

                <label>Website</label>

                <input type="text" name="website" class="form-control"

                       value="<?php echo e(old('website', $company->website ?? '')); ?>">

            </div>



            

            <div class="col-md-6 mb-3">

                <label>GST Number</label>

                <input type="text" name="gst_number" class="form-control"

                       value="<?php echo e(old('gst_number', $company->gst_number ?? '')); ?>">

            </div>



             

            <div class="col-md-6 mb-3">

                <label>Company Logo</label>

                <input type="file" name="company_logo" class="form-control">

                <?php if(!empty($company->company_logo)): ?>

                    <img src="<?php echo e(asset('storage/' . $company->company_logo)); ?>" class="mt-2" width="100">

                <?php endif; ?>

            </div>



            

            <div class="col-md-12 mb-3">

                <label>Address</label>

                <textarea name="address" class="form-control"><?php echo e(old('address', $company->address ?? '')); ?></textarea>

            </div>

        </div>

        <!-- email -->

         <hr>

<h5 class="text-primary fw-bold mb-3">✉️ Email Settings</h5>



<div class="row">



    

    <div class="col-md-6 mb-3">

        <label>SMTP Server Address *</label>

        <input type="text" name="mail_host" class="form-control"

               value="<?php echo e(old('mail_host', $company->mail_host ?? '')); ?>" required>

    </div>



    

    <div class="col-md-6 mb-3">

        <label>SMTP Username *</label>

        <input type="text" name="mail_username" class="form-control"

               value="<?php echo e(old('mail_username', $company->mail_username ?? '')); ?>" required>

    </div>



    

    <div class="col-md-6 mb-3">

        <label>SMTP Password *</label>

        <input type="password" name="mail_password" class="form-control"

               value="<?php echo e(old('mail_password', $company->mail_password ?? '')); ?>" required>

    </div>



    

    <div class="col-md-6 mb-3">

        <label>SMTP Port *</label>

        <input type="text" name="mail_port" class="form-control"

               value="<?php echo e(old('mail_port', $company->mail_port ?? '')); ?>" required>

    </div>



    

    <div class="col-md-6 mb-3">

        <label class="form-label">Encryption Type</label>

            <select name="mail_encryption" class="form-select">

                <option value="">Select Encryption Type</option>

                <option value="ssl" <?php echo e(old('mail_encryption', $company->mail_encryption ?? '') == 'ssl' ? 'selected' : ''); ?>>SSL</option>

                <option value="tls" <?php echo e(old('mail_encryption', $company->mail_encryption ?? '') == 'tls' ? 'selected' : ''); ?>>TLS</option>

            </select>

    </div>



    

    <div class="col-md-6 mb-3">

        <label>Mail From Name *</label>

        <input type="text" name="mail_from_name" class="form-control"

               value="<?php echo e(old('mail_from_name', $company->mail_from_name ?? '')); ?>" required>

    </div>



    

    <div class="col-md-6 mb-3">

        <label>Mail From Address *</label>

        <input type="email" name="mail_from_address" class="form-control"

               value="<?php echo e(old('mail_from_address', $company->mail_from_address ?? '')); ?>" required>

    </div>



    

    <div class="col-md-6 mb-3">

        <label>Mail Footer Text</label>

        <input type="text" name="mail_footer" class="form-control"

               value="<?php echo e(old('mail_footer', $company->mail_footer ?? '')); ?>">

    </div>



    

    <div class="col-md-6 mb-3">

        <label>Mail Signature</label>

        <input type="text" name="mail_signature" class="form-control"

               value="<?php echo e(old('mail_signature', $company->mail_signature ?? '')); ?>">

    </div>


</div>


        

        
        <hr>
        <h5>✉️ Exception Permission Email</h5>
        <div class="col-md-6 mb-3">

                <label>Exception Permission Email</label>

                <input type="email" name="exception_permission_email" class="form-control"

                       value="<?php echo e(old('exception_permission_email', $company->exception_permission_email ?? '')); ?>">

                <small class="text-muted">Exception emails from Feasibility (SM) will be sent to this address.</small>

            </div>



        

        

        

        <hr>

<h5 class="text-primary fw-bold mb-3">🌐 Social Media Links</h5>

<div class="row mb-3">

    

    <div class="col-md-6">

        <label for="linkedin_url" class="form-label">LinkedIn URL</label>

        <input type="url" name="linkedin_url" class="form-control" value="<?php echo e(old('linkedin_url', $company->linkedin_url)); ?>">

    </div>

    

    <div class="col-md-6">

        <label for="whatsapp_number" class="form-label">WhatsApp Number</label>

        <input type="text" name="whatsapp_number" class="form-control" value="<?php echo e(old('whatsapp_number', $company->whatsapp_number)); ?>">

    </div>

</div>



<div class="row mb-3">

    

    <div class="col-md-6">

        <label for="facebook_url" class="form-label">Facebook URL</label>

        <input type="url" name="facebook_url" class="form-control" value="<?php echo e(old('facebook_url', $company->facebook_url)); ?>">

    </div>

     

    <div class="col-md-6">

        <label for="instagram_url" class="form-label">Instagram URL</label>

        <input type="url" name="instagram_url" class="form-control" value="<?php echo e(old('instagram_url', $company->instagram_url)); ?>">

    </div>

</div>



<?php if($company->linkedin_url): ?>

    <a href="<?php echo e($company->linkedin_url); ?>" target="_blank" class="me-2">

        <i class="bi bi-linkedin text-primary fs-4"></i>

    </a>

<?php endif; ?>



<?php if($company->whatsapp_number): ?>

    <a href="https://wa.me/<?php echo e($company->whatsapp_number); ?>" target="_blank" class="me-2">

        <i class="bi bi-whatsapp text-success fs-4"></i>

    </a>

<?php endif; ?>





        <button type="submit" class="btn btn-primary">Save Settings</button>

    </form>

</div>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\settings\company.blade.php ENDPATH**/ ?>