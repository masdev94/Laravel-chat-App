<?php

namespace App\Services;

use OpenAI;
use Exception;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected $client;
    protected $model;

    public function __construct()
    {
        $this->client = OpenAI::client(config('services.openai.api_key'));
        $this->model = config('services.openai.model', 'gpt-3.5-turbo');
    }

    /**
     * Generate AI response for chat message
     *
     * @param string $message
     * @param string $room
     * @param array $context
     * @param int|null $userId
     * @return string|null
     */
    public function generateChatResponse(string $message, string $room, array $context = [], ?int $userId = null): ?string
    {
        try {
            $messages = $this->buildMessagesWithHistory($message, $room, $context, $userId);

            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => $messages,
                'max_tokens' => 150,
                'temperature' => 0.7,
            ]);

            return $response->choices[0]->message->content ?? null;
        } catch (Exception $e) {
            Log::error('OpenAI API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate AI response with full conversation history
     *
     * @param string $message
     * @param string $roomId
     * @param int $userId
     * @param array $aiSettings
     * @return string|null
     */
    public function generateContextualResponse(string $message, string $roomId, int $userId, array $aiSettings = []): ?string
    {
        try {
            // Get recent chat history
            $history = \App\Models\AIChatHistory::getRecentHistory($roomId, $userId, 10);

            // Build messages array with history
            $messages = [];

            // System prompt
            $messages[] = ['role' => 'system', 'content' => $this->buildContextualSystemPrompt($roomId, $aiSettings)];

            // Add conversation history
            foreach ($history as $chat) {
                $messages[] = ['role' => 'user', 'content' => $chat->user_message];
                $messages[] = ['role' => 'assistant', 'content' => $chat->ai_response];
            }

            // Add current message
            $messages[] = ['role' => 'user', 'content' => $message];

            $response = $this->client->chat()->create([
                'model' => $aiSettings['model'] ?? $this->model,
                'messages' => $messages,
                'max_tokens' => $aiSettings['max_tokens'] ?? 150,
                'temperature' => $aiSettings['temperature'] ?? 0.7,
            ]);

            return $response->choices[0]->message->content ?? null;
        } catch (Exception $e) {
            Log::error('OpenAI API Error: ' . $e->getMessage());
            return null;
        }
    }    /**
     * Build system prompt for AI
     *
     * @param string $room
     * @param array $context
     * @return string
     */
    protected function buildSystemPrompt(string $room, array $context = []): string
    {
        $basePrompt = "You are a helpful AI assistant participating in a chat room called '{$room}'. ";
        $basePrompt .= "Keep your responses friendly, conversational, and under 140 characters. ";
        $basePrompt .= "You can discuss any topic and help answer questions. ";

        if (!empty($context)) {
            $basePrompt .= "Here's some recent chat context: " . implode(' ', $context) . " ";
        }

        $basePrompt .= "Respond naturally as if you're part of the conversation.";

        return $basePrompt;
    }

    /**
     * Check if message is directed at AI
     *
     * @param string $message
     * @return bool
     */
    public function isMessageForAI(string $message): bool
    {
        $aiTriggers = ['@ai', '@bot', 'ai:', 'bot:', 'hey ai', 'ai help'];
        $message = strtolower(trim($message));

        foreach ($aiTriggers as $trigger) {
            if (str_contains($message, $trigger)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Clean message for AI processing
     *
     * @param string $message
     * @return string
     */
    public function cleanMessageForAI(string $message): string
    {
        $aiTriggers = ['@ai', '@bot', 'ai:', 'bot:', 'hey ai'];
        $message = trim($message);

        foreach ($aiTriggers as $trigger) {
            $message = preg_replace('/\b' . preg_quote($trigger, '/') . '\b/i', '', $message);
        }

        return trim($message);
    }

    /**
     * Build messages array with conversation history
     *
     * @param string $message
     * @param string $room
     * @param array $context
     * @param int|null $userId
     * @return array
     */
    protected function buildMessagesWithHistory(string $message, string $room, array $context, ?int $userId): array
    {
        $messages = [];

        // System prompt
        $messages[] = ['role' => 'system', 'content' => $this->buildSystemPrompt($room, $context)];

        // Add recent history if user ID is provided
        if ($userId) {
            $history = \App\Models\AIChatHistory::getRecentHistory($room, $userId, 5);
            foreach ($history as $chat) {
                $messages[] = ['role' => 'user', 'content' => $chat->user_message];
                $messages[] = ['role' => 'assistant', 'content' => $chat->ai_response];
            }
        }

        // Current message
        $messages[] = ['role' => 'user', 'content' => $message];

        return $messages;
    }

    /**
     * Build contextual system prompt for AI rooms
     *
     * @param string $roomId
     * @param array $aiSettings
     * @return string
     */
    protected function buildContextualSystemPrompt(string $roomId, array $aiSettings = []): string
    {
        $personality = $aiSettings['personality'] ?? 'helpful_assistant';

        $prompts = [
            'helpful_assistant' => "You are a helpful AI assistant in a private chat room. Provide thoughtful, accurate responses and remember the conversation context. Be friendly and professional.",
            'creative_writer' => "You are a creative writing assistant. Help with storytelling, character development, plot ideas, and creative inspiration. Be imaginative and supportive.",
            'technical_expert' => "You are a technical expert assistant. Help with programming, technology questions, troubleshooting, and provide detailed technical explanations.",
            'tutor' => "You are a patient tutor. Help explain concepts clearly, provide step-by-step guidance, and adapt to the user's learning pace.",
            'brainstorm_partner' => "You are a brainstorming partner. Help generate ideas, think creatively, and explore different perspectives on topics.",
        ];

        return $prompts[$personality] ?? $prompts['helpful_assistant'];
    }

    /**
     * Save chat interaction to history
     *
     * @param int $userId
     * @param string $roomId
     * @param string $userMessage
     * @param string $aiResponse
     * @return void
     */
    public function saveChatHistory(int $userId, string $roomId, string $userMessage, string $aiResponse): void
    {
        \App\Models\AIChatHistory::createEntry(
            $userId,
            $roomId,
            $userMessage,
            $aiResponse,
            $this->model
        );
    }
}
