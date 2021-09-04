<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ReceiveNewChats implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public string $identifier;
    public string $key;
    public int $id;
    public object $userElements;

    public function __construct(string $identifier,string $key, int $id)
    {
        $this->identifier = $identifier;
        $this->id = $id;
        $this->key = $key;
        $this->userElements = User::find($id);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastWith()
    {
        return ['id' => $this->id, 'key' => $this->key, 'image' => $this->userElements->image, 'name' => $this->userElements->name];
    }
    public function broadcastOn()
    {
        return new Channel($this->identifier);
    }
}