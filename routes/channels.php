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

Broadcast::channel('room.{id}', function ($user, $id) {
    return $user->only('id', 'name');
});

// AI Room private channels
Broadcast::channel('ai-room.{userId}.{roomId}', function ($user, $userId, $roomId) {
    // Only allow the owner of the AI room to join
    if ($user->id == $userId) {
        // Verify user owns this AI room
        $aiRoom = \App\Models\AIRoom::where('room_id', $roomId)
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->first();

        return $aiRoom ? $user->only('id', 'name') : null;
    }

    return null;
});
