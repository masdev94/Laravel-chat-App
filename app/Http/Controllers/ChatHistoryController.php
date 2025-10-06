<?php

namespace App\Http\Controllers;

use App\Models\ChatHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ChatHistoryController extends Controller
{
    /**
     * Get chat history for a user in a specific room
     *
     * @param Request $request
     * @param string $room
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHistory(Request $request, string $room)
    {
        $history = ChatHistory::getRecentHistoryForRoom(
            $room,
            $request->user()->id,
            20 // Get more history for viewing
        );

        return Response::json([
            'history' => $history->map(function ($chat) {
                return [
                    'id' => $chat->id,
                    'user_message' => $chat->user_message,
                    'ai_response' => $chat->ai_response,
                    'ai_model' => $chat->ai_model,
                    'created_at' => $chat->created_at->format('Y-m-d H:i:s'),
                ];
            })
        ]);
    }

    /**
     * Clear chat history for a user in a specific room
     *
     * @param Request $request
     * @param string $room
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearHistory(Request $request, string $room)
    {
        ChatHistory::clearHistoryForRoom(
            $request->user()->id,
            $room
        );

        return Response::json([
            'message' => 'Chat history cleared successfully for room: ' . $room
        ]);
    }

    /**
     * Get all rooms where user has chat history
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoomsWithHistory(Request $request)
    {
        $rooms = ChatHistory::where('user_id', $request->user()->id)
            ->select('room_name')
            ->groupBy('room_name')
            ->orderBy('room_name')
            ->get()
            ->pluck('room_name');

        return Response::json([
            'rooms' => $rooms
        ]);
    }
}
