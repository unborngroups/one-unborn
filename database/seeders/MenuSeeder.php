<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
                        // Feasibility Notification Settings
                        // ['module_name' => 'Settings', 'user_type' => 'superadmin', 'name' => 'Feasibility Notification Settings','sub_section' => null ,'route' => 'settings.feasibility-notifications.edit', 'icon' => 'bi bi-bell', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            // 🌟 Dashboard Module
            ['module_name' => 'Dashboard', 'user_type' => 'superadmin', 'name' => 'Dashboard', 'sub_section' => null , 'route' => 'welcome', 'icon' => 'bi bi-speedometer2', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
           // 👥 Masters
            ['module_name' => 'Masters', 'user_type' => 'superadmin', 'name' => 'Manage User', 'sub_section' => null , 'route' => 'users.index', 'icon' => 'bi bi-people', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Masters', 'user_type' => 'superadmin', 'name' => 'User Type', 'sub_section' => null , 'route' => 'usertype.index', 'icon' => 'bi bi-person-lines-fill', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Masters', 'user_type' => 'superadmin', 'name' => 'Template Masters', 'sub_section' => null , 'route' => 'emails.index', 'icon' => 'bi bi-file-earmark-text', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Masters', 'user_type' => 'superadmin', 'name' => 'Client Master', 'sub_section' => null , 'route' => 'client.index', 'icon' => 'bi bi-person-badge', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Masters', 'user_type' => 'superadmin', 'name' => 'Vendor Master', 'sub_section' => null , 'route' => 'vendor.index', 'icon' => 'bi bi-truck', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Masters', 'user_type' => 'superadmin', 'name' => 'Company Details', 'sub_section' => null , 'route' => 'company.index', 'icon' => 'bi bi-building', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
        //    Masters - Asset Master sub-menus
           ['module_name' => 'Masters', 'user_type' => 'superadmin', 'name' => 'Asset Master', 'sub_section' => null , 'route' => null, 'icon' => 'bi bi-box-seam', 'can_add' => 0, 'can_edit' => 0, 'can_delete' => 0, 'can_view' => 1],           /* 🔽 Add main group for Asset Master */
           ['module_name' => 'Masters', 'user_type' => 'superadmin', 'name' => 'Asset Master', 'sub_section' => 'Asset Type' , 'route' => 'assetmaster.asset_type.index', 'icon' => 'bi bi-tag', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
           ['module_name' => 'Masters', 'user_type' => 'superadmin', 'name' => 'Asset Master', 'sub_section' => 'Make Type' , 'route' => 'assetmaster.make_type.index', 'icon' => 'bi bi-tools', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
           ['module_name' => 'Masters', 'user_type' => 'superadmin', 'name' => 'Asset Master', 'sub_section' => 'Model Type' , 'route' => 'assetmaster.model_type.index', 'icon' => 'bi bi-tools', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            // 🛠️ Sales & Marketing - Feasibility Master
            ['module_name' => 'Sales & Marketing', 'user_type' => 'superadmin', 'name' => 'Feasibility Master', 'sub_section' => null , 'route' => 'feasibility.index', 'icon' => 'bi bi-diagram-3', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            // 🛠️ Sales & Marketing - Feasibility Master - Sub-Sections
            ['module_name' => 'Sales & Marketing', 'user_type' => 'superadmin', 'name' => 'Feasibility Master', 'sub_section' => 'SM Feasibility Open', 'route' => 'sm.feasibility.open', 'icon' => 'bi bi-diagram-3', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Sales & Marketing', 'user_type' => 'superadmin', 'name' => 'Feasibility Master', 'sub_section' => 'SM Feasibility In Progress', 'route' => 'sm.feasibility.inprogress', 'icon' => 'bi bi-diagram-3', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Sales & Marketing', 'user_type' => 'superadmin', 'name' => 'Feasibility Master', 'sub_section' => 'SM Feasibility Closed', 'route' => 'sm.feasibility.closed', 'icon' => 'bi bi-diagram-3', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],

            ['module_name' => 'Sales & Marketing', 'user_type' => 'superadmin', 'name' => 'Purchase Order', 'sub_section' => null , 'route' => 'sm.purchaseorder.index', 'icon' => 'bi bi-receipt', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Sales & Marketing', 'user_type' => 'superadmin', 'name' => 'Proposal', 'sub_section' => null , 'route' => 'sm.proposal.index', 'icon' => 'bi bi-receipt', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Sales & Marketing', 'user_type' => 'superadmin', 'name' => 'sm Deliverables', 'sub_section' => null , 'route' => null, 'icon' => 'bi bi-truck', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            
            ['module_name' => 'Sales & Marketing', 'user_type' => 'superadmin', 'name' => 'sm Deliverables', 'sub_section' => 'SM Deliverables Open', 'route' => 'sm.deliverables.open', 'icon' => 'bi bi-truck', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Sales & Marketing', 'user_type' => 'superadmin', 'name' => 'sm Deliverables', 'sub_section' => 'SM Deliverables In Progress', 'route' => 'sm.deliverables.inprogress', 'icon' => 'bi bi-truck', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Sales & Marketing', 'user_type' => 'superadmin', 'name' => 'sm Deliverables', 'sub_section' => 'SM Deliverables Delivery', 'route' => 'sm.deliverables.delivery', 'icon' => 'bi bi-truck', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Sales & Marketing', 'user_type' => 'superadmin', 'name' => 'sm Deliverables', 'sub_section' => 'SM Deliverables Acceptance', 'route' => 'sm.deliverables.acceptance', 'icon' => 'bi bi-truck', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            
            // 📦 operations Module - Feasibility Status
            ['module_name' => 'operations', 'user_type' => 'superadmin', 'name' => 'operations Feasibility', 'sub_section' => null , 'route' => null, 'icon' => 'bi bi-kanban', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'operations', 'user_type' => 'superadmin', 'name' => 'operations Feasibility', 'sub_section' => 'Operations Feasibility Open', 'route' => 'operations.feasibility.open', 'icon' => 'bi bi-kanban', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'operations', 'user_type' => 'superadmin', 'name' => 'operations Feasibility', 'sub_section' => 'Operations Feasibility In Progress', 'route' => 'operations.feasibility.inprogress', 'icon' => 'bi bi-kanban', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'operations', 'user_type' => 'superadmin', 'name' => 'operations Feasibility', 'sub_section' => 'Operations Feasibility Closed', 'route' => 'operations.feasibility.closed', 'icon' => 'bi bi-kanban', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'operations', 'user_type' => 'superadmin', 'name' => 'operations Feasibility', 'sub_section' => 'Operations Feasibility Not-Feasible', 'route' => 'operations.feasibility.notfeasible', 'icon' => 'bi bi-kanban', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            
            ['module_name' => 'operations', 'user_type' => 'superadmin', 'name' => 'operations Deliverables', 'sub_section' => null , 'route' => null, 'icon' => 'bi bi-truck', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'operations', 'user_type' => 'superadmin', 'name' => 'operations Deliverables', 'sub_section' => 'Operations Deliverables Open' , 'route' => 'operations.deliverables.open', 'icon' => 'bi bi-truck', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'operations', 'user_type' => 'superadmin', 'name' => 'operations Deliverables', 'sub_section' => 'Operations Deliverables In Progress' , 'route' => 'operations.deliverables.inprogress', 'icon' => 'bi bi-truck', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'operations', 'user_type' => 'superadmin', 'name' => 'operations Deliverables', 'sub_section' => 'Operations Deliverables Delivery' , 'route' => 'operations.deliverables.delivery', 'icon' => 'bi bi-truck', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'operations', 'user_type' => 'superadmin', 'name' => 'operations Deliverables', 'sub_section' => 'Operations Deliverables Acceptance' , 'route' => 'operations.deliverables.acceptance', 'icon' => 'bi bi-truck', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'operations', 'user_type' => 'superadmin', 'name' => 'Asset', 'sub_section' => null , 'route' => 'operations.asset.index', 'icon' => 'bi bi-shield-check', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'operations', 'user_type' => 'superadmin', 'name' => 'Renewals', 'sub_section' => null , 'route' => 'operations.renewals.index', 'icon' => 'bi bi-wallet2', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'operations', 'user_type' => 'superadmin', 'name' => 'Termination', 'sub_section' => null , 'route' => 'operations.termination.index', 'icon' => 'bi bi-wallet2', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            
            // Finance Module
            ['module_name' => 'Finance', 'user_type' => 'superadmin', 'name' => 'Accounts', 'sub_section' => null , 'route' => 'finance.accounts.index', 'icon' => 'bi bi-wallet2', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Finance', 'user_type' => 'superadmin', 'name' => 'Banking', 'sub_section' => null , 'route' => 'finance.banking.index', 'icon' => 'bi bi-wallet2', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Finance', 'user_type' => 'superadmin', 'name' => 'Purchases', 'sub_section' => null , 'route' => 'finance.purchases.index', 'icon' => 'bi bi-wallet2', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Finance', 'user_type' => 'superadmin', 'name' => 'Reports', 'sub_section' => null , 'route' => 'finance.reports.index', 'icon' => 'bi bi-wallet2', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Finance', 'user_type' => 'superadmin', 'name' => 'Settings', 'sub_section' => null , 'route' => 'finance.settings.index', 'icon' => 'bi bi-wallet2', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Finance', 'user_type' => 'superadmin', 'name' => 'Invoice', 'sub_section' => null , 'route' => 'finance.invoice.index', 'icon' => 'bi bi-wallet2', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Finance', 'user_type' => 'superadmin', 'name' => 'Items', 'sub_section' => null , 'route' => 'finance.items.index', 'icon' => 'bi bi-wallet2', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            
            // compliance Module
            ['module_name' => 'Compliance', 'user_type' => 'superadmin', 'name' => 'Compliance', 'sub_section' => null , 'route' => 'compliance.index', 'icon' => 'bi bi-file-shield', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],

            // 🛡️ Assurance Module
            ['module_name' => 'Assurance', 'user_type' => 'superadmin', 'name' => 'Assurance', 'sub_section' => null , 'route' => 'assurance.index', 'icon' => 'bi bi-shield-check', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            
            // 🛡️ Asset Module
            
            // HR Module
            ['module_name' => 'HR', 'user_type' => 'superadmin', 'name' => 'HR', 'sub_section' => null , 'route' => 'null', 'icon' => 'bi bi-people-fill', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'HR', 'user_type' => 'superadmin', 'name' => 'Employee', 'sub_section' => null , 'route' => 'hr.employee.index', 'icon' => 'bi bi-people-fill', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'HR', 'user_type' => 'superadmin', 'name' => 'Leave Management', 'sub_section' => null , 'route' => 'hr.leave.index', 'icon' => 'bi bi-calendar-check', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            // Training Module
            ['module_name' => 'Training', 'user_type' => 'superadmin', 'name' => 'Training', 'sub_section' => null , 'route' => 'training.index', 'icon' => 'bi bi-journal-bookmark-fill', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            
            // Admin Module
            ['module_name' => 'Admin', 'user_type' => 'superadmin', 'name' => 'Admin', 'sub_section' => null , 'route' => 'admin.index', 'icon' => 'bi bi-gear-fill', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
 
            // Strategy Module
            ['module_name' => 'Strategy', 'user_type' => 'superadmin', 'name' => 'Strategy', 'sub_section' => null , 'route' => 'strategy.index', 'icon' => 'bi bi-lightbulb-fill', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            
            // 📊 Reports - Deliverable Report
            ['module_name' => 'Reports','user_type' => 'superadmin','name' => 'Deliverable Report','sub_section' => null,'route' => null,'icon' => 'bi bi-bar-chart','can_add' => 0,'can_edit' => 0,'can_delete' => 0,'can_view' => 1,],
            ['module_name' => 'Reports','user_type' => 'superadmin','name' => 'Deliverable Report','sub_section' => 'Open','route' => 'report.deliverable.open','icon' => 'bi bi-hourglass-split','can_add' => 0,'can_edit' => 0,'can_delete' => 0,'can_view' => 1,],
            [
                                        'module_name' => 'Reports',
                                        'user_type' => 'superadmin',
                                        'name' => 'Deliverable Report',
                                        'sub_section' => 'In Progress',
                                        'route' => 'report.deliverable.inprogress',
                                        'icon' => 'bi bi-clock-history','can_add' => 0,'can_edit' => 0,'can_delete' => 0,'can_view' => 1,
                                    ],
                                    [
                                        'module_name' => 'Reports','user_type' => 'superadmin','name' => 'Deliverable Report','sub_section' => 'Delivery','route' => 'report.deliverable.delivery','icon' => 'bi bi-truck-flatbed','can_add' => 0,'can_edit' => 0,'can_delete' => 0,'can_view' => 1,
                                    ],
            // ⚙️ Settings
            ['module_name' => 'Settings', 'user_type' => 'superadmin', 'name' => 'Company Settings', 'sub_section' => null , 'route' => 'settings.company', 'icon' => 'bi bi-building', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Settings', 'user_type' => 'superadmin', 'name' => 'System Settings', 'sub_section' => null , 'route' => 'settings.system', 'icon' => 'bi bi-sliders', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Settings', 'user_type' => 'superadmin', 'name' => 'WhatsApp Settings', 'sub_section' => null , 'route' => 'settings.whatsapp', 'icon' => 'bi bi-sliders', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
                // Feasibility Status Menus
                // ['module_name' => 'Feasibility', 'user_type' => 'superadmin', 'name' => 'Feasibility - Open', 'sub_section' => null , 'route' => 'feasibility.open', 'icon' => 'bi bi-folder2-open', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
                // ['module_name' => 'Feasibility', 'user_type' => 'superadmin', 'name' => 'Feasibility - In Progress', 'sub_section' => null , 'route' => 'feasibility.inprogress', 'icon' => 'bi bi-hourglass-split', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
                // ['module_name' => 'Feasibility', 'user_type' => 'superadmin', 'name' => 'Feasibility - Closed', 'sub_section' => null , 'route' => 'feasibility.closed', 'icon' => 'bi bi-check2-circle', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
                // // Deliverable Status Menus
                // ['module_name' => 'Deliverable', 'user_type' => 'superadmin', 'name' => 'Deliverable - Open', 'sub_section' => null , 'route' => 'deliverable.open', 'icon' => 'bi bi-truck', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
                // ['module_name' => 'Deliverable', 'user_type' => 'superadmin', 'name' => 'Deliverable - In Progress', 'sub_section' => null , 'route' => 'deliverable.inprogress', 'icon' => 'bi bi-hourglass-split', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
                // ['module_name' => 'Deliverable', 'user_type' => 'superadmin', 'name' => 'Deliverable - Closed', 'sub_section' => null , 'route' => 'deliverable.closed', 'icon' => 'bi bi-check2-circle', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
                // ['module_name' => 'Deliverable', 'user_type' => 'superadmin', 'name' => 'Deliverable - Delivery', 'sub_section' => null , 'route' => 'deliverable.delivery', 'icon' => 'bi bi-box-seam', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],

            // Finance Module - State-wise Invoice Report
            ['module_name' => 'Finance', 'user_type' => 'superadmin', 'name' => 'State-wise Invoice Report', 'sub_section' => null , 'route' => 'finance.invoices.state_report', 'icon' => 'bi bi-map', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],

        ];

        foreach ($menus as $menu) {
            // Ensure new boolean columns introduced by migrations (for example
            // `can_menu`) have sensible defaults without editing every entry.
            $defaults = [
                'can_menu' => 1,
            ];

            $menu = array_merge($defaults, $menu);

            Menu::updateOrCreate(
                [
                    'user_type' => $menu['user_type'],
                    'module_name' => $menu['module_name'],
                    'name' => $menu['name'],
                    'sub_section' => $menu['sub_section'],
                ],
                $menu
            );
        }
    }
}
