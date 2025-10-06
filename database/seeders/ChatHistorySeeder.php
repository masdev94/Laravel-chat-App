<?php

namespace Database\Seeders;

use App\Models\ChatHistory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ChatHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get the first user for testing
        $user = User::first();

        if (!$user) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        // Create sample chat history for 'general' room
        $chatHistories = [
            [
                'user_message' => '@ai what is Laravel?',
                'ai_response' => 'Laravel is a popular PHP framework that makes web development elegant and enjoyable. It provides expressive syntax and powerful features like routing, authentication, and database management.',
            ],
            [
                'user_message' => '@ai tell me about PHP best practices',
                'ai_response' => 'Here are some PHP best practices: use PSR standards, validate input, use prepared statements for database queries, implement proper error handling, and follow object-oriented programming principles.',
            ],
            [
                'user_message' => 'ai: how does dependency injection work?',
                'ai_response' => 'Dependency injection is a design pattern where objects receive their dependencies from external sources rather than creating them internally. In Laravel, the service container automatically resolves dependencies through type-hinting in constructors.',
            ],
            [
                'user_message' => '@bot explain MVC pattern',
                'ai_response' => 'MVC (Model-View-Controller) separates application logic: Models handle data, Views display information, and Controllers process user input. This separation makes code more organized and maintainable.',
            ],
        ];

        foreach ($chatHistories as $index => $history) {
            ChatHistory::create([
                'user_id' => $user->id,
                'room_name' => 'general',
                'user_message' => $history['user_message'],
                'ai_response' => $history['ai_response'],
                'ai_model' => 'gpt-3.5-turbo',
                'context' => [],
                'created_at' => now()->subMinutes(30 - ($index * 5)), // Spread over 30 minutes
                'updated_at' => now()->subMinutes(30 - ($index * 5)),
            ]);
        }

        // Create some history for 'development' room too
        $devHistories = [
            [
                'user_message' => '@ai what is the difference between Laravel and CodeIgniter?',
                'ai_response' => 'Laravel is more modern with features like Eloquent ORM, Blade templating, and built-in authentication. CodeIgniter is lighter but less feature-rich. Laravel follows convention over configuration while CodeIgniter gives more freedom.',
            ],
            [
                'user_message' => 'ai: explain database migrations',
                'ai_response' => 'Database migrations are version control for your database schema. They allow you to modify database structure in a consistent way across different environments using PHP code instead of SQL scripts.',
            ],
        ];

        foreach ($devHistories as $index => $history) {
            ChatHistory::create([
                'user_id' => $user->id,
                'room_name' => 'development',
                'user_message' => $history['user_message'],
                'ai_response' => $history['ai_response'],
                'ai_model' => 'gpt-3.5-turbo',
                'context' => [],
                'created_at' => now()->subMinutes(20 - ($index * 3)),
                'updated_at' => now()->subMinutes(20 - ($index * 3)),
            ]);
        }

        $this->command->info('Chat history seeded successfully for user: ' . $user->email);
    }
}
