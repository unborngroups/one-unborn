<?php
namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        'Illuminate\Auth\Events\Logout' => [
            'App\Listeners\MarkUserOffline',
        ],
    ];

    public function boot()
    {
        parent::boot();
    }
}
