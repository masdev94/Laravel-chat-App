<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ChatRoomController extends Controller
{
    /**
     * Render the chat room.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $room
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, $room = null)
    {
        // If no room is assigned, generate a random room name.
        if (! $room) {
            return Redirect::route('chat.room', ['room' => Str::random(10)]);
        }

        // Check if AI is enabled for this room
        $aiRooms = session('ai_rooms', []);
        $aiEnabled = isset($aiRooms[$room]);

        return Inertia::render('ChatRoom', [
            'room' => $room,
            'link' => route('chat.room', ['room' => $room]),
            'ai_enabled' => $aiEnabled,
        ]);
    }
}
