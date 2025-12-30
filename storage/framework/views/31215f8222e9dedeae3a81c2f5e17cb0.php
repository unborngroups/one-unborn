

<?php $__env->startSection('content'); ?>
<div class="container">

    <h4 class="mb-3">Add Asset</h4>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

     <?php
            $importRow = session('imported_row', []);
        ?>
     
    <!-- <h5 class="mb-3 ">Import Assets</h5> -->
        <div class="row g-3 mb-3">
            <div class="col-md-12">
                <button class="btn btn-info" type="button" id="importExcelBtn" aria-controls="importCard">
                    Import Assets via Excel
                </button>
                <div class="collapse mt-3" id="importCard">
                    <div class="card border-info">
                        <div class="card-body">
                            <p class="mb-3 small text-muted">Download the sample format, populate it with Asset data, and then upload it via Import Excel.</p>
                            <form action="<?php echo e(route('operations.asset.import')); ?>" method="POST" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                <div class="input-group">
                                    <input type="file" name="file" class="form-control" required  accept=".xlsx, .xls,.csv,.xlsm,.ods">
                                    <a href="<?php echo e(asset('images/assets/assets (10).xlsx')); ?>" target="_blank" class="btn btn-outline-secondary" title="Download asset sample">Download Format</a>
                                    <button type="submit" class="btn btn-primary">Import Excel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    
    <?php if(session('import_errors')): ?>
        <div class="alert alert-warning mt-2">
            <ul class="mb-0">
                <?php $__currentLoopData = session('import_errors'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

</div>

    
    
    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <form action="<?php echo e(route('operations.asset.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <?php echo $__env->make('operations.asset.form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <button type="submit" class="btn btn-primary mt-1 float-start">Save</button>

        <a href="<?php echo e(route('operations.asset.index')); ?>" class="btn btn-secondary mt-1 float-end">
            <-- Back
        </a>
    </form>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    // Open the import box only on first click, do not toggle closed
    document.getElementById('importExcelBtn')?.addEventListener('click', function () {
        var importCard = document.getElementById('importCard');
        if (importCard && !importCard.classList.contains('show')) {
            var collapse = bootstrap.Collapse.getOrCreateInstance(importCard);
            collapse.show();
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\operations\asset\create.blade.php ENDPATH**/ ?>