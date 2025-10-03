<?php

namespace App\Console\Commands;

use App\Services\OpenAIService;
use Illuminate\Console\Command;

class TestAICommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:test {message?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test OpenAI integration';

    /**
     * Execute the console command.
     *
     * @param OpenAIService $openAIService
     * @return int
     */
    public function handle(OpenAIService $openAIService)
    {
        $message = $this->argument('message') ?? 'Hello, can you help me test this chat system?';

        $this->info("Testing OpenAI integration...");
        $this->info("Message: {$message}");
        $this->info("---");

        try {
            $response = $openAIService->generateChatResponse($message, 'test-room');

            if ($response) {
                $this->info("✅ AI Response: {$response}");
                return Command::SUCCESS;
            } else {
                $this->error("❌ No response from AI");
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
