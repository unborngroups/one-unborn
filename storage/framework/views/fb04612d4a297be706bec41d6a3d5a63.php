<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title>One-Unborn</title>

    <link rel="icon" type="image/png" width="20" height="20" href="<?php echo e(asset('images/logo.jpg')); ?>">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-bg: #031d58;
            /* Slate 900 */
            --sidebar-hover: #3b82f6;
            --primary-accent: #3b82f6;
            /* Modern Blue */
            --glass-white: rgba(255, 255, 255, 0.85);
            --border-color: rgba(226, 232, 240, 0.8);
        }

        body {
            background: #f8fafc;
            /* Lighter background for better contrast */
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            overflow-x: hidden;
        }

        /* --- 🔥 Slim Glass Navbar --- */
        .navbar {
            background: var(--glass-white) !important;
            backdrop-filter: blur(12px);
            /* Glassmorphism */
            -webkit-backdrop-filter: blur(12px);
            height: 60px !important;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1040;
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
            padding: 0 1.5rem !important;
        }

        /* --- 🛡️ Professional Sidebar --- */
        #sidebar {
            width: 300px;
            position: fixed;
            top: 0;
            /* Full height looks more professional */
            left: 0;
            height: 100vh;
            background-color: var(--sidebar-bg);
            padding-top: 70px;
            /* Offset for navbar */
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1050;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.05);
            overflow-y: auto;
        }

        /* --- 💎 Interactive Navigation --- */
        .nav-link.menu-item {
            padding: 10px 18px;
            margin: 4px 12px;
            border-radius: 10px;
            color: #94a3b8 !important;
            font-size: 0.92rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .nav-link.menu-item:hover {
            background-color: var(--sidebar-hover);
            color: #fff !important;
            transform: translateX(5px);
        }

        .nav-link.menu-item.active {
            background-color: var(--primary-accent) !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);
        }

        /* Replace heavy borders with subtle accents in Sidebar */
        .master,
        .sm,
        .operation,
        .finance,
        .hr,
        .system,
        .report {
            border: none !important;
            border-left: 3px solid transparent !important;
            margin: 2px 0;
        }

        /* Subtle color coding for sections */
        .master {
            border-left-color: #ef4444 !important;
        }

        .sm {
            border-left-color: #0ea5e9 !important;
        }

        .operation {
            border-left-color: #10b981 !important;
        }

        .finance {
            border-left-color: #f59e0b !important;
        }

        /* --- 📱 Mobile UI Adjustments --- */
        @media (max-width: 767px) {
            .content-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 15px !important;
            }

            #sidebar {
                left: -260px;
            }

            #sidebar.active {
                left: 0;
            }
        }

        /* --- 📊 Main Content Area --- */
        .content-wrapper {
            margin-left: 300px;
            width: calc(100% - 300px);
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        body.sidebar-collapsed #sidebar {
            left: -260px;
        }

        body.sidebar-collapsed .content-wrapper {
            margin-left: 0;
            width: 100%;
        }

        main {
            margin-top: 65px;
            padding: 2rem;
        }

        /* --- 📄 Table & UI Components --- */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(149, 227, 15, 0.04);
        }

        .table thead th {
            background-color: #f1f5f9 !important;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
        }

        /* --- Navbar Responsive to Sidebar --- */
        body:not(.sidebar-collapsed) .navbar {
            margin-left: 0px;
            width: calc(100% - 260px);
            transition: margin-left 0.3s, width 0.3s;
        }

        body.sidebar-collapsed .navbar {
            margin-left: 0;
            width: 100%;
        }

        .th,
        .td {
            .col {
                width: 130px;
                white-space: nowrap;

            }

            .table th,
            .table td {

                width: 130px;

                white-space: nowrap;

            }

        }
    </style>
</head>

<body class="d-flex">
    <div id="sidebarOverlay"></div>

    <?php echo $__env->make('layouts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="flex-grow-1 content-wrapper">
        <?php echo $__env->make('layouts.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <main>
            <?php if(session('alert')): ?>
            <div class="alert alert-warning alert-dismissible fade show text-center rounded-3 shadow-sm border-0 mb-4">
                <?php echo e(session('alert')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
            <?php echo $__env->yieldPushContent('scripts'); ?>
        </main>
    </div>

    <?php echo $__env->make('layouts.partials.internal-chat', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>

    <script>
        $(document).ready(function() {
            // Sidebar Toggle Logic
            $('#sidebarToggle').on('click', function() {
                const isMobile = window.innerWidth <= 768;
                if (isMobile) {
                    $('#sidebar').toggleClass('active');
                    $('#sidebarOverlay').fadeToggle(300);
                } else {
                    $('body').toggleClass('sidebar-collapsed');
                }
            });

            $('#sidebarOverlay').on('click', function() {
                $('#sidebar').removeClass('active');
                $(this).fadeOut(300);
            });

            // Initialize Components
            $('.select2-tags').select2({
                theme: 'bootstrap-5',
                tags: true,
                placeholder: 'Select or Type...',
                width: '100%'
            });

            flatpickr("input[type=date]", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "F j, Y",
            });
        });

        // Heartbeat logic
        setInterval(() => {
            fetch("<?php echo e(url('user/activity/heartbeat')); ?>", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Type': 'application/json'
                }
            });
        }, 120000);
    </script>

    <?php echo $__env->yieldContent('scripts'); ?>
</body>

</html><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\layouts\app.blade.php ENDPATH**/ ?>