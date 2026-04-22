



<?php $__env->startSection('content'); ?>

<div class="container py-4">

    <h3 class="mb-3 text-primary">View Company</h3>



    <div class="card shadow border-0 p-4">

        <table class="table table-bordered">

            <tr>

                <th>Client Name</th>

                <td style="text-align: left;"><?php echo e($client->client_name ?? '-'); ?></td>

            </tr>
            <tr>

                <th>Short Name</th>

                <td style="text-align: left;"><?php echo e($client->short_name ?? '-'); ?></td> 

            <tr>

                <th>Client Code</th>

                <td style="text-align: left;"><?php echo e($client->client_code ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Business Display Name</th>

                <td style="text-align: left;"><?php echo e($client->business_display_name ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Office Type</th>

                <td style="text-align: left;"><?php echo e(ucfirst($client->office_type) ?? '-'); ?></td>

            <tr>
               

            <tr>
                <th>Head Office</th>
                <td style="text-align: left;">
                    <?php if($client->office_type === 'Branch' && $client->headOffice): ?>
                        <?php echo e($client->headOffice->client_name); ?> (<?php echo e($client->headOffice->client_code); ?>)
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>PAN Number</th>
                <td style="text-align: left;"><?php echo e($client->pan_number ?? '-'); ?></td>
            </tr>

            <tr>

                <th>Address 1</th>

                <td style="text-align: left;"><?php echo e($client->address1 ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Address 2</th>

                <td style="text-align: left;"><?php echo e($client->address2 ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Address 3</th>

                <td style="text-align: left;"><?php echo e($client->address3 ?? '-'); ?></td>

            </tr>

            <tr>

                <th>City</th>

                <td style="text-align: left;"><?php echo e($client->city ?? '-'); ?></td>

            </tr>

            <tr>

                <th>State</th>

                <td style="text-align: left;"><?php echo e($client->state ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Country</th>

                <td style="text-align: left;"><?php echo e($client->country ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Pincode</th>

                <td style="text-align: left;"><?php echo e($client->pincode ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Billing SPOC Name</th>

                <td style="text-align: left;"><?php echo e($client->billing_spoc_name ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Contact Number</th>

                <td style="text-align: left;"><?php echo e($client->billing_spoc_contact ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Email</th>

                <td style="text-align: left;"><?php echo e($client->billing_spoc_email ?? '-'); ?></td>

            </tr>

            <tr>
                <th>Billing Sequence</th>
                
                <td style="text-align: left;"><?php echo e(ucfirst($client->billing_sequence) ?? '-'); ?></td>
            </tr>
            <tr>

                <th>GSTIN</th>

                <td style="text-align: left;"><?php echo e($client->gstin ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Invoice Email</th>

                <td style="text-align: left;"><?php echo e($client->invoice_email ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Invoice CC</th>

                <td style="text-align: left;"><?php echo e($client->invoice_cc ?? '-'); ?></td>

            </tr>

             <tr>

                <th>Delivered Email</th>

                <td style="text-align: left;"><?php echo e($client->delivered_email ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Delivered CC</th>

                <td style="text-align: left;"><?php echo e($client->delivered_cc ?? '-'); ?></td>

            </tr>

            <tr>

                <th>SPOC Name</th>

                <td style="text-align: left;"><?php echo e($client->support_spoc_name ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Mobile</th>

                <td style="text-align: left;"><?php echo e($client->support_spoc_mobile ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Email</th>

                <td style="text-align: left;"><?php echo e($client->support_spoc_email ?? '-'); ?></td>

            </tr>



            <tr>

                <th>Status</th>

                <td style="text-align: left;">

                    <span class="badge <?php echo e($client->status === 'Active' ? 'bg-success' : 'bg-danger'); ?>">

                        <?php echo e($client->status); ?>


                    </span>

                </td>

            </tr>

        </table>



        <div class="text-end">

            <a href="<?php echo e(route('clients.index')); ?>" class="btn btn-secondary">Back</a>

        </div>

    </div>

</div>

<style>
    td{
        text-align: left;
    
    }
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\clients\view.blade.php ENDPATH**/ ?>