<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            // 🌟 Dashboard Module
            ['module_name' => 'Dashboard', 'user_type' => 'superadmin', 'name' => 'Dashboard', 'route' => 'welcome', 'icon' => 'bi bi-speedometer2', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
           
            // 👥 Masters
            ['module_name' => 'Masters', 'user_type' => 'superadmin', 'name' => 'Manage User', 'route' => 'users.index', 'icon' => 'bi bi-people', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Masters', 'user_type' => 'superadmin', 'name' => 'User Type', 'route' => 'usertype.index', 'icon' => 'bi bi-person-lines-fill', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Masters', 'user_type' => 'superadmin', 'name' => 'Template Masters', 'route' => 'emails.index', 'icon' => 'bi bi-file-earmark-text', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Masters', 'user_type' => 'superadmin', 'name' => 'Client Master', 'route' => 'client.index', 'icon' => 'bi bi-person-badge', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Masters', 'user_type' => 'superadmin', 'name' => 'Vendor Master', 'route' => 'vendor.index', 'icon' => 'bi bi-truck', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Masters', 'user_type' => 'superadmin', 'name' => 'Company Details', 'route' => 'company.index', 'icon' => 'bi bi-building', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            

/* 🔽 Add main group for Asset Master */
['module_name' => 'Asset Master', 'user_type' => 'superadmin', 'name' => 'Asset Master', 'route' => null, 'icon' => 'bi bi-box-seam', 'can_add' => 0, 'can_edit' => 0, 'can_delete' => 0, 'can_view' => 1],

/* 🔽 Sub menus */
['module_name' => 'Asset Master', 'user_type' => 'superadmin', 'name' => 'Asset Type', 'route' => 'assetmaster.asset_type.index', 'icon' => 'bi bi-tag', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
['module_name' => 'Asset Master', 'user_type' => 'superadmin', 'name' => 'Make Type', 'route' => 'assetmaster.make_type.index', 'icon' => 'bi bi-tools', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],

            // 🛠️ Sales & Marketing - Feasibility Master
            ['module_name' => 'Sales & Marketing', 'user_type' => 'superadmin', 'name' => 'Feasibility Master', 'route' => 'feasibility.index', 'icon' => 'bi bi-diagram-3', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Sales & Marketing', 'user_type' => 'superadmin', 'name' => 'Proposal', 'route' => 'sm.proposal.index', 'icon' => 'bi bi-receipt', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Sales & Marketing', 'user_type' => 'superadmin', 'name' => 'Purchase Order', 'route' => 'sm.purchaseorder.index', 'icon' => 'bi bi-receipt', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Sales & Marketing', 'user_type' => 'superadmin', 'name' => 'Proposal', 'route' => 'sm.proposal.index', 'icon' => 'bi bi-receipt', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Sales & Marketing', 'user_type' => 'superadmin', 'name' => 'sm Deliverables', 'route' => 'sm.deliverables.open', 'icon' => 'bi bi-truck', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            // 📦 operations Module - Feasibility Status
            ['module_name' => 'operations', 'user_type' => 'superadmin', 'name' => 'operations Feasibility', 'route' => 'operations.feasibility.status', 'icon' => 'bi bi-kanban', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'operations', 'user_type' => 'superadmin', 'name' => 'operations Deliverables', 'route' => 'operations.deliverables.open', 'icon' => 'bi bi-truck', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'operations', 'user_type' => 'superadmin', 'name' => 'Asset', 'route' => 'operations.asset.index', 'icon' => 'bi bi-shield-check', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'operations', 'user_type' => 'superadmin', 'name' => 'Renewals', 'route' => 'operations.renewals.index', 'icon' => 'bi bi-wallet2', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            
            // Finance Module
            ['module_name' => 'Finance', 'user_type' => 'superadmin', 'name' => 'Accounts', 'route' => 'finance.accounts.index', 'icon' => 'bi bi-wallet2', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Finance', 'user_type' => 'superadmin', 'name' => 'Banking', 'route' => 'finance.banking.index', 'icon' => 'bi bi-wallet2', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Finance', 'user_type' => 'superadmin', 'name' => 'GST', 'route' => 'finance.gst.index', 'icon' => 'bi bi-wallet2', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Finance', 'user_type' => 'superadmin', 'name' => 'Purchases', 'route' => 'finance.purchases.index', 'icon' => 'bi bi-wallet2', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Finance', 'user_type' => 'superadmin', 'name' => 'Reports', 'route' => 'finance.reports.index', 'icon' => 'bi bi-wallet2', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Finance', 'user_type' => 'superadmin', 'name' => 'Sales', 'route' => 'finance.sales.index', 'icon' => 'bi bi-wallet2', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Finance', 'user_type' => 'superadmin', 'name' => 'TDS', 'route' => 'finance.tds.index', 'icon' => 'bi bi-wallet2', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Finance', 'user_type' => 'superadmin', 'name' => 'Settings', 'route' => 'finance.settings.index', 'icon' => 'bi bi-wallet2', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            
            // compliance Module
            ['module_name' => 'Compliance', 'user_type' => 'superadmin', 'name' => 'Compliance', 'route' => 'compliance.index', 'icon' => 'bi bi-file-shield', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],

            // 🛡️ Assurance Module
            ['module_name' => 'Assurance', 'user_type' => 'superadmin', 'name' => 'Assurance', 'route' => 'assurance.index', 'icon' => 'bi bi-shield-check', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            
            // 🛡️ Asset Module
            
            // HR Module
            ['module_name' => 'HR', 'user_type' => 'superadmin', 'name' => 'HR', 'route' => 'hr.index', 'icon' => 'bi bi-people-fill', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            
            // Training Module
            ['module_name' => 'Training', 'user_type' => 'superadmin', 'name' => 'Training', 'route' => 'training.index', 'icon' => 'bi bi-journal-bookmark-fill', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            
            // Admin Module
            ['module_name' => 'Admin', 'user_type' => 'superadmin', 'name' => 'Admin', 'route' => 'admin.index', 'icon' => 'bi bi-gear-fill', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
 
            // Strategy Module
            ['module_name' => 'Strategy', 'user_type' => 'superadmin', 'name' => 'Strategy', 'route' => 'strategy.index', 'icon' => 'bi bi-lightbulb-fill', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            
            // ⚙️ Settings
            ['module_name' => 'Settings', 'user_type' => 'superadmin', 'name' => 'Company Settings', 'route' => 'settings.company', 'icon' => 'bi bi-building', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Settings', 'user_type' => 'superadmin', 'name' => 'System Settings', 'route' => 'settings.system', 'icon' => 'bi bi-sliders', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Settings', 'user_type' => 'superadmin', 'name' => 'WhatsApp Settings', 'route' => 'settings.whatsapp', 'icon' => 'bi bi-sliders', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],

        ];

        foreach ($menus as $menu) {
            // Ensure new boolean columns introduced by migrations (for example
            // `can_menu`) have sensible defaults without editing every entry.
            $defaults = [
                'can_menu' => 1,
            ];

            $menu = array_merge($defaults, $menu);

            Menu::updateOrCreate(
                ['user_type' => $menu['user_type'], 'name' => $menu['name']],
                $menu
            );
        }
    }
}
