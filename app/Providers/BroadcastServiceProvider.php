<?php

namespace App\Providers;

use App\Http\Middleware\CheckProfileCreated;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Broadcast::routes([
            'middleware' => ['auth', CheckProfileCreated::class],
        ]);

        require base_path('routes/channels.php');
    }
}
