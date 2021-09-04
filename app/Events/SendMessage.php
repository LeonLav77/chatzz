<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SendMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public string $message;
    public int $id;
    public string $username;
    public string $image;

    public function __construct(string $message, int $id, string  $username, string $image)
    {
        $this->message = $message;
        $this->id = $id;
        $this->username = $username;
        $this->image = $image;
        date_default_timezone_set("Europe/Belgrade");
        Message::insert([
            'user_id' => $id,
            'content' => $message,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
    public function broadcastWith()
    {
        return ['message' => $this->message, 'id' => $this->id, 'username' => $this->username, 'image' => $this->image];
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('chat');
    }
}
