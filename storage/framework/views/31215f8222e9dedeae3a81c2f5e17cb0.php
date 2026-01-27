

<?php $__env->startSection('content'); ?>
<div class="container">

    <h4 class="mb-3">Add Asset</h4>


    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php if(session('imported_rows')): ?>
            <div class="card border-success mb-3">
                <div class="card-header bg-success text-white py-1">Imported Rows Summary</div>
                <div class="card-body p-2">
                    <div style="max-height:300px;overflow:auto;">
                        <table class="table table-bordered table-sm mb-0">
                            <thead>
                                <tr>
                                    <?php $__currentLoopData = session('import_headers', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th><?php echo e($header); ?></th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = session('imported_rows', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cell): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <td><?php echo e($cell); ?></td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    
    <?php if(session('import_errors')): ?>
        <div class="alert alert-warning">
            <strong>Import could not process some rows:</strong>
            <?php if(session('failed_rows')): ?>
                <?php if(count(session('failed_rows', [])) > 0): ?>
                    <form action="#" method="POST" class="mt-2">
                        <?php echo csrf_field(); ?>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="downloadFailedRowsAsset()">Download Failed Rows (CSV)</button>
                    </form>
                    <div id="failedRowsTable" style="max-height:300px; overflow:auto;">
                        <input type="text" id="failedFilterInput" class="form-control form-control-sm mb-2" placeholder="Filter by error reason..." onkeyup="filterFailedRows()">
                        <table class="table table-bordered table-sm mt-2">
                            <thead>
                                <tr>
                                    <?php $__currentLoopData = session('import_headers', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th><?php echo e($header); ?></th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            </thead>
                            <tbody id="failedRowsTbody">
                                <?php $__currentLoopData = session('failed_rows', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cell): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <td><?php echo e($cell); ?></td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <script>
                        function filterFailedRows() {
                            var input = document.getElementById('failedFilterInput');
                            var filter = input.value.toLowerCase();
                            var tbody = document.getElementById('failedRowsTbody');
                            var rows = tbody.getElementsByTagName('tr');
                            for (var i = 0; i < rows.length; i++) {
                                var show = false;
                                var cells = rows[i].getElementsByTagName('td');
                                for (var j = 0; j < cells.length; j++) {
                                    if (cells[j].innerText.toLowerCase().indexOf(filter) > -1) {
                                        show = true;
                                        break;
                                    }
                                }
                                rows[i].style.display = show ? '' : 'none';
                            }
                        }
                        function downloadFailedRowsAsset() {
                            // You can implement a route for asset failed rows download if needed
                            alert('Download failed rows not implemented.');
                        }
                    </script>
                <?php else: ?>
                    <div class="alert alert-info mt-2">No failed rows to display.</div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

     <?php
            $importRow = session('imported_row', []);
        ?>
     
    <!-- <h5 class="mb-3 ">Import Assets</h5> -->
        <div class="row g-3 mb-3">
            <div class="col-md-12">
                <button class="btn btn-info mb-2" type="button" onclick="toggleImportAsset()">Import Assets via Excel</button>
                <div id="importAssetBox" style="display:none;">
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
                <script>
                    function toggleImportAsset() {
                        var box = document.getElementById('importAssetBox');
                        box.style.display = (box.style.display === 'none' || box.style.display === '') ? 'block' : 'none';
                    }
                </script>
            </div>
        </div>

<!-- 
    
    <?php if(session('import_errors')): ?>
        <div class="alert alert-warning mt-2">
            <ul class="mb-0">
                <?php $__currentLoopData = session('import_errors'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?> -->

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