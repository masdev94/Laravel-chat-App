<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AIRoomMessage implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var int
     */
    protected $userId;

    /**
     * @var string
     */
    protected $roomId;

    /**
     * @var string
     */
    protected $userMessage;

    /**
     * @var string
     */
    protected $aiResponse;

    /**
     * Create a new event instance.
     *
     * @param int $userId
     * @param string $roomId
     * @param string $userMessage
     * @param string $aiResponse
     * @return void
     */
    public function __construct(int $userId, string $roomId, string $userMessage, string $aiResponse)
    {
        $this->userId = $userId;
        $this->roomId = $roomId;
        $this->userMessage = $userMessage;
        $this->aiResponse = $aiResponse;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel("ai-room.{$this->userId}.{$this->roomId}");
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'user_message' => $this->userMessage,
            'ai_response' => $this->aiResponse,
            'room_id' => $this->roomId,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'ai.message';
    }

    /**
     * Broadcast AI room message to specific user
     *
     * @param int $userId
     * @param string $roomId
     * @param string $userMessage
     * @param string $aiResponse
     * @return void
     */
    public static function broadcastToUser(int $userId, string $roomId, string $userMessage, string $aiResponse): void
    {
        broadcast(new static($userId, $roomId, $userMessage, $aiResponse));
    }
}
