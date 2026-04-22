<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserType;
use App\Models\Menu;
use App\Models\UserMenuPrivilege;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure user type exists
        $superadminType = UserType::firstOrCreate(
            ['name' => 'superadmin'],
            ['Description' => 'System Super Administrator', 'status' => 'Active']
        );

         $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'Date_of_Birth' => '2000-01-01',
                'status' => 'Active',
                'user_type_id' => $superadminType->id,
                'is_superuser' => 1,
                'role' => 'superadmin',
                'profile_created' => true, // so not redirected to profile
            ]
        );
       // ✅ 3. Give Super Admin full privileges for all menus
        $menus = Menu::all();

        foreach ($menus as $menu) {
            UserMenuPrivilege::firstOrCreate(
                [
                    'user_id' => $superAdmin->id,
                    'menu_id' => $menu->id,
                ],
                [
                    'can_menu' => 1,
                    'can_view' => 1,
                    'can_add' => 1,
                    'can_edit' => 1,
                    'can_delete' => 1,
                ]
            );
        }
        $this->command->info('✅ Super Admin and full menu privileges seeded successfully.');
    }
}
