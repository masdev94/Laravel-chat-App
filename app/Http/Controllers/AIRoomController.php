<?php

namespace App\Http\Controllers;

use App\Models\AIRoom;
use App\Models\AIChatHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;

class AIRoomController extends Controller
{
    /**
     * Show AI room interface
     *
     * @param Request $request
     * @param string $roomId
     * @return \Inertia\Response
     */
    public function show(Request $request, string $roomId)
    {
        $user = $request->user();

        // Find or create AI room
        $aiRoom = AIRoom::where('room_id', $roomId)
            ->where('user_id', $user->id)
            ->first();

        if (!$aiRoom) {
            // If room doesn't exist, redirect to AI rooms list
            return redirect()->route('ai.rooms.index');
        }

        // Get chat history
        $chatHistory = AIChatHistory::getRecentHistory($roomId, $user->id, 50);

        // Update last activity
        $aiRoom->updateActivity();

        return Inertia::render('AIRoom', [
            'room' => $aiRoom,
            'chat_history' => $chatHistory,
            'personalities' => $this->getPersonalities(),
        ]);
    }

    /**
     * List user's AI rooms
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $aiRooms = AIRoom::getUserRooms($user->id);

        return Inertia::render('AIRooms', [
            'ai_rooms' => $aiRooms,
            'personalities' => $this->getPersonalities(),
        ]);
    }

    /**
     * Create new AI room
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'personality' => ['required', 'string', 'in:helpful_assistant,creative_writer,technical_expert,tutor,brainstorm_partner'],
            'model' => ['required', 'string', 'in:gpt-3.5-turbo,gpt-4'],
        ]);

        $aiRoom = AIRoom::createForUser(
            $request->user()->id,
            $request->title,
            $request->description,
            [
                'personality' => $request->personality,
                'model' => $request->model,
                'temperature' => 0.7,
                'max_tokens' => 150,
            ]
        );

        return Response::json([
            'success' => true,
            'room' => $aiRoom,
            'redirect_url' => route('ai.room', ['roomId' => $aiRoom->room_id]),
        ]);
    }

    /**
     * Update AI room settings
     *
     * @param Request $request
     * @param string $roomId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $roomId)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'personality' => ['required', 'string', 'in:helpful_assistant,creative_writer,technical_expert,tutor,brainstorm_partner'],
            'model' => ['required', 'string', 'in:gpt-3.5-turbo,gpt-4'],
        ]);

        $aiRoom = AIRoom::where('room_id', $roomId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $aiRoom->update([
            'title' => $request->title,
            'description' => $request->description,
            'ai_settings' => array_merge($aiRoom->ai_settings ?? [], [
                'personality' => $request->personality,
                'model' => $request->model,
            ]),
        ]);

        return Response::json([
            'success' => true,
            'room' => $aiRoom,
        ]);
    }

    /**
     * Delete AI room
     *
     * @param Request $request
     * @param string $roomId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, string $roomId)
    {
        $aiRoom = AIRoom::where('room_id', $roomId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        // Soft delete by marking as inactive
        $aiRoom->update(['is_active' => false]);

        return Response::json(['success' => true]);
    }

    /**
     * Get available AI personalities
     *
     * @return array
     */
    private function getPersonalities(): array
    {
        return [
            'helpful_assistant' => [
                'name' => 'Helpful Assistant',
                'description' => 'General purpose helpful AI assistant',
                'icon' => '🤖',
            ],
            'creative_writer' => [
                'name' => 'Creative Writer',
                'description' => 'Storytelling and creative writing assistant',
                'icon' => '✍️',
            ],
            'technical_expert' => [
                'name' => 'Technical Expert',
                'description' => 'Programming and technology specialist',
                'icon' => '💻',
            ],
            'tutor' => [
                'name' => 'Patient Tutor',
                'description' => 'Educational and learning assistant',
                'icon' => '📚',
            ],
            'brainstorm_partner' => [
                'name' => 'Brainstorm Partner',
                'description' => 'Creative thinking and idea generation',
                'icon' => '💡',
            ],
        ];
    }
}
