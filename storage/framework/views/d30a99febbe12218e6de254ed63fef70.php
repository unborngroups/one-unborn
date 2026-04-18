<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        
        <div class="navbar-left d-flex align-items-center">
            <button class="btn-sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
                <i id="sidebarToggleIcon" class="bi bi-list"></i>
            </button>
            <a class="navbar-brand-custom" href="<?php echo e(url('/')); ?>">
                <img src="<?php echo e(asset('images/logo1.png')); ?>" alt="Unborn Logo">
            </a>
        </div>

        <div class="navbar-center d-none d-lg-flex">
            <?php if(auth()->guard()->check()): ?>
            <div id="onlineStatusTicker" class="time-badge">
                <span class="status-indicator"></span>
                <span class="time-text">
                    <i class="bi bi-clock me-1"></i>
                    <span id="onlineSinceValue"><?php echo e($clockDisplay ?? 'Loading...'); ?></span>
                    <span class="duration-label ms-1" id="onlineDurationLabel">(<?php echo e($onlineDurationLabel ?? '0s'); ?>)</span>
                </span>
            </div>
            <?php endif; ?>
        </div>

        <div class="navbar-right">
            <?php if(auth()->guard()->check()): ?>
            <div class="dropdown">
                <a class="profile-pill dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="profile-info d-none d-md-block text-end me-2">
                        <span class="user-name"><?php echo e(Auth::user()->name); ?></span>
                        <span class="user-role">Super Admin</span>
                    </div>
                    <img src="<?php echo e(asset(optional(Auth::user()->profile)->profile_photo ?? 'images/default.png')); ?>" alt="Profile" class="avatar">
                </a>
                <ul class="dropdown-menu dropdown-menu-end profile-dropdown-menu">
                    <li><a class="dropdown-item" href="<?php echo e(route('profile.view')); ?>"><i class="bi bi-person me-2"></i> Profile</a></li>
                    <li><a class="dropdown-item" href="<?php echo e(route('profile.edit')); ?>"><i class="bi bi-gear me-2"></i> Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button class="dropdown-item text-danger"><i class="bi bi-power me-2"></i> Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
  /* --- Global Navbar Polish --- */
.navbar {
    background: rgba(255, 255, 255, 0.8) !important;
    backdrop-filter: blur(10px);
    border-bottom: 1px solid #e2e8f0;
    height: 65px;
    padding: 0 1rem;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1040;
}

/* --- Left Side: Logo & Toggle --- */
.btn-sidebar-toggle {
    background: #f1f5f9;
    border: none;
    color: #475569;
    border-radius: 8px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    margin-right: 15px;
}

.btn-sidebar-toggle:hover {
    background: #e2e8f0;
    color: #0f172a;
}

.navbar-brand-custom img {
    height: 32px;
    width: auto;
}

/* --- Center: Time Badge --- */
.time-badge {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 6px 16px;
    border-radius: 50px;
    display: flex;
    align-items: center;
    font-size: 0.85rem;
    color: #64748b;
}

.status-indicator {
    width: 8px;
    height: 8px;
    background: #22c55e;
    border-radius: 50%;
    margin-right: 10px;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15);
    animation: pulse-green 2s infinite;
}

@keyframes pulse-green {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.5); }
    70% { transform: scale(1); box-shadow: 0 0 0 5px rgba(34, 197, 94, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
}

/* --- Right Side: Profile Pill --- */
.profile-pill {
    display: flex;
    align-items: center;
    text-decoration: none;
    padding: 4px;
    border-radius: 50px;
    transition: background 0.2s;
}

.profile-pill:hover { background: #f1f5f9; }

.user-name {
    display: block;
    font-weight: 600;
    font-size: 0.9rem;
    color: #1e293b;
    line-height: 1;
}

.user-role {
    font-size: 0.75rem;
    color: #64748b;
}

.avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    object-fit: cover;
}

/* --- Dropdown Polish --- */
.profile-dropdown-menu {
    border: none;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    border-radius: 12px;
    padding: 10px;
    margin-top: 15px !important;
}

.dropdown-item {
    border-radius: 6px;
    padding: 8px 12px;
    font-size: 0.9rem;
}

</style><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\layouts\navbar.blade.php ENDPATH**/ ?>