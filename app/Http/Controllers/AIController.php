<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;

class AIController extends Controller
{
    /**
     * Toggle AI for a room
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleAI(Request $request)
    {
        $request->validate([
            'room' => ['required', 'string', 'max:100'],
            'enabled' => ['required', 'boolean'],
        ]);

        // Store AI status in session for now
        // In production, you might want to store this in database
        $aiRooms = session('ai_rooms', []);

        if ($request->enabled) {
            $aiRooms[$request->room] = true;
        } else {
            unset($aiRooms[$request->room]);
        }

        session(['ai_rooms' => $aiRooms]);

        return Response::json([
            'ok' => true,
            'ai_enabled' => $request->enabled
        ]);
    }

    /**
     * Get AI status for a room
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAIStatus(Request $request)
    {
        $request->validate([
            'room' => ['required', 'string', 'max:100'],
        ]);

        $aiRooms = session('ai_rooms', []);
        $isEnabled = isset($aiRooms[$request->room]);

        return Response::json([
            'ai_enabled' => $isEnabled
        ]);
    }
}
