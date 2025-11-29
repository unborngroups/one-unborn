<!-- Navbar -->

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">

    <div class="container-fluid d-flex justify-content-between align-items-center">

        <!-- Left: Brand + Mobile Toggle -->

        <div class="d-flex align-items-center">

            <!-- Hamburger button for mobile -->

            <button class="btn btn-dark d-md-none me-2" id="sidebarToggle" style="border: none; font-size: 1.5rem; padding: 8px 12px; min-width: 45px; min-height: 45px;">

                <i class="bi bi-list" style="color: white;"></i>

            </button>



            <!-- Logo -->

            <a class="navbar-brand fw-bold d-flex align-items-center" href="<?php echo e(url('/')); ?>">

                <img src="<?php echo e(asset('images/logo.jpg')); ?>" alt="One-Unborn"

                    style="height:52px; width:52px; object-fit:cover; border-radius:4px; margin-right:10px;">

                <span>One-Unborn</span>

            </a>

        </div>



        <!-- Right: Profile Dropdown -->

        <?php if(auth()->guard()->check()): ?>

        <div class="dropdown">

            <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" role="button"

                id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">

                <img src="<?php echo e(asset(optional(Auth::user()->profile)->profile_photo 

    ? Auth::user()->profile->profile_photo 

    : 'images/default.png')); ?>"

                    alt="Profile" class="rounded-circle" width="35" height="35"

                    style="object-fit: cover; border: 2px solid #0d6efd;">

                <span class="ms-2 fw-semibold text-dark"><?php echo e(Auth::user()->name); ?></span>

            </a>

            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">

                <li><a class="dropdown-item" href="<?php echo e(route('profile.view')); ?>"><i class="bi bi-person me-2"></i> View Profile</a></li>

                <li><a class="dropdown-item" href="<?php echo e(route('profile.edit')); ?>"><i class="bi bi-pencil-square me-2"></i> Edit Profile</a></li>

                <li><hr class="dropdown-divider"></li>

                <li>

                    <form method="POST" action="<?php echo e(route('logout')); ?>">

                        <?php echo csrf_field(); ?>

                        <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>

                    </form>

                </li>

            </ul>

        </div>

        <?php endif; ?>

    </div>

</nav>

<?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/layouts/navbar.blade.php ENDPATH**/ ?>