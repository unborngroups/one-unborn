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
            // ['module_name' => 'Dashboard', 'user_type' => 'admin', 'name' => 'Dashboard', 'route' => 'welcome', 'icon' => 'bi bi-speedometer2', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 0, 'can_view' => 1],
            // ['module_name' => 'Dashboard', 'user_type' => 'users', 'name' => 'Dashboard', 'route' => 'welcome', 'icon' => 'bi bi-speedometer2', 'can_add' => 0, 'can_edit' => 0, 'can_delete' => 0, 'can_view' => 1],

            // 👥 Masters
            ['module_name' => 'Masters', 'user_type' => 'superadmin', 'name' => 'Manage User', 'route' => 'users.index', 'icon' => 'bi bi-people', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Masters', 'user_type' => 'superadmin', 'name' => 'User Type', 'route' => 'usertype.index', 'icon' => 'bi bi-person-lines-fill', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Masters', 'user_type' => 'superadmin', 'name' => 'Template Masters', 'route' => 'emails.index', 'icon' => 'bi bi-file-earmark-text', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Masters', 'user_type' => 'superadmin', 'name' => 'Client Masters', 'route' => 'client.index', 'icon' => 'bi bi-person-badge', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Masters', 'user_type' => 'superadmin', 'name' => 'Vendor Masters', 'route' => 'vendor.index', 'icon' => 'bi bi-truck', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Masters', 'user_type' => 'superadmin', 'name' => 'Company Details', 'route' => 'company.index', 'icon' => 'bi bi-building', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            
            // 🛠️ Sales & Marketing - Feasibility Masters
            ['module_name' => 'Sales & Marketing', 'user_type' => 'superadmin', 'name' => 'Feasibility Masters', 'route' => 'feasibility.index', 'icon' => 'bi bi-diagram-3', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Sales & Marketing', 'user_type' => 'superadmin', 'name' => 'Purchase Order', 'route' => 'sm.purchaseorder.index', 'icon' => 'bi bi-receipt', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'operations', 'user_type' => 'superadmin', 'name' => 'operations Feasibility', 'route' => 'feasibility.status', 'icon' => 'bi bi-kanban', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
           
            
            // ['module_name' => 'Masters', 'user_type' => 'admin', 'name' => 'Manage User', 'route' => 'users.index', 'icon' => 'bi bi-people', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 0, 'can_view' => 1],

            
            // 🏢 Company Module
            // ['module_name' => 'Company', 'user_type' => 'superadmin', 'name' => 'Company Details', 'route' => 'company.index', 'icon' => 'bi bi-building', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            // ['module_name' => 'Company', 'user_type' => 'admin', 'name' => 'Company Details', 'route' => 'company.index', 'icon' => 'bi bi-building', 'can_add' => 0, 'can_edit' => 1, 'can_delete' => 0, 'can_view' => 1],

            // 📂 Masters Module
            // ['module_name' => 'Masters', 'user_type' => 'superadmin', 'name' => 'Template Masters', 'route' => 'emails.index', 'icon' => 'bi bi-file-earmark-text', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            // ['module_name' => 'Masters', 'user_type' => 'superadmin', 'name' => 'Client Masters', 'route' => 'client.index', 'icon' => 'bi bi-person-badge', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            // ['module_name' => 'Masters', 'user_type' => 'superadmin', 'name' => 'Vendor Masters', 'route' => 'vendor.index', 'icon' => 'bi bi-truck', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],

            // ['module_name' => 'Masters', 'user_type' => 'admin', 'name' => 'Template Masters', 'route' => 'emails.index', 'icon' => 'bi bi-file-earmark-text', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 0, 'can_view' => 1],
            // ['module_name' => 'Masters', 'user_type' => 'admin', 'name' => 'Client Masters', 'route' => 'client.index', 'icon' => 'bi bi-person-badge', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 0, 'can_view' => 1],
            // ['module_name' => 'Masters', 'user_type' => 'admin', 'name' => 'Vendor Masters', 'route' => 'vendor.index', 'icon' => 'bi bi-truck', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 0, 'can_view' => 1],

            // 🛠️ Sales & Marketing - Feasibility Masters
            // ['module_name' => 'Sales & Marketing', 'user_type' => 'superadmin', 'name' => 'Feasibility Masters', 'route' => 'feasibility.index', 'icon' => 'bi bi-diagram-3', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            // ['module_name' => 'Sales & Marketing', 'user_type' => 'admin', 'name' => 'Feasibility Masters', 'route' => 'feasibility.index', 'icon' => 'bi bi-diagram-3', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 0, 'can_view' => 1],
            // ['module_name' => 'Sales & Marketing', 'user_type' => 'users', 'name' => 'Feasibility Masters', 'route' => 'feasibility.index', 'icon' => 'bi bi-diagram-3', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 0, 'can_view' => 1],

            // 🛒 Sales & Marketing - Purchase Order
            // ['module_name' => 'Sales & Marketing', 'user_type' => 'superadmin', 'name' => 'Purchase Order', 'route' => 'sm.purchaseorder.index', 'icon' => 'bi bi-receipt', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            // ['module_name' => 'Sales & Marketing', 'user_type' => 'admin', 'name' => 'Purchase Order', 'route' => 'sm.purchaseorder.index', 'icon' => 'bi bi-receipt', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 0, 'can_view' => 1],
            // ['module_name' => 'Sales & Marketing', 'user_type' => 'users', 'name' => 'Purchase Order', 'route' => 'sm.purchaseorder.index', 'icon' => 'bi bi-receipt', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 0, 'can_view' => 1],

            // 🛠️ operations Module - Feasibility Status
            // ['module_name' => 'operations', 'user_type' => 'superadmin', 'name' => 'operations Feasibility', 'route' => 'feasibility.status', 'icon' => 'bi bi-kanban', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            // ['module_name' => 'operations', 'user_type' => 'admin', 'name' => 'operations Feasibility', 'route' => 'feasibility.status', 'icon' => 'bi bi-kanban', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 0, 'can_view' => 1],
            // ['module_name' => 'operations', 'user_type' => 'users', 'name' => 'operations Feasibility', 'route' => 'feasibility.status', 'icon' => 'bi bi-kanban', 'can_add' => 0, 'can_edit' => 1, 'can_delete' => 0, 'can_view' => 1],

            // 🚛 operations Module - Deliverables Status
            ['module_name' => 'operations', 'user_type' => 'superadmin', 'name' => 'operations Deliverables', 'route' => 'operations.deliverables.open', 'icon' => 'bi bi-truck', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'operations', 'user_type' => 'admin', 'name' => 'operations Deliverables', 'route' => 'operations.deliverables.open', 'icon' => 'bi bi-truck', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 0, 'can_view' => 1],
            ['module_name' => 'operations', 'user_type' => 'users', 'name' => 'operations Deliverables', 'route' => 'operations.deliverables.open', 'icon' => 'bi bi-truck', 'can_add' => 0, 'can_edit' => 1, 'can_delete' => 0, 'can_view' => 1],

            // ⚙️ Settings
            ['module_name' => 'Settings', 'user_type' => 'superadmin', 'name' => 'Common Settings', 'route' => 'settings.index', 'icon' => 'bi bi-gear', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],

            // 👤 User Menus
            ['module_name' => 'Masters', 'user_type' => 'users', 'name' => 'Client Masters', 'route' => 'client.index', 'icon' => 'bi bi-person-badge', 'can_add' => 0, 'can_edit' => 0, 'can_delete' => 0, 'can_view' => 1],
            ['module_name' => 'Masters', 'user_type' => 'users', 'name' => 'Vendor Masters', 'route' => 'vendor.index', 'icon' => 'bi bi-truck', 'can_add' => 0, 'can_edit' => 0, 'can_delete' => 0, 'can_view' => 1],
        
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
