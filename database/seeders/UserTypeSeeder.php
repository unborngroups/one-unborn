<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserType;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { 
        $userTypes = [
            ['name' => 'superadmin'],
            ['name' => 'user'],
        ];
        foreach ($userTypes as $type) {
            UserType::firstOrCreate($type); // prevents duplicates if seeder runs multiple times
        }
    }
}
