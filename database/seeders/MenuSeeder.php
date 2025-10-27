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
            ['module_name' => 'Dashboard', 'user_type' => 'admin', 'name' => 'Dashboard', 'route' => 'welcome', 'icon' => 'bi bi-speedometer2', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 0, 'can_view' => 1],
            ['module_name' => 'Dashboard', 'user_type' => 'users', 'name' => 'Dashboard', 'route' => 'welcome', 'icon' => 'bi bi-speedometer2', 'can_add' => 0, 'can_edit' => 0, 'can_delete' => 0, 'can_view' => 1],

            // 👥 User Management
            ['module_name' => 'User Management', 'user_type' => 'superadmin', 'name' => 'Manage Users', 'route' => 'users.index', 'icon' => 'bi bi-people', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'User Management', 'user_type' => 'superadmin', 'name' => 'User Type', 'route' => 'usertype.index', 'icon' => 'bi bi-person-lines-fill', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'User Management', 'user_type' => 'admin', 'name' => 'Manage Users', 'route' => 'users.index', 'icon' => 'bi bi-people', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 0, 'can_view' => 1],

            
            // 🏢 Company Module
            ['module_name' => 'Company', 'user_type' => 'superadmin', 'name' => 'Company Details', 'route' => 'company.index', 'icon' => 'bi bi-building', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Company', 'user_type' => 'admin', 'name' => 'Company Details', 'route' => 'company.index', 'icon' => 'bi bi-building', 'can_add' => 0, 'can_edit' => 1, 'can_delete' => 0, 'can_view' => 1],

            // 📂 Master Module
            ['module_name' => 'Master', 'user_type' => 'superadmin', 'name' => 'Template Master', 'route' => 'template.index', 'icon' => 'bi bi-file-earmark-text', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Master', 'user_type' => 'superadmin', 'name' => 'Client Master', 'route' => 'client.index', 'icon' => 'bi bi-person-badge', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Master', 'user_type' => 'superadmin', 'name' => 'Vendor Master', 'route' => 'vendor.index', 'icon' => 'bi bi-truck', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],

            ['module_name' => 'Master', 'user_type' => 'admin', 'name' => 'Template Master', 'route' => 'template.index', 'icon' => 'bi bi-file-earmark-text', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 0, 'can_view' => 1],
            ['module_name' => 'Master', 'user_type' => 'admin', 'name' => 'Client Master', 'route' => 'client.index', 'icon' => 'bi bi-person-badge', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 0, 'can_view' => 1],
            ['module_name' => 'Master', 'user_type' => 'admin', 'name' => 'Vendor Master', 'route' => 'vendor.index', 'icon' => 'bi bi-truck', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 0, 'can_view' => 1],

            // 🛠️ Operations Module - Feasibility
            ['module_name' => 'Operations', 'user_type' => 'superadmin', 'name' => 'Feasibility', 'route' => 'feasibility.index', 'icon' => 'bi bi-diagram-3', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],
            ['module_name' => 'Operations', 'user_type' => 'admin', 'name' => 'Feasibility', 'route' => 'feasibility.index', 'icon' => 'bi bi-diagram-3', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 0, 'can_view' => 1],
            ['module_name' => 'Operations', 'user_type' => 'users', 'name' => 'Feasibility', 'route' => 'feasibility.index', 'icon' => 'bi bi-diagram-3', 'can_add' => 0, 'can_edit' => 0, 'can_delete' => 0, 'can_view' => 1],

            // ⚙️ Settings
            ['module_name' => 'Settings', 'user_type' => 'superadmin', 'name' => 'Common Settings', 'route' => 'settings.index', 'icon' => 'bi bi-gear', 'can_add' => 1, 'can_edit' => 1, 'can_delete' => 1, 'can_view' => 1],

            // 👤 User Menus
            ['module_name' => 'Master', 'user_type' => 'users', 'name' => 'Client Master', 'route' => 'client.index', 'icon' => 'bi bi-person-badge', 'can_add' => 0, 'can_edit' => 0, 'can_delete' => 0, 'can_view' => 1],
            ['module_name' => 'Master', 'user_type' => 'users', 'name' => 'Vendor Master', 'route' => 'vendor.index', 'icon' => 'bi bi-truck', 'can_add' => 0, 'can_edit' => 0, 'can_delete' => 0, 'can_view' => 1],
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
