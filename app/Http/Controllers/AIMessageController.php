<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessAIRoomResponse;
use App\Models\AIRoom;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AIMessageController extends Controller
{
    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    /**
     * Send message to AI in dedicated AI room
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'room_id' => ['required', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:500'],
        ]);

        $user = $request->user();

        // Verify user owns this AI room
        $aiRoom = AIRoom::where('room_id', $request->room_id)
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->firstOrFail();

        // Update room activity
        $aiRoom->updateActivity();

        // Dispatch AI response job
        ProcessAIRoomResponse::dispatch(
            $request->message,
            $request->room_id,
            $user->id,
            $aiRoom->ai_settings ?? []
        );

        return Response::json(['success' => true]);
    }

    /**
     * Get chat history for AI room
     *
     * @param Request $request
     * @param string $roomId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHistory(Request $request, string $roomId)
    {
        $user = $request->user();

        // Verify user owns this AI room
        AIRoom::where('room_id', $roomId)
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->firstOrFail();

        $limit = $request->get('limit', 50);
        $history = \App\Models\AIChatHistory::getRecentHistory($roomId, $user->id, $limit);

        return Response::json([
            'success' => true,
            'history' => $history,
        ]);
    }

    /**
     * Clear chat history for AI room
     *
     * @param Request $request
     * @param string $roomId
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearHistory(Request $request, string $roomId)
    {
        $user = $request->user();

        // Verify user owns this AI room
        AIRoom::where('room_id', $roomId)
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->firstOrFail();

        // Delete chat history for this room and user
        \App\Models\AIChatHistory::where('room_id', $roomId)
            ->where('user_id', $user->id)
            ->delete();

        return Response::json(['success' => true]);
    }
}
