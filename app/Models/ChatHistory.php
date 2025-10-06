<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    use HasFactory;

    protected $table = 'chat_histories';

    protected $fillable = [
        'user_id',
        'room_name',
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
     * Get recent chat history for context in a general chat room
     *
     * @param string $roomName
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRecentHistoryForRoom(string $roomName, int $userId, int $limit = 10)
    {
        return static::where('room_name', $roomName)
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    /**
     * Create a new chat history entry for general chat room
     *
     * @param int $userId
     * @param string $roomName
     * @param string $userMessage
     * @param string $aiResponse
     * @param string $aiModel
     * @param array $context
     * @return static
     */
    public static function createEntry(
        int $userId,
        string $roomName,
        string $userMessage,
        string $aiResponse,
        string $aiModel = 'gpt-3.5-turbo',
        array $context = []
    ): static {
        return static::create([
            'user_id' => $userId,
            'room_name' => $roomName,
            'user_message' => $userMessage,
            'ai_response' => $aiResponse,
            'ai_model' => $aiModel,
            'context' => $context,
        ]);
    }

    /**
     * Clear chat history for a user in a specific room
     *
     * @param int $userId
     * @param string $roomName
     * @return void
     */
    public static function clearHistoryForRoom(int $userId, string $roomName): void
    {
        static::where('user_id', $userId)
            ->where('room_name', $roomName)
            ->delete();
    }
}
