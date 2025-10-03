<?php

namespace App\Models;

use App\Models\User;

class AIUser
{
    /**
     * Get AI user representation
     *
     * @return array
     */
    public static function getAIUser(): array
    {
        return [
            'id' => 'ai',
            'name' => 'AI Assistant',
            'email' => 'ai@chatroom.local',
            'is_ai' => true,
        ];
    }

    /**
     * Create a fake User instance for AI (for compatibility)
     *
     * @return User
     */
    public static function createFakeUserInstance(): User
    {
        $user = new User();
        $user->id = 'ai';
        $user->name = 'AI Assistant';
        $user->email = 'ai@chatroom.local';

        return $user;
    }
}
