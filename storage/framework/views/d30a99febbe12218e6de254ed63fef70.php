<!-- Navbar -->

<nav class="navbar navbar-expand-lg navbar-dark bg-light shadow-sm">

    <div class="container-fluid d-flex justify-content-between align-items-center">

        <!-- Left: Brand + Mobile Toggle -->

        <div class="d-flex align-items-center">

            <!-- Sidebar toggle button (desktop + mobile) -->

            <button class="btn btn-dark me-2" id="sidebarToggle" style="border: none; font-size: 1.4rem; padding: 6px 10px; min-width: 40px; min-height: 40px;">

                <i id="sidebarToggleIcon" class="bi bi-chevron-left" style="color: white;"></i>

            </button>


            <!-- Logo -->

            <a class="navbar-brand fw-bold d-flex align-items-center" href="<?php echo e(url('/')); ?>">

                <img src="<?php echo e(asset('images/logo1.png')); ?>" alt="One-Unborn" class="navbar-logo"

                    style="height:52px; max-width:150px; object-fit:contain; border-radius:4px; margin-right:10px;">

                <!-- <span>One-Unborn</span> -->

            </a>

        </div>



        <!-- Right: Profile Dropdown -->

        <?php if(auth()->guard()->check()): ?>
        <?php if(isset($clockDisplay) || isset($onlineSince)): ?>
            <span id="onlineStatusTicker" class="me-3 fw-semibold text-white" style="font-size: 15px;" data-login-time="<?php echo e($onlineLoginTimeIso); ?>">
                ⏱
                <?php if(isset($clockDisplay)): ?>
                    <span id="onlineSinceValue"><?php echo e($clockDisplay); ?></span>
                <?php else: ?>
                    <span id="onlineSinceValue">Online since <?php echo e($onlineSince); ?></span>
                <?php endif; ?>
                <?php if(!empty($onlineDurationLabel)): ?>
                    (<span id="onlineDurationLabel"><?php echo e($onlineDurationLabel); ?></span>)
                <?php else: ?>
                    (<span id="onlineDurationLabel">0s</span>)
                <?php endif; ?>
            </span>
        <?php endif; ?>

        <div class="dropdown">

            <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" role="button"

                id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">

                <img src="<?php echo e(asset(optional(Auth::user()->profile)->profile_photo 

    ? Auth::user()->profile->profile_photo 

    : 'images/default.png')); ?>"

                    alt="Profile" class="rounded-circle" width="35" height="35"

                    style="object-fit: cover; border: 2px solid #e7eaeeff;">

                <span class="ms-2 fw-semibold text-white"><?php echo e(Auth::user()->name); ?></span>

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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ticker = document.getElementById('onlineStatusTicker');
        if (!ticker || !ticker.dataset.loginTime) {
            return;
        }

        const durationSpan = document.getElementById('onlineDurationLabel');
        const loginTime = new Date(ticker.dataset.loginTime);

        const formatDuration = (milliseconds) => {
            const totalSeconds = Math.floor(milliseconds / 1000);
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;
            const parts = [];
            if (hours) {
                parts.push(hours + 'h');
            }
            if (minutes) {
                parts.push(minutes + 'm');
            }
            parts.push(seconds + 's');
            return parts.join(' ');
        };

        const updateDuration = () => {
            const elapsed = Math.max(Date.now() - loginTime.getTime(), 0);
            if (durationSpan) {
                durationSpan.textContent = formatDuration(elapsed);
            }
        };

        updateDuration();
        setInterval(updateDuration, 1000);
    });
</script><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\layouts\navbar.blade.php ENDPATH**/ ?>