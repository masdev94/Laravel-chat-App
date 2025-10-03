<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AIRoom extends Model
{
    use HasFactory;

    protected $table = 'ai_rooms';

    protected $fillable = [
        'room_id',
        'user_id',
        'title',
        'description',
        'ai_settings',
        'is_active',
        'last_activity_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($room) {
            if (empty($room->room_id)) {
                $room->room_id = 'ai_' . Str::random(12);
            }
        });
    }

    protected $casts = [
        'ai_settings' => 'array',
        'is_active' => 'boolean',
        'last_activity_at' => 'datetime',
    ];

    /**
     * Get the user that owns the AI room.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get chat history for this room
     */
    public function chatHistory()
    {
        return $this->hasMany(AIChatHistory::class, 'room_id', 'room_id');
    }

    /**
     * Create a new AI room for a user
     *
     * @param int $userId
     * @param string $title
     * @param string|null $description
     * @param array $aiSettings
     * @return static
     */
    public static function createForUser(
        int $userId,
        string $title = 'AI Chat',
        ?string $description = null,
        array $aiSettings = []
    ): static {
        return static::create([
            'user_id' => $userId,
            'title' => $title,
            'description' => $description,
            'ai_settings' => array_merge([
                'model' => 'gpt-3.5-turbo',
                'temperature' => 0.7,
                'max_tokens' => 150,
                'personality' => 'helpful_assistant',
            ], $aiSettings),
            'is_active' => true,
            'last_activity_at' => now(),
        ]);
    }

    /**
     * Update last activity timestamp
     */
    public function updateActivity()
    {
        $this->update(['last_activity_at' => now()]);
    }

    /**
     * Get user's active AI rooms
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getUserRooms(int $userId)
    {
        return static::where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('last_activity_at', 'desc')
            ->get();
    }
}
