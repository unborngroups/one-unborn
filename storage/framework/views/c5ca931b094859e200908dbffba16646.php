
<div class="sidebar bg-dark text-white vh-100 overflow-auto" style="width: 250px; position: fixed; top: 0; left: 0;">
    <div class="p-3">
        <h5 class="text-center mb-4"><i class="bi bi-grid-3x3-gap"></i> ERP Menu</h5>

        <ul class="nav flex-column">

            
            <?php
                $dashboard = \App\Helpers\TemplateHelper::getUserMenuPermissions('Dashboard');
            ?>
            <?php if($dashboard && $dashboard->can_menu): ?>
                <li class="nav-item">
                    <a class="nav-link text-white menu-item <?php echo e(request()->is('welcome') ? 'active' : ''); ?>" href="<?php echo e(url('/welcome')); ?>">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
            <?php endif; ?>

            
            <?php
                $company = \App\Helpers\TemplateHelper::getUserMenuPermissions('Company Details');
                $users = \App\Helpers\TemplateHelper::getUserMenuPermissions('Manage Users');
                $userType = \App\Helpers\TemplateHelper::getUserMenuPermissions('User Type');
                $client = \App\Helpers\TemplateHelper::getUserMenuPermissions('Client Master');
                $vendor = \App\Helpers\TemplateHelper::getUserMenuPermissions('Vendor Master');
            ?>
            <?php if(($company && $company->can_menu) || ($users && $users->can_menu) || ($userType && $userType->can_menu) || ($client && $client->can_menu) || ($vendor && $vendor->can_menu)): ?>
                <li class="nav-item">
                    <a class="nav-link text-white d-flex justify-content-between align-items-center"
                       data-bs-toggle="collapse" href="#masterMenu" role="button"
                       aria-expanded="<?php echo e(request()->is('companies*') || request()->is('users*') || request()->is('usertypetable*') || request()->is('clients*') || request()->is('vendors*') ? 'true' : 'false'); ?>"
                       aria-controls="masterMenu">
                        <span><i class="bi bi-collection"></i> Masters</span>
                        <i class="bi bi-chevron-down small"></i>
                    </a>

                    <div class="collapse <?php echo e(request()->is('companies*') || request()->is('users*') || request()->is('usertypetable*') || request()->is('clients*') || request()->is('vendors*') ? 'show' : ''); ?>" id="masterMenu">
                        <ul class="nav flex-column ms-3 mt-2">
                            <?php if($company && $company->can_menu): ?>
                                <li><a class="nav-link text-white menu-item <?php echo e(request()->is('companies*') ? 'active' : ''); ?>" href="<?php echo e(route('companies.index')); ?>"><i class="bi bi-building"></i> Company Details</a></li>
                            <?php endif; ?>
                            <?php if($users && $users->can_menu): ?>
                                <li><a class="nav-link text-white menu-item <?php echo e(request()->is('users*') ? 'active' : ''); ?>" href="<?php echo e(route('users.index')); ?>"><i class="bi bi-people"></i> Manage Users</a></li>
                            <?php endif; ?>
                            <?php if($userType && $userType->can_menu): ?>
                                <li><a class="nav-link text-white menu-item <?php echo e(request()->is('usertypetable*') ? 'active' : ''); ?>" href="<?php echo e(route('usertypetable.index')); ?>"><i class="bi bi-person-badge"></i> User Type</a></li>
                            <?php endif; ?>
                            <?php if($client && $client->can_menu): ?>
                                <li><a class="nav-link text-white menu-item <?php echo e(request()->is('clients*') ? 'active' : ''); ?>" href="<?php echo e(route('clients.index')); ?>"><i class="bi bi-person-workspace"></i> Client Master</a></li>
                            <?php endif; ?>
                            <?php if($vendor && $vendor->can_menu): ?>
                                <li><a class="nav-link text-white menu-item <?php echo e(request()->is('vendors*') ? 'active' : ''); ?>" href="<?php echo e(route('vendors.index')); ?>"><i class="bi bi-truck"></i> Vendor Master</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            
            <?php
                $feasibility = \App\Helpers\TemplateHelper::getUserMenuPermissions('Feasibility');
            ?>
            <?php if($feasibility && $feasibility->can_menu): ?>
                <li class="nav-item">
                    <a class="nav-link text-white d-flex justify-content-between align-items-center"
                       data-bs-toggle="collapse" href="#operationsMenu" role="button"
                       aria-expanded="<?php echo e(request()->is('feasibility*') ? 'true' : 'false'); ?>"
                       aria-controls="operationsMenu">
                        <span><i class="bi bi-tools"></i>  Sales & Marketing</span>
                        <i class="bi bi-chevron-down small"></i>
                    </a>

                    <div class="collapse <?php echo e(request()->is('feasibility*') ? 'show' : ''); ?>" id="operationsMenu">
                        <ul class="nav flex-column ms-3 mt-2">
                            <li><a class="nav-link text-white menu-item <?php echo e(request()->is('feasibility*') ? 'active' : ''); ?>" href="<?php echo e(route('feasibility.index')); ?>"><i class="bi bi-diagram-3"></i> Feasibility</a></li>
                           <li><a class="nav-link text-white menu-item <?php echo e(request()->is('feasibility-status/open') ? 'active' : ''); ?>" 
                          href="<?php echo e(route('feasibility.status.index', ['status' => 'Open'])); ?>">
                        <i class="bi bi-hourglass-split"></i> Open
                       </a></li>

                          <li><a class="nav-link text-white menu-item <?php echo e(request()->is('feasibility-status/inprogress') ? 'active' : ''); ?>" 
                                 href="<?php echo e(route('feasibility.status.index', ['status' => 'InProgress'])); ?>">
                             <i class="bi bi-clock-history"></i>  In Progress
                          </a></li>

                          <li><a class="nav-link text-white menu-item <?php echo e(request()->is('feasibility-status/closed') ? 'active' : ''); ?>" 
                                href="<?php echo e(route('feasibility.status.index', ['status' => 'Closed'])); ?>">
                             <i class="bi bi-check-circle"></i>  Closed
                          </a></li>

   
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            
            <?php
                $finance = \App\Helpers\TemplateHelper::getUserMenuPermissions('Tax & Invoice Settings');
            ?>
            <?php if($finance && $finance->can_menu): ?>
                <li class="nav-item">
                    <a class="nav-link text-white d-flex justify-content-between align-items-center"
                       data-bs-toggle="collapse" href="#financeMenu" role="button"
                       aria-expanded="<?php echo e(request()->is('tax.invoice') ? 'true' : 'false'); ?>"
                       aria-controls="financeMenu">
                        <span><i class="bi bi-cash-coin"></i> Finance</span>
                        <i class="bi bi-chevron-down small"></i>
                    </a>

                    <div class="collapse <?php echo e(request()->routeIs('tax.invoice') ? 'show' : ''); ?>" id="financeMenu">
                        <ul class="nav flex-column ms-3 mt-2">
                            <li><a class="nav-link text-white menu-item <?php echo e(request()->routeIs('tax.invoice') ? 'active' : ''); ?>" href="<?php echo e(route('tax.invoice')); ?>"><i class="bi bi-receipt"></i> Tax & Invoice Settings</a></li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            
            <?php
                $template = \App\Helpers\TemplateHelper::getUserMenuPermissions('Template Master');
                $menu = \App\Helpers\TemplateHelper::getUserMenuPermissions('Manage Menu');
                $commonSettings = \App\Helpers\TemplateHelper::getUserMenuPermissions('Common Settings');
            ?>
            <?php if(($template && $template->can_menu) || ($menu && $menu->can_menu) || ($commonSettings && $commonSettings->can_menu)): ?>
                <li class="nav-item">
                    <a class="nav-link text-white d-flex justify-content-between align-items-center"
                       data-bs-toggle="collapse" href="#systemMenu" role="button"
                       aria-expanded="<?php echo e(request()->is('emails*') || request()->is('menus*') || request()->is('company-settings*') || request()->is('system-settings*') ? 'true' : 'false'); ?>"
                       aria-controls="systemMenu">
                        <span><i class="bi bi-gear"></i> System</span>
                        <i class="bi bi-chevron-down small"></i>
                    </a>

                    <div class="collapse <?php echo e(request()->is('emails*') || request()->is('menus*') || request()->is('company-settings*') || request()->is('system-settings*') ? 'show' : ''); ?>" id="systemMenu">
                        <ul class="nav flex-column ms-3 mt-2">
                            <?php if($template && $template->can_menu): ?>
                                <li><a class="nav-link text-white menu-item <?php echo e(request()->is('emails*') ? 'active' : ''); ?>" href="<?php echo e(route('emails.index')); ?>"><i class="bi bi-envelope"></i> Template Master</a></li>
                            <?php endif; ?>
                            <?php if($menu && $menu->can_menu): ?>
                                <li><a class="nav-link text-white menu-item <?php echo e(request()->is('menus*') ? 'active' : ''); ?>" href="<?php echo e(route('menus.index')); ?>"><i class="bi bi-list-check"></i> Manage Menu</a></li>
                            <?php endif; ?>
                            <?php if($commonSettings && $commonSettings->can_menu): ?>
                                <li><a class="nav-link text-white menu-item <?php echo e(request()->is('company-settings*') ? 'active' : ''); ?>" href="<?php echo e(route('company.settings')); ?>"><i class="bi bi-building"></i> Company Settings</a></li>
                                <li><a class="nav-link text-white menu-item <?php echo e(request()->is('system-settings*') ? 'active' : ''); ?>" href="<?php echo e(route('system.settings')); ?>"><i class="bi bi-sliders"></i> System Settings</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            
            <li class="nav-item mt-4">
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>


<style>
    .menu-item.active {
        background-color: #0d6efd;
        border-radius: 5px;
        color: #fff !important;
    }
    .menu-item:hover {
        background-color: #0b5ed7;
        color: #fff !important;
    }
</style>
<?php /**PATH F:\xampp\htdocs\new\multipleuserpage\resources\views/layouts/partials/fullmenu.blade.php ENDPATH**/ ?>