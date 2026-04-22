



<?php $__env->startSection('content'); ?>

<div class="container-fluid py-4">

    <h3 class="mb-4 fw-bold text-primary">Settings</h3>



    <div class="row">

        <!-- Left Side Tabs -->

        <div class="col-md-3">

            <div class="list-group shadow-sm rounded-3">

                <a href="<?php echo e(route('company.settings')); ?>" 

                   class="list-group-item list-group-item-action <?php echo e(request()->is('company-settings') ? 'active' : ''); ?>">

                    <i class="bi bi-building"></i> Company Settings

                </a>

                <a href="<?php echo e(route('tax.invoice')); ?>" 

                   class="list-group-item list-group-item-action <?php echo e(request()->is('tax-invoice-settings') ? 'active' : ''); ?>">

                    <i class="bi bi-receipt"></i> Tax & Invoice Settings

                </a>

                <a href="<?php echo e(route('system.settings')); ?>" 

                   class="list-group-item list-group-item-action <?php echo e(request()->is('system-settings') ? 'active' : ''); ?>">

                    <i class="bi bi-sliders"></i> System Settings

                </a>

            </div>

        </div>



        <!-- Right Side Content -->

        <div class="col-md-9">

            <div class="card border-0 shadow-lg p-4 rounded-4 bg-white">

                <?php echo $__env->yieldContent('settings-content'); ?>

            </div>

        </div>

    </div>

</div>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\settings\layout.blade.php ENDPATH**/ ?>