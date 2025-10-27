<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container-fluid">
        <!-- Hamburger button for mobile -->
        <button class="btn btn-dark d-md-none" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>

        <a class="navbar-brand fw-bold ms-2 d-flex align-items-center" href="<?php echo e(url('/')); ?>">
            <img src="<?php echo e(asset('images/logo.jpg')); ?>" alt="One-Unborn" style="height:32px; width:32px; object-fit:cover; border-radius:4px; margin-right:8px;">
            <span>One-Unborn</span>
        </a>

        <div class="d-flex">
            <span class="me-3 text-muted">
                <!-- Logged in as: <?php echo e(Auth::user()->name ?? 'Guest'); ?> -->
            </span>
        </div>
    </div>
</nav>

<?php /**PATH F:\xampp\htdocs\new\multipleuserpage\resources\views/layouts/navbar.blade.php ENDPATH**/ ?>