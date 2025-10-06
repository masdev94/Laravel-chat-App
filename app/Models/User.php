<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the AI rooms for the user.
     */
    public function aiRooms()
    {
        return $this->hasMany(AIRoom::class);
    }

    /**
     * Get the AI chat history for the user.
     */
    public function aiChatHistory()
    {
        return $this->hasMany(AIChatHistory::class);
    }

    /**
     * Get the general chat history for the user.
     */
    public function chatHistory()
    {
        return $this->hasMany(ChatHistory::class);
    }
}
