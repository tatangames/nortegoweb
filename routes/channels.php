<?php

use App\Models\Administrador;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use Laravel\Prompts\Key;
use Tymon\JWTAuth\JWT;
use Tymon\JWTAuth\Facades\JWTAuth;
/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/


Broadcast::channel('presence-users', function ($user) {
    return ['id' => $user->id, 'name' => $user->telefono];
});







