<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserTypeMenuPrivilege;
use App\Models\UserType;
use App\Models\Menu;

class UserTypeMenuPrivilegeSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Starting Safe UserTypeMenuPrivilegeSeeder...');

        // --- Collect User Types ---
        $superAdmin = UserType::where('name', 'superadmin')->first();
        $admin      = UserType::where('name', 'admin')->first();
        $users      = UserType::where('name', 'users')->first();
        $user       = UserType::where('name', 'user')->first();

        // ---------------------------
        // SUPERADMIN — FULL ACCESS
        // ---------------------------
        if ($superAdmin) {
            foreach (Menu::all() as $menu) {
                UserTypeMenuPrivilege::updateOrCreate(
                    [
                        'user_type_id' => $superAdmin->id,
                        'menu_id' => $menu->id,
                    ],
                    [
                        'can_menu' => true,
                        'can_add' => true,
                        'can_edit' => true,
                        'can_delete' => true,
                        'can_view' => true,
                    ]
                );
            }
        }

        // ---------------------------
        // ADMIN — LIMITED DELETE
        // ---------------------------
        if ($admin) {
            $adminMenus = Menu::whereIn('user_type', ['admin', 'superadmin'])->get();

            foreach ($adminMenus as $menu) {
                UserTypeMenuPrivilege::updateOrCreate(
                    [
                        'user_type_id' => $admin->id,
                        'menu_id' => $menu->id,
                    ],
                    [
                        'can_menu' => true,
                        'can_add' => true,
                        'can_edit' => true,
                        'can_delete' => false,
                        'can_view' => true,
                    ]
                );
            }
        }

        // ---------------------------
        // USERS — BASIC OPERATION ACCESS
        // ---------------------------
        if ($users) {
            $userMenus = Menu::where('user_type', 'users')->get();

            foreach ($userMenus as $menu) {
                UserTypeMenuPrivilege::updateOrCreate(
                    [
                        'user_type_id' => $users->id,
                        'menu_id' => $menu->id,
                    ],
                    [
                        'can_menu' => true,
                        'can_add' => in_array($menu->name, [
                            'Feasibility Master',
                            'Purchase Order'
                        ]),
                        'can_edit' => in_array($menu->name, [
                            'Feasibility Master',
                            'Purchase Order',
                            'operations Feasibility',
                            'operations Deliverables'
                        ]),
                        'can_delete' => false,
                        'can_view' => true,
                    ]
                );
            }
        }

        // ---------------------------
        // USER — VERY BASIC ACCESS
        // ---------------------------
        if ($user) {
            $basicMenus = Menu::whereIn('name', ['Dashboard'])->get();

            foreach ($basicMenus as $menu) {
                UserTypeMenuPrivilege::updateOrCreate(
                    [
                        'user_type_id' => $user->id,
                        'menu_id' => $menu->id,
                    ],
                    [
                        'can_menu' => true,
                        'can_add' => false,
                        'can_edit' => false,
                        'can_delete' => false,
                        'can_view' => true,
                    ]
                );
            }
        }

        $this->command->info('🎉 Privileges updated safely without deleting existing data!');
    }
}
