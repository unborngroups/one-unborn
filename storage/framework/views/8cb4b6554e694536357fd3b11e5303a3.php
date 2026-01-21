
<?php
    $dashboard = \App\Helpers\TemplateHelper::getUserMenuPermissions('Dashboard');

    // Masters dropdown permissions
    $company = \App\Helpers\TemplateHelper::getUserMenuPermissions('Company Details');
    $users = \App\Helpers\TemplateHelper::getUserMenuPermissions('Manage User');
    $userType = \App\Helpers\TemplateHelper::getUserMenuPermissions('User Type');
    $client = \App\Helpers\TemplateHelper::getUserMenuPermissions('Client Master');
    $vendor = \App\Helpers\TemplateHelper::getUserMenuPermissions('Vendor Master');
    $Asset = \App\Helpers\TemplateHelper::getUserMenuPermissions('Asset Master');
    $assetType = \App\Helpers\TemplateHelper::getUserMenuPermissions('Asset Master', 'Asset Type');
    $makeType = \App\Helpers\TemplateHelper::getUserMenuPermissions('Asset Master', 'Make Type');
    $modelType = \App\Helpers\TemplateHelper::getUserMenuPermissions('Asset Master', 'Model Type');

?>

            <?php if($dashboard && $dashboard->can_menu): ?>

                <li class="nav-item">

                    <a class="nav-link text-white menu-item <?php echo e(request()->is('welcome') ? 'active' : ''); ?>" href="<?php echo e(url('/welcome')); ?>"> 

                        <i class="bi bi-speedometer2"></i> Dashboard

                    </a>

                </li>
                        </ul>
                    </div>
                </li>
                <?php endif; ?>

            <?php if(($company && $company->can_menu) || ($users && $users->can_menu) || ($userType && $userType->can_menu) || ($client && $client->can_menu) || ($vendor && $vendor->can_menu)): ?>

                <li class="nav-item">

                    <details class="sidebar-dropdown" <?php echo e(request()->is('companies*') || request()->is('users*') || request()->is('usertypetable*') || request()->is('clients*') || request()->is('vendors*') ? 'open' : ''); ?>>

                        <summary class="nav-link text-white d-flex justify-content-between align-items-center" style="background:#ff9671;">

                            <span><i class="bi bi-collection"></i> Masters</span>

                            <i class="bi bi-chevron-down arrow-icon"></i>

                        </summary>

                        <ul class="nav flex-column ms-3 mt-2">

                            <?php if($company && $company->can_menu): ?>

                                <li><a class="nav-link text-white menu-item <?php echo e(request()->is('companies*') ? 'active' : ''); ?>" href="<?php echo e(route('companies.index')); ?>"><i class="bi bi-building"></i> Company Details</a></li>

                            <?php endif; ?>

                            <!-- <?php if($users && $users->can_menu): ?>

                                <li><a class="nav-link text-white menu-item <?php echo e(request()->is('users*') ? 'active' : ''); ?>" href="<?php echo e(route('users.index')); ?>"><i class="bi bi-people"></i> Manage User</a></li>

                            <?php endif; ?> -->

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
                            $assetMasterRoutesAvailable = Route::has('assetmaster.asset_type.index') || Route::has('assetmaster.make_type.index') || Route::has('assetmaster.model_type.index');
                        ?>
                        <?php if($Asset && $Asset->can_menu && $assetMasterRoutesAvailable): ?>
<li class="nav-item">
    <details class="sidebar-dropdown" <?php echo e(request()->is('assetmaster/*') ? 'open' : ''); ?>>
        <summary class="nav-link text-white d-flex justify-content-between align-items-center menu-sales">
            <span><i class="bi bi-box-seam"></i> Asset Master</span>
            <i class="bi bi-chevron-down arrow-icon"></i>
        </summary>
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
            <?php if($modelType && $modelType->can_menu && Route::has('assetmaster.model_type.index')): ?>
                <li>
                    <a href="<?php echo e(route('assetmaster.model_type.index')); ?>"
                       class="nav-link text-white menu-item <?php echo e(request()->is('assetmaster/model_type*') ? 'active' : ''); ?>">
                        <i class="bi bi-tools"></i> Model Type
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </details>
</li>

            <?php endif; ?>

                        </ul>

                    </div>

                </li>

            <?php endif; ?>



            

<?php

    $feasibilityMaster = \App\Helpers\TemplateHelper::getUserMenuPermissions('Feasibility Master');
    // sub-section privileges for Sales & Marketing Feasibility
    $feasibilityMasterOpen = \App\Helpers\TemplateHelper::getUserMenuPermissions('Feasibility Master', 'SM Feasibility Open');
    $feasibilityMasterInProgress = \App\Helpers\TemplateHelper::getUserMenuPermissions('Feasibility Master', 'SM Feasibility In Progress');
    $feasibilityMasterClosed = \App\Helpers\TemplateHelper::getUserMenuPermissions('Feasibility Master', 'SM Feasibility Closed');


    $purchaseOrder = \App\Helpers\TemplateHelper::getUserMenuPermissions('Purchase Order');
    $proposal = \App\Helpers\TemplateHelper::getUserMenuPermissions('Proposal');
    $smDeliverables = \App\Helpers\TemplateHelper::getUserMenuPermissions('sm Deliverables');

    // sub-section privileges for Sales & Marketing Deliverables
    $smDeliverablesOpen = \App\Helpers\TemplateHelper::getUserMenuPermissions('sm Deliverables', 'SM Deliverables Open');
    $smDeliverablesInProgress = \App\Helpers\TemplateHelper::getUserMenuPermissions('sm Deliverables', 'SM Deliverables In Progress');
    $smDeliverablesDelivery = \App\Helpers\TemplateHelper::getUserMenuPermissions('sm Deliverables', 'SM Deliverables Delivery');
    $smDeliverablesAcceptance = \App\Helpers\TemplateHelper::getUserMenuPermissions('sm Deliverables', 'SM Deliverables Acceptance');
?>

<?php if(($feasibilityMaster && $feasibilityMaster->can_menu) || ($feasibilityMasterOpen && $feasibilityMasterOpen->can_menu) || ($feasibilityMasterInProgress && $feasibilityMasterInProgress->can_menu) || ($feasibilityMasterClosed && $feasibilityMasterClosed->can_menu) 
|| ($purchaseOrder && $purchaseOrder->can_menu) || ($proposal && $proposal->can_menu) || ($smDeliverables && $smDeliverables->can_menu || ($smDeliverablesOpen && $smDeliverablesOpen->can_menu) || ($smDeliverablesInProgress && $smDeliverablesInProgress->can_menu) 
|| ($smDeliverablesDelivery && $smDeliverablesDelivery->can_menu) || ($smDeliverablesAcceptance && $smDeliverablesAcceptance->can_menu))): ?>
<li class="nav-item ">

    <details class="sidebar-dropdown" <?php echo e(request()->is('sm/feasibility*') || request()->is('sm/purchaseorder*') || request()->is('sm/deliverables*') ? 'open' : ''); ?>>

        <summary class="nav-link text-white d-flex justify-content-between align-items-center bg-primary">

            <span><i class="bi bi-briefcase"></i> Sales & Marketing</span>

            <i class="bi bi-chevron-down arrow-icon"></i>

        </summary>

        <ul class="nav flex-column ms-3 mt-1">



            

            <?php if($feasibilityMaster && $feasibilityMaster->can_menu): ?>

            <li class="nav-item">

                <details class="sidebar-dropdown" <?php echo e(request()->is('sm/feasibility*') || request()->is('feasibility/create*') ? 'open' : ''); ?>>

                    <summary class="nav-link text-white d-flex justify-content-between align-items-center">

                        <span><i class="bi bi-diagram-3 me-2"></i> Feasibility</span>

                        <i class="bi bi-chevron-down arrow-icon"></i>

                    </summary>

                    <ul class="nav flex-column ms-3">

                        
                        <?php if($feasibilityMaster && $feasibilityMaster->can_menu): ?>
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
                        <?php endif; ?>
                        

                           

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

                        
                        <?php if($feasibilityMasterOpen && $feasibilityMasterOpen->can_menu): ?>

                        <li>

                            <a class="nav-link text-white menu-item <?php echo e($isSMOpenActive ? 'active' : ''); ?>"

                               href="<?php echo e(route('sm.feasibility.open')); ?>">

                               <i class="bi bi-hourglass-split me-2"></i> Open

                            </a>

                        </li>
                        <?php endif; ?>

                        
                        <?php if($feasibilityMasterInProgress && $feasibilityMasterInProgress->can_menu): ?>

                        <li>

                            <a class="nav-link text-white menu-item <?php echo e($isSMInProgressActive ? 'active' : ''); ?>"

                               href="<?php echo e(route('sm.feasibility.inprogress')); ?>">

                               <i class="bi bi-clock-history me-2"></i> In Progress

                            </a>

                        </li>
                        <?php endif; ?>

                        
                        <?php if($feasibilityMasterClosed && $feasibilityMasterClosed->can_menu): ?>

                        <li>

                            <a class="nav-link text-white menu-item <?php echo e($isSMClosedActive ? 'active' : ''); ?>"

                               href="<?php echo e(route('sm.feasibility.closed')); ?>">

                               <i class="bi bi-check-circle me-2"></i> Closed

                            </a>

                        </li>
                        <?php endif; ?>

                    </ul>

                </details>

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
                $isSmDeliverablesAcceptanceActive = false;

                if (request()->is('sm/deliverables/open')) {
                    $isSmDeliverablesOpenActive = true;
                } elseif (request()->is('sm/deliverables/inprogress')) {
                    $isSmDeliverablesInProgressActive = true;
                } elseif (request()->is('sm/deliverables/delivery')) {
                    $isSmDeliverablesDeliveryActive = true;
                } elseif (request()->is('sm/deliverables/acceptance') || request()->is('sm/deliverables/*/acceptance')) {
                    $isSmDeliverablesAcceptanceActive = true;
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
                $smDeliverablesAcceptanceRoute = url('/sm/deliverables/acceptance');
            ?>

            
            <?php if($smDeliverables && $smDeliverables->can_menu): ?>
            <li class="nav-item">

                <details class="sidebar-dropdown" <?php echo e(request()->is('sm/deliverables*') ? 'open' : ''); ?>>

                    <summary class="nav-link text-white d-flex justify-content-between align-items-center">

                        <span><i class="bi bi-truck me-2"></i> Deliverables</span>

                        <i class="bi bi-chevron-down arrow-icon"></i>

                    </summary>

                    <ul class="nav flex-column ms-3 mt-1">

                        
                        <?php if($smDeliverablesOpen && $smDeliverablesOpen->can_menu): ?>

                        <li>

                            <a class="nav-link text-white menu-item <?php echo e($isSmDeliverablesOpenActive ? 'active' : ''); ?>" href="<?php echo e($smDeliverablesOpenRoute); ?>">

                               <i class="bi bi-hourglass-split me-2"></i> Open

                            </a>

                        </li>
                        <?php endif; ?>

                        
                        <?php if($smDeliverablesInProgress && $smDeliverablesInProgress->can_menu): ?>
                            
                        <li>

                            <a class="nav-link text-white menu-item <?php echo e($isSmDeliverablesInProgressActive ? 'active' : ''); ?>" href="<?php echo e($smDeliverablesInProgressRoute); ?>">

                               <i class="bi bi-clock-history me-2"></i> In Progress

                            </a>

                        </li>
                        <?php endif; ?>

                        
                        <?php if($smDeliverablesAcceptance && $smDeliverablesAcceptance->can_menu): ?>

                        <li>

                            <a class="nav-link text-white menu-item <?php echo e($isSmDeliverablesAcceptanceActive ? 'active' : ''); ?>" href="<?php echo e($smDeliverablesAcceptanceRoute); ?>">
                               <i class="bi bi-truck-flatbed me-2"></i> Accepted

                            </a>

                        </li>
                        <?php endif; ?>

                    </ul>

                </details>
            </li>
            <?php endif; ?>

                        
                        <?php if($smDeliverablesDelivery && $smDeliverablesDelivery->can_menu): ?>
                        <li>

                            <a class="nav-link text-white menu-item <?php echo e($isSmDeliverablesDeliveryActive ? 'active' : ''); ?>" href="<?php echo e($smDeliverablesDeliveryRoute); ?>">

                               <i class="bi bi-truck-flatbed me-2"></i> Delivered

                            </a>

                        </li>
                        <?php endif; ?>

        </ul>

    </div>

</li>

<?php endif; ?>
            

<?php

    $operationsFeasibility = \App\Helpers\TemplateHelper::getUserMenuPermissions('operations Feasibility');
    //sub-section privilege  
    $operationsFeasibilityOpen = \App\Helpers\TemplateHelper::getUserMenuPermissions('operations Feasibility', 'Operations Feasibility Open');
    $operationsFeasibilityInProgress = \App\Helpers\TemplateHelper::getUserMenuPermissions('operations Feasibility', 'Operations Feasibility In Progress');
    $operationsFeasibilityClosed = \App\Helpers\TemplateHelper::getUserMenuPermissions('operations Feasibility', 'Operations Feasibility Closed');
    
    $operationsDeliverables = \App\Helpers\TemplateHelper::getUserMenuPermissions('operations Deliverables');
    // sub-section privilege  
    $operationsDeliverablesOpen = \App\Helpers\TemplateHelper::getUserMenuPermissions('operations Deliverables', 'Operations Deliverables Open');
    $operationsDeliverablesInProgress = \App\Helpers\TemplateHelper::getUserMenuPermissions('operations Deliverables', 'Operations Deliverables In Progress');
    $operationsDeliverablesDelivery = \App\Helpers\TemplateHelper::getUserMenuPermissions('operations Deliverables', 'Operations Deliverables Delivery');
    $operationsDeliverablesAcceptance = \App\Helpers\TemplateHelper::getUserMenuPermissions('operations Deliverables', 'Operations Deliverables Acceptance');

    $operationsAsset = \App\Helpers\TemplateHelper::getUserMenuPermissions('Asset');
    $operationsRenewals = \App\Helpers\TemplateHelper::getUserMenuPermissions('Renewals');
    $operationsTermination = \App\Helpers\TemplateHelper::getUserMenuPermissions('Termination');

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

    $isDeliverablesAcceptanceActive = false;


    // Check for direct deliverables page routes

    if (request()->is('operations/deliverables/open')) {

        $isDeliverablesOpenActive = true;

    } elseif (request()->is('operations/deliverables/inprogress')) {

        $isDeliverablesInProgressActive = true;

    } elseif (request()->is('operations/deliverables/delivery')) {

        $isDeliverablesDeliveryActive = true;

    } elseif (request()->is('operations/deliverables/acceptance') || request()->is('operations/deliverables/*/acceptance')) {

        $isDeliverablesAcceptanceActive = true;

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
    // For deliverables view/edit pages, check the record status / type

    if (request()->is('operations/deliverables/*/view') || request()->is('operations/deliverables/*/edit')) {

        $recordId = request()->segment(3); // Get the ID from URL

        if ($recordId) {

            try {

                $record = \App\Models\Deliverables::with('feasibility')->find($recordId);

                if ($record && $record->status) {

                    switch ($record->status) {

                        case 'Open':

                            $isDeliverablesOpenActive = true;

                            break;

                        case 'InProgress':

                            $isDeliverablesInProgressActive = true;

                            break;

                        case 'Delivery':

                            // For ILL service, treat as Accepted in menu
                            if (optional($record->feasibility)->type_of_service === 'ILL') {
                                $isDeliverablesAcceptanceActive = true;
                            } else {
                                $isDeliverablesDeliveryActive = true;
                            }

                            break;

                    }

                }

            } catch (Exception $e) {

                // Fallback - no active state

            }

        }

    }

?>

<?php if(($operationsAsset && $operationsAsset->can_menu) || ($operationsRenewals && $operationsRenewals->can_menu) || ($operationsFeasibility && $operationsFeasibility->can_menu) 
|| ($operationsFeasibilityOpen && $operationsFeasibilityOpen->can_menu) || ($operationsDeliverables && $operationsDeliverables->can_menu) || ($operationsTermination && $operationsTermination->can_menu)): ?>

<li class="nav-item ">

    <details class="sidebar-dropdown" <?php echo e(request()->is('operations/asset*') || request()->is('operations/renewals*') || request()->is('operations/feasibility*') || request()->is('operations/deliverables*') || request()->is('operations/purchaseorder*') || request()->is('operations/termination*') ? 'open' : ''); ?>>
        <summary class="nav-link text-white d-flex justify-content-between align-items-center bg-success">

            <span><i class="bi bi-gear-wide-connected"></i> Operations</span>

            <i class="bi bi-chevron-down arrow-icon"></i>

        </summary>

        <ul class="nav flex-column ms-3 mt-1">
            
            <!-- Feasibility Main Menu --> 
            <?php if($operationsFeasibility && $operationsFeasibility->can_menu): ?>

            <li class="nav-item">

                <details class="sidebar-dropdown" <?php echo e(request()->is('operations/feasibility*') ? 'open' : ''); ?>>

                    <summary class="nav-link text-white d-flex justify-content-between align-items-center">

                        <span><i class="bi bi-diagram-3 me-2"></i> Feasibility</span>

                        <i class="bi bi-chevron-down arrow-icon"></i>

                    </summary>

        <ul class="nav flex-column ms-3 mt-1">

               <!-- operations feasibility open menu -->
                <?php if($operationsFeasibilityOpen && $operationsFeasibilityOpen->can_menu): ?>
            <li>

                <a class="nav-link text-white menu-item <?php echo e($isFeasibilityOpenActive ? 'active' : ''); ?>"

                   href="<?php echo e(route('operations.feasibility.open')); ?>">

                   <i class="bi bi-hourglass-split me-2"></i> Open

                </a>

            </li>
            <?php endif; ?>
               <!-- operations feasibility In progress menu -->
                <?php if($operationsFeasibilityInProgress && $operationsFeasibilityInProgress->can_menu): ?>
            <li>

                <a class="nav-link text-white menu-item <?php echo e($isFeasibilityInProgressActive ? 'active' : ''); ?>"

                   href="<?php echo e(route('operations.feasibility.inprogress')); ?>">

                   <i class="bi bi-clock-history me-2"></i> In Progress

                </a>

            </li>
            <?php endif; ?>
                <!-- operations feasibility closed menu -->
            <?php if($operationsFeasibilityClosed && $operationsFeasibilityClosed->can_menu): ?>
            <li>

                <a class="nav-link text-white menu-item <?php echo e($isFeasibilityClosedActive ? 'active' : ''); ?>" href="<?php echo e(route('operations.feasibility.closed')); ?>">

                   <i class="bi bi-check-circle me-2"></i> Closed

                </a>

            </li>
            <?php endif; ?>  

        </ul>

                </details>
            </li>
            <?php endif; ?>


            <!-- Operations Deliverables Main Menu -->
            <?php if($operationsDeliverables && $operationsDeliverables->can_menu): ?>
            <li class="nav-item">

                <details class="sidebar-dropdown" <?php echo e(request()->is('operations/deliverables*') ? 'open' : ''); ?>>

                    <summary class="nav-link text-white d-flex justify-content-between align-items-center">

                        <span><i class="bi bi-truck me-2"></i> Deliverables</span>

                        <i class="bi bi-chevron-down arrow-icon"></i>

                    </summary>

        <ul class="nav flex-column ms-3 mt-1">

                       <!-- Operations Deliverables open Menu -->
                        <?php if($operationsDeliverablesOpen && $operationsDeliverablesOpen->can_menu): ?>
            <li>

                <a class="nav-link text-white menu-item <?php echo e($isDeliverablesOpenActive ? 'active' : ''); ?>" href="<?php echo e(route('operations.deliverables.open')); ?>">

                   <i class="bi bi-hourglass-split me-2"></i> Open

                </a>

            </li>
            <?php endif; ?>

            <!-- Operations Deliverables In Progress Menu -->
            <?php if($operationsDeliverablesInProgress && $operationsDeliverablesInProgress->can_menu): ?>

            <li>

                <a class="nav-link text-white menu-item <?php echo e($isDeliverablesInProgressActive ? 'active' : ''); ?>" href="<?php echo e(route('operations.deliverables.inprogress')); ?>">

                   <i class="bi bi-clock-history me-2"></i> In Progress

                </a>

            </li>
            <?php endif; ?>

            <!-- Operations Deliverables Delivered Menu (now shown as main menu item below) -->

             <!-- Operations Deliverables Acceptance Menu -->
            <?php if($operationsDeliverablesAcceptance && $operationsDeliverablesAcceptance->can_menu): ?>
            <li>

                <a class="nav-link text-white menu-item <?php echo e($isDeliverablesAcceptanceActive ? 'active' : ''); ?>"
                   href="<?php echo e(route('operations.deliverables.acceptance')); ?>">

                   <i class="bi bi-truck-flatbed me-2"></i> Accepted
                </a>
            </li>
            <?php endif; ?>
        </ul>
                </details>
            </li>
            <?php endif; ?>
            
              <!-- Operations Delivered Main Menu -->
            <?php if($operationsDeliverablesDelivery && $operationsDeliverablesDelivery->can_menu): ?>
            <li>

                <a class="nav-link text-white menu-item <?php echo e($isDeliverablesDeliveryActive ? 'active' : ''); ?>"
                   href="<?php echo e(route('operations.deliverables.delivery')); ?>">

                   <i class="bi bi-truck-flatbed me-2"></i> Delivered
                </a>
            </li>
            <?php endif; ?>

            
                <!-- Renewals in operations -->
                <?php if($operationsRenewals && $operationsRenewals->can_menu): ?>
                      <li>
                        <a class="nav-link text-white menu-item <?php echo e(request()->is('operations/renewals*') ? 'active' : ''); ?>"
                                   href="<?php echo e(route('operations.renewals.index')); ?>">
                            <i class="bi bi-receipt me-2"></i> Renewals
                        </a>
                        </li>
                <?php endif; ?>

                <!-- Asset in operations -->
            <?php if($operationsAsset && $operationsAsset->can_menu): ?>

        <li class="nav-item">

                    <a class="nav-link text-white menu-item <?php echo e(request()->is('operations/asset/*') ? 'active' : ''); ?>" href="<?php echo e(route('operations.asset.index')); ?>">
                        <i class="bi bi-gear"></i> Asset

                    </a>

                </li>
            <?php endif; ?>

                <!-- Termination in operations -->                 

             <?php if($operationsTermination && $operationsTermination->can_menu): ?>

        <li class="nav-item">

                    <a class="nav-link text-white menu-item <?php echo e(request()->is('operations/termination/*') ? 'active' : ''); ?>" href="<?php echo e(route('operations.termination.index')); ?>">
                        <i class="bi bi-wallet2"></i> Termination

                    </a>

                </li>
            <?php endif; ?>

        </ul>
    </details>
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
                    <details class="sidebar-dropdown" <?php echo e(request()->is('finance/*') ? 'open' : ''); ?>>
                        <summary class="nav-link text-white d-flex justify-content-between align-items-center  bg-warning">
                            <span><i class="bi bi-cash-coin"></i> Finance</span>
                            <i class="bi bi-chevron-down arrow-icon"></i>
                        </summary>

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
                    </details>
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
                $users = \App\Helpers\TemplateHelper::getUserMenuPermissions('Manage User');
                $employees = \App\Helpers\TemplateHelper::getUserMenuPermissions('Employee');
                $leavetype = \App\Helpers\TemplateHelper::getUserMenuPermissions('Leave Management');

            ?>

            <?php if($hr && $hr->can_menu): ?>
                <li class="nav-item ">
                    <details class="sidebar-dropdown" <?php echo e(request()->is('hr*') || request()->is('users*') ? 'open' : ''); ?>>
                        <summary class="nav-link text-white d-flex justify-content-between align-items-center bg-secondary">
                            <span><i class="bi bi-people-fill"></i> HR</span>
                            <i class="bi bi-chevron-down arrow-icon"></i>
                        </summary>

                        <?php if($employees && $employees->can_menu): ?>  
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link text-white menu-item <?php echo e(request()->is('hr/employee*') ? 'active' : ''); ?>" href="<?php echo e(route('hr.employee.index')); ?>">
                                    <i class="bi bi-person-badge"></i> Employee
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php if($users && $users->can_menu): ?>
                                <li class="nav-item">
                                    <a class="nav-link text-white menu-item <?php echo e(request()->is('users*') ? 'active' : ''); ?>" href="<?php echo e(route('users.index')); ?>">
                                        <i class="bi bi-people"></i> Manage User
                                    </a>
                                </li>
                            <?php endif; ?>

                            
                            <?php if($leavetype && $leavetype->can_menu): ?>
                            <li class="nav-item">
                                <a class="nav-link text-white menu-item <?php echo e(request()->is('hr/leavetype*') ? 'active' : ''); ?>" href="<?php echo e(route('hr.leavetype.index')); ?>">
                                    <i class="bi bi-calendar-check"></i> Leave Type
                                </a>
                            </li>
                            <?php endif; ?>

                        </ul>
                    </details>
                </li>

            <?php endif; ?>

             

            <?php

                $training = \App\Helpers\TemplateHelper::getUserMenuPermissions('Training');
            ?>

            <?php if($training && $training->can_menu): ?>

                <li class="nav-item">

                    <a class="nav-link text-white menu-item <?php echo e(request()->is('training/*') ? 'active' : ''); ?>"
                     href="<?php echo e(url('/training')); ?>">
                        <i class="bi bi-journal-bookmark"></i> Training

                    </a>

                </li>

            <?php endif; ?>

             

            <?php

                $admin = \App\Helpers\TemplateHelper::getUserMenuPermissions('Admin');
            ?>

            <?php if($admin && $admin->can_menu): ?>

                <li class="nav-item" >

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

                <li class="nav-item" >

                    <details class="sidebar-dropdown" <?php echo e(request()->is('emails*') || request()->is('menus*') || request()->is('company-settings*') || request()->is('system-settings*') || request()->is('whatsapp-settings*') ? 'open' : ''); ?>>

                        <summary class="nav-link text-white d-flex justify-content-between align-items-center" style="background:#0083b0;">

                            <span><i class="bi bi-gear"></i> System</span>

                            <i class="bi bi-chevron-down arrow-icon"></i>

                        </summary>

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
                    </details>
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
    /* Base link styling */
    .nav-link {
        font-size: 0.925rem;
        padding: 0.55rem 0.9rem;
        border-radius: 4px;
        color: #e5ecff;
        display: flex;
        align-items: center;
        gap: 0.45rem;
        transition: background-color 0.18s ease, color 0.18s ease, padding-left 0.18s ease;
    }

    /* Top-level sections a bit bolder */
    .nav > .nav-item > .nav-link {
        font-weight: 500;
    }

    /* Nested menu indentation */
    .nav .nav .nav-link {
        font-weight: 400;
        padding-left: 1.6rem;
        font-size: 0.9rem;
    }

    /* Active state for menu items */
    .menu-item.active {
        background-color: #0d6efd;
        border-radius: 4px;
        color: #fff !important;
        box-shadow: 0 0 0 1px rgba(13, 110, 253, 0.4);
    }

    /* Hover state */
    .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.06);
        color: #ffffff !important;
        text-decoration: none;
    }

    .menu-item:hover {
        background-color: #0b5ed7;
        color: #fff !important;
    }

    /* Icons */
    .nav-link i {
        font-size: 1rem;
    }

    /* Arrow icon rotation for open groups */
    .arrow-icon {
        transition: transform 0.18s ease;
        font-size: 0.8rem;
    }

    .nav-link[aria-expanded="true"] .arrow-icon {
        transform: rotate(180deg);
    }

    /* Spacing between groups */
    .nav > .nav-item + .nav-item {
        margin-top: 0.15rem;
    }

    .nav .collapse .nav-item + .nav-item {
        margin-top: 0.05rem;
    }
</style>

<?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\layouts\partials\fullmenu.blade.php ENDPATH**/ ?>