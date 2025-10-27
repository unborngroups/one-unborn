<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>One-Unborn</title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/logo.jpg')); ?>">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <!-- ✅ Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
   
    <!-- ✅ Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- ✅ Select2 CSS (with Bootstrap 5 theme) -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <!-- style -->
     <style>
    /* Sidebar default for large screens */
  #sidebar {
    width: 230px;
    min-height: 100vh;
    transition: all 0.3s;
    position: fixed;
    top: 0;
    left: 0;
    background-color: #121722ff;
    z-index: 100;
}
     /* ✅ Active & Hover Colors */
        .nav-link.menu-item {
            padding: 10px 15px;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }
        .nav-link.menu-item:hover {
            background-color: #1e40af;
            color: #fff !important;
        }
        .nav-link.menu-item.active {
            background-color: #0d6efd;
            color: #fff !important;
            font-weight: 600;
        }
        /* xx  */
        .menu-item.active {
    background-color: #0d6efd !important;
    border-radius: 6px;
}
#sidebar .collapse .nav-link {
    font-size: 0.95rem;
    padding-left: 1.8rem;
}
        /*  */

    /* Content shift */
    .content-wrapper {
    margin-left: 230px; /* a bit smaller for better balance */
    width: calc(100% - 230px);
    transition: margin-left 0.3s ease, width 0.3s ease;
    padding: 0; /* remove extra padding causing push */
}
    /* Sidebar collapsed (mobile) */
    @media (max-width: 768px) {
        #sidebar {
            position: fixed;
            left: -230px;
            top: 0;
            height: 100%;
            z-index: 999;
            transition: left 0.3s ease;
        }
        #sidebar.active {
            left: 0;
        }
        .content-wrapper {
            margin-left: 0 !important;
             width: 100% !important;
        /* transition: all 0.3s ease; */
        }
        /* ✅ Overlay for background dim */
    #sidebarOverlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 998;
    }

    #sidebar.active + #sidebarOverlay {
        display: block;
    }  
    .form-control {
        border-radius: 8px;
        padding: 10px;
    }

    .form-group {
        margin-bottom: 15px;
    }
    }
    /* Fix action button alignment */
.table td .btn {
    vertical-align: middle;
    margin-top: 0 !important;
}

/* Restore alternating row colors (fix for .table-striped + .table-bordered) */
.table-striped tbody tr:nth-of-type(odd) {
    background-color: #f9fafb !important;
}

/* Improve Action button look */
.table td .btn-sm {
    border-radius: 6px;
    padding: 4px 8px;
}

/* ✅ Fix Action button vertical alignment */
.table td {
    vertical-align: middle !important;
}

/* ✅ Center align the Action column */
.table th:nth-child(2),
.table td:nth-child(2) {
    text-align: center;
    vertical-align: middle !important;
}

/* ✅ Keep buttons inline and neat */
.table td .btn {
    margin: 2px 3px;
    vertical-align: middle;
}

/* Optional: soften button colors to match your previous look */
.btn-primary {
    background-color: #0d6efd !important;
    border-color: #0d6efd !important;
}

.btn-danger {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
}

.btn-success {
    background-color: #198754 !important;
    border-color: #198754 !important;
}

.btn-warning {
    background-color: #ffc107 !important;
    border-color: #ffc107 !important;
    color: #000 !important;
}

.btn-info {
    background-color: #0dcaf0 !important;
    border-color: #0dcaf0 !important;
    color: #000 !important;
}

    
</style>
<?php if(session('alert')): ?>
  <div class="alert alert-warning text-center"><?php echo e(session('alert')); ?></div>
<?php endif; ?>


</head>
<div id="sidebarOverlay"></div>

<body class="d-flex">
    <?php echo $__env->make('layouts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="flex-grow-1 content-wrapper">
        <?php echo $__env->make('layouts.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <main class="p-4">
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>
</body>
<!-- ✅ jQuery must come before Select2 and before your custom script -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>

<!-- ✅ Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- ✅ Bootstrap JS (after jQuery is fine) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"></script>

<!-- ✅ Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">


<script>
$(document).ready(function() {
    // ✅ Initialize Select2 for multiple company selection
    $('#company_id').select2({
        theme: 'bootstrap-5',
        placeholder: "Select Companies",
        allowClear: true,
        width: '100%' // ensures full width styling
    });

    // ✅ Sidebar toggle
   // $('#sidebarToggle').on('click', function() {
     //   $('#sidebar').toggleClass('active');
   // });
    // ✅ Sidebar toggle for mobile
$('#sidebarToggle').on('click', function() {
    $('#sidebar').toggleClass('active');
    $('#sidebarOverlay').toggle();
});

// ✅ Close sidebar when overlay is clicked
$('#sidebarOverlay').on('click', function() {
    $('#sidebar').removeClass('active');
    $(this).hide();
});


    // ✅ Active menu item highlighting
    $('.menu-item').on('click', function() {
        $('.menu-item').removeClass('active');
        $(this).addClass('active');
    });

    flatpickr("input[type=date]", {
    dateFormat: "d-m-Y", // change format to DD-MM-YYYY
    altInput: true,      // shows friendly format
    altFormat: "F j, Y", // shows “October 6, 2025”
    allowInput: true,
    // theme: "dark"      // uncomment for dark mode
});

$(document).ready(function () {
    $('.select2-tags').select2({
        theme: 'bootstrap-5',
        tags: true, // ✅ allows typing new values
        placeholder: 'Select or Type',
        allowClear: true,
        width: '100%'
    });
});

});

</script>

</html>
<?php /**PATH F:\xampp\htdocs\new\multipleuserpage\resources\views/layouts/app.blade.php ENDPATH**/ ?>