<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Menu;
use App\Models\UserMenuPrivilege;

class GenerateUserMenuPrivileges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:user-menu-privileges';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate user_menu_privileges for every menu for a specific user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->ask('Enter the user ID');
        $user = User::find($userId);
        if (!$user) {
            $this->error('User not found.');
            return;
        }

        $menus = Menu::all();
        foreach ($menus as $menu) {
            UserMenuPrivilege::updateOrCreate(
                ['user_id' => $user->id, 'menu_id' => $menu->id],
                [
                    'can_menu' => 0, // Set to 1 for visible, 0 for hidden
                    'can_add' => 0,
                    'can_edit' => 0,
                    'can_delete' => 0,
                    'can_view' => 0,
                ]
            );
        }
        $this->info('User menu privileges generated for user: ' . $user->name);
    }
}
