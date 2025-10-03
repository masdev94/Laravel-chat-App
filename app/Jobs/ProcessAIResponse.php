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

    /**
     * Create a new job instance.
     *
     * @param string $message
     * @param string $room
     * @param array $context
     * @return void
     */
    public function __construct(string $message, string $room, array $context = [])
    {
        $this->message = $message;
        $this->room = $room;
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

            // Generate AI response
            $aiResponse = $openAIService->generateChatResponse(
                $cleanMessage,
                $this->room,
                $this->context
            );

            if ($aiResponse) {
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
