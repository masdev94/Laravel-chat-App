<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AIMessage implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var string
     */
    protected $room;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var array
     */
    protected $aiUser;

    /**
     * Create a new event instance.
     *
     * @param string $room
     * @param string $message
     * @return void
     */
    public function __construct(string $room, string $message)
    {
        $this->room = $room;
        $this->message = $message;
        $this->aiUser = [
            'id' => 'ai',
            'name' => 'AI Assistant',
            'is_ai' => true,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel("room.{$this->room}");
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'user' => $this->aiUser,
            'message' => $this->message,
            'is_ai_message' => true,
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'room.message';
    }

    /**
     * Broadcast the AI message
     *
     * @param string $room
     * @param string $message
     * @return void
     */
    public static function broadcast(string $room, string $message): void
    {
        broadcast(new static($room, $message));
    }
}
