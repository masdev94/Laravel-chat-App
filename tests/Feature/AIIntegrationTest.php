<?php

namespace Tests\Feature;

use App\Services\OpenAIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AIIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_service_detects_ai_messages()
    {
        $service = new OpenAIService();

        // Test AI trigger detection
        $this->assertTrue($service->isMessageForAI('@ai hello'));
        $this->assertTrue($service->isMessageForAI('@bot how are you?'));
        $this->assertTrue($service->isMessageForAI('ai: what is the weather?'));
        $this->assertTrue($service->isMessageForAI('hey ai, help me'));

        // Test non-AI messages
        $this->assertFalse($service->isMessageForAI('hello everyone'));
        $this->assertFalse($service->isMessageForAI('how is everyone doing?'));
    }

    public function test_ai_message_cleaning()
    {
        $service = new OpenAIService();

        $this->assertEquals('hello there', $service->cleanMessageForAI('@ai hello there'));
        $this->assertEquals('how are you?', $service->cleanMessageForAI('@bot how are you?'));
        $this->assertEquals('what is the weather?', $service->cleanMessageForAI('ai: what is the weather?'));
    }

    public function test_ai_toggle_endpoint()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->post('/ai/toggle', [
            'room' => 'test-room',
            'enabled' => true
        ]);

        $response->assertStatus(200);
        $response->assertJson(['ok' => true, 'ai_enabled' => true]);
    }

    public function test_message_sending_with_ai_trigger()
    {
        $user = \App\Models\User::factory()->create();

        // Enable AI for the room
        $this->actingAs($user)->post('/ai/toggle', [
            'room' => 'test-room',
            'enabled' => true
        ]);

        // Send AI message
        $response = $this->actingAs($user)->post('/message', [
            'room' => 'test-room',
            'message' => '@ai hello there'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['ok' => true]);
    }
}
