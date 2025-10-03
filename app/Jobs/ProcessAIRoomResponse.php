<?php

namespace App\Jobs;

use App\Events\AIRoomMessage;
use App\Services\OpenAIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAIRoomResponse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    protected $roomId;
    protected $userId;
    protected $aiSettings;

    /**
     * Create a new job instance.
     *
     * @param string $message
     * @param string $roomId
     * @param int $userId
     * @param array $aiSettings
     * @return void
     */
    public function __construct(string $message, string $roomId, int $userId, array $aiSettings = [])
    {
        $this->message = $message;
        $this->roomId = $roomId;
        $this->userId = $userId;
        $this->aiSettings = $aiSettings;
    }

    /**
     * Execute the job.
     *
     * @param OpenAIService $openAIService
     * @return void
     */
    public function handle(OpenAIService $openAIService)
    {
        try {
            // Generate AI response with full context
            $aiResponse = $openAIService->generateContextualResponse(
                $this->message,
                $this->roomId,
                $this->userId,
                $this->aiSettings
            );

            if ($aiResponse) {
                // Save to chat history
                $openAIService->saveChatHistory(
                    $this->userId,
                    $this->roomId,
                    $this->message,
                    $aiResponse
                );

                // Broadcast AI response to the specific user
                AIRoomMessage::broadcastToUser(
                    $this->userId,
                    $this->roomId,
                    $this->message,
                    $aiResponse
                );
            } else {
                // Fallback response if AI fails
                AIRoomMessage::broadcastToUser(
                    $this->userId,
                    $this->roomId,
                    $this->message,
                    "I'm sorry, I'm having trouble responding right now. Please try again!"
                );
            }
        } catch (\Exception $e) {
            Log::error('AI Room Response Job Error: ' . $e->getMessage());

            // Send error message
            AIRoomMessage::broadcastToUser(
                $this->userId,
                $this->roomId,
                $this->message,
                "I encountered an error. Please check if the AI service is configured correctly."
            );
        }
    }
}
