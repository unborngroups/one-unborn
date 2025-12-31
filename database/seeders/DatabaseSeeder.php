<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserTypeSeeder::class,    // Run first - Create user types
            MenuSeeder::class,        // Create menus before assigning privileges
            UserTypeMenuPrivilegeSeeder::class, // Set up default user type privileges  
            SuperAdminSeeder::class,  // Then create admin and assign privileges
            EmailTemplateSeeder::class, // Finally add default email templates
            PrefixSystemSeeder::class, // Add prefix system data
            UserMenuPrivilegeSeeder::class, // Add user-level menu privileges for testing
        ]);
    }
}
