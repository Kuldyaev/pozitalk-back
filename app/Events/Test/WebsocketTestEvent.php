<?php

namespace App\Events\Test;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class WebsocketTestEvent implements ShouldBroadcast
{
    use InteractsWithBroadcasting;

    public function __construct(
        public string $type,
    ) {
    }

    public function broadcastAs()
    {
        return 'event';
    }

    public function broadcastOn()
    {
        return new Channel('test');
    }

    public function broadcastWith()
    {
        return [
            'message' => 'Hello!!!',
        ];
    }
}
