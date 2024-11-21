<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {


        Broadcast::routes(['middleware' => ['jwt.auth']]);

        Broadcast::channel('presence-users', function ($user) {
            return ['id' => $user->id, 'name' => $user->telefono];
        });

        require base_path('routes/channels.php');
    }
}
