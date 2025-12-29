

<!-- <div class="sidebar bg-dark-info text-white vh-100 overflow-auto"> -->

    <div class="p-3">

        <!-- <h5 class="text-center mb-4"><i class="bi bi-grid-3x3-gap"></i> ERP Menu</h5> -->



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

                $users = \App\Helpers\TemplateHelper::getUserMenuPermissions('Manage User');

                $userType = \App\Helpers\TemplateHelper::getUserMenuPermissions('User Type');

                $client = \App\Helpers\TemplateHelper::getUserMenuPermissions('Client Master');

                $vendor = \App\Helpers\TemplateHelper::getUserMenuPermissions('Vendor Master');
                $Asset = \App\Helpers\TemplateHelper::getUserMenuPermissions('Asset Master');
                $assetType = \App\Helpers\TemplateHelper::getUserMenuPermissions('Asset Type');
                $makeType = \App\Helpers\TemplateHelper::getUserMenuPermissions('Make Type');

            ?>

            <?php if(($company && $company->can_menu) || ($users && $users->can_menu) || ($userType && $userType->can_menu) || ($client && $client->can_menu) || ($vendor && $vendor->can_menu)): ?>

                <li class="nav-item">

                    <a class="nav-link text-white d-flex justify-content-between align-items-center"

                       data-bs-toggle="collapse" href="#masterMenu" role="button"

                       aria-expanded="<?php echo e(request()->is('companies*') || request()->is('users*') || request()->is('usertypetable*') || request()->is('clients*') || request()->is('vendors*') ? 'true' : 'false'); ?>"

                       aria-controls="masterMenu">

                        <span><i class="bi bi-collection"></i> Masters</span>

                        <i class="bi bi-chevron-down arrow-icon"></i>

                    </a>



                    <div class="collapse <?php echo e(request()->is('companies*') || request()->is('users*') || request()->is('usertypetable*') || request()->is('clients*') || request()->is('vendors*') ? 'show' : ''); ?>" id="masterMenu">

                        <ul class="nav flex-column ms-3 mt-2">

                            <?php if($company && $company->can_menu): ?>

                                <li><a class="nav-link text-white menu-item <?php echo e(request()->is('companies*') ? 'active' : ''); ?>" href="<?php echo e(route('companies.index')); ?>"><i class="bi bi-building"></i> Company Details</a></li>

                            <?php endif; ?>

                            <?php if($users && $users->can_menu): ?>

                                <li><a class="nav-link text-white menu-item <?php echo e(request()->is('users*') ? 'active' : ''); ?>" href="<?php echo e(route('users.index')); ?>"><i class="bi bi-people"></i> Manage User</a></li>

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

                            <!--  -->
                        <?php
                            $assetMasterRoutesAvailable = Route::has('assetmaster.asset_type.index') || Route::has('assetmaster.make_type.index');
                        ?>
                        <?php if($Asset && $Asset->can_menu && $assetMasterRoutesAvailable): ?>
<li>
    <a class="nav-link text-white d-flex justify-content-between align-items-center"
       data-bs-toggle="collapse" href="#assetMasterMenu" role="button"
       aria-expanded="<?php echo e(request()->is('assetmaster/asset_type*') || request()->is('assetmaster/make_type*') ? 'true' : 'false'); ?>"
       aria-controls="assetMasterMenu">
       <span><i class="bi bi-box-seam"></i> Asset Master</span>
       <i class="bi bi-chevron-down arrow-icon"></i>
    </a>

    <div class="collapse <?php echo e(request()->is('assetmaster/*') ? 'show' : ''); ?>" id="assetMasterMenu">
        <ul class="nav flex-column ms-3 mt-1">

            <?php if($assetType && $assetType->can_menu && Route::has('assetmaster.asset_type.index')): ?>
                <li>
                    <a href="<?php echo e(route('assetmaster.asset_type.index')); ?>"
                       class="nav-link text-white menu-item <?php echo e(request()->is('assetmaster/asset_type*') ? 'active' : ''); ?>">
                        <i class="bi bi-tag"></i> Asset Type
                    </a>
                </li>
            <?php endif; ?>

            <?php if($makeType && $makeType->can_menu && Route::has('assetmaster.make_type.index')): ?>
                <li>
                    <a href="<?php echo e(route('assetmaster.make_type.index')); ?>"
                       class="nav-link text-white menu-item <?php echo e(request()->is('assetmaster/make_type*') ? 'active' : ''); ?>">
                        <i class="bi bi-tools"></i> Make Type
                    </a>
                </li>
            <?php endif; ?>

        </ul>
    </div>
</li>

            <?php endif; ?>

                        </ul>

                    </div>

                </li>

            <?php endif; ?>



            

<?php

    $feasibilityMaster = \App\Helpers\TemplateHelper::getUserMenuPermissions('Feasibility Master');

    $purchaseOrder = \App\Helpers\TemplateHelper::getUserMenuPermissions('Purchase Order');
    $proposal = \App\Helpers\TemplateHelper::getUserMenuPermissions('Proposal');
    $smDeliverables = \App\Helpers\TemplateHelper::getUserMenuPermissions('sm Deliverables');


?>

<?php if(($feasibilityMaster && $feasibilityMaster->can_menu) || ($purchaseOrder && $purchaseOrder->can_menu) || ($proposal && $proposal->can_menu) || ($smDeliverables && $smDeliverables->can_menu)): ?>

<li class="nav-item">

    <a class="nav-link text-white d-flex justify-content-between align-items-center"

       data-bs-toggle="collapse" href="#salesMarketingMenu" role="button"

       aria-expanded="<?php echo e(request()->is('sm/feasibility*') || request()->is('sm/purchaseorder*') ? 'true' : 'false'); ?>"

       aria-controls="salesMarketingMenu">

        <span><i class="bi bi-briefcase"></i> Sales & Marketing</span>

        <i class="bi bi-chevron-down arrow-icon"></i>

    </a>

    <div class="collapse <?php echo e(request()->is('sm/feasibility*') || request()->is('sm/purchaseorder*') ? 'show' : ''); ?>" id="salesMarketingMenu">

        <ul class="nav flex-column ms-3 mt-1">



            

            <?php if($feasibilityMaster && $feasibilityMaster->can_menu): ?>

            <li>

                <a class="nav-link text-white d-flex justify-content-between align-items-center"

                   data-bs-toggle="collapse" href="#feasibilityMainMenu" role="button"

                   aria-expanded="<?php echo e(request()->is('sm/feasibility*') ? 'true' : 'false'); ?>"

                   aria-controls="feasibilityMainMenu">

                    <span><i class="bi bi-diagram-3 me-2"></i> Feasibility</span>

                    <i class="bi bi-chevron-down arrow-icon"></i>

                </a>



                <div class="collapse <?php echo e(request()->is('sm/feasibility*') || request()->is('feasibility/create*') ? 'show' : ''); ?>" id="feasibilityMainMenu">

                    <ul class="nav flex-column ms-3">

                        

                        <li>

                            <a class="nav-link text-white menu-item <?php echo e(request()->is('feasibility/create*') ? 'active bg-primary fw-bold' : ''); ?>"

                               href="<?php echo e(route('feasibility.create')); ?>">

                               <i class="bi bi-plus-circle"></i> Add Feasibility

                            </a>
                            <a class="nav-link text-white menu-item <?php echo e(request()->is('feasibility') || request()->is('feasibility/*/edit') ? 'active bg-primary fw-bold' : ''); ?>"

                               href="<?php echo e(route('feasibility.index')); ?>">

                               <i class="bi bi-pencil"></i> Edit Feasibility

                            </a>

                        </li>

                        </li>

                        <?php

                            // Determine which S&M menu item should be active

                            $isSMOpenActive = false;

                            $isSMInProgressActive = false;

                            $isSMClosedActive = false;

                            

                            // Check for direct S&M page routes

                            if (request()->is('sm/feasibility/open')) {

                                $isSMOpenActive = true;

                            } elseif (request()->is('sm/feasibility/inprogress')) {

                                $isSMInProgressActive = true;

                            } elseif (request()->is('sm/feasibility/closed')) {

                                $isSMClosedActive = true;

                            }

                            

                            // For S&M view and edit pages, check the record status

                            if (request()->is('sm/feasibility/*/view') || request()->is('sm/feasibility/*/edit')) {

                                $recordId = request()->segment(3); // Get the ID from URL

                                if ($recordId) {

                                    try {

                                        $record = \App\Models\FeasibilityStatus::find($recordId);

                                        if ($record && $record->status) {

                                            switch ($record->status) {

                                                case 'Open':

                                                    $isSMOpenActive = true;

                                                    break;

                                                case 'InProgress':

                                                    $isSMInProgressActive = true;

                                                    break;

                                                case 'Closed':

                                                    $isSMClosedActive = true;

                                                    break;

                                            }

                                        }

                                    } catch (Exception $e) {

                                        // Fallback - no active state

                                    }

                                }

                            }

                        ?>

                        

                        

                        <li>

                            <a class="nav-link text-white menu-item <?php echo e($isSMOpenActive ? 'active' : ''); ?>"

                               href="<?php echo e(route('sm.feasibility.open')); ?>">

                               <i class="bi bi-hourglass-split me-2"></i> Open

                            </a>

                        </li>

                        

                        <li>

                            <a class="nav-link text-white menu-item <?php echo e($isSMInProgressActive ? 'active' : ''); ?>"

                               href="<?php echo e(route('sm.feasibility.inprogress')); ?>">

                               <i class="bi bi-clock-history me-2"></i> In Progress

                            </a>

                        </li>

                        

                        <li>

                            <a class="nav-link text-white menu-item <?php echo e($isSMClosedActive ? 'active' : ''); ?>"

                               href="<?php echo e(route('sm.feasibility.closed')); ?>">

                               <i class="bi bi-check-circle me-2"></i> Closed

                            </a>

                        </li>

                    </ul>

                </div>

            </li>

            <?php endif; ?>
            
            

            <?php if($proposal && $proposal->can_menu): ?>

            <li>

                <a class="nav-link text-white menu-item <?php echo e(request()->is('sm/proposal*') ? 'active' : ''); ?>" href="<?php echo e(route('sm.proposal.index')); ?>">

                    <span><i class="bi bi-receipt me-2"></i> Proposal</span>

                </a>

            </li>

            <?php endif; ?>

            

            <?php if($purchaseOrder && $purchaseOrder->can_menu): ?>

            <li>

                <a class="nav-link text-white menu-item <?php echo e(request()->is('sm/purchaseorder*') ? 'active' : ''); ?>" href="<?php echo e(route('sm.purchaseorder.index')); ?>">

                    <span><i class="bi bi-receipt me-2"></i> Purchase Order</span>

                </a>

            </li>

            <?php endif; ?>

            <?php
                $isSmDeliverablesOpenActive = false;
                $isSmDeliverablesInProgressActive = false;
                $isSmDeliverablesDeliveryActive = false;

                if (request()->is('sm/deliverables/open')) {
                    $isSmDeliverablesOpenActive = true;
                } elseif (request()->is('sm/deliverables/inprogress')) {
                    $isSmDeliverablesInProgressActive = true;
                } elseif (request()->is('sm/deliverables/delivery')) {
                    $isSmDeliverablesDeliveryActive = true;
                }

                if (request()->is('sm/deliverables/*/view') || request()->is('sm/deliverables/*/edit')) {
                    $recordId = request()->segment(3);
                    if ($recordId) {
                        try {
                            $record = \App\Models\Deliverables::find($recordId);
                            if ($record && $record->status) {
                                switch ($record->status) {
                                    case 'Open':
                                        $isSmDeliverablesOpenActive = true;
                                        break;
                                    case 'InProgress':
                                        $isSmDeliverablesInProgressActive = true;
                                        break;
                                    case 'Delivery':
                                        $isSmDeliverablesDeliveryActive = true;
                                        break;
                                }
                            }
                        } catch (Exception $e) {
                            // Fallback - no active state
                        }
                    }
                }
                $smDeliverablesOpenRoute = url('/sm/deliverables/open');
                $smDeliverablesInProgressRoute = url('/sm/deliverables/inprogress');
                $smDeliverablesDeliveryRoute = url('/sm/deliverables/delivery');
            ?>

            
            <?php if($smDeliverables && $smDeliverables->can_menu): ?>
            <li>

                <a class="nav-link text-white d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#smDeliverablesMenu" role="button" aria-expanded="<?php echo e(request()->is('sm/deliverables*') ? 'true' : 'false'); ?>" aria-controls="smDeliverablesMenu">

                    <span><i class="bi bi-truck me-2"></i> Deliverables</span>

                    <i class="bi bi-chevron-down arrow-icon"></i>

                </a>
                
                <div class="collapse <?php echo e(request()->is('sm/deliverables*') ? 'show' : ''); ?>" id="smDeliverablesMenu">

                    <ul class="nav flex-column ms-3 mt-1">

                        

                        <li>

                            <a class="nav-link text-white menu-item <?php echo e($isSmDeliverablesOpenActive ? 'active' : ''); ?>" href="<?php echo e($smDeliverablesOpenRoute); ?>">

                               <i class="bi bi-hourglass-split me-2"></i> Open

                            </a>

                        </li>

                        

                        <li>

                            <a class="nav-link text-white menu-item <?php echo e($isSmDeliverablesInProgressActive ? 'active' : ''); ?>" href="<?php echo e($smDeliverablesInProgressRoute); ?>">

                               <i class="bi bi-clock-history me-2"></i> In Progress

                            </a>

                        </li>

                        

                        <li>

                            <a class="nav-link text-white menu-item <?php echo e($isSmDeliverablesDeliveryActive ? 'active' : ''); ?>" href="<?php echo e($smDeliverablesDeliveryRoute); ?>">

                               <i class="bi bi-truck-flatbed me-2"></i> Delivered

                            </a>

                        </li>

                    </ul>

                </div>
            </li>
            <?php endif; ?>

        </ul>

    </div>

</li>

<?php endif; ?>
            <!--  -->

<?php

    $operationsFeasibility = \App\Helpers\TemplateHelper::getUserMenuPermissions('operations Feasibility');
    $operationsDeliverables = \App\Helpers\TemplateHelper::getUserMenuPermissions('operations Deliverables');
    $operationsAsset = \App\Helpers\TemplateHelper::getUserMenuPermissions('Asset');
    $operationsRenewals = \App\Helpers\TemplateHelper::getUserMenuPermissions('Renewals');

    // Determine which operations feasibility menu item should be active

    $isFeasibilityOpenActive = false;

    $isFeasibilityInProgressActive = false;

    $isFeasibilityClosedActive = false;

    
    // Check for direct feasibility page routes

    if (request()->is('operations/feasibility/open')) {

        $isFeasibilityOpenActive = true;

    } elseif (request()->is('operations/feasibility/inprogress')) {

        $isFeasibilityInProgressActive = true;

    } elseif (request()->is('operations/feasibility/closed')) {

        $isFeasibilityClosedActive = true;

    }

    
    // Determine which operations deliverables menu item should be active

    $isDeliverablesOpenActive = false;

    $isDeliverablesInProgressActive = false;

    $isDeliverablesDeliveryActive = false;


    // Check for direct deliverables page routes

    if (request()->is('operations/deliverables/open')) {

        $isDeliverablesOpenActive = true;

    } elseif (request()->is('operations/deliverables/inprogress')) {

        $isDeliverablesInProgressActive = true;

    } elseif (request()->is('operations/deliverables/delivery')) {

        $isDeliverablesDeliveryActive = true;

    }

    

    // For feasibility view/edit pages, check the record status

    if (request()->is('operations/feasibility/*/view') || request()->is('operations/feasibility/*/edit')) {

        $recordId = request()->segment(3); // Get the ID from URL

        if ($recordId) {

            try {

                $record = \App\Models\FeasibilityStatus::find($recordId);

                if ($record && $record->status) {

                    switch ($record->status) {

                        case 'Open':

                            $isFeasibilityOpenActive = true;

                            break;

                        case 'InProgress':

                            $isFeasibilityInProgressActive = true;

                            break;

                        case 'Closed':

                            $isFeasibilityClosedActive = true;

                            break;

                    }

                }

            } catch (Exception $e) {

                // Fallback - no active state

            }

        }

    }
    // For deliverables view/edit pages, check the record status

    if (request()->is('operations/deliverables/*/view') || request()->is('operations/deliverables/*/edit')) {

        $recordId = request()->segment(3); // Get the ID from URL

        if ($recordId) {

            try {

                // Assuming you have a Deliverables model with status

                $record = \App\Models\Deliverables::find($recordId);

                if ($record && $record->status) {

                    switch ($record->status) {

                        case 'Open':

                            $isDeliverablesOpenActive = true;

                            break;

                        case 'InProgress':

                            $isDeliverablesInProgressActive = true;

                            break;

                        case 'Delivery':

                            $isDeliverablesDeliveryActive = true;

                            break;

                    }

                }

            } catch (Exception $e) {

                // Fallback - no active state

            }

        }

    }

?>

<?php if(($operationsAsset && $operationsAsset->can_menu) || ($operationsRenewals && $operationsRenewals->can_menu) || ($operationsFeasibility && $operationsFeasibility->can_menu) || ($operationsDeliverables && $operationsDeliverables->can_menu)): ?>

<li class="nav-item">

    <a class="nav-link text-white d-flex justify-content-between align-items-center"

       data-bs-toggle="collapse" href="#operationsMenu" role="button"

       aria-expanded="<?php echo e(request()->is('operations/asset*') || request()->is('operations/renewals*') || request()->is('operations/feasibility*') || request()->is('operations/deliverables*') ? 'true' : 'false'); ?>"

       aria-controls="operationsMenu">

        <span><i class="bi bi-gear-wide-connected"></i> Operations</span>

        <i class="bi bi-chevron-down arrow-icon"></i>

    </a>
    <!--  -->

    <div class="collapse <?php echo e(request()->is('operations/asset*') || request()->is('operations/renewals*') || request()->is('operations/feasibility*') || request()->is('operations/deliverables*') || request()->is('operations/purchaseorder*') ? 'show' : ''); ?>" id="operationsMenu">

        <ul class="nav flex-column ms-3 mt-1">
            <!-- Asset in operations -->

        <li class="nav-item">

                    <a class="nav-link text-white menu-item <?php echo e(request()->is('operations/asset/*') ? 'active' : ''); ?>" href="<?php echo e(route('operations.asset.index')); ?>">
                        <i class="bi bi-gear-fill"></i> Asset

                    </a>

                </li>

                <!-- Renewals in operations -->
                      <li>
                        <a class="nav-link text-white menu-item <?php echo e(request()->is('operations/renewals*') ? 'active' : ''); ?>"
                                   href="<?php echo e(route('operations.renewals.index')); ?>">
                            <i class="bi bi-receipt me-2"></i> Renewals
                        </a>
                        </li>
                          

            <!-- Feasibility Main Menu --> 

            <li>

                <a class="nav-link text-white d-flex justify-content-between align-items-center"

                   data-bs-toggle="collapse" href="#operationsFeasibilityMenu" role="button" aria-expanded="<?php echo e(request()->is('operations/feasibility*') ? 'true' : 'false'); ?>" aria-controls="operationsFeasibilityMenu">

                    <span><i class="bi bi-diagram-3 me-2"></i> Feasibility</span>

                    <i class="bi bi-chevron-down arrow-icon"></i>

                </a>


    <div class="collapse <?php echo e(request()->is('operations/feasibility*') ? 'show' : ''); ?>" id="operationsFeasibilityMenu">

        <ul class="nav flex-column ms-3 mt-1">

               <!-- operations feasibility open menu -->


            <li>

                <a class="nav-link text-white menu-item <?php echo e($isFeasibilityOpenActive ? 'active' : ''); ?>"

                   href="<?php echo e(route('operations.feasibility.open')); ?>">

                   <i class="bi bi-hourglass-split me-2"></i> Open

                </a>

            </li>
               <!-- operations feasibility In progress menu -->


            <li>

                <a class="nav-link text-white menu-item <?php echo e($isFeasibilityInProgressActive ? 'active' : ''); ?>"

                   href="<?php echo e(route('operations.feasibility.inprogress')); ?>">

                   <i class="bi bi-clock-history me-2"></i> In Progress

                </a>

            </li>
                <!-- operations feasibility closed menu -->


            <li>

                <a class="nav-link text-white menu-item <?php echo e($isFeasibilityClosedActive ? 'active' : ''); ?>" href="<?php echo e(route('operations.feasibility.closed')); ?>">

                   <i class="bi bi-check-circle me-2"></i> Closed

                </a>

            </li>

        </ul>

    </div>
            </li>

            <!-- Operations Deliverables Main Menu -->
            <?php if($operationsDeliverables && $operationsDeliverables->can_menu): ?>
            <li>

                <a class="nav-link text-white d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#operationsDeliverablesMenu" role="button" aria-expanded="<?php echo e(request()->is('operations/deliverables*') ? 'true' : 'false'); ?>" aria-controls="operationsDeliverablesMenu">

                    <span><i class="bi bi-truck me-2"></i> Deliverables</span>

                    <i class="bi bi-chevron-down arrow-icon"></i>

                </a>
    <!--  -->


    <div class="collapse <?php echo e(request()->is('operations/deliverables*') ? 'show' : ''); ?>" id="operationsDeliverablesMenu">

        <ul class="nav flex-column ms-3 mt-1">

                       <!-- Operations Deliverables open Menu -->


            <li>

                <a class="nav-link text-white menu-item <?php echo e($isDeliverablesOpenActive ? 'active' : ''); ?>" href="<?php echo e(route('operations.deliverables.open')); ?>">

                   <i class="bi bi-hourglass-split me-2"></i> Open

                </a>

            </li>

            <!-- Operations Deliverables In Progress Menu -->

            <li>

                <a class="nav-link text-white menu-item <?php echo e($isDeliverablesInProgressActive ? 'active' : ''); ?>" href="<?php echo e(route('operations.deliverables.inprogress')); ?>">

                   <i class="bi bi-clock-history me-2"></i> In Progress

                </a>

            </li>

            <!-- Operations Deliverables Delivered Menu -->

            <li>

                <a class="nav-link text-white menu-item <?php echo e($isDeliverablesDeliveryActive ? 'active' : ''); ?>"

                   href="<?php echo e(route('operations.deliverables.delivery')); ?>">

                   <i class="bi bi-truck-flatbed me-2"></i> Delivered
                </a>
            </li>
        </ul>
    </div>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</li>
<?php endif; ?>

            <!-- Finance Dropdown -->

            <?php

                $finance = \App\Helpers\TemplateHelper::getUserMenuPermissions('Accounts');
                $banking = \App\Helpers\TemplateHelper::getUserMenuPermissions('Banking');
                $gst = \App\Helpers\TemplateHelper::getUserMenuPermissions('GST');
                $purchase = \App\Helpers\TemplateHelper::getUserMenuPermissions('Purchases');
                $reports = \App\Helpers\TemplateHelper::getUserMenuPermissions('Reports');
                $sales = \App\Helpers\TemplateHelper::getUserMenuPermissions('Sales');
                $settings = \App\Helpers\TemplateHelper::getUserMenuPermissions('Settings');
                $tds = \App\Helpers\TemplateHelper::getUserMenuPermissions('TDS');
                

            ?>

            <?php if($finance && $finance->can_menu): ?>
                <li class="nav-item">
                    <a class="nav-link text-white d-flex justify-content-between align-items-center"
                       data-bs-toggle="collapse"
                       href="#financeMenu"
                       role="button"
                       aria-expanded="<?php echo e(request()->is('finance/accounts*') || request()->is('finance/sales*') || request()->is('finance/purchases*') || request()->is('finance/gst*') || request()->is('finance/tds*') || request()->is('finance/banking*') || request()->is('finance/reports*') || request()->is('finance/settings*') ? 'true' : 'false'); ?>"
                       aria-controls="financeMenu">
                        <span><i class="bi bi-cash-coin"></i> Finance</span>
                        <i class="bi bi-chevron-down arrow-icon"></i>
                    </a>

                    <div class="collapse <?php echo e(request()->is('finance/accounts*') || request()->is('finance/sales*') || request()->is('finance/purchases*') || request()->is('finance/gst*') || request()->is('finance/tds*') || request()->is('finance/banking*') || request()->is('finance/reports*') || request()->is('finance/settings*') ? 'show' : ''); ?>" id="financeMenu">
                        <ul class="nav flex-column ms-3 mt-1">
                            <li>
                                <a class="nav-link text-white menu-item <?php echo e(request()->is('finance/accounts*') ? 'active' : ''); ?>"
                                   href="<?php echo e(route('finance.accounts.index')); ?>">
                                    <span><i class="bi bi-receipt me-2"></i> Accounts</span>
                                </a>
                            </li>

                            <?php if(Route::has('finance.sales.index')): ?>
                            <li>
                                <a class="nav-link text-white menu-item <?php echo e(request()->is('finance/sales*') ? 'active' : ''); ?>"
                                   href="<?php echo e(route('finance.sales.index')); ?>">
                                   <i class="bi bi-receipt me-2"></i> Sales
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php if(Route::has('finance.purchases.index')): ?>
                            <li>
                                <a class="nav-link text-white menu-item <?php echo e(request()->is('finance/purchases*') ? 'active' : ''); ?>"
                                   href="<?php echo e(route('finance.purchases.index')); ?>">
                                   <i class="bi bi-cart-check me-2"></i> Purchases
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php if(Route::has('finance.gst.index')): ?>
            <li>
                <a class="nav-link text-white menu-item <?php echo e(request()->is('finance/gst*') ? 'active' : ''); ?>"
                   href="<?php echo e(route('finance.gst.index')); ?>">
                   <i class="bi bi-percent me-2"></i> GST
                </a>
            </li>
            <?php endif; ?>

                            <?php if(Route::has('finance.tds.index')): ?>
            <li>
                <a class="nav-link text-white menu-item <?php echo e(request()->is('finance/tds*') ? 'active' : ''); ?>"
                   href="<?php echo e(route('finance.tds.index')); ?>">
                   <i class="bi bi-scissors me-2"></i> TDS
                </a>
            </li>
            <?php endif; ?>

                            <?php if(Route::has('finance.banking.index')): ?>
            <li>
                <a class="nav-link text-white menu-item <?php echo e(request()->is('finance/banking*') ? 'active' : ''); ?>"
                   href="<?php echo e(route('finance.banking.index')); ?>">
                   <i class="bi bi-bank me-2"></i> Banking
                </a>
            </li>
            <?php endif; ?>

            

                            <?php if(Route::has('finance.reports.index')): ?>
            <li>
                <a class="nav-link text-white menu-item <?php echo e(request()->is('finance/reports*') ? 'active' : ''); ?>"
                   href="<?php echo e(route('finance.reports.index')); ?>">
                   <i class="bi bi-bar-chart-line me-2"></i> Reports
                </a>
            </li>
            <?php endif; ?>

                            <?php if(Route::has('finance.settings.index')): ?>
            <li>
                <a class="nav-link text-white menu-item <?php echo e(request()->is('finance/settings*') ? 'active' : ''); ?>"
                   href="<?php echo e(route('finance.settings.index')); ?>">
                   <i class="bi bi-gear me-2"></i> Settings
                </a>
            </li>
            <?php endif; ?>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

                     

            <?php

                $compliance = \App\Helpers\TemplateHelper::getUserMenuPermissions('Compliance');

            ?>

            <?php if($compliance && $compliance->can_menu): ?>
                <li class="nav-item">

                    <a class="nav-link text-white menu-item <?php echo e(request()->is('compliance/*') ? 'active' : ''); ?>" href="<?php echo e(url('/compliance')); ?>">

                        <i class="bi bi-shield-check"></i> Compliance

                    </a>

                </li>

            <?php endif; ?>

            

            <?php

                $assurance = \App\Helpers\TemplateHelper::getUserMenuPermissions('Assurance');

            ?>

            <?php if($assurance && $assurance->can_menu): ?>
                <li class="nav-item">

                    <a class="nav-link text-white menu-item <?php echo e(request()->is('assurance/*') ? 'active' : ''); ?>" href="<?php echo e(url('/assurance')); ?>">

                        <i class="bi bi-people-fill"></i> Assurance

                    </a>

                </li>

            <?php endif; ?>

             

            <?php

                $hr = \App\Helpers\TemplateHelper::getUserMenuPermissions('HR');

            ?>

            <?php if($hr && $hr->can_menu): ?>
                <li class="nav-item">

                    <a class="nav-link text-white menu-item <?php echo e(request()->is('hr/*') ? 'active' : ''); ?>" href="<?php echo e(url('/hr')); ?>">

                        <i class="bi bi-people-fill"></i> HR

                    </a>

                </li>

            <?php endif; ?>

             

            <?php

                $training = \App\Helpers\TemplateHelper::getUserMenuPermissions('Training');
            ?>

            <?php if($training && $training->can_menu): ?>

                <li class="nav-item">

                    <a class="nav-link text-white menu-item <?php echo e(request()->is('training/*') ? 'active' : ''); ?>" href="<?php echo e(url('/training')); ?>">
                        <i class="bi bi-journal-bookmark"></i> Training

                    </a>

                </li>

            <?php endif; ?>

             

            <?php

                $admin = \App\Helpers\TemplateHelper::getUserMenuPermissions('Admin');
            ?>

            <?php if($admin && $admin->can_menu): ?>

                <li class="nav-item">

                    <a class="nav-link text-white menu-item <?php echo e(request()->is('admin/*') ? 'active' : ''); ?>" href="<?php echo e(url('/admin')); ?>">

                        <i class="bi bi-gear-fill"></i> Admin

                    </a>

                </li>

            <?php endif; ?>

           
             

            <?php

                $strategy = \App\Helpers\TemplateHelper::getUserMenuPermissions('Strategy');
            ?>

            <?php if($strategy && $strategy->can_menu): ?>

                <li class="nav-item">

                    <a class="nav-link text-white menu-item <?php echo e(request()->is('strategy/*') ? 'active' : ''); ?>" href="<?php echo e(url('/strategy')); ?>">
                        <i class="bi bi-graph-up"></i> Strategy

                    </a>

                </li>

            <?php endif; ?>

            

            

            <?php

                $template = \App\Helpers\TemplateHelper::getUserMenuPermissions('Template Masters');

                $menu = \App\Helpers\TemplateHelper::getUserMenuPermissions('Manage Menu');

                $companySettings = \App\Helpers\TemplateHelper::getUserMenuPermissions('Company Settings');

                $SystemSettings = \App\Helpers\TemplateHelper::getUserMenuPermissions('System Settings');

                $whatsappSettings = \App\Helpers\TemplateHelper::getUserMenuPermissions('WhatsApp Settings');

            ?>

            <?php if(($template && $template->can_menu) || ($menu && $menu->can_menu) || ($companySettings && $companySettings->can_menu)): ?>

                <li class="nav-item">

                    <a class="nav-link text-white d-flex justify-content-between align-items-center"

                       data-bs-toggle="collapse" href="#systemMenu" role="button"

                       aria-expanded="<?php echo e(request()->is('emails*') || request()->is('menus*') || request()->is('company-settings*') || request()->is('system-settings*') ? 'true' : 'false'); ?>"

                       aria-controls="systemMenu">

                        <span><i class="bi bi-gear"></i> System</span>

                        <i class="bi bi-chevron-down arrow-icon"></i>

                    </a>



                    <div class="collapse <?php echo e(request()->is('emails*') || request()->is('menus*') || request()->is('company-settings*') || request()->is('system-settings*') ? 'show' : ''); ?>" id="systemMenu">

                        <ul class="nav flex-column ms-3 mt-2">

                            <?php if($template && $template->can_menu): ?>

                                <li><a class="nav-link text-white menu-item <?php echo e(request()->is('emails*') ? 'active' : ''); ?>" href="<?php echo e(route('emails.index')); ?>"><i class="bi bi-envelope"></i> Template Master</a></li>

                            <?php endif; ?>

                            <?php if($menu && $menu->can_menu): ?>

                                <li><a class="nav-link text-white menu-item <?php echo e(request()->is('menus*') ? 'active' : ''); ?>" href="<?php echo e(route('menus.index')); ?>"><i class="bi bi-list-check"></i> Manage Menu</a></li>

                            <?php endif; ?>

                            <?php if($companySettings && $companySettings->can_menu): ?>

                                <li><a class="nav-link text-white menu-item <?php echo e(request()->is('company-settings*') ? 'active' : ''); ?>" href="<?php echo e(route('settings.company')); ?>"><i class="bi bi-building"></i> Company Settings</a></li>

                                 <?php endif; ?>

                            <?php if($SystemSettings && $SystemSettings->can_menu): ?>
                            

                                <li><a class="nav-link text-white menu-item <?php echo e(request()->is('system-settings*') ? 'active' : ''); ?>" href="<?php echo e(route('settings.system')); ?>"><i class="bi bi-sliders"></i> System Settings</a></li>

                            <?php endif; ?>
                            
                            <?php if($whatsappSettings && $whatsappSettings->can_menu): ?>
                                <li><a class="nav-link text-white menu-item <?php echo e(request()->is('whatsapp-settings*') ? 'active' : ''); ?>" href="<?php echo e(route('settings.whatsapp')); ?>"><i class="bi bi-sliders"></i> WhatsApp Settings</a></li>
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

<!-- </div> -->





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

<?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/layouts/partials/fullmenu.blade.php ENDPATH**/ ?>