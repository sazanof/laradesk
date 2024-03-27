<?php

use Illuminate\Support\Facades\Broadcast;

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

Broadcast::channel('users.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});

Broadcast::channel('notifications.{userId}.*', function (\App\Models\User $user, int $userId) {
    return $user->id === $userId;
});
