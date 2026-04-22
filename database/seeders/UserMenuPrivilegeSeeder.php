<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserMenuPrivilege;
use App\Models\User;
use App\Models\Menu;

class UserMenuPrivilegeSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Starting UserMenuPrivilegeSeeder...');


        // SUPERADMIN — FULL ACCESS
        $superAdmins = User::whereHas('userType', function($q) {
            $q->where('name', 'superadmin');
        })->get();
        foreach ($superAdmins as $user) {
            foreach (Menu::all() as $menu) {
                UserMenuPrivilege::updateOrCreate(
                    [
                        'user_id' => $user->id,
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

        // ADMIN — LIMITED DELETE
        $admins = User::whereHas('userType', function($q) {
            $q->where('name', 'admin');
        })->get();
        $adminMenus = Menu::whereIn('user_type', ['admin', 'superadmin'])->get();
        foreach ($admins as $user) {
            foreach ($adminMenus as $menu) {
                UserMenuPrivilege::updateOrCreate(
                    [
                        'user_id' => $user->id,
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

        // USERS — BASIC OPERATION ACCESS
        $users = User::whereHas('userType', function($q) {
            $q->where('name', 'users');
        })->get();
        $userMenus = Menu::where('user_type', 'users')->get();
        foreach ($users as $user) {
            foreach ($userMenus as $menu) {
                UserMenuPrivilege::updateOrCreate(
                    [
                        'user_id' => $user->id,
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

        // USER — VERY BASIC ACCESS
        $basicUsers = User::whereHas('userType', function($q) {
            $q->where('name', 'user');
        })->get();
        $basicMenus = Menu::whereIn('name', ['Dashboard'])->get();
        foreach ($basicUsers as $user) {
            foreach ($basicMenus as $menu) {
                UserMenuPrivilege::updateOrCreate(
                    [
                        'user_id' => $user->id,
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
