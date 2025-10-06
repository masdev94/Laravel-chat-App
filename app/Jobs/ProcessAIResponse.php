<?php

namespace App\Jobs;

use App\Events\AIMessage;
use App\Services\OpenAIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAIResponse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    protected $room;
    protected $context;
    protected $userId;

    /**
     * Create a new job instance.
     *
     * @param string $message
     * @param string $room
     * @param int $userId
     * @param array $context
     * @return void
     */
    public function __construct(string $message, string $room, int $userId, array $context = [])
    {
        $this->message = $message;
        $this->room = $room;
        $this->userId = $userId;
        $this->context = $context;
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
            // Clean the message for AI processing
            $cleanMessage = $openAIService->cleanMessageForAI($this->message);

            // Generate AI response with conversation history
            $aiResponse = $openAIService->generateChatResponseWithHistory(
                $cleanMessage,
                $this->room,
                $this->userId,
                $this->context
            );

            if ($aiResponse) {
                // Save the conversation to chat history
                $openAIService->saveChatHistoryForRoom(
                    $this->userId,
                    $this->room,
                    $cleanMessage,
                    $aiResponse
                );

                // Broadcast AI response
                AIMessage::broadcast($this->room, $aiResponse);
            } else {
                // Fallback response if AI fails
                AIMessage::broadcast(
                    $this->room,
                    "Sorry, I'm having trouble responding right now. Please try again!"
                );
            }
        } catch (\Exception $e) {
            Log::error('AI Response Job Error: ' . $e->getMessage());

            // Send error message
            AIMessage::broadcast(
                $this->room,
                "I encountered an error. Please check if the AI service is configured correctly."
            );
        }
    }
}
