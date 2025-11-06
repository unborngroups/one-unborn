<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>One-Unborn</title>
    <link rel="icon" type="image/png" width="20" height="20" href="{{ asset('images/logo.jpg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
            position: fixed !important;
            left: -280px !important; /* Hidden off-screen by default */
            top: 0;
            height: 100vh !important;
            width: 280px !important;
            max-width: 80vw !important;
            z-index: 1050 !important;
            transition: left 0.3s ease;
            background-color: #121722ff !important;
            overflow-y: auto;
            transform: translateX(-100%); /* Extra ensure it's hidden */
        }
        #sidebar.active {
            left: 0 !important;
            transform: translateX(0); /* Slide in when active */
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
        }
        .content-wrapper {
            margin-left: 0 !important;
            width: 100% !important;
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
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            opacity: 0;
            transition: opacity 0.3s ease;
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
.table-dark-primary {
    background-color: #0A3D62 !important;
    color: white !important;
}

.table-dark-primary th {
    background-color: #0A3D62 !important;
    color: white !important;
}

    
</style>
@if(session('alert'))
  <div class="alert alert-warning text-center">{{ session('alert') }}</div>
@endif


</head>
<div id="sidebarOverlay"></div>

<body class="d-flex">
    @include('layouts.sidebar')
    <div class="flex-grow-1 content-wrapper">
        @include('layouts.navbar')
        <main class="p-4">
            @yield('content')
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

    // ✅ Sidebar toggle
   // $('#sidebarToggle').on('click', function() {
     //   $('#sidebar').toggleClass('active');
   // });
    // ✅ Sidebar toggle for mobile - Real website behavior
$('#sidebarToggle').on('click', function(e) {
        e.preventDefault();
        console.log('📱 Mobile menu toggle clicked');
        
        const sidebar = $('#sidebar');
        const overlay = $('#sidebarOverlay');
        
        if (sidebar.hasClass('active')) {
            // Close menu
            sidebar.removeClass('active');
            overlay.removeClass('show');
            setTimeout(() => overlay.hide(), 300); // Hide after animation
            console.log('📱 Menu closed');
        } else {
            // Open menu
            sidebar.addClass('active');
            overlay.show().addClass('show');
            console.log('📱 Menu opened');
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

@yield('scripts')

</html>
