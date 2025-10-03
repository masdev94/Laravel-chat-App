<?php

namespace App\Http\Controllers;

use App\Events\Message;
use App\Jobs\ProcessAIResponse;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SendMessageController extends Controller
{
    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    /**
     * Send the message to a room.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'room' => ['required', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:140'],
        ]);

        // Broadcast the user's message first
        Message::broadcast(
            $request->user(),
            $request->room,
            $request->message,
        );

        // Check if AI should respond (only if AI is enabled for this room)
        $aiRooms = session('ai_rooms', []);
        $aiEnabled = isset($aiRooms[$request->room]);

        if ($aiEnabled && $this->openAIService->isMessageForAI($request->message)) {
            // Dispatch AI response job (async)
            ProcessAIResponse::dispatch(
                $request->message,
                $request->room,
                [] // Context can be enhanced later
            );
        }

        return Response::json(['ok' => true]);
    }
}
