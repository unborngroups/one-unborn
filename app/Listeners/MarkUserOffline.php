<?php
namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Models\UserStatus;

class MarkUserOffline
{
    public function handle(Logout $event)
    {
        if ($event->user) {
            UserStatus::where('user_id', $event->user->id)
                ->update(['is_online' => 0]);
        }
    }
}
