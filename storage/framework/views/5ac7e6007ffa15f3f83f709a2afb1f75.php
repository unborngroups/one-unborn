<!-- Sidebar -->
<aside id="sidebar" class="bg-dark text-white p-3">
    <h4 class="text-center mb-4">Menu</h4>
    

    <?php if(Auth::check()): ?>
        <?php
            $user = Auth::user();
            $role = strtolower(optional($user->userType)->name);
            $menus = \App\Http\Controllers\Controller::getUserMenus();

        ?>

        
        <?php if($user->is_superuser || in_array($role, ['superadmin', 'admin'])): ?>
            <?php echo $__env->make('layouts.partials.fullmenu', ['menus' => $menus], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <?php elseif($user->profile_created): ?>
            
            <?php echo $__env->make('layouts.partials.fullmenu', ['menus' => $menus], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <?php else: ?>
            <?php echo $__env->make('layouts.partials.createprofilemenu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>
    <?php endif; ?>
</aside>
<?php /**PATH F:\xampp\htdocs\new\multipleuserpage\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>