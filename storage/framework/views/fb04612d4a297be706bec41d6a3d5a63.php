<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title>One-Unborn</title>

    <link rel="icon" type="image/png" width="20" height="20" href="<?php echo e(asset('images/logo.jpg')); ?>">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <!-- ✅ Flatpickr CSS -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- ✅ Flatpickr JS -->

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


    <!-- <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.css" rel="stylesheet"> -->

    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet">

    <!-- ✅ Bootstrap CSS -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">



    <!-- ✅ Select2 CSS (with Bootstrap 5 theme) -->

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <!-- style -->

     <style>
               
        body{
            background:#f4f6f9;
            overflow-y: auto !important;
            /* background: #f4f6f9; */
        }
        /* Ensure main content starts below navbar and status bar on mobile */
        @media (max-width: 767px) {
            .content-wrapper {
                margin-left: 0 !important;
                width: 100vw !important;
                padding: 0 !important;
            }
        }

        h1, h2, h3, h4, h5, h6 {
            font-size: 1.2rem !important;
            word-break: break-word;
        }
        .form-control {
            font-size: 1rem !important;
            padding: 8px !important;
        }
        label, .form-label {
            font-size: 0.98rem !important;
        }
        /* Fix heading overflow */
        .page-title, .main-title, .add-client-title {
            font-size: 1.3rem !important;
            text-align: center !important;
            word-break: break-word;
            margin-top: 1.2rem !important;
        }
        /* Sidebar default for large screens */

    #sidebar {
    width: 260px;
    min-width: 260px;
    max-width: 260px;
    position: fixed;
    top: 55px; /* same as navbar height */
    left: 0;
    height: calc(100vh - 55px);
    background-color: #061c5c;
    overflow-y: auto;
    z-index: 1050;
}

     /* ✅ Active & Hover Colors */

        .nav-link.menu-item {

            padding: 5px 20px;

            border-radius: 6px;

            transition: background-color 0.3s ease;

        }
        /* .navbar {
    background: #122558ff !important;
} */

/* 🔥 Slim Professional Navbar */
.navbar {
    background: #010144ff !important;
    padding-top: 6px !important;
    padding-bottom: 6px !important;
    height: 55px !important; /* reduced height */
    /* display: flex; */
    align-items: center;
    position: fixed;
    top: 0;
    left: 0; /* touch left edge */
    width: 100%;
    z-index: 1000;
    border-bottom: 1px solid rgba(13, 110, 253, 0.15);
    box-shadow: none !important;
}
/* Make the navbar full width and start from left edge */

/* Push the main content down below navbar */
.navbar .nav-link,
.navbar-brand,
.navbar-text {
    padding-top: 0 !important;
    padding-bottom: 0 !important;
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
         .nav-item{
           padding: 1px;
           transition: background-color 0.3s ease;
      }

      details > summary {
    list-style: none;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

details > summary::-webkit-details-marker {
    display: none;
}

/* Arrow default */
.arrow {
    transition: transform 0.2s ease;
}

/* When opened → arrow up */
details[open] .arrow {
    transform: rotate(180deg);
}

details > ul {
    animation: dropdown 0.2s ease-out;
}

@keyframes dropdown {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}


#sidebar .collapse .nav-link {

    font-size: 0.95rem;

    padding-left: 1.8rem;

}

    /* Content shift (desktop, sidebar visible) */

    .content-wrapper {
        margin-left: 260px;
        width: calc(100% - 260px);
        transition: all 0.3s ease;
    }

    /* Collapsed sidebar on desktop */
    body.sidebar-collapsed #sidebar {
        left: -260px;
    }

    body.sidebar-collapsed .content-wrapper {
        margin-left: 0;
        width: 100%;
    }

    /* Mobile hamburger button */

    @media (max-width: 767px) {

        #sidebarToggle {

            display: inline-block !important;

            visibility: visible !important;

            opacity: 1 !important;

            position: relative !important;

            z-index: 1060 !important;

        }

    }

    /* Sidebar collapsed (mobile) - Hide by default */

    @media (max-width: 768px) {

        #sidebar {
        left: -260px;
        transform: none;
    }

    #sidebar.active {
        left: 0;
    }

        /* Mobile menu styling improvements */

        #sidebar .nav-link {

            font-size: 0.9rem !important;

            padding: 8px 12px !important;

            min-height: 44px !important;

            display: flex !important;

            align-items: center !important;

        }

        #sidebar h5 {

            font-size: 1.1rem !important;

        }

        #sidebar .collapse .nav-link {

            font-size: 0.85rem !important;

            padding-left: 1.5rem !important;

        }

        /* Touch-friendly menu items for mobile */

        #sidebar .collapse {

            padding-left: 0 !important;

        }

        #sidebar .collapse .collapse {

            padding-left: 10px !important;

        }

        /* ✅ Overlay for background dim */

        #sidebarOverlay {

            display: none;

            position: fixed;

            top: 55px;

            left: 0;

            width: 100%;

            height: calc(100vh - 55px);

            background: rgba(0, 0, 0, 0.5);

            z-index: 1040;

        }
         #sidebar.active + #sidebarOverlay {

        display: block;

    }  
        #sidebarOverlay.show {
    display: block;
}

        .arrow-icon {
    transition: transform 0.3s ease;
}

.nav-link[aria-expanded="true"] .arrow-icon {
    transform: rotate(180deg);
}

        #sidebarOverlay.show {

            display: block !important;

            opacity: 1;

        }

    }

    /* Extra small mobile devices */

    @media (max-width: 480px) {

        #sidebar {

            width: 260px !important;

            max-width: 85vw !important;

            left: -260px !important;

        }

    }
    /* Form input styling */
    .form-control {

        border-radius: 8px;

        padding: 10px;

    }

    .form-group {

        margin-bottom: 15px;

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
.table th,  .table td {
    width: 300px;
    white-space: nowrap;
    text-align: center;
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

.table-dark-primary {

    background-color: #0A3D62 !important;

    color: white !important;

}

.table-dark-primary th {

    background-color: #0A3D62 !important;

    color: white !important;

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

        <main class="p-4 mt-5">

            <?php echo $__env->yieldContent('content'); ?>

            <?php echo $__env->yieldPushContent('scripts'); ?>

        </main>

    </div>

    <?php echo $__env->make('layouts.partials.internal-chat', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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

<!-- ✅ Summernote CSS & JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>

<!-- ✅ Internal chat toggle (fallback if Vite chat bundle isn't loaded) -->
<script src="<?php echo e(asset('js/internal-chat-toggle.js')); ?>"></script>

<script>

$(document).ready(function() {

    // 🔍 Debug mobile hamburger button

    console.log('Hamburger button exists:', $('#sidebarToggle').length > 0);

    console.log('Hamburger button visible:', $('#sidebarToggle').is(':visible'));

    console.log('Window width:', $(window).width());

    // 📱 Ensure sidebar is hidden on mobile initially

    if ($(window).width() <= 768) {

        $('#sidebar').removeClass('active');

        $('#sidebarOverlay').hide().removeClass('show');

        console.log('📱 Mobile detected - sidebar hidden initially');

    }

    

    // ✅ Initialize Select2 for multiple company selection

    $('#company_id').select2({

        theme: 'bootstrap-5',

        placeholder: "Select Companies",

        allowClear: true,

        width: '100%' // ensures full width styling

    });



    // ✅ Unified sidebar toggle (desktop + mobile)
    $('#sidebarToggle').on('click', function(e) {
        e.preventDefault();

        const isMobile = $(window).width() <= 768;
        const sidebar = $('#sidebar');
        const overlay = $('#sidebarOverlay');
        const body = $('body');
        const icon = $('#sidebarToggleIcon');

        if (isMobile) {
            console.log('📱 Mobile menu toggle clicked');

            if (sidebar.hasClass('active')) {
                // Close mobile menu
                sidebar.removeClass('active');
                overlay.removeClass('show');
                setTimeout(() => overlay.hide(), 300); // Hide after animation
                console.log('📱 Menu closed');
            } else {
                // Open mobile menu
                sidebar.addClass('active');
                overlay.show().addClass('show');
                console.log('📱 Menu opened');
            }
        } else {
            console.log('💻 Desktop sidebar toggle clicked');
            body.toggleClass('sidebar-collapsed');

            const collapsed = body.hasClass('sidebar-collapsed');
            if (icon && icon.length) {
                if (collapsed) {
                    icon.removeClass('bi-chevron-left').addClass('bi-chevron-right');
                } else {
                    icon.removeClass('bi-chevron-right').addClass('bi-chevron-left');
                }
            }
        }
    });

// ✅ Close sidebar when overlay is clicked

$('#sidebarOverlay').on('click', function() {

    console.log('📱 Overlay clicked - closing menu');

    $('#sidebar').removeClass('active');

    $(this).removeClass('show');

    setTimeout(() => $(this).hide(), 300);

});

// ✅ Close sidebar when close button is clicked (mobile)

$(document).on('click', '#sidebarClose', function() {

    console.log('📱 Close button clicked');

    $('#sidebar').removeClass('active');

    $('#sidebarOverlay').removeClass('show');

    setTimeout(() => $('#sidebarOverlay').hide(), 300);

});
// ✅ Close menu when clicking any menu item (mobile)

$(document).on('click', '#sidebar .nav-link', function() {

    if ($(window).width() <= 768) {

        console.log('📱 Menu item clicked - auto closing menu');

        $('#sidebar').removeClass('active');

        $('#sidebarOverlay').removeClass('show');

        setTimeout(() => $('#sidebarOverlay').hide(), 300);

    }

});
    // ✅ Active menu item highlighting

    
    flatpickr("input[type=date]", {
    dateFormat: "Y-m-d", // HTML date input expects YYYY-MM-DD
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


<script>
// Heartbeat: ping server every 2 minutes to update user last activity
setInterval(function() {
    fetch("<?php echo e(url('user/activity/heartbeat')); ?>", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({})
    });
}, 2 * 60 * 1000); // 2 minutes

// On tab close, update last_activity and set status to Offline
window.addEventListener('beforeunload', function (e) {
    if (navigator.sendBeacon) {
        navigator.sendBeacon("<?php echo e(url('user/activity/tab-close')); ?>");
    } else {
        // fallback for older browsers
        fetch("<?php echo e(url('user/activity/tab-close')); ?>", {method: 'POST', headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')}});
    }
});
</script>

<?php echo $__env->yieldContent('scripts'); ?>



</html>

<?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\layouts\app.blade.php ENDPATH**/ ?>