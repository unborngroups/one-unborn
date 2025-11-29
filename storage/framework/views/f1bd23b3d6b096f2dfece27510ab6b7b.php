



<?php $__env->startSection('content'); ?>

<div class="container py-4">

    <h3 class="mb-3 text-primary">View feasibility</h3>



    <div class="card shadow border-0 p-4">

        <table class="table table-bordered">

            <tr>

                <th>Feasibility Request ID</th>

                <td><?php echo e($feasibility->feasibility_request_id ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Type of Service </th>

                <td><?php echo e($feasibility->type_of_service ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Company Name</th>

                <td><?php echo e($feasibility->company->company_name ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Client Name </th>

                <td><?php echo e($feasibility->client->client_name ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Pincode</th>

                <td><?php echo e($feasibility->pincode ?? '-'); ?></td>

            </tr>

            <tr>

                <th>State</th>

                <td><?php echo e($feasibility->state ?? '-'); ?></td>

            </tr>

            <tr>

                <th>District</th>

                <td><?php echo e($feasibility->district ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Area</th>

                <td><?php echo e($feasibility->area ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Address</th>

                <td><?php echo e($feasibility->address ?? '-'); ?></td>

            </tr>

            <tr>

                <th>SPOC Name </th>

                <td><?php echo e($feasibility->spoc_name ?? '-'); ?></td>

            </tr>

            <tr>

                <th>SPOC Contact 1 </th>

                <td><?php echo e($feasibility->spoc_contact1 ?? '-'); ?></td>
            </tr>

            <tr>

                <th>SPOC Contact 2 </th>

                <td><?php echo e($feasibility->spoc_contact2 ?? '-'); ?></td>
            </tr>

            <tr>

                <th>SPOC Email </th>

                <td><?php echo e($feasibility->spoc_email ?? '-'); ?></td>
            </tr>

              <tr>

                <th>No. Of Links </th>

                <td><?php echo e($feasibility->no_of_links ?? '-'); ?></td>
            </tr>

            <tr> 

                <th>Vendor Type</th>

                <td><?php echo e($feasibility->vendor_type ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Speed</th>

                <td><?php echo e($feasibility->speed ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Static IP</th>

                <td><?php echo e($feasibility->static_ip ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Static IP Subnet</th>

                <td><?php echo e($feasibility->static_ip_subnet ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Expected Delivery</th>

                <td><?php echo e($feasibility->expected_delivery ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Expected Activation</th>

                <td><?php echo e($feasibility->expected_activation ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Hardware Required</th>

                <td><?php echo e($feasibility->hardware_required ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Hardware Model Name</th>

                <td><?php echo e($feasibility->hardware_model_name ?? '-'); ?></td>

            </tr>

            <tr>

                <th>Status</th>

                <td>

                    <span class="badge <?php echo e($feasibility->status === 'Active' ? 'bg-success' : 'bg-danger'); ?>">

                        <?php echo e($feasibility->status); ?>


                    </span>

                </td>

            </tr>

        </table>

        <div class="text-end">

            <a href="<?php echo e(route('feasibility.index')); ?>" class="btn btn-secondary">Back</a>

        </div>

    </div>

</div>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\feasibility\view.blade.php ENDPATH**/ ?>