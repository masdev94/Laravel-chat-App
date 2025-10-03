<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIChatHistory extends Model
{
    use HasFactory;

    protected $table = 'ai_chat_histories';

    protected $fillable = [
        'user_id',
        'room_id',
        'user_message',
        'ai_response',
        'ai_model',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    /**
     * Get the user that owns the chat history.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get recent chat history for context
     *
     * @param string $roomId
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRecentHistory(string $roomId, int $userId, int $limit = 10)
    {
        return static::where('room_id', $roomId)
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    /**
     * Create a new chat history entry
     *
     * @param int $userId
     * @param string $roomId
     * @param string $userMessage
     * @param string $aiResponse
     * @param string $aiModel
     * @param array $context
     * @return static
     */
    public static function createEntry(
        int $userId,
        string $roomId,
        string $userMessage,
        string $aiResponse,
        string $aiModel = 'gpt-3.5-turbo',
        array $context = []
    ): static {
        return static::create([
            'user_id' => $userId,
            'room_id' => $roomId,
            'user_message' => $userMessage,
            'ai_response' => $aiResponse,
            'ai_model' => $aiModel,
            'context' => $context,
        ]);
    }
}
